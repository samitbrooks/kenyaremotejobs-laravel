<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AshbySource implements JobSource
{
    /**
     * Top-tier remote-first companies and high-growth AI startups running on Ashby ATS.
     * Ashby provides a public, unauthenticated posting API for direct career board queries.
     */
    private const BOARDS = [
        ['slug' => 'linear', 'source_name' => 'Linear'],
        ['slug' => 'supabase', 'source_name' => 'Supabase'],
        ['slug' => 'sentry', 'source_name' => 'Sentry'],
        ['slug' => 'replit', 'source_name' => 'Replit'],
        ['slug' => 'zapier', 'source_name' => 'Zapier'],
        ['slug' => 'cohere', 'source_name' => 'Cohere'],
        ['slug' => 'ramp', 'source_name' => 'Ramp'],
        ['slug' => 'perplexity', 'source_name' => 'Perplexity AI'],
        ['slug' => 'cursor', 'source_name' => 'Cursor'],
        ['slug' => 'langchain', 'source_name' => 'LangChain'],
        ['slug' => 'resend', 'source_name' => 'Resend'],
        ['slug' => 'vapi', 'source_name' => 'Vapi'],
    ];

    public function fetch(): array
    {
        $jobs = [];

        foreach (self::BOARDS as $board) {
            try {
                $boardJobs = $this->fetchBoard($board);
                array_push($jobs, ...$boardJobs);
            } catch (Throwable $e) {
                Log::warning('[ashby] a board failed', ['slug' => $board['slug'], 'error' => $e->getMessage()]);
            }
        }

        return $jobs;
    }

    private function fetchBoard(array $board): array
    {
        $response = Http::timeout(8)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'KenyaRemoteJobs/1.0 (Direct ATS Job Ingestion; info@kenyaremotejobs.com)',
            ])
            ->get("https://api.ashbyhq.com/posting-api/job-board/{$board['slug']}");

        if ($response->failed()) {
            return [];
        }

        $allJobs = $response->json('jobs', []);

        return collect($allJobs)
            ->filter(function ($job) {
                if (($job['isRemote'] ?? false) === true) {
                    return true;
                }

                $workplace = mb_strtolower((string) ($job['workplaceType'] ?? ''));
                if (str_contains($workplace, 'remote')) {
                    return true;
                }

                $location = mb_strtolower((string) ($job['location'] ?? ''));

                return str_contains($location, 'remote') || str_contains($location, 'anywhere') || str_contains($location, 'worldwide');
            })
            ->map(fn ($job) => $this->normalize($job, $board['source_name'], $board['slug']))
            ->values()
            ->all();
    }

    private function normalize(array $job, string $sourceName, string $slug): array
    {
        $tags = array_values(array_filter([
            $job['department'] ?? null,
            $job['team'] ?? null,
            $job['employmentType'] ?? null,
        ]));

        $publishedAt = isset($job['publishedAt']) ? Carbon::parse($job['publishedAt']) : now();

        return [
            'id' => "ashby-{$slug}--{$job['id']}",
            'source_name' => $sourceName,
            'source_id' => (string) $job['id'],
            'source_url' => $job['jobUrl'] ?? $job['applyUrl'] ?? "https://jobs.ashbyhq.com/{$slug}/{$job['id']}",
            'title' => $job['title'],
            'company' => $sourceName,
            'description' => $job['descriptionHtml'] ?? $job['descriptionPlain'] ?? '',
            'tags' => $tags,
            'location' => $job['location'] ?: 'Remote',
            'remote_type' => 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => $publishedAt,
        ];
    }
}
