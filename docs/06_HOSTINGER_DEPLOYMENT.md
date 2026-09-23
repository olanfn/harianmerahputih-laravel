# Fase 5 — Runbook Deploy Hostinger Premium

Dokumen ini adalah panduan rilis **khusus Harian Merah Putih** (Laravel 12, PHP minimal aplikasi 8.2; target hosting **PHP 8.3 atau lebih baru**). Dokumen ini tidak menjalankan deploy, tidak mengubah DNS, dan tidak memuat kredensial.

## Ringkasan audit proyek

| Area | Hasil audit | Implikasi rilis |
| --- | --- | --- |
| Runtime | Laravel 12.69.2, PHP lokal 8.4.22; `composer.json` mensyaratkan PHP `^8.2` | Pilih PHP 8.3+ di hPanel. |
| Database | Migration aplikasi sudah mencakup users, cache, jobs, editorial, media, audit log, dan redirects | Buat database MySQL/MariaDB khusus lalu jalankan migration. |
| Aset | Vite menggunakan aset build di `public/build` | Jalankan build di lokal/CI lalu unggah `public/build`; Node.js tidak wajib ada di hosting. |
| Media | Disk `public` memakai `storage/app/public` dan URL `/storage` | Symlink `public/storage` wajib tersedia. |
| Scheduler | `media:cleanup-temporary` dijadwalkan harian | Cron `schedule:run` wajib dibuat setiap menit. |
| SEO/distribusi | Route sitemap, news sitemap, RSS, robots, dan redirect tersedia | Masuk checklist pascadeploy. |
| Seed development | Seeder development hanya untuk local/testing | Jangan menjalankan `db:seed` pada produksi. Buat akun awal dengan prosedur privat yang disetujui tim. |

Temuan lokal: `php artisan schedule:list` tidak dapat menulis ke `storage/logs/laravel.log` dan cache framework karena izin Windows lokal. Ini bukan perubahan kode atau bukti kegagalan hosting, tetapi menjadikan verifikasi izin tulis di server sebagai langkah wajib.

## 1. Prasyarat di hPanel

Sebelum upload, pemilik proyek memastikan sendiri hal berikut di hPanel:

- Paket Premium untuk domain ini, domain aktif, dan SSL dapat diaktifkan.
- PHP domain disetel ke **8.3+** beserta ekstensi Laravel: OpenSSL, PDO MySQL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, dan ZIP.
- Satu database MySQL/MariaDB serta satu user database khusus aplikasi tersedia.
- Cron Job tersedia dari hPanel.
- Document root domain dapat diarahkan ke folder `public` aplikasi. Ini adalah metode yang diprioritaskan.
- SSH tersedia bila akan menjalankan Composer, Artisan, atau membuat symlink di server. Di paket Hostinger tertentu Composer tersedia melalui SSH; pastikan dahulu versi `php` CLI dan `composer` pada akun.
- Ruang disk cukup untuk source, `vendor`, `storage`, backup database, dan upload media.

