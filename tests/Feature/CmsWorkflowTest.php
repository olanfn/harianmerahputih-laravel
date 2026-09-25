<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_login_and_manage_reference_data_and_users(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'password' => 'correct-password']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'correct-password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->post(route('admin.categories.store'), ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Teknologi', 'sort_order' => 1, 'is_active' => 1])->assertRedirect();
        $this->post(route('admin.tags.store'), ['name' => 'Teknologi', 'slug' => 'teknologi'])->assertRedirect();
        $this->post(route('admin.users.store'), ['name' => 'Editor Baru', 'email' => 'editor@example.test', 'password' => 'strong-password-123', 'password_confirmation' => 'strong-password-123', 'role' => 'editor'])->assertRedirect();

        $this->assertDatabaseHas('categories', ['slug' => 'teknologi']);
        $this->assertDatabaseHas('tags', ['slug' => 'teknologi']);
        $this->assertDatabaseHas('users', ['email' => 'editor@example.test', 'role' => 'editor']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login']);
    }

    public function test_editor_can_save_article_and_upload_media(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create(['role' => 'editor', 'password' => 'correct-password']);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($editor)->post(route('admin.articles.store'), [
            'category_id' => $category->id,
            'title' => 'Artikel CMS Teruji',
            'excerpt' => 'Ringkasan artikel CMS.',
            'body' => 'Isi artikel CMS.',
            'status' => 'draft',
            'tag_ids' => [$tag->id],
        ])->assertRedirect();

        $this->actingAs($editor)->post(route('admin.media.store'), ['image' => UploadedFile::fake()->image('redaksi.jpg', 1200, 800)])
            ->assertOk()
            ->assertJsonStructure(['id', 'url', 'name']);

        $this->assertDatabaseHas('articles', ['title' => 'Artikel CMS Teruji', 'status' => 'draft']);
        $this->assertDatabaseHas('media', ['original_name' => 'redaksi.jpg', 'is_temporary' => 1]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'article.created']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'media.uploaded']);
    }

    public function test_editor_can_leave_excerpt_empty_and_it_is_generated_from_article_body(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();

        $this->actingAs($editor)->post(route('admin.articles.store'), [
            'category_id' => $category->id,
            'title' => 'Artikel Tanpa Ringkasan',
            'excerpt' => '',
            'body' => '<p>Paragraf pembuka artikel yang dapat digunakan sebagai ringkasan otomatis.</p><p>Paragraf berikutnya.</p>',
            'status' => 'draft',
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'title' => 'Artikel Tanpa Ringkasan',
            'excerpt' => 'Paragraf pembuka artikel yang dapat digunakan sebagai ringkasan otomatis. Paragraf berikutnya.',
        ]);
    }

    public function test_media_uses_article_title_for_filename_and_empty_metadata(): void
    {
        Storage::fake('public');
        $editor = User::factory()->create(['role' => 'editor']);
        $category = Category::factory()->create();
        $title = 'Judul Artikel Untuk Gambar';

        $upload = $this->actingAs($editor)->postJson(route('admin.media.store'), [
            'image' => UploadedFile::fake()->image('kamera-lama.jpg', 1200, 800),
            'article_title' => $title,
        ])->assertOk();

        $media = Media::findOrFail($upload->json('id'));
        $this->assertSame('judul-artikel-untuk-gambar.jpg', $media->original_name);
        Storage::disk('public')->assertExists($media->path);

        $this->actingAs($editor)->post(route('admin.articles.store'), [
            'category_id' => $category->id,
            'title' => $title,
            'body' => 'Isi artikel.',
            'status' => 'draft',
            'media' => [['id' => $media->id, 'role' => 'featured', 'sort_order' => 0, 'alt_text' => '', 'caption' => '']],
        ])->assertRedirect();

        $this->assertDatabaseHas('media', ['id' => $media->id, 'alt_text' => $title, 'caption' => $title, 'is_temporary' => 0]);
        $this->assertDatabaseHas('article_media', ['media_id' => $media->id, 'role' => 'featured', 'caption_override' => $title]);
    }
}
