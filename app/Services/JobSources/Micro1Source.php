<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class Micro1Source implements JobSource
{
    private const API_URL = 'https://prod-api.micro1.ai/api/v1/job/portal?page=1&limit=100';

    public function fetch(): array
    {
        $jobs = [];

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                    'Origin' => 'https://jobs.micro1.ai',
                    'Referer' => 'https://jobs.micro1.ai/',
                ])
                ->post(self::API_URL, [
                    'action' => 'get_all_jobs',
                ]);

            if ($response->successful()) {
                $jobs = $response->json('data', []);
            }
        } catch (Throwable $e) {
            // Handled via fallback below
        }

        // Fallback to bundled snapshot if API failed or blocked hosting datacenter IP
        if (empty($jobs)) {
            $fallbackPath = resource_path('data/micro1_jobs.json');
            if (file_exists($fallbackPath)) {
                $content = file_get_contents($fallbackPath);
                $jobs = json_decode((string) $content, true) ?: [];
            }
        }

        if (empty($jobs)) {
            throw new RuntimeException('micro1 fetch failed: no jobs retrieved from API or bundled snapshot');
        }

        return collect($jobs)
            ->filter(fn ($job) => is_array($job) && ! empty($job['job_id']) && ! empty($job['job_name']))
            ->map(fn ($job) => $this->normalize($job))
            ->values()
            ->all();
    }

    private function normalize(array $job): array
    {
        $jobId = (string) $job['job_id'];
        $title = trim((string) ($job['job_name'] ?? 'Remote AI Engineer'));
        $company = trim((string) ($job['company_name'] ?? 'micro1')) ?: 'micro1';

        $hourly = $job['ideal_hourly_rate'] ?? null;
        $yearly = $job['ideal_yearly_compensation'] ?? null;
        $salary = null;
        $annualSalaryUsd = null;

        if (is_array($hourly) && isset($hourly['min'], $hourly['max'])) {
            $salary = "\${$hourly['min']} - \${$hourly['max']}/hr";
            $annualSalaryUsd = [
                'min' => (int) ($hourly['min'] * 2000),
                'max' => (int) ($hourly['max'] * 2000),
            ];
        } elseif (is_numeric($yearly) && $yearly > 0) {
            $salary = '$'.number_format((float) $yearly).'/yr';
            $annualSalaryUsd = [
                'min' => (int) $yearly,
                'max' => (int) $yearly,
            ];
        }

        $skills = is_array($job['skills'] ?? null) ? $job['skills'] : [];
        $domain = trim((string) ($job['domain_slug'] ?? ''));

        $tags = array_values(array_unique(array_filter([
            ...$skills,
            $domain !== '' ? ucwords(str_replace(['-', '_'], ' ', $domain)) : null,
            'AI Training',
            'Remote',
        ])));

        $applyUrl = trim((string) ($job['apply_url'] ?? "https://jobs.micro1.ai/post/{$jobId}"));

        $description = "<p>Apply for <strong>{$title}</strong> with <strong>{$company}</strong>.</p>"
            .'<p>This is a flexible, high-paying remote role training frontier AI models or contributing to world-class software engineering initiatives.</p>'
            .(! empty($skills) ? '<p><strong>Required Skills & Expertise:</strong> '.implode(', ', $skills).'</p>' : '')
            .($salary ? "<p><strong>Estimated Compensation:</strong> {$salary}</p>" : '')
            .'<p>Apply directly via the official micro1 expert recruitment portal.</p>';

        $postedAt = now();
        if (! empty($job['date_posted'])) {
            try {
                $postedAt = Carbon::parse($job['date_posted']);
            } catch (Throwable) {
                $postedAt = now();
            }
        }

        return [
            'id' => "micro1--{$jobId}",
            'source_name' => 'micro1',
            'source_id' => $jobId,
            'source_url' => $applyUrl,
            'title' => $title,
            'company' => $company,
            'description' => $description,
            'tags' => $tags,
            'location' => 'Worldwide / Remote',
            'remote_type' => 'Remote',
            'salary' => $salary,
            'annual_salary_usd' => $annualSalaryUsd,
            'posted_at' => $postedAt,
        ];
    }
}
