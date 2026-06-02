# WARIS 🛒
### Warung Informasi Sales

Aplikasi Point of Sale (POS) berbasis web untuk warung dengan asisten AI lokal (Ollama) dan input suara (Whisper).

---

## 👥 Tim Pengembang

| No | Nama | Jobdesk |
|----|------|---------|
| 1 | Radhitya Andromeda Barito | PM + Chatbot AI + Pembayaran + STT |
| 2 | Aisha Hannah Heriawan | UI/UX Designer (Figma) |
| 3 | Dzilal Waliyurrahman | Auth + Laporan |
| 4 | M. Abi Rangga | Manajemen Produk & Kategori |
| 5 | Ghalib | Halaman Kasir + Dashboard |

---

## ✨ Fitur Utama

- **Autentikasi** — Login/register kasir dengan Laravel Fortify (2FA support)
- **Manajemen Produk & Kategori** — CRUD produk dan kategori lengkap dengan stok & gambar
- **Halaman POS** — Keranjang belanja real-time, kalkulasi total otomatis, cari produk via teks/suara
- **Pembayaran** — Cash (hitung kembalian) dan QRIS (offline / mock)
- **Dashboard** — Ringkasan penjualan harian, grafik 7 hari (Chart.js), notifikasi stok rendah
- **Riwayat** — Daftar transaksi (filter tanggal — WIP)
- **Chatbot AI (Ollama)** — Tanya stok/harga, rekomendasi produk, analisis penjualan, prediksi stok
- **Voice Input** — Input teks ke POS & Chatbot via browser Speech-to-Text + Whisper (fallback)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 |
| Frontend | Blade + TailwindCSS v4 + Alpine.js |
| Database | MySQL |
| Auth | Laravel Fortify |
| AI Chatbot | Ollama (Nous Hermes 7B) |
| STT | Whisper (local) |
| Payment | Midtrans (package terinstall, integrasi QRIS offline sementara) |
| Queue | Database |
| HTTP Client | Guzzle |

---

## 📁 Struktur Folder Aktual

```
pos-warung/
├── app/
│   ├── Actions/Fortify/          # Fortify actions (create, update, reset user)
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php
│   │       ├── KasirController.php     # POS + checkout (cash+qris)
│   │       ├── CategoryController.php
│   │       ├── ProductController.php
│   │       ├── ChatbotController.php
│   │       └── SttController.php       # Speech-to-text
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   └── ChatHistory.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── FortifyServiceProvider.php
│   └── Services/
│       ├── OllamaService.php         # Chatbot AI (local LLM)
│       ├── WhisperService.php        # Speech-to-text lokal
│       └── DbContextService.php      # Konteks database utk AI
├── database/
│   ├── factories/                    # UserFactory, CategoryFactory, ProductFactory, ChatHistoryFactory
│   ├── migrations/                   # 10 migration (users, cache, jobs, 2FA, categories, products, orders, order_items, payments, chat_histories)
│   └── seeders/                      # DatabaseSeeder, UserSeeder, CategorySeeder, ProductSeeder, OrderSeeder, ChatHistorySeeder
├── resources/
│   └── views/
│       ├── layouts/                  # app.blade.php + auth.blade.php
│       ├── components/               # sidebar.blade.php, sidebar-item.blade.php, header.blade.php
│       ├── auth/                     # login, register, forgot-pw, reset-pw
│       ├── dashboard/
│       ├── kasir/                    # Halaman POS
│       ├── produk/                   # index, create, edit
│       ├── kategori/                 # index, create, edit
│       └── chatbot/                  # Full chatbot page
├── routes/
│   ├── web.php
│   └── console.php
├── public/images/
│   └── sidebar-logo.png
└── tests/
    ├── Feature/ExampleTest.php
    └── Unit/ExampleTest.php
```

---

## 🗄️ Skema Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data kasir (name, email, password, 2FA) |
| `categories` | Kategori produk |
| `products` | Produk warung (harga, stok, kategori, gambar) |
| `orders` | Header transaksi (invoice, total, status) |
| `order_items` | Detail item per transaksi |
| `payments` | Data pembayaran (cash / qris, kembalian) |
| `chat_histories` | Riwayat percakapan dengan chatbot AI |

---

## ⚙️ Instalasi & Menjalankan Project

### Prasyarat

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL (atau SQLite untuk development)