Referensi Hostinger: [Composer melalui SSH](https://www.hostinger.com/id/support/5792078-bagaimana-cara-menggunakan-composer-di-hostinger/), [Cron Job hPanel](https://support.hostinger.com/id/articles/1583465-cara-setup-cron-job-di-hostinger/), dan [alur deploy Laravel](https://www.hostinger.com/id/support/6152127-bagaimana-cara-deploy-laravel-8-di-hostinger/).

## 2. Struktur folder produksi yang aman

### Pilihan A — wajib diprioritaskan: document root langsung ke `public/`

```text
/home/ACCOUNT/domains/DOMAIN/
└── harianmerahputih/                 # Laravel root, bukan web root
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/                       # satu-satunya document root domain
    │   ├── build/
    │   ├── branding/
    │   ├── storage -> ../storage/app/public
    │   └── index.php
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env                          # tidak pernah diunggah ke web root
    └── artisan
```

Atur document root domain menjadi path absolut ke `.../harianmerahputih/public`. Dengan ini `php artisan storage:link` bekerja sesuai konfigurasi proyek.

### Pilihan B — hanya jika hPanel tidak menyediakan pengaturan document root

Simpan Laravel root di folder non-web, misalnya `.../harianmerahputih/`, lalu jadikan `public_html/` berisi **hanya salinan isi** `harianmerahputih/public/`. Jangan menyalin `app`, `vendor`, `.env`, `storage`, `routes`, atau `artisan` ke `public_html`.

`public_html/index.php` harus merujuk tepat ke autoloader dan bootstrap di Laravel root non-web. Contoh bila kedua folder adalah sibling:

```php
require __DIR__.'/../harianmerahputih/vendor/autoload.php';
$app = require_once __DIR__.'/../harianmerahputih/bootstrap/app.php';
```

Sesuaikan path dengan path absolut aktual hPanel dan uji sebelum DNS diarahkan. Pada pola ini, link media harus dibuat sebagai `public_html/storage -> ../harianmerahputih/storage/app/public`; `storage:link` standar membuat link di `harianmerahputih/public/storage`, bukan di `public_html`.

**Blocker produksi:** bila tidak bisa mengatur document root **dan** SSH/file manager tidak dapat membuat symlink untuk `public_html/storage`, media artikel tidak dapat disajikan aman. Jangan memindahkan seluruh `storage/` ke web root sebagai jalan pintas; selesaikan kemampuan document root/symlink terlebih dahulu.

## 3. Contoh `.env` produksi

Buat `.env` langsung di Laravel root menggunakan nilai nyata dari hPanel. Jangan commit, kirim chat, atau menyimpan nilai rahasia di dokumen ini.

```dotenv
APP_NAME="Harian Merah Putih"
APP_ENV=production
APP_KEY=base64:GENERATED_ON_SERVER_ONLY
APP_DEBUG=false
APP_URL=https://YOUR_DOMAIN
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID
APP_TIMEZONE=Asia/Jakarta
APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=YOUR_DATABASE_HOST
DB_PORT=3306
DB_DATABASE=YOUR_DATABASE_NAME
DB_USERNAME=YOUR_DATABASE_USER
DB_PASSWORD=YOUR_DATABASE_PASSWORD

CACHE_STORE=file
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@YOUR_DOMAIN"
MAIL_FROM_NAME="${APP_NAME}"

ADMIN_PATH=admin
```

`MAIL_MAILER=log` adalah placeholder aman sampai SMTP produksi diset secara terpisah. Jangan mengganti ke kredensial email pada tahap ini. `ADMIN_PATH` harus konsisten dengan URL panel yang dipilih; nilai saat ini adalah `admin`.

Alur lupa password admin memakai route `/${ADMIN_PATH}/reset-password/{token}`. Sebelum production, konfigurasi SMTP nyata dan `APP_URL` HTTPS domain kanonis; jangan mengirim tautan reset yang masih memakai `localhost`.

## 4. Urutan rilis aman

1. **Siapkan rollback dahulu.** Ambil backup database terverifikasi dan arsip release aktif. Catat path release aktif, versi commit/arsip, dan waktu mulai rilis.
2. **Aktifkan maintenance bila diperlukan.** Dari Laravel root: `php artisan down --render="errors::503"` (opsional untuk rilis singkat). Jangan jalankan jika perintah Artisan belum terbukti memakai PHP yang benar.
3. **Unggah release baru ke folder baru**, misalnya `harianmerahputih-release-YYYYMMDDHHMM`. Unggah source aplikasi, `vendor/` hasil `composer install --no-dev --optimize-autoloader` yang kompatibel, serta seluruh isi `public/build/` dan `public/branding/`. Jangan unggah `.env` lokal, database SQLite lokal, `node_modules`, atau log lokal.
4. **Buat `.env` produksi** dari contoh di atas dan masukkan `APP_KEY` baru melalui `php artisan key:generate --force` hanya di server. Pastikan nilai database mengarah ke database khusus yang baru dibuat.
5. **Atur web root** dengan Pilihan A. Jika memakai Pilihan B, salin hanya isi folder `public/` ke `public_html/`, sesuaikan dua path `index.php`, dan validasikan media symlink terlebih dahulu.
6. **Instal dependency bila SSH/Composer tersedia:** `composer install --no-dev --optimize-autoloader --no-interaction`. Bila Composer/SSH tidak tersedia, unggah `vendor/` yang dibangun dari environment PHP 8.3 yang kompatibel dan jangan menjalankan `composer update` di hosting.
7. **Jalankan migration secara terkontrol:** `php artisan migrate --force`. Periksa output dan jangan jalankan `db:seed` pada produksi.
8. **Buat storage link:** `php artisan storage:link`. Konfirmasi bahwa URL `/storage/...` menunjuk ke `storage/app/public`. Pada Pilihan B, buat link di web root seperti dijelaskan sebelumnya dan jangan membuat folder storage duniawi yang terbuka.
9. **Atur izin minimum yang diperlukan** untuk user web server pada `storage/` dan `bootstrap/cache/` (bukan `777` secara default). Setelah itu cek tulis log dan cache. Metode dan angka izin mengikuti user/group hosting; minta dukungan Hostinger jika owner file hasil upload berbeda dari proses PHP.
10. **Bersihkan lalu cache konfigurasi rilis:**

    ```bash
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

    Jalankan `php artisan optimize` hanya bila seluruh langkah di atas sudah sukses. Setiap perubahan `.env` berikutnya harus diikuti `php artisan config:clear` / `config:cache`.
11. **Daftarkan Cron Job hPanel** pada menu Website → Kelola → Cron Job. Gunakan setiap menit dan path yang benar dari hPanel:

    ```cron
    * * * * * /PATH/TO/php /PATH/TO/harianmerahputih/artisan schedule:run >> /PATH/TO/logs/hmp-scheduler.log 2>&1
    ```

    Hostinger mendokumentasikan waktu cron dalam UTC+0; aplikasi sendiri memakai `Asia/Jakarta`. Gunakan path PHP CLI yang ditampilkan/terverifikasi di akun, bukan contoh ini secara mentah.
12. **Matikan maintenance:** `php artisan up`, lalu lakukan checklist pascadeploy sebelum rilis diumumkan.

### Daftar pengecualian paket upload

Buat arsip deployment dari source aplikasi, tetapi keluarkan artefak lokal berikut. Daftar ini tidak berarti source penting boleh dihapus dari proyek:

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

`storage/app/qa/` berisi sekitar 62 MB data browser QA dan tidak diperlukan di Hostinger. Folder `storage/app/public/media/` juga jangan diunggah jika isinya hanya media lokal/QA. Jika berisi konten final yang memang akan dipakai production, unggah isinya dan pertahankan struktur `media/` agar URL serta relasi file tidak berubah.

Setelah upload, buat ulang direktori runtime yang diperlukan (`storage/framework/cache`, `storage/framework/sessions`, dan `storage/framework/views`) bila file manager/arsip tidak menyertakannya, lalu pastikan permission runtime dapat ditulis oleh PHP.

## 5. Checklist verifikasi pascadeploy

Gunakan browser privat dan satu akun admin yang disiapkan secara aman. Jangan memasukkan password ke checklist atau log.

- [ ] HTTPS aktif, sertifikat valid, dan `http://` mengarah 301 ke URL HTTPS kanonis.
- [ ] Beranda, kategori, tag, indeks, pencarian, pagination, dan detail artikel published memuat tanpa 500.
- [ ] Halaman admin di `/${ADMIN_PATH}` dapat login; halaman admin tidak tersedia bagi pengguna anonim.
- [ ] Gambar utama artikel muncul, dan URL gambarnya berada di `/storage/...` tanpa akses ke file di luar `public/`.
- [ ] Galeri multi-gambar, urutan, alt text/caption, gambar inline, dan penghapusan relasi media diuji pada artikel uji privat.
- [ ] `https://YOUR_DOMAIN/sitemap.xml`, `/news-sitemap.xml`, `/feed.xml`, dan `/robots.txt` memberi respons benar dan hanya memakai URL produksi.
- [ ] Redirect slug lama memberi HTTP 301 ke URL artikel baru yang benar.
- [ ] `APP_DEBUG=false` dikonfirmasi lewat perilaku halaman error; tidak ada stack trace publik.
- [ ] `storage/logs/laravel.log` diperiksa setelah halaman publik, login, dan upload diuji; tidak ada error permission, database, atau path.
- [ ] Cron memiliki log/indikasi eksekusi; tunggu satu siklus harian atau jalankan `php artisan schedule:run` sekali secara manual untuk memvalidasi jadwal cleanup tanpa menghapus media yang masih dipakai.
- [ ] Uji lebar 375px, 768px, 1024px, dan 1440px untuk halaman publik utama sesuai dokumen QA.

## 6. Rencana rollback

### Jika upload, Composer, cache, atau konfigurasi gagal sebelum migration

1. Biarkan atau arahkan kembali document root ke release lama.
2. Pulihkan `.env` release lama yang kompatibel.
3. Jalankan `php artisan optimize:clear` pada release yang akan dipakai bila cache menyebabkan error.
4. Periksa log, perbaiki release baru secara terpisah, lalu ulangi deploy. Database tidak perlu disentuh.

### Jika migration gagal

1. Hentikan rilis dan simpan output error serta log; jangan menjalankan `migrate:fresh`, menghapus tabel, atau menyimpulkan aman untuk `migrate:rollback`.
2. Jika migration belum mengubah skema/data, arahkan aplikasi kembali ke release lama dan investigasi di staging.
3. Jika sudah ada perubahan, kembalikan **database backup yang dibuat sebelum rilis** dan kode ke release lama secara berpasangan. Gunakan rollback Artisan hanya bila migration tersebut benar-benar reversible dan dampaknya telah diperiksa.
4. Verifikasi halaman publik, login admin, dan media setelah pemulihan.

### Jika symlink/media gagal

Arahkan kembali web root ke release sebelumnya yang memiliki link berfungsi. Jangan membuat `storage/` penuh dapat diakses publik. Selesaikan blocker document root/symlink dengan Hostinger sebelum mencoba lagi.

## 7. Informasi yang pemilik proyek perlu ambil dari hPanel

Simpan pribadi dan jangan kirim nilai rahasia melalui issue atau chat:

- Nama domain final, URL kanonis (`www` atau non-`www`), serta status SSL/Force HTTPS.
- Path absolut akun, path Laravel root, path document root, path binary PHP CLI, dan versi PHP CLI.
- Apakah domain dapat diarahkan langsung ke subfolder `public/`.
- Apakah SSH, Composer 2, dan pembuatan symlink tersedia untuk akun ini.
- Nama host database, port, database, username database, dan password database baru.
- Pengaturan/ownership file yang diperlukan agar PHP dapat menulis ke `storage/` dan `bootstrap/cache/`.
- Path/menu Cron Job, zona waktu cron, dan tujuan file log scheduler.
- Kebijakan backup database/file dan cara restore di hPanel.
- Batas upload file, ruang disk, serta batas proses yang dapat memengaruhi upload banyak gambar.

## Batas fase ini

Tidak ada deployment, DNS, kredensial, newsletter, iklan, analytics, atau perubahan fitur aplikasi yang dilakukan dalam Fase 5. Sebelum mengeksekusi langkah rilis, gunakan `docs/07_QA_AND_RELEASE.md` sebagai checklist QA fungsional dan visual.
