@extends('layouts.app')

@php
    $title = $article->seo_title ?: $article->title;
    $metaDescription = $article->seo_description ?: $article->excerpt;
    $articlePage = true;
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
                <div class="article-page__tools">
                    <a href="{{ $backUrl }}" class="article-page__back focus-ring"><x-icon name="arrow" size="15" /><span>{{ $backLabel }}</span></a>
                    <img src="{{ asset('branding/logo-mobile.png') }}" alt="Harian Merah Putih" class="article-page__tools-brand">
                    <div class="article-page__actions">
                        @if ($isAdminPreview)<span class="article-page__preview-label">Pratinjau redaksi</span>@endif
                        <button type="button" class="focus-ring" data-article-font aria-label="Perbesar ukuran teks">A<sup>+</sup></button>
                        <div class="article-share">
                            <button type="button" class="focus-ring" data-article-share aria-label="Bagikan artikel" aria-expanded="false" aria-controls="article-share-menu">↗</button>
                            <div class="article-share-menu" id="article-share-menu" data-article-share-menu hidden>
                                <span>Bagikan ke</span>
                                <div class="article-share-menu__links">
                                    <a class="focus-ring article-social-action article-social-action--facebook" data-article-social="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke Facebook" title="Bagikan ke Facebook"><x-icon name="facebook" size="15" /></a>
                                    <a class="focus-ring article-social-action article-social-action--whatsapp" data-article-social="whatsapp" href="https://wa.me/?text={{ urlencode($article->title.' '.url()->current()) }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke WhatsApp" title="Bagikan ke WhatsApp"><x-icon name="whatsapp" size="15" /></a>
                                    <button type="button" class="focus-ring article-social-action article-social-action--instagram" data-article-social="instagram" aria-label="Bagikan ke Instagram" title="Salin tautan untuk Instagram"><x-icon name="instagram" size="15" /></button>
                                    <button type="button" class="focus-ring article-social-action article-social-action--tiktok" data-article-social="tiktok" aria-label="Bagikan ke TikTok" title="Salin tautan untuk TikTok"><x-icon name="tiktok" size="15" /></button>
                                </div>
                            </div>
                        </div>
                        <span class="article-action-status" data-article-action-status role="status" aria-live="polite"></span>
                    </div>
                </div>
                <header class="article-page__header">
                    <div class="article-page__category">
                        <span class="article-category-badge">{{ $article->category->name }}</span>
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
                            @foreach (preg_split('/(?:\R{2,}|\R(?=\s*["“]))/u', trim($segment['text'])) as $paragraph)
                                @if (trim($paragraph) !== '')
                                    <p @class(['article-body__paragraph', 'article-body__paragraph--quote' => \Illuminate\Support\Str::startsWith(trim($paragraph), ['"', '“'])])>{!! nl2br(e(trim($paragraph))) !!}</p>
                                @endif
                            @endforeach
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
