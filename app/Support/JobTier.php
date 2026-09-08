<?php

namespace App\Support;

/**
 * Only RemoteOK and some Himalayas listings report a structured, currency-
 * confirmed annual salary — everything else (the majority of live listings)
 * has no reliable figure to bucket by and defaults to Basic rather than
 * being hidden or guessed into a pricier tier it was never confirmed to
 * deserve.
 *
 * Ported from the Next.js version's src/lib/jobTiers.ts computeTier().
 */
class JobTier
{
    /**
     * @param  array{min: ?int, max: ?int}|null  $annualSalaryUsd
     */
    public static function compute(?array $annualSalaryUsd): string
    {
        $figure = $annualSalaryUsd['max'] ?? $annualSalaryUsd['min'] ?? null;

        if ($figure === null) {
            return 'basic';
        }
        if ($figure >= config('jobs.premium_min_usd')) {
            return 'premium';
        }
        if ($figure >= config('jobs.intermediate_min_usd')) {
            return 'intermediate';
        }

        return 'basic';
    }
}
