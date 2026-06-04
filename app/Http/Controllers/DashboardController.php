<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPenjualanHariIni = Order::whereDate('created_at', today())
            ->where('status', 'paid')
            ->sum('total');

        $penjualanKemarin = Order::whereDate('created_at', today()->subDay())
            ->where('status', 'paid')
            ->sum('total');

        $penjualanGrowth = $penjualanKemarin > 0
            ? round((($totalPenjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100)
            : ($totalPenjualanHariIni > 0 ? 100 : 0);

        $jumlahTransaksi = Order::whereDate('created_at', today())
            ->where('status', 'paid')
            ->count();

        $transaksiKemarin = Order::whereDate('created_at', today()->subDay())
            ->where('status', 'paid')
            ->count();

        $transaksiGrowth = $transaksiKemarin > 0
            ? round((($jumlahTransaksi - $transaksiKemarin) / $transaksiKemarin) * 100)
            : ($jumlahTransaksi > 0 ? 100 : 0);

        $produkAktif = Product::where('is_active', true)->count();

        $produkStokMenipis = Product::where('stock', '<=', 5)->orderBy('stock')->get();

        $stokMenipis = $produkStokMenipis->count();

        $labaHariIni = OrderItem::whereHas('order', fn ($q) => $q->whereDate('created_at', today())->where('status', 'paid'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('(order_items.price - products.purchase_price) * order_items.quantity'));

        $labaKemarin = OrderItem::whereHas('order', fn ($q) => $q->whereDate('created_at', today()->subDay())->where('status', 'paid'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('(order_items.price - products.purchase_price) * order_items.quantity'));

        $labaGrowth = $labaKemarin > 0
            ? round((($labaHariIni - $labaKemarin) / $labaKemarin) * 100)
            : ($labaHariIni > 0 ? 100 : 0);

        $chartLabels = [];
        $chartData = [];
        $labaChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('l');
            $chartData[] = Order::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('total');
            $labaChartData[] = OrderItem::whereHas('order', fn ($q) => $q->whereDate('created_at', $date)->where('status', 'paid'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->sum(DB::raw('(order_items.price - products.purchase_price) * order_items.quantity'));
        }

        return view('dashboard.index', compact(
            'totalPenjualanHariIni',
            'penjualanGrowth',
            'jumlahTransaksi',
            'transaksiGrowth',
            'produkAktif',
            'stokMenipis',
            'produkStokMenipis',
            'chartLabels',
            'chartData',
            'labaHariIni',
            'labaGrowth',
            'labaChartData',
        ));
    }
}