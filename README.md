# 📚 Aplikasi Perpustakaan

Selamat datang di **Perpustakaan App**!  
Sebuah aplikasi berbasis **Laravel** untuk mengelola data buku, anggota, serta transaksi peminjaman dan pengembalian.  
Tujuan proyek ini simpel: bikin sistem perpustakaan yang **mudah, cepat, dan elegan**. ✨

---

## 🚀 Fitur Utama (rencana/roadmap)
- ✅ Autentikasi (Login & Register)
- 📖 Manajemen Buku (Tambah, Edit, Hapus, Cari)
- 👥 Manajemen Anggota
- 🔄 Transaksi Peminjaman & Pengembalian
- 📊 Laporan sederhana (riwayat pinjam & status buku)

> *Catatan:* Fitur masih dalam tahap pengembangan, jadi jangan kaget kalau ada bug yang nongol lebih cepat daripada notifikasi WA mantan. 😅

---

## 🛠️ Instalasi & Setup

1. **Clone repository**
   ```bash
   git clone https://github.com/kyraara/perpustakaan.git
   cd perpustakaan
2.**Install dependencies**
composer install
3.**Copy file environment**
cp .env.example .env
4.**Generate key**
php artisan key:generate
5.**Setup database**
```Edit .env sesuai konfigurasi database lokal (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
``Jalankan migrasi & seeder:
php artisan migrate --seed
6.**Jalankan aplikasi**
php artisan serve
Akses via browser 👉 http://localhost:8000

---

Mau gue bikinin juga **contoh `CONTRIBUTING.md`** biar repo lu keliatan makin profesional (aturan kontribusi, cara bikin PR, coding style)?


