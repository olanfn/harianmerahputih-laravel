@extends('admin.layout')
@section('title', 'Dashboard')
@section('section', 'Dashboard')
@section('content')
<header class="admin-page-head">
    <div><span class="admin-eyebrow">Ringkasan hari ini</span><h1>Selamat bekerja, {{ Str::before(auth()->user()->name, ' ') }}</h1><p>Pantau alur editorial dan lanjutkan pekerjaan redaksi Anda.</p></div>
    <a class="admin-button" href="{{ route('admin.articles.create') }}"><x-icon name="plus" />Tulis artikel</a>
</header>
<div class="admin-stats">
    <article><span class="admin-stat-icon admin-stat-icon--red"><x-icon name="article" /></span><div><b>{{ $articleCount }}</b><span>Total artikel</span><small>Seluruh konten yang dapat Anda kelola</small></div></article>
    <article><span class="admin-stat-icon admin-stat-icon--amber"><x-icon name="edit" /></span><div><b>{{ $draftCount }}</b><span>Dalam draf</span><small>Konten yang belum dikirim untuk review</small></div></article>
    <article><span class="admin-stat-icon admin-stat-icon--blue"><x-icon name="clock" /></span><div><b>{{ $reviewCount }}</b><span>Menunggu review</span><small>Konten siap diperiksa editor</small></div></article>
</div>
<section class="admin-welcome-card"><div><span class="admin-eyebrow">Alur editorial</span><h2>Dari ide hingga terbit, dalam satu ruang kerja.</h2><p>Gunakan menu Artikel untuk menulis berita, mengatur gambar utama, menyisipkan media, lalu mengirimkannya kepada editor.</p><a href="{{ route('admin.articles.index') }}">Kelola semua artikel <x-icon name="arrow" /></a></div><x-icon name="article" size="88" /></section>
@endsection
