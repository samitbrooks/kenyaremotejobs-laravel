<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\PageView;
use App\Models\User;
use App\Support\SafeUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('jobs.admin_emails', ['superadmin@kenyaremotejobs.com']);
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');
        $response->assertHeader('X-XSS-Protection', '0');
    }

    public function test_safe_url_helper_blocks_open_redirect_vectors(): void
    {
        $this->assertSame('/account', SafeUrl::redirectPath('/\\evil.com', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath('/\\//evil.com', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath('//evil.com', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath('https://evil.com', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath('javascript:alert(1)', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath('evil.com', '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath("/\evil.com", '/account'));
        $this->assertSame('/account', SafeUrl::redirectPath("/path\r\nevil.com", '/account'));

        // Legitimate paths should be preserved
        $this->assertSame('/jobs/frontend-dev', SafeUrl::redirectPath('/jobs/frontend-dev', '/account'));
        $this->assertSame('/pricing?plan=pro', SafeUrl::redirectPath('/pricing?plan=pro', '/account'));
    }

    public function test_candidate_login_sanitizes_malicious_open_redirects(): void
    {
        $response = $this->post(route('account.login'), [
            'email' => 'candidate@example.com',
            'redirectTo' => '//malicious-phishing.com/steal-creds',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();
    }

    public function test_admin_login_sanitizes_malicious_open_redirects(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('AdminPass123!'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => 'AdminPass123!',
            'redirectTo' => '//evil.com',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_magic_auth_link_sanitizes_malicious_open_redirects(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'auth.link',
            now()->addMinutes(30),
            ['user' => $user->id, 'redirectTo' => '//evil.com']
        );

        $response = $this->get($signedUrl);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);
    }

    public function test_rate_limiting_throttles_excessive_account_login_requests(): void
    {
        for ($i = 0; $i < 10; $i++) {
            auth()->logout();
            $this->flushSession();
            $response = $this->post(route('account.login'), [
                'email' => "testuser{$i}@example.com",
            ]);
            $response->assertStatus(302);
        }

        // 11th request from the same IP should be throttled
        auth()->logout();
        $this->flushSession();
        $response = $this->post(route('account.login'), [
            'email' => 'blocked@example.com',
        ]);

        $response->assertStatus(429);
    }

    public function test_rate_limiting_throttles_trial_activation_requests(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $response = $this->get(route('trial.activate'));
            $response->assertStatus(302);
        }

        // 16th request in the same minute should be throttled
        $response = $this->get(route('trial.activate'));
        $response->assertStatus(429);
    }

    public function test_admin_livewire_components_reject_guests_with_forbidden(): void
    {
        $user = User::factory()->create();

        $response = Livewire::test('admin-subscription-toggle', [
            'userId' => $user->id,
            'subscribed' => false,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_livewire_components_reject_regular_users_with_forbidden(): void
    {
        $regularUser = User::factory()->create([
            'email' => 'regular@example.com',
        ]);
        $targetUser = User::factory()->create();

        $response = Livewire::actingAs($regularUser)->test('admin-subscription-toggle', [
            'userId' => $targetUser->id,
            'subscribed' => false,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_livewire_components_allow_authorized_admin(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
        ]);
        $targetUser = User::factory()->create();

        $component = Livewire::actingAs($admin)->test('admin-subscription-toggle', [
            'userId' => $targetUser->id,
            'subscribed' => false,
        ]);

        $component->assertStatus(200);
        $component->call('toggle');

        $this->assertTrue((bool) $targetUser->fresh()->subscribed);
    }

    public function test_subscribe_button_ignores_tampered_is_authed_property_when_guest(): void
    {
        Livewire::test('subscribe-button', [
            'isAuthed' => true,
            'alreadySubscribed' => false,
            'period' => 'monthly',
        ])
            ->call('subscribe')
            ->assertRedirect('/account?next=/pricing');
    }

    public function test_buy_package_button_ignores_tampered_is_authed_property_when_guest(): void
    {
        Livewire::test('buy-package-button', [
            'isAuthed' => true,
            'tier' => 'basic',
            'priceKes' => 300,
        ])
            ->call('buy')
            ->assertRedirect('/account?next=/pricing');
    }

    public function test_unlock_button_requires_auth_and_prevents_duplicate_unlock(): void
    {
        $job = JobListing::create([
            'id' => 'sec-test-job-1',
            'source_name' => 'direct',
            'source_id' => 'sec-101',
            'source_url' => 'https://example.com/sec-101',
            'title' => 'Security Engineer',
            'company' => 'SafeCorp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Security job description',
            'tags' => ['Security'],
            'origin' => 'synced',
            'tier' => 'basic',
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote'],
            'audience_segments' => [],
            'posted_at' => now(),
        ]);

        // 1. Guest is redirected to login
        Livewire::test('unlock-button', [
            'jobId' => $job->id,
            'tier' => 'basic',
            'remainingCredits' => 5,
            'packagePriceKes' => 300,
            'isAuthed' => true, // spoofed
        ])
            ->call('unlock')
            ->assertRedirect("/account?next=/jobs/{$job->id}");

        // 2. Authenticated user already unlocked redirects cleanly without exception
        $user = User::factory()->create();
        $user->jobUnlocks()->create([
            'job_listing_id' => $job->id,
            'tier' => 'basic',
            'amount_kes' => 0,
            'unlocked_at' => now(),
        ]);

        Livewire::actingAs($user)->test('unlock-button', [
            'jobId' => $job->id,
            'tier' => 'basic',
            'remainingCredits' => 0,
            'packagePriceKes' => 300,
            'isAuthed' => true,
        ])
            ->call('unlock')
            ->assertRedirect("/jobs/{$job->id}");
    }

    public function test_mpesa_webhook_rejects_empty_or_invalid_secret(): void
    {
        Config::set('payments.mpesa.callback_secret', 'very-strong-secret-12345');

        $response = $this->postJson('/webhooks/mpesa/callback/wrong-secret', [
            'Body' => ['stkCallback' => []],
        ]);

        $response->assertStatus(403);
    }

    public function test_whatsapp_webhook_verification_timing_attack_protection(): void
    {
        Config::set('whatsapp.webhook_verify_token', 'my-verify-token-xyz');

        // Wrong token fails with 403
        $failResponse = $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=wrong-token&hub_challenge=12345');
        $failResponse->assertStatus(403);

        // Correct token succeeds with 200 and returns challenge
        $okResponse = $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=my-verify-token-xyz&hub_challenge=12345');
        $okResponse->assertStatus(200);
        $this->assertSame('12345', $okResponse->getContent());
    }

    public function test_whatsapp_webhook_signature_verification(): void
    {
        Config::set('whatsapp.app_secret', 'test-app-secret-6789');

        $payload = json_encode(['entry' => []]);

        // Request with missing signature is rejected 401
        $noSigResponse = $this->call(
            'POST',
            '/webhooks/whatsapp',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $noSigResponse->assertStatus(401);

        // Request with invalid signature is rejected 401
        $badSigResponse = $this->call(
            'POST',
            '/webhooks/whatsapp',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => 'sha256=invalid-digest',
            ],
            $payload
        );
        $badSigResponse->assertStatus(401);

        // Request with valid HMAC SHA256 is accepted 200
        $validHash = 'sha256='.hash_hmac('sha256', $payload, 'test-app-secret-6789');
        $goodSigResponse = $this->call(
            'POST',
            '/webhooks/whatsapp',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => $validHash,
            ],
            $payload
        );
        $goodSigResponse->assertStatus(200);
    }

    public function test_track_page_view_truncates_oversized_path_safely(): void
    {
        $oversizedPath = '/jobs/'.str_repeat('a', 500);

        $response = $this->get($oversizedPath);

        $view = PageView::latest('id')->first();
        $this->assertNotNull($view);
        $this->assertLessThanOrEqual(250, strlen($view->path));
    }
}
