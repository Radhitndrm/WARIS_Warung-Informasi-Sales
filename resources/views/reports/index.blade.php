@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-primary">Riwayat</h1>
        <p class="text-sm text-gray-500 mt-0.5">Lihat dan kelola semua riwayat transaksi</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @php
        $cardConfig = [
            'bag'    => ['bg' => 'bg-blue-100',   'icon_bg' => 'bg-blue-500',   'icon' => 'fa-bag-shopping'],
            'chart'  => ['bg' => 'bg-green-100',  'icon_bg' => 'bg-green-500',  'icon' => 'fa-chart-line'],
            'trend'  => ['bg' => 'bg-red-100',    'icon_bg' => 'bg-red-400',    'icon' => 'fa-arrow-trend-up'],
            'wallet' => ['bg' => 'bg-purple-100', 'icon_bg' => 'bg-purple-400', 'icon' => 'fa-wallet'],
        ];
        @endphp

        @foreach($statCards as $card)
        @php $cfg = $cardConfig[$card['icon']]; @endphp
        <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-4 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl {{ $cfg['icon_bg'] }} flex items-center justify-center shrink-0">
                <i class="fa-solid {{ $cfg['icon'] }} text-white text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 leading-tight">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-primary mt-0.5">
                    @if($card['format'] === 'currency')
                        Rp {{ number_format($card['value'], 0, ',', '.') }}
                    @else
                        {{ number_format($card['value'], 0, ',', '.') }}
                    @endif
                </p>
                <p class="text-xs mt-1 {{ $card['change']['up'] ? 'text-green-600' : 'text-red-500' }}">
                    <i class="fa-solid {{ $card['change']['up'] ? 'fa-arrow-up' : 'fa-arrow-down' }} text-[10px]"></i>
                    {{ $card['change']['value'] }}% dari kemarin
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('riwayat') }}" id="filterForm">
        <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-4">
            <div class="flex flex-wrap gap-3 items-end">

                <div class="relative flex-1 min-w-[180px]">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari produk..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-[#8C8A75]/50 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-sidebar/30 text-primary placeholder:text-gray-400">
                </div>

                <select name="metode"
                    class="px-4 py-2.5 text-sm bg-white border border-[#8C8A75]/50 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-sidebar/30 text-primary min-w-[150px]">
                    <option value="">Semua Metode</option>
                    <option value="cash"    {{ request('metode') === 'cash'    ? 'selected' : '' }}>Tunai</option>
                    <option value="qris"    {{ request('metode') === 'qris'    ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ request('metode') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="debt"    {{ request('metode') === 'debt'    ? 'selected' : '' }}>Utang</option>
                </select>

                <select name="status"
                    class="px-4 py-2.5 text-sm bg-white border border-[#8C8A75]/50 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-sidebar/30 text-primary min-w-[150px]">
                    <option value="">Semua Status</option>
                    <option value="paid"      {{ request('status') === 'paid'      ? 'selected' : '' }}>Selesai</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="debt"      {{ request('status') === 'debt'      ? 'selected' : '' }}>Utang</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <div class="flex items-center gap-2">
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="px-3 py-2.5 text-sm bg-white border border-[#8C8A75]/50 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-sidebar/30 text-primary">
                    <span class="text-gray-400 text-sm">—</span>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="px-3 py-2.5 text-sm bg-white border border-[#8C8A75]/50 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-sidebar/30 text-primary">
                </div>

                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2.5 bg-sidebar text-white text-sm font-semibold rounded-xl hover:bg-sidebar-hover transition-colors">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>

                <a href="{{ route('riwayat') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#8C8A75]/50 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                </a>

            </div>
        </div>
    </form>

    {{-- Tabel Transaksi --}}
    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-[#8C8A75]/30">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No.</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#8C8A75]/20">
                @forelse($orders as $order)
                <tr class="hover:bg-[#E6E4CE]/40 transition-colors">
                    <td class="px-5 py-4 text-sm text-gray-500">{{ $orders->firstItem() + $loop->index }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-primary">{{ $order->invoice_no }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        @if($order->status === 'debt' && $order->debt)
                            {{ $order->debt->customer_name }}
                        @else
                            {{ $order->user->name ?? 'Pelanggan Umum' }}
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-primary">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        @php $metode = optional($order->payment)->method ?? ($order->status === 'debt' ? 'debt' : null); @endphp
                        @if($metode === 'cash')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-[#BFDCDE] text-gray-800">
                                <i class="fa-solid fa-money-bill-wave text-[10px]"></i> Tunai
                            </span>
                        @elseif($metode === 'qris')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-[#DBC5E8] text-gray-800">
                                <i class="fa-solid fa-qrcode text-[10px]"></i> QRIS
                            </span>
                        @elseif($metode === 'ewallet')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-[#FDE68A] text-gray-800">
                                <i class="fa-solid fa-wallet text-[10px]"></i> E-Wallet
                            </span>
                        @elseif($metode === 'debt')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                <i class="fa-solid fa-file-invoice-dollar text-[10px]"></i> Utang
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($order->status === 'paid')
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-[#C1F2D0] text-green-800">Selesai</span>
                        @elseif($order->status === 'pending')
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-[#FEF9C3] text-yellow-800">Pending</span>
                        @elseif($order->status === 'debt')
                            @php
                                $isOverdue = $order->debt?->due_date && $order->debt->due_date->isPast();
                                $debtPercent = $order->debt ? round(($order->debt->paid_amount / $order->debt->total_amount) * 100) : 0;
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                @if($isOverdue) Jatuh Tempo @else Utang {{ $debtPercent }}% @endif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-[#F7CDCD] text-red-800">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('invoice.show', $order) }}"
                                title="Lihat Detail"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#BFDCDE] text-gray-700 hover:bg-[#A8C8CA] transition-colors">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('riwayat.export.pdf', ['from' => $order->created_at->format('Y-m-d'), 'to' => $order->created_at->format('Y-m-d')]) }}"
                                title="Cetak Struk"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#F4F2DE] border border-[#8C8A75]/50 text-gray-700 hover:bg-[#E6E4CE] transition-colors">
                                <i class="fa-solid fa-print text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-2xl text-gray-300"></i>
                        </div>
                        <p class="font-semibold text-gray-500">Belum ada transaksi</p>
                        <p class="text-sm mt-1">Transaksi akan muncul di sini setelah ada penjualan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-[#8C8A75]/20 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} transaksi
            </p>
            <div class="flex items-center gap-1">
                @if($orders->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-[#8C8A75]/40 text-gray-600 hover:bg-[#E6E4CE] transition-colors">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach($orders->getUrlRange(max(1, $orders->currentPage()-2), min($orders->lastPage(), $orders->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-colors
                            {{ $page == $orders->currentPage() ? 'bg-sidebar text-white' : 'bg-white border border-[#8C8A75]/40 text-gray-600 hover:bg-[#E6E4CE]' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-[#8C8A75]/40 text-gray-600 hover:bg-[#E6E4CE] transition-colors">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Bottom Section: Ringkasan + Export --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Ringkasan Metode Pembayaran --}}
        <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-5">
            <h3 class="text-sm font-bold text-primary mb-4">Ringkasan Metode Pembayaran</h3>
            <div class="flex gap-4 items-center">
                <div class="w-28 h-28 shrink-0">
                    <canvas id="chartMetode"></canvas>
                </div>
                <div class="flex-1 space-y-2">
                    @php
                        $metodePairs = [
                            'cash'    => ['label' => 'Tunai',    'color' => 'bg-[#BFDCDE]'],
                            'ewallet' => ['label' => 'E-Wallet', 'color' => 'bg-[#DBC5E8]'],
                            'qris'    => ['label' => 'QRIS',     'color' => 'bg-[#A8D5A2]'],
                            'debt'    => ['label' => 'Utang',    'color' => 'bg-amber-300'],
                        ];
                    @endphp
                    @foreach($metodePairs as $key => $meta)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $meta['color'] }}"></span>
                            <span class="text-xs text-gray-600">{{ $meta['label'] }}</span>
                        </div>
                        <span class="text-xs font-semibold text-primary">
                            Rp {{ number_format($ringkasanMetode[$key] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                    <div class="border-t border-[#8C8A75]/30 pt-2 flex items-center justify-between">
                        <span class="text-xs font-bold text-primary">Total</span>
                        <span class="text-xs font-bold text-primary">
                            Rp {{ number_format($ringkasanMetode->sum(), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Status Transaksi --}}
        <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-5">
            <h3 class="text-sm font-bold text-primary mb-4">Ringkasan Status Transaksi</h3>
            <div class="flex gap-4 items-center">
                <div class="w-28 h-28 shrink-0">
                    <canvas id="chartStatus"></canvas>
                </div>
                <div class="flex-1 space-y-2">
                    @php
                        $statusPairs = [
                            'paid'      => ['label' => 'Selesai',    'color' => 'bg-[#C1F2D0]'],
                            'pending'   => ['label' => 'Pending',    'color' => 'bg-[#FEF9C3]'],
                            'debt'      => ['label' => 'Utang',      'color' => 'bg-amber-200'],
                            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'bg-[#F7CDCD]'],
                        ];
                    @endphp
                    @foreach($statusPairs as $key => $meta)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $meta['color'] }}"></span>
                            <span class="text-xs text-gray-600">{{ $meta['label'] }}</span>
                        </div>
                        <span class="text-xs font-semibold text-primary">{{ $ringkasanStatus[$key] ?? 0 }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-[#8C8A75]/30 pt-2 flex items-center justify-between">
                        <span class="text-xs font-bold text-primary">Total</span>
                        <span class="text-xs font-bold text-primary">{{ $ringkasanStatus->sum() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Export --}}
        <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-5 flex flex-col">
            <h3 class="text-sm font-bold text-primary mb-1">Export Riwayat</h3>
            <p class="text-xs text-gray-500 mb-5">Export data riwayat transaksi dalam format file</p>

            <a href="{{ route('riwayat.export.excel', ['from' => request('from'), 'to' => request('to')]) }}"
                class="flex items-center justify-center gap-2.5 w-full py-3 mb-3 bg-[#4CAF50] text-white text-sm font-bold rounded-xl hover:bg-[#43A047] transition-colors shadow-sm">
                <i class="fa-solid fa-file-excel text-base"></i> Export Excel
            </a>

            <a href="{{ route('riwayat.export.pdf', ['from' => request('from'), 'to' => request('to')]) }}"
                class="flex items-center justify-center gap-2.5 w-full py-3 bg-[#F44336] text-white text-sm font-bold rounded-xl hover:bg-[#E53935] transition-colors shadow-sm">
                <i class="fa-solid fa-file-pdf text-base"></i> Export PDF
            </a>
        </div>

    </div>

</div>

{{-- Modal Detail Transaksi --}}
@foreach($orders as $order)
<div x-data="{ open: false }"
     x-show="open" x-cloak
     @open-modal.window="if ($event.detail === 'detail-{{ $order->id }}') open = true"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40"></div>
    <div x-show="open" @click.outside="open = false"
         class="relative bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-xl w-full max-w-lg p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-primary">Detail Transaksi</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $order->invoice_no }}</p>
            </div>
            <button @click="open = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="bg-white rounded-xl border border-[#8C8A75]/40 p-4 mb-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-medium text-primary">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Status</span>
                <span class="font-semibold {{ $order->status === 'paid' ? 'text-green-700' : ($order->status === 'pending' ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Metode Bayar</span>
                <span class="font-medium text-primary">{{ ucfirst(optional($order->payment)->method ?? '-') }}</span>
            </div>
            @if(optional($order->payment)->change_amount > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Kembalian</span>
                <span class="font-medium text-primary">Rp {{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-[#8C8A75]/40 p-4 mb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Item Pembelian</p>
            <div class="space-y-2">
                @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700">{{ optional($item->product)->name ?? 'Produk dihapus' }}
                        <span class="text-gray-400">x{{ $item->quantity }}</span>
                    </span>
                    <span class="font-medium text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="border-t border-[#8C8A75]/30 mt-3 pt-3 flex justify-between">
                <span class="text-sm font-bold text-primary">Total</span>
                <span class="text-sm font-bold text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
        <button @click="open = false"
            class="w-full py-2.5 bg-sidebar text-white text-sm font-semibold rounded-xl hover:bg-sidebar-hover transition-colors">
            Tutup
        </button>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    window.addEventListener('live-search', (e) => {
        document.querySelector('#filterForm input[name="search"]').value = e.detail || '';
    });
    window.addEventListener('search-submit', (e) => {
        document.querySelector('#filterForm input[name="search"]').value = e.detail || '';
        document.getElementById('filterForm').submit();
    });

    // Pie Chart Metode Pembayaran
    new Chart(document.getElementById('chartMetode'), {
        type: 'doughnut',
        data: {
        labels: ['Tunai', 'E-Wallet', 'QRIS', 'Utang'],
        datasets: [{
            data: [
                {{ $ringkasanMetode['cash'] ?? 0 }},
                {{ $ringkasanMetode['ewallet'] ?? 0 }},
                {{ $ringkasanMetode['qris'] ?? 0 }},
                {{ $ringkasanMetode['debt'] ?? 0 }},
            ],
            backgroundColor: ['#BFDCDE', '#DBC5E8', '#A8D5A2', '#FDE68A'],
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '65%',
            plugins: { legend: { display: false } },
        }
    });

    // Pie Chart Status Transaksi
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Pending', 'Dibatalkan'],
            datasets: [{
                data: [
                    {{ $ringkasanStatus['paid'] ?? 0 }},
                    {{ $ringkasanStatus['pending'] ?? 0 }},
                    {{ $ringkasanStatus['debt'] ?? 0 }},
                    {{ $ringkasanStatus['cancelled'] ?? 0 }},
                ],
                backgroundColor: ['#C1F2D0', '#FEF9C3', '#FDE68A', '#F7CDCD'],
                borderWidth: 0,
            }]
        },
        options: {
            cutout: '65%',
            plugins: { legend: { display: false } },
        }
    });
</script>
@endpush

@endsection