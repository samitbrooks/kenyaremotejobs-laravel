<?php

namespace Tests\Feature;

use App\Mail\JobMatchesDigestEmail;
use App\Models\JobListing;
use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class JobMatchesDigestTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleJob(string $id, string $title, string $company): JobListing
    {
        return JobListing::create([
            'id' => $id,
            'origin' => 'employer',
            'tier' => 'basic',
            'title' => $title,
            'company' => $company,
            'location' => 'Worldwide',
            'remote_type' => 'Full-time Remote',
            'description' => 'Test job description for remote role.',
            'source_id' => 'sample-'.$id,
            'source_name' => 'Internal',
            'source_url' => 'https://example.com/jobs/'.$id,
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global Remote'],
            'tags' => ['remote', 'engineering'],
            'audience_segments' => [],
        ]);
    }

    public function test_job_matches_digest_mailable_renders_dark_theme_and_headers(): void
    {
        $user = User::factory()->create([
            'name' => 'Sammie Kamau',
            'email' => 'sammie@example.com',
        ]);

        $job = $this->createSampleJob('job-1', 'Recruiting & Candidate Coordination Virtual Assistant', 'Remote Talent Co');

        $mailable = new JobMatchesDigestEmail($user, collect([$job]), 167);

        $mailable->assertHasSubject('Sammie, 167 open roles for you');
        $mailable->assertSeeInHtml('JOB MATCHES');
        $mailable->assertSeeInHtml('New roles for you, Sammie.');
        $mailable->assertSeeInHtml('Recruiting &amp; Candidate Coordination Virtual Assistant', false);
        $mailable->assertSeeInHtml('Remote Talent Co');
        $mailable->assertSeeInHtml('View All 167 Roles');
        $mailable->assertSeeInHtml('Does your CV meet remote-work hiring standards?');
        $mailable->assertSeeInHtml('Evaluate your CV');

        $headers = $mailable->headers();
        $this->assertNotNull($headers);
        $this->assertArrayHasKey('List-Unsubscribe', $headers->text);
        $this->assertArrayHasKey('List-Unsubscribe-Post', $headers->text);
    }

    public function test_job_recommendation_service_respects_marketing_opt_out(): void
    {
        Mail::fake();

        $this->createSampleJob('job-2', 'Customer Support Specialist', 'Acme Support');

        $optedOutUser = User::factory()->create([
            'email' => 'optout@example.com',
            'marketing_opt_out_at' => now(),
        ]);

        $service = app(JobRecommendationService::class);
        $sent = $service->sendDigestToUser($optedOutUser);

        $this->assertFalse($sent);
        Mail::assertNothingSent();

        // Forced sending (e.g. admin test preview) ignores opt-out
        $sentForced = $service->sendDigestToUser($optedOutUser, force: true);
        $this->assertTrue($sentForced);
        Mail::assertSent(JobMatchesDigestEmail::class, function ($mail) use ($optedOutUser) {
            return $mail->hasTo($optedOutUser->email);
        });
    }

    public function test_send_digest_artisan_command_dry_run_and_execution(): void
    {
        Mail::fake();

        $this->createSampleJob('job-3', 'Virtual Assistant (Outreach and Engagement)', 'Growth Engine');

        $activeUser = User::factory()->create([
            'email' => 'active@example.com',
            'marketing_opt_out_at' => null,
            'last_job_digest_at' => null,
        ]);

        // Dry run mode
        $this->artisan('jobs:send-digest', ['--dry-run' => true])
            ->expectsOutputToContain('DRY RUN MODE')
            ->assertExitCode(0);

        Mail::assertNothingSent();

        // Send to specific user via flag
        $this->artisan('jobs:send-digest', [
            '--user' => $activeUser->id,
            '--force' => true,
        ])->assertExitCode(0);

        Mail::assertSent(JobMatchesDigestEmail::class, function ($mail) use ($activeUser) {
            return $mail->hasTo($activeUser->email);
        });

        $this->assertNotNull($activeUser->fresh()->last_job_digest_at);
    }

    public function test_new_user_registration_dispatches_initial_digest(): void
    {
        Mail::fake();

        $this->createSampleJob('job-4', 'Full-Cycle Sales Representative', 'Global Sales Inc');

        $response = $this->post('/account/login', [
            'email' => 'newuser@example.com',
            'name' => 'Wanjiku Mwangi',
        ]);

        $response->assertRedirect('/account');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);

        Mail::assertSent(JobMatchesDigestEmail::class, function ($mail) {
            return $mail->hasTo('newuser@example.com')
                && str_contains($mail->envelope()->subject, 'Wanjiku');
        });
    }
}
