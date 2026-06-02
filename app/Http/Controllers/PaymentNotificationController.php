<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentNotificationController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $serverKey = config('services.midtrans.server_key');
            $input = $request->all();

            $orderId = $input['order_id'] ?? null;
            $statusCode = $input['status_code'] ?? '';
            $grossAmount = $input['gross_amount'] ?? '';
            $signatureKey = $input['signature_key'] ?? '';
            $transactionStatus = $input['transaction_status'] ?? '';
            $fraudStatus = $input['fraud_status'] ?? '';

            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans: Invalid signature', [
                    'order_id' => $orderId,
                    'expected' => $expectedSignature,
                    'received' => $signatureKey,
                ]);
                return response()->json(['status' => 'invalid signature'], 400);
            }

            $order = Order::with('payment')->where('invoice_no', $orderId)->first();

            if (!$order) {
                Log::warning('Midtrans: Order not found', ['order_id' => $orderId]);
                return response()->json(['status' => 'order not found'], 404);
            }

            if ($order->status === 'paid') {
                return response()->json(['status' => 'ok']);
            }

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $order->update(['status' => 'paid']);
                    $order->payment->update(['status' => 'success']);
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $order->payment->update(['status' => 'failed']);
            } elseif ($transactionStatus === 'pending') {
                $order->payment->update(['status' => 'pending']);
            }

            Log::info('Midtrans: Notification processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Midtrans: Notification error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
