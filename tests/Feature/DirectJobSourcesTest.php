<?php

namespace Tests\Feature;

use App\Services\JobSources\AshbySource;
use App\Services\JobSources\GreenhouseSource;
use App\Services\JobSources\HackerNewsSource;
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
}
