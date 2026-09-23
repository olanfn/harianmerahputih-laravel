<rss {{ 'version' }}="2.0"><channel><title>{{ config('news.brand.name') }}</title><link>{{ url('/') }}</link><description>{{ config('news.brand.tagline') }}</description><language>id-id</language>
@foreach ($articles as $article)
    <item><title>{{ $article->title }}</title><link>{{ route('news.show', $article->slug) }}</link><guid isPermaLink="true">{{ route('news.show', $article->slug) }}</guid><pubDate>{{ $article->published_at->toRfc7231String() }}</pubDate><description>{{ $article->excerpt }}</description><category>{{ $article->category->name }}</category></item>
@endforeach
</channel></rss>