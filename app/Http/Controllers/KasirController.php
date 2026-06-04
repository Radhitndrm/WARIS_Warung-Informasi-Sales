<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $products = Product::with('category')->where('is_active', true)->latest()->get();

        return view('kasir.index', compact('categories', 'products'));
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'amount_paid' => 'required_if:payment_method,cash|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $total = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->name} tidak mencukupi. Sisa: {$product->stock}",
                    ], 422);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $orderItems[] = new OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $isCash = $validated['payment_method'] === 'cash';

            $order = Order::create([
                'user_id' => $user->id,
                'invoice_no' => 'TEMP',
                'total' => $total,
                'status' => $isCash ? 'paid' : 'pending',
            ]);

            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->update(['invoice_no' => $invoiceNo]);

            $order->items()->saveMany($orderItems);

            if ($isCash) {
                $amountPaid = $validated['amount_paid'];
                $changeAmount = $amountPaid - $total;

                Payment::create([
                    'order_id' => $order->id,
                    'method' => 'cash',
                    'amount' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'status' => 'success',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil',
                    'order' => [
                        'id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'total' => $order->total,
                        'change_amount' => $changeAmount,
                        'payment_method' => 'cash',
                        'items' => collect($orderItems)->map(fn ($item) => [
                            'product' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->subtotal,
                        ]),
                        'created_at' => $order->created_at->format('d/m/Y H:i'),
                    ],
                ]);
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => 'qris',
                'amount' => $total,
                'change_amount' => 0,
                'status' => 'pending',
            ]);

            $midtrans = app(MidtransService::class);
            $snapResponse = $midtrans->createSnapTransaction($order->load('items.product', 'user'));

            $payment->update([
                'snap_token' => $snapResponse->token,
                'payment_url' => $snapResponse->redirect_url,
                'midtrans_id' => $order->invoice_no,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menunggu pembayaran QRIS',
                'snap_token' => $snapResponse->token,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paymentCallback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::with('payment')->findOrFail($validated['order_id']);

            if ($order->status === 'paid') {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran sudah berhasil sebelumnya',
                    'order' => $this->formatOrderReceipt($order),
                ]);
            }

            try {
                $midtrans = app(MidtransService::class);
                $status = $midtrans->getTransactionStatus($order->invoice_no);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada pembayaran ditemukan',
                ]);
            }

            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status;

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $order->update(['status' => 'paid']);
                    $order->payment->update(['status' => 'success']);

                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran berhasil',
                        'order' => $this->formatOrderReceipt($order),
                    ]);
                }
            }

            if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $order->payment->update(['status' => 'failed']);

                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran gagal: ' . $transactionStatus,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Pembayaran masih diproses',
                'status' => $transactionStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function formatOrderReceipt(Order $order): array
    {
        return [
            'id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'total' => $order->total,
            'change_amount' => $order->payment->change_amount,
            'payment_method' => $order->payment->method,
            'items' => $order->items->map(fn ($item) => [
                'product' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
            ]),
            'created_at' => $order->created_at->format('d/m/Y H:i'),
        ];
    }
}
