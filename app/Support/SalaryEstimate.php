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
    public static function estimateHourlyUsd(array|int|float|null $annual = null, ?string $salaryString = null): ?array
    {
        if (is_numeric($annual)) {
            $annual = ['min' => (float) $annual, 'max' => (float) $annual];
        }

        if (is_array($annual)) {
            $lo = $annual['min'] ?? $annual['max'] ?? null;
            $hi = $annual['max'] ?? $annual['min'] ?? null;
            if ($lo !== null && $hi !== null && ($lo >= self::MIN_PLAUSIBLE_ANNUAL_USD || $hi >= self::MIN_PLAUSIBLE_ANNUAL_USD)) {
                return [
                    'min' => round($lo / self::HOURS_PER_YEAR),
                    'max' => round($hi / self::HOURS_PER_YEAR),
                ];
            }
        }

        if ($salaryString) {
            // Match explicit hourly rate e.g. "$15 - $25/hr", "$50-$75 /hour", "$20/hr"
            if (preg_match('/\$([0-9]+(?:\.[0-9]+)?)(?:\s*-\s*\$?([0-9]+(?:\.[0-9]+)?))?\s*\/(?:hr|hour)/i', $salaryString, $matches)) {
                $lo = (float) $matches[1];
                $hi = isset($matches[2]) && $matches[2] !== '' ? (float) $matches[2] : $lo;

                return [
                    'min' => $lo,
                    'max' => $hi,
                ];
            }

            // Match annual "k" notation e.g. "$35.3k - $52k", "$50k - $80k", "$35,3k- $52k"
            $normalizedSalary = str_replace(',', '.', $salaryString);
            if (preg_match('/\$([0-9]+(?:\.[0-9]+)?)\s*k(?:\s*-\s*\$?([0-9]+(?:\.[0-9]+)?)\s*k)?/i', $normalizedSalary, $matches)) {
                $lo = ((float) $matches[1]) * 1000;
                $hi = isset($matches[2]) && $matches[2] !== '' ? ((float) $matches[2]) * 1000 : $lo;

                return [
                    'min' => round($lo / self::HOURS_PER_YEAR),
                    'max' => round($hi / self::HOURS_PER_YEAR),
                ];
            }

            // Match standard annual range e.g. "$50,000 - $80,000"
            if (preg_match('/\$([0-9]{1,3}(?:,[0-9]{3})+)(?:\s*-\s*\$?([0-9]{1,3}(?:,[0-9]{3})+))?/i', $salaryString, $matches)) {
                $lo = (float) str_replace(',', '', $matches[1]);
                $hi = isset($matches[2]) && $matches[2] !== '' ? (float) str_replace(',', '', $matches[2]) : $lo;

                if ($lo >= self::MIN_PLAUSIBLE_ANNUAL_USD || $hi >= self::MIN_PLAUSIBLE_ANNUAL_USD) {
                    return [
                        'min' => round($lo / self::HOURS_PER_YEAR),
                        'max' => round($hi / self::HOURS_PER_YEAR),
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Formats an hourly estimate into a clean string e.g. "$20 - $35/hr" or "$25/hr".
     *
     * @param  array{min: float|int, max: float|int}|null  $hourly
     */
    public static function formatHourly(?array $hourly): ?string
    {
        if (! $hourly) {
            return null;
        }

        $min = $hourly['min'];
        $max = $hourly['max'];

        if ($min <= 0 && $max <= 0) {
            return null;
        }

        $minStr = ($min == (int) $min) ? (string) (int) $min : number_format($min, 2);
        $maxStr = ($max == (int) $max) ? (string) (int) $max : number_format($max, 2);

        if ($min === $max || $minStr === $maxStr) {
            return '$'.$minStr.'/hr';
        }

        return '$'.$minStr.' - $'.$maxStr.'/hr';
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
