<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_index_filters_and_sorts_server_side(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['name' => 'Politik']);
        Article::factory()->create(['category_id' => $category->id, 'title' => 'Zeta headline', 'status' => 'published']);
        Article::factory()->create(['category_id' => $category->id, 'title' => 'Alpha headline', 'status' => 'draft']);
        $response = $this->actingAs($admin)->get(route('admin.articles.index', ['q' => 'Alpha', 'status' => 'draft', 'category' => $category->id, 'sort' => 'title_asc']));
        $response->assertOk()->assertSee('Alpha headline')->assertDontSee('Zeta headline')->assertSee('1 konten ditemukan')->assertSee('name="q"', false);
    }

    public function test_writer_query_is_limited_to_owned_articles_and_invalid_sort_falls_back(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        Article::factory()->create(['author_id' => $writer->id, 'title' => 'Owned article']);
        Article::factory()->create(['title' => 'Other article']);
        $this->actingAs($writer)->get(route('admin.articles.index', ['sort' => 'title;drop table articles']))->assertOk()->assertSee('Owned article')->assertDontSee('Other article');
    }
}
