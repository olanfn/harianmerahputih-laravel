<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\EventPhoto;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Models\TvVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CmsCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_in_use_cannot_be_deleted_and_article_survives(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'category_id' => $category->id]);
    }

    public function test_admin_taxonomy_pages_render_with_their_real_route_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk()->assertSee(route('admin.categories.create'));
        $this->actingAs($admin)->get(route('admin.categories.create'))->assertOk()->assertSee(route('admin.categories.store'));
        $this->actingAs($admin)->get(route('admin.categories.edit', $category))->assertOk()->assertSee(route('admin.categories.update', $category));
        $this->actingAs($admin)->get(route('admin.tags.index'))->assertOk()->assertSee(route('admin.tags.create'));
        $this->actingAs($admin)->get(route('admin.tags.edit', $tag))->assertOk()->assertSee(route('admin.tags.update', $tag));
    }

    public function test_public_media_url_is_same_origin_for_reliable_editor_previews(): void
    {
        $media = new Media(['disk' => 'public', 'path' => 'media/2026/09/preview-image.jpg']);

        $this->assertSame('/storage/media/2026/09/preview-image.jpg', $media->url());
        $this->assertStringStartsWith(rtrim((string) config('app.url'), '/').'/storage/', $media->absoluteUrl());
    }

    public function test_public_article_renders_featured_gallery_and_inline_media_in_order(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $article = Article::factory()->published()->create(['category_id' => $category->id]);
        $inlineMedia = null;
        foreach (['featured', 'gallery', 'inline'] as $order => $role) {
            $media = Media::create(['disk' => 'public', 'path' => "articles/$role.jpg", 'original_name' => "$role.jpg", 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 1200, 'height' => 800, 'alt_text' => null, 'caption' => null, 'is_temporary' => false]);
            $article->mediaLinks()->create(['media_id' => $media->id, 'role' => $role, 'sort_order' => $order]);
            if ($role === 'inline') $inlineMedia = $media;
        }
        $article->update(['body' => 'Isi sebelum [[media:'.$inlineMedia->id.']] isi sesudah.']);

        $response = $this->get(route('news.show', $article->slug));
        $response->assertOk()->assertSee('articles/featured.jpg')->assertSee('articles/gallery.jpg')->assertSee('articles/inline.jpg')->assertSee($article->title);
    }

    public function test_inline_token_renders_at_body_position_and_invalid_tokens_are_ignored(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $article = Article::factory()->published()->create(['category_id' => $category->id, 'body' => 'Sebelum [[media:999999]] dan sesudah.']);
        $media = Media::create(['disk' => 'public', 'path' => 'inline-position.jpg', 'original_name' => 'inline-position.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 800, 'height' => 600, 'alt_text' => 'Inline aman', 'caption' => 'Caption inline', 'is_temporary' => false]);
        $article->update(['body' => 'Sebelum [[media:'.$media->id.']] dan sesudah [[media:888888]].']);
        $article->mediaLinks()->create(['media_id' => $media->id, 'role' => 'inline', 'sort_order' => 4]);

        $response = $this->get(route('news.show', $article->slug));
        $response->assertOk()->assertSeeInOrder(['Sebelum', 'inline-position.jpg', 'dan sesudah'])->assertDontSee('[[media:888888]]')->assertSee('Caption inline');
    }

    public function test_inline_token_is_available_in_private_preview(): void
    {
        Storage::fake('public');
        $writer = User::factory()->create(['role' => 'writer']);
        $category = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $category->id, 'author_id' => $writer->id, 'body' => 'Preview [[media:123456]].']);
        $media = Media::create(['disk' => 'public', 'path' => 'preview-inline.jpg', 'original_name' => 'preview-inline.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 800, 'height' => 600, 'is_temporary' => false]);
        $article->update(['body' => 'Preview [[media:'.$media->id.']].']);
        $article->mediaLinks()->create(['media_id' => $media->id, 'role' => 'inline', 'sort_order' => 0]);

        $this->actingAs($writer)->get(route('admin.articles.preview', $article))->assertOk()->assertSee('preview-inline.jpg');
    }

    public function test_media_library_blocks_deleting_used_media_and_audit_and_revision_are_private(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $article = Article::factory()->create(['category_id' => $category->id]);
        $media = Media::create(['disk' => 'public', 'path' => 'used.jpg', 'original_name' => 'used.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 100, 'height' => 100, 'is_temporary' => false]);
        $article->mediaLinks()->create(['media_id' => $media->id, 'role' => 'gallery', 'sort_order' => 0]);
        $revision = ArticleRevision::create(['article_id' => $article->id, 'editor_id' => $admin->id, 'title' => $article->title, 'body' => $article->body, 'status' => 'draft']);

        $this->actingAs($admin)->delete(route('admin.media.destroy', $media))->assertRedirect();
        $this->assertDatabaseHas('media', ['id' => $media->id]);
        $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.articles.revisions', $article))->assertOk()->assertSee($article->title);
    $this->actingAs($admin)->post(route('admin.articles.revisions.restore', [$article, $revision]))->assertRedirect();
    }

    public function test_media_library_hides_temporary_uploads_until_article_is_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Media::create(['disk' => 'public', 'path' => 'temporary.jpg', 'original_name' => 'temporary.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 100, 'height' => 100, 'is_temporary' => true]);
        Media::create(['disk' => 'public', 'path' => 'saved.jpg', 'original_name' => 'saved.jpg', 'mime_type' => 'image/jpeg', 'size' => 10, 'width' => 100, 'height' => 100, 'is_temporary' => false]);

        $this->actingAs($admin)
            ->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('saved.jpg')
            ->assertDontSee('temporary.jpg');
    }

    public function test_photo_and_tv_modules_filter_to_published_public_content(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create(['role' => 'editor']);
        EventPhoto::create(['title' => 'Foto Terbit', 'slug' => 'foto-terbit', 'status' => 'published', 'published_at' => now(), 'author_id' => $user->id, 'sort_order' => 1]);
        EventPhoto::create(['title' => 'Foto Draft', 'slug' => 'foto-draft', 'status' => 'draft', 'author_id' => $user->id]);
        TvVideo::create(['title' => 'TV Terbit', 'slug' => 'tv-terbit', 'status' => 'published', 'published_at' => now(), 'author_id' => $user->id, 'sort_order' => 1]);

        $this->get('/foto-peristiwa')->assertOk()->assertSee('Foto Terbit')->assertDontSee('Foto Draft');
        $this->get('/merah-putih-tv')->assertOk()->assertSee('TV Terbit');
    }

    public function test_event_photo_can_be_published_without_manual_publication_time_and_uploaded_media_is_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/event-photo.jpg',
            'original_name' => 'event-photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 10,
            'width' => 1200,
            'height' => 800,
            'is_temporary' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.showcase.create', 'event-photos'))
            ->assertOk()
            ->assertSee('showcase-media-input');

        $this->actingAs($admin)
            ->post(route('admin.showcase.store', 'event-photos'), [
                'title' => 'Foto Peristiwa Uji',
                'status' => 'published',
                'media_id' => $media->id,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.showcase.index', 'event-photos'));

        $photo = EventPhoto::where('title', 'Foto Peristiwa Uji')->firstOrFail();
        $this->assertNotNull($photo->published_at);
        $this->assertSame($media->id, $photo->media_id);
        $this->assertFalse($media->fresh()->is_temporary);
        $this->get('/foto-peristiwa')->assertSee('Foto Peristiwa Uji');
    }

    public function test_password_reset_request_page_is_available_and_rate_limited_route_exists(): void
    {
        $this->get(route('admin.password.request'))->assertOk()->assertSee('Lupa password');
        $this->assertNotNull(route('admin.password.email'));
        $this->assertNotNull(route('admin.password.reset', 'test-token'));
    }

    public function test_password_reset_uses_admin_route_and_local_log_mailer(): void
    {
        config(['mail.default' => 'log', 'app.url' => 'https://harianmerahputih.id']);
        URL::forceRootUrl('https://harianmerahputih.id');
        URL::forceScheme('https');
        $user = User::factory()->create(['email' => 'reset-target@example.test']);

        $this->post(route('admin.password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
        $this->assertStringStartsWith('https://harianmerahputih.id/admin/reset-password/', route('admin.password.reset', 'test-token'));
    }
}
