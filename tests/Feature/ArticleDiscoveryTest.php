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

    public function test_home_latest_cards_continue_after_headline_stories(): void
    {
        $category = Category::factory()->create();
        $articles = collect(range(1, 14))->map(
            fn (int $number) => Article::factory()->published()->create([
                'category_id' => $category,
                'title' => 'Berita Beranda '.$number,
                'published_at' => now()->subMinutes($number),
            ]),
        );

        $response = $this->get(route('home'));

        $response->assertOk();
        $this->assertSame(
            $articles->slice(4, 10)->pluck('id')->all(),
            $response->viewData('latest')->pluck('id')->all(),
        );
        $this->assertSame(
            $articles->slice(1, 3)->pluck('id')->all(),
            $response->viewData('secondary')->pluck('id')->all(),
        );
        $this->assertSame(
            $articles->slice(0, 4)->pluck('id')->all(),
            $response->viewData('tickerArticles')->pluck('id')->all(),
        );
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

    public function test_published_articles_require_a_publication_time_and_scheduled_articles_require_a_future_time(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();
        $payload = [
            'category_id' => $category->id,
            'title' => 'Artikel Siap Terbit',
            'excerpt' => 'Ringkasan artikel siap terbit.',
            'body' => 'Isi artikel siap terbit.',
            'status' => 'published',
        ];

        $this->actingAs($editor)
            ->post(route('admin.articles.store'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'title' => 'Artikel Siap Terbit',
            'status' => 'published',
        ]);

        $this->actingAs($editor)
            ->post(route('admin.articles.store'), [
                ...$payload,
                'status' => 'scheduled',
                'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('published_at');

        $this->assertSame('published', Article::query()->where('title', 'Artikel Siap Terbit')->value('status'));
    }
}
