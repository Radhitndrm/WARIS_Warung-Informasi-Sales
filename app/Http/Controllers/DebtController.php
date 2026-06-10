<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::with(['order', 'order.items.product'])
            ->latest()
            ->get();

        $totalUtang = $debts->where('status', 'active')->sum('remaining_amount');
        $totalLunas = $debts->where('status', 'paid')->sum('total_amount');

        return view('utang.index', compact('debts', 'totalUtang', 'totalLunas'));
    }

    public function show(Debt $debt)
    {
        $debt->load([
            'order', 'order.items.product',
            'payments' => fn ($q) => $q->latest(),
        ]);

        return view('utang.show', compact('debt'));
    }

    public function storePayment(Request $request, Debt $debt): JsonResponse
    {
        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Utang sudah lunas.',
            ], 422);
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|in:cash,qris',
            'notes' => 'nullable|string|max:255',
        ]);

        $amount = $validated['amount'];
        $remaining = $debt->remaining_amount - $amount;

        if ($remaining < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah bayar melebihi sisa utang (Rp' . number_format($debt->remaining_amount, 0, ',', '.') . ').',
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($validated['method'] === 'qris') {
                DebtPayment::where('debt_id', $debt->id)
                    ->where('method', 'qris')
                    ->where('status', 'pending')
                    ->update(['status' => 'failed']);
            }

            $debtPayment = DebtPayment::create([
                'debt_id' => $debt->id,
                'amount' => $amount,
                'method' => $validated['method'],
                'status' => $validated['method'] === 'cash' ? 'success' : 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($validated['method'] === 'cash') {
                $newPaid = $debt->paid_amount + $amount;
                $debt->update([
                    'paid_amount' => $newPaid,
                    'remaining_amount' => $debt->total_amount - $newPaid,
                ]);

                if ($debt->fresh()->remaining_amount <= 0) {
                    $debt->update(['status' => 'paid']);
                    $debt->order->update(['status' => 'paid']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran utang berhasil.',
                    'debt_payment' => $debtPayment->load('debt'),
                ]);
            }

            $invoiceNo = 'DEBT-' . $debt->id . '-' . now()->format('His');
            $midtrans = app(MidtransService::class);
            $snapResponse = $midtrans->createDebtTransaction($debt->load('order', 'order.items.product'), $amount, $invoiceNo);

            $debtPayment->update([
                'midtrans_id' => $invoiceNo,
                'snap_token' => $snapResponse->token,
                'payment_url' => $snapResponse->redirect_url,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menunggu pembayaran QRIS',
                'snap_token' => $snapResponse->token,
                'debt_payment_id' => $debtPayment->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paymentCallback(Request $request, Debt $debt): JsonResponse
    {
        $validated = $request->validate([
            'debt_payment_id' => 'required|exists:debt_payments,id',
        ]);

        $debtPayment = DebtPayment::where('id', $validated['debt_payment_id'])
            ->where('debt_id', $debt->id)
            ->firstOrFail();

        if ($debtPayment->status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran sudah berhasil sebelumnya.',
                'debt' => $this->formatDebtResponse($debt->fresh(['payments'])),
            ]);
        }

        try {
            $midtrans = app(MidtransService::class);
            $status = $midtrans->getTransactionStatus($debtPayment->midtrans_id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada pembayaran ditemukan.',
            ]);
        }

        $transactionStatus = $status->transaction_status;
        $fraudStatus = $status->fraud_status ?? null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                DB::beginTransaction();
                try {
                    $debtPayment->update(['status' => 'success']);

                    $newPaid = $debt->paid_amount + $debtPayment->amount;
                    $debt->update([
                        'paid_amount' => $newPaid,
                        'remaining_amount' => $debt->total_amount - $newPaid,
                    ]);

                    if ($debt->fresh()->remaining_amount <= 0) {
                        $debt->update(['status' => 'paid']);
                        $debt->order->update(['status' => 'paid']);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses pembayaran.',
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran utang berhasil via QRIS.',
                    'debt' => $this->formatDebtResponse($debt->fresh(['payments'])),
                ]);
            }
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $debtPayment->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal: ' . $transactionStatus,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pembayaran masih diproses.',
        ]);
    }

    public function cancelPayment(Request $request, Debt $debt): JsonResponse
    {
        $validated = $request->validate([
            'debt_payment_id' => 'required|exists:debt_payments,id',
        ]);

        $debtPayment = DebtPayment::where('id', $validated['debt_payment_id'])
            ->where('debt_id', $debt->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $debtPayment->update(['status' => 'failed']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran dibatalkan.',
        ]);
    }

    private function formatDebtResponse(Debt $debt): array
    {
        return [
            'id' => $debt->id,
            'customer_name' => $debt->customer_name,
            'total_amount' => $debt->total_amount,
            'paid_amount' => $debt->paid_amount,
            'remaining_amount' => $debt->remaining_amount,
            'status' => $debt->status,
            'payments' => $debt->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'method' => $p->method,
                'status' => $p->status,
                'created_at' => $p->created_at->format('d/m/Y H:i'),
            ]),
        ];
    }
}
