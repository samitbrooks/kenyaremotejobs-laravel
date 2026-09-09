<?php

namespace App\Support;

/**
 * Daraja's STK Push wants a phone number as 2547XXXXXXXX/2541XXXXXXXX (12
 * digits, country code, no leading + or 0) — but there's no reason to make
 * a buyer type it that way. Accepts whatever a Kenyan would naturally type
 * (0712345678, +254712345678, 254712345678, or bare 712345678) and
 * normalizes it, or returns null if it doesn't look like a real Safaricom/
 * Airtel-range Kenyan mobile number at all.
 */
class KenyanPhone
{
    public static function normalize(string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', $input);

        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            $local = substr($digits, 3);
        } elseif (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $local = substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $local = $digits;
        } else {
            return null;
        }

        // Kenyan mobile numbers start with 7 (Safaricom/most networks) or 1
        // (Safaricom's newer 01xx range) after the leading 0/254 is dropped.
        if (! preg_match('/^[71]\d{8}$/', $local)) {
            return null;
        }

        return '254'.$local;
    }
}
