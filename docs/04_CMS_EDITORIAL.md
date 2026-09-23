# Fase 3 — CMS dan Editorial

## Tujuan

Menyediakan panel privat agar redaksi dapat mengelola konten tanpa mengubah kode.

## Akses dan peran

Tidak ada registrasi publik. URL login harus dapat dikonfigurasi melalui environment/config dan tidak ditampilkan sebagai CTA publik.

| Peran | Hak minimum |
|---|---|
| Super Admin | Pengaturan, user, peran, seluruh konten |
| Admin | Konten, media, kategori, tag, sebagian pengaturan |
| Editor | Review, edit, jadwal, terbitkan sesuai izin |
| Writer | Buat dan ubah artikel miliknya; kirim untuk review |

Gunakan middleware/policy pada setiap aksi. Menyembunyikan tombol UI tidak cukup sebagai kontrol akses.

## Modul

1. Dashboard ringkas.
2. Artikel: autosave sederhana atau penyimpanan draft manual, status editorial, preview privat, penjadwalan, revisi, serta galeri gambar artikel.
3. Kategori, tag, topik, dan pengguna.
4. Media: upload gambar dengan tipe, ukuran, nama file aman, alt text, caption, dan penghapusan file yang terkoordinasi.
5. Foto Peristiwa dan Merah Putih TV sebagai entitas/modul terpisah setelah artikel stabil.
6. Audit log: siapa melakukan aksi penting dan kapan.

## Editor artikel praktis dan multi-gambar

Form artikel harus memungkinkan editor menyelesaikan berita tanpa perlu mengunggah gambar satu per satu atau membuka halaman media berulang kali.

### Alur editor

1. Editor menulis judul, ringkasan, kategori, isi, dan status artikel.
2. Pada bagian **Gambar artikel**, editor dapat memilih banyak file sekaligus melalui pemilih berkas atau menyeret beberapa berkas ke area unggah.
3. Sistem menampilkan thumbnail serta status upload setiap gambar. Upload yang gagal dapat diulang tanpa menghapus gambar lain yang sudah berhasil.
4. Editor dapat mengubah urutan gambar dengan drag-and-drop, memilih satu gambar sebagai gambar utama, lalu mengisi alt text dan caption langsung pada tiap gambar.
5. Saat menulis isi artikel, editor dapat memilih gambar yang sudah diunggah dari panel media artikel dan menyisipkannya pada posisi kursor. Gambar tidak harus diunggah ulang.
6. Tombol **Simpan draf**, **Pratinjau**, **Kirim review**, **Jadwalkan**, dan **Terbitkan** harus mempertahankan data artikel dan urutan gambar secara konsisten.

### Struktur data minimum

| Model/tabel | Kolom inti | Kegunaan |
|---|---|---|
| Media | id, disk, path, original_name, mime_type, size, width, height, alt_text, caption, uploaded_by | Satu berkas gambar yang tersimpan aman |
| ArticleMedia | article_id, media_id, role, sort_order, caption_override | Menghubungkan banyak gambar ke satu artikel |

Nilai `role`: `featured`, `gallery`, atau `inline`. Satu artikel maksimal memiliki satu `featured`; jumlah `gallery`/`inline` mengikuti batas konfigurasi yang realistis untuk hosting.

### Ketentuan teknis

- Form menggunakan input `multiple` dan upload bertahap via request yang tervalidasi; tidak memerlukan worker permanen.
- Simpan urutan pada `sort_order`, bukan berdasarkan nama file atau waktu upload.
- Simpan artikel dan relasi gambar di dalam transaksi database ketika editor menekan simpan/publikasi.
- File baru yang gagal dikaitkan ke artikel harus ditandai sebagai media sementara dan dapat dibersihkan oleh Scheduler setelah masa retensi yang aman.
- Saat gambar dihapus dari artikel, hapus hanya relasinya terlebih dahulu. Berkas fisik hanya boleh dihapus bila tidak lagi dipakai artikel/modul lain.
- Validasi server-side wajib memeriksa jumlah file, ukuran, ekstensi yang diizinkan, MIME type, dan dimensi gambar. Jangan mempercayai data dari browser.
- Jangan menyimpan gambar sebagai base64 di kolom artikel atau memasukkan banyak URL gambar secara manual dalam teks.

### Kriteria penerimaan multi-gambar

- Editor dapat mengunggah sedikitnya 10 gambar dalam satu tindakan pada lingkungan development, kemudian melihat thumbnail dan status tiap berkas.
- Editor dapat mengubah urutan dan menetapkan gambar utama tanpa upload ulang.
- Alt text/caption setiap gambar tersimpan dan dapat diedit kembali.
- Artikel yang dipublikasikan menampilkan gambar utama, galeri terurut, serta gambar inline sesuai posisi yang dipilih editor.
- Hapus gambar dari satu artikel tidak menghapus file yang masih dipakai artikel lain.

## Keamanan

- Form request validation untuk semua input.
- Rate limit pada login dan reset password.
- Password hashing Laravel, CSRF bawaan, session regeneration, dan otorisasi server-side.
- Simpan upload di storage lokal; jangan percaya nama/mime type dari browser saja.

## Kriteria penerimaan

- Writer tidak dapat menerbitkan artikel jika kebijakan hanya memberi Editor/Admin hak publikasi.
- URL admin tanpa autentikasi dialihkan aman ke login.
- Upload yang tidak valid ditolak dan file tidak tertinggal.
- Alur unggah banyak gambar, perubahan urutan, penyimpanan draf, dan publikasi artikel diuji tanpa kehilangan relasi media.
