@extends('layouts.app')

@section('title', 'Invoice #' . $order->invoice_no)

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

<div class="space-y-6 max-w-3xl mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('riwayat') }}" class="text-sm text-muted hover:text-primary transition-colors flex items-center gap-1 mb-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke riwayat
            </a>
            <h1 class="text-2xl font-bold text-primary">Invoice #{{ $order->invoice_no }}</h1>
            <p class="text-sm text-muted mt-0.5">{{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <a href="{{ route('riwayat.export.pdf', ['from' => $order->created_at->format('Y-m-d'), 'to' => $order->created_at->format('Y-m-d')]) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-[#8C8A75]/50 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-print"></i> Cetak
        </a>
    </div>

    {{-- Status & Method --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Status</p>
            @if($order->status === 'paid')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold">
                <i class="fa-solid fa-circle-check"></i> Selesai
            </span>
            @elseif($order->status === 'debt')
            @php
                $debtStatus = $order->debt?->due_date && $order->debt->due_date->isPast() ? 'Jatuh Tempo' : 'Utang';
                $debtBadge = $order->debt?->due_date && $order->debt->due_date->isPast() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800';
            @endphp
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $debtBadge }} text-sm font-semibold">
                <i class="fa-solid fa-file-invoice-dollar"></i> {{ $debtStatus }}
            </span>
            @elseif($order->status === 'pending')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold">
                <i class="fa-solid fa-clock"></i> Pending
            </span>
            @else
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-800 text-sm font-semibold">
                <i class="fa-solid fa-xmark"></i> Dibatalkan
            </span>
            @endif
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Metode</p>
            @php $metode = optional($order->payment)->method ?? ($order->status === 'debt' ? 'debt' : '-'); @endphp
            @if($metode === 'cash')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                <i class="fa-solid fa-money-bill-wave"></i> Tunai
            </span>
            @elseif($metode === 'qris')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-semibold">
                <i class="fa-solid fa-qrcode"></i> QRIS
            </span>
            @elseif($metode === 'debt')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold">
                <i class="fa-solid fa-file-invoice-dollar"></i> Utang
            </span>
            @else
            <span class="text-muted text-sm">-</span>
            @endif
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Total</p>
            <h3 class="text-lg font-bold text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Kasir</p>
            <h3 class="text-sm font-bold text-primary">{{ $order->user->name }}</h3>
        </div>
    </div>

    {{-- Debt Info --}}
    @if($order->status === 'debt' && $order->debt)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <h3 class="text-sm font-bold text-amber-800 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar"></i> Informasi Utang
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-amber-600">Pelanggan</p>
                <p class="font-semibold text-amber-900">{{ $order->debt->customer_name }}</p>
                <p class="text-xs text-amber-600">{{ $order->debt->customer_phone }}</p>
            </div>
            <div>
                <p class="text-xs text-amber-600">Total Utang</p>
                <p class="font-semibold text-amber-900">Rp {{ number_format($order->debt->total_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-amber-600">Sudah Dibayar</p>
                <p class="font-semibold text-green-700">Rp {{ number_format($order->debt->paid_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs text-amber-600">Sisa</p>
                <p class="font-semibold {{ $order->debt->remaining_amount > 0 ? 'text-red-600' : 'text-green-700' }}">
                    Rp {{ number_format($order->debt->remaining_amount, 0, ',', '.') }}
                </p>
            </div>
            @if($order->debt->due_date)
            <div>
                <p class="text-xs text-amber-600">Jatuh Tempo</p>
                <p class="font-semibold {{ $order->debt->due_date->isPast() ? 'text-red-600' : 'text-amber-900' }}">
                    {{ $order->debt->due_date->format('d/m/Y') }}
                    @if($order->debt->due_date->isPast())
                    <span class="text-xs">(Terlambat!)</span>
                    @endif
                </p>
            </div>
            @endif
            <div>
                <p class="text-xs text-amber-600">Status</p>
                @php $percent = round(($order->debt->paid_amount / $order->debt->total_amount) * 100); @endphp
                <div class="w-full bg-amber-200 rounded-full h-2 mt-1">
                    <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                </div>
                <p class="text-xs text-amber-700 mt-1">{{ $percent }}% dibayar</p>
            </div>
        </div>

        {{-- Debt Payment History --}}
        @if($order->debt->payments->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-amber-200">
            <p class="text-xs font-semibold text-amber-800 mb-2">Riwayat Pembayaran Utang</p>
            <div class="space-y-1.5">
                @foreach($order->debt->payments as $payment)
                <div class="flex items-center justify-between text-sm bg-white/60 rounded-lg px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $payment->method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $payment->method === 'cash' ? 'Tunai' : 'QRIS' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-semibold text-green-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        @if($payment->status === 'success')
                        <span class="text-green-600 text-xs"><i class="fa-solid fa-circle-check"></i></span>
                        @elseif($payment->status === 'pending')
                        <span class="text-yellow-600 text-xs"><i class="fa-solid fa-clock"></i></span>
                        @else
                        <span class="text-red-500 text-xs"><i class="fa-solid fa-circle-xmark"></i></span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <a href="{{ route('utang.show', $order->debt) }}" class="inline-block mt-4 text-sm font-semibold text-amber-800 hover:text-amber-900 underline">
            Kelola utang →
        </a>
    </div>
    @endif

    {{-- Items Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-primary">Item Pembelian</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="text-xs text-muted bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Produk</th>
                    <th class="px-5 py-3 text-center font-medium">Qty</th>
                    <th class="px-5 py-3 text-right font-medium">Harga</th>
                    <th class="px-5 py-3 text-right font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $item->product->name ?? 'Produk dihapus' }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                    <td class="px-5 py-3 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50/50">
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right font-bold text-gray-800">Total</td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
                @if(optional($order->payment)->change_amount > 0)
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-sm text-gray-600">Kembalian</td>
                    <td class="px-5 py-3 text-right font-semibold text-green-700">Rp {{ number_format($order->payment->change_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>

</div>

@endsection
