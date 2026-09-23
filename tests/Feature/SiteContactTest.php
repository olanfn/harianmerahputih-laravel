<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_contact_links_and_public_layout_uses_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->put(route('admin.site-contact.update'), [
            'social' => ['facebook' => 'https://facebook.com/merahputih', 'x' => 'https://x.com/merahputih', 'instagram' => 'https://instagram.com/merahputih', 'youtube' => 'https://youtube.com/@merahputih', 'tiktok' => 'https://tiktok.com/@merahputih', 'rss' => route('distribution.rss')],
            'contact' => ['office_phone' => '021-1234567', 'whatsapp' => '628123456789'],
        ]);
        $response->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('https://facebook.com/merahputih', Setting::where('key', 'social.facebook')->value('value'));
        $this->get(route('home'))->assertOk()->assertSee('https://facebook.com/merahputih')->assertSee('tel:0211234567')->assertSee('https://wa.me/628123456789');
    }

    public function test_writer_cannot_manage_contact_links(): void
    {
        $writer = User::factory()->create(['role' => 'writer']);
        $this->actingAs($writer)->get(route('admin.site-contact.edit'))->assertForbidden();
    }
}
