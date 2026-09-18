<?php

namespace Tests\Feature;

use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_rss_feed_returns_valid_xml(): void
    {
        $response = $this->get('/feed/featured.rss');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $response->assertSee('<rss version="2.0"', false);
        $response->assertSee('<channel>', false);
        $response->assertSee(config('site.name'), false);
    }

    public function test_featured_rss_feed_includes_kenya_friendly_jobs_with_social_copy(): void
    {
        $friendlyJob = JobListing::create([
            'id' => 'friendly-test-job-1',
            'source_name' => 'direct',
            'source_id' => '1001',
            'source_url' => 'https://example.com/job-1',
            'title' => 'Senior Support Specialist',
            'company' => 'Acme Global',
            'description' => 'Great remote role for customer advocates living in Kenya.',
            'tags' => ['Support', 'Remote'],
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'salary' => '$2,500/mo',
            'annual_salary_usd' => ['min' => 30000, 'max' => 36000],
            'posted_at' => now(),
            'kenya_friendly' => true,
            'kenya_score' => 6,
            'kenya_reasons' => ['East Africa Time overlap', 'No US visa required'],
            'audience_segments' => ['customer-support'],
            'origin' => 'synced',
            'tier' => 'standard',
        ]);

        $unfriendlyJob = JobListing::create([
            'id' => 'unfriendly-test-job-2',
            'source_name' => 'direct',
            'source_id' => '1002',
            'source_url' => 'https://example.com/job-2',
            'title' => 'US Only Tax Attorney',
            'company' => 'US Only Corp',
            'description' => 'Requires US citizenship and residing in California.',
            'tags' => ['Legal'],
            'location' => 'United States Only',
            'remote_type' => 'Full-time',
            'salary' => '$100,000/yr',
            'annual_salary_usd' => ['min' => 100000, 'max' => 100000],
            'posted_at' => now(),
            'kenya_friendly' => false,
            'kenya_score' => 1,
            'kenya_reasons' => ['Strict US residency required'],
            'audience_segments' => ['finance-legal'],
            'origin' => 'synced',
            'tier' => 'standard',
        ]);

        $response = $this->get('/feed/featured.rss');

        $response->assertStatus(200);
        $response->assertSee('Senior Support Specialist at Acme Global', false);
        $response->assertDontSee('US Only Tax Attorney', false);
        $response->assertSee('utm_source=linkedin', false);
        $response->assertSee('NEW VERIFIED REMOTE ROLE', false);
        $response->assertSee('#RemoteJobsKenya', false);
        $response->assertSee('<guid isPermaLink="true">', false);
    }

    public function test_feed_aliases_work(): void
    {
        $this->get('/feed/jobs.rss')->assertStatus(200);
        $this->get('/feed/featured-jobs.rss')->assertStatus(200);
    }
}
