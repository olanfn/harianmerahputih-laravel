
<urlset {{ 'xmlns' }}="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ url('/') }}</loc></url><url><loc>{{ route('news.index') }}</loc></url>
@foreach ($categories as $category)
    <url><loc>{{ route('news.category', $category) }}</loc></url>
@endforeach
@foreach ($institutionalPages as $page)
    <url><loc>{{ route($page->publicRouteName()) }}</loc><lastmod>{{ $page->updated_at->toAtomString() }}</lastmod></url>
@endforeach
@foreach ($articles as $article)
    <url><loc>{{ route('news.show', $article->slug) }}</loc><lastmod>{{ $article->updated_at->toAtomString() }}</lastmod><changefreq>daily</changefreq></url>
@endforeach
</urlset>
