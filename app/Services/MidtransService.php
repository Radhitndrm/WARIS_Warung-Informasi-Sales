<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapTransaction(Order $order): object
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_no,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => (string) $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            })->toArray(),
        ];

        return Snap::createTransaction($params);
    }

    public function createDebtTransaction(Debt $debt, int $amount, string $orderId): object
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $debt->customer_name,
                'phone' => $debt->customer_phone,
            ],
            'item_details' => [
                [
                    'id' => 'DEBT-' . $debt->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Utang #' . $debt->id . ' - ' . $debt->customer_name,
                ],
            ],
        ];

        return Snap::createTransaction($params);
    }

    public function getTransactionStatus(string $orderId): object
    {
        return Transaction::status($orderId);
    }
}
