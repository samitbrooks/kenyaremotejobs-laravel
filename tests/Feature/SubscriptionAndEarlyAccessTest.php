<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use App\Services\AiTailorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAndEarlyAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_page_renders_pro_membership_plans(): void
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
        $response->assertSee('Remote Career Accelerator');
        $response->assertSee('M-Pesa Supported');
        $response->assertSee('Free vs Pro Membership');
        $response->assertSee('Full Access to Apply to All 800+ Jobs');
        $response->assertSee('Pro Exclusive: Employers Actively Seeking Kenyan Talent');
        $response->assertSee('Included with Pro');
    }

    public function test_unsubscribed_visitor_can_view_jobs_and_job_details_with_gated_apply_action(): void
    {
        $job = JobListing::create([
            'id' => 'test-job-gated',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Remote Python Developer',
            'company' => 'PyCorp Ltd',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time Remote',
            'description' => 'Python developer needed.',
            'source_id' => 'py-1',
            'source_name' => 'RemoteOK',
            'source_url' => 'https://example.com/jobs/py-1',
            'posted_at' => now()->subDay(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'tags' => ['python'],
            'audience_segments' => [],
        ]);

        // Visiting /jobs returns 200 and shows listing
        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Remote Python Developer');

        // Visiting /jobs/{id} returns 200, displays role description, but gates the apply action
        $detailResponse = $this->get('/jobs/'.$job->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Remote Python Developer');
        $detailResponse->assertSee('PyCorp Ltd');
        $detailResponse->assertSee('Python developer needed.');
        $detailResponse->assertSee('Early Access');
        $detailResponse->assertDontSee('https://example.com/jobs/py-1');
    }

    public function test_subscribed_user_can_view_jobs_and_transparent_company_details(): void
    {
        $job = JobListing::create([
            'id' => 'test-job-1',
            'origin' => 'scraper',
            'tier' => 'basic',
            'title' => 'Senior Laravel Engineer',
            'company' => 'Acme Global Corp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time Remote',
            'description' => 'We are hiring a Senior Laravel Engineer to build cloud APIs.',
            'source_id' => '123',
            'source_name' => 'RemoteOK',
            'source_url' => 'https://example.com/jobs/123',
            'posted_at' => now()->subDays(3),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Global remote'],
            'tags' => ['laravel', 'php'],
            'audience_segments' => [],
        ]);

        $proUser = User::factory()->create([
            'subscribed' => true,
            'subscribed_at' => now(),
        ]);

        $response = $this->actingAs($proUser)->get('/jobs/'.$job->id);

        $response->assertStatus(200);
        $response->assertSee('Acme Global Corp');
        $response->assertSee('Apply Directly at Acme Global Corp');
        $response->assertSee('https://example.com/jobs/123');
    }

    public function test_fresh_job_listing_displays_early_access_for_subscribed_users(): void
    {
        $freshJob = JobListing::create([
            'id' => 'test-job-2',
            'origin' => 'scraper',
            'tier' => 'premium',
            'title' => 'DevOps Specialist',
            'company' => 'CloudScale Ltd',
            'location' => 'Anywhere',
            'remote_type' => 'Contract',
            'description' => 'Looking for AWS Kubernetes specialist.',
            'source_id' => '456',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/jobs/456',
            'posted_at' => now()->subHours(2), // 2 hours old -> in early access
            'kenya_friendly' => true,
            'kenya_score' => 85,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['devops', 'kubernetes'],
            'audience_segments' => [],
        ]);

        // Unauthenticated visitor can view job details, but apply action is gated behind Early Access
        $guestResponse = $this->get('/jobs/'.$freshJob->id);
        $guestResponse->assertStatus(200);
        $guestResponse->assertDontSee('Apply Directly at CloudScale Ltd');
        $guestResponse->assertSee('Early Access');

        // Subscribed Pro member can access and see early access indicator
        $proUser = User::factory()->create([
            'subscribed' => true,
            'subscribed_at' => now(),
        ]);

        $proResponse = $this->actingAs($proUser)->get('/jobs/'.$freshJob->id);
        $proResponse->assertStatus(200);
        $proResponse->assertSee('Pro Member Early Access Active');
        $proResponse->assertSee('Apply Directly at CloudScale Ltd');
    }

    public function test_direct_employer_listing_applications_accessible_to_pro_members(): void
    {
        $employerJob = JobListing::create([
            'id' => 'test-employer-job',
            'origin' => 'employer', // Direct submission
            'tier' => 'basic',
            'title' => 'Nairobi Remote Lead Engineer',
            'company' => 'KenyanFintech Co',
            'location' => 'Kenya / Remote',
            'remote_type' => 'Full-time',
            'description' => 'Direct employer seeking Kenyan software engineers.',
            'source_id' => 'emp-101',
            'source_name' => 'Direct Employer',
            'source_url' => 'https://kenyanfintech.com/careers/apply',
            'posted_at' => now()->subDays(10),
            'kenya_friendly' => true,
            'kenya_score' => 100,
            'kenya_reasons' => ['Direct employer in Kenya'],
            'tags' => ['engineering'],
            'audience_segments' => [],
        ]);

        // Free visitor can see job details, but apply action is gated
        $guestResponse = $this->get('/jobs/'.$employerJob->id);
        $guestResponse->assertStatus(200);
        $guestResponse->assertDontSee('https://kenyanfintech.com/careers/apply');

        // Pro member can access and apply directly
        $proUser = User::factory()->create([
            'subscribed' => true,
            'subscribed_at' => now(),
        ]);

        $proResponse = $this->actingAs($proUser)->get('/jobs/'.$employerJob->id);
        $proResponse->assertStatus(200);
        $proResponse->assertSee('Verified Direct Employer');
        $proResponse->assertSee('Apply Directly at KenyanFintech Co');
        $proResponse->assertSee('https://kenyanfintech.com/careers/apply');
    }

    public function test_user_can_track_applications_in_crm(): void
    {
        $user = User::factory()->create();
        $job = JobListing::create([
            'id' => 'test-job-3',
            'origin' => 'scraper',
            'tier' => 'basic',
            'title' => 'Customer Success Specialist',
            'company' => 'GlobalSupport Inc',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Help customers worldwide.',
            'source_id' => '789',
            'source_name' => 'Jobicy',
            'source_url' => 'https://example.com/jobs/789',
            'posted_at' => now()->subDays(5),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['support'],
            'audience_segments' => [],
        ]);

        $app = JobApplication::create([
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'status' => 'interviewing',
            'recruiter_contact' => 'recruiter@globalsupport.com',
            'notes' => 'Screening call completed. Technical round next Tuesday.',
            'applied_at' => now(),
        ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $app->id,
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'status' => 'interviewing',
        ]);

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
        $response->assertSee('Customer Success Specialist');
        $response->assertSee('GlobalSupport Inc');
        $response->assertSee('recruiter@globalsupport.com');
        $response->assertSee('Screening call completed');
    }

    public function test_ai_tailor_service_generates_resume_bullets_and_cover_letter(): void
    {
        $user = User::factory()->create(['free_tailors_remaining' => 1]);
        $this->assertTrue($user->canUseAiTailor());

        $job = JobListing::create([
            'id' => 'test-job-ai',
            'origin' => 'scraper',
            'tier' => 'basic',
            'title' => 'Remote Python Engineer',
            'company' => 'PyData Global',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Looking for Python, Django, AWS, and REST API specialist.',
            'source_id' => '999',
            'source_name' => 'RemoteOK',
            'source_url' => 'https://example.com/jobs/999',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['python', 'django', 'aws'],
            'audience_segments' => [],
        ]);

        $service = app(AiTailorService::class);
        $result = $service->tailor($job, ['role' => 'Software Engineer', 'skills' => ['python', 'fastapi']]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('match_score', $result);
        $this->assertArrayHasKey('tailored_bullets', $result);
        $this->assertArrayHasKey('tailored_cover_letter', $result);
        $this->assertArrayHasKey('kenya_advantage', $result);
        $this->assertGreaterThanOrEqual(80, $result['match_score']);

        $user->useAiTailor();
        $this->assertEquals(0, $user->free_tailors_remaining);
        $this->assertFalse($user->canUseAiTailor());
    }
}
