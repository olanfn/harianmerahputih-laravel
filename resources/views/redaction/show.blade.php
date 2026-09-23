@extends('layouts.app')
@section('title', $page->title.' · Harian Merah Putih')
@section('content')
<section class="site-container redaction-page">
    <div class="mobile-page-bar"><a href="{{ route('home') }}" aria-label="Kembali">‹</a><strong>{{ $page->title }}</strong><span></span></div>
    <header class="redaction-page__heading"><span class="section-kicker">Harian Merah Putih</span><h1>{{ $page->title }}</h1><p>{{ $page->summary }}</p></header>
    <div class="redaction-layout">
        <article class="redaction-content">
            {!! $page->content !!}
        </article>
        <aside class="redaction-sidebar">
            <section class="redaction-side-card"><div class="redaction-side-title"><h2>Topik Terkini</h2><span></span></div><div class="redaction-topics">@forelse($topics as $topic)<a href="{{ route('news.category', $topic) }}"><x-icon name="folder" />{{ $topic->name }}</a>@empty<p>Belum ada topik.</p>@endforelse</div></section>
            <section class="redaction-side-card"><div class="redaction-side-title"><h2>Viral</h2><span></span></div><div class="redaction-viral">@forelse($viralArticles as $article)<a href="{{ route('news.show', $article->slug) }}"><x-news-media :article="$article" /><span><strong>{{ $article->title }}</strong><small>{{ $article->published_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB</small></span></a>@empty<p class="redaction-sidebar-empty">Belum ada berita published pada kategori Viral.</p>@endforelse</div></section>
        </aside>
    </div>
</section>
@endsection
