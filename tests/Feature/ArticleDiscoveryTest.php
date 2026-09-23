<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_tabs_return_latest_popular_and_editor_picks(): void
    {
        $category = Category::factory()->create();
        $latest = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Artikel Paling Baru', 'published_at' => now(), 'view_count' => 1]);
        $popular = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Artikel Paling Populer', 'published_at' => now()->subDay(), 'view_count' => 900]);
        $pick = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Artikel Pilihan Editor', 'published_at' => now()->subDays(2), 'is_editor_pick' => true]);

        $this->get(route('home', ['panel' => 'latest']))->assertOk()->assertSee('is-active', false)->assertSee($latest->title);
        $this->get(route('home', ['panel' => 'popular']))->assertOk()->assertSeeInOrder([$popular->title, $latest->title]);
        $this->get(route('home', ['panel' => 'editor']))->assertOk()->assertSee($pick->title);
    }

    public function test_category_tabs_filter_and_order_articles(): void
    {
        $category = Category::factory()->create();
        $regular = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Reguler', 'view_count' => 2]);
        $popular = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Populer Kategori', 'view_count' => 500, 'published_at' => now()->subDay()]);
        $pick = Article::factory()->published()->create(['category_id' => $category, 'title' => 'Pilihan Kategori', 'is_editor_pick' => true, 'published_at' => now()->subDays(2)]);

        $this->get(route('news.category', ['category' => $category, 'tab' => 'popular']))->assertOk()->assertSeeInOrder([$popular->title, $regular->title]);
        $this->get(route('news.category', ['category' => $category, 'tab' => 'editor']))->assertOk()->assertSee($pick->title)->assertDontSee($regular->title);
    }

    public function test_public_article_counts_only_once_per_session(): void
    {
        $article = Article::factory()->published()->create(['view_count' => 10]);

        $this->get(route('news.show', $article->slug))->assertOk();
        $this->get(route('news.show', $article->slug))->assertOk();

        $this->assertSame(11, $article->fresh()->view_count);
    }

    public function test_editor_can_mark_article_as_editor_pick(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $article = Article::factory()->create(['author_id' => $editor]);

        $payload = [
            'category_id' => $article->category_id,
            'title' => $article->title,
            'slug' => $article->slug,
            'excerpt' => $article->excerpt,
            'body' => $article->body,
            'status' => 'draft',
            'is_editor_pick' => '1',
        ];

        $this->actingAs($editor)->put(route('admin.articles.update', $article), $payload)->assertRedirect();
        $this->assertTrue($article->fresh()->is_editor_pick);
    }
}
