# Fase 1 — Foundation Laravel

## Tujuan

Membuat fondasi Laravel 12 yang siap dikembangkan sebagai portal berita, tetap ringan untuk Hostinger Premium.

## Lingkup

1. Inisialisasi Laravel 12 dengan PHP 8.3+.
2. Konfigurasi `.env.example` untuk MySQL/MariaDB, cache file/database, session database, storage lokal, dan queue `sync`.
3. Pasang Tailwind CSS dan Alpine.js saat pengembangan; hasil aset produksi wajib dibangun sebelum upload. Node.js tidak dibutuhkan di server produksi.
4. Buat layout Blade mobile-first: utility bar, logo, navigasi kategori, pencarian, footer, dan komponen notifikasi.
5. Buat konfigurasi kategori: Nasional, Politik, Hukrim, Kesehatan, Ekbis, Pendidikan, Olahraga, Lifestyle, Viral, dan Internasional.
6. Buat halaman health/internal readiness yang tidak membocorkan konfigurasi rahasia dan hanya aktif pada environment `local` serta `testing`; pada production endpoint harus 404.
7. Siapkan favicon dan placeholder aset brand yang telah disetujui.

## Kriteria penerimaan

- `composer install` dan build aset berjalan di lingkungan pengembangan.
- Beranda dapat dirender responsif tanpa data artikel.
- Navigasi, heading, focus state, dan teks utama dapat digunakan di mobile maupun desktop.
- Tidak ada API key, password, atau konfigurasi produksi di repository.
- Proyek dapat disajikan oleh document root `public/`.

## Jangan dikerjakan pada fase ini

- Login/admin, CRUD CMS, upload, newsletter, iklan, integrasi analitik, ataupun berita nyata.
