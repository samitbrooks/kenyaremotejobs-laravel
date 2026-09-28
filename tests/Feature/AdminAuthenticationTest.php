<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('jobs.admin_emails', ['superadmin@kenyaremotejobs.com', 'admin@example.com']);
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Admin Portal Sign In');
        $response->assertSee('Administrator Credentials Required');
    }

    public function test_non_admin_email_cannot_log_in_via_admin_portal(): void
    {
        User::factory()->create([
            'email' => 'regular@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'regular@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_admin_logs_in_successfully_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('secure-pass-456'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => 'secure-pass-456',
            'redirectTo' => '/admin/jobs',
        ]);

        $response->assertRedirect('/admin/jobs');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_email_on_public_candidate_login_form_is_redirected_to_secure_admin_login(): void
    {
        User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('secure-pass-456'),
        ]);

        // Attempting to bypass password via /account/login with admin email
        $response = $this->post(route('account.login'), [
            'email' => 'superadmin@kenyaremotejobs.com',
        ]);

        // Must redirect to admin.login and remain a guest
        $response->assertRedirect(route('admin.login', ['next' => '/account']));
        $this->assertGuest();
    }

    public function test_admin_can_log_out_safely(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('secure-pass-456'),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_setup_artisan_command_creates_or_updates_admin(): void
    {
        $this->artisan('admin:setup', [
            '--email' => 'superadmin@kenyaremotejobs.com',
            '--password' => 'ArtisanCreatedPass123',
        ])->assertSuccessful();

        $user = User::where('email', 'superadmin@kenyaremotejobs.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('ArtisanCreatedPass123', $user->password));
    }
}
