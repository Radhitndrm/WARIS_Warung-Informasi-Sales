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

- **Autentikasi** — Login/register kasir dengan Laravel Fortify (2FA support, forgot/reset password via email)
- **Manajemen Produk & Kategori** — CRUD produk dan kategori lengkap dengan stok & gambar
- **Halaman POS** — Keranjang belanja real-time, kalkulasi total otomatis, cari produk via teks/suara
- **Pembayaran** — Cash (hitung kembalian) dan **QRIS via Midtrans Snap** (popup pembayaran otomatis + webhook)
- **Dashboard** — Ringkasan penjualan harian, grafik 7 hari (Chart.js), notifikasi stok rendah
- **Riwayat** — Daftar transaksi dengan filter (tanggal, metode, status), grafik ringkasan, export PDF & Excel, detail transaksi
- **Live Search** — Pencarian real-time client-side di halaman Produk & Kategori, header search terintegrasi dengan filter Riwayat
- **Chatbot AI (Ollama)** — Tanya stok/harga, rekomendasi produk, analisis penjualan, prediksi stok, panduan penggunaan
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
| Payment | Midtrans Snap (QRIS real-time via Snap popup + webhook) |
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
│       │   ├── KasirController.php              # POS + checkout (cash+midtrans snap)
│       │   ├── PaymentNotificationController.php # Midtrans webhook handler
│       │   ├── CategoryController.php
│       │   ├── ProductController.php
│       │   ├── ChatbotController.php
│       │   └── SttController.php                # Speech-to-text
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
│       ├── MidtransService.php       # Midtrans Snap API + cek status
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
│       ├── chatbot/                  # Full chatbot page
│       └── reports/                  # Riwayat transaksi (index, export PDF)
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

### 📋 Prasyarat

