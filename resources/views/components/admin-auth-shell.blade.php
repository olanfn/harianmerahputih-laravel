@props(['title'])
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Harian Merah Putih</title>
    <link rel="icon" href="{{ asset('branding/favicon_merahputih.ico') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login">
    <main class="admin-auth-shell">
        <aside class="admin-auth-brand" aria-label="Portal redaksi Harian Merah Putih">
            <a href="{{ route('home') }}" class="admin-auth-brand__logo"><img src="{{ asset('branding/logo-primary.png') }}" alt="Harian Merah Putih"></a>
            <div class="admin-auth-brand__content">
                <span>Portal Editorial</span>
                <h2>Berita terpercaya dimulai dari ruang redaksi yang tertata.</h2>
                <p>Kelola artikel, gambar, dan proses publikasi dalam satu panel privat.</p>
                <ul>
                    <li><x-icon name="edit" /> Alur editorial berbasis peran</li>
                    <li><x-icon name="image" /> Media dan featured image terintegrasi</li>
                    <li><x-icon name="shield" /> Akses privat dan tercatat</li>
                </ul>
            </div>
            <small>Harian Merah Putih · Kebanggaan Indonesia</small>
        </aside>
        <section class="admin-auth-panel">
            <div class="admin-auth-card">{{ $slot }}</div>
        </section>
    </main>
</body>
</html>
