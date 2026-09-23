# Changelog Proyek

## 2026-09-22 — Fase 1 Foundation

- Membuat scaffold Laravel 12 dengan PHP 8.3+ compatible.
- Menyiapkan `.env.example` untuk MySQL/MariaDB, cache file/database, session database, disk public lokal, dan queue `sync`.
- Menambahkan layout Blade mobile-first, utility bar, brand, navigasi kategori, pencarian placeholder, footer, dan notifikasi status.
- Menambahkan konfigurasi 10 kategori, favicon, brand mark, beranda tanpa berita contoh, serta `/internal/readiness`.
- Menambahkan Alpine.js, build Vite/Tailwind, storage link, dan test foundation.
- Tidak mengerjakan login, CMS, upload media, atau modul berita publik.

Verifikasi: `php artisan view:cache`, `php artisan test`, `npm run build`, dan smoke test HTTP `/` serta `/internal/readiness` berhasil.

## 2026-09-22 — Fase 2 Portal Berita Publik

- Menambahkan model dan migration `Category`, `Article`, `Tag`, `ArticleTag`, dan `Setting`.
- Menambahkan factory dan seeder untuk kategori, tag, author demonstrasi, serta artikel `published`, `draft`, `scheduled`, dan `archived`.
- Menambahkan beranda, kategori, detail artikel, tag, pencarian terbatas 100 karakter, indeks, pagination, empty state, dan 404 melalui route model binding.
- Semua query publik menggunakan scope `published`: status harus `published`, `published_at` tidak null, dan waktunya tidak boleh di masa depan.
- Tidak mengerjakan CMS/admin, login, upload media, newsletter, iklan, analytics, atau deployment.

Verifikasi: migration, seeder, test visibility artikel, route binding, view cache, dan build aset dijalankan.

## 2026-09-22 — Fase 4 Media, SEO, dan Distribusi

- Menambahkan canonical URL, meta description/title, Open Graph, X metadata, dan JSON-LD `NewsArticle` untuk artikel published non-demo.
- Menggunakan featured media yang sudah ditetapkan sebagai gambar sosial; tidak memilih gambar acak dari galeri.
- Menambahkan `sitemap.xml`, `news-sitemap.xml`, `feed.xml`, dan `robots.txt`.
- Menambahkan `article_redirects` dan redirect 301 slug lama yang hanya mengikuti target artikel published.
- Menjaga filter `published` untuk sitemap, news sitemap, RSS, metadata berita, dan detail publik.
- Tidak mengerjakan newsletter, iklan, analytics, atau deployment.

Verifikasi: `php artisan test` — 12 test dan 71 assertion lulus; `php artisan view:cache` dan `npm run build` berhasil.

## 2026-09-22 — Fase 3 CMS Completion

- Menolak penghapusan kategori yang masih dipakai artikel dan mengubah FK kategori menjadi `RESTRICT`.
- Menampilkan featured image, gallery, dan inline media terurut pada detail artikel publik dengan fallback alt/caption.
- Menambahkan Media Library privat, proteksi penghapusan media berelasi, Audit Log privat, tombol hapus berkonfirmasi, dan reset password rate-limited.
- Menambahkan snapshot revision artikel, riwayat, dan pemulihan sebagai draf dengan policy.
- Menambahkan entitas dan modul terpisah Foto Peristiwa serta Merah Putih TV dengan status, media, urutan, CRUD privat, dan halaman publik.

Verifikasi: `php artisan test` — 19 test dan 109 assertion lulus; `php artisan view:cache`, `npm run build`, dan `php artisan schedule:list` berhasil.

## 2026-09-22 — Inline Media Token

- Editor artikel menyisipkan token aman `[[media:ID]]` pada posisi kursor.
- Renderer publik dan preview privat hanya mengganti token bila media ber-role `inline` dan benar-benar terhubung ke artikel.
- Token invalid atau media tidak terkait diabaikan; isi teks tetap dirender melalui escaping Blade.
- Featured image dan gallery tetap diproses terpisah; inline tidak dikumpulkan di akhir gallery.

Verifikasi: `php artisan test` — 21 test dan 118 assertion lulus; `php artisan view:cache` dan `npm run build` berhasil.