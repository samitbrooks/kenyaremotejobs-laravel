<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Services\IndexNowService;
use App\Support\CompanyDirectory;
use App\Support\JobCategorySeo;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:indexnow {--limit=100 : Maximum number of jobs to submit} {--dry-run : Simulate without sending HTTP requests} {--url= : Specific URL to submit}')]
#[Description('Submit URLs directly to IndexNow (Bing, Yandex, Seznam, Naver) for instant crawl and indexing')]
class IndexNowCommand extends Command
{
    public function handle(IndexNowService $indexNow): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $customUrl = $this->option('url');

        if ($customUrl) {
            $urls = [(string) $customUrl];
        } else {
            $limit = max(1, (int) $this->option('limit'));
            $urlPrefix = str_starts_with(config('site.url'), 'http://localhost')
                ? 'https://kenyaremotejobs.com'
                : rtrim(config('site.url'), '/');

            $coreUrls = [
                "{$urlPrefix}/",
                "{$urlPrefix}/jobs",
                "{$urlPrefix}/companies",
                "{$urlPrefix}/collections",
                "{$urlPrefix}/journal",
                "{$urlPrefix}/employers",
                "{$urlPrefix}/pricing",
                "{$urlPrefix}/faqs",
                "{$urlPrefix}/surveys",
            ];

            $categoryUrls = collect(JobCategorySeo::all())
                ->map(fn ($cat) => "{$urlPrefix}/remote-jobs/{$cat['slug']}")
                ->all();

            $companyUrls = collect(CompanyDirectory::all())
                ->take(10)
                ->map(fn ($comp) => "{$urlPrefix}/companies/{$comp['slug']}")
                ->all();

            $postUrls = BlogPost::where('published', true)
                ->latest('published_at')
                ->take(10)
                ->pluck('slug')
                ->map(fn ($slug) => "{$urlPrefix}/journal/{$slug}")
                ->all();

            $jobUrls = JobListing::visible()
                ->latest('posted_at')
                ->take($limit)
                ->pluck('id')
                ->map(fn ($id) => "{$urlPrefix}/jobs/{$id}")
                ->all();

            $urls = array_merge($coreUrls, $categoryUrls, $companyUrls, $postUrls, $jobUrls);
        }

        $this->info(sprintf('Preparing to submit %d URL(s) to IndexNow...', count($urls)));

        if ($dryRun) {
            $this->warn('Running in --dry-run mode. No network requests dispatched.');
            foreach (array_slice($urls, 0, 15) as $url) {
                $this->line("  [DRY-RUN] {$url}");
            }
            if (count($urls) > 15) {
                $this->line(sprintf('  ... and %d more URLs', count($urls) - 15));
            }

            return self::SUCCESS;
        }

        $result = $indexNow->submitUrls($urls);

        if ($result['success']) {
            $this->info("Completed: {$result['message']}");

            return self::SUCCESS;
        }

        $this->error("Failed [Status: {$result['status']}]: {$result['message']}");

        return self::FAILURE;
    }
}
