<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeverSource implements JobSource
{
    /**
     * Top-tier remote employers running on Lever ATS.
     * Lever provides a public, unauthenticated posting API for direct career board queries.
     * Every listing links directly to the employer's official Lever application form.
     */
    private const BOARDS = [
        ['slug' => 'brafton', 'source_name' => 'Brafton'],
        ['slug' => 'superside', 'source_name' => 'Superside'],
        ['slug' => 'appen', 'source_name' => 'Appen'],
        ['slug' => 'spotify', 'source_name' => 'Spotify'],
    ];

    public function fetch(): array
    {
        $jobs = [];

        foreach (self::BOARDS as $board) {
            try {
                $boardJobs = $this->fetchBoard($board);
                array_push($jobs, ...$boardJobs);
            } catch (Throwable $e) {
                Log::warning('[lever] a board failed', ['slug' => $board['slug'], 'error' => $e->getMessage()]);
            }
        }

        return $jobs;
    }

    private function fetchBoard(array $board): array
    {
        $response = Http::timeout(12)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; KenyaRemoteJobs/1.0; Direct ATS Ingestion; +https://kenyaremotejobs.com)',
            ])
            ->get("https://api.lever.co/v0/postings/{$board['slug']}?mode=json");

        if ($response->failed()) {
            return [];
        }

        $allJobs = $response->json();
        if (! is_array($allJobs)) {
            return [];
        }

        return collect($allJobs)
            ->filter(function ($job) {
                $workplaceType = mb_strtolower((string) ($job['workplaceType'] ?? ''));
                if (str_contains($workplaceType, 'remote')) {
                    return true;
                }

                $location = mb_strtolower((string) ($job['categories']['location'] ?? ''));
                if (
                    str_contains($location, 'remote') ||
                    str_contains($location, 'global') ||
                    str_contains($location, 'anywhere') ||
                    str_contains($location, 'worldwide') ||
                    str_contains($location, 'africa') ||
                    str_contains($location, 'kenya')
                ) {
                    return true;
                }

                $allLocations = $job['categories']['allLocations'] ?? [];
                if (is_array($allLocations)) {
                    foreach ($allLocations as $loc) {
                        $locLower = mb_strtolower((string) $loc);
                        if (
                            str_contains($locLower, 'remote') ||
                            str_contains($locLower, 'global') ||
                            str_contains($locLower, 'worldwide')
                        ) {
                            return true;
                        }
                    }
                }

                return false;
            })
            ->map(fn ($job) => $this->normalize($job, $board['source_name'], $board['slug']))
            ->values()
            ->all();
    }

    private function normalize(array $job, string $sourceName, string $slug): array
    {
        $tags = array_values(array_filter([
            $job['categories']['team'] ?? null,
            $job['categories']['department'] ?? null,
            $job['categories']['commitment'] ?? null,
        ]));

        $createdAt = isset($job['createdAt'])
            ? Carbon::createFromTimestampMs((int) $job['createdAt'])
            : now();

        $location = $job['categories']['location'] ?? 'Remote / Global';

        $applyUrl = $job['applyUrl'] ?? $job['hostedUrl'] ?? "https://jobs.lever.co/{$slug}/{$job['id']}/apply";

        return [
            'id' => "lever-{$slug}--{$job['id']}",
            'source_name' => $sourceName,
            'source_id' => (string) $job['id'],
            'source_url' => $applyUrl,
            'title' => $job['text'] ?? 'Remote Role',
            'company' => $sourceName,
            'description' => $job['description'] ?? $job['descriptionPlain'] ?? '',
            'tags' => $tags,
            'location' => $location ?: 'Remote',
            'remote_type' => 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => $createdAt,
        ];
    }
}
