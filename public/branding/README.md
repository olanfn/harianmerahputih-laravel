# Brand Assets

Folder ini berisi aset branding resmi Harian Merah Putih dan menjadi sumber
kebenaran untuk logo serta ikon aplikasi.

## Aset resmi

| File | Penggunaan |
| --- | --- |
| `logo-primary.png` | Master logo PNG terbaru untuk header desktop, admin, login, dan metadata brand |
| `logo-mobile.png` | Master logo PNG untuk header mobile dan detail artikel |
| `logo-footer.png` | Varian logo footer dengan warna merah dipertahankan dan tagline terang untuk latar gelap |
| `logo-mark.png` | Varian mark untuk fallback compact dan ruang sempit |
| `favicon_merahputih.ico` | Favicon utama browser |
| `favicon.ico` | Favicon fallback browser |
| `favicon-16x16.png` | Favicon PNG ukuran 16x16 |
| `favicon-32x32.png` | Favicon PNG ukuran 32x32 |
| `apple-touch-icon.png` | Ikon perangkat Apple |

Template Laravel menggunakan `asset('branding/...')` untuk seluruh referensi
logo dan ikon. Footer tetap memakai varian terang agar terbaca di atas latar
gelap.

## Aturan penggunaan

- Gunakan hanya aset resmi yang tercantum di atas.
- Jangan menggambar ulang, mengganti warna, memotong, atau meregangkan aset.
- Pertahankan aspect ratio dan gunakan dimensi eksplisit saat menampilkan logo.
- Jangan membuat text fallback atau varian branding baru tanpa persetujuan owner.
- Jangan meregenerasi atau mengganti aset tanpa instruksi eksplisit owner.
