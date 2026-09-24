<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_only_expose_published_articles(): void
    {
        $category = Category::factory()->create(['slug' => 'nasional']);
        $tag = Tag::factory()->create(['slug' => 'demonstrasi']);
        $author = User::factory()->create();
        $published = Article::factory()->published()->create(['category_id' => $category->id, 'author_id' => $author->id, 'title' => 'Artikel Terbit Demonstrasi']);
        $published->tags()->attach($tag);
        $draft = Article::factory()->create(['category_id' => $category->id, 'author_id' => $author->id, 'title' => 'Artikel Draft Rahasia']);
        $scheduled = Article::factory()->scheduled()->create(['category_id' => $category->id, 'author_id' => $author->id, 'title' => 'Artikel Jadwal Rahasia']);
        $future = Article::factory()->published()->create(['category_id' => $category->id, 'author_id' => $author->id, 'title' => 'Artikel Masa Depan', 'published_at' => now()->addDay()]);

        $this->get('/')->assertOk()->assertSee('Artikel Terbit Demonstrasi')->assertDontSee('Artikel Draft Rahasia')->assertDontSee('Artikel Jadwal Rahasia')->assertDontSee('Artikel Masa Depan');
        $this->get('/kategori/nasional')->assertOk()->assertSee('Artikel Terbit Demonstrasi')->assertDontSee('Artikel Draft Rahasia')->assertDontSee('Artikel Jadwal Rahasia')->assertDontSee('Artikel Masa Depan');
        $this->get('/tag/demonstrasi')->assertOk()->assertSee('Artikel Terbit Demonstrasi')->assertDontSee('Artikel Draft Rahasia');
        $this->get('/indeks')->assertOk()->assertSee('Artikel Terbit Demonstrasi')->assertDontSee('Artikel Draft Rahasia');
        $this->get('/cari?q=Rahasia')->assertOk()->assertDontSee('Artikel Draft Rahasia')->assertDontSee('Artikel Jadwal Rahasia')->assertDontSee('Artikel Masa Depan');
    }

    public function test_non_published_articles_return_not_found_and_published_detail_works(): void
    {
        $category = Category::factory()->create();
        $draft = Article::factory()->create(['category_id' => $category->id]);
        $scheduled = Article::factory()->scheduled()->create(['category_id' => $category->id]);
        $future = Article::factory()->published()->create(['category_id' => $category->id, 'published_at' => now()->addDay()]);
        $published = Article::factory()->published()->create(['category_id' => $category->id]);

        $this->get('/berita/'.$draft->slug)->assertNotFound();
        $this->get('/berita/'.$scheduled->slug)->assertNotFound();
        $this->get('/berita/'.$future->slug)->assertNotFound();
        $this->get('/berita/'.$published->slug)->assertOk()->assertSee($published->title);
    }

    public function test_quote_on_a_new_line_is_rendered_as_an_editorial_quote_block(): void
    {
        $category = Category::factory()->create();
        $article = Article::factory()->published()->create([
            'category_id' => $category->id,
            'body' => "Paragraf pembuka.\n\"Ini adalah kutipan penting.\"",
        ]);

        $this->get(route('news.show', $article->slug))
            ->assertOk()
            ->assertSee('<p class="article-body__paragraph">Paragraf pembuka.</p>', false)
            ->assertSee('<p class="article-body__paragraph article-body__paragraph--quote">', false)
            ->assertSee('Ini adalah kutipan penting.');
    }

    public function test_search_input_is_limited_and_empty_states_are_rendered(): void
    {
        $this->get('/cari?q='.str_repeat('x', 101))->assertStatus(302);
        $this->get('/cari?q=tidak-ada-hasil')->assertOk()->assertSee('Tidak ada hasil pencarian');
        $this->get('/kategori/tidak-ada')->assertNotFound();
        $this->get('/tag/tidak-ada')->assertNotFound();
    }
}