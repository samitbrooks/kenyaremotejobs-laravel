<?php

namespace App\Support;

class SafeUrl
{
    /**
     * Sanitizes and validates a redirect path to ensure it is strictly a safe,
     * same-origin relative path, mitigating open redirect vulnerabilities.
     */
    public static function redirectPath(mixed $target, string $default = '/account'): string
    {
        if (! is_string($target) || $target === '') {
            return $default;
        }

        $target = trim($target);

        // Reject non-printable ASCII/control characters or backslashes
        if (preg_match('/[^\x20-\x7E]/', $target) || str_contains($target, '\\')) {
            return $default;
        }

        // Must start with single slash, not protocol-relative // or /\\
        if (! str_starts_with($target, '/') || str_starts_with($target, '//')) {
            return $default;
        }

        // Parse and ensure no host or scheme is present
        $parsed = parse_url($target);
        if ($parsed === false || isset($parsed['host']) || isset($parsed['scheme'])) {
            return $default;
        }

        return $target;
    }
}
