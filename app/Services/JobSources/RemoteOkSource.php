<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RemoteOkSource implements JobSource
{
    public function fetch(): array
    {
        $response = Http::acceptJson()
            // RemoteOK rejects requests without a browser-like User-Agent.
            ->withUserAgent('Mozilla/5.0 (compatible; KenyaRemoteJobsBot/1.0; +https://kenyaremotejobs.com)')
            ->get('https://remoteok.com/api');

        if ($response->failed()) {
            throw new RuntimeException("RemoteOK API returned {$response->status()}");
        }

        return collect($response->json())
            // The first entry is a "legal" notice, not a real job.
            ->filter(fn ($job) => ($job['id'] ?? null) && ($job['position'] ?? null) && ($job['company'] ?? null) && ($job['url'] ?? null) && empty($job['legal']))
            ->map(fn ($job) => $this->normalize($job))
            ->values()
            ->all();
    }

    private function normalize(array $job): array
    {
        $min = $job['salary_min'] ?? null;
        $max = $job['salary_max'] ?? null;
        $salary = ($min || $max) ? '$'.($min ?? '?').' - $'.($max ?? '?') : null;

        // RemoteOK's public API documents salary_min/salary_max as annual
        // USD — the one source we trust enough to derive an hourly estimate
        // from.
        $annualSalaryUsd = ($min || $max) ? ['min' => $min, 'max' => $max] : null;

        $url = $job['url'];
        $sourceUrl = str_starts_with($url, 'http') ? $url : "https://remoteok.com{$url}";

        return [
            'id' => "remoteok--{$job['id']}",
            'source_name' => 'RemoteOK',
            'source_id' => (string) $job['id'],
            'source_url' => $sourceUrl,
            'title' => $job['position'],
            'company' => $job['company'],
            'description' => $job['description'] ?? '',
            'tags' => $job['tags'] ?? [],
            'location' => $job['location'] ?: 'Worldwide',
            'remote_type' => 'Remote',
            'salary' => $salary,
            'annual_salary_usd' => $annualSalaryUsd,
            'posted_at' => isset($job['date']) ? Carbon::parse($job['date']) : now(),
        ];
    }
}
