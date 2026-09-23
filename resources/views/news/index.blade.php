@extends('layouts.app')

@php
    $robots = $articles->getCollection()->contains('is_demo', true) ? 'noindex' : null;
    $title = 'Indeks berita';
    $metaDescription = 'Indeks berita dan kategori Harian Merah Putih.';
@endphp

@section('content')
    <section class="site-container listing-page">
        <div class="listing-intro"><div><span class="section-kicker">Arsip Publik</span><h1>Indeks Berita</h1></div><p>Jelajahi seluruh berita yang telah dipublikasikan berdasarkan kategori dan waktu terbit.</p></div>
        <div class="content-columns" style="margin-top: 30px">
            <div>
                @if ($articles->isNotEmpty())
                    <div class="latest-list">@foreach ($articles as $article)<article class="latest-item"><a href="{{ route('news.show', $article->slug) }}" class="focus-ring"><x-news-media :article="$article" /></a><div><div class="story-meta"><a href="{{ route('news.category', $article->category) }}" class="story-meta__category">{{ $article->category->name }}</a>@if ($article->is_demo)<span class="demo-tag">DEMO</span>@endif</div><h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><div class="story-meta" style="color: var(--muted); margin-top: 10px"><time datetime="{{ $article->published_at?->toISOString() }}">{{ $article->published_at?->locale('id')->translatedFormat('d F Y') }}</time></div></div></article>@endforeach</div>
                    <div class="pagination-wrap">{{ $articles->links() }}</div>
                @else
                    <x-empty-state title="Indeks belum berisi artikel" />
                @endif
            </div>
            <aside class="sidebar-stack"><div class="promo-card"><span class="section-kicker">Kategori Aktif</span><div class="footer-links" style="margin-top: 16px">@foreach ($categories as $category)<a href="{{ route('news.category', $category) }}" style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--line); padding-bottom: 8px; color: var(--ink); font-weight: 800"><span>{{ $category->name }}</span><small>{{ $category->articles_count }}</small></a>@endforeach</div></div><x-newsletter-card /></aside>
        </div>
    </section>
@endsection