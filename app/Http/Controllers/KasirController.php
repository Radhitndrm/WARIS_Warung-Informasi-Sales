<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
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

            $lastOrder = Order::latest()->first();
            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad(($lastOrder ? $lastOrder->id + 1 : 1), 4, '0', STR_PAD_LEFT);

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

            $order = Order::create([
                'user_id' => $user->id,
                'invoice_no' => $invoiceNo,
                'total' => $total,
                'status' => 'paid',
            ]);

            $order->items()->saveMany($orderItems);

            $amountPaid = $validated['payment_method'] === 'qris' ? $total : $validated['amount_paid'];
            $changeAmount = $validated['payment_method'] === 'cash' ? ($amountPaid - $total) : 0;

            Payment::create([
                'order_id' => $order->id,
                'method' => $validated['payment_method'],
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
                    'payment_method' => $validated['payment_method'],
                    'items' => collect($orderItems)->map(fn ($item) => [
                        'product' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ]),
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
