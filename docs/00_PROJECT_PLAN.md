# Rencana Proyek — Harian Merah Putih

## Keputusan utama

Bangun `harianmerahputih.id` sebagai proyek baru berbasis **Laravel 12** yang dapat dijalankan pada Hostinger Premium. Proyek dimulai dari nol dengan struktur, database, dan konfigurasi hostingnya sendiri.

## Target produksi

- PHP 8.3+ dan Laravel 12.
- Blade, Tailwind CSS, dan Alpine.js untuk antarmuka.
- MySQL/MariaDB, Eloquent, serta Laravel migrations.
- Storage lokal (`storage/app/public` + symbolic link public) untuk gambar dan media.
- Cache file atau database, session database, queue `sync` pada fase awal.
- Laravel Scheduler melalui cron Hostinger.

## Tidak digunakan di produksi

- Runtime Node.js di server produksi.
- PostgreSQL, Redis, Docker, Supervisor, PM2, persistent worker, VPS, dan S3.
- Pendaftaran pengguna publik.

## Prinsip eksekusi

1. Satu fase dikerjakan dalam satu sesi agent dan wajib diverifikasi sebelum fase berikutnya.
2. Jangan mengarang fakta/berita. Seed dan data demonstrasi harus ditandai sebagai contoh.
3. Jangan memasukkan kredensial ke Git atau percakapan. Gunakan `.env` lokal/hosting.
4. Setiap perubahan harus kecil, terfokus, dan tidak merusak fitur yang sudah berjalan.
5. Semua halaman publik harus responsif sejak dibuat.

## Tahapan

| Fase | Dokumen | Hasil |
|---|---|---|
| 0 | `01_PROJECT_GUARDRAILS.md` | Batas kerja proyek baru |
| 1 | `02_FOUNDATION.md` | Laravel siap jalan, basis UI dan layout publik |
| 2 | `03_PUBLIC_NEWS.md` | Beranda, kategori, detail artikel, pencarian |
| 3 | `04_CMS_EDITORIAL.md` | Admin privat, RBAC, editorial workflow |
| 4 | `05_MEDIA_SEO.md` | Media, SEO, sitemap, RSS, redirect |
| 5 | `06_HOSTINGER_DEPLOYMENT.md` | Deploy aman di Hostinger Premium |
| 6 | `07_QA_AND_RELEASE.md` | Uji regresi dan checklist rilis |

Fase 1–2 membentuk MVP. Fase 3–5 hanya dikerjakan setelah MVP stabil.
