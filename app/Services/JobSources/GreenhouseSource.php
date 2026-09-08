<?php

namespace App\Services\JobSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GreenhouseSource implements JobSource
{
    // Greenhouse's public job-board API (api.greenhouse.io/v1/boards/{slug}/jobs)
    // needs no auth and no API key — it's the same feed a company's own
    // careers page reads from, so what we pull is exactly what they've
    // published, and the source URL points straight at their real posting.
    // To add another company, confirm
    // https://api.greenhouse.io/v1/boards/<slug>/jobs resolves for their
    // slug (usually visible in their careers page URL) and add it below —
    // nothing else needs to change.
    private const BOARDS = [
        ['slug' => 'gitlab', 'source_name' => 'GitLab'],
        ['slug' => 'turing', 'source_name' => 'Turing'],
        // Moniepoint's own listings name its former legal entity and
        // Nigerian subsidiary in passing ("...Moniepoint MFB, and TeamApt
        // Ltd..."), a leak the standard company-name redaction can't catch
        // since it's an unrelated string, not a variant of "Moniepoint".
        // Normalized to the display name below before storage, same as
        // everywhere else redaction depends on `company` being the *only*
        // name that needs stripping.
        ['slug' => 'moniepoint', 'source_name' => 'Moniepoint', 'aliases' => ['TeamApt']],
    ];

    public function fetch(): array
    {
        $jobs = [];

        foreach (self::BOARDS as $board) {
            try {
                array_push($jobs, ...$this->fetchBoard($board));
            } catch (Throwable $e) {
                Log::warning('[greenhouse] a board failed', ['slug' => $board['slug'], 'error' => $e->getMessage()]);
            }
        }

        return $jobs;
    }

    private function fetchBoard(array $board): array
    {
        $response = Http::acceptJson()->get("https://api.greenhouse.io/v1/boards/{$board['slug']}/jobs", ['content' => 'true']);

        if ($response->failed()) {
            throw new RuntimeException("Greenhouse API ({$board['slug']}) returned {$response->status()}");
        }

        // A single company's board lists every open role, most of which
        // aren't remote — only the subset whose location explicitly says so
        // belongs on a remote-jobs board.
        return collect($response->json('jobs', []))
            ->filter(fn ($job) => str_contains(mb_strtolower($job['location']['name'] ?? ''), 'remote'))
            ->map(fn ($job) => $this->normalize($job, $board['source_name'], $board['slug'], $board['aliases'] ?? []))
            ->values()
            ->all();
    }

    private function normalize(array $job, string $sourceName, string $slug, array $aliases): array
    {
        $content = $job['content'] ?? '';
        foreach ($aliases as $alias) {
            $content = preg_replace('/\b'.preg_quote($alias, '/').'\b/i', $sourceName, $content);
        }

        return [
            'id' => "greenhouse-{$slug}--{$job['id']}",
            'source_name' => $sourceName,
            'source_id' => (string) $job['id'],
            'source_url' => $job['absolute_url'],
            'title' => $job['title'],
            'company' => $sourceName,
            'description' => $content,
            'tags' => array_map(fn ($d) => $d['name'], $job['departments'] ?? []),
            'location' => $job['location']['name'] ?? 'Remote',
            'remote_type' => 'Remote',
            'salary' => null,
            // Greenhouse's public board API doesn't expose a structured
            // salary field.
            'annual_salary_usd' => null,
            'posted_at' => $job['updated_at'],
        ];
    }
}
