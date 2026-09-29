<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use App\Services\GoogleIndexingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:google-index {--limit=100 : Maximum number of jobs to submit} {--dry-run : Simulate without sending HTTP requests} {--url= : Specific URL to submit} {--type=URL_UPDATED : Notification type (URL_UPDATED or URL_DELETED)}')]
#[Description('Push job posting URLs directly to Google Indexing API for rapid crawling and indexing')]
class GoogleIndexJobsCommand extends Command
{
    public function handle(GoogleIndexingService $indexingService): int
    {
        $type = (string) $this->option('type');
        $dryRun = (bool) $this->option('dry-run');
        $customUrl = $this->option('url');

        if ($customUrl) {
            $urls = [(string) $customUrl];
        } else {
            $limit = max(1, (int) $this->option('limit'));
            $urlPrefix = str_starts_with(config('site.url'), 'http://localhost')
                ? 'https://kenyaremotejobs.com'
                : config('site.url');

            // Select visible jobs ordered by latest posted
            $urls = JobListing::visible()
                ->latest('posted_at')
                ->take($limit)
                ->pluck('id')
                ->map(fn ($id) => "{$urlPrefix}/jobs/{$id}")
                ->all();
        }

        $this->info(sprintf('Preparing to submit %d URL(s) to Google Indexing API [Type: %s]...', count($urls), $type));

        if ($dryRun) {
            $this->warn('Running in --dry-run mode. No requests will be dispatched.');
            foreach ($urls as $url) {
                $this->line("  [DRY-RUN] {$url}");
            }

            return self::SUCCESS;
        }

        if (! $indexingService->isConfigured()) {
            $this->error('Google Indexing API credentials are not configured.');
            $this->line('');
            $this->line('To configure:');
            $this->line('1. Create a Google Cloud Service Account with indexing permissions.');
            $this->line('2. Add the service account email as an Owner in Google Search Console.');
            $this->line('3. Place the JSON key in storage/app/google-indexing-key.json');
            $this->line('   OR set GOOGLE_INDEXING_CREDENTIALS="{\"type\":...}" in your .env');
            $this->line('');

            return self::FAILURE;
        }

        $successCount = 0;
        $failCount = 0;

        $bar = $this->output->createProgressBar(count($urls));
        $bar->start();

        foreach ($urls as $url) {
            $res = $indexingService->publishUrl($url, $type);

            if ($res['success']) {
                $successCount++;
            } else {
                $failCount++;
                $this->newLine();
                $this->error("Failed [{$res['status']}]: {$url} - {$res['message']}");
            }

            $bar->advance();
            usleep(100000); // 100ms pause to respect API rate limits
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed: {$successCount} submitted successfully, {$failCount} failed.");

        return $failCount > 0 && $successCount === 0 ? self::FAILURE : self::SUCCESS;
    }
}
