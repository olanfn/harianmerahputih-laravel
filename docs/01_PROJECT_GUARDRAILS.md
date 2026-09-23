# Aturan Kerja Agent Codex

## Ruang kerja

- Buat proyek baru, misalnya `harianmerahputih-laravel`.
- Gunakan direktori, domain, database, dan konfigurasi environment khusus proyek ini.
- Jangan mengubah proyek atau website lain yang berada dalam akun hosting yang sama.

## Sebelum mengubah apa pun

1. Tampilkan lokasi proyek aktif dan periksa apakah ada `AGENTS.md`.
2. Periksa status Git bila repository sudah ada. Jangan menghapus perubahan pengguna.
3. Catat tujuan fase aktif dan kriteria selesai dari dokumen ini.
4. Bila ada konflik antara dokumen dan kondisi proyek, berhenti dan laporkan konflik; jangan menebak.

## Aturan implementasi

- Gunakan controller, request validation, policy/middleware, Eloquent, migration, dan Blade standar Laravel.
- Hindari dependensi yang membutuhkan service server terpisah.
- Jangan menambahkan fitur di luar fase yang sedang dikerjakan.
- Jangan menjalankan `migrate:fresh`, `db:wipe`, `git reset --hard`, atau penghapusan data tanpa persetujuan eksplisit.
- Jangan membuat akun admin default dengan kata sandi lemah pada produksi.
- Jika seed diperlukan untuk development, gunakan kredensial dummy dan dokumentasikan cara menggantinya.

## Definition of Done setiap fase

- Kode memenuhi ruang lingkup fase.
- Rute dan halaman terkait dapat dibuka tanpa error.
- Validasi dan otorisasi diuji untuk alur yang berubah.
- Migrasi dapat dijalankan pada database kosong tanpa menghapus data lain.
- Catatan perubahan dan hasil verifikasi disampaikan singkat.

## Format instruksi yang diberikan ke agent

Gunakan pola berikut:

```text
Kerjakan Fase <nomor> berdasarkan docs/<nama-dokumen>.
Proyek aktif: <path proyek Laravel baru>.
Ini adalah proyek baru. Jangan mengubah proyek atau website lain pada workspace maupun akun hosting.
Ikuti aturan di docs/01_PROJECT_GUARDRAILS.md.
Kerjakan hanya ruang lingkup fase ini, verifikasi hasilnya, lalu laporkan file yang diubah, cara uji, dan blocker bila ada.
```
