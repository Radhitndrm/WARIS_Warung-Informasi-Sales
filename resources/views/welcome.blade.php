<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di WARIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Custom Warna Pastel agar Serasi dengan Dashboard WARIS */
        body { background-color: #E6E4CE; }
        .bg-card-custom { background-color: #F4F2DE; }
        .border-custom { border-color: #8C8A75; }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans text-gray-800">

    <nav class="px-8 py-4 flex items-center justify-between border-b border-[#8C8A75]/30 bg-[#F4F2DE]/60 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <div class="text-2xl text-slate-700"><i class="fa-solid fa-cart-shopping"></i></div>
            <div>
                <span class="text-xl font-black tracking-wider text-slate-700">WA<span class="text-amber-500">R</span>IS</span>
                <p class="text-[9px] text-slate-600 font-medium -mt-1">Warung Informasi Sales</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <a href="/login" class="text-sm font-bold text-slate-700 hover:text-slate-900 transition px-3 py-1.5 rounded-lg">
                Masuk
            </a>
            <a href="/register" class="bg-[#A3C193] border border-[#8C8A75]/50 text-slate-900 text-sm font-bold px-4 py-2 rounded-xl shadow-sm hover:bg-[#92b281] transition">
                Daftar Akun
            </a>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-6 max-w-4xl mx-auto -mt-12">
        
        <div class="inline-flex items-center space-x-2 bg-[#A3C193]/30 border border-[#8C8A75]/40 text-slate-800 text-xs font-bold px-3 py-1 rounded-full mb-6 shadow-sm">
            <i class="fa-solid fa-wand-magic-sparkles text-amber-600 animate-pulse"></i>
            <span>Sistem Informasi Sales & AI Pintar</span>
        </div>

        <h1 class="text-4xl md:text-6xl font-black tracking-tight text-slate-800 leading-tight mb-4">
            Kelola Warung & Penjualan <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-700 via-amber-700 to-emerald-800"> Jauh Lebih Mudah Bersama WARIS</span>
        </h1>

        <p class="text-sm md:text-base text-gray-600 max-w-2xl font-medium mb-8 leading-relaxed">
            Pantau grafik transaksi harian, kelola stok menipis secara instan, dan manfaatkan integrasi asisten pintar bawaan <span class="font-bold text-purple-700">ChatBox AI</span> untuk ringkasan laporan otomatis warungmu.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
            <a href="/login" class="w-full sm:w-auto bg-[#A3C193] border-2 border-[#8C8A75] text-slate-900 font-extrabold px-8 py-3.5 rounded-2xl shadow-md hover:bg-[#8fae7f] hover:translate-y-[-2px] transition flex items-center justify-center space-x-2 text-base">
                <span>Mulai Masuk Aplikasi</span>
                <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
            </a>
            <a href="#fitur" class="w-full sm:w-auto bg-[#F4F2DE] border border-[#8C8A75] text-slate-700 font-bold px-6 py-3.5 rounded-2xl hover:bg-white transition flex items-center justify-center space-x-2 text-sm">
                <i class="fa-solid fa-circle-info"></i>
                <span>Pelajari Fitur</span>
            </a>
        </div>

    </main>

    <section id="fitur" class="px-8 py-10 border-t border-[#8C8A75]/30 bg-[#F4F2DE]/40">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-card-custom p-5 rounded-2xl border border-custom shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl bg-[#C1F2D0] border border-custom/60 text-emerald-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Transaksi Kasir Instan</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Proses pembukuan kasir yang cepat, rapi, dan otomatis terhubung langsung ke pencatatan riwayat keuangan.</p>
                </div>
            </div>

            <div class="bg-card-custom p-5 rounded-2xl border border-custom shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl bg-[#F7CDCD] border border-custom/60 text-red-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Peringatan Stok Menipis</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Sistem pintar mendeteksi otomatis barang dagangan yang mau habis dan siap memberikan tombol restock cepat.</p>
                </div>
            </div>

            <div class="bg-card-custom p-5 rounded-2xl border border-custom shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl bg-[#DBC5E8] border border-custom/60 text-purple-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-800 mb-1">Asisten ChatBox AI</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Cukup tanya lewat chat untuk merangkum omset penjualan hari ini atau mencari produk paling laris di tokomu.</p>
                </div>
            </div>

        </div>
    </section>

    <footer class="py-4 text-center text-[11px] text-gray-500 border-t border-[#8C8A75]/20 bg-[#E6E4CE]">
        &copy; 2026 <span class="font-bold">WARIS</span> - Warung Informasi Sales. All Rights Reserved.
    </footer>

</body>
</html>