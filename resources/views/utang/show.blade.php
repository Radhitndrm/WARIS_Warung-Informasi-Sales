@extends('layouts.app')

@section('title', 'Detail Utang')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://{{ config('services.midtrans.is_production') ? 'app' : 'app.sandbox' }}.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

@section('content')

<div x-data="debtDetail()" class="space-y-6 max-w-4xl">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('utang') }}" class="text-sm text-muted hover:text-primary transition-colors flex items-center gap-1 mb-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar utang
            </a>
            <h1 class="text-2xl font-bold text-primary">Detail Utang</h1>
            <p class="text-sm text-muted mt-1">{{ $debt->order->invoice_no }}</p>
        </div>
        <div>
            @if($debt->status === 'paid')
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-sm font-semibold">
                <i class="fa-solid fa-circle-check"></i> Lunas
            </span>
            @elseif($debt->due_date && $debt->due_date->isPast())
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-red-100 text-red-800 text-sm font-semibold">
                <i class="fa-solid fa-circle-exclamation"></i> Jatuh Tempo
            </span>
            @else
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold">
                <i class="fa-solid fa-clock"></i> Aktif
            </span>
            @endif
        </div>
    </div>

    {{-- Customer & Debt Info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Pelanggan</p>
            <h3 class="text-lg font-bold text-primary">{{ $debt->customer_name }}</h3>
            <p class="text-sm text-muted"><i class="fa-solid fa-phone mr-1.5"></i>{{ $debt->customer_phone }}</p>
            @if($debt->due_date)
            <p class="text-sm mt-1 {{ $debt->due_date->isPast() ? 'text-red-600 font-semibold' : 'text-muted' }}">
                <i class="fa-solid fa-calendar mr-1.5"></i>
                Jatuh tempo: {{ $debt->due_date->format('d/m/Y') }}
                @if($debt->due_date->isPast() && $debt->status === 'active')
                <span class="text-red-600">(Terlambat!)</span>
                @endif
            </p>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Total Utang</p>
            <h3 class="text-lg font-bold text-amber-700">Rp {{ number_format($debt->total_amount, 0, ',', '.') }}</h3>
            <p class="text-xs text-muted mt-1">Dibayar: Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-muted mb-1">Sisa Utang</p>
            <h3 class="text-lg font-bold {{ $debt->remaining_amount > 0 ? 'text-red-600' : 'text-green-700' }}">
                Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
            </h3>
            @if($debt->remaining_amount > 0)
            @php
                $percent = round(($debt->paid_amount / $debt->total_amount) * 100);
            @endphp
            <div class="mt-2 w-full bg-gray-100 rounded-full h-2">
                <div class="bg-amber-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
            </div>
            <p class="text-xs text-muted mt-1">{{ $percent }}% sudah dibayar</p>
            @endif
        </div>
    </div>

    {{-- Pay Form --}}
    @if($debt->status === 'active')
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-primary mb-4">Bayar Utang</h3>

        <div class="flex items-center gap-4 mb-4">
            <button @click="payMethod = 'cash'"
                :class="payMethod === 'cash' ? 'bg-sidebar text-white border-sidebar' : 'bg-white text-gray-600 border-[#C8C4A0]'"
                class="px-5 py-2 rounded-xl text-sm font-semibold border transition-colors">
                <i class="fa-solid fa-money-bill-wave mr-1.5"></i>Tunai
            </button>
            <button @click="payMethod = 'qris'"
                :class="payMethod === 'qris' ? 'bg-sidebar text-white border-sidebar' : 'bg-white text-gray-600 border-[#C8C4A0]'"
                class="px-5 py-2 rounded-xl text-sm font-semibold border transition-colors">
                <i class="fa-solid fa-qrcode mr-1.5"></i>QRIS
            </button>
        </div>

        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="text-xs font-medium text-muted block mb-1">Jumlah Bayar</label>
                <input type="number" x-model="payAmount"
                    class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar placeholder:text-gray-400"
                    placeholder="Jumlah bayar...">
            </div>
            <div class="flex-1">
                <label class="text-xs font-medium text-muted block mb-1">Catatan (opsional)</label>
                <input type="text" x-model="payNotes"
                    class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar placeholder:text-gray-400"
                    placeholder="Catatan...">
            </div>
            <button @click="submitPayment"
                :disabled="payLoading"
                class="px-6 py-2.5 bg-sidebar text-white rounded-xl text-sm font-bold hover:bg-sidebar-hover transition-colors disabled:opacity-50 flex items-center gap-2 shrink-0">
                <i x-show="!payLoading" class="fa-solid fa-check"></i>
                <i x-show="payLoading" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="payLoading ? 'Memproses...' : 'Bayar'"></span>
            </button>
        </div>

        {{-- Quick amounts --}}
        <div class="flex items-center gap-2 mt-3">
            <span class="text-xs text-muted">Cepat:</span>
            <button @click="payAmount = {{ $debt->remaining_amount }}" class="text-xs px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold hover:bg-amber-200 transition-colors">
                Lunas (Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }})
            </button>
            <button @click="payAmount = Math.ceil({{ $debt->remaining_amount }} / 2)" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors">
                Setengah
            </button>
            <button @click="payAmount = Math.ceil({{ $debt->remaining_amount }} / 4)" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors">
                Seperempat
            </button>
        </div>
    </div>
    @endif

    {{-- Products in Order --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-primary mb-3">Produk Dibeli</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2 font-medium">Produk</th>
                        <th class="px-4 py-2 font-medium text-center">Qty</th>
                        <th class="px-4 py-2 font-medium text-right">Harga</th>
                        <th class="px-4 py-2 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($debt->order->items as $item)
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-gray-800">{{ $item->product->name }}</td>
                        <td class="px-4 py-2.5 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/50">
                    <tr>
                        <td colspan="3" class="px-4 py-2.5 font-bold text-gray-800 text-right">Total</td>
                        <td class="px-4 py-2.5 font-bold text-gray-800 text-right">Rp {{ number_format($debt->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-primary mb-3">Riwayat Pembayaran</h3>
        @if($debt->payments->isEmpty())
        <p class="text-sm text-muted text-center py-8">Belum ada pembayaran untuk utang ini.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-muted bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2 font-medium">Tanggal</th>
                        <th class="px-4 py-2 font-medium">Metode</th>
                        <th class="px-4 py-2 font-medium text-right">Jumlah</th>
                        <th class="px-4 py-2 font-medium">Status</th>
                        <th class="px-4 py-2 font-medium">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($debt->payments as $payment)
                    <tr>
                        <td class="px-4 py-2.5 text-gray-800">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $payment->method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                <i class="fa-solid {{ $payment->method === 'cash' ? 'fa-money-bill-wave' : 'fa-qrcode' }}"></i>
                                {{ $payment->method === 'cash' ? 'Tunai' : 'QRIS' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold text-green-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5">
                            @if($payment->status === 'success')
                            <span class="text-green-700 text-xs font-semibold"><i class="fa-solid fa-circle-check mr-1"></i>Berhasil</span>
                            @elseif($payment->status === 'pending')
                            <span class="text-yellow-700 text-xs font-semibold"><i class="fa-solid fa-clock mr-1"></i>Menunggu</span>
                            @else
                            <span class="text-red-600 text-xs font-semibold"><i class="fa-solid fa-circle-xmark mr-1"></i>Gagal</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-muted text-xs">{{ $payment->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
function debtDetail() {
    return {
        payMethod: 'cash',
        payAmount: 0,
        payNotes: '',
        payLoading: false,
        processingPayment: false,
        snapToken: null,

        async submitPayment() {
            const amount = parseInt(this.payAmount);
            if (!amount || amount <= 0) {
                alert('Masukkan jumlah bayar yang valid');
                return;
            }
            if (amount > {{ $debt->remaining_amount }}) {
                alert('Jumlah bayar melebihi sisa utang (Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }})');
                return;
            }

            this.payLoading = true;
            const self = this;

            try {
                const res = await fetch('{{ route("utang.bayar", $debt) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        amount: amount,
                        method: this.payMethod,
                        notes: this.payNotes || null,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    if (data.snap_token) {
                        this.snapToken = data.snap_token;
                        this.payLoading = false;
                        this.processingPayment = true;
                        this.openSnap(data.debt_payment_id);
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'Gagal memproses pembayaran');
                }
            } catch (e) {
                alert('Terjadi kesalahan koneksi');
            } finally {
                if (!this.snapToken) {
                    this.payLoading = false;
                }
            }
        },

        openSnap(debtPaymentId) {
            const snapToken = this.snapToken;
            const self = this;
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    self.verifyPayment(debtPaymentId);
                },
                onPending: function(result) {
                    alert('Pembayaran masih diproses. Silakan cek status nanti.');
                    self.processingPayment = false;
                    self.snapToken = null;
                    window.location.reload();
                },
                onError: function(result) {
                    self.cancelPendingPayment(debtPaymentId);
                    alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan'));
                    self.processingPayment = false;
                    self.snapToken = null;
                },
                onClose: function() {
                    if (self.processingPayment) {
                        self.cancelPendingPayment(debtPaymentId);
                        self.processingPayment = false;
                        self.snapToken = null;
                        self.payLoading = false;
                    }
                },
            });
        },

        async cancelPendingPayment(debtPaymentId) {
            try {
                await fetch('{{ route("utang.payment-cancel", $debt) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ debt_payment_id: debtPaymentId }),
                });
            } catch (e) {}
        },

        async verifyPayment(debtPaymentId) {
            try {
                const res = await fetch('{{ route("utang.payment-callback", $debt) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ debt_payment_id: debtPaymentId }),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Pembayaran belum dikonfirmasi');
                    window.location.reload();
                }
            } catch (e) {
                alert('Gagal memverifikasi pembayaran');
                window.location.reload();
            } finally {
                this.processingPayment = false;
                this.snapToken = null;
            }
        },
    };
}
</script>
@endpush
