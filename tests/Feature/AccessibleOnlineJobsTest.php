<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use App\Services\DirectAtsResolver;
use App\Services\JobSources\CuratedStudentJobsSource;
use App\Services\JobSources\LeverSource;
use App\Services\JobSources\Micro1Source;
use App\Services\JobSources\WorkableSource;
use App\Services\JobSyncService;
use App\Support\KenyaRelevance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AccessibleOnlineJobsTest extends TestCase
{
    use RefreshDatabase;

    private function job(string $id, array $attributes = []): JobListing
    {
        return JobListing::create(array_merge([
            'id' => $id, 'source_name' => 'Employer', 'source_id' => $id,
            'source_url' => 'https://jobs.ashbyhq.com/private-employer/'.$id.'/application',
            'title' => 'Customer Support Representative', 'company' => 'Private Employer Ltd',
            'description' => 'Answer customer questions by email and chat.', 'tags' => ['Customer Support'],
            'location' => 'Remote — Kenya', 'remote_type' => 'Remote', 'posted_at' => now(),
            'kenya_friendly' => true, 'kenya_score' => 3, 'kenya_reasons' => [],
            'origin' => 'synced', 'tier' => 'basic', 'audience_segments' => [],
        ], $attributes));
    }

    public function test_homepage_prioritises_practical_online_work_without_removing_specialist_catalogue(): void
    {
        $senior = $this->job('senior', ['title' => 'Senior AI Research Engineer', 'tags' => ['AI Training', 'Students'], 'audience_segments' => ['students']]);
        $support = $this->job('support');

        $this->get('/')->assertViewHas('feed', fn ($feed): bool => $feed->modelKeys() === [$support->id]);
        $this->get('/jobs')->assertViewHas('jobs', fn ($jobs): bool => $jobs->modelKeys() === [$support->id, $senior->id]);
    }

    #[TestWith(['PhD in physics required.'])]
    #[TestWith(['At least 7 years of professional experience required.'])]
    public function test_specialist_requirements_do_not_become_beginner_friendly_through_annotation_tags(string $requirements): void
    {
        $job = $this->job('advanced', ['title' => 'AI Data Annotator', 'description' => $requirements, 'tags' => ['Students', 'AI Training']]);

        $this->assertFalse($job->isAccessibleOnlineWork());
        $this->get('/jobs?onlineWork=true')->assertViewHas('total', 0);
    }

    public function test_direct_filter_uses_resolved_employer_links_and_excludes_other_boards_and_hiring_platforms(): void
    {
        $direct = $this->job('direct');
        $resolved = $this->job('resolved', ['source_url' => 'https://remoteok.com/role', 'description' => 'Apply at https://jobs.lever.co/employer/123/apply']);
        $this->job('aggregator', ['source_url' => 'https://remoteok.com/other-role']);
        $this->job('platform', ['source_url' => 'https://work.turing.com']);
        $this->job('senior', ['title' => 'Principal Software Engineer']);

        $response = $this->get('/jobs?onlineWork=true&directApply=true&kenyaFriendly=true');

        $response->assertViewHas('total', 2)->assertViewHas('jobs', fn ($jobs): bool => $jobs->modelKeys() === [$direct->id, $resolved->id])
            ->assertDontSee('Private Employer Ltd')->assertDontSee('https://jobs.lever.co/employer/123/apply');
    }

    #[TestWith(['https://greenhouse.io.evil.example/apply'])]
    #[TestWith(['https://evil.example/greenhouse.io/application'])]
    #[TestWith(['https://jobs.ashbyhq.com@evil.example/apply'])]
    #[TestWith(['javascript:greenhouse.io'])]
    public function test_application_host_checks_reject_domain_spoofing(string $url): void
    {
        $this->assertFalse(DirectAtsResolver::isDirectUrl($url));
        $this->assertNull(DirectAtsResolver::extractDirectLink('<a href="'.$url.'">Apply</a>'));
    }

    public function test_job_board_urls_are_not_treated_as_employer_application_pages(): void
    {
        $this->assertTrue(DirectAtsResolver::isAggregatorUrl('https://jobs.workable.com/view/123'));
        $this->assertFalse(DirectAtsResolver::isEmployerApplicationUrl('https://jobs.workable.com/view/123'));
        $this->assertTrue(DirectAtsResolver::isEmployerApplicationUrl('https://apply.workable.com/employer/j/123/apply/'));
        $this->assertSame('https://apply.workable.com/employer/j/123/apply/?a=1&b=2', DirectAtsResolver::extractDirectLink('<a href="https://apply.workable.com/employer/j/123/apply/?a=1&amp;b=2">Apply</a>'));
    }

    public function test_current_employer_feed_keeps_an_older_open_role_visible_without_changing_posted_date(): void
    {
        $this->freezeTime();
        $job = $this->job('long-running', ['posted_at' => now()->subMonths(4), 'last_seen_at' => now()]);
        $user = User::factory()->create(['subscribed' => true]);

        $this->get('/jobs')->assertViewHas('jobs', fn ($jobs): bool => $jobs->contains('id', $job->id));
        $this->actingAs($user)->get('/jobs/'.$job->id)->assertViewHas('canApply', true)->assertSee('Last seen in the live employer feed');
        $this->travel(37)->hours();
        $this->get('/jobs')->assertViewHas('total', 0);
        $this->get('/jobs/'.$job->id)->assertViewHas('isExpired', true)->assertViewHas('canApply', false);
    }

    #[TestWith(['Remote — Kenya', true])]
    #[TestWith(['Nairobi, Kenya', true])]
    #[TestWith(['Remote (Africa)', true])]
    #[TestWith(['Remote (South Africa)', false])]
    #[TestWith(['Remote — Nigeria', false])]
    public function test_location_scoring_does_not_confuse_another_african_country_with_kenya(string $location, bool $expected): void
    {
        $signals = KenyaRelevance::score(['location' => $location, 'description' => 'Flexible hours.']);

        $this->assertSame($expected, $signals['kenyaFriendly']);
    }

    public function test_retired_student_source_no_longer_generates_vacancies(): void
    {
        $this->assertSame([], (new CuratedStudentJobsSource)->fetch());
    }

    public function test_workable_imports_only_published_remote_kenya_roles_and_deduplicates_locations(): void
    {
        $this->freezeTime();
        Http::preventStrayRequests();
        $listing = ['shortcode' => 'KE123', 'title' => 'Customer Support Consultant', 'telecommuting' => true, 'country' => 'Kenya', 'published_on' => '2026-05-01', 'experience' => 'Entry level'];
        Http::fake([
            'https://apply.workable.com/api/v1/widget/accounts/supportyourapp' => Http::response(['jobs' => [
                $listing, $listing,
                [...$listing, 'shortcode' => 'US123', 'country' => 'United States'],
                [...$listing, 'shortcode' => 'OFFICE', 'telecommuting' => false],
                [...$listing, 'shortcode' => 'CLOSED'],
            ]]),
            'https://apply.workable.com/api/v1/widget/accounts/cloudfactory' => Http::response(['jobs' => []]),
            'https://apply.workable.com/api/v2/accounts/supportyourapp/jobs/KE123' => Http::response([
                'state' => 'published', 'title' => 'Customer Support Consultant', 'published' => '2026-05-01T12:00:00Z',
                'description' => '<p>Answer customer questions.</p>', 'requirements' => '<p>Written English.</p>', 'benefits' => '',
            ]),
            'https://apply.workable.com/api/v2/accounts/supportyourapp/jobs/CLOSED' => Http::response(['state' => 'archived']),
        ]);

        $jobs = (new WorkableSource)->fetch();

        $this->assertCount(1, $jobs);
        $this->assertSame('workable-supportyourapp--KE123', $jobs[0]['id']);
        $this->assertSame('https://apply.workable.com/supportyourapp/j/KE123/apply/', $jobs[0]['source_url']);
        $this->assertSame('2026-05-01', $jobs[0]['posted_at']->toDateString());
        $this->assertTrue($jobs[0]['last_seen_at']->equalTo(now()));
        $this->assertNull($jobs[0]['salary']);
        $this->assertStringContainsString('Written English.', $jobs[0]['description']);
        Http::assertSentCount(4);
    }

    public function test_workable_failure_does_not_create_fallback_vacancies(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://apply.workable.com/api/v1/widget/accounts/*' => Http::response([], 503)]);

        $this->assertSame([], (new WorkableSource)->fetch());
        Http::assertSentCount(2);
    }

    public function test_sync_retires_synthetic_listings_but_preserves_recently_confirmed_jobs_during_partial_outages(): void
    {
        $this->freezeTime();
        config(['services.google_indexing.credentials_json' => null, 'services.google_indexing.key_path' => null]);
        Http::preventStrayRequests();
        $fresh = $this->job('fresh-missing', ['posted_at' => now()->subMonths(2), 'last_seen_at' => now()]);
        $stale = $this->job('stale-missing', ['posted_at' => now()->subMonths(2), 'last_seen_at' => now()->subHours(37)]);
        $synthetic = $this->job('student--synthetic');
        $manual = $this->job('manual', ['origin' => 'manual']);
        Http::fake([
            'https://api.ashbyhq.com/posting-api/job-board/clipboard' => Http::response(['jobs' => [[
                'id' => 'incoming', 'title' => 'Customer Support Representative', 'isRemote' => true,
                'location' => 'Remote (Africa)', 'publishedAt' => now()->subMonths(4)->toIso8601String(),
                'applyUrl' => 'https://jobs.ashbyhq.com/clipboard/incoming/application',
                'descriptionHtml' => '<p>Answer customer questions.</p>',
            ]]]),
            '*' => Http::response(['jobs' => [], 'data' => [], 'hits' => []]),
        ]);

        $this->assertSame(1, app(JobSyncService::class)->sync());

        $this->assertModelExists($fresh);
        $this->assertModelExists($manual);
        $this->assertModelMissing($stale);
        $this->assertModelMissing($synthetic);
        $this->assertDatabaseHas('job_listings', ['id' => 'ashby-clipboard--incoming', 'company' => 'Clipboard']);
        $this->assertSame(1, Http::recorded(fn ($request) => str_contains($request->url(), '/job-board/clipboard'))->count());
    }

    public function test_micro1_api_failure_does_not_republish_bundled_snapshot(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://prod-api.micro1.ai/api/v1/job/portal*' => Http::response([], 503)]);

        $this->expectException(\RuntimeException::class);
        (new Micro1Source)->fetch();
    }

    public function test_lever_preserves_requirements_so_advanced_roles_are_not_mistaken_for_easy_annotation_work(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://api.lever.co/v0/postings/*' => Http::response([[
            'id' => 'specialist', 'text' => 'Data Annotator', 'workplaceType' => 'remote',
            'categories' => ['location' => 'Remote (Kenya)'],
            'description' => '<p>Annotate research data.</p>',
            'lists' => [['text' => 'Requirements', 'content' => '<li>PhD in physics required.</li>']],
            'applyUrl' => 'https://jobs.lever.co/employer/specialist/apply',
        ]])]);

        $jobs = (new LeverSource)->fetch();

        $this->assertNotEmpty($jobs);
        $this->assertStringContainsString('PhD in physics required.', $jobs[0]['description']);
        Http::assertSentCount(5);
    }

    public function test_focused_refresh_imports_live_employer_roles_without_replacing_other_sources(): void
    {
        Http::preventStrayRequests();
        $other = $this->job('other-source');
        $synthetic = $this->job('student--synthetic');
        Http::fake([
            'https://api.ashbyhq.com/posting-api/job-board/clipboard' => Http::response(['jobs' => [[
                'id' => 'new-support', 'title' => 'Customer Experience Specialist', 'isRemote' => true,
                'location' => 'Remote (Africa)', 'publishedAt' => now()->subMonths(4)->toIso8601String(),
                'applyUrl' => 'https://jobs.ashbyhq.com/clipboard/new-support/application',
                'descriptionHtml' => '<p>Customer support by chat and email.</p>',
            ]]]),
            'https://apply.workable.com/api/v1/widget/accounts/*' => Http::response(['jobs' => []]),
        ]);

        $this->artisan('jobs:sync', ['--direct-online' => true])->assertSuccessful();

        $this->assertModelExists($other);
        $this->assertModelMissing($synthetic);
        $this->assertDatabaseHas('job_listings', ['id' => 'ashby-clipboard--new-support', 'kenya_friendly' => true]);
        Http::assertSentCount(3);
    }
}
