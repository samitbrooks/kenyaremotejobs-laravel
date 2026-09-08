<?php

namespace App\Support;

/**
 * Cheap, local heuristic — not a real language detector, just enough signal
 * to decide whether a "Translate" button is worth showing on a listing.
 * Counts common function words for English vs. a handful of other languages
 * these job boards actually surface (German via Arbeitnow, Spanish/
 * Portuguese via RemoteOK). English wins ties: the button only appears when
 * another language clearly outscores it, so it never nags an English reader.
 *
 * Ported from the Next.js version's src/lib/language.ts.
 */
class Language
{
    private const ENGLISH_WORDS = [
        'the', 'and', 'you', 'your', 'with', 'for', 'our', 'this', 'that', 'are',
        'is', 'we', 'will', 'have', 'from', 'role', 'team', 'work', 'experience',
    ];

    private const OTHER_LANGUAGE_WORDS = [
        // German
        'und', 'der', 'die', 'das', 'mit', 'für', 'ist', 'wir', 'sie', 'nicht', 'auch',
        // Spanish
        'de', 'la', 'el', 'los', 'las', 'para', 'con', 'que', 'una', 'un', 'es', 'en',
        // Portuguese
        'de', 'para', 'com', 'não', 'você', 'uma', 'um', 'está',
        // French
        'le', 'la', 'les', 'des', 'vous', 'nous', 'pour', 'avec', 'est', 'une',
    ];

    public static function looksNonEnglish(string $text): bool
    {
        $cleaned = preg_replace('/[^a-zà-ÿ\s]/ui', ' ', mb_strtolower($text));
        $words = array_slice(array_values(array_filter(preg_split('/\s+/u', $cleaned))), 0, 400);

        if (count($words) < 20) {
            return false;
        }

        $englishHits = count(array_intersect($words, self::ENGLISH_WORDS));
        $otherHits = 0;
        $otherSet = array_flip(self::OTHER_LANGUAGE_WORDS);
        foreach ($words as $word) {
            if (isset($otherSet[$word])) {
                $otherHits++;
            }
        }

        return $otherHits > $englishHits * 1.5 && $otherHits >= 6;
    }
}
