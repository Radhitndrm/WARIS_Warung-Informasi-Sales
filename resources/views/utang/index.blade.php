@extends('layouts.app')

@section('title', 'Utang')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">Manajemen Utang</h1>
            <p class="text-sm text-muted mt-1">Pantau dan kelola transaksi utang pelanggan</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Total Utang Aktif</p>
                    <h3 class="text-lg font-bold text-amber-700">Rp {{ number_format($totalUtang, 0, ',', '.') }}</h3>
                    <p class="text-xs font-medium text-muted">{{ $debts->where('status', 'active')->count() }} pelanggan</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-500/10 flex items-center justify-center text-green-600 shrink-0">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Total Sudah Lunas</p>
                    <h3 class="text-lg font-bold text-green-700">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
                    <p class="text-xs font-medium text-muted">{{ $debts->where('status', 'paid')->count() }} lunas</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted">Total Transaksi Utang</p>
                    <h3 class="text-lg font-bold text-primary">{{ $debts->count() }} <span class="text-sm font-normal text-muted">Transaksi</span></h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Debt List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-primary">Daftar Utang</h3>
        </div>

        @if($debts->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <i class="fa-solid fa-file-invoice-dollar text-5xl mb-4 text-gray-300"></i>
            <p class="font-semibold text-gray-500">Belum ada transaksi utang</p>
            <p class="text-sm mt-1">Transaksi utang dari kasir akan muncul di sini</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 font-medium">Invoice</th>
                        <th class="px-5 py-3 font-medium">Pelanggan</th>
                        <th class="px-5 py-3 font-medium">Total</th>
                        <th class="px-5 py-3 font-medium">Dibayar</th>
                        <th class="px-5 py-3 font-medium">Sisa</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($debts as $debt)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3 font-medium text-primary">{{ $debt->order->invoice_no }}</td>
                        <td class="px-5 py-3">
                            <div>
                                <span class="font-medium text-gray-800">{{ $debt->customer_name }}</span>
                                <p class="text-xs text-muted">{{ $debt->customer_phone }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-800">Rp {{ number_format($debt->total_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 font-semibold text-green-700">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 font-semibold text-amber-700">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            @if($debt->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                <i class="fa-solid fa-circle-check"></i> Lunas
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                                <i class="fa-solid fa-clock"></i> Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('utang.show', $debt) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-sidebar text-white text-xs font-semibold hover:bg-sidebar-hover transition-colors">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
