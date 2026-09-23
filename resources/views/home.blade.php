@extends('layouts.app')

@php
    $hasDemo = $headline?->is_demo || $latest->contains('is_demo', true) || $panelArticles->contains('is_demo', true);
    $robots = $hasDemo ? 'noindex' : null;
    $isPopularPage = request()->query('panel') === 'popular';
    $title = $isPopularPage ? 'Berita Terpopuler' : 'Beranda';
    $metaDescription = $isPopularPage ? 'Daftar berita terpopuler Harian Merah Putih berdasarkan jumlah pembaca.' : 'Berita terbaru Indonesia dari Harian Merah Putih.';
    $secondary = $latest->take(3);
    $tickerArticle = $headline ?: $latest->first();
@endphp

@section('content')
    <div class="ticker"><div class="site-container ticker__inner"><span class="ticker__label"><b aria-hidden="true">ϟ</b> TERKINI</span>@if ($tickerArticle)<a href="{{ route('news.show', $tickerArticle->slug) }}" class="ticker__headline">{{ $tickerArticle->title }}</a>@else<span class="ticker__headline">Ruang redaksi sedang menyiapkan berita perdana.</span>@endif<div class="ticker__controls"><span aria-hidden="true">‹</span><span aria-hidden="true">›</span><a href="{{ route('news.index') }}" class="ticker__all">Lihat Semua</a></div></div></div>
    @if ($isPopularPage)
        <div class="popular-page">
            <section class="site-container popular-page__intro">
                <div>
                    <span class="section-kicker">Paling banyak dibaca</span>
                    <h1>Berita<br><em>Terpopuler</em></h1>
                    <p>Daftar berita yang paling banyak dibaca oleh pembaca Harian Merah Putih.</p>
                </div>
                <div class="popular-page__stamp" aria-hidden="true"><strong>TOP</strong><span>NEWS<br>HMP</span></div>
            </section>
            <section class="site-container popular-page__content">
                @if ($panelArticles->isNotEmpty())
                    @php($featuredPopular = $panelArticles->first())
                    <article class="popular-feature">
                        <div class="popular-feature__rank">01</div>
                        <a href="{{ route('news.show', $featuredPopular->slug) }}" class="popular-feature__media focus-ring"><x-news-media :article="$featuredPopular" size="wide" /></a>
                        <div class="popular-feature__body">
                            <div class="story-meta"><a href="{{ route('news.category', $featuredPopular->category) }}" class="story-meta__category">{{ $featuredPopular->category->name }}</a><span>{{ number_format($featuredPopular->view_count) }} dibaca</span></div>
                            <h2><a href="{{ route('news.show', $featuredPopular->slug) }}">{{ $featuredPopular->title }}</a></h2>
                            <p>{{ $featuredPopular->excerpt }}</p>
                            <a href="{{ route('news.show', $featuredPopular->slug) }}" class="popular-feature__link focus-ring">Baca berita <x-icon name="arrow" size="15" /></a>
                        </div>
                    </article>
                    <div class="popular-page__grid">
                        <div class="popular-ranking">
                            <div class="popular-ranking__heading"><span class="section-kicker">Daftar peringkat</span><span>01—05</span></div>
                            @foreach ($panelArticles->skip(1) as $index => $article)
                                <article class="popular-ranking__item">
                                    <strong>{{ str_pad($index + 2, 2, '0', STR_PAD_LEFT) }}</strong>
                                    <a href="{{ route('news.show', $article->slug) }}" class="popular-ranking__media focus-ring"><x-news-media :article="$article" /></a>
                                    <div>
                                        <div class="story-meta"><a href="{{ route('news.category', $article->category) }}" class="story-meta__category">{{ $article->category->name }}</a></div>
                                        <h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3>
                                        <small>{{ number_format($article->view_count) }} dibaca · {{ $article->published_at?->locale('id')->translatedFormat('d M Y') }}</small>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <aside class="popular-page__aside">
                            <div class="popular-page__aside-card">
                                <span class="section-kicker">Pilihan pembaca</span>
                                <strong>Topik yang sedang ramai diperbincangkan.</strong>
                                <p>Ikuti berita yang paling menarik perhatian pembaca hari ini.</p>
                                <a href="{{ route('news.index') }}" class="focus-ring">Lihat semua berita <x-icon name="arrow" size="15" /></a>
                            </div>
                            <x-newsletter-card />
                        </aside>
                    </div>
                @else
                    <x-empty-state title="Belum ada berita terpopuler" message="Daftar berita populer akan muncul setelah artikel mendapatkan pembaca." />
                @endif
            </section>
        </div>
    @else
    <section class="site-container home-content">
        <div class="headline-grid">
            @if ($headline)
                <article class="hero-story"><x-news-media :article="$headline" size="story" /><div class="hero-story__body"><div class="story-meta"><a href="{{ route('news.category', $headline->category) }}" class="story-meta__category">{{ $headline->category->name }}</a><span>Headline</span>@if ($headline->is_demo)<span class="demo-tag">DEMONSTRASI</span>@endif</div><h1><a href="{{ route('news.show', $headline->slug) }}">{{ $headline->title }}</a></h1><p>{{ $headline->excerpt }}</p><div class="story-meta story-meta--time"><time datetime="{{ $headline->published_at?->toISOString() }}">{{ $headline->published_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time></div></div></article>
            @else
                <div class="hero-story"><div class="hero-story__body"><span class="section-kicker section-kicker--light">Beranda</span><h1>Berita perdana sedang disiapkan.</h1><p>Belum ada artikel terbit. Data demonstrasi dapat dibuat dengan seeder development.</p></div></div>
            @endif
            <div class="headline-middle">
                @forelse ($secondary as $article)
                    <article class="secondary-story"><x-news-media :article="$article" size="story" /><div class="secondary-story__body"><div class="story-meta"><span class="story-meta__category">{{ $article->category->name }}</span>@if ($article->is_demo)<span class="demo-tag">DEMO</span>@endif</div><h2><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h2></div></article>
                @empty
                    <div class="empty-state"><span class="section-kicker">Berita</span><h2>Belum tersedia</h2><p>Berita sekunder akan muncul setelah artikel terbit.</p></div>
                @endforelse
            </div>
            <aside class="popular-panel">
                <nav class="popular-panel__tabs" aria-label="Filter berita">
                    <a href="{{ route('home', ['panel' => 'popular']) }}" @class(['is-active' => $activePanel === 'popular'])>Terpopuler</a>
                    <a href="{{ route('home', ['panel' => 'latest']) }}" @class(['is-active' => $activePanel === 'latest'])>Terbaru</a>
                    <a href="{{ route('home', ['panel' => 'editor']) }}" @class(['is-active' => $activePanel === 'editor'])>Pilihan</a>
                </nav>
                @if ($panelArticles->isNotEmpty())
                    <ol class="popular-list">
                        @foreach ($panelArticles as $index => $article)
                            <li>
                                <b>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</b>
                                <a href="{{ route('news.show', $article->slug) }}" class="popular-list__media focus-ring"><x-news-media :article="$article" /></a>
                                <div class="popular-list__body">
                                    <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                                    <small>
                                        {{ $article->category->name }} · {{ $article->published_at?->locale('id')->translatedFormat('d M Y') }}
                                        @if ($activePanel === 'popular')
                                            · {{ number_format($article->view_count) }} dibaca
                                        @endif
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <div class="empty-state"><h2>Belum ada</h2><p>{{ $activePanel === 'editor' ? 'Editor belum memilih artikel untuk daftar ini.' : 'Belum ada artikel terbit pada daftar ini.' }}</p></div>
                @endif
                <a href="{{ route('news.index') }}" class="section-heading__link focus-ring">Lihat semua →</a>
            </aside>
        </div>
        <div class="content-columns">
            <section><x-section-heading title="Berita Terbaru" eyebrow="Update redaksi" :link="route('news.index')" />@if ($latest->isNotEmpty())<div class="latest-list latest-list--cards">@foreach ($latest as $article)<article class="latest-item"><a href="{{ route('news.show', $article->slug) }}" class="focus-ring"><x-news-media :article="$article" /></a><div><div class="story-meta"><a href="{{ route('news.category', $article->category) }}" class="story-meta__category">{{ $article->category->name }}</a>@if ($article->is_demo)<span class="demo-tag">DEMO</span>@endif</div><h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><div class="story-meta story-meta--muted"><time datetime="{{ $article->published_at?->toISOString() }}">{{ $article->published_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time></div></div></article>@endforeach</div>@else<div class="section-empty"><x-empty-state /></div>@endif</section>
            <aside class="sidebar-stack"><div class="promo-card"><span class="section-kicker">Harian Merah Putih</span><strong>Kabar yang dekat dengan Indonesia.</strong><p>Ruang promosi internal untuk tahap berikutnya.</p></div><x-newsletter-card /></aside>
        </div>
        <section class="mobile-category-grid" aria-label="Jelajahi kategori"><span class="section-kicker">Jelajahi Kategori</span><div>@foreach ($categories as $category)<a href="{{ route('news.category', $category) }}" class="focus-ring">{{ $category->name }}</a>@endforeach</div></section>
    </section>
    <section class="choice-section"><div class="site-container"><x-section-heading title="Berita Pilihan" eyebrow="Kurasi redaksi" :link="route('home', ['panel' => 'editor'])" />@if ($editorPicks->isNotEmpty())<div class="choice-grid">@foreach ($editorPicks->take(3) as $article)<article class="choice-card"><x-news-media :article="$article" size="wide" /><div class="choice-card__body"><div class="story-meta"><span class="story-meta__category">{{ $article->category->name }}</span></div><h3><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h3></div></article>@endforeach</div>@else<div class="section-empty"><x-empty-state title="Belum ada pilihan editor" message="Artikel yang dipilih editor akan tampil di bagian ini." /></div>@endif</div></section>
    @endif
@endsection
