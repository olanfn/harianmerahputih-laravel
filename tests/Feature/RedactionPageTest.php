<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\RedactionPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedactionPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_redaction_page_has_topics_viral_news_and_footer_link(): void
    {
        RedactionPage::query()->findOrFail(1)->update(['content' => '<h2>Susunan Redaksi</h2><p><strong>Pemimpin Redaksi:</strong> Nama Redaktur</p>']);
        $viral = Category::factory()->create(['name' => 'Viral', 'slug' => 'viral', 'is_active' => true]);
        $other = Category::factory()->create(['name' => 'Nasional', 'slug' => 'nasional', 'is_active' => true]);
        $published = Article::factory()->published()->create(['category_id' => $viral, 'title' => 'Berita Viral Terbit']);
        Article::factory()->create(['category_id' => $viral, 'title' => 'Berita Viral Draft', 'status' => 'draft']);

        $this->get(route('redaction.show'))->assertOk()
            ->assertSee('Susunan Redaksi')->assertSee('Nama Redaktur')
            ->assertSee($other->name)->assertSee($published->title)->assertDontSee('Berita Viral Draft');
        $this->get(route('home'))->assertOk()->assertSee(route('redaction.show'));
    }

    public function test_only_admin_roles_can_edit_and_dangerous_rich_text_is_removed(): void
    {
        RedactionPage::query()->findOrFail(1)->update(['content' => '<p>Awal</p>']);
        $writer = User::factory()->create(['role' => 'writer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($writer)->get(route('admin.redaction.edit'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.redaction.edit'))->assertOk()->assertSee('Isi halaman redaksi');
        $this->actingAs($admin)->put(route('admin.redaction.update'), [
            'content' => '<h2 onclick="alert(1)">Redaksi Aman</h2><script>alert(2)</script><p><strong>Nama</strong></p><a href="javascript:alert(3)">Bahaya</a>',
        ])->assertRedirect();

        $content = RedactionPage::query()->findOrFail(1)->content;
        $this->assertStringContainsString('<strong>Nama</strong>', $content);
        $this->assertStringNotContainsString('onclick', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('javascript:', $content);
    }

    public function test_safe_rich_text_preserves_center_alignment_from_the_editor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.redaction.update'), [
            'content' => '<h2 style="text-align: center">Judul Tentang Harian Merah Putih</h2><div style="text-align: right">Isi aman</div>',
        ])->assertRedirect();

        $content = RedactionPage::query()->where('slug', 'redaksi')->value('content');
        $this->assertStringContainsString('style="text-align: center"', $content);
        $this->assertStringContainsString('style="text-align: right"', $content);
    }

    public function test_all_institutional_pages_share_the_redaction_template_and_have_real_footer_links(): void
    {
        $routes = [
            'redaction.show' => 'Redaksi',
            'institutional.about' => 'Tentang Kami',
            'institutional.cyber-media-guidelines' => 'Pedoman Media Siber',
            'institutional.privacy' => 'Kebijakan Privasi',
            'institutional.contact' => 'Hubungi Redaksi',
        ];

        foreach ($routes as $routeName => $title) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('redaction-layout', false)
                ->assertSee('Topik Terkini')
                ->assertSee('Viral');
        }

        $home = $this->get(route('home'))->assertOk();
        foreach (array_keys($routes) as $routeName) {
            $home->assertSee(route($routeName), false);
        }
    }

    public function test_admin_can_edit_each_institutional_page_from_one_editor_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $page = RedactionPage::query()->where('slug', 'tentang-kami')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('Isi halaman tentang kami')
            ->assertSee('Pedoman Media Siber');

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'content' => '<h2>Tentang Kami Baru</h2><script>alert(1)</script>',
        ])->assertRedirect();

        $page->refresh();
        $this->assertStringContainsString('Tentang Kami Baru', $page->content);
        $this->assertStringNotContainsString('<script', $page->content);
        $this->assertSame($admin->id, $page->updated_by);
    }
}
