# WARIS 🛒
### Warung Informasi Sales

Aplikasi Point of Sale (POS) berbasis web untuk warung, dilengkapi dengan pembayaran digital (QRIS) dan asisten AI berbasis Gemini.

---

## 👥 Tim Pengembang

| No | Nama | Jobdesk |
|----|------|---------|
| 1 | Radhitya Andromeda Barito | PM + Chatbot Gemini + Pembayaran |
| 2 | Aisha Hannah Heriawan | UI/UX Designer (Figma) |
| 3 | Dzilal Waliyurrahman | Auth + Laporan |
| 4 | M. Abi Rangga | Manajemen Produk & Kategori |
| 5 | Ghalib | Halaman Kasir + Dashboard |

---

## ✨ Fitur Utama

- **Autentikasi** — Login kasir menggunakan Laravel Fortify
- **Manajemen Produk** — CRUD produk dan kategori beserta stok
- **Halaman POS** — Keranjang belanja, kalkulasi total otomatis
- **Pembayaran** — Cash (hitung kembalian) dan QRIS via Midtrans
- **Dashboard** — Ringkasan penjualan harian dan notifikasi stok rendah
- **Riwayat & Laporan** — Filter transaksi, export PDF
- **Chatbot Gemini AI** — Tanya stok/harga, analisis laporan, input via teks atau suara (mic)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 |
| Frontend | Blade + TailwindCSS v4 |
| Database | MySQL |
| Auth | Laravel Fortify |
| Payment | Midtrans (Snap) |
| AI | Google Gemini API |
| HTTP Client | Guzzle |

---

## 📁 Struktur Folder

```
pos-warung/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ReportController.php
│   │   │   └── ChatbotController.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   └── ChatHistory.php
│   └── Services/
│       ├── MidtransService.php
│       └── GeminiService.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── dashboard/
│       ├── products/
│       ├── orders/
│       ├── reports/
│       └── chatbot/
└── routes/
    └── web.php
```

---

## 🗄️ Skema Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data kasir (name, email, password) |
| `categories` | Kategori produk |
| `products` | Produk warung (harga, stok, kategori) |
| `orders` | Header transaksi |
| `order_items` | Detail item per transaksi |
| `payments` | Data pembayaran (cash / QRIS) |
| `chat_histories` | Riwayat percakapan dengan Gemini |

---

## ⚙️ Instalasi & Menjalankan Project

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/pos-warung.git
cd pos-warung

# 2. Install dependensi PHP
composer install

# 3. Install dependensi Node
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi .env (lihat bagian Environment Variables)

# 7. Jalankan migrasi dan seeder
php artisan migrate --seed

# 8. Build assets
npm run dev

# 9. Jalankan server
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 🔑 Environment Variables

Tambahkan variabel berikut di file `.env`:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_warung
DB_USERNAME=root
DB_PASSWORD=

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false

# Gemini AI
GEMINI_API_KEY=your_gemini_api_key
```

### Mendapatkan API Key

- **Midtrans** — Daftar di [dashboard.midtrans.com](https://dashboard.midtrans.com), gunakan mode Sandbox untuk development
- **Gemini** — Dapatkan API key di [aistudio.google.com](https://aistudio.google.com)

---

## 👤 Akun Default (Seeder)

| Email | Password | Role |
|-------|----------|------|
| kasir@poswarung.test | password | Kasir |

---

## 🗺️ Alur Penggunaan

```
Login
  └── Dashboard (ringkasan penjualan + stok rendah)
        ├── Manajemen Produk → tambah / edit / hapus produk & kategori
        ├── Halaman POS → pilih produk → keranjang → bayar
        │     ├── Cash → input nominal → hitung kembalian → selesai
        │     └── QRIS → Midtrans Snap popup → scan QR → webhook → selesai
        ├── Riwayat & Laporan → filter tanggal → export PDF
        └── Chatbot Gemini → tanya stok/harga → analisis laporan → voice input
```

---

## 📅 Target Pengerjaan

### Minggu 1 — Fondasi
| Hari | Target | PIC |
|------|--------|-----|
| 1–2 | Setup project, install semua dependensi, konfigurasi .env | Radhitya |
| 2–3 | Desain UI semua halaman di Figma | Aisha |
| 3–4 | Konfigurasi Fortify, halaman login, layout app.blade.php | Dzilal |
| 3–5 | Migration + seeder semua tabel, Model + relasi | Semua |

### Minggu 2 — Fitur Utama
| Hari | Target | PIC |
|------|--------|-----|
| 1–2 | CRUD Kategori + Produk (controller + view) | Abi Rangga |
| 2–4 | Halaman POS: keranjang, kalkulasi total, checkout | Ghalib |
| 3–5 | Integrasi Midtrans: Cash + QRIS + webhook handler | Radhitya |

### Minggu 3 — Fitur Pendukung
| Hari | Target | PIC |
|------|--------|-----|
| 1–2 | Dashboard: ringkasan penjualan, stok rendah | Ghalib |
| 2–4 | Riwayat transaksi, filter tanggal, export PDF | Dzilal |
| 3–5 | Chatbot Gemini: teks, analisis laporan, voice input | Radhitya |

### Minggu 4 — Finishing
| Hari | Target | PIC |
|------|--------|-----|
| 1–2 | Implementasi desain Figma ke semua halaman Blade | Semua |
| 3 | Testing end-to-end semua fitur | Semua |
| 4 | Bug fix & penyesuaian UI | Semua |
| 5 | Deploy / demo final + dokumentasi | Radhitya |

---

## 🤝 Panduan Kontribusi Tim

```bash
# Sebelum mulai coding, selalu pull dulu
git pull origin main

# Buat branch baru untuk setiap fitur
git checkout -b fitur/nama-fitur

# Setelah selesai, push dan buat Pull Request
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
