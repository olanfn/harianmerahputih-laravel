# Harian Merah Putih

Portal berita Harian Merah Putih dibangun dengan Laravel 12, Blade, Alpine.js, dan Vite. Aplikasi ini disiapkan untuk Hostinger Premium dengan document root domain diarahkan ke folder `public/`.

## Fitur yang tersedia

### Publik

- Beranda editorial dengan headline, berita terbaru, berita pilihan, dan sidebar terpopuler.
- Halaman kategori, tag, indeks, pencarian, pagination, dan detail artikel.
- Filter artikel **Terbaru**, **Terpopuler**, dan **Pilihan Editor**.
- Halaman publik institusional dengan template yang konsisten: `/redaksi`, `/tentang-kami`, `/pedoman-media-siber`, `/kebijakan-privasi`, dan `/hubungi-redaksi`.
- Featured image, galeri multi-gambar, gambar inline, alt text, caption, dan fallback brand.
- Sitemap, news sitemap, RSS, robots.txt dinamis, serta redirect slug lama.
- Metadata SEO, Open Graph, JSON-LD `NewsArticle`, dan URL siap didaftarkan ke Google Search Console setelah deployment HTTPS.
- Layout responsif untuk desktop dan mobile, termasuk bottom navigation mobile.
- Endpoint diagnostik `/internal/readiness` hanya aktif pada environment `local` dan `testing`; endpoint ini otomatis 404 pada production.

Data yang dibuat oleh seeder diberi label demonstrasi dan tidak boleh dianggap sebagai berita faktual.

### CMS dan peran

Panel privat berada di `/${ADMIN_PATH}` (nilai default: `/admin`) dan tidak memiliki registrasi publik. Otorisasi dilakukan di server berdasarkan peran:

- **Super Admin**: akses penuh termasuk pengguna dan konfigurasi editorial.
- **Admin**: pengelolaan artikel, media, kategori, tag, dan halaman publik.
- **Editor**: menyunting artikel dan mengelola proses editorial sesuai policy.
- **Writer**: membuat dan menyunting artikel sesuai batas status yang diizinkan.

CMS menyediakan CRUD artikel, kategori, tag, dan pengguna sesuai role; preview privat draft/review; rich text editor; upload banyak gambar dengan thumbnail/status, retry, drag-and-drop, pengurutan, featured image, alt text, dan caption; penyisipan media existing; relasi media `featured`, `gallery`, dan `inline`; audit log; cleanup media temporary; serta editor halaman publik yang memakai template Redaksi yang sama.

## Menjalankan proyek di lokal

Prasyarat: PHP 8.3+, Composer, Node.js/npm, dan MySQL/MariaDB atau SQLite untuk development.

```bash
composer install
copy .env.example .env       # Windows; gunakan cp pada macOS/Linux
php artisan key:generate
npm install
```

Atur koneksi database di `.env`, kemudian jalankan migration:

```bash
php artisan migrate
```

Buat symlink media agar file pada `storage/app/public` dapat diakses melalui `/storage`:

```bash
php artisan storage:link
```

Jalankan server dan Vite saat mengembangkan:

```bash
php artisan serve
npm run dev
```

Buka `http://127.0.0.1:8000/`.

## Akun development

Seeder hanya berjalan pada environment `local` atau `testing`. Setelah migration, buat data demonstrasi dan akun development dengan:

```bash
php artisan db:seed
```

Akun yang disediakan seeder:

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | `superadmin@harianmerahputih.test` | `password` |
| Admin | `admin@harianmerahputih.test` | `password` |
| Editor | `editor@harianmerahputih.test` | `password` |
| Writer | `writer@harianmerahputih.test` | `password` |

Seeder bersifat idempotent sehingga dapat dijalankan ulang di lokal tanpa menggandakan data demonstrasi. Ganti atau hapus kredensial ini sebelum memakai database lokal untuk data nyata.

