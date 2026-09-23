# Fase 6 — QA dan Rilis

## Uji fungsi

- Beranda, kategori, tag, indeks, detail artikel, pencarian, pagination, dan 404.
- Tampilan 360px, 768px, 1024px, dan desktop lebar.
- Artikel draf, review, jadwal, serta arsip tidak bocor ke publik.
- Login, logout, pembatasan peran, CSRF, dan validasi upload.
- Pilih banyak gambar, kegagalan satu unggahan, retry, urutan drag-and-drop, gambar utama, caption/alt text, penyisipan inline, dan penghapusan relasi media.
- Meta canonical, sitemap, RSS, robots, redirect, dan link gambar.

## Uji operasional

- Pastikan `APP_DEBUG=false` di produksi.
- Pastikan `.env` tidak dapat diakses HTTP.
- Periksa `storage/logs/laravel.log` setelah deploy.
- Periksa hak tulis `storage/` dan `bootstrap/cache/`.
- Pastikan cron Scheduler berjalan jika ada tugas terjadwal.

## Kondisi publikasi

Jangan buka indeks mesin pencari sampai konten, halaman kebijakan, kontak, identitas redaksi, dan metadata produksi telah disetujui. Hapus status demonstrasi/noindex hanya saat artikel dan data benar-benar siap diterbitkan.

## Laporan agent sesudah setiap fase

1. Fase yang selesai dan ringkasan hasil.
2. File yang dibuat/diubah.
3. Perintah atau langkah verifikasi yang dijalankan beserta hasilnya.
4. Hal yang sengaja belum dikerjakan.
5. Blocker atau tindakan yang diperlukan dari pemilik proyek.
