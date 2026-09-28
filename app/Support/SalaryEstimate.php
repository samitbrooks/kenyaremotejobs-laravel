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
     * @param  array{min: ?float, max: ?float}|int|float|null  $annual
     * @return array{min: float, max: float}|null
     */
    public static function estimateHourlyUsd(array|int|float|null $annual): ?array
    {
        if (is_numeric($annual)) {
            $annual = ['min' => (float) $annual, 'max' => (float) $annual];
        }

        if (! is_array($annual)) {
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

    /**
     * Estimates the gross monthly compensation in Kenyan Shillings (KES)
     * using current average mid-market conversion rates (~130 KES / 1 USD).
     *
     * @param  array{min: ?float, max: ?float}|int|float|null  $annual
     */
    public static function estimateMonthlyKes(array|int|float|null $annual, ?string $salaryString = null, float $usdToKesRate = 130.0): ?string
    {
        if (is_numeric($annual)) {
            $annual = ['min' => (float) $annual, 'max' => (float) $annual];
        }

        $minAnnual = is_array($annual) ? ($annual['min'] ?? null) : null;
        $maxAnnual = is_array($annual) ? ($annual['max'] ?? null) : null;

        if ($minAnnual !== null && $minAnnual < self::MIN_PLAUSIBLE_ANNUAL_USD) {
            $minAnnual = null;
        }
        if ($maxAnnual !== null && $maxAnnual < self::MIN_PLAUSIBLE_ANNUAL_USD) {
            $maxAnnual = null;
        }

        // If annual is provided and plausible
        if ($minAnnual !== null || $maxAnnual !== null) {
            $lo = $minAnnual ?? $maxAnnual;
            $hi = $maxAnnual ?? $minAnnual;

            $loMonthlyKes = (int) round(($lo / 12) * $usdToKesRate / 1000) * 1000;
            $hiMonthlyKes = (int) round(($hi / 12) * $usdToKesRate / 1000) * 1000;

            if ($loMonthlyKes > 0 && $loMonthlyKes === $hiMonthlyKes) {
                return '~KES '.number_format($loMonthlyKes).'/mo';
            }

            if ($loMonthlyKes > 0 && $hiMonthlyKes > 0) {
                return '~KES '.number_format($loMonthlyKes).' - '.number_format($hiMonthlyKes).'/mo';
            }
        }

        // Fallback: parse raw salary string if USD figures are present
        if ($salaryString && preg_match('/\$([0-9]{1,3}(?:,[0-9]{3})*|\d+)/', $salaryString, $matches)) {
            $rawNumber = (float) str_replace(',', '', $matches[1]);
            $isMonthly = (bool) preg_match('/(\/mo|per month|month)/i', $salaryString);

            if ($isMonthly && $rawNumber >= 300) {
                $kes = (int) round($rawNumber * $usdToKesRate / 1000) * 1000;

                return '~KES '.number_format($kes).'/mo';
            }

            if (! $isMonthly && $rawNumber >= self::MIN_PLAUSIBLE_ANNUAL_USD) {
                $kes = (int) round(($rawNumber / 12) * $usdToKesRate / 1000) * 1000;

                return '~KES '.number_format($kes).'/mo';
            }
        }

        return null;
    }
}
