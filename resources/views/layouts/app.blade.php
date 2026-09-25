<!DOCTYPE html>
<html lang="id">
    @php
        $pageTitle = $title ?? config('news.brand.name');
        $pageDescription = $metaDescription ?? 'Portal berita Harian Merah Putih, kebanggaan Indonesia.';
        $canonicalUrl = $canonical ?? url()->current();
        if (! str_starts_with($canonicalUrl, rtrim(config('app.url'), '/'))) $canonicalUrl = url()->current();
        $articleImage = isset($article) && $article->featuredMedia?->media ? $article->featuredMedia->media->absoluteUrl() : null;
        $socialImage = $ogImage ?? $articleImage;
        $isPublishedArticle = isset($article) && $article->status === 'published' && $article->published_at?->isPast() && ! $article->is_demo;
        $siteSettings = \App\Models\Setting::values(['social.facebook', 'social.x', 'social.instagram', 'social.youtube', 'social.tiktok', 'social.rss', 'contact.office_phone', 'contact.whatsapp']);
        $officePhone = trim($siteSettings['contact.office_phone'] ?? '');
        $whatsapp = preg_replace('/[^0-9]/', '', $siteSettings['contact.whatsapp'] ?? '');
        $socialLinks = [['facebook', 'Facebook'], ['x', 'X'], ['instagram', 'Instagram'], ['youtube', 'YouTube'], ['tiktok', 'TikTok']];
        $rssUrl = $siteSettings['social.rss'] ?? route('distribution.rss');
        $structuredData = $isPublishedArticle ? [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $pageTitle,
            'description' => $pageDescription,
            'url' => $canonicalUrl,
            'datePublished' => $article->published_at->toAtomString(),
            'dateModified' => $article->updated_at->toAtomString(),
            'author' => ['@type' => 'Person', 'name' => $article->author?->name ?: 'Redaksi Harian Merah Putih'],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
            'image' => $articleImage ? [$articleImage] : [],
            'publisher' => ['@type' => 'Organization', 'name' => config('news.brand.name'), 'logo' => ['@type' => 'ImageObject', 'url' => asset('branding/logo-primary.png')]],
        ] : null;
        $siteStructuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                ['@type' => 'Organization', 'name' => config('news.brand.name'), 'url' => rtrim(config('app.url'), '/'), 'logo' => asset('branding/logo-primary.png')],
                ['@type' => 'WebSite', 'name' => config('news.brand.name'), 'url' => rtrim(config('app.url'), '/')],
            ],
        ];
        $breadcrumbStructuredData = isset($article) ? [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $article->category->name, 'item' => route('news.category', $article->category)],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $article->title, 'item' => $canonicalUrl],
            ],
        ] : null;
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta property="og:type" content="{{ isset($article) ? 'article' : 'website' }}">
        <meta property="og:site_name" content="{{ config('news.brand.name') }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        @if ($socialImage)
            <meta property="og:image" content="{{ $socialImage }}">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:image" content="{{ $socialImage }}">
        @else
            <meta name="twitter:card" content="summary">
        @endif
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDescription }}">
        @if (($robots ?? null) === 'noindex')
            <meta name="robots" content="noindex, follow">
        @else
            <meta name="robots" content="index, follow">
        @endif
        <title>{{ $pageTitle }} | {{ config('news.brand.tagline') }}</title>
        @if ($structuredData)
            <script type="application/ld+json">@json($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
        @endif
        @if ($breadcrumbStructuredData)
            <script type="application/ld+json">@json($breadcrumbStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
        @endif
        @if (!isset($article))
            <script type="application/ld+json">@json($siteStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
        @endif
        <link rel="icon" href="{{ asset('branding/favicon_merahputih.ico') }}?v=2" type="image/x-icon" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('branding/apple-touch-icon.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-shell antialiased">
        <a href="#main-content" class="skip-link focus-ring">Lewati ke konten utama</a>
        <header x-data="{ menuOpen: false }" class="site-header">
            <div class="info-bar">
                <div class="site-container info-bar__inner">
                    <div class="info-bar__left"><span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span><span class="info-separator">|</span><span>Jakarta, Indonesia</span></div>
                    <div class="info-bar__right"><span class="social-links" aria-label="Media sosial">@foreach($socialLinks as [$key, $label]) @if(!empty($siteSettings["social.$key"]))<a href="{{ $siteSettings["social.$key"] }}" title="{{ $label }}" target="_blank" rel="noopener noreferrer"><x-icon name="{{ $key }}" size="15" /></a>@endif @endforeach<a href="{{ $rssUrl }}" title="RSS"><x-icon name="rss" size="14" /></a>@if($officePhone)<a href="tel:{{ preg_replace("/[^0-9+]/", "", $officePhone) }}" title="Telepon kantor"><x-icon name="phone" size="15" /></a>@endif @if($whatsapp)<a href="https://wa.me/{{ $whatsapp }}" title="WhatsApp"><x-icon name="whatsapp" size="15" /></a>@endif</span><form action="{{ route('news.search') }}" method="get" class="header-search"><label class="sr-only" for="header-search">Cari berita</label><x-icon name="search" size="14" /><input id="header-search" name="q" maxlength="100" placeholder="Cari berita, topik, atau kata kunci..." value="{{ request('q') }}"></form></div>
                </div>
            </div>
            <div class="masthead">
                <div class="site-container masthead__inner">
                    <button type="button" @click="menuOpen = !menuOpen" class="mobile-menu-button focus-ring" :aria-expanded="menuOpen.toString()" :aria-label="menuOpen ? 'Tutup navigasi utama' : 'Buka navigasi utama'">
                        <span class="mobile-action-button__icon" aria-hidden="true">
                            <span x-show="!menuOpen" x-cloak><x-icon name="menu" size="20" /></span>
                            <span x-show="menuOpen"><x-icon name="close" size="20" /></span>
                        </span>
                    </button>
                    <a href="{{ route('home') }}" class="brand-lockup focus-ring" aria-label="Harian Merah Putih, kembali ke beranda">
                        <img src="{{ asset('branding/logo-primary.png') }}" alt="Harian Merah Putih" class="brand-lockup__primary">
                        <img src="{{ asset('branding/logo-mobile.png') }}" alt="Harian Merah Putih" class="brand-lockup__mobile">
                    </a>
                    <div class="masthead__date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}<br><strong>Berita Indonesia Hari Ini</strong></div>
                    <a href="{{ route('news.search') }}" class="mobile-search-button focus-ring" aria-label="Buka pencarian"><x-icon name="search" size="20" /></a>
                </div>
            </div>
            @php
                $navigationCategories = collect($categories ?? config('news.categories'))->values();
                $primaryCategorySlugs = ['politik', 'hukrim', 'kesehatan', 'ekbis', 'pendidikan', 'olahraga'];
                $primaryCategories = $navigationCategories->filter(fn ($category) => in_array(data_get($category, 'slug'), $primaryCategorySlugs, true));
                $moreCategories = $navigationCategories->reject(fn ($category) => in_array(data_get($category, 'slug'), $primaryCategorySlugs, true));
                $activeCategorySlug = data_get(request()->route('category'), 'slug');
            @endphp
            <nav class="main-nav" :class="{ 'main-nav--open': menuOpen }" aria-label="Navigasi utama">
                <div class="site-container main-nav__inner">
                    <div class="main-nav__mobile-heading">
                        <span>Navigasi utama</span>
                        <small>Jelajahi Kategori</small>
                    </div>
                    <a href="{{ route('home') }}" class="desktop-nav-brand focus-ring" aria-label="Harian Merah Putih, kembali ke beranda">
                        <img src="{{ asset('branding/logo-primary.png') }}" alt="Harian Merah Putih">
                    </a>
                    <a href="{{ route('home') }}" class="main-nav__home focus-ring {{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
                    <div class="main-nav__categories">
                        @foreach ($primaryCategories as $category)
                            @php($slug = data_get($category, 'slug'))
                            <a href="{{ isset($category->slug) ? route('news.category', $category) : '#' }}" class="focus-ring {{ $activeCategorySlug === $slug ? 'is-active' : '' }}">{{ data_get($category, 'name') }}</a>
                        @endforeach
                    </div>
                    @if ($moreCategories->isNotEmpty())
                        <details class="main-nav__more">
                            <summary class="focus-ring">Lainnya <span class="main-nav__more-icon" aria-hidden="true"><x-icon name="chevron-down" size="13" /></span></summary>
                            <div class="main-nav__more-menu">
                                <a href="{{ route('showcase.photos') }}" class="focus-ring {{ request()->routeIs('showcase.photos') ? 'is-active' : '' }}">Foto Peristiwa</a>
                                <a href="{{ route('showcase.tv') }}" class="focus-ring {{ request()->routeIs('showcase.tv') ? 'is-active' : '' }}">Merah Putih TV</a>
                                @foreach ($moreCategories as $category)
                                    @php($slug = data_get($category, 'slug'))
                                    <a href="{{ isset($category->slug) ? route('news.category', $category) : '#' }}" class="focus-ring {{ $activeCategorySlug === $slug ? 'is-active' : '' }}">{{ data_get($category, 'name') }}</a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                    <a href="{{ route('news.index') }}" class="main-nav__index focus-ring {{ request()->routeIs('news.index') ? 'is-active' : '' }}">Indeks</a>
                    <a href="{{ route('news.search') }}" class="main-nav__search focus-ring {{ request()->routeIs('news.search') ? 'is-active' : '' }}" aria-label="Cari berita">Cari <span aria-hidden="true">⌕</span></a>
                </div>
            </nav>
        </header>

        <main id="main-content">
            @if (session('status'))
                <div class="site-container status-message" role="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>

        <footer id="footer" class="site-footer">
            <div class="site-container footer-grid">
                <div class="footer-brand"><a href="{{ route('home') }}" class="brand-lockup brand-lockup--footer"><img src="{{ asset('branding/logo-footer.png') }}" alt="Harian Merah Putih" class="brand-lockup__primary"></a><p>Portal berita Indonesia yang menyajikan kabar, konteks, dan suara dari seluruh negeri.</p></div>
                <div><h2>Kategori</h2><div class="footer-links">@foreach (($categories ?? config('news.categories')) as $category)<a href="{{ isset($category->slug) ? route('news.category', $category) : '#' }}">{{ $category->name ?? $category['name'] }}</a>@endforeach</div></div>
                <div><h2>Harian Merah Putih</h2><div class="footer-links"><a href="{{ route('institutional.about') }}">Tentang Kami</a><a href="{{ route('redaction.show') }}">Redaksi</a><a href="{{ route('showcase.photos') }}">Foto Peristiwa</a><a href="{{ route('showcase.tv') }}">Merah Putih TV</a><a href="{{ route('institutional.cyber-media-guidelines') }}">Pedoman Media Siber</a><a href="{{ route('institutional.privacy') }}">Kebijakan Privasi</a><a href="{{ route('institutional.contact') }}">Hubungi Redaksi</a></div></div>
                <div class="footer-follow"><h2>Ikuti Kami</h2><p>Dapatkan kabar pilihan langsung dari ruang redaksi.</p><div class="footer-socials">@foreach($socialLinks as [$key, $label]) @if(!empty($siteSettings['social.'.$key]))<a href="{{ $siteSettings['social.'.$key] }}" title="{{ $label }}" target="_blank" rel="noopener noreferrer"><x-icon name="{{ $key }}" size="15" /></a>@endif @endforeach<a href="{{ $rssUrl }}" title="RSS"><x-icon name="rss" size="15" /></a>@if($officePhone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $officePhone) }}" title="Telepon kantor"><x-icon name="phone" size="15" /></a>@endif @if($whatsapp)<a href="https://wa.me/{{ $whatsapp }}" title="WhatsApp" target="_blank" rel="noopener noreferrer"><x-icon name="whatsapp" size="15" /></a>@endif</div></div>
            </div>
            <div class="site-container footer-bottom"><span>© {{ date('Y') }} Harian Merah Putih</span><span>Seluruh hak cipta dilindungi.</span><span>Data demonstrasi hanya untuk development.</span></div>
        </footer>

        @include('components.bottom-nav')
    </body>
</html>
