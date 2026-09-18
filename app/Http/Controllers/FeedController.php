<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\Format;
use App\Support\SalaryEstimate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    /**
     * Generate an RSS 2.0 feed of curated, Kenya-friendly remote jobs
     * formatted specifically for social automation tools (Buffer, Publer, Make, Zapier).
     */
    public function featured(Request $request): Response
    {
        $siteUrl = rtrim(config('site.url'), '/');
        $siteName = config('site.name');

        $query = JobListing::visible()
            ->where('kenya_friendly', true);

        if ($request->boolean('with_salary')) {
            $query->whereNotNull('annual_salary_usd');
        }

        $limit = min(50, max(5, $request->integer('limit', 25)));
        $jobs = $query->latest('posted_at')->take($limit)->get();

        $items = $jobs->map(function (JobListing $job) use ($siteUrl) {
            $kesMonthly = SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
            $hourly = SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);

            $salaryBadge = '';
            if ($kesMonthly) {
                $salaryBadge = " ({$kesMonthly})";
            } elseif ($hourly) {
                $salaryBadge = ' (~$'.$hourly['min'].'-'.$hourly['max'].'/hr)';
            }

            $cleanTitle = "🇰🇪 {$job->title} at {$job->company}{$salaryBadge}";
            $jobUrl = "{$siteUrl}/jobs/{$job->id}?utm_source=linkedin&utm_medium=rss_feed&utm_campaign=featured_jobs";

            $compensationLine = $kesMonthly ? "Compensation: {$kesMonthly}" : ($job->salary ? "Compensation: {$job->salary}" : 'Compensation: Competitive international rates (USD / Wise / M-Pesa)');

            $plainDesc = Format::stripHtml($job->description ?? '');
            $excerpt = Format::truncate($plainDesc, 180);

            // Pre-built LinkedIn/X post copy inside the description node
            $socialBody = implode("\n", [
                '🚨 NEW VERIFIED REMOTE ROLE (Open to Kenya 🇰🇪)',
                '',
                "Role: {$job->title}",
                "Company: {$job->company}",
                "Type: 100% Remote ({$job->remote_type})",
                $compensationLine,
                '',
                'Highlights:',
                '✅ Screened for East Africa Time (UTC+3) compatibility',
                '✅ Zero foreign visa or sponsorship roadblocks',
                '✅ Direct international compensation',
                '',
                'About the role:',
                $excerpt,
                '',
                'Full details & how to apply directly:',
                "👉 {$jobUrl}",
                '',
                '#RemoteJobsKenya #WorkFromHomeKenya #KenyaRemoteJobs #KenyaTech #NairobiJobs',
            ]);

            return [
                'title' => $cleanTitle,
                'link' => $jobUrl,
                'guid' => "{$siteUrl}/jobs/{$job->id}",
                'pubDate' => $job->posted_at->toRfc2822String(),
                'company' => $job->company,
                'location' => $job->location ?? 'Remote (Kenya-Friendly)',
                'description' => $socialBody,
                'categories' => array_slice($job->tags ?? ['Remote', 'Kenya'], 0, 4),
            ];
        });

        $xml = view('feeds.featured-jobs', [
            'items' => $items,
            'siteUrl' => $siteUrl,
            'siteName' => $siteName,
            'feedUrl' => "{$siteUrl}/feed/featured.rss",
            'lastBuildDate' => optional($jobs->first())->posted_at?->toRfc2822String() ?? now()->toRfc2822String(),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }
}
