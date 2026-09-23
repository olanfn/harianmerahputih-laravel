# Fase 2 — Portal Berita Publik (MVP)

## Tujuan

Membuat pengalaman pembaca lengkap untuk artikel yang sudah dipublikasikan.

## Model data minimum

| Model | Kolom inti |
|---|---|
| Category | name, slug, description, is_active, sort_order |
| Article | category_id, title, slug, excerpt, body, status, published_at, author_id, seo_title, seo_description |
| Tag | name, slug |
| ArticleTag | article_id, tag_id |
| Setting | key, value, type |

Status artikel: `draft`, `review`, `scheduled`, `published`, `archived`. Halaman publik hanya menampilkan `published` dengan `published_at <= now()`.

## Rute publik

- `/` — beranda: headline, terkini, populer (awalnya berdasarkan data sederhana), dan daftar kategori.
- `/kategori/{category:slug}` — indeks kategori dengan pagination.
- `/berita/{article:slug}` — detail artikel.
- `/cari?q=` — pencarian server-side yang dipaginasi.
- `/tag/{tag:slug}` — indeks tag.
- `/indeks` — indeks kategori dan arsip dasar.

Gunakan slug unik. Parameter pencarian harus dibatasi panjangnya, di-escape saat ditampilkan, dan menggunakan query builder/Eloquent; jangan gunakan raw SQL dari input pengguna.

## UI yang dipertahankan

- Identitas Harian Merah Putih — Kebanggaan Indonesia.
- Dominan merah, putih, dan gelap; desain editorial modern, bukan menyalin halaman lama secara pixel-perfect.
- Header desktop dan header mobile, navigasi horizontal yang dapat digeser di layar kecil, ticker terkini, kartu artikel, serta area Foto Peristiwa dan Merah Putih TV sebagai modul placeholder bila datanya belum ada.
- Tidak ada artikel contoh yang boleh terindeks seolah-olah berita faktual.

## Kriteria penerimaan

- Seeder development menghasilkan data dummy yang jelas ditandai demonstrasi.
- Artikel draf/jadwal tidak dapat dibuka publik.
- Pencarian, pagination, kategori, tag, dan detail artikel memiliki empty state dan 404 yang baik.
- Semua meta title/description memiliki fallback aman.
