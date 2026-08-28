# Portal Topup Game (VraStore)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue.js-3.x-green.svg?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-2.x-purple.svg?style=flat-square)](https://inertiajs.com)
[![Tailwind](https://img.shields.io/badge/Tailwind_CSS-3.x-38bdf8.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)

Aplikasi portal top-up game yang dikembangkan menggunakan **Laravel 12**, **Inertia.js (Vue 3)**, dan **Tailwind CSS**. Sistem ini menyediakan fungsionalitas pemesanan produk game, manajemen inventaris produk, dashboard admin, dan integrasi awal dengan Tripay Payment Gateway.

---

## 🌟 Fitur yang Tersedia

### Sisi Pengguna (Public & Customer)
- **Halaman Utama (Home):** Menampilkan daftar game aktif, banner promo, dan berita terbaru.
- **Formulir Pemesanan (Game Page):** Pemilihan produk/nominal topup dan pengisian data akun game (User ID / Server ID).
- **Validasi Kode Voucher:** Fitur klaim voucher diskon aktif saat proses checkout.
- **Halaman Invoice & Pelacakan Status:** Halaman khusus untuk menampilkan detail pembayaran dan status pesanan menggunakan nomor invoice unik.

### Sisi Administrator (Admin Panel)
- **Dashboard Statis:** Menampilkan ringkasan ringkas terkait transaksi.
- **Manajemen Game & Produk:** Mengelola daftar game, kategori produk, serta produk top-up beserta harga.
- **Manajemen Metode Pembayaran:** Mengelola opsi pembayaran (Payment Method & Payment Channel) serta pengaturan biaya admin (*fee*).
- **Manajemen Konten:** Mengelola data banner promosi, pengumuman/berita, dan voucher diskon.
- **Manajemen Konfigurasi API:** Menu pengaturan kredensial API pihak ketiga seperti Tripay secara dinamis dari panel admin.
- **Laporan Penjualan:** Rekap harian penjualan serta ekspor laporan transaksi ke dokumen PDF.

---

## 📂 Arsitektur Kode

Proyek ini menggunakan pemisahan logika yang rapi untuk mempermudah pemeliharaan:

```text
app/
├── Http/Controllers/
│   ├── Admin/         # Controller pengelolaan data master & laporan di dashboard admin
│   ├── Api/           # Webhook controller untuk menangkap callback dari Tripay
│   └── Customer/      # Controller alur transaksi publik (Home, Checkout, Invoice)
├── Repositories/      # Penanganan kueri database untuk memisahkan logika Eloquent
├── Services/          # Logika bisnis utama & modul integrasi payment gateway
├── Models/            # Model Eloquent (Game, Product, Order, Voucher, dll.)
└── Jobs/              # Queue handler untuk pemrosesan status pesanan (ProcessOrderJob)
```

---

## 🚀 Panduan Instalasi Lokal

### 1. Klon Repositori
```bash
git clone https://github.com/username/topup.git
cd topup
```

### 2. Instal Dependensi Backend & Frontend
```bash
composer install
npm install
```

### 3. Konfigurasi Database
Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database Anda di sana.

### 4. Setup Aplikasi
Jalankan perintah berikut untuk menghasilkan application key, menjalankan migrasi database, dan mengisi data awal (seeder):
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Menjalankan Server Pengembangan
Gunakan perintah bawaan composer untuk menjalankan server Laravel, Vite compiler, dan queue worker secara bersamaan:
```bash
composer run dev
```

---

## 💳 Integrasi Pembayaran (Tripay Callback)

Aplikasi ini mendukung sinkronisasi status pembayaran menggunakan webhook Tripay:
- Endpoint callback terdaftar pada rute `/api/webhook/tripay` (`Api/WebhookController.php`).
- Menggunakan verifikasi signature `SHA256` untuk memastikan callback resmi dikirim dari server Tripay.
- Menerapkan **Pessimistic Locking** (`lockForUpdate()`) selama transaksi database berlangsung untuk menjamin konsistensi data order dari kondisi balapan (*race conditions*).

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi [MIT](LICENSE).
