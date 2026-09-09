<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RemotiveSource implements JobSource
{
    public function fetch(): array
    {
        $response = Http::acceptJson()->get('https://remotive.com/api/remote-jobs');

        if ($response->failed()) {
            throw new RuntimeException("Remotive API returned {$response->status()}");
        }

        return collect($response->json('jobs', []))
            ->filter(fn ($job) => trim((string) ($job['company_name'] ?? '')) !== '')
            ->map(fn ($job) => $this->normalize($job))
            ->values()
            ->all();
    }

    private function normalize(array $job): array
    {
        return [
            'id' => "remotive--{$job['id']}",
            'source_name' => 'Remotive',
            'source_id' => (string) $job['id'],
            'source_url' => $job['url'],
            'title' => $job['title'],
            'company' => $job['company_name'],
            'description' => $job['description'] ?? '',
            'tags' => array_values(array_filter([...($job['tags'] ?? []), $job['category'] ?? null])),
            'location' => $job['candidate_required_location'] ?: 'Worldwide',
            'remote_type' => $job['job_type'] ?: 'Remote',
            'salary' => $job['salary'] ?: null,
            // Remotive's salary is free text (e.g. "$70k - $90k",
            // "Competitive"), not structured numbers — too unreliable to
            // parse into an hourly rate.
            'annual_salary_usd' => null,
            'posted_at' => isset($job['publication_date']) ? Carbon::parse($job['publication_date']) : now(),
        ];
    }
}