### Langkah Instalasi

```bash
# 1. Clone & masuk direktori
git clone https://github.com/username/pos-warung.git
cd pos-warung

# 2. Install dependensi
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env

# 5. Migrasi & seeder
php artisan migrate --seed

# 6. Jalankan (server + queue + vite)
composer run dev
```

Akses aplikasi di: `http://localhost:8000`

> `composer run dev` menjalankan 4 proses sekaligus: `php artisan serve`, `queue:listen`, `pail` (logs), dan `npm run dev` via `concurrently`.

---

## 🔑 Environment Variables

```env
# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_warung
DB_USERNAME=root
DB_PASSWORD=

# Database (SQLite — alternatif)
# DB_CONNECTION=sqlite

# Queue & Cache (wajib database)
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

# Midtrans (opsional — pembayaran QRIS masih offline)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Ollama (chatbot AI lokal)
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=nous-hermes:7b

# Whisper (speech-to-text lokal)
WHISPER_BINARY=/opt/homebrew/bin/whisper-cli
WHISPER_MODEL=/path/to/ggml-tiny.bin
```

### Setup AI Lokal (wajib untuk chatbot & voice)

1. **Ollama** — Install dari [ollama.com](https://ollama.com), lalu jalankan:
   ```bash
   ollama pull nous-hermes:7b
   ollama serve
   ```
2. **Whisper** (opsional, untuk voice input) — Install `whisper-cpp` via Homebrew:
   ```bash
   brew install whisper-cpp
   ```

---

## 👤 Akun Default (Seeder)

| Email | Password | Role |
|-------|----------|------|
| kasir@poswarung.test | password | Kasir |

Seeder juga membuat: 5 kategori, 20 produk, 30 transaksi (7 hari terakhir), dan 10 contoh chat.

---

## 🗺️ Alur Penggunaan

```
Login
  └── Dashboard (ringkasan penjualan + grafik + stok rendah + widget chatbot)
        ├── Manajemen Produk → tambah / edit / hapus produk
        ├── Manajemen Kategori → tambah / edit / hapus kategori
        ├── Halaman POS → cari produk (teks/suara) → keranjang → bayar
        │     ├── Cash → input nominal → hitung kembalian → selesai
        │     └── QRIS → generate kode → konfirmasi manual → selesai
        ├── Riwayat → daftar transaksi (WIP)
        └── Chatbot AI → tanya stok/harga, rekomendasi, analisis penjualan, voice input
```

---

## 📦 Scripts

| Command | Description |
|---------|-------------|
| `composer run dev` | Jalankan server + queue + logs + Vite (dev) |
| `composer run setup` | Setup awal (install, migrate, build) |
| `composer run test` | Jalankan tests |
| `npm run build` | Build asset production |

---

## 📅 Status Pengerjaan

### ✅ Selesai
- Setup Laravel 13 + dependensi
- Fortify auth (login, register, forgot/reset password, 2FA)
- Migration & seeder semua tabel (6 seeder)
- Model + Eloquent relationships
- CRUD Kategori & Produk (dengan upload gambar)
- Halaman POS: keranjang real-time, search, voice input
- Pembayaran Cash + QRIS (offline/mock)
- Dashboard: ringkasan, grafik Chart.js, stok rendah, widget chatbot
- Chatbot AI: teks, voice, analisis penjualan, rekomendasi, prediksi stok
- Speech-to-Text via Whisper (browser API + fallback)
- DbContextService (konteks cerdas untuk AI: 10+ jenis analisis)
- Layout lengkap (sidebar, header, komponen reusable)

### 🚧 WIP / Belum
- Riwayat filter tanggal + export PDF (rute ada, view belum)
- Integrasi Midtrans Snap (QRIS masih offline manual)
- Implementasi penuh desain Figma

---

## 🤝 Panduan Kontribusi Tim

```bash
git pull origin main
git checkout -b fitur/nama-fitur
git add .
git commit -m "feat: deskripsi singkat perubahan"
git push origin fitur/nama-fitur
```

### Konvensi Nama Branch

| Prefix | Kegunaan |
|--------|----------|
| `fitur/` | Fitur baru |
| `fix/` | Perbaikan bug |
| `ui/` | Perubahan tampilan |

---

## 📄 Lisensi

Project ini dibuat untuk keperluan tugas mata kuliah **Pemrograman Web Lanjut**.
