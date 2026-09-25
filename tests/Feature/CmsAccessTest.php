<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_requires_login_and_writer_cannot_publish(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $writer = User::factory()->create(['role' => 'writer']);
        $article = Article::factory()->create([
            'author_id' => $writer->id,
            'category_id' => Category::factory(),
            'status' => 'draft',
        ]);

        $this->assertFalse($writer->can('publish', $article));
        $this->actingAs($writer)->get('/admin/articles/'.$article->id.'/preview')->assertOk();

        $other = Article::factory()->create([
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'status' => 'draft',
        ]);

        $this->actingAs($writer)->get('/admin/articles/'.$other->id.'/preview')->assertForbidden();
    }

    public function test_admin_can_edit_draft_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $article = Article::factory()->create([
            'category_id' => Category::factory(),
            'status' => 'draft',
            'title' => 'Draft yang Bisa Diedit',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.articles.edit', $article))
            ->assertOk()
            ->assertSee('Draft yang Bisa Diedit');
    }

    public function test_media_relation_keeps_shared_media(): void
    {
        $article = Article::factory()->create(['category_id' => Category::factory()]);
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/test.jpg',
            'original_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1,
            'width' => 100,
            'height' => 100,
            'is_temporary' => false,
        ]);

        $article->mediaLinks()->create(['media_id' => $media->id, 'role' => 'featured', 'sort_order' => 0]);
        $article->mediaLinks()->delete();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }
}
