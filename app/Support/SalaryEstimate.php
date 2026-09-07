<?php

namespace App\Support;

/**
 * Ported from the Next.js version's src/lib/salaryEstimate.ts.
 */
class SalaryEstimate
{
    private const HOURS_PER_YEAR = 2080; // standard full-time: 40 hrs/week × 52 weeks

    // Guards against non-annual figures slipping through (e.g. a day rate or
    // an already-hourly number in the same field) by refusing to treat
    // anything implausibly small as an annual salary.
    private const MIN_PLAUSIBLE_ANNUAL_USD = 3000;

    /**
     * @param  array{min: ?float, max: ?float}|null  $annual
     * @return array{min: float, max: float}|null
     */
    public static function estimateHourlyUsd(?array $annual): ?array
    {
        if (! $annual) {
            return null;
        }

        $lo = $annual['min'] ?? $annual['max'] ?? null;
        $hi = $annual['max'] ?? $annual['min'] ?? null;
        if ($lo === null || $hi === null) {
            return null;
        }
        if ($lo < self::MIN_PLAUSIBLE_ANNUAL_USD && $hi < self::MIN_PLAUSIBLE_ANNUAL_USD) {
            return null;
        }

        return [
            'min' => round($lo / self::HOURS_PER_YEAR, 2),
            'max' => round($hi / self::HOURS_PER_YEAR, 2),
        ];
    }
}
