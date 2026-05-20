@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6 text-gray-800 relative w-full p-4">
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-[#F4F2DE] p-4 rounded-2xl border border-[#8C8A75] shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Penjualan Hari ini</p>
                <h3 class="text-xl font-bold tracking-tight">Rp 2.300.000</h3>
                <p class="text-xs text-green-600 font-bold flex items-center"><i class="fa-solid fa-caret-up mr-1"></i> 9.6%</p>
            </div>
        </div>

        <div class="bg-[#F4F2DE] p-4 rounded-2xl border border-[#8C8A75] shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-emerald-400 text-white flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Jumlah Transaksi</p>
                <h3 class="text-xl font-bold tracking-tight">67 <span class="text-sm font-normal text-gray-500">Transaksi</span></h3>
                <p class="text-xs text-green-600 font-bold flex items-center"><i class="fa-solid fa-caret-up mr-1"></i> 9.6%</p>
            </div>
        </div>

        <div class="bg-[#F4F2DE] p-4 rounded-2xl border border-[#8C8A75] shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-400 text-white flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Produk Aktif</p>
                <h3 class="text-xl font-bold tracking-tight">167 <span class="text-sm font-normal text-gray-500">Item</span></h3>
                <p class="text-xs text-green-600 font-bold flex items-center"><i class="fa-solid fa-caret-up mr-1"></i> 9.6%</p>
            </div>
        </div>

        <div class="bg-[#F4F2DE] p-4 rounded-2xl border border-[#8C8A75] shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Stok Menipis</p>
                <h3 class="text-xl font-bold tracking-tight text-amber-700">9 <span class="text-sm font-normal text-gray-500">Produk</span></h3>
                <p class="text-xs text-red-600 font-bold tracking-wide">Segera Restock!</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-[#F4F2DE] p-5 rounded-2xl border border-[#8C8A75] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Grafik Penjualan</h3>
                <div class="inline-flex rounded-lg border border-[#8C8A75]/60 bg-[#E6E4CE]/50 p-0.5 text-xs font-medium">
                    <button class="bg-[#C8C6B2] text-gray-800 px-3 py-1 rounded-md font-bold">Harian</button>
                    <button class="text-gray-600 px-3 py-1">Mingguan</button>
                    <button class="text-gray-600 px-3 py-1">Bulanan</button>
                </div>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="salesChart"></canvas>
            </div>
            <div class="grid grid-cols-3 border-t border-[#8C8A75]/40 mt-4 pt-3 text-center text-xs">
                <div>
                    <p class="text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block mr-1.5"></span>Total Penjualan</p>
                    <p class="font-bold text-sm mt-0.5">Rp 2.300.000</p>
                </div>
                <div>
                    <p class="text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block mr-1.5"></span>Jumlah Transaksi</p>
                    <p class="font-bold text-sm mt-0.5">67</p>
                </div>
                <div>
                    <p class="text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-purple-400 inline-block mr-1.5"></span>Produk Aktif</p>
                    <p class="font-bold text-sm mt-0.5">167</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[#F4F2DE] p-5 rounded-2xl border border-[#8C8A75] shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-bold">Produk Stok Menipis</h3>
                    <a href="#" class="text-xs text-gray-500 underline">Lihat Semua</a>
                </div>
                <table class="w-full text-xs text-left">
                    <thead class="text-gray-500 border-b border-[#8C8A75]/30">
                        <tr>
                            <th class="pb-2 font-medium">Produk</th>
                            <th class="pb-2 text-center font-medium">Stok</th>
                            <th class="pb-2 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#8C8A75]/20">
                        <tr>
                            <td class="py-2.5 font-medium text-gray-700">Minyak Goreng 1L</td>
                            <td class="py-2.5 text-center text-red-600 font-bold">5</td>
                            <td class="py-2.5 text-center"><button class="bg-[#F0A985] text-[#3a1d0b] px-2 py-0.5 rounded text-[10px] font-bold">Restock</button></td>
                        </tr>
                        <tr class="text-gray-400">
                            <td class="py-2.5">Produk</td><td class="py-2.5 text-center">3</td>
                            <td class="py-2.5 text-center"><button class="bg-[#F0A985]/60 px-2 py-0.5 rounded text-[10px] font-bold">Restock</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-[#F4F2DE] p-5 rounded-2xl border border-[#8C8A75] shadow-sm">
                <h3 class="text-md font-bold mb-4">Shortcut Cepat</h3>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-bold">
                    <div class="bg-[#BFDCDE] border border-[#8C8A75] p-3 rounded-xl flex flex-col items-center justify-center space-y-2">
                        <i class="fa-regular fa-folder-open text-2xl text-slate-700"></i><span class="text-[11px]">Kategori Produk</span>
                    </div>
                    <div class="bg-[#DBC5E8] border border-[#8C8A75] p-3 rounded-xl flex flex-col items-center justify-center space-y-2">
                        <i class="fa-solid fa-square-plus text-2xl text-slate-700"></i><span class="text-[11px]">Tambah Produk</span>
                    </div>
                    <div class="bg-[#C1F2D0] border border-[#8C8A75] p-3 rounded-xl flex flex-col items-center justify-center space-y-2">
                        <i class="fa-solid fa-cart-arrow-down text-2xl text-slate-700"></i><span class="text-[11px]">Buka Kasir</span>
                    </div>
                    <div class="bg-[#F7CDCD] border border-[#8C8A75] p-3 rounded-xl flex flex-col items-center justify-center space-y-2">
                        <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-700"></i><span class="text-[11px]">Riwayat Transaksi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-4 right-6 w-80 bg-[#F4F2DE] border border-[#8C8A75] rounded-2xl shadow-xl overflow-hidden flex flex-col z-50">
        <div class="bg-slate-100 px-4 py-3 border-b border-[#8C8A75]/40 flex items-center justify-between">
            <div class="flex items-center space-x-2"><i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i><span class="font-bold text-sm">ChatBox AI</span></div>
            <div class="w-5 h-0.5 bg-gray-600 rounded"></div>
        </div>
        <div class="p-4 space-y-3 max-h-64 overflow-y-auto text-xs">
            <p class="font-bold">Hai, ghalib 👋!</p>
            <p class="text-gray-600">Ada yang bisa saya bantu hari ini?</p>
            <div class="space-y-1.5 pt-2">
                <button class="w-full text-left bg-white border border-[#8C8A75]/50 px-3 py-1.5 rounded-full hover:bg-slate-50 flex items-center"><i class="fa-solid fa-chart-simple text-gray-400 mr-2"></i> Ringkas penjualan hari ini</button>
                <button class="w-full text-left bg-white border border-[#8C8A75]/50 px-3 py-1.5 rounded-full hover:bg-slate-50 flex items-center"><i class="fa-solid fa-fire text-amber-500 mr-2"></i> Produk paling laris minggu ini</button>
            </div>
        </div>
        <div class="p-3 bg-[#E6E4CE]/40 border-t border-[#8C8A75]/30 flex items-center space-x-2">
            <input type="text" placeholder="Ketik pertanyaan..." class="flex-1 px-3 py-1.5 rounded-full bg-white border border-[#8C8A75]/60 text-xs focus:outline-none">
            <button class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center"><i class="fa-solid fa-paper-plane text-xs"></i></button>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    data: [0.2, 0.8, 1.4, 2.3, 1.7, 0.9, 1.6],
                    borderColor: '#7FA1C3',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }, 200);
</script>
@endsection