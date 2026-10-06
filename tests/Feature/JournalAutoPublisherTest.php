<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class JournalAutoPublisherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Google Indexing and IndexNow so tests don't make external network calls
        $googleMock = Mockery::mock(GoogleIndexingService::class);
        $googleMock->shouldReceive('isConfigured')->andReturn(false);
        $this->app->instance(GoogleIndexingService::class, $googleMock);

        $indexNowMock = Mockery::mock(IndexNowService::class);
        $indexNowMock->shouldReceive('submitUrls')->andReturn(['success' => true, 'message' => 'Submitted 1 URL']);
        $this->app->instance(IndexNowService::class, $indexNowMock);
    }

    public function test_can_publish_scheduled_blog_post_via_artisan_command(): void
    {
        $this->artisan('journal:publish-scheduled')
            ->expectsOutputToContain('Journal Scheduled Publisher')
            ->expectsOutputToContain('Successfully published article')
            ->assertSuccessful();

        $post = BlogPost::latest('published_at')->first();
        $this->assertNotNull($post);
        $this->assertTrue($post->published);
        $this->assertNotNull($post->published_at);
        $this->assertNotEmpty($post->title);
        $this->assertNotEmpty($post->content);
        $this->assertNotEmpty($post->excerpt);

        // Verify the post renders on the journal index
        $indexResponse = $this->get('/journal');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($post->title);

        // Verify the post detail page renders with full content and 200 OK
        $detailResponse = $this->get('/journal/'.$post->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($post->title);
        $detailResponse->assertSee($post->category);
    }

    public function test_dry_run_mode_does_not_persist_post(): void
    {
        $initialCount = BlogPost::count();

        $this->artisan('journal:publish-scheduled', ['--dry-run' => true])
            ->expectsOutputToContain('DRY RUN MODE: Publication Preview')
            ->assertSuccessful();

        $this->assertSame($initialCount, BlogPost::count());
    }

    public function test_weekly_quota_prevents_publishing_more_than_three_posts_unless_forced(): void
    {
        $initialCount = BlogPost::count();

        // Create 3 posts published this week
        for ($i = 1; $i <= 3; $i++) {
            BlogPost::create([
                'slug' => "existing-post-test-{$i}",
                'title' => "Existing Post Test {$i}",
                'excerpt' => "Excerpt {$i}",
                'content' => "Content {$i}",
                'category' => 'Career Guides',
                'author_name' => 'KenyaRemoteJobs Team',
                'published' => true,
                'published_at' => now()->startOfWeek()->addHours($i * 4),
            ]);
        }

        $this->assertSame($initialCount + 3, BlogPost::count());

        // Running without --force should recognize the 3-post weekly quota and not publish
        $this->artisan('journal:publish-scheduled')
            ->expectsOutputToContain('Weekly publication quota (3 posts) already met')
            ->assertSuccessful();

        $this->assertSame($initialCount + 3, BlogPost::count());

        // Running with --force should override the quota and publish a 4th post
        $this->artisan('journal:publish-scheduled', ['--force' => true])
            ->expectsOutputToContain('Successfully published article')
            ->assertSuccessful();

        $this->assertSame($initialCount + 4, BlogPost::count());
    }

    public function test_can_publish_specific_topic_via_topic_option(): void
    {
        $this->artisan('journal:publish-scheduled', [
            '--topic' => 'upwork-fiverr-to-direct-clients-kenya-blueprint',
        ])
            ->expectsOutputToContain('Successfully published article')
            ->assertSuccessful();

        $post = BlogPost::where('slug', 'upwork-fiverr-to-direct-clients-kenya-blueprint')->first();
        $this->assertNotNull($post);
        $this->assertStringContainsString('Upwork', $post->title);
    }

    public function test_published_post_is_present_in_sitemap_xml(): void
    {
        $this->artisan('journal:publish-scheduled')
            ->assertSuccessful();

        $post = BlogPost::first();
        $this->assertNotNull($post);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertSee('/journal/'.$post->slug.'</loc>', false);
    }
}
