<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Services\DirectAtsResolver;
use App\Services\JobSources\AshbySource;
use App\Services\JobSources\GreenhouseSource;
use App\Services\JobSources\HackerNewsSource;
use App\Services\JobSources\LeverSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DirectJobSourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_ashby_source_normalizes_remote_job_feed(): void
    {
        Http::fake([
            'https://api.ashbyhq.com/posting-api/job-board/*' => Http::response([
                'jobs' => [
                    [
                        'id' => 'ashby-job-123',
                        'title' => 'Senior Backend Engineer (Remote)',
                        'department' => 'Engineering',
                        'team' => 'Platform',
                        'employmentType' => 'FullTime',
                        'location' => 'Remote - Worldwide',
                        'isRemote' => true,
                        'workplaceType' => 'Remote',
                        'jobUrl' => 'https://jobs.ashbyhq.com/linear/ashby-job-123',
                        'descriptionHtml' => '<p>Build core infrastructure.</p>',
                        'publishedAt' => '2026-09-28T10:00:00Z',
                    ],
                    [
                        'id' => 'onsite-job-456',
                        'title' => 'Office Facilities Manager',
                        'isRemote' => false,
                        'workplaceType' => 'OnSite',
                        'location' => 'San Francisco, CA',
                    ],
                ],
            ], 200),
        ]);

        $source = new AshbySource;
        $jobs = $source->fetch();

        $this->assertNotEmpty($jobs);
        $first = $jobs[0];
        $this->assertEquals('Senior Backend Engineer (Remote)', $first['title']);
        $this->assertEquals('Remote', $first['remote_type']);
        $this->assertStringContainsString('ashby-', $first['id']);
        $this->assertStringContainsString('jobs.ashbyhq.com', $first['source_url']);
    }

    public function test_hacker_news_source_fetches_and_normalizes_remote_comments(): void
    {
        Http::fake([
            'https://hn.algolia.com/api/v1/search_by_date*' => Http::response([
                'hits' => [
                    [
                        'objectID' => '49522897',
                        'title' => 'Ask HN: Who is hiring? (September 2026)',
                    ],
                ],
            ], 200),
            'https://hn.algolia.com/api/v1/search*' => Http::response([
                'hits' => [
                    [
                        'objectID' => '99887766',
                        'comment_text' => '<p>Acme Corp | Staff Remote Engineer | REMOTE | Full-time | $140k</p><p>We are building global fintech infrastructure. Apply at https://acme.corp/jobs</p>',
                        'created_at' => '2026-09-28T14:30:00Z',
                    ],
                ],
            ], 200),
        ]);

        $source = new HackerNewsSource;
        $jobs = $source->fetch();

        $this->assertNotEmpty($jobs);
        $job = $jobs[0];
        $this->assertEquals('Acme Corp', $job['company']);
        $this->assertStringContainsString('Staff Remote Engineer', $job['title']);
        $this->assertEquals('Hacker News Direct', $job['source_name']);
        $this->assertEquals('https://news.ycombinator.com/item?id=99887766', $job['source_url']);
        $this->assertEquals('hn-49522897--99887766', $job['id']);
    }

    public function test_greenhouse_source_fetches_canonical_and_expanded_boards(): void
    {
        Http::fake([
            'https://api.greenhouse.io/v1/boards/*' => Http::response([
                'jobs' => [
                    [
                        'id' => 771122,
                        'title' => 'Cloud Infrastructure Engineer (EMEA Remote)',
                        'absolute_url' => 'https://boards.greenhouse.io/canonical/jobs/771122',
                        'location' => ['name' => 'Home Based - EMEA (Remote)'],
                        'departments' => [['name' => 'Engineering']],
                        'content' => 'Join Canonical building Ubuntu open-source systems.',
                        'updated_at' => '2026-09-28T09:00:00Z',
                    ],
                ],
            ], 200),
        ]);

        $source = new GreenhouseSource;
        $jobs = $source->fetch();

        $this->assertNotEmpty($jobs);
        $first = $jobs[0];
        $this->assertStringContainsString('greenhouse-', $first['id']);
        $this->assertEquals('Remote', $first['remote_type']);
        $this->assertStringContainsString('canonical', $first['source_url']);
    }

    public function test_lever_source_normalizes_remote_job_feed(): void
    {
        Http::fake([
            'https://api.lever.co/v0/postings/*' => Http::response([
                [
                    'id' => 'lever-job-789',
                    'text' => 'Content Marketing Strategist (Remote)',
                    'workplaceType' => 'remote',
                    'hostedUrl' => 'https://jobs.lever.co/brafton/lever-job-789',
                    'applyUrl' => 'https://jobs.lever.co/brafton/lever-job-789/apply',
                    'description' => '<p>Write high-impact marketing and technical copy.</p>',
                    'categories' => [
                        'location' => 'Global Remote',
                        'commitment' => 'Full-time',
                        'team' => 'Content',
                        'department' => 'Marketing',
                    ],
                    'createdAt' => 1791400000000,
                ],
            ], 200),
        ]);

        $source = new LeverSource;
        $jobs = $source->fetch();

        $this->assertNotEmpty($jobs);
        $first = $jobs[0];
        $this->assertEquals('Content Marketing Strategist (Remote)', $first['title']);
        $this->assertStringContainsString('lever-', $first['id']);
        $this->assertEquals('https://jobs.lever.co/brafton/lever-job-789/apply', $first['source_url']);
        $this->assertEquals('Remote', $first['remote_type']);
    }

    public function test_direct_ats_resolver_and_model_methods(): void
    {
        $this->assertTrue(DirectAtsResolver::isDirectUrl('https://boards.greenhouse.io/canonical/jobs/123'));
        $this->assertTrue(DirectAtsResolver::isDirectUrl('https://jobs.ashbyhq.com/writer/456'));
        $this->assertTrue(DirectAtsResolver::isDirectUrl('https://jobs.lever.co/superside/789/apply'));
        $this->assertTrue(DirectAtsResolver::isAggregatorUrl('https://remoteok.com/remote-jobs/123'));
        $this->assertTrue(DirectAtsResolver::isAggregatorUrl('https://jobicy.com/jobs/456'));

        // Resolution from description
        $descWithAts = '<p>To apply, visit our direct portal: https://boards.greenhouse.io/acme/jobs/999 and submit your CV.</p>';
        $resolved = DirectAtsResolver::resolve('https://remoteok.com/remote-jobs/123', $descWithAts);
        $this->assertEquals('https://boards.greenhouse.io/acme/jobs/999', $resolved);

        // JobListing model methods
        $directJob = new JobListing([
            'source_url' => 'https://jobs.ashbyhq.com/linear/123',
            'origin' => 'synced',
        ]);
        $this->assertTrue($directJob->isDirectAts());
        $this->assertFalse($directJob->isAggregator());
        $this->assertEquals('https://jobs.ashbyhq.com/linear/123', $directJob->directApplyUrl());

        $aggregatorJob = new JobListing([
            'source_url' => 'https://remoteok.com/remote-jobs/777',
            'description' => '<p>Submit application at https://jobs.lever.co/acme/888/apply directly.</p>',
            'origin' => 'synced',
        ]);
        $this->assertTrue($aggregatorJob->isAggregator());
        $this->assertEquals('https://jobs.lever.co/acme/888/apply', $aggregatorJob->directApplyUrl());
    }

    public function test_just_posted_section_redacts_company_for_unauthenticated_guests(): void
    {
        $job = JobListing::create([
            'id' => 'test-just-posted-1',
            'title' => 'Lead Technical Writer',
            'company' => 'SecretTech Corp',
            'source_name' => 'Direct',
            'source_id' => 'sec-1',
            'source_url' => 'https://boards.greenhouse.io/secret/1',
            'description' => 'Write documentation for remote teams.',
            'tags' => ['writing', 'technical'],
            'location' => 'Remote Worldwide',
            'remote_type' => 'Remote',
            'posted_at' => now()->subMinutes(10),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['East Africa Time overlap'],
            'audience_segments' => ['writing'],
            'tier' => 'standard',
            'origin' => 'synced',
        ]);

        $response = $this->get('/');
        $response->assertOk();
        // The company name SecretTech Corp must NOT be visible to guest in Just Posted or feed
        $response->assertDontSee('SecretTech Corp');
        $response->assertSee('Verified Employer');
        $response->assertSee('Pro Only');
    }
}
