@extends('layouts.app')

@php
    $title = $article->seo_title ?: $article->title;
    $metaDescription = $article->seo_description ?: $article->excerpt;
    $robots = $article->is_demo ? 'noindex' : null;
    $isAdminPreview = request()->routeIs('admin.articles.preview');
    $backUrl = $isAdminPreview
        ? route('admin.articles.edit', $article)
        : (url()->previous() !== url()->current() ? url()->previous() : route('home'));
    $backLabel = $isAdminPreview ? 'Kembali ke editor' : 'Kembali';
    $topicCategories = collect($categories ?? [])->take(5);
@endphp

@section('content')
    <article class="article-page">
        <div class="article-page__ticker" aria-label="Berita pilihan">
            <span class="article-page__ticker-label">Trending</span>
            <a href="{{ route('news.category', $article->category) }}">{{ $article->title }}</a>
            <span class="article-page__ticker-arrow" aria-hidden="true">→</span>
        </div>

        <div class="article-page__layout">
            <div class="article-page__main">
                <header class="article-page__header">
                    <nav class="article-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">›</span><a href="{{ route('news.category', $article->category) }}">{{ $article->category->name }}</a><span aria-hidden="true">›</span><span>{{ $article->title }}</span></nav>
                    <div class="article-page__tools">
                        <a href="{{ $backUrl }}" class="article-page__back focus-ring"><x-icon name="arrow" size="15" /><span>{{ $backLabel }}</span></a>
                        <img src="{{ asset('branding/logo-mobile.png') }}" alt="Harian Merah Putih" class="article-page__tools-brand">
                        <div class="article-page__actions">
                            @if ($isAdminPreview)<span class="article-page__preview-label">Pratinjau redaksi</span>@endif
                            <button type="button" class="focus-ring" aria-label="Atur ukuran teks">A<sup>+</sup></button>
                            <button type="button" class="focus-ring" aria-label="Bagikan artikel">↗</button>
                        </div>
                    </div>
                    <div class="article-page__category">
                        <a href="{{ route('news.category', $article->category) }}" class="story-meta__category">{{ $article->category->name }}</a>
                        @if ($article->is_demo)<span class="demo-tag">DEMONSTRASI</span>@endif
                    </div>
                    <h1>{{ $article->title }}</h1>
                    <p class="article-page__excerpt">{{ $article->excerpt }}</p>
                    <div class="article-page__byline">
                        <span><x-icon name="users" size="14" /> Oleh <strong>{{ $article->author?->name ?: 'Redaksi' }}</strong></span>
                        <span><x-icon name="folder" size="14" /> {{ $article->category->name }}</span>
                        <time datetime="{{ $article->published_at?->toISOString() }}"><x-icon name="clock" size="14" /> {{ $article->published_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time>
                    </div>
                </header>

                @if ($article->is_demo)<div class="demo-notice">DEMONSTRASI: artikel ini adalah data development, bukan berita nyata.</div>@endif

                @php($featured = $article->mediaLinks->firstWhere('role', 'featured'))
                @if ($featured)
                    <figure class="article-page__visual"><img class="article-main-image" src="{{ $featured->media->url() }}" alt="{{ $featured->media->alt_text ?: $article->title }}"><figcaption>{{ $featured->caption_override ?: ($featured->media->caption ?: 'Visual artikel Harian Merah Putih.') }}</figcaption></figure>
                @else
                    <figure class="article-page__visual"><x-news-media :article="$article" size="hero" /><figcaption>Visual artikel Harian Merah Putih.</figcaption></figure>
                @endif

                <div class="article-page__body">
                    @foreach ($contentSegments ?? [['type' => 'text', 'text' => $article->body]] as $segment)
                        @if ($segment['type'] === 'media')
                            <figure class="article-inline-media"><img src="{{ $segment['media']->url() }}" alt="{{ $segment['media']->alt_text ?: $article->title }}"><figcaption>{{ $segment['caption'] }}</figcaption></figure>
                        @else
                            {!! nl2br(e($segment['text'])) !!}
                        @endif
                    @endforeach
                </div>

                @php($gallery = $article->mediaLinks->where('role', 'gallery')->sortBy('sort_order'))
                @if ($gallery->isNotEmpty())
                    <section class="article-gallery" aria-label="Galeri artikel">
                        @foreach ($gallery as $link)
                            <figure><img src="{{ $link->media->url() }}" alt="{{ $link->media->alt_text ?: $article->title }}"><figcaption>{{ $link->caption_override ?: ($link->media->caption ?: 'Visual artikel Harian Merah Putih.') }}</figcaption></figure>
                        @endforeach
                    </section>
                @endif

                <div class="article-page__tags">
                    <span>Topik:</span>
                    @foreach ($article->tags as $tag)<a href="{{ route('news.tag', $tag) }}" class="focus-ring">#{{ $tag->name }}</a>@endforeach
                </div>
            </div>

            <aside class="article-page__sidebar">
                <section class="article-sidebar__panel">
                    <div class="article-sidebar__heading"><span>Eksplorasi</span><h2>Topik Terkini</h2></div>
                    <ul class="article-sidebar__topics">
                        @foreach ($topicCategories as $topic)
                            <li><a href="{{ route('news.category', $topic) }}"><x-icon name="folder" size="14" />{{ data_get($topic, 'name') }}<span aria-hidden="true">→</span></a></li>
                        @endforeach
                    </ul>
                </section>

                @if ($related->isNotEmpty())
                    <section class="article-sidebar__panel article-sidebar__related">
                        <div class="article-sidebar__heading"><span>Dari kategori yang sama</span><h2>Artikel Terkait</h2></div>
                        <div class="article-sidebar__list">
                            @foreach ($related as $relatedArticle)
                                <article class="article-sidebar__item">
                                    <a href="{{ route('news.show', $relatedArticle->slug) }}" class="article-sidebar__item-media focus-ring"><x-news-media :article="$relatedArticle" size="card" /></a>
                                    <div>
                                        <a href="{{ route('news.show', $relatedArticle->slug) }}"><h3>{{ $relatedArticle->title }}</h3></a>
                                        <time datetime="{{ $relatedArticle->published_at?->toISOString() }}">{{ $relatedArticle->published_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <div class="article-sidebar__note">
                    <span class="section-kicker">Harian Merah Putih</span>
                    <strong>Kabar hari ini, dengan konteks yang berarti.</strong>
                    <a href="{{ route('news.index') }}">Jelajahi semua berita <x-icon name="arrow" size="14" /></a>
                </div>
            </aside>
        </div>
    </article>
@endsection
