<?php

namespace App\Console\Commands;

use App\Services\JournalAutoPublisherService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('journal:publish-scheduled {--force : Force publishing even if the weekly 3-post quota is already met} {--topic= : Specific topic slug or custom topic title to publish} {--dry-run : Simulate publication without saving to database or pinging search engines}')]
#[Description('Automatically publish scheduled blog articles for The Journal (3x per week on Mon, Wed, Fri)')]
class PublishWeeklyBlogCommand extends Command
{
    public function handle(JournalAutoPublisherService $autoPublisher): int
    {
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $topic = $this->option('topic') ? (string) $this->option('topic') : null;

        $publishedThisWeek = $autoPublisher->getPostsPublishedThisWeekCount();
        $this->info("Journal Scheduled Publisher: Current week has {$publishedThisWeek} published post(s).");

        $result = $autoPublisher->publishScheduledPost(
            requestedTopic: $topic,
            force: $force,
            dryRun: $dryRun,
        );

        if (! $result['success']) {
            if ($result['status'] === 'weekly_quota_met') {
                $this->warn($result['message']);

                return self::SUCCESS;
            }

            $this->error($result['message']);

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('=== DRY RUN MODE: Publication Preview ===');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Title', $result['preview']['title'] ?? 'N/A'],
                    ['Slug', $result['preview']['slug'] ?? 'N/A'],
                    ['Category', $result['preview']['category'] ?? 'N/A'],
                    ['Word Count', (string) ($result['preview']['word_count'] ?? '0')],
                    ['Excerpt', $result['preview']['excerpt'] ?? 'N/A'],
                ]
            );

            return self::SUCCESS;
        }

        $post = $result['post'];
        $url = url('/journal/'.$post->slug);

        $this->info("✓ Successfully published article: '{$post->title}'");
        $this->table(
            ['Property', 'Details'],
            [
                ['ID', $post->id],
                ['Slug', $post->slug],
                ['URL', $url],
                ['Category', $post->category],
                ['Published At', $post->published_at?->toDateTimeString() ?? 'Now'],
                ['Google Indexing', $result['google_indexing']['message'] ?? 'N/A'],
                ['IndexNow', $result['indexnow']['message'] ?? 'N/A'],
            ]
        );

        return self::SUCCESS;
    }
}
