<?php

namespace Tests\Feature;

use App\Mail\FreeTrialInvitationEmail;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FreeTrialAndEmailInviteTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleJob(string $id = 'job-trial-test'): JobListing
    {
        return JobListing::create([
            'id' => $id,
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Senior Laravel Engineer',
            'company' => 'Global Remote Co',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Work remotely on high scale Laravel applications.',
            'source_id' => 'src-123',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/apply-laravel',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'tags' => ['laravel', 'php'],
            'audience_segments' => [],
        ]);
    }

    public function test_guest_navigating_to_trial_activate_is_redirected_to_account_login(): void
    {
        $response = $this->get(route('trial.activate'));
        $response->assertRedirect('/account?next=%2Ftrial%2Factivate&trial=1');
        $response->assertSessionHas('info');
    }

    public function test_registered_user_can_activate_24_hour_free_trial(): void
    {
        $user = User::factory()->create([
            'subscribed' => false,
            'trial_started_at' => null,
            'trial_ends_at' => null,
        ]);

        $this->assertFalse($user->onTrial());
        $this->assertFalse($user->hasUsedTrial());

        $response = $this->actingAs($user)->get(route('trial.activate'));
        $response->assertRedirect('/jobs');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->onTrial());
        $this->assertTrue($user->hasUsedTrial());
        $this->assertNotNull($user->trial_started_at);
        $this->assertNotNull($user->trial_ends_at);
        $this->assertTrue($user->trial_ends_at->isFuture());
    }

    public function test_user_on_active_trial_can_access_jobs_and_sees_trial_banner(): void
    {
        $job = $this->createSampleJob();

        $user = User::factory()->onTrial(18)->create([
            'subscribed' => false,
        ]);

        $response = $this->actingAs($user)->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Senior Laravel Engineer');
        $response->assertSee('24-Hour Free Pass Active');

        $detailResponse = $this->actingAs($user)->get('/jobs/'.$job->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Global Remote Co');
        $detailResponse->assertSee('24-Hour Free Pass Active');
    }

    public function test_user_with_expired_trial_is_redirected_to_pricing(): void
    {
        $this->createSampleJob();

        $user = User::factory()->expiredTrial()->create([
            'subscribed' => false,
        ]);

        $this->assertFalse($user->onTrial());
        $this->assertTrue($user->hasUsedTrial());

        // Visiting /jobs redirects to /pricing with expired notice
        $response = $this->actingAs($user)->get('/jobs');
        $response->assertRedirect(route('pricing'));
        $response->assertSessionHas('info');

        // Trying to activate trial again fails and directs to pricing
        $activateResponse = $this->actingAs($user)->get(route('trial.activate'));
        $activateResponse->assertRedirect(route('pricing'));
        $activateResponse->assertSessionHas('error');
    }

    public function test_user_can_claim_free_trial_via_signed_magic_link(): void
    {
        $this->createSampleJob();

        $user = User::factory()->create([
            'name' => 'Sami Kidemi',
            'subscribed' => false,
            'trial_started_at' => null,
            'trial_ends_at' => null,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'trial.claim',
            now()->addDays(7),
            ['user' => $user->id]
        );

        $response = $this->get($signedUrl);
        $response->assertRedirect('/jobs');
        $response->assertSessionHas('success');

        // User is now authenticated and on trial
        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertTrue($user->onTrial());
    }

    public function test_free_trial_invitation_email_renders_jobs_and_signed_button(): void
    {
        $job = $this->createSampleJob('job-email-preview');

        $user = User::factory()->create([
            'name' => 'Sami Brooks',
            'email' => 'sami@example.com',
            'subscribed' => false,
        ]);

        $mailable = new FreeTrialInvitationEmail($user);
        $mailable->assertHasSubject('Sami, we found remote and/or flexible jobs you might like!');
        $mailable->assertSeeInHtml('Dear Sami,');
        $mailable->assertSeeInHtml('Senior Laravel Engineer');
        $mailable->assertSeeInHtml('Global Remote Co');
        $mailable->assertSeeInHtml('Claim Your 24-Hour Free Trial!');
        $mailable->assertSeeInHtml('FIND A BETTER WAY TO WORK');
    }

    public function test_artisan_command_sends_free_trial_invites_to_pending_visitors(): void
    {
        Mail::fake();

        $this->createSampleJob();

        $pendingUser = User::factory()->create([
            'email' => 'pending-visitor@example.com',
            'subscribed' => false,
        ]);

        Payment::create([
            'user_id' => $pendingUser->id,
            'gateway' => 'mpesa',
            'purpose' => 'subscription',
            'amount_kes' => 299,
            'status' => 'pending',
            'phone' => '254712345678',
            'payload' => [],
        ]);

        $otherUser = User::factory()->create([
            'email' => 'other-user@example.com',
            'subscribed' => false,
        ]);

        // Run command with --pending-only
        $this->artisan('email:send-free-trial-invites', ['--pending-only' => true])
            ->assertSuccessful();

        Mail::assertQueued(FreeTrialInvitationEmail::class, function (FreeTrialInvitationEmail $mail) use ($pendingUser) {
            return $mail->user->id === $pendingUser->id;
        });

        Mail::assertNotQueued(FreeTrialInvitationEmail::class, function (FreeTrialInvitationEmail $mail) use ($otherUser) {
            return $mail->user->id === $otherUser->id;
        });
    }
}
