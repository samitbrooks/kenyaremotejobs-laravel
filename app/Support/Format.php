<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Ported from the Next.js version's src/lib/format.ts.
 */
class Format
{
    private const HTML_ENTITIES = [
        '&amp;' => '&',
        '&lt;' => '<',
        '&gt;' => '>',
        '&quot;' => '"',
        '&#39;' => "'",
        '&nbsp;' => ' ',
    ];

    /**
     * Job descriptions from upstream APIs are HTML, and some sources (e.g.
     * RemoteOK) double-encode entities, so a raw "<p>" can itself be spelled
     * "&lt;p&gt;". Decode entities first so those tags are still real tags by
     * the time we strip markup — otherwise the stripped-down text still shows
     * literal "<p>" to the reader. We never render raw HTML from a third party.
     */
    public static function stripHtml(string $html): string
    {
        $decoded = $html;
        for ($i = 0; $i < 3; $i++) {
            $next = preg_replace_callback(
                '/&[a-z#0-9]+;/i',
                fn ($m) => self::HTML_ENTITIES[$m[0]] ?? $m[0],
                $decoded
            );
            if ($next === $decoded) {
                break;
            }
            $decoded = $next;
        }

        $withBreaks = preg_replace('#</(p|div|li|h[1-6])>#i', "\n\n", $decoded);
        $withBreaks = preg_replace('#<br\s*/?>#i', "\n", $withBreaks);
        $withBreaks = preg_replace('/<li>/i', '• ', $withBreaks);

        $textOnly = preg_replace('/<[^>]+>/', '', $withBreaks);

        $lines = array_map('trim', explode("\n", $textOnly));

        return trim(preg_replace('/\n{3,}/', "\n\n", implode("\n", $lines)));
    }

    public static function truncate(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxLength)).'…';
    }

    public static function estimateReadMinutes(string $content): int
    {
        $words = count(array_filter(preg_split('/\s+/', trim($content))));

        return max(1, (int) round($words / 200));
    }

    /**
     * Turns every "[Employer]" redaction placeholder in a piece of
     * description text into a link to the employer/recruiter side of the
     * site — a low-friction way for the (likely occasional-recruiter-too)
     * reader to notice that side of the product exists. Only use this on
     * text that isn't already inside a link (the job cards wrap the whole
     * card in a link to the job itself — nesting an anchor inside that
     * would be invalid HTML), which is why it's applied on the job detail
     * page only. Returns safe HTML — the surrounding text is escaped, only
     * the anchor markup itself is raw.
     */
    public static function linkifyEmployerPlaceholder(string $text): string
    {
        $placeholder = '[Employer]';
        if (! str_contains($text, $placeholder)) {
            return e($text);
        }

        $link = '<a href="'.e(url('/employers')).'" title="Hiring? See how to post a role here" class="font-medium text-horizon-600 underline decoration-horizon-300 decoration-dotted underline-offset-2 hover:decoration-horizon-600">[Employer]</a>';

        return implode($link, array_map('e', explode($placeholder, $text)));
    }

    public static function timeAgo(CarbonInterface $date): string
    {
        // Carbon 3's diff*() methods default to signed results (negative
        // for a date in the past), unlike Carbon 2 — absolute: true
        // restores the "how long ago" behavior every caller here expects.
        $diffSec = (int) now()->diffInSeconds($date, absolute: true);

        if ($diffSec < 60) {
            return 'just now';
        }
        $diffMin = intdiv($diffSec, 60);
        if ($diffMin < 60) {
            return "{$diffMin}m ago";
        }
        $diffHour = intdiv($diffMin, 60);
        if ($diffHour < 24) {
            return "{$diffHour}h ago";
        }
        $diffDay = intdiv($diffHour, 24);
        if ($diffDay < 30) {
            return "{$diffDay}d ago";
        }
        $diffMonth = intdiv($diffDay, 30);

        return "{$diffMonth}mo ago";
    }
}
