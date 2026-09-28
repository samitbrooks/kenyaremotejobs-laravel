<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAndJobsSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsubscribed_guest_is_redirected_to_pricing_when_navigating_to_jobs(): void
    {
        $job = JobListing::create([
            'id' => 'gated-job-1',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Software Engineer',
            'company' => 'Tech Corp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Software engineer needed for global remote team.',
            'source_id' => '101',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/apply',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Global'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);

        // Attempting to visit /jobs redirects to /pricing
        $response = $this->get('/jobs');
        $response->assertRedirect(route('pricing'));
        $response->assertSessionHas('info');

        // Attempting to visit /jobs/{id} redirects to /pricing
        $detailResponse = $this->get('/jobs/'.$job->id);
        $detailResponse->assertRedirect(route('pricing'));
        $detailResponse->assertSessionHas('info');
    }

    public function test_subscribed_user_can_navigate_to_jobs_and_job_details(): void
    {
        $job = JobListing::create([
            'id' => 'accessible-job-1',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Product Designer',
            'company' => 'Design Studio',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Product designer needed for global remote team.',
            'source_id' => '102',
            'source_name' => 'RemoteOK',
            'source_url' => 'https://example.com/apply-design',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 88,
            'kenya_reasons' => ['Global'],
            'tags' => ['design'],
            'audience_segments' => [],
        ]);

        $subscriber = User::factory()->create([
            'subscribed' => true,
            'subscribed_at' => now(),
        ]);

        $response = $this->actingAs($subscriber)->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Product Designer');

        $detailResponse = $this->actingAs($subscriber)->get('/jobs/'.$job->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Design Studio');
        $detailResponse->assertSee('Apply Directly at Design Studio');
    }

    public function test_guest_accessing_admin_is_redirected_to_admin_credentials_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login', ['next' => '/admin']));
        $response->assertSessionHas('info');
    }

    public function test_non_admin_user_is_denied_access_to_admin(): void
    {
        $regularUser = User::factory()->create([
            'email' => 'regular@example.com',
            'subscribed' => true,
        ]);

        $response = $this->actingAs($regularUser)->get('/admin');
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_access_admin_dashboard_and_view_all_jobs_and_sources(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com', // listed in config('jobs.admin_emails')
        ]);

        JobListing::create([
            'id' => 'job-himalayas-1',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Frontend Developer',
            'company' => 'Himalayas Tech',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Frontend developer description.',
            'source_id' => 'him-1',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/him-1',
            'posted_at' => now()->subHours(2),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['frontend'],
            'audience_segments' => [],
        ]);

        JobListing::create([
            'id' => 'job-remoteok-1',
            'origin' => 'synced',
            'tier' => 'intermediate',
            'title' => 'Backend Architect',
            'company' => 'RemoteOK Corp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Backend architect description.',
            'source_id' => 'rok-1',
            'source_name' => 'RemoteOK',
            'source_url' => 'https://example.com/rok-1',
            'posted_at' => now()->subDay(),
            'kenya_friendly' => false,
            'kenya_score' => 40,
            'kenya_reasons' => ['Limited'],
            'tags' => ['backend'],
            'audience_segments' => [],
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('All Job Listings');
        $response->assertSee('Frontend Developer');
        $response->assertSee('Backend Architect');
        $response->assertSee('Himalayas');
        $response->assertSee('RemoteOK');
    }

    public function test_admin_can_filter_jobs_by_source_on_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        JobListing::create([
            'id' => 'job-himalayas-filter',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Unique Himalayas Listing',
            'company' => 'Himalayas Co',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Unique Himalayas Listing description.',
            'source_id' => 'him-filter',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/him-filter',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);

        JobListing::create([
            'id' => 'job-jobicy-filter',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Unique Jobicy Listing',
            'company' => 'Jobicy Co',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Unique Jobicy Listing description.',
            'source_id' => 'jobicy-filter',
            'source_name' => 'Jobicy',
            'source_url' => 'https://example.com/jobicy-filter',
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);

        // Filter only Himalayas
        $response = $this->actingAs($admin)->get('/admin?source=Himalayas');
        $response->assertStatus(200);
        $response->assertSee('Unique Himalayas Listing');
        $response->assertDontSee('Unique Jobicy Listing');

        // Filter only Jobicy
        $responseJobicy = $this->actingAs($admin)->get('/admin?source=Jobicy');
        $responseJobicy->assertStatus(200);
        $responseJobicy->assertSee('Unique Jobicy Listing');
        $responseJobicy->assertDontSee('Unique Himalayas Listing');
    }

    public function test_admin_can_sort_jobs_by_posting_order(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $oldJob = JobListing::create([
            'id' => 'job-oldest',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Oldest Chronological Job',
            'company' => 'Old Corp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Oldest Chronological Job description.',
            'source_id' => 'old-1',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/old-1',
            'posted_at' => now()->subDays(20),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);

        $newJob = JobListing::create([
            'id' => 'job-newest',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Brand New Chronological Job',
            'company' => 'New Corp',
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'description' => 'Brand New Chronological Job description.',
            'source_id' => 'new-1',
            'source_name' => 'Himalayas',
            'source_url' => 'https://example.com/new-1',
            'posted_at' => now()->subMinutes(5),
            'kenya_friendly' => true,
            'kenya_score' => 90,
            'kenya_reasons' => ['Worldwide'],
            'tags' => ['tech'],
            'audience_segments' => [],
        ]);

        // Default: newest first
        $newestResponse = $this->actingAs($admin)->get('/admin?order=newest');
        $newestResponse->assertStatus(200);
        $newestContent = $newestResponse->getContent();
        $this->assertTrue(strpos($newestContent, 'Brand New Chronological Job') < strpos($newestContent, 'Oldest Chronological Job'));

        // Oldest first
        $oldestResponse = $this->actingAs($admin)->get('/admin?order=oldest');
        $oldestResponse->assertStatus(200);
        $oldestContent = $oldestResponse->getContent();
        $this->assertTrue(strpos($oldestContent, 'Oldest Chronological Job') < strpos($oldestContent, 'Brand New Chronological Job'));
    }
}
