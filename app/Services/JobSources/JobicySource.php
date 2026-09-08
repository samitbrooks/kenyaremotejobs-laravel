<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JobicySource implements JobSource
{
    // Jobicy caps `count` at 100 regardless of the value requested, and has
    // no page/offset param — one request is the whole available batch.
    public function fetch(): array
    {
        $response = Http::acceptJson()->get('https://jobicy.com/api/v2/remote-jobs', ['count' => 100]);

        if ($response->failed()) {
            throw new RuntimeException("Jobicy API returned {$response->status()}");
        }

        return collect($response->json('jobs', []))
            ->filter(fn ($job) => ($job['id'] ?? null) && ($job['jobTitle'] ?? null) && ($job['companyName'] ?? null) && ($job['url'] ?? null))
            ->map(fn ($job) => $this->normalize($job))
            ->values()
            ->all();
    }

    private function normalize(array $job): array
    {
        return [
            'id' => "jobicy--{$job['id']}",
            'source_name' => 'Jobicy',
            'source_id' => (string) $job['id'],
            'source_url' => $job['url'],
            'title' => $job['jobTitle'],
            'company' => $job['companyName'],
            'description' => $job['jobDescription'] ?? $job['jobExcerpt'] ?? '',
            'tags' => [...($job['jobIndustry'] ?? []), ...($job['jobType'] ?? [])],
            'location' => $job['jobGeo'] ?? 'Worldwide',
            'remote_type' => $job['jobType'][0] ?? 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => isset($job['pubDate']) ? Carbon::parse($job['pubDate']) : now(),
        ];
    }
}
