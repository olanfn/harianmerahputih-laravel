<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    public function test_prerendered_maintenance_page_is_safe_and_returns_service_unavailable(): void
    {
        try {
            $exitCode = Artisan::call('down', [
                '--render' => 'errors::503',
                '--retry' => 120,
                '--status' => 503,
            ]);

            $this->assertSame(0, $exitCode);

            $response = $this->get('/artikel/maintenance-test');

            $response
                ->assertStatus(503)
                ->assertHeader('Retry-After', '120')
                ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
                ->assertSee('Harian Merah Putih')
                ->assertSee('Sedang Dalam Pemeliharaan')
                ->assertSee('Kami sedang melakukan peningkatan sistem')
                ->assertSee('/branding/logo-primary.png', false)
                ->assertDontSee('APP_KEY')
                ->assertDontSee('DB_PASSWORD')
                ->assertDontSee('Stack trace')
                ->assertDontSee('PHP_VERSION');

            $this->assertFalse($response->isRedirection());
        } finally {
            Artisan::call('up');
        }
    }
}
