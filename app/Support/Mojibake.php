<?php

namespace App\Support;

/**
 * Some upstream sources (confirmed on RemoteOK) serve their own JSON already
 * double-encoded: real UTF-8 bytes got decoded once as Latin-1 on their end,
 * so what we receive as "tecnolÃ³gico" is genuinely supposed to be
 * "tecnológico". Reversing that — reinterpret the string's code points as
 * Latin-1 bytes, then decode those bytes as UTF-8 — recovers the original
 * text.
 *
 * Ported from the Next.js version's src/lib/mojibake.ts.
 */
class Mojibake
{
    /**
     * Only attempt the repair when every character fits in a byte (0–255); a
     * string that already contains a real multi-byte code point (an em dash,
     * an emoji, a properly-decoded accented letter) can't be this kind of
     * mojibake, and reinterpreting it as bytes would corrupt it. As a second
     * guard, only keep the result if it decoded cleanly — an invalid byte
     * sequence surfaces as the U+FFFD replacement character, which means the
     * input wasn't actually mojibake in the first place.
     */
    public static function repair(string $input): string
    {
        if (! mb_check_encoding($input, 'UTF-8')) {
            return $input;
        }

        $bytes = '';
        foreach (mb_str_split($input) as $char) {
            $codepoint = mb_ord($char, 'UTF-8');
            if ($codepoint === false || $codepoint > 0xFF) {
                return $input;
            }
            $bytes .= chr($codepoint);
        }

        // $bytes now holds exactly the byte sequence
        // Buffer.from(input, 'latin1') would produce. Reinterpreting it as
        // UTF-8 is then just validating it as such — a PHP string IS its raw
        // bytes, so no further decode step is needed once it's confirmed valid.
        if (! mb_check_encoding($bytes, 'UTF-8')) {
            return $input;
        }

        return $bytes;
    }

    /**
     * @param  array<string, mixed>  $job
     * @return array<string, mixed>
     */
    public static function repairJob(array $job): array
    {
        $job['title'] = self::repair($job['title']);
        $job['company'] = self::repair($job['company']);
        $job['description'] = self::repair($job['description']);
        $job['location'] = self::repair($job['location']);
        $job['tags'] = array_map(fn ($tag) => self::repair($tag), $job['tags']);

        return $job;
    }
}
