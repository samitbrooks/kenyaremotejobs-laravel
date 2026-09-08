<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HimalayasSource implements JobSource
{
    // Himalayas silently clamps `limit` to 20 regardless of what's
    // requested, and the catalog runs to six figures — so we page with the
    // cursor it hands back instead of trying to ask for more per request.
    // Capped at a fixed number of pages so a sync stays bounded even though
    // the upstream catalog is huge.
    private const PAGE_SIZE = 20;

    private const MAX_PAGES = 15;

    public function fetch(): array
    {
        $jobs = [];
        $cursor = null;

        for ($page = 0; $page < self::MAX_PAGES; $page++) {
            $params = ['limit' => self::PAGE_SIZE];
            if ($cursor) {
                $params['cursor'] = $cursor;
            }

            $response = Http::acceptJson()->get('https://himalayas.app/jobs/api', $params);

            if ($response->failed()) {
                if ($page === 0) {
                    throw new RuntimeException("Himalayas API returned {$response->status()}");
                }
                break; // keep what we already fetched rather than losing the whole sync
            }

            $body = $response->json();

            foreach ($body['jobs'] ?? [] as $job) {
                if (($job['title'] ?? null) && ($job['companyName'] ?? null) && ($job['guid'] ?? null)) {
                    $jobs[] = $this->normalize($job);
                }
            }

            if (empty($body['nextCursor'])) {
                break;
            }
            $cursor = $body['nextCursor'];
        }

        return $jobs;
    }

    private function normalize(array $job): array
    {
        $restrictions = $job['locationRestrictions'] ?? [];
        $location = count($restrictions) > 0 ? implode(', ', $restrictions) : 'Worldwide';

        // Fold the structured UTC-offset window into the description text
        // using the same "UTC-x to UTC+y" phrasing KenyaRelevance's regex
        // already looks for, so this source benefits from the same
        // timezone-overlap detection as free-text listings instead of
        // needing its own numeric-offset code path.
        $timezones = $job['timezoneRestrictions'] ?? [];
        $timezoneNote = count($timezones) > 0
            ? ' Timezone: UTC'.$this->formatOffset(min($timezones)).' to UTC'.$this->formatOffset(max($timezones)).'.'
            : '';

        $currency = $job['currency'] ?? null;
        $salaryPeriod = $job['salaryPeriod'] ?? null;
        $min = $job['minSalary'] ?? null;
        $max = $job['maxSalary'] ?? null;
        $hasStructuredUsdSalary = $currency === 'USD' && $salaryPeriod === 'annual' && ($min || $max);

        $salary = ($min || $max)
            ? ($currency ?? '$').($min ?? '?').' - '.($currency ?? '$').($max ?? '?').($salaryPeriod ? " ({$salaryPeriod})" : '')
            : null;

        // guid is a full URL (e.g. .../companies/acme/jobs/role-1234567) —
        // use just its last path segment as the id. The raw URL would break
        // the /jobs/{id} route, whose slashes get read as extra path
        // segments.
        $segments = array_values(array_filter(explode('/', $job['guid'])));
        $slug = end($segments) ?: $job['guid'];

        return [
            'id' => "himalayas--{$slug}",
            'source_name' => 'Himalayas',
            'source_id' => $slug,
            'source_url' => $job['applicationLink'],
            'title' => $job['title'],
            'company' => $job['companyName'],
            'description' => ($job['description'] ?? $job['excerpt'] ?? '').$timezoneNote,
            'tags' => $job['categories'] ?? [],
            'location' => $location,
            'remote_type' => $job['employmentType'] ?? 'Remote',
            'salary' => $salary,
            'annual_salary_usd' => $hasStructuredUsdSalary ? ['min' => $min, 'max' => $max] : null,
            'posted_at' => isset($job['pubDate']) ? Carbon::createFromTimestamp($job['pubDate']) : now(),
        ];
    }

    private function formatOffset(int $offset): string
    {
        return $offset >= 0 ? "+{$offset}" : (string) $offset;
    }
}
