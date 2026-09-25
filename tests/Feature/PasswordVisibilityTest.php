<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_reset_password_forms_expose_accessible_visibility_controls(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-controls="password"', false);

        $this->get(route('admin.password.reset', ['token' => 'test-token', 'email' => 'editor@example.test']))
            ->assertOk()
            ->assertSee('aria-controls="password"', false)
            ->assertSee('aria-controls="password_confirmation"', false)
            ->assertSee('aria-label="Tampilkan password"', false);
    }

    public function test_user_form_exposes_visibility_controls_for_both_password_fields(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('aria-controls="user-password"', false)
            ->assertSee('aria-controls="user-password-confirmation"', false)
            ->assertSee('data-password-toggle', false);
    }
}
