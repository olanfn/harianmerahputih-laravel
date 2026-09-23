<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\RedactionPage;
use Illuminate\Http\Response;

class DistributionController extends Controller
{
    public function sitemap(): Response
    {
        $articles = $this->publishedArticles()->where('published_at', '>=', now()->subDays(2))->take(1000)->get();
        $categories = Category::query()->active()->orderBy('sort_order')->get();
        $institutionalPages = RedactionPage::query()->orderBy('sort_order')->get();

        return response()->view('distribution.sitemap', compact('articles', 'categories', 'institutionalPages'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function newsSitemap(): Response
    {
        $articles = $this->publishedArticles()->get();

        return response()->view('distribution.news-sitemap', compact('articles'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function rss(): Response
    {
        $articles = $this->publishedArticles()->take(30)->get();

        return response()->view('distribution.rss', compact('articles'))->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /internal',
            'Disallow: /cari',
            'Sitemap: '.$baseUrl.'/sitemap.xml',
            'Sitemap: '.$baseUrl.'/news-sitemap.xml',
        ])."\n";

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function publishedArticles()
    {
        return Article::query()->published()->with(['category', 'featuredMedia.media'])->latest('published_at');
    }
}
