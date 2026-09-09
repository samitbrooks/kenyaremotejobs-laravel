<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ArbeitnowSource implements JobSource
{
    public function fetch(): array
    {
        $response = Http::acceptJson()->get('https://www.arbeitnow.com/api/job-board-api');

        if ($response->failed()) {
            throw new RuntimeException("Arbeitnow API returned {$response->status()}");
        }

        return collect($response->json('data', []))
            ->filter(fn ($job) => ($job['remote'] ?? false) && trim((string) ($job['company_name'] ?? '')) !== '')
            ->map(fn ($job) => $this->normalize($job))
            ->values()
            ->all();
    }

    private function normalize(array $job): array
    {
        $min = $job['salary_min'] ?? null;
        $max = $job['salary_max'] ?? null;
        $salary = ($min || $max) ? ($min ?? '?').' - '.($max ?? '?') : null;

        return [
            'id' => "arbeitnow--{$job['slug']}",
            'source_name' => 'Arbeitnow',
            'source_id' => $job['slug'],
            'source_url' => $job['url'],
            'title' => $job['title'],
            'company' => $job['company_name'],
            'description' => $job['description'] ?? '',
            'tags' => [...($job['tags'] ?? []), ...($job['job_types'] ?? [])],
            'location' => $job['location'] ?: 'Worldwide',
            'remote_type' => 'Remote',
            'salary' => $salary,
            // Arbeitnow doesn't confirm a currency for these figures (often
            // EUR, not USD), so we show the raw text but don't derive an
            // hourly rate.
            'annual_salary_usd' => null,
            'posted_at' => Carbon::createFromTimestamp($job['created_at']),
        ];
    }
}
