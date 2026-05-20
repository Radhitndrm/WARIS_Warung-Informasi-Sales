<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $products = Product::where('is_active', true)->get();

        for ($i = 0; $i < 30; $i++) {
            $daysAgo = rand(0, 6);
            $createdAt = Carbon::now()->subDays($daysAgo)->setTime(rand(8, 20), rand(0, 59));

            $date = $createdAt->format('Ymd');
            $seq = str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $invoiceNo = "INV-{$date}-{$seq}";

            $itemCount = rand(1, 4);
            $pickedProducts = $products->random($itemCount);

            $orderItems = [];
            $total = 0;

            foreach ($pickedProducts as $product) {
                $qty = rand(1, 3);
                $subtotal = $product->price * $qty;
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $product->price,
                    'subtotal'   => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id'    => $user->id,
                'invoice_no' => $invoiceNo,
                'total'      => $total,
                'status'     => 'paid',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create(array_merge($item, [
                    'order_id'   => $order->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]));
            }

            $method = rand(0, 1) ? 'cash' : 'qris';
            $amount = $method === 'cash'
                ? $total + rand(0, 5) * 1000
                : $total;

            Payment::create([
                'order_id'      => $order->id,
                'method'        => $method,
                'amount'        => $amount,
                'change_amount' => $amount - $total,
                'midtrans_id'   => $method === 'qris' ? 'QRIS-' . strtoupper(uniqid()) : null,
                'status'        => 'success',
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);
        }
    }
}
