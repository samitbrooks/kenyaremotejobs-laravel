<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Services\GoogleIndexingService;
use Database\Seeders\SeoPillarContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRichResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobs_page_canonical_is_self_referencing_on_paginated_pages(): void
    {
        // Seed enough visible jobs so totalPages > 1
        for ($i = 1; $i <= 25; $i++) {
            JobListing::create([
                'id' => 'all-job-'.$i,
                'source_name' => 'direct',
                'source_id' => 'job-'.$i,
                'source_url' => 'https://example.com/apply/'.$i,
                'title' => 'Remote Engineer '.$i,
                'company' => 'Global Corp '.$i,
                'description' => 'Great remote engineering role.',
                'tags' => ['engineering'],
                'location' => 'Worldwide',
                'remote_type' => 'full_time',
                'posted_at' => now()->subMinutes($i),
                'kenya_friendly' => true,
                'kenya_score' => 90,
                'kenya_reasons' => ['Open to Kenyan applicants'],
                'audience_segments' => ['tech'],
                'origin' => 'employer',
                'tier' => 'regular',
            ]);
        }

        $responsePage1 = $this->get('/jobs');
        $responsePage1->assertStatus(200);
        $responsePage1->assertSee('<link rel="canonical" href="'.url('/jobs').'">', false);

        $responsePage2 = $this->get('/jobs?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('<link rel="canonical" href="'.url('/jobs').'?page=2">', false);
    }

    public function test_jobs_page_renders_item_list_and_breadcrumb_schemas(): void
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('"@type":"ItemList"', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"name":"Remote Jobs"', false);
    }

    public function test_category_landing_page_canonical_is_self_referencing_and_has_schemas(): void
    {
        // Seed enough matching jobs so totalPages > 1
        for ($i = 1; $i <= 25; $i++) {
            JobListing::create([
                'id' => 'dev-job-'.$i,
                'source_name' => 'direct',
                'source_id' => 'dev-'.$i,
                'source_url' => 'https://example.com/apply/'.$i,
                'title' => 'Software Developer '.$i,
                'company' => 'Tech Corp '.$i,
                'description' => 'Great software development job for Kenya.',
                'tags' => ['developer', 'software'],
                'location' => 'Worldwide',
                'remote_type' => 'full_time',
                'posted_at' => now()->subMinutes($i),
                'kenya_friendly' => true,
                'kenya_score' => 90,
                'kenya_reasons' => ['Open to Kenyan developers'],
                'audience_segments' => ['tech'],
                'origin' => 'employer',
                'tier' => 'regular',
            ]);
        }

        $response = $this->get('/remote-jobs/software-developer-kenya');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="'.url('/remote-jobs/software-developer-kenya').'">', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"@type":"ItemList"', false);
        $response->assertSee('"@type":"FAQPage"', false);

        $responsePage2 = $this->get('/remote-jobs/software-developer-kenya?page=2');
        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('<link rel="canonical" href="'.url('/remote-jobs/software-developer-kenya').'?page=2">', false);
    }

    public function test_job_detail_page_has_valid_job_posting_and_breadcrumb_rich_results(): void
    {
        $job = JobListing::create([
            'id' => 'test-rich-results-job',
            'source_name' => 'direct',
            'source_id' => '1001',
            'source_url' => 'https://example.com/apply',
            'title' => 'Senior Laravel Engineer',
            'company' => 'Automattic',
            'description' => '<p>Exciting remote opportunity open to developers in Kenya.</p>',
            'tags' => ['PHP', 'Laravel', 'Vue.js'],
            'location' => 'Worldwide',
            'remote_type' => 'full_time',
            'annual_salary_usd' => ['min' => 60000, 'max' => 90000],
            'posted_at' => now()->subDay(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Remote Worldwide - Kenya eligible'],
            'audience_segments' => ['tech'],
            'origin' => 'employer',
            'tier' => 'featured',
        ]);

        $response = $this->get('/jobs/'.$job->id);

        $response->assertStatus(200);
        $response->assertSee('"@type":"JobPosting"', false);
        $response->assertSee('"jobLocationType":"TELECOMMUTE"', false);
        $response->assertSee('"employmentType":"FULL_TIME"', false);
        $response->assertSee('"applicantLocationRequirements"', false);
        $response->assertSee('"jobLocation"', false);
        $response->assertSee('"directApply":true', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_layout_organization_schema_includes_logo_and_social(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('"@type":"Organization"', false);
        $response->assertSee('"@type":"ImageObject"', false);
        $response->assertSee('/images/logo.png', false);
        $response->assertSee('"@type":"WebSite"', false);
    }

    public function test_directory_index_pages_have_breadcrumb_and_list_schemas(): void
    {
        $this->seed(SeoPillarContentSeeder::class);

        // Companies index
        $compResponse = $this->get('/companies');
        $compResponse->assertStatus(200);
        $compResponse->assertSee('"@type":"BreadcrumbList"', false);
        $compResponse->assertSee('"@type":"ItemList"', false);
        $compResponse->assertSee('Top Global Remote Companies Hiring in Kenya', false);

        // Collections index
        $colResponse = $this->get('/collections');
        $colResponse->assertStatus(200);
        $colResponse->assertSee('"@type":"BreadcrumbList"', false);
        $colResponse->assertSee('"@type":"ItemList"', false);
        $colResponse->assertSee('Curated Remote Job Collections for Kenya', false);

        // Journal index
        $journalResponse = $this->get('/journal');
        $journalResponse->assertStatus(200);
        $journalResponse->assertSee('"@type":"BreadcrumbList"', false);
        $journalResponse->assertSee('"@type":"Blog"', false);

        // FAQs
        $faqsResponse = $this->get('/faqs');
        $faqsResponse->assertStatus(200);
        $faqsResponse->assertSee('"@type":"BreadcrumbList"', false);
        $faqsResponse->assertSee('"@type":"FAQPage"', false);
    }

    public function test_google_indexing_command_dry_run_executes_successfully(): void
    {
        $this->artisan('seo:google-index', ['--dry-run' => true, '--limit' => 3])
            ->expectsOutputToContain('Preparing to submit')
            ->expectsOutputToContain('Running in --dry-run mode')
            ->assertExitCode(0);
    }

    public function test_google_indexing_service_returns_not_configured_when_no_credentials(): void
    {
        $service = new GoogleIndexingService;
        $this->assertFalse($service->isConfigured());

        $result = $service->publishUrl('https://kenyaremotejobs.com/jobs/123');
        $this->assertFalse($result['success']);
        $this->assertSame(401, $result['status']);
    }
}
