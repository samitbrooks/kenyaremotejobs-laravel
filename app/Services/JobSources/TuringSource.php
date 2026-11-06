<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TuringSource implements JobSource
{
    private const GREENHOUSE_API = 'https://api.greenhouse.io/v1/boards/turing/jobs?content=true';

    private const WORK_TURING_URL = 'https://work.turing.com';

    public function fetch(): array
    {
        $jobs = [];

        // 1. Fetch live openings from Turing Greenhouse board
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->get(self::GREENHOUSE_API);

            if ($response->successful()) {
                $ghJobs = $response->json('jobs', []);
                foreach ($ghJobs as $job) {
                    $location = mb_strtolower($job['location']['name'] ?? '');
                    // Include roles marked Remote or Global
                    if (str_contains($location, 'remote') || str_contains($location, 'global')) {
                        $jobs[] = $this->normalizeGreenhouseJob($job);
                    }
                }
            } else {
                Log::warning('[turing] Greenhouse API returned non-200 status', ['status' => $response->status()]);
            }
        } catch (Throwable $e) {
            Log::warning('[turing] Failed fetching greenhouse jobs: '.$e->getMessage());
        }

        return $jobs;
    }

    private function normalizeGreenhouseJob(array $job): array
    {
        $id = (string) $job['id'];
        $title = trim((string) ($job['title'] ?? 'Remote Role at Turing'));
        $departments = array_map(fn ($d) => $d['name'] ?? '', $job['departments'] ?? []);
        $departments = array_values(array_filter($departments));

        $tags = array_values(array_unique(array_filter([
            ...$departments,
            'Turing',
            'Remote',
            'AI & Tech',
        ])));

        $url = ! empty($job['absolute_url'])
            ? (string) $job['absolute_url']
            : self::WORK_TURING_URL;

        return [
            'id' => "turing--gh-{$id}",
            'source_name' => 'Turing',
            'source_id' => $id,
            'source_url' => $url,
            'title' => $title,
            'company' => 'Turing',
            'description' => (string) ($job['content'] ?? "<p>Apply for {$title} at Turing.</p>"),
            'tags' => $tags,
            'location' => $job['location']['name'] ?? 'Worldwide / Remote',
            'remote_type' => 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => ! empty($job['updated_at']) ? Carbon::parse($job['updated_at']) : Carbon::now(),
        ];
    }
}
