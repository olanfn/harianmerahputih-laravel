<article class="article-card">
    <a href="{{ route('news.show', $article->slug) }}" class="focus-ring"><x-news-media :article="$article" /></a>
    <div class="article-card__body">
        <a href="{{ route('news.category', $article->category) }}" class="story-meta__category">{{ $article->category->name }}</a>@if ($article->is_demo)<span class="demo-tag">DEMO</span>@endif
        <h2><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h2>
        <p>{{ $article->excerpt }}</p>
        <div class="article-card__meta"><time datetime="{{ $article->published_at?->toISOString() }}">{{ $article->published_at?->locale('id')->translatedFormat('d F Y') }}</time><a href="{{ route('news.show', $article->slug) }}">Baca →</a></div>
    </div>
</article>