<?php

namespace App\Console\Commands;

use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seo:gsc-report {--site= : Search Console site URL property (default from config)} {--days=28 : Days back to analyze} {--limit=20 : Number of top queries to display}')]
#[Description('Fetch Google Search Console performance analytics (clicks, impressions, CTR, position, queries)')]
class SearchConsoleReportCommand extends Command
{
    public function handle(GoogleSearchConsoleService $gsc): int
    {
        $siteUrl = (string) ($this->option('site') ?: $gsc->getSiteUrl());
        $days = max(1, (int) $this->option('days'));
        $limit = max(1, (int) $this->option('limit'));

        $this->info("Connecting to Google Search Console for property: {$siteUrl}...");

        if (! $gsc->isConfigured()) {
            $this->error('Google Service Account credentials are not configured.');
            $this->line('Ensure storage/app/google-indexing-key.json exists or GOOGLE_INDEXING_CREDENTIALS is set in .env.');

            return self::FAILURE;
        }

        $startDate = now()->subDays($days + 2)->format('Y-m-d');
        $endDate = now()->subDays(2)->format('Y-m-d');

        $this->line("Date range: {$startDate} to {$endDate} (last {$days} days, excluding 2-day reporting lag)");

        $res = $gsc->queryAnalytics($siteUrl, $startDate, $endDate, ['query'], $limit);

        if (! $res['success']) {
            $this->error('Failed to retrieve Search Console data:');
            $this->line($res['message'] ?? 'Unknown error');

            if (str_contains((string) ($res['message'] ?? ''), 'disabled') || str_contains((string) ($res['message'] ?? ''), 'has not been used in project')) {
                $this->warn('Action required: Please enable the Google Search Console API in Google Cloud Console.');
            } elseif (str_contains((string) ($res['message'] ?? ''), 'User does not have sufficient permission')) {
                $this->warn('Action required: Add the service account email as a User in Search Console Settings > Users and Permissions.');
            }

            return self::FAILURE;
        }

        $totals = $res['totals'];
        $this->newLine();
        $this->info('=== Search Console Performance Summary ===');
        $this->table(
            ['Total Clicks', 'Total Impressions', 'Average CTR', 'Average Position'],
            [[
                number_format($totals['clicks']),
                number_format($totals['impressions']),
                $totals['ctr'].'%',
                $totals['position'],
            ]]
        );

        if (! empty($res['rows'])) {
            $this->newLine();
            $this->info("=== Top Search Queries (Top {$limit}) ===");
            $rows = array_map(function ($row) {
                return [
                    $row['keys'][0] ?? '(not set)',
                    number_format($row['clicks'] ?? 0),
                    number_format($row['impressions'] ?? 0),
                    round(($row['ctr'] ?? 0) * 100, 2).'%',
                    round($row['position'] ?? 0, 1),
                ];
            }, $res['rows']);

            $this->table(['Query', 'Clicks', 'Impressions', 'CTR', 'Position'], $rows);
        } else {
            $this->line('No query rows returned for this period yet.');
        }

        return self::SUCCESS;
    }
}
