# Brand Assets

Folder ini berisi aset branding resmi Harian Merah Putih dan menjadi sumber
kebenaran untuk logo serta ikon aplikasi.

## Aset resmi

| File | Penggunaan |
| --- | --- |
| `logo-primary.png` | Logo utama untuk header desktop dan footer |
| `logo-mobile.png` | Logo ringkas untuk header mobile |
| `logo-mark.png` | Brand mark untuk penggunaan compact |
| `favicon.ico` | Favicon fallback browser |
| `favicon-16x16.png` | Favicon PNG ukuran 16x16 |
| `favicon-32x32.png` | Favicon PNG ukuran 32x32 |
| `apple-touch-icon.png` | Ikon perangkat Apple |

Varian logo dipetakan melalui `src/components/brand/logo.tsx`. Favicon dan
Apple touch icon didaftarkan melalui metadata di `src/app/layout.tsx`.

## Aturan penggunaan

- Gunakan hanya aset resmi yang tercantum di atas.
- Jangan menggambar ulang, mengganti warna, memotong, atau meregangkan aset.
- Pertahankan aspect ratio dan gunakan dimensi eksplisit saat menampilkan logo.
- Jangan membuat text fallback atau varian branding baru tanpa persetujuan owner.
- Jangan meregenerasi atau mengganti aset tanpa instruksi eksplisit owner.
