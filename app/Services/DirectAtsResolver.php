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
        'workable.com',
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
    ];

    public static function isDirectUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $urlLower = mb_strtolower($url);
        foreach (self::DIRECT_PATTERNS as $pattern) {
            if (str_contains($urlLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public static function isAggregatorUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $urlLower = mb_strtolower($url);
        foreach (self::AGGREGATOR_PATTERNS as $pattern) {
            if (str_contains($urlLower, $pattern)) {
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
        $patterns = implode('|', array_map('preg_quote', self::DIRECT_PATTERNS));
        if (preg_match('/https?:\/\/[^\s"\'<>]*(?:'.$patterns.')[^\s"\'<>]*/i', $content, $matches)) {
            return rtrim($matches[0], '.,;)');
        }

        return null;
    }
}
