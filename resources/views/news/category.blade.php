@extends('layouts.app')

@php
    $robots = request()->query('tab') ? 'noindex' : ($articles->getCollection()->contains('is_demo', true) ? 'noindex' : null);
    $title = $category->name;
    $metaDescription = $category->description ?: 'Berita terbaru dari kategori '.$category->name.'.';
    $featured = $articles->first();
@endphp

@section('content')
    <section class="site-container listing-page">
        <div class="mobile-page-bar"><a href="{{ route('home') }}" aria-label="Kembali ke beranda">←</a><strong>{{ $category->name }}</strong><a href="{{ route('news.search') }}" aria-label="Cari berita">⌕</a></div>
        <div class="listing-intro"><div><span class="section-kicker">Kategori Berita</span><h1>{{ $category->name }}</h1></div><p>{{ $category->description ?: 'Kumpulan berita terbaru dari kategori '.$category->name.'.' }}</p></div>
        <nav class="category-tabs" aria-label="Filter kategori"><a href="{{ route('news.category', ['category' => $category, 'tab' => 'latest']) }}" @class(['is-active' => $activeTab === 'latest'])>Terbaru</a><a href="{{ route('news.category', ['category' => $category, 'tab' => 'popular']) }}" @class(['is-active' => $activeTab === 'popular'])>Populer</a><a href="{{ route('news.category', ['category' => $category, 'tab' => 'editor']) }}" @class(['is-active' => $activeTab === 'editor'])>Pilihan Editor</a></nav>
        @if ($featured)
            <div class="category-feature"><a href="{{ route('news.show', $featured->slug) }}" class="focus-ring"><x-news-media :article="$featured" size="hero" /></a><div class="category-feature__copy"><div class="story-meta"><a href="{{ route('news.category', $category) }}" class="story-meta__category">{{ $category->name }}</a>@if ($featured->is_demo)<span class="demo-tag">DEMO</span>@endif</div><h2><a href="{{ route('news.show', $featured->slug) }}">{{ $featured->title }}</a></h2><p>{{ $featured->excerpt }}</p><div class="story-meta" style="color: var(--muted); margin-top: 18px"><time datetime="{{ $featured->published_at?->toISOString() }}">{{ $featured->published_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time></div></div></div>
            <div class="category-list">@foreach ($articles->skip(1) as $article)<article class="latest-item"><a href="{{ route('news.show', $article->slug) }}" class="focus-ring"><x-news-media :article="$article" /></a><div><div class="story-meta"><span class="story-meta__category">{{ $article->category->name }}</span>@if ($article->is_demo)<span class="demo-tag">DEMO</span>@endif</div><h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><div class="story-meta" style="color: var(--muted); margin-top: 10px"><time datetime="{{ $article->published_at?->toISOString() }}">{{ $article->published_at?->locale('id')->translatedFormat('d F Y') }}</time></div></div></article>@endforeach</div>
            <div class="pagination-wrap">{{ $articles->links() }}</div>
        @else
            <div style="margin-top: 26px"><x-empty-state title="Belum ada artikel di kategori ini" message="Kategori ini belum memiliki artikel terbit yang dapat dibaca." /></div>
        @endif
        <div style="max-width: 480px; margin-top: 35px"><x-newsletter-card /></div>
    </section>
@endsection
