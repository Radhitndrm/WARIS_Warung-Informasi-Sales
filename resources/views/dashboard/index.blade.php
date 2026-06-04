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

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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

{{-- Chatbot Widget --}}
<div x-data="{ open: false }">
    <div x-show="open" x-cloak
        x-transition:enter="transition-all duration-300 ease-out"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all duration-200 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-20 right-6 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden flex flex-col"
        style="max-height: 520px; z-index: 60;">
        <div class="bg-primary px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
                <span class="text-sm font-semibold text-white">ChatBox AI</span>
            </div>
            <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="widget-messages" class="flex-1 overflow-y-auto p-4 space-y-3 text-xs" style="min-height: 280px; max-height: 360px;">
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
                <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2 max-w-[85%]">
                    <p class="text-gray-700 leading-relaxed">Hai, {{ Auth::user()->name }}! Ada yang bisa saya bantu?</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5 px-8">
                <button class="quick-widget bg-white border border-gray-200 rounded-full px-2.5 py-1 text-[11px] text-muted hover:border-primary hover:text-primary transition-colors">
                    Ringkas penjualan hari ini
                </button>
                <button class="quick-widget bg-white border border-gray-200 rounded-full px-2.5 py-1 text-[11px] text-muted hover:border-primary hover:text-primary transition-colors">
                    Produk paling laris minggu ini
                </button>
            </div>
        </div>

        <div class="border-t border-gray-100 p-2 flex items-center gap-2 bg-white">
            <button class="widget-mic w-8 h-8 rounded-full flex items-center justify-center text-muted hover:text-primary hover:bg-gray-50 transition-colors shrink-0" title="Speech-to-text (online: Chrome STT, offline: Whisper lokal)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                </svg>
            </button>
            <input class="widget-input flex-1 bg-transparent text-xs text-primary placeholder-muted outline-none px-1" type="text" placeholder="Ketik pertanyaan...">
            <button class="widget-send w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white hover:bg-primary/90 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>
    </div>

    <button @click="open = !open"
        class="fixed bottom-4 right-6 z-50 w-12 h-12 rounded-full bg-primary text-white shadow-lg hover:bg-primary/90 transition-all duration-200 flex items-center justify-center"
        :class="{ 'rotate-45': open }">
        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
        </svg>
        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
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

