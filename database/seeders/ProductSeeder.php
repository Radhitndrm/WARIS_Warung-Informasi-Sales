<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Minuman
            ['name' => 'Aqua 600ml', 'price' => 3000, 'purchase_price' => 2200, 'stock' => 50],
            ['name' => 'Teh Botol Sosro 350ml', 'price' => 5000, 'purchase_price' => 3800, 'stock' => 40],
            ['name' => 'Kopi Kapal Api Sachet', 'price' => 2000, 'purchase_price' => 1400, 'stock' => 3],
            ['name' => 'Pocari Sweat 350ml', 'price' => 7000, 'purchase_price' => 5500, 'stock' => 25],
            // Makanan Ringan
            ['name' => 'Chitato Original 68g', 'price' => 10000, 'purchase_price' => 7500, 'stock' => 30],
            ['name' => 'Indomie Goreng', 'price' => 3500, 'purchase_price' => 2700, 'stock' => 2],
            ['name' => 'Oreo Original', 'price' => 5000, 'purchase_price' => 3800, 'stock' => 20],
            ['name' => 'Supermi Ayam Bawang', 'price' => 3000, 'purchase_price' => 2300, 'stock' => 35],
            // Rokok
            ['name' => 'Gudang Garam Merah', 'price' => 25000, 'purchase_price' => 22000, 'stock' => 15],
            ['name' => 'Sampoerna Mild', 'price' => 28000, 'purchase_price' => 25000, 'stock' => 4],
            ['name' => 'Djarum Super', 'price' => 24000, 'purchase_price' => 21000, 'stock' => 10],
            // Sembako
            ['name' => 'Beras Ramos 1kg', 'price' => 15000, 'purchase_price' => 13500, 'stock' => 20],
            ['name' => 'Gula Pasir 1kg', 'price' => 14000, 'purchase_price' => 12500, 'stock' => 3],
            ['name' => 'Minyak Goreng Bimoli 1L', 'price' => 20000, 'purchase_price' => 18000, 'stock' => 12],
            ['name' => 'Telur Ayam 1kg', 'price' => 28000, 'purchase_price' => 25000, 'stock' => 8],
            // Kebersihan
            ['name' => 'Sabun Mandi Lifebuoy', 'price' => 5000, 'purchase_price' => 3500, 'stock' => 22],
            ['name' => 'Shampo Pantene Sachet', 'price' => 2000, 'purchase_price' => 1200, 'stock' => 2],
            ['name' => 'Deterjen Rinso 800g', 'price' => 22000, 'purchase_price' => 18000, 'stock' => 18],
            ['name' => 'Odol Pepsodent 120g', 'price' => 10000, 'purchase_price' => 7500, 'stock' => 14],
            ['name' => 'Tisu Paseo 250 lembar', 'price' => 8000, 'purchase_price' => 6000, 'stock' => 9],
        ];

        $categoryMap = Category::pluck('id', 'name');

        $categoryForProduct = [
            'Aqua 600ml'              => 'Minuman',
            'Teh Botol Sosro 350ml'   => 'Minuman',
            'Kopi Kapal Api Sachet'   => 'Minuman',
            'Pocari Sweat 350ml'      => 'Minuman',
            'Chitato Original 68g'    => 'Makanan Ringan',
            'Indomie Goreng'          => 'Makanan Ringan',
            'Oreo Original'           => 'Makanan Ringan',
            'Supermi Ayam Bawang'     => 'Makanan Ringan',
            'Gudang Garam Merah'      => 'Rokok',
            'Sampoerna Mild'          => 'Rokok',
            'Djarum Super'            => 'Rokok',
            'Beras Ramos 1kg'         => 'Sembako',
            'Gula Pasir 1kg'          => 'Sembako',
            'Minyak Goreng Bimoli 1L' => 'Sembako',
            'Telur Ayam 1kg'          => 'Sembako',
            'Sabun Mandi Lifebuoy'    => 'Kebersihan',
            'Shampo Pantene Sachet'   => 'Kebersihan',
            'Deterjen Rinso 800g'     => 'Kebersihan',
            'Odol Pepsodent 120g'     => 'Kebersihan',
            'Tisu Paseo 250 lembar'   => 'Kebersihan',
        ];

        foreach ($products as $data) {
            Product::create([
                'category_id'    => $categoryMap[$categoryForProduct[$data['name']]],
                'name'           => $data['name'],
                'price'          => $data['price'],
                'purchase_price' => $data['purchase_price'],
                'stock'          => $data['stock'],
                'is_active'      => true,
            ]);
        }
    }
}
