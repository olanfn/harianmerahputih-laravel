@extends('layouts.app')

@php
    $title = 'Halaman tidak ditemukan';
    $metaDescription = 'Halaman yang Anda cari tidak tersedia atau sudah dipindahkan.';
@endphp

@section('content')
    <section class="site-container error-page">
        <div class="error-page__card">
            <div class="error-page__code" aria-hidden="true">404</div>
            <span class="section-kicker">Harian Merah Putih</span>
            <h1>Halaman ini tidak tersedia.</h1>
            <p>Alamat yang Anda buka mungkin salah, sudah dipindahkan, atau belum tersedia untuk publik. Silakan kembali ke beranda atau lanjutkan membaca dari indeks berita.</p>
            <div class="error-page__actions">
                <a href="{{ route('home') }}">Kembali ke beranda</a>
                <a href="{{ route('news.index') }}">Buka indeks berita</a>
            </div>
        </div>
    </section>
@endsection
