<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['payment', 'items.product', 'user', 'debt'])->latest();

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('metode')) {
            if ($request->metode === 'debt') {
                $query->where('status', 'debt');
            } else {
                $query->whereHas('payment', fn($q) => $q->where('method', $request->metode));
            }
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate(10)->withQueryString();

        $baseQuery = Order::with('payment')
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'),   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->get();

        $ringkasanMetode = $baseQuery
            ->groupBy(function ($o) {
                if ($o->status === 'debt') return 'debt';
                return optional($o->payment)->method ?? 'unknown';
            })
            ->map(fn($group) => $group->sum('total'));

        $ringkasanStatus = $baseQuery
            ->groupBy('status')
            ->map->count();

        // ── Stat Cards ──
        $today          = Carbon::today();
        $thisMonth      = Carbon::now()->startOfMonth();
        $yesterday      = Carbon::yesterday();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd   = Carbon::now()->subMonth()->endOfMonth();

        $totalTransaksiHariIni  = Order::whereDate('created_at', $today)->count();
        $totalPenjualanHariIni  = Order::whereDate('created_at', $today)->where('status', 'paid')->sum('total');
        $totalTransaksiBulanIni = Order::whereDate('created_at', '>=', $thisMonth)->count();
        $totalPenjualanBulanIni = Order::whereDate('created_at', '>=', $thisMonth)->where('status', 'paid')->sum('total');

        $transaksiKemarin   = Order::whereDate('created_at', $yesterday)->count();
        $penjualanKemarin   = Order::whereDate('created_at', $yesterday)->where('status', 'paid')->sum('total');
        $transaksiLastMonth = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $penjualanLastMonth = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->where('status', 'paid')->sum('total');

        $statCards = [
            [
                'label'  => 'Total Transaksi (Hari Ini)',
                'value'  => $totalTransaksiHariIni,
                'format' => 'number',
                'icon'   => 'bag',
                'color'  => 'blue',
                'change' => $this->calcChange($totalTransaksiHariIni, $transaksiKemarin),
            ],
            [
                'label'  => 'Total Penjualan (Hari Ini)',
                'value'  => $totalPenjualanHariIni,
                'format' => 'currency',
                'icon'   => 'chart',
                'color'  => 'green',
                'change' => $this->calcChange($totalPenjualanHariIni, $penjualanKemarin),
            ],
            [
                'label'  => 'Total Transaksi (Bulan Ini)',
                'value'  => $totalTransaksiBulanIni,
                'format' => 'number',
                'icon'   => 'trend',
                'color'  => 'red',
                'change' => $this->calcChange($totalTransaksiBulanIni, $transaksiLastMonth),
            ],
            [
                'label'  => 'Total Penjualan (Bulan Ini)',
                'value'  => $totalPenjualanBulanIni,
                'format' => 'currency',
                'icon'   => 'wallet',
                'color'  => 'purple',
                'change' => $this->calcChange($totalPenjualanBulanIni, $penjualanLastMonth),
            ],
        ];

        return view('reports.index', compact('orders', 'ringkasanMetode', 'ringkasanStatus', 'statCards'));
    }

    private function calcChange($current, $previous): array
    {
        if ($previous == 0) return ['value' => 0, 'up' => true];
        $pct = round((($current - $previous) / $previous) * 100, 1);
        return ['value' => abs($pct), 'up' => $pct >= 0];
    }

    public function exportPdf(Request $request)
    {
        $orders = $this->getFilteredOrders($request);
        $pdf = Pdf::loadView('reports.export-pdf', [
            'orders' => $orders,
            'from'   => $request->from,
            'to'     => $request->to,
        ])->setPaper('a4', 'landscape');
        return $pdf->download('riwayat-transaksi.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new TransaksiExport($request->from, $request->to),
            'riwayat-transaksi.xlsx'
        );
    }

    public function showInvoice(Order $order)
    {
        $order->load(['payment', 'items.product', 'user', 'debt.payments' => fn($q) => $q->latest()]);

        return view('reports.invoice', compact('order'));
    }

    private function getFilteredOrders(Request $request)
    {
        return Order::with(['payment', 'items.product', 'debt', 'user'])
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'),   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->get();
    }
}