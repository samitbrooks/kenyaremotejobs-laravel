<?php

namespace Tests\Feature;

use App\Mail\JobMatchesDigestEmail;
use App\Mail\ProJobRecommendationsEmail;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\User;
use App\Services\JobRecommendationService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProJobRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        JobListing::create([
            'id' => 'pro-job-1',
            'origin' => 'synced',
            'tier' => 'premium',
            'title' => 'Senior Cloud Architect',
            'company' => 'Acme Cloud Global',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Architect cloud infrastructure for globally distributed platforms.',
            'source_id' => 'acme-1',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/jobs/acme-1',
            'posted_at' => now(),
            'annual_salary_usd' => 120_000,
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote', 'East Africa timezone compatible'],
            'tags' => ['AWS', 'DevOps', 'Cloud'],
            'audience_segments' => ['tech'],
        ]);
    }

    public function test_subscribed_user_receives_pro_job_recommendations_email_instead_of_standard_digest(): void
    {
        Mail::fake();

        $subscriber = User::factory()->create([
            'name' => 'David Ochieng',
            'email' => 'david@example.com',
            'subscribed' => true,
            'subscribed_at' => now(),
        ]);

        $recommendationService = app(JobRecommendationService::class);
        $ok = $recommendationService->sendDigestToUser($subscriber, force: true, immediate: true);

        $this->assertTrue($ok);

        // Ensure Pro email was queued, not the standard free digest
        Mail::assertQueued(ProJobRecommendationsEmail::class, function ($mail) use ($subscriber) {
            return $mail->hasTo($subscriber->email)
                && str_contains($mail->envelope()->subject, 'VIP Pro Alert');
        });
        Mail::assertNotQueued(JobMatchesDigestEmail::class);
    }

    public function test_non_subscribed_user_receives_standard_job_matches_digest(): void
    {
        Mail::fake();

        $freeUser = User::factory()->create([
            'name' => 'Jane Wanjiku',
            'email' => 'jane@example.com',
            'subscribed' => false,
        ]);

        $recommendationService = app(JobRecommendationService::class);
        $ok = $recommendationService->sendDigestToUser($freeUser, force: true, immediate: true);

        $this->assertTrue($ok);

        Mail::assertQueued(JobMatchesDigestEmail::class, function ($mail) use ($freeUser) {
            return $mail->hasTo($freeUser->email);
        });
        Mail::assertNotQueued(ProJobRecommendationsEmail::class);
    }

    public function test_pro_recommendations_email_content_contains_curated_jobs_and_concierge_retention_services(): void
    {
        $subscriber = User::factory()->create([
            'name' => 'David Ochieng',
            'email' => 'david@example.com',
            'subscribed' => true,
        ]);

        $jobs = app(JobRecommendationService::class)->getProRecommendedJobsForUser($subscriber);
        $mailable = new ProJobRecommendationsEmail($subscriber, $jobs, 1);

        $rendered = $mailable->render();

        // Verifies real-time matched jobs are present
        $this->assertStringContainsString('Senior Cloud Architect', $rendered);
        $this->assertStringContainsString('Acme Cloud Global', $rendered);

        // Verifies the "What We Can Do For You" retention concierge services are present
        $this->assertStringContainsString('What Our VIP Concierge Team Can Do For You', $rendered);
        $this->assertStringContainsString('1-on-1 CV Optimization', $rendered);
        $this->assertStringContainsString('Custom Niche Role Scouting', $rendered);
        $this->assertStringContainsString('Tailored Application Pitches', $rendered);
        $this->assertStringContainsString('Employer Priority Shortlisting', $rendered);
        $this->assertStringContainsString('Direct VIP Desk Support', $rendered);
        $this->assertStringContainsString('PRO VIP SUBSCRIBER', $rendered);
    }

    public function test_paying_subscription_fulfillment_triggers_pro_recommendations_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'paying-client@example.com',
            'subscribed' => false,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'mpesa',
            'purpose' => 'subscription',
            'payload' => ['period' => 'monthly'],
            'amount_kes' => 299,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $paymentService = app(PaymentService::class);
        $paymentService->fulfill($payment);

        $user->refresh();
        $this->assertTrue($user->subscribed);

        Mail::assertQueued(ProJobRecommendationsEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_jobs_send_pro_alerts_command_dispatches_to_active_subscribers(): void
    {
        Mail::fake();

        $subscriber = User::factory()->create([
            'email' => 'pro-user@example.com',
            'subscribed' => true,
        ]);

        $this->artisan('jobs:send-pro-alerts', ['--immediate' => true])
            ->assertSuccessful();

        Mail::assertQueued(ProJobRecommendationsEmail::class, function ($mail) use ($subscriber) {
            return $mail->hasTo($subscriber->email);
        });
    }
}
