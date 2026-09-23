# Fase 4 — Media, SEO, dan Distribusi

## SEO teknis

- Canonical URL per halaman publik.
- Title, description, Open Graph, dan X metadata dari data artikel dengan fallback aman.
- JSON-LD `NewsArticle` hanya untuk artikel yang benar-benar sudah dipublikasikan.
- `sitemap.xml`, News Sitemap bila siap digunakan, dan `robots.txt` sesuai status produksi.
- RSS artikel publik.
- Tabel redirect 301 untuk slug lama bila perlu migrasi URL.

## Media

- Simpan berkas di disk `public` Laravel dengan struktur tanggal/jenis yang konsisten.
- Media artikel menggunakan relasi `ArticleMedia` dan `sort_order`, sehingga satu artikel dapat memiliki gambar utama, galeri, dan gambar inline tanpa data duplikat.
- Generate ukuran turunan gambar secara terbatas dan aman; tugas berat harus tetap bisa dijalankan manual/scheduler tanpa worker permanen.
- Wajib alt text dan caption yang dapat diedit.
- Batas ukuran file, dimensi, ekstensi, dan MIME type didokumentasikan di konfigurasi.
- Gunakan gambar utama artikel sebagai fallback Open Graph hanya jika editor sudah menetapkannya; jangan memilih gambar acak dari galeri.

## Modul bisnis bertahap

- Slot iklan internal (bukan script pihak ketiga secara default).
- Newsletter hanya menyimpan alamat email setelah ada kebijakan privasi dan mekanisme persetujuan.
- Analytics harus dipasang setelah persetujuan provider dan kebijakan privasi siap.

## Kriteria penerimaan

- Tidak ada `noindex` pada halaman artikel publik produksi.
- Artikel draft tidak muncul di sitemap atau RSS.
- URL/slug lama yang tercatat dapat diarahkan 301 tanpa loop.
