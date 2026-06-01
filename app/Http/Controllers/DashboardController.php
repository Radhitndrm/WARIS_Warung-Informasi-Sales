<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;

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

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('l');
            $chartData[] = Order::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('total');
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
        ));
    }
}