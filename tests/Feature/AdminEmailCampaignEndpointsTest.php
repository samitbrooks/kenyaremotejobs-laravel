<?php

namespace Tests\Feature;

use App\Mail\FollowUpInvitationEmail;
use App\Mail\FreeTrialInvitationEmail;
use App\Mail\JobMatchesDigestEmail;
use App\Mail\MarketingEmail;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailCampaignEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleJob(): JobListing
    {
        return JobListing::create([
            'id' => 'sample-test-job-'.uniqid(),
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Software Engineer',
            'company' => 'Global Tech',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Test remote job description.',
            'source_id' => 'src-'.uniqid(),
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/job',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);
    }

    public function test_admin_can_view_email_dashboard_with_campaign_cards(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($admin)->get('/admin/email');
        $response->assertStatus(200);
        $response->assertSee('Email Users and Automated Campaigns');
        $response->assertSee('Daily Job Matches Digest');
        $response->assertSee('FlexJobs-Style 24-Hour Free Trial Campaign');
        $response->assertSee('FlexJobs-Style Follow-Up Campaign');
        $response->assertSee('Compose Custom Broadcast');
    }

    public function test_admin_can_dispatch_free_trial_broadcast_via_post(): void
    {
        Mail::fake();
        $this->createSampleJob();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $freeUser = User::factory()->create(['subscribed' => false]);

        $response = $this->actingAs($admin)->post('/admin/email/trial', [
            'target' => 'free',
        ]);

        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');

        Mail::assertQueued(FreeTrialInvitationEmail::class, function ($mail) use ($freeUser) {
            return $mail->hasTo($freeUser->email);
        });
    }

    public function test_admin_can_dispatch_free_trial_to_pending_checkout_drop_offs(): void
    {
        Mail::fake();
        $this->createSampleJob();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $pendingUser = User::factory()->create(['subscribed' => false]);

        Payment::create([
            'user_id' => $pendingUser->id,
            'gateway' => 'mpesa',
            'purpose' => 'subscription',
            'amount_kes' => 299,
            'status' => 'pending',
            'phone' => '254700000000',
            'payload' => [],
        ]);

        $response = $this->actingAs($admin)->post('/admin/email/trial', [
            'target' => 'pending',
        ]);

        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');

        Mail::assertQueued(FreeTrialInvitationEmail::class, function ($mail) use ($pendingUser) {
            return $mail->hasTo($pendingUser->email);
        });
    }

    public function test_admin_can_dispatch_follow_up_broadcast_via_post(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $eligibleUser = User::factory()->create([
            'subscribed' => false,
            'created_at' => now()->subHours(30),
            'follow_up_sent_at' => null,
        ]);

        $response = $this->actingAs($admin)->post('/admin/email/follow-up');

        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');

        Mail::assertQueued(FollowUpInvitationEmail::class, function ($mail) use ($eligibleUser) {
            return $mail->hasTo($eligibleUser->email);
        });

        $this->assertNotNull($eligibleUser->fresh()->follow_up_sent_at);
    }

    public function test_admin_can_dispatch_daily_digest_broadcast_via_post(): void
    {
        Mail::fake();
        $this->createSampleJob();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $user = User::factory()->create([
            'subscribed' => false,
            'last_job_digest_at' => null,
        ]);

        $response = $this->actingAs($admin)->post('/admin/email/digest', [
            'force' => 1,
        ]);

        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');

        Mail::assertQueued(JobMatchesDigestEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertNotNull($user->fresh()->last_job_digest_at);
    }

    public function test_admin_can_send_deliverability_test_samples_immediately(): void
    {
        Mail::fake();
        $this->createSampleJob();

        $admin = User::factory()->create(['email' => 'admin@example.com']);

        // Test trial invite
        $response = $this->actingAs($admin)->post('/admin/email/test', [
            'test_email' => 'admin@example.com',
            'test_type' => 'trial',
        ]);
        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');
        Mail::assertQueued(FreeTrialInvitationEmail::class);

        // Test follow-up sample
        $response2 = $this->actingAs($admin)->post('/admin/email/test', [
            'test_email' => 'admin@example.com',
            'test_type' => 'follow_up',
        ]);
        $response2->assertRedirect('/admin/email');
        $response2->assertSessionHas('success');
        Mail::assertQueued(FollowUpInvitationEmail::class);

        // Test digest sample
        $response3 = $this->actingAs($admin)->post('/admin/email/test', [
            'test_email' => 'admin@example.com',
            'test_type' => 'digest',
        ]);
        $response3->assertRedirect('/admin/email');
        $response3->assertSessionHas('success');
        Mail::assertQueued(JobMatchesDigestEmail::class);
    }

    public function test_admin_can_send_custom_announcement_broadcast(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $user = User::factory()->create(['subscribed' => false]);

        $response = $this->actingAs($admin)->post('/admin/email/broadcast', [
            'audience' => 'all',
            'subject' => 'Important Site Announcement',
            'message' => 'Hello everyone, here is a site update.',
        ]);

        $response->assertRedirect('/admin/email');
        $response->assertSessionHas('success');

        Mail::assertQueued(MarketingEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
