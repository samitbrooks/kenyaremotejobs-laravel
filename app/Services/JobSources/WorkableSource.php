<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WorkableSource implements JobSource
{
    private const ACCOUNTS = [
        'supportyourapp' => 'SupportYourApp',
        'cloudfactory' => 'CloudFactory',
    ];

    public function fetch(): array
    {
        $jobs = [];
        foreach (self::ACCOUNTS as $account => $company) {
            try {
                $response = Http::acceptJson()->connectTimeout(5)->timeout(15)
                    ->get("https://apply.workable.com/api/v1/widget/accounts/{$account}");
                $response->throw();

                $candidates = collect($response->json('jobs', []))
                    ->filter(fn (array $job): bool => ($job['telecommuting'] ?? false) === true
                        && (mb_strtolower($job['country'] ?? '') === 'kenya'
                            || collect($job['locations'] ?? [])->contains(fn (array $location): bool => ($location['countryCode'] ?? '') === 'KE')))
                    ->unique('shortcode');

                foreach ($candidates as $job) {
                    $shortcode = (string) ($job['shortcode'] ?? '');
                    if ($shortcode === '' || ! preg_match('/^[a-zA-Z0-9]+$/', $shortcode)) {
                        continue;
                    }
                    $detail = Http::acceptJson()->connectTimeout(5)->timeout(15)
                        ->get("https://apply.workable.com/api/v2/accounts/{$account}/jobs/{$shortcode}");
                    if ($detail->failed() || $detail->json('state') !== 'published' || $detail->json('isInternal') === true) {
                        continue;
                    }
                    $description = implode("\n", array_filter([
                        $detail->json('description'), $detail->json('requirements'), $detail->json('benefits'),
                    ]));
                    if (trim(strip_tags($description)) === '') {
                        continue;
                    }
                    $jobs["workable-{$account}--{$shortcode}"] = [
                        'id' => "workable-{$account}--{$shortcode}",
                        'source_name' => $company,
                        'source_id' => $shortcode,
                        'source_url' => "https://apply.workable.com/{$account}/j/{$shortcode}/apply/",
                        'title' => $detail->json('title') ?: $job['title'],
                        'company' => $company,
                        'description' => $description,
                        'tags' => array_values(array_filter([$job['department'] ?? null, $job['function'] ?? null, $job['experience'] ?? null])),
                        'location' => 'Remote — Kenya',
                        'remote_type' => 'Remote',
                        'salary' => null,
                        'annual_salary_usd' => null,
                        'posted_at' => Carbon::parse($detail->json('published') ?: $job['published_on']),
                        'last_seen_at' => now(),
                    ];
                }
            } catch (Throwable $exception) {
                Log::warning('[workable] employer feed failed', ['account' => $account, 'error' => $exception->getMessage()]);
            }
        }

        return array_values($jobs);
    }
}
