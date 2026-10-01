<?php

namespace App\Console\Commands;

use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use App\Support\SurveyPlatforms;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Signature('surveys:sync {--notify : Submit updated survey page to Google and IndexNow}')]
#[Description('Verify availability of paid survey platforms and ping search engines to index daily updates')]
class SyncSurveysCommand extends Command
{
    public function handle(GoogleIndexingService $googleIndexing, IndexNowService $indexNow): int
    {
        $platforms = SurveyPlatforms::all();
        $this->info(sprintf('Verifying %d paid survey platforms for Kenya...', count($platforms)));

        $healthy = 0;
        $flagged = 0;

        foreach ($platforms as $platform) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KenyaRemoteJobsBot/1.0; +https://kenyaremotejobs.com)'])
                    ->get($platform['url']);

                if ($response->successful() || in_array($response->status(), [301, 302, 403, 406])) {
                    // Some platforms block automated curl/bots with 403/406 but are live for real browsers
                    $healthy++;
                    $this->line("  ✓ [{$response->status()}] {$platform['name']}");
                } else {
                    $flagged++;
                    $this->warn("  ⚠ [{$response->status()}] {$platform['name']} responded with unexpected status");
                }
            } catch (\Throwable $e) {
                // Network timeout or DNS check
                $flagged++;
                $this->warn("  ⚠ {$platform['name']} connection error: {$e->getMessage()}");
            }
        }

        Cache::forever('surveys_last_synced_at', now()->format('F j, Y'));
        Cache::forever('surveys_healthy_count', $healthy);

        $this->info("Completed: {$healthy} verified active, {$flagged} flagged.");

        // Automatically push /surveys to Google Indexing API and IndexNow
        $surveyUrl = rtrim(config('site.url'), '/').'/surveys';
        if (str_starts_with($surveyUrl, 'http://localhost')) {
            $surveyUrl = 'https://kenyaremotejobs.com/surveys';
        }

        $this->info("Pushing {$surveyUrl} to Search Engines...");

        if ($googleIndexing->isConfigured()) {
            $gRes = $googleIndexing->publishUrl($surveyUrl, 'URL_UPDATED');
            $this->line('  Google Indexing API: '.($gRes['success'] ? 'Submitted successfully (200)' : "Notice: {$gRes['message']}"));
        }

        $iRes = $indexNow->submitUrls([$surveyUrl]);
        $this->line('  IndexNow (Bing/Yahoo): '.($iRes['success'] ? 'Submitted successfully' : "Notice: {$iRes['message']}"));

        return self::SUCCESS;
    }
}
