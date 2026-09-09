<?php

// Some hosts run PHP without the real ext-mbstring loaded and rely on
// symfony/polyfill-mbstring to cover for it — but that polyfill deliberately
// doesn't implement mb_strcut() (see vendor/symfony/polyfill-mbstring), and
// league/commonmark (used to render every Markdown mail) calls it directly.
// Loaded unconditionally via composer.json's autoload.files, same mechanism
// Symfony's own polyfills use, so it's available before anything needs it.
if (! function_exists('mb_strcut')) {
    function mb_strcut(string $string, int $start, ?int $length = null, ?string $encoding = null): string
    {
        $cut = $length === null ? substr($string, $start) : substr($string, $start, $length);

        // mb_strcut never splits a multibyte character in half — trim a
        // trailing lead byte whose continuation bytes got cut off by the
        // plain byte-based substr() above.
        $cutLength = strlen($cut);
        for ($i = 1; $i <= 3 && $i <= $cutLength; $i++) {
            $byte = ord($cut[$cutLength - $i]);

            if ($byte < 0x80) {
                break; // ASCII byte — the sequence (if any) already ended before it
            }

            if ($byte >= 0xC0) {
                $sequenceLength = $byte >= 0xF0 ? 4 : ($byte >= 0xE0 ? 3 : 2);

                return $sequenceLength > $i ? substr($cut, 0, $cutLength - $i) : $cut;
            }

            // else a continuation byte (0x80-0xBF) — keep walking backward
        }

        return $cut;
    }
}
