<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleRedirect;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoDistributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_article_has_safe_seo_metadata_and_featured_image(): void
    {
        $category = Category::factory()->create(['slug' => 'nasional']);
        $article = Article::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Judul SEO Demonstrasi',
            'excerpt' => 'Deskripsi SEO demonstrasi.',
            'seo_title' => 'Judul SEO Khusus',
            'seo_description' => 'Deskripsi SEO Khusus.',
            'is_demo' => false,
        ]);
        $media = Media::query()->create([
            'disk' => 'public',
            'path' => 'articles/2026/09/seo.jpg',
            'original_name' => 'seo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'width' => 1200,
            'height' => 630,
            'alt_text' => 'Gambar SEO',
            'is_temporary' => false,
        ]);
        $article->mediaLinks()->create(['media_id' => $media->id, 'role' => 'featured', 'sort_order' => 0]);

        $response = $this->get('/berita/'.$article->slug);

        $response->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('news.show', $article->slug).'">', false)
            ->assertSee('property="og:title" content="Judul SEO Khusus"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('NewsArticle', false)
            ->assertSee($media->url(), false);
    }

    public function test_sitemaps_and_rss_only_contain_published_articles(): void
    {
        $category = Category::factory()->create();
        $published = Article::factory()->published()->create(['category_id' => $category->id, 'title' => 'Published Feed Article', 'published_at' => now()->subHour(), 'is_demo' => false]);
        $olderPublished = Article::factory()->published()->create(['category_id' => $category->id, 'title' => 'Older Published Feed Article', 'published_at' => now()->subDays(5), 'is_demo' => false]);
        $demo = Article::factory()->published()->create(['category_id' => $category->id, 'title' => 'Demo Hidden Feed Article', 'published_at' => now()->subHour(), 'is_demo' => true]);
        $draft = Article::factory()->create(['category_id' => $category->id, 'title' => 'Draft Hidden Feed Article']);
        $future = Article::factory()->published()->create(['category_id' => $category->id, 'title' => 'Future Hidden Feed Article', 'published_at' => now()->addDay()]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee($published->slug)
            ->assertSee($olderPublished->slug)
            ->assertDontSee($demo->slug)
            ->assertDontSee($draft->slug)
            ->assertDontSee($future->slug);

        $this->get('/news-sitemap.xml')
            ->assertOk()
            ->assertSee($published->slug)
            ->assertDontSee($olderPublished->slug)
            ->assertDontSee($demo->slug)
            ->assertDontSee($draft->slug)
            ->assertDontSee($future->slug);

        $this->get('/feed.xml')
            ->assertOk()
            ->assertSee($published->slug)
            ->assertSee($olderPublished->slug)
            ->assertDontSee($demo->slug)
            ->assertDontSee($draft->slug)
            ->assertDontSee($future->slug);

        $this->get('/sitemap.xml')
            ->assertSee(route('redaction.show'), false)
            ->assertSee(route('institutional.about'), false)
            ->assertSee(route('institutional.cyber-media-guidelines'), false)
            ->assertSee(route('institutional.privacy'), false)
            ->assertSee(route('institutional.contact'), false);

        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: '.url('/sitemap.xml'))->assertSee('Disallow: /admin');
    }

    public function test_robots_is_served_only_by_laravel_with_production_rules(): void
    {
        $this->assertFileDoesNotExist(public_path('robots.txt'));
        config(['app.url' => 'https://harianmerahputih.id']);

        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSeeText('User-agent: *')
            ->assertSeeText('Disallow: /admin')
            ->assertSeeText('Disallow: /internal')
            ->assertSeeText('Disallow: /cari')
            ->assertSeeText('Sitemap: https://harianmerahputih.id/sitemap.xml')
            ->assertSeeText('Sitemap: https://harianmerahputih.id/news-sitemap.xml');
    }

    public function test_old_article_slug_redirects_only_to_a_published_target(): void
    {
        $category = Category::factory()->create();
        $article = Article::factory()->published()->create(['category_id' => $category->id, 'slug' => 'slug-baru']);
        ArticleRedirect::query()->create(['old_slug' => 'slug-lama', 'article_id' => $article->id]);

        $this->get('/berita/slug-lama')->assertRedirect(route('news.show', 'slug-baru'))->assertStatus(301);
        $this->get('/berita/'.$article->slug)->assertOk();
    }
}
