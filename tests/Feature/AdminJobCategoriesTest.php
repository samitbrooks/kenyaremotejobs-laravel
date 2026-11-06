<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use App\Support\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_categories_prefer_job_titles_and_preserve_specialist_work(): void
    {
        $this->assertSame('customer-support-kenya', JobCategory::classify('Customer Support Representative', ['ai', 'software']));
        $this->assertSame('ai-training-annotation-kenya', JobCategory::classify('Data Annotation Specialist'));
        $this->assertSame('transcription-translation-kenya', JobCategory::classify('Transcription Specialist'));
        $this->assertSame('data-entry-kenya', JobCategory::classify('Data Entry Assistant'));
        $this->assertSame('human-resources-kenya', JobCategory::classify('Recruiter'));
        $this->assertSame('digital-marketing-kenya', JobCategory::classify('Specialist', ['seo']));
        $this->assertSame('uncategorised', JobCategory::classify('Chronicler'));
    }

    public function test_category_filter_combines_with_source_search_and_kenya_filters(): void
    {
        $this->signInAdmin();
        $this->listing('included', 'Customer Support Alpha');
        $this->listing('other-category', 'Software Developer Alpha');
        $this->listing('other-source', 'Customer Support Beta', ['source_name' => 'Other']);
        $this->listing('other-region', 'Customer Support Gamma', ['kenya_friendly' => false]);
        $response = $this->get('/admin/jobs?category=customer-support-kenya&source=Workable&q=Alpha&kenya_friendly=true');
        $response->assertOk()->assertSee('Customer Support Alpha')->assertDontSee('Software Developer Alpha')
            ->assertDontSee('Customer Support Beta')->assertDontSee('Customer Support Gamma')
            ->assertViewHas('total', 1)
            ->assertViewHas('categoryCounts', fn (array $counts): bool => $counts['customer-support-kenya'] === 1 && $counts['software-developer-kenya'] === 1);
    }

    public function test_category_groups_are_sorted_before_pagination_and_keep_filters_in_links(): void
    {
        $this->signInAdmin();
        $this->listing('software', 'Software Developer', ['posted_at' => now()->addDay()]);
        for ($index = 1; $index <= 26; $index++) {
            $this->listing('support-'.$index, sprintf('Customer Support %02d', $index));
        }
        $response = $this->get('/admin/jobs?group_by=category&source=Workable&order=title&kenya_friendly=true');
        $response->assertOk()->assertSee('scope="rowgroup"', false)->assertSee('26 matching')
            ->assertSee('Customer Support 25')->assertDontSee('Customer Support 26')->assertDontSee('>Software Developer<', false)
            ->assertSee('group_by=category')->assertSee('page=2')->assertSee('source=Workable')->assertSee('kenya_friendly=true');
        $this->get('/admin/jobs?group_by=category&source=Workable&order=title&kenya_friendly=true&page=2')
            ->assertOk()->assertSeeInOrder(['Customer Support 26', 'Software Developer'])->assertViewHas('total', 27);
    }

    public function test_source_grouping_keeps_order_within_each_group_and_shows_freshness(): void
    {
        $this->signInAdmin();
        $this->listing('z-source', 'Customer Support Z', ['source_name' => 'Zeta']);
        $this->listing('a-old', 'Customer Support Old', ['source_name' => 'Alpha', 'posted_at' => now()->subDays(2)]);
        $this->listing('a-new', 'Customer Support New', ['source_name' => 'Alpha', 'last_seen_at' => now()]);
        $this->get('/admin/jobs?group_by=source')->assertOk()
            ->assertSeeInOrder(['Customer Support New', 'Customer Support Old', 'Customer Support Z'])
            ->assertSee('Seen in employer feed')->assertSee('Current on site')
            ->assertViewHas('groupTotals', fn ($totals): bool => $totals->all() === ['Alpha' => 2, 'Zeta' => 1]);
    }

    public function test_unmatched_jobs_remain_available_and_invalid_group_filters_fall_back_safely(): void
    {
        $this->signInAdmin();
        $this->listing('unmatched', 'Chronicler');
        $this->listing('matched', 'Customer Support');
        $this->get('/admin/jobs?category=uncategorised&group_by=category')->assertOk()
            ->assertSee('Chronicler')->assertViewHas('total', 1);
        $this->get('/admin/jobs?category[]=invalid&group_by[]=invalid')->assertOk()
            ->assertViewHas('category', '')->assertViewHas('groupBy', 'none')->assertViewHas('total', 2);
        $this->get('/admin/jobs?category=missing&group_by=missing')->assertOk()->assertViewHas('total', 2);
        $this->get('/admin/jobs?category=accounting-finance-kenya')->assertOk()->assertSee('No jobs match these filters.');
    }

    private function signInAdmin(): void
    {
        config(['jobs.admin_emails' => ['admin@example.com']]);
        $this->actingAs(User::factory()->create(['email' => 'admin@example.com']))
            ->withSession(['admin_authenticated' => true]);
    }

    /** @param array<string, mixed> $attributes */
    private function listing(string $id, string $title, array $attributes = []): JobListing
    {
        return JobListing::create(array_replace([
            'id' => $id, 'origin' => 'synced', 'tier' => 'basic', 'title' => $title,
            'company' => 'Example Employer', 'location' => 'Worldwide', 'remote_type' => 'Full-time',
            'description' => 'Remote opening.', 'source_id' => $id, 'source_name' => 'Workable',
            'source_url' => 'https://apply.workable.com/example/j/'.$id, 'posted_at' => now(),
            'kenya_friendly' => true, 'kenya_score' => 90, 'kenya_reasons' => ['Worldwide'],
            'tags' => [], 'audience_segments' => [],
        ], $attributes));
    }
}
