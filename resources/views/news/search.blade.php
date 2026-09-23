@extends('layouts.app')

@php
    $robots = 'noindex';
    $title = $term ? 'Pencarian: '.$term : 'Cari berita';
    $metaDescription = 'Pencarian berita Harian Merah Putih.';
@endphp

@section('content')
    <section class="site-container listing-page search-page">
        <div class="search-hero">
            <div class="search-hero__intro">
                <span class="section-kicker">Pusat pencarian</span>
                <h1>{{ $term ? 'Hasil Cari' : 'Cari Berita' }}</h1>
                <p>Temukan berita berdasarkan judul, ringkasan, atau isi artikel yang sudah dipublikasikan.</p>
                <a href="{{ route('news.index') }}" class="search-hero__index focus-ring">Jelajahi indeks berita <x-icon name="arrow" size="15" /></a>
            </div>
            <div class="search-hero__panel">
                <div class="search-hero__panel-head"><span>Cari cepat</span><span class="search-hero__panel-mark" aria-hidden="true">01</span></div>
                <form action="{{ route('news.search') }}" method="get" class="search-form focus-ring">
                    <label for="search-page">Kata kunci pencarian</label>
                    <div>
                        <x-icon name="search" size="18" />
                        <input id="search-page" name="q" value="{{ $term }}" maxlength="100" placeholder="Ketik kata kunci...">
                        <button type="submit"><span>Cari</span><x-icon name="arrow" size="15" /></button>
                    </div>
                </form>
                <p class="search-hero__hint">Gunakan kata kunci spesifik untuk hasil yang lebih relevan.</p>
            </div>
        </div>
        @if ($term)
            <div class="search-result-note">
                <span class="search-result-note__label">Hasil pencarian</span>
                <strong>“{{ $term }}”</strong>
                <span>{{ $articles->total() }} artikel ditemukan</span>
            </div>
        @endif
        @if ($articles->isNotEmpty())
            <div class="listing-grid">@foreach ($articles as $article)<x-article-card :article="$article" />@endforeach</div>
            <div class="pagination-wrap">{{ $articles->links() }}</div>
        @else
            <div class="search-empty"><x-empty-state title="Tidak ada hasil pencarian" message="Coba gunakan kata kunci lain atau jelajahi indeks berita." /></div>
        @endif
    </section>
@endsection