| Tools | Versi | Keterangan |
|-------|-------|------------|
| PHP | >= 8.3 | Ekstensi: `pdo`, `pdo_mysql` (atau `pdo_sqlite`), `mbstring`, `gd`, `fileinfo`, `bcmath` |
| Composer | >= 2.x | [getcomposer.org](https://getcomposer.org) |
| Node.js & NPM | >= 20.x | [nodejs.org](https://nodejs.org) |
| MySQL | >= 8.0 | Atau gunakan SQLite untuk development lokal |
| Git | >= 2.x | [git-scm.com](https://git-scm.com) |

> **Catatan**: Untuk fitur pembayaran QRIS, kamu perlu akun [Midtrans](https://midtrans.com) (Dashboard → Settings → Access Keys). Untuk AI Chatbot & Voice, lihat bagian [Setup AI Lokal](#setup-ai-lokal-wajib-untuk-chatbot--voice).

---

### 🚀 Langkah Instalasi (Step by Step)

#### 1. Clone Repository

```bash
git clone https://github.com/username/pos-warung.git
cd pos-warung
```

#### 2. Install Dependensi Backend (PHP)

```bash
composer install
```

Jika terjadi error terkait ekstensi PHP, pastikan semua ekstensi yang dibutuhkan sudah aktif. Cek dengan:

```bash
php -m | grep -E "pdo|mbstring|gd|fileinfo|bcmath"
```

#### 3. Install Dependensi Frontend (Node.js)

```bash
npm install
```

#### 4. Setup Environment Variables

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Buka `.env` dengan text editor dan sesuaikan konfigurasi berikut:

**Database (pilih salah satu — SQLite atau MySQL):**

- **SQLite** (rekomendasi untuk development, tanpa setup tambahan):
  ```env
  DB_CONNECTION=sqlite
  # DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD tidak perlu diisi
  ```
  SQLite akan otomatis membuat file `database/database.sqlite`. Tidak perlu MySQL server.

- **MySQL**:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=pos_warung
  DB_USERNAME=root
  DB_PASSWORD=
  ```
  Pastikan database `pos_warung` sudah dibuat di MySQL:
  ```sql
  CREATE DATABASE pos_warung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

**Queue & Session (wajib database):**

```env
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

**Mailer untuk Forgot Password** (wajib agar fitur reset password via email berfungsi):

Gunakan konfigurasi SMTP Gmail atau Mailtrap untuk development:

- **Gmail SMTP** (gunakan App Password):
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your-email@gmail.com
  MAIL_PASSWORD=your-app-password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS="your-email@gmail.com"
  MAIL_FROM_NAME="${APP_NAME}"
  ```
  > Untuk App Password: Gmail → Settings → Security → 2-Step Verification → App passwords.

- **Mailtrap** (untuk testing lokal):
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=sandbox.smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=your-mailtrap-username
  MAIL_PASSWORD=your-mailtrap-password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS="noreply@poswarung.test"
  MAIL_FROM_NAME="${APP_NAME}"
  ```

**Midtrans** (wajib untuk pembayaran QRIS):

```env
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

> Dapatkan key dari [Midtrans Dashboard](https://dashboard.midtrans.com) → Settings → Access Keys.

**Ollama & Whisper** (opsional, untuk AI & Voice):

```env
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=nous-hermes:7b
# WHISPER_BINARY=/opt/homebrew/bin/whisper-cli (hanya untuk macOS)
# WHISPER_MODEL=/path/to/ggml-tiny.bin
```

#### 5. Jalankan Migrasi & Seeder

```bash
php artisan migrate --seed
```

Seeder akan membuat:
- 1 akun kasir default (lihat [Akun Default](#-akun-default-seeder))
- 5 kategori produk
- 20 produk
- 30 transaksi (7 hari terakhir)
- 10 contoh chat history

#### 6. Jalankan Aplikasi

```bash
composer run dev
```

> `composer run dev` menjalankan 4 proses sekaligus via [concurrently](https://github.com/open-cli-tools/concurrently):
> - `php artisan serve` — HTTP server di port 8000
> - `php artisan queue:listen` — Queue worker untuk Midtrans webhook
> - `php artisan pail` — Log viewer (Laravel Pail)
> - `npm run dev` — Vite dev server untuk TailwindCSS & Alpine.js

Akses aplikasi di: **`http://localhost:8000`**

---

### 🔧 Setup Alternatif (Manual)

Jika `composer run dev` tidak berjalan, jalankan proses berikut di terminal terpisah:

| Terminal | Command | Fungsi |
|----------|---------|--------|
| Terminal 1 | `php artisan serve` | HTTP server |
| Terminal 2 | `php artisan queue:listen` | Queue worker |
| Terminal 3 | `npm run dev` | Vite (CSS & JS Hot Reload) |

---

### ⚠️ Troubleshooting Instalasi

| Masalah | Solusi |
|---------|--------|
| `SQLSTATE[HY000]` Connection refused | Pastikan MySQL berjalan dan kredensial di `.env` benar |
| `SQLSTATE[HY000]` no such table | Jalankan `php artisan migrate --seed` |
| `Class "..." not found` | Jalankan `composer dump-autoload` |
| `Vite manifest not found` | Jalankan `npm run dev` terlebih dahulu |
| Midtrans popup tidak muncul | Cek `MIDTRANS_CLIENT_KEY` di `.env` dan network tab browser |
| Email reset password tidak terkirim | Cek konfigurasi `MAIL_*` di `.env`. Gunakan Mailtrap untuk testing |
| `openssl` / `key:generate` error | Pastikan ekstensi PHP `openssl` aktif |

---

## 🔑 Environment Variables Reference

Seluruh variabel environment yang digunakan ada di file `.env.example`. Berikut ringkasan grup konfigurasinya:

| Grup | Variabel Kunci | Wajib? | Keterangan |
|------|---------------|--------|------------|
| **App** | `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL` | ✅ | Konfigurasi dasar aplikasi |
| **Database** | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | ✅ | Gunakan `sqlite` untuk dev, `mysql` untuk production |
| **Queue & Session** | `QUEUE_CONNECTION`, `CACHE_STORE`, `SESSION_DRIVER` | ✅ | Harus `database` agar Midtrans webhook & session berfungsi |
| **Mail** | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | ✅ | Diperlukan untuk **forgot/reset password** via email |
| **Midtrans** | `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION` | ⚠️ | Wajib jika menggunakan pembayaran QRIS |
| **Ollama** | `OLLAMA_BASE_URL`, `OLLAMA_MODEL` | ⚠️ | Wajib untuk chatbot AI |
| **Whisper** | `WHISPER_BINARY`, `WHISPER_MODEL` | ❌ | Opsional, untuk voice input (fallback dari browser STT) |

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
        │     └── QRIS → popup Midtrans Snap → bayar via QRIS/credit card → konfirmasi otomatis
        ├── Riwayat → daftar transaksi + filter + export PDF/Excel
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
- Pembayaran Cash + QRIS via Midtrans Snap (popup real-time + webhook)
- Dashboard: ringkasan, grafik Chart.js, stok rendah, widget chatbot
- Chatbot AI: teks, voice, analisis penjualan, rekomendasi, prediksi stok
- Speech-to-Text via Whisper (browser API + fallback)
- DbContextService (konteks cerdas untuk AI: 10+ jenis analisis)
- Layout lengkap (sidebar, header, komponen reusable)
- Riwayat: filter (tanggal, metode, status, pencarian), grafik ringkasan, export PDF & Excel, detail transaksi
- Live search client-side di Produk & Kategori via header search bar
- Header search terintegrasi dengan form filter halaman Riwayat
- Lisensi MIT

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

MIT License — lihat file [LICENSE](LICENSE) untuk detail lengkap.

Project ini dibuat untuk keperluan tugas mata kuliah **Pemrograman Web Lanjut**.
