# Spesifikasi Visual Frontend — Harian Merah Putih

## Tujuan

Frontend publik harus mengikuti referensi mockup yang disetujui: portal berita Indonesia modern, informatif, padat tetapi tetap mudah dibaca. Identitas visual memakai merah, putih, dan hitam/abu gelap dengan logo Harian Merah Putih yang tersedia. Ini adalah spesifikasi tampilan; tidak mengubah aturan publikasi artikel, model data, rute, CMS, atau otorisasi.

## Aturan umum

- Gunakan layout publik khusus, terpisah dari layout admin/auth.
- Pertahankan seluruh URL, named route, controller, query, pagination, dan perilaku akses publik yang sudah ada.
- Gunakan data artikel/kategori/tag yang sudah tersedia. Jangan membuat berita fiktif baru hanya untuk estetika.
- Gunakan `<a href>` dan formulir pencarian biasa; tidak membutuhkan SPA, paket baru, CDN, atau JavaScript besar.
- Logo harus menggunakan aset brand resmi yang tersedia; jangan menggambar ulang logo dengan teks biasa.
- Hindari gradien, tekstur, dan bayangan dekoratif berlebihan. Gaya harus tajam, editorial, dan profesional.

## Aset branding resmi

Paket `branding.zip` berisi aset yang wajib ditempatkan di `public/branding/` proyek Laravel sebelum frontend dirapikan.

| Berkas | Pemakaian |
|---|---|
| `logo-primary.png` | Masthead desktop dan footer desktop |
| `logo-mobile.png` | Header mobile dan header detail artikel mobile |
| `logo-mark.png` | Logo ringkas/fallback area sempit |
| `favicon.ico` | Favicon fallback browser |
| `favicon-16x16.png`, `favicon-32x32.png` | Favicon ukuran spesifik |
| `apple-touch-icon.png` | Ikon layar utama perangkat Apple |

Gunakan helper `asset('branding/...')` dan deklarasikan seluruh favicon yang relevan pada layout publik. Jaga rasio asli gambar, gunakan `width` dan `height` bila dimensi telah diketahui, serta jangan mengubah warna/proporsi aset melalui CSS.

## Desktop — struktur beranda

1. **Info bar tipis**: tanggal, cuaca/lokasi opsional, ikon sosial, dan kotak pencarian.
2. **Masthead putih**: logo di kiri, navigasi kategori horizontal, dengan item aktif merah dan garis bawah merah.
3. **Ticker merah “TERKINI”**: label dengan ikon petir, satu judul berjalan/statis, panah navigasi, dan tautan “Lihat Semua”.
4. **Grid headline tiga kolom**:
   - kiri ±55%: hero besar, gambar dengan overlay gelap di bawah, label kategori merah, waktu, judul besar, ringkasan, kontrol carousel;
   - tengah ±27%: tiga berita sekunder bertumpuk, masing-masing berupa gambar dengan overlay gelap dan label kategori;
   - kanan ±18%: kartu putih “Terpopuler / Terbaru / Pilihan Editor”, daftar bernomor 01–05, serta tombol lihat semua.
5. **Berita Terbaru**: lima kartu horizontal berisi thumbnail, label kategori, waktu, judul, dan ringkasan singkat. Di desktop dapat memakai area konten utama dengan sidebar kanan berisi banner promosi, newsletter, dan kutipan redaksi.
6. **Berita Pilihan**: tiga kartu gambar lebar.
7. **Footer gelap**: logo, kolom tautan, kategori, sosial, legal, dan copyright.

Gunakan container lebar sekitar 1200–1280px, jarak antarkomponen konsisten, border tipis abu-abu, radius kartu kecil (sekitar 8px), dan tipografi sans-serif tegas dengan hirarki jelas.

## Mobile — beranda

Lebar acuan 360–430px. Urutan harus:

1. Header putih: tombol hamburger, logo mobile, pencarian.
2. Ticker “TERKINI” horizontal.
3. Hero besar dengan gambar, overlay gelap, label kategori, judul, waktu, dan ringkasan.
4. “Berita Terbaru” sebagai daftar vertikal ringkas: thumbnail kiri, judul dan waktu kanan, pemisah tipis.
5. Kartu kategori dalam grid 4 kolom menggunakan ikon/warna sederhana.
6. Banner promosi, lalu footer ringkas.
7. Bottom navigation tetap: Beranda, Kategori, Terpopuler, Pencarian. Item aktif merah.

Tidak boleh ada horizontal scrolling selain navigasi kategori/ticker yang memang disengaja. Header dan bottom navigation tidak boleh menutupi isi.

## Mobile — halaman kategori

- Header dengan tombol kembali, judul kategori di tengah, pencarian di kanan.
- Tab: Terbaru, Populer, Pilihan Editor.
- Artikel utama berupa kartu gambar besar di atas.
- Artikel lain berupa daftar vertikal thumbnail-kiri dengan garis pemisah.
- Tombol “Muat Berita Lainnya” dan kartu newsletter di bagian bawah.
- Bottom navigation tetap.

## Mobile — detail artikel

- Header minimal: tombol kembali, logo kecil, pengaturan ukuran teks dan bagikan.
- Label kategori merah, judul besar, ringkasan, penulis, waktu terbit/diperbarui, gambar utama, caption, isi artikel, kutipan, artikel terkait, tombol berbagi, dan tag.
- Lebar teks nyaman dibaca; gunakan ukuran tubuh minimal 16px dan line-height longgar.
- Gambar artikel tidak boleh terdistorsi; gunakan `object-fit: cover` dengan rasio yang sesuai.

## Responsif dan aksesibilitas

- Desktop: grid headline dapat runtuh menjadi dua kolom di tablet, lalu satu kolom di mobile.
- Kontrol hamburger, pencarian, tab, dan bottom navigation wajib memiliki label aksesibel dan focus state.
- Kontras teks di atas gambar harus cukup; overlay digunakan demi keterbacaan, bukan dekorasi.
- Uji minimal pada 375px, 768px, 1024px, dan 1440px tanpa clipping, overflow, atau teks bertumpuk.

## Kriteria penerimaan

- Beranda desktop dan mobile secara komposisi jelas menyerupai referensi: hierarchy header → ticker → hero → list/kartu berita → footer.
- Logo desktop, mobile, mark, dan favicon memakai aset branding resmi yang tepat.
- Halaman kategori dan detail artikel memiliki desain mobile khusus, bukan sekadar desktop yang dipersempit.
- Semua halaman publik dan status empty/404 tetap berfungsi.
- Tidak ada perubahan pada migration, model, controller, query, rute, aturan artikel published, atau data seed.
- Build aset dan test yang sudah ada tetap lulus.
