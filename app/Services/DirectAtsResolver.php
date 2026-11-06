<?php

namespace App\Services;

class DirectAtsResolver
{
    /**
     * Recognized direct ATS domains and employer applicant systems.
     * These platforms allow candidates to apply directly to the employer
     * without having to register accounts on intermediate listing boards.
     */
    private const DIRECT_PATTERNS = [
        'greenhouse.io',
        'ashbyhq.com',
        'lever.co',
        'apply.workable.com',
        'bamboohr.com',
        'smartrecruiters.com',
        'recruitee.com',
        'myworkdayjobs.com',
        'workday.com',
        'icims.com',
        'jobvite.com',
        'rippling-ats.com',
        'pinpointhq.com',
        'personio.com',
        'teamtailor.com',
        'micro1.ai',
        'turing.com',
    ];

    /**
     * Aggregator domains known for gating, intercepting, or forcing candidate registrations.
     */
    private const AGGREGATOR_PATTERNS = [
        'remoteok.com',
        'remoteok.io',
        'jobicy.com',
        'himalayas.app',
        'remotive.com',
        'remotive.io',
        'arbeitnow.com',
        'jobs.workable.com',
        'linkedin.com',
        'indeed.com',
        'glassdoor.com',
        'flexjobs.com',
        'news.ycombinator.com',
    ];

    public static function isDirectUrl(?string $url): bool
    {
        return self::matchesHost($url, self::DIRECT_PATTERNS);
    }

    public static function isAggregatorUrl(?string $url): bool
    {
        return self::matchesHost($url, self::AGGREGATOR_PATTERNS);
    }

    public static function isHiringPlatformUrl(?string $url): bool
    {
        return self::matchesHost($url, ['micro1.ai', 'turing.com', 'outlier.ai', 'alignerr.com', 'dataannotation.tech', 'oneforma.com', 'appen.com']);
    }

    public static function isEmployerApplicationUrl(?string $url): bool
    {
        return self::isDirectUrl($url)
            && ! self::isHiringPlatformUrl($url)
            && strlen(trim((string) parse_url($url, PHP_URL_PATH), '/')) > 0;
    }

    /**
     * @param  array<int, string>  $domains
     */
    private static function matchesHost(?string $url, array $domains): bool
    {
        if (! $url || ! in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            return false;
        }
        $host = strtolower(rtrim((string) parse_url($url, PHP_URL_HOST), '.'));
        foreach ($domains as $domain) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolves the best direct application URL from a job's source_url and description.
     */
    public static function resolve(?string $sourceUrl, ?string $description = null): string
    {
        if (self::isDirectUrl($sourceUrl)) {
            return $sourceUrl ?? '';
        }

        // If the current URL is an aggregator, look inside the description for a direct ATS link
        if ($description) {
            $extracted = self::extractDirectLink($description);
            if ($extracted) {
                return $extracted;
            }
        }

        return $sourceUrl ?? '';
    }

    /**
     * Extracts the first valid direct ATS link from text/html.
     */
    public static function extractDirectLink(string $content): ?string
    {
        preg_match_all('/https?:\/\/[^\s"\'<>]+/i', html_entity_decode($content, ENT_QUOTES | ENT_HTML5), $matches);
        foreach ($matches[0] as $match) {
            $url = rtrim($match, '.,;)');
            if (self::isEmployerApplicationUrl($url)) {
                return $url;
            }
        }

        return null;
    }
}
