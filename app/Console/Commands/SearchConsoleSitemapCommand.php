<?php

namespace App\Console\Commands;

use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:gsc-sitemap {action=submit : Action to perform: submit or list} {--sitemap= : Sitemap URL to submit (default: config site.url/sitemap.xml)} {--site= : Search Console site property}')]
#[Description('Submit or list sitemaps via Google Search Console Webmasters API')]
class SearchConsoleSitemapCommand extends Command
{
    public function handle(GoogleSearchConsoleService $gsc): int
    {
        $siteUrl = (string) ($this->option('site') ?: $gsc->getSiteUrl());
        $action = strtolower((string) $this->argument('action'));

        $this->info("Google Search Console Property: {$siteUrl}");

        if (! $gsc->isConfigured()) {
            $this->error('Google Service Account credentials are not configured.');
            $this->line('Ensure storage/app/google-indexing-key.json exists or GOOGLE_INDEXING_CREDENTIALS is set in .env.');
            $this->newLine();
            $this->line('Manual Submission Option:');
            $this->line('Submit directly in the Search Console GUI at https://search.google.com/search-console');
            $this->line('URL: '.rtrim(config('site.url'), '/').'/sitemap.xml');

            return self::FAILURE;
        }

        if ($action === 'list') {
            $res = $gsc->listSitemaps($siteUrl);
            if (! $res['success']) {
                $this->error('Failed to list sitemaps: '.($res['message'] ?? 'Unknown error'));

                return self::FAILURE;
            }

            $sitemaps = $res['sitemaps'];
            if (empty($sitemaps)) {
                $this->warn('No sitemaps currently registered for this property in Google Search Console.');

                return self::SUCCESS;
            }

            $rows = array_map(fn (array $s) => [
                $s['path'] ?? 'N/A',
                $s['lastSubmitted'] ?? 'N/A',
                ($s['isPending'] ?? false) ? 'Pending' : 'Processed',
                $s['errors'] ?? 0,
            ], $sitemaps);

            $this->table(['Sitemap Path', 'Last Submitted', 'Status', 'Errors'], $rows);

            return self::SUCCESS;
        }

        if ($action === 'submit') {
            $defaultSitemap = str_starts_with(config('site.url'), 'http://localhost')
                ? 'https://kenyaremotejobs.com/sitemap.xml'
                : rtrim(config('site.url'), '/').'/sitemap.xml';

            $sitemapUrl = (string) ($this->option('sitemap') ?: $defaultSitemap);
            $this->line("Submitting sitemap: {$sitemapUrl}...");

            $res = $gsc->submitSitemap($sitemapUrl, $siteUrl);

            if ($res['success']) {
                $this->info("✓ Successfully submitted sitemap to Google Search Console: {$sitemapUrl}");

                return self::SUCCESS;
            }

            $this->error('Failed to submit sitemap: '.($res['message'] ?? 'Unknown error'));
            $this->newLine();
            $this->line('Tips:');
            $this->line('1. The service account email must be added as an Owner or Full User of the Search Console property.');
            $this->line('2. You can also paste the URL directly into Google Search Console UI under Indexing > Sitemaps:');
            $this->line('   '.$sitemapUrl);

            return self::FAILURE;
        }

        $this->error("Unknown action '{$action}'. Supported actions: submit, list");

        return self::FAILURE;
    }
}