**Jangan menjalankan `php artisan db:seed` di production.** Akun production harus dibuat melalui prosedur privat yang aman dan password development `password` tidak boleh digunakan di production.

## Email dan lupa password

Untuk lokal, gunakan mailer log:

```dotenv
MAIL_MAILER=log
```

Kirim permintaan lupa password dari `/admin/forgot-password`, kemudian periksa `storage/logs/laravel.log`. Tautan yang dicatat menggunakan route privat `/admin/reset-password/{token}`. Pada production, ganti mailer log dengan SMTP nyata, gunakan `APP_URL=https://domain-anda.tld`, dan pastikan tautan reset memakai domain HTTPS production—bukan `localhost`. Detail konfigurasi SMTP dan checklist verifikasi tersedia di [docs/06_HOSTINGER_DEPLOYMENT.md](docs/06_HOSTINGER_DEPLOYMENT.md).

## Test, cache, dan build aset

```bash
php artisan test
php artisan view:cache
npm run build
```

Untuk memeriksa jadwal cleanup media temporary:

```bash
php artisan schedule:list
```

Saat development, jalankan `composer run dev` agar web server, scheduler, queue listener,
log viewer, dan Vite berjalan bersamaan. Scheduler lokal diperlukan agar artikel dengan
status **Jadwalkan** otomatis berubah menjadi **Terbitkan** ketika waktu publikasinya tiba.
Pada production Hostinger, gunakan cron `php artisan schedule:run` setiap menit seperti
yang dijelaskan pada bagian deployment di bawah.

## Deployment Hostinger

Deployment tidak dilakukan oleh repository ini. Ikuti panduan khusus proyek di [docs/06_HOSTINGER_DEPLOYMENT.md](docs/06_HOSTINGER_DEPLOYMENT.md), lalu gunakan checklist QA di [docs/07_QA_AND_RELEASE.md](docs/07_QA_AND_RELEASE.md).

Hal penting:

- Gunakan PHP 8.3+ dan ekstensi Laravel yang diperlukan.
- Simpan root Laravel di luar web root; hanya `public/` yang menjadi document root.
- Buat database MySQL/MariaDB khusus aplikasi.
- Atur `.env` production dengan `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS, dan kredensial database nyata.
- Unggah `vendor/`, `public/build/`, dan `public/branding/`; `node_modules/` tidak diperlukan di server.
- Jalankan migration terkontrol dengan `php artisan migrate --force`.
- Jalankan `php artisan storage:link`, cache produksi, dan atur izin `storage/` serta `bootstrap/cache/`.
- Daftarkan cron `php artisan schedule:run` setiap menit untuk Scheduler Laravel.
- Verifikasi login admin, artikel published, multi-gambar, sitemap, RSS, robots, redirect 301, HTTPS, dan log error setelah rilis.
- Setelah domain aktif, verifikasi properti di Google Search Console melalui DNS lalu submit `/sitemap.xml`; panduan lengkap tersedia di [docs/13_PRODUCTION_SEO.md](docs/13_PRODUCTION_SEO.md).

### Pengecualian paket deployment

Jangan masukkan artefak lokal berikut ke arsip Hostinger:

```text
.env
node_modules/
database/database.sqlite
storage/app/qa/
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
.phpunit.result.cache
tests/
docs/branding.zip
```

`storage/app/qa/` berukuran sekitar 62 MB dan hanya berisi data browser QA. Folder `storage/app/public/media/` hanya ikut diunggah jika berisi media final production; pertahankan struktur folder tersebut bila digunakan.

Dokumen proyek lainnya tersedia di [docs/](docs/), termasuk [guardrails](docs/01_PROJECT_GUARDRAILS.md), [CMS editorial](docs/04_CMS_EDITORIAL.md), [SEO dan distribusi](docs/05_MEDIA_SEO.md), dan [QA release](docs/07_QA_AND_RELEASE.md).
