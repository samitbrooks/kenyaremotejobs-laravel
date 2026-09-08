<?php

namespace App\Support;

/**
 * Ported from the Next.js version's src/lib/kenyaRelevance.ts.
 */
class KenyaRelevance
{
    private const POSITIVE_LOCATION_SIGNALS = [
        'worldwide',
        'anywhere',
        'global',
        'africa',
        'remote (global)',
    ];

    private const POSITIVE_TIMEZONE_SIGNALS = [
        'eat',
        'gmt+3',
        'gmt +3',
        'utc+3',
        'utc +3',
        'europe',
        'emea',
        'africa',
        'flexible timezone',
        'flexible hours',
        'async',
        'asynchronous',
        'any timezone',
        'any time zone',
    ];

    // Matches things like "UTC-3 to UTC+4" or "GMT-2/+5" so we can tell
    // whether a stated offset window actually overlaps East Africa Time
    // (UTC+3).
    private const TIMEZONE_RANGE_REGEX = '/(?:UTC|GMT)\s*([+-]\d{1,2})\s*(?:to|-|–|through)\s*(?:UTC|GMT)?\s*([+-]\d{1,2})/i';

    private const NEGATIVE_SIGNALS = [
        'us only',
        'u.s. only',
        'usa only',
        'united states only',
        'us-based only',
        'us citizens only',
        'must be based in the us',
        'must reside in the us',
        'eu only',
        'eu-based only',
        'europe only',
        'uk only',
        'united kingdom only',
        'must be located in the us',
        'must be located in europe',
        'visa sponsorship not available',
        'no visa sponsorship',
        'visa required',
        'we cannot sponsor',
        'this position is not open to candidates outside',
    ];

    /**
     * @param  array{location: string, description: string, title?: string, tags?: array<int, string>}  $job
     * @return array{kenyaFriendly: bool, score: int, reasons: array<int, string>}
     */
    public static function score(array $job): array
    {
        $location = mb_strtolower($job['location']);
        $description = mb_strtolower($job['description']);
        $haystack = "{$location} {$description}";
        $reasons = [];
        $score = 0;

        foreach (self::POSITIVE_LOCATION_SIGNALS as $signal) {
            if (str_contains($location, $signal)) {
                $score += 2;
                $reasons[] = "Location mentions \"{$signal}\"";
                break;
            }
        }

        foreach (self::POSITIVE_TIMEZONE_SIGNALS as $signal) {
            if (str_contains($description, $signal) || str_contains($location, $signal)) {
                $score += 1;
                $reasons[] = "Mentions \"{$signal}\"";
            }
        }

        if (preg_match_all(self::TIMEZONE_RANGE_REGEX, $haystack, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $from = (int) $match[1];
                $to = (int) $match[2];
                $lo = min($from, $to);
                $hi = max($from, $to);
                // East Africa Time is UTC+3; treat -3..+4 as a comfortable
                // overlap window.
                if ($lo <= 4 && $hi >= -3) {
                    $score += 2;
                    $reasons[] = "Timezone range {$match[0]} overlaps EAT (UTC+3)";
                }
            }
        }

        $hasHardExclusion = false;
        foreach (self::NEGATIVE_SIGNALS as $signal) {
            if (str_contains($haystack, $signal)) {
                $hasHardExclusion = true;
                $reasons[] = "Restriction detected: \"{$signal}\"";
            }
        }

        if ($hasHardExclusion) {
            $score = max(0, $score - 5);
        }

        $kenyaFriendly = ! $hasHardExclusion && $score >= 2;

        return ['kenyaFriendly' => $kenyaFriendly, 'score' => $score, 'reasons' => $reasons];
    }
}
