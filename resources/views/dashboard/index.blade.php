@extends('layouts.app')

@section('title', 'Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">Dashboard</h1>
            <p class="text-sm text-muted mt-1">Selamat datang, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Penjualan Hari Ini</p>
                    <h3 class="text-lg font-bold text-primary">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</h3>
                    <p class="text-xs font-semibold {{ $penjualanGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $penjualanGrowth >= 0 ? '↑' : '↓' }} {{ abs($penjualanGrowth) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-500/10 flex items-center justify-center text-green-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Laba Hari Ini</p>
                    <h3 class="text-lg font-bold text-green-700">Rp {{ number_format($labaHariIni, 0, ',', '.') }}</h3>
                    <p class="text-xs font-semibold {{ $labaGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $labaGrowth >= 0 ? '↑' : '↓' }} {{ abs($labaGrowth) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a6 6 0 0112 0m-12 0a6 6 0 0112 0m-12 0h12M12 15.75a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Jumlah Transaksi</p>
                    <h3 class="text-lg font-bold text-primary">{{ $jumlahTransaksi }} <span class="text-sm font-normal text-muted">Transaksi</span></h3>
                    <p class="text-xs font-semibold {{ $transaksiGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $transaksiGrowth >= 0 ? '↑' : '↓' }} {{ abs($transaksiGrowth) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Produk Aktif</p>
                    <h3 class="text-lg font-bold text-primary">{{ $produkAktif }} <span class="text-sm font-normal text-muted">Item</span></h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Stok Menipis</p>
                    <h3 class="text-lg font-bold text-amber-700">{{ $stokMenipis }} <span class="text-sm font-normal text-muted">Produk</span></h3>
                    <p class="text-xs font-semibold text-red-500">Segera Restock!</p>
                </div>
            </div>
        </div>
    </div>

    @if($jumlahUtangAktif > 0)
    <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-700 shrink-0">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-amber-800">Ada {{ $jumlahUtangAktif }} utang yang belum lunas</p>
                <p class="text-xs text-amber-700">Total sisa utang: <span class="font-bold">Rp {{ number_format($totalUtangAktif, 0, ',', '.') }}</span></p>
                <a href="{{ route('utang') }}" class="text-xs font-semibold text-amber-800 hover:text-amber-900 underline mt-1 inline-block">
                    Kelola utang →
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-primary">Grafik Penjualan (7 Hari)</h3>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="grid grid-cols-4 border-t border-gray-100 mt-4 pt-3 text-center text-xs">
                <div>
                    <p class="text-muted"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block mr-1.5"></span>Total Penjualan</p>
                    <p class="font-bold text-sm text-primary mt-0.5">Rp {{ number_format(array_sum($chartData), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-muted"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block mr-1.5"></span>Total Laba</p>
                    <p class="font-bold text-sm text-green-700 mt-0.5">Rp {{ number_format(array_sum($labaChartData), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-muted"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block mr-1.5"></span>Jumlah Transaksi</p>
                    <p class="font-bold text-sm text-primary mt-0.5">{{ $jumlahTransaksi }}</p>
                </div>
                <div>
                    <p class="text-muted"><span class="w-2.5 h-2.5 rounded-full bg-purple-400 inline-block mr-1.5"></span>Produk Aktif</p>
                    <p class="font-bold text-sm text-primary mt-0.5">{{ $produkAktif }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-primary">Produk Stok Menipis</h3>
                    <a href="#" class="text-xs text-muted hover:text-primary">Lihat Semua</a>
                </div>
                @if($produkStokMenipis->count() > 0)
                <table class="w-full text-xs text-left">
                    <thead class="text-muted border-b border-gray-100">
                        <tr>
                            <th class="pb-2 font-medium">Produk</th>
                            <th class="pb-2 text-center font-medium">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($produkStokMenipis as $produk)
                        <tr>
                            <td class="py-2 font-medium text-primary">{{ $produk->name }}</td>
                            <td class="py-2 text-center font-bold {{ $produk->stock <= 2 ? 'text-red-500' : 'text-amber-600' }}">
                                {{ $produk->stock }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-xs text-muted py-4 text-center">Tidak ada produk dengan stok menipis.</p>
                @endif
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-primary mb-4">Shortcut Cepat</h3>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-medium">
                    <a href="{{ route('kategori') }}" class="bg-blue-50 p-3 rounded-xl flex flex-col items-center gap-2 text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        </svg>
                        <span>Kategori Produk</span>
                    </a>
                    <a href="{{ route('produk') }}" class="bg-emerald-50 p-3 rounded-xl flex flex-col items-center gap-2 text-emerald-700 hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tambah Produk</span>
                    </a>
                    <a href="{{ route('kasir') }}" class="bg-cyan-50 p-3 rounded-xl flex flex-col items-center gap-2 text-cyan-700 hover:bg-cyan-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        <span>Buka Kasir</span>
                    </a>
                    <a href="{{ route('riwayat') }}" class="bg-rose-50 p-3 rounded-xl flex flex-col items-center gap-2 text-rose-700 hover:bg-rose-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Riwayat Transaksi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
setTimeout(() => {
    const ctx = document.getElementById('salesChart')?.getContext('2d');
    if (!ctx) return;

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    const gradientLaba = ctx.createLinearGradient(0, 0, 0, 300);
    gradientLaba.addColorStop(0, 'rgba(34, 197, 94, 0.25)');
    gradientLaba.addColorStop(1, 'rgba(34, 197, 94, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Penjualan',
                data: @json($chartData),
                borderColor: '#3B82F6',
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3B82F6',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4
            }, {
                label: 'Laba',
                data: @json($labaChartData),
                borderColor: '#22C55E',
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#22C55E',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                backgroundColor: gradientLaba,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'bottom', labels: { padding: 16, usePointStyle: true, font: { size: 11 } } } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp' + (v / 1000).toFixed(0) + 'k',
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
}, 200);
</script>
@endpush
