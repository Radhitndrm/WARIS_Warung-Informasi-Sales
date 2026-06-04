<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DbContextService
{
    public function getContext(string $message): string
    {
        $ctx = "# DATA TOKO WARIS\n";
        $ctx .= $this->getBasicInfo();

        $lower = strtolower($message);

        if (str_contains($lower, 'kategori') || str_contains($lower, 'category')) {
            $ctx .= "\n" . $this->getCategories();
        }

        if (
            str_contains($lower, 'produk') || str_contains($lower, 'product') ||
            str_contains($lower, 'barang') || str_contains($lower, 'harga') ||
            str_contains($lower, 'price') || str_contains($lower, 'laba') ||
            str_contains($lower, 'keuntungan')
        ) {
            $ctx .= "\n" . $this->getProducts();
        }

        if (str_contains($lower, 'stok') || str_contains($lower, 'stock')) {
            $ctx .= "\n" . $this->getStockInfo();
        }

        $productMatch = $this->findProduct($message);
        if ($productMatch) {
            $ctx .= "\n{$productMatch}";
        }

        if (
            str_contains($lower, 'penjualan') || str_contains($lower, 'sales') ||
            str_contains($lower, 'transaksi') || str_contains($lower, 'omzet') ||
            str_contains($lower, 'pendapatan')
        ) {
            $ctx .= "\n" . $this->getSalesSummary();
        }

        if (
            str_contains($lower, 'laku') || str_contains($lower, 'terjual') ||
            str_contains($lower, 'best seller') || str_contains($lower, 'populer') ||
            str_contains($lower, 'laris')
        ) {
            $ctx .= "\n" . $this->getBestSellers();
        }

        // Rekomendasi produk
        if (
            str_contains($lower, 'rekomendasi') || str_contains($lower, 'saran') ||
            str_contains($lower, 'uang') || str_contains($lower, 'budget') ||
            str_contains($lower, 'paket') || str_contains($lower, 'bundling') ||
            str_contains($lower, 'murah') || str_contains($lower, 'hemat')
        ) {
            $ctx .= "\n" . $this->getProductRecommendations($message);
        }

        // Prediksi stok
        if (
            str_contains($lower, 'prediksi') || str_contains($lower, 'estimasi') ||
            str_contains($lower, 'kapan habis') || str_contains($lower, 'berapa lama') ||
            str_contains($lower, 'perkiraan')
        ) {
            $ctx .= "\n" . $this->getStockPrediction();
        }

        // Perbandingan
        if (
            str_contains($lower, 'banding') || str_contains($lower, 'vs') ||
            str_contains($lower, 'lebih') || str_contains($lower, 'naik') ||
            str_contains($lower, 'turun') || str_contains($lower, 'growth') ||
            str_contains($lower, 'perbandingan') || str_contains($lower, 'sebelumnya') ||
            str_contains($lower, 'kemarin') || str_contains($lower, 'lalu')
        ) {
            $ctx .= "\n" . $this->getComparisonReport($message);
        }

        // Bantuan
        if (
            str_contains($lower, 'bantuan') || str_contains($lower, 'help') ||
            str_contains($lower, 'gimana') || str_contains($lower, 'cara') ||
            str_contains($lower, 'panduan') || str_contains($lower, 'tolong') ||
            str_contains($lower, 'bagaimana')
        ) {
            $ctx .= "\n" . $this->getHelpGuide($message);
        }

        // Margin / laba
        if (
            str_contains($lower, 'margin') || str_contains($lower, 'untung') ||
            str_contains($lower, 'laba') || str_contains($lower, 'harga jual') ||
            str_contains($lower, 'modal') || str_contains($lower, 'profit')
        ) {
            $ctx .= "\n" . $this->getMarginRecommendation($message);
        }

        return $ctx;
    }

    // ==================== BANTUAN CEPAT ====================

    protected function getHelpGuide(string $message): string
    {
        $lower = strtolower($message);
        $guide = "PANDUAN PENGGUNAAN WARIS:\n\n";

        if (str_contains($lower, 'kategori')) {
            $guide .= "— KATEGORI —\n";
            $guide .= "• Lihat kategori: buka menu Kategori\n";
            $guide .= "• Tambah: klik 'Tambah Kategori', isi nama, simpan\n";
            $guide .= "• Edit: klik 'Edit' pada kategori yang ingin diubah\n";
            $guide .= "• Hapus: hanya bisa jika tidak ada produk di dalamnya\n";
        } elseif (str_contains($lower, 'produk') || str_contains($lower, 'barang')) {
            $guide .= "— PRODUK —\n";
            $guide .= "• Lihat produk: buka menu Produk\n";
            $guide .= "• Tambah: klik 'Tambah Produk', isi nama, kategori, harga beli, harga jual, stok, upload gambar\n";
            $guide .= "• Edit: klik 'Edit' pada produk, ubah data, simpan\n";
            $guide .= "• Hapus: klik 'Hapus', konfirmasi di modal\n";
            $guide .= "• Upload gambar: format JPEG/PNG/WebP, maks 2MB\n";
            $guide .= "• Laba: selisih harga jual - harga beli, terlihat di tabel produk & dashboard\n";
        } elseif (str_contains($lower, 'kasir') || str_contains($lower, 'jual') || str_contains($lower, 'transaksi')) {
            $guide .= "— KASIR —\n";
            $guide .= "• Buka menu Kasir\n";
            $guide .= "• Pilih kategori untuk filter produk (atau tampilkan semua)\n";
            $guide .= "• Klik produk untuk menambah ke keranjang\n";
            $guide .= "• Atur jumlah dengan tombol +/-\n";
            $guide .= "• Pilih metode bayar: Tunai atau QRIS\n";
            $guide .= "• Jika tunai, masukkan jumlah bayar\n";
            $guide .= "• Klik 'Bayar' untuk memproses transaksi\n";
            $guide .= "• Gunakan ikon mic untuk cari produk pakai suara\n";
        } elseif (str_contains($lower, 'chat') || str_contains($lower, 'ai')) {
            $guide .= "— CHATBOT AI —\n";
            $guide .= "• Klik ikon chat di pojok kanan bawah dashboard\n";
            $guide .= "• Tanya tentang produk, stok, penjualan, dll\n";
            $guide .= "• Gunakan quick button untuk pertanyaan cepat\n";
            $guide .= "• Klik ikon mic untuk bicara langsung\n";
        } elseif (str_contains($lower, 'laporan') || str_contains($lower, 'riwayat')) {
            $guide .= "— LAPORAN —\n";
            $guide .= "• Cek dashboard untuk ringkasan penjualan hari ini\n";
            $guide .= "• Grafik penjualan 7 hari tersedia di dashboard\n";
            $guide .= "• Buka menu Riwayat untuk lihat transaksi sebelumnya\n";
            $guide .= "• Tanya chatbot untuk perbandingan penjualan\n";
        } else {
            $guide .= "Silakan tanya tentang fitur berikut:\n";
            $guide .= "• Kategori — kelola kategori produk\n";
            $guide .= "• Produk — kelola daftar produk\n";
            $guide .= "• Kasir — cara menggunakan POS\n";
            $guide .= "• Chatbot — cara pakai AI\n";
            $guide .= "• Laporan — cek penjualan & riwayat\n";
            $guide .= "Contoh: 'cara tambah produk', 'gimana pakai kasir'\n";
        }

        return $guide;
    }

    // ==================== REKOMENDASI PRODUK ====================

    protected function getProductRecommendations(string $message): string
    {
        $lower = strtolower($message);
        $lines = "REKOMENDASI PRODUK:\n";

        // Extract budget from message
        $budget = $this->extractNumber($message);

        // Budget-based recommendations
        if ($budget > 0) {
            $affordableProducts = Product::where('is_active', true)
                ->where('price', '<=', $budget)
                ->orderBy('price')
                ->get();

            if ($affordableProducts->isNotEmpty()) {
                $lines .= "\nProduk dengan harga ≤ Rp" . number_format($budget, 0, ',', '.') . ":\n";
                foreach ($affordableProducts as $p) {
                    $harga = number_format($p->price, 0, ',', '.');
                    $lines .= "- {$p->name}: Rp{$harga} (stok {$p->stock})\n";
                }

                // Suggest combinations
                $lines .= "\nKombinasi yang bisa dibeli:\n";
                $remaining = $budget;
                $comboProducts = Product::where('is_active', true)
                    ->where('price', '<=', $remaining)
                    ->orderBy('price')
                    ->get();

                $comboItems = [];
                foreach ($comboProducts as $p) {
                    if ($p->price <= $remaining) {
                        $qty = min((int)($remaining / $p->price), $p->stock);
                        if ($qty > 0) {
                            $comboItems[] = "{$qty}x {$p->name}";
                            $remaining -= $p->price * $qty;
                        }
                    }
                }
                if (!empty($comboItems)) {
                    $lines .= "- " . implode(" + ", $comboItems) . "\n";
                    $sisa = number_format($remaining, 0, ',', '.');
                    $lines .= "- Sisa uang: Rp{$sisa}\n";
                }
            } else {
                $lines .= "Tidak ada produk dalam range harga Rp" . number_format($budget, 0, ',', '.') . "\n";
            }
        }

        // Bundle/paket recommendations
        if (str_contains($lower, 'paket') || str_contains($lower, 'bundling') || $budget === 0) {
            $categories = Category::withCount('products')->having('products_count', '>=', 2)->get();
            if ($categories->isNotEmpty()) {
                $cat = $categories->random();
                $products = Product::where('is_active', true)
                    ->where('category_id', $cat->id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();

                if ($products->count() >= 2) {
                    $total = $products->sum('price');
                    $lines .= "\nPaket {$cat->name}:\n";
                    foreach ($products as $p) {
                        $harga = number_format($p->price, 0, ',', '.');
                        $lines .= "- {$p->name}: Rp{$harga}\n";
                    }
                    $lines .= "Total paket: Rp" . number_format($total, 0, ',', '.') . "\n";
                }
            }

            // Cheapest products
            $murah = Product::where('is_active', true)
                ->orderBy('price')
                ->take(5)
                ->get();
            $lines .= "\nProduk termurah:\n";
            foreach ($murah as $p) {
                $harga = number_format($p->price, 0, ',', '.');
                $lines .= "- {$p->name}: Rp{$harga} (stok {$p->stock})\n";
            }
        }

        return $lines;
    }

    // ==================== PREDIKSI STOK ====================

    protected function getStockPrediction(): string
    {
        $lines = "PREDIKSI STOK:\n";

        $thirtyDaysAgo = now()->subDays(30);

        $salesData = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('COUNT(DISTINCT orders.id) as order_count')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->where('orders.created_at', '>=', $thirtyDaysAgo)
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->get();

        if ($products->isEmpty()) {
            return $lines . "Tidak ada produk dengan stok tersedia.\n";
        }

        $lines .= "\nEstimasi kehabisan stok:\n";
        $needsRestock = [];

        foreach ($products as $p) {
            $sale = $salesData->get($p->id);
            $avgDaily = $sale ? ($sale->total_sold / 30) : 0;

            if ($avgDaily > 0) {
                $daysLeft = (int)($p->stock / $avgDaily);
                $dateEstimate = now()->addDays($daysLeft)->format('d/m/Y');

                if ($daysLeft <= 7) {
                    $needsRestock[] = [
                        'name' => $p->name,
                        'stock' => $p->stock,
                        'days_left' => $daysLeft,
                        'avg_sold' => round($avgDaily, 1),
                    ];
                }

                $lines .= "- {$p->name}: stok {$p->stock}, rata-rata terjual {$avgDaily}/hari";
                if ($daysLeft < 30) {
                    $lines .= ", diperkirakan habis {$dateEstimate} ({$daysLeft} hari lagi)";
                } else {
                    $lines .= ", estimasi stok aman > 30 hari";
                }
                $lines .= "\n";
            } else {
                $lines .= "- {$p->name}: stok {$p->stock} (belum ada data penjualan)\n";
            }
        }

        if (!empty($needsRestock)) {
            $lines .= "\n⚠️ PRODUK YANG PERLU SEGERA RESTOCK:\n";
            foreach ($needsRestock as $nr) {
                $lines .= "- {$nr['name']}: sisa {$nr['stock']}, habis dalam {$nr['days_left']} hari (terjual {$nr['avg_sold']}/hari)\n";
            }
        }

        return $lines;
    }

    // ==================== PERBANDINGAN ====================

    protected function getComparisonReport(string $message): string
    {
        $lower = strtolower($message);
        $lines = "LAPORAN PERBANDINGAN:\n";

        $isWeekly = str_contains($lower, 'minggu') || str_contains($lower, 'week');
        $isMonthly = str_contains($lower, 'bulan') || str_contains($lower, 'month');

        if ($isMonthly) {
            // This month vs last month
            $thisStart = now()->startOfMonth();
            $lastStart = now()->subMonth()->startOfMonth();
            $lastEnd = now()->subMonth()->endOfMonth();

            $thisTotal = Order::where('status', 'paid')
                ->whereDate('created_at', '>=', $thisStart)
                ->sum('total');
            $thisCount = Order::where('status', 'paid')
                ->whereDate('created_at', '>=', $thisStart)
                ->count();

            $lastTotal = Order::where('status', 'paid')
                ->whereDate('created_at', '>=', $lastStart)
                ->whereDate('created_at', '<=', $lastEnd)
                ->sum('total');
            $lastCount = Order::where('status', 'paid')
                ->whereDate('created_at', '>=', $lastStart)
                ->whereDate('created_at', '<=', $lastEnd)
                ->count();

            $growth = $lastTotal > 0 ? round((($thisTotal - $lastTotal) / $lastTotal) * 100) : ($thisTotal > 0 ? 100 : 0);
            $growthCount = $lastCount > 0 ? round((($thisCount - $lastCount) / $lastCount) * 100) : ($thisCount > 0 ? 100 : 0);

            $bulanIni = now()->translatedFormat('F');
            $bulanLalu = now()->subMonth()->translatedFormat('F');

            $lines .= "\nBulan ini ({$bulanIni}):\n";
            $lines .= "  Penjualan: Rp" . number_format($thisTotal, 0, ',', '.') . " ({$thisCount} transaksi)\n";
            $lines .= "Bulan lalu ({$bulanLalu}):\n";
            $lines .= "  Penjualan: Rp" . number_format($lastTotal, 0, ',', '.') . " ({$lastCount} transaksi)\n";
            $lines .= "Perubahan: {$growth}% (nominal) | {$growthCount}% (transaksi)\n";
        } else {
            // This week vs last week (default)
            $thisStart = now()->startOfWeek();
            $thisEnd = now()->endOfWeek();
            $lastStart = now()->subWeek()->startOfWeek();
            $lastEnd = now()->subWeek()->endOfWeek();

            // Today vs yesterday
            if (str_contains($lower, 'hari') || str_contains($lower, 'sekarang') || str_contains($lower, 'today')) {
                $todayTotal = Order::where('status', 'paid')
                    ->whereDate('created_at', now())
                    ->sum('total');
                $todayCount = Order::where('status', 'paid')
                    ->whereDate('created_at', now())
                    ->count();

                $yesterdayTotal = Order::where('status', 'paid')
                    ->whereDate('created_at', now()->subDay())
                    ->sum('total');
                $yesterdayCount = Order::where('status', 'paid')
                    ->whereDate('created_at', now()->subDay())
                    ->count();

                $growth = $yesterdayTotal > 0 ? round((($todayTotal - $yesterdayTotal) / $yesterdayTotal) * 100) : ($todayTotal > 0 ? 100 : 0);

                $lines .= "\nHari ini:\n";
                $lines .= "  Penjualan: Rp" . number_format($todayTotal, 0, ',', '.') . " ({$todayCount} transaksi)\n";
                $lines .= "Kemarin:\n";
                $lines .= "  Penjualan: Rp" . number_format($yesterdayTotal, 0, ',', '.') . " ({$yesterdayCount} transaksi)\n";
                $lines .= "Perubahan: {$growth}%\n";
            } else {
                $thisTotal = Order::where('status', 'paid')
                    ->whereDate('created_at', '>=', $thisStart)
                    ->whereDate('created_at', '<=', $thisEnd)
                    ->sum('total');
                $thisCount = Order::where('status', 'paid')
                    ->whereDate('created_at', '>=', $thisStart)
                    ->whereDate('created_at', '<=', $thisEnd)
                    ->count();

                $lastTotal = Order::where('status', 'paid')
                    ->whereDate('created_at', '>=', $lastStart)
                    ->whereDate('created_at', '<=', $lastEnd)
                    ->sum('total');
                $lastCount = Order::where('status', 'paid')
                    ->whereDate('created_at', '>=', $lastStart)
                    ->whereDate('created_at', '<=', $lastEnd)
                    ->count();

                $growth = $lastTotal > 0 ? round((($thisTotal - $lastTotal) / $lastTotal) * 100) : ($thisTotal > 0 ? 100 : 0);
                $growthCount = $lastCount > 0 ? round((($thisCount - $lastCount) / $lastCount) * 100) : ($thisCount > 0 ? 100 : 0);

                $lines .= "\nMinggu ini:\n";
                $lines .= "  Penjualan: Rp" . number_format($thisTotal, 0, ',', '.') . " ({$thisCount} transaksi)\n";
                $lines .= "Minggu lalu:\n";
                $lines .= "  Penjualan: Rp" . number_format($lastTotal, 0, ',', '.') . " ({$lastCount} transaksi)\n";
                $lines .= "Perubahan: {$growth}% (nominal) | {$growthCount}% (jumlah transaksi)\n";
            }
        }

        return $lines;
    }

    // ==================== REKOMENDASI MARGIN ====================

    protected function getMarginRecommendation(string $message): string
    {
        $lower = strtolower($message);
        $lines = "ANALISIS LABA & MARGIN:\n";

        $modal = $this->extractNumber($message);

        if ($modal > 0) {
            $lines .= "\nModal terdeteksi: Rp" . number_format($modal, 0, ',', '.') . "\n";
            $margins = [10, 20, 30, 40, 50];
            foreach ($margins as $m) {
                $hargaJual = (int)($modal * (1 + $m / 100));
                $untung = $hargaJual - $modal;
                $lines .= "- Margin {$m}%: Rp" . number_format($hargaJual, 0, ',', '.') . " (untung Rp" . number_format($untung, 0, ',', '.') . ")\n";
            }
        }

        $lines .= "\nLaba per produk (data aktual):\n";
        $products = Product::where('is_active', true)
            ->with('category')
            ->where('purchase_price', '>', 0)
            ->orderByDesc(DB::raw('price - purchase_price'))
            ->get();

        if ($products->isNotEmpty()) {
            foreach ($products as $p) {
                $laba = $p->price - $p->purchase_price;
                $margin = round(($laba / $p->purchase_price) * 100);
                $labaStr = number_format($laba, 0, ',', '.');
                $lines .= "- {$p->name}: laba Rp{$labaStr} (margin {$margin}%)\n";
            }
        }

        $lines .= "\nRingkasan laba per kategori:\n";
        $categories = Category::withCount('products')->having('products_count', '>', 0)->get();

        foreach ($categories as $cat) {
            $catProducts = Product::where('is_active', true)
                ->where('category_id', $cat->id)
                ->where('purchase_price', '>', 0)
                ->get();

            if ($catProducts->isEmpty()) continue;

            $avgPrice = $catProducts->avg('price');
            $avgModal = $catProducts->avg('purchase_price');
            $avgLaba = (int)$avgPrice - (int)$avgModal;
            $avgMargin = $avgModal > 0 ? round(($avgLaba / $avgModal) * 100) : 0;

            $lines .= "- {$cat->name}: rata-rata laba Rp" . number_format($avgLaba, 0, ',', '.') . " (margin {$avgMargin}%)\n";
        }

        return $lines;
    }

    // ==================== EXISTING METHODS ====================

    protected function getBasicInfo(): string
    {
        $totalProduk = Product::where('is_active', true)->count();
        $totalKategori = Category::count();

        return "Total produk aktif: {$totalProduk}\nTotal kategori: {$totalKategori}\n";
    }

    protected function getCategories(): string
    {
        $categories = Category::withCount('products')->get();
        if ($categories->isEmpty()) {
            return "Tidak ada kategori.\n";
        }

        $lines = "Daftar kategori:\n";
        foreach ($categories as $cat) {
            $lines .= "- {$cat->name} ({$cat->products_count} produk)\n";
        }
        return $lines;
    }

    protected function getProducts(): string
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return "Tidak ada produk aktif.\n";
        }

        $lines = "Daftar produk:\n";
        foreach ($products as $p) {
            $harga = number_format($p->price, 0, ',', '.');
            $hargaBeli = number_format($p->purchase_price, 0, ',', '.');
            $laba = $p->price - $p->purchase_price;
            $labaStr = number_format($laba, 0, ',', '.');
            $margin = $p->purchase_price > 0 ? round(($laba / $p->purchase_price) * 100) : 0;
            $lines .= "- {$p->name} | {$p->category->name} | Beli: Rp{$hargaBeli} | Jual: Rp{$harga} | Laba: Rp{$labaStr} ({$margin}%) | stok {$p->stock}\n";
        }
        return $lines;
    }

    protected function getStockInfo(): string
    {
        $lowStock = Product::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        if ($lowStock->isNotEmpty()) {
            $lines = "Produk stok menipis:\n";
            foreach ($lowStock as $p) {
                $lines .= "- {$p->name}: stok {$p->stock}\n";
            }
            return $lines;
        }

        return "Semua produk stok cukup.\n";
    }

    protected function getSalesSummary(): string
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        $todayTotal = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');

        $todayCount = Order::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->count();

        $todayLaba = OrderItem::whereHas('order', fn ($q) => $q->whereDate('created_at', $today)->where('status', 'paid'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('(order_items.price - products.purchase_price) * order_items.quantity'));

        $weekTotal = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', $weekStart)
            ->sum('total');

        $weekCount = Order::where('status', 'paid')
            ->whereDate('created_at', '>=', $weekStart)
            ->count();

        $weekLaba = OrderItem::whereHas('order', fn ($q) => $q->whereDate('created_at', '>=', $weekStart)->where('status', 'paid'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('(order_items.price - products.purchase_price) * order_items.quantity'));

        $hariIni = number_format($todayTotal, 0, ',', '.');
        $labaIni = number_format($todayLaba, 0, ',', '.');
        $mingguIni = number_format($weekTotal, 0, ',', '.');
        $labaMinggu = number_format($weekLaba, 0, ',', '.');

        return "Penjualan hari ini: Rp{$hariIni} ({$todayCount} transaksi)\nLaba hari ini: Rp{$labaIni}\nPenjualan minggu ini: Rp{$mingguIni} ({$weekCount} transaksi)\nLaba minggu ini: Rp{$labaMinggu}\n";
    }

    protected function getBestSellers(): string
    {
        $bestSellers = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();

        if ($bestSellers->isEmpty()) {
            return "Belum ada data penjualan.\n";
        }

        $lines = "Produk terlaris:\n";
        foreach ($bestSellers as $item) {
            $lines .= "- {$item->product->name}: terjual {$item->total_qty}\n";
        }
        return $lines;
    }

    protected function findProduct(string $message): ?string
    {
        $lowerMsg = strtolower($message);
        $products = Product::where('is_active', true)->get();
        foreach ($products as $p) {
            $lowerName = strtolower($p->name);
            $words = explode(' ', $lowerName);
            if (str_contains($lowerMsg, $lowerName) || count(array_intersect(explode(' ', $lowerMsg), $words)) >= 2) {
                $harga = number_format($p->price, 0, ',', '.');
                $hargaBeli = number_format($p->purchase_price, 0, ',', '.');
                $laba = $p->price - $p->purchase_price;
                $labaStr = number_format($laba, 0, ',', '.');
                $kategori = $p->category->name ?? '-';
                return "Produk: {$p->name} | Kategori: {$kategori} | Harga Beli: Rp{$hargaBeli} | Harga Jual: Rp{$harga} | Laba: Rp{$labaStr} | Stok: {$p->stock}";
            }
        }
        return null;
    }

    // ==================== UTILITY ====================

    protected function extractNumber(string $message): int
    {
        // Remove currency formatting and extract numbers
        $cleaned = str_replace(['.', ',', 'rp', 'Rp', 'Rp.', 'rp.'], '', $message);
        preg_match_all('/\d+/', $cleaned, $matches);

        if (!empty($matches[0])) {
            return (int)max($matches[0]);
        }

        return 0;
    }
}