(function() {
    const widget = document.querySelector('#widget-messages');
    if (!widget) return;

    const input = document.querySelector('.widget-input');
    const sendBtn = document.querySelector('.widget-send');
    const micBtn = document.querySelector('.widget-mic');
    const quickBtns = document.querySelectorAll('.quick-widget');

    let isListening = false;
    let recognition = null;
    let mediaRecorder = null;
    let audioChunks = [];

    function startWhisperSTT() {
        widgetAddMessage('assistant', '🎤 Dengarkan... bicara sekarang.');
        audioChunks = [];

        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                mediaRecorder.start();

                mediaRecorder.ondataavailable = (e) => {
                    audioChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    stream.getTracks().forEach(t => t.stop());

                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'recording.webm');
                    formData.append('_token', '{{ csrf_token() }}');

                    micBtn.classList.remove('text-red-500', 'bg-red-50');
                    micBtn.classList.add('text-muted');
                    isListening = false;

                    fetch('{{ route("stt.transcribe") }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.text) {
                            input.value = data.text;
                            sendWidgetMessage();
                        } else {
                            widgetAddMessage('assistant', 'Maaf, gagal mengenali suara.');
                        }
                    })
                    .catch(() => {
                        widgetAddMessage('assistant', 'Maaf, terjadi kesalahan saat memproses suara.');
                    });
                };

                setTimeout(() => {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, 5000);
            })
            .catch(() => {
                micBtn.classList.remove('text-red-500', 'bg-red-50');
                micBtn.classList.add('text-muted');
                isListening = false;
                widgetAddMessage('assistant', 'Maaf, mic tidak dapat diakses. Periksa izin browser.');
            });
    }

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onresult = (e) => {
            let finalTranscript = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const transcript = e.results[i][0].transcript;
                if (e.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    input.value = transcript;
                }
            }
            if (finalTranscript) {
                input.value = finalTranscript;
                micBtn.classList.remove('text-red-500', 'bg-red-50');
                micBtn.classList.add('text-muted');
                isListening = false;
                sendWidgetMessage();
            }
        };

        recognition.onerror = (e) => {
            micBtn.classList.remove('text-red-500', 'bg-red-50');
            micBtn.classList.add('text-muted');
            isListening = false;
            if (e.error === 'network' || e.error === 'not-allowed') {
                startWhisperSTT();
            }
        };

        recognition.onend = () => {
            micBtn.classList.remove('text-red-500', 'bg-red-50');
            micBtn.classList.add('text-muted');
            isListening = false;
        };
    }

    micBtn.addEventListener('click', () => {
        if (isListening) {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            } else if (recognition) {
                recognition.stop();
            }
            return;
        }

        isListening = true;
        micBtn.classList.add('text-red-500', 'bg-red-50');
        micBtn.classList.remove('text-muted');

        if (recognition) {
            try { recognition.start(); } catch (e) {
                startWhisperSTT();
            }
        } else {
            startWhisperSTT();
        }
    });

    function speakText(text) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 1.0;
        utter.pitch = 1.0;
        window.speechSynthesis.speak(utter);
    }

    function widgetScroll() {
        widget.scrollTop = widget.scrollHeight;
    }

    function widgetAddMessage(role, content) {
        const div = document.createElement('div');
        if (role === 'user') {
            div.className = 'flex items-start gap-2 justify-end';
            div.innerHTML = `
                <div class="bg-primary text-white rounded-2xl rounded-tr-sm px-3 py-2 max-w-[85%]">
                    <p class="leading-relaxed">${escapeHtml(content)}</p>
                </div>
                <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-primary text-[10px] font-semibold shrink-0">U</div>
            `;
        } else {
            div.className = 'flex items-start gap-2';
            div.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
                <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2 max-w-[85%]">
                    <p class="text-gray-700 leading-relaxed">${escapeHtml(content)}</p>
                    <button class="tts-btn mt-1 text-xs text-muted hover:text-primary transition-colors flex items-center gap-1" data-text="${escapeHtml(content)}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg>
                        Dengarkan
                    </button>
                </div>
            `;
            div.querySelector('.tts-btn')?.addEventListener('click', function() {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg> Dengarkan`;
                } else {
                    speakText(this.dataset.text);
                    this.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg> Stop`;
                }
            });
        }
        widget.appendChild(div);
        widgetScroll();
    }

    function widgetAddLoading() {
        const div = document.createElement('div');
        div.id = 'widget-loading';
        div.className = 'flex items-start gap-2';
        div.innerHTML = `
            <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-[10px] font-semibold shrink-0">AI</div>
            <div class="bg-gray-50 rounded-2xl rounded-tl-sm px-3 py-2">
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-primary/40 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        `;
        widget.appendChild(div);
        widgetScroll();
    }

    function widgetRemoveLoading() {
        const el = document.getElementById('widget-loading');
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function removeQuickSuggestions() {
        const suggestions = widget.querySelector('.flex-wrap');
        if (suggestions) suggestions.remove();
        const firstMsg = widget.querySelector('.flex.items-start.gap-2:first-child');
        if (firstMsg) firstMsg.remove();
    }

    function sendWidgetMessage() {
        const message = input.value.trim();
        if (!message) return;

        widgetAddMessage('user', message);
        input.value = '';
        removeQuickSuggestions();
        widgetAddLoading();

        fetch('{{ route("chatbot.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ message }),
        })
        .then(r => r.json())
        .then(data => {
            widgetRemoveLoading();
            if (data.success) {
                widgetAddMessage('assistant', data.response);
            }
        })
        .catch(() => {
            widgetRemoveLoading();
            widgetAddMessage('assistant', 'Maaf, terjadi kesalahan koneksi. Coba lagi.');
        });
    }

    if (sendBtn) sendBtn.addEventListener('click', sendWidgetMessage);
    if (input) input.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendWidgetMessage(); });

    quickBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            input.value = btn.textContent.trim();
            sendWidgetMessage();
        });
    });
})();
</script>
@endpush
