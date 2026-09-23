# Urutan Menjalankan Agent Codex

Simpan semua file dokumentasi ini di folder `docs/` proyek Laravel baru. Jalankan agent dengan urutan berikut; jangan melompati fase.

1. `00_PROJECT_PLAN.md` dan `01_PROJECT_GUARDRAILS.md` — minta agent menyiapkan proyek baru dengan batas kerja yang jelas.
2. `02_FOUNDATION.md` — buat proyek Laravel dan fondasi antarmuka.
3. `03_PUBLIC_NEWS.md` — bangun MVP publik beserta data development.
4. `09_FRONTEND_VISUAL_SPEC.md` — sempurnakan visual frontend publik setelah data/rute MVP stabil.
5. `04_CMS_EDITORIAL.md` — hanya setelah MVP publik stabil.
6. `05_MEDIA_SEO.md` — aktivasi media dan SEO setelah CMS siap.
7. `06_HOSTINGER_DEPLOYMENT.md` — lakukan saat siap deploy.
8. `07_QA_AND_RELEASE.md` — jalankan sebelum rilis publik.

## Prompt awal untuk Codex

```text
Saya sedang membangun ulang harianmerahputih.id sebagai Laravel 12 untuk Hostinger Premium.
Baca seluruh folder docs terlebih dahulu. Ikuti docs/01_PROJECT_GUARDRAILS.md tanpa pengecualian.
Ini proyek baru. Jangan menyentuh website/proyek lain pada workspace atau akun hosting.
Mulai dari fase pertama yang belum selesai, jangan mengerjakan fase lain, dan jangan deploy tanpa instruksi saya.
```

## Aturan kelanjutan

Setelah agent menyelesaikan satu fase, simpan laporan hasilnya ke `docs/CHANGELOG.md` atau kirimkan ke pemilik proyek. Lanjut hanya setelah hasil dan blocker diperiksa.
