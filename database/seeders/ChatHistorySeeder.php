<?php

namespace Database\Seeders;

use App\Models\ChatHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatHistorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $conversations = [
            ['role' => 'user',      'message' => 'Berapa stok Aqua 600ml sekarang?'],
            ['role' => 'assistant', 'message' => 'Stok Aqua 600ml saat ini: 50 pcs.'],
            ['role' => 'user',      'message' => 'Harga Indomie Goreng berapa?'],
            ['role' => 'assistant', 'message' => 'Harga Indomie Goreng adalah Rp3.500.'],
            ['role' => 'user',      'message' => 'Produk apa yang stoknya hampir habis?'],
            ['role' => 'assistant', 'message' => 'Produk stok rendah (<5): Kopi Kapal Api, Indomie Goreng, Sampoerna Mild, Gula Pasir, Shampo Pantene.'],
            ['role' => 'user',      'message' => 'Berapa total penjualan hari ini?'],
            ['role' => 'assistant', 'message' => 'Total penjualan hari ini adalah Rp342.000 dari 4 transaksi.'],
            ['role' => 'user',      'message' => 'Harga Gudang Garam Merah?'],
            ['role' => 'assistant', 'message' => 'Harga Gudang Garam Merah adalah Rp25.000 per bungkus.'],
        ];

        foreach ($conversations as $chat) {
            ChatHistory::create([
                'user_id' => $user->id,
                'role'    => $chat['role'],
                'message' => $chat['message'],
            ]);
        }
    }
}
