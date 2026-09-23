<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_foundation_content_without_articles(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Harian Merah Putih');
        $response->assertSee('Berita perdana sedang disiapkan');
    }

    public function test_readiness_page_does_not_expose_environment_secrets(): void
    {
        $response = $this->get('/internal/readiness');

        $response->assertOk();
        $response->assertSee('Foundation siap diperiksa.');
        $response->assertDontSee((string) config('app.key'));
        $response->assertDontSee('DB_PASSWORD');
    }

    public function test_readiness_page_is_not_available_in_production(): void
    {
        config(['app.env' => 'production']);

        $this->get('/internal/readiness')->assertNotFound();
    }
}
