<?php

namespace App\Support;

/**
 * Ported from the Next.js version's src/lib/matching.ts. Deterministic
 * keyword overlap — no external API, so it costs nothing to run per job.
 * Skills matching a job's own tags carry the most weight (tags are curated
 * per-listing); a role matching the title is a smaller signal; experience
 * isn't scored at all since junior/senior wording is inconsistent across
 * five different job-board sources (kept on the profile only so the form
 * remembers the user's choice).
 */
class Matching
{
    public const COOKIE_NAME = 'krj_profile';

    public const COOKIE_MINUTES = 180 * 24 * 60; // ~6 months

    /**
     * @return array{skills: array<int, string>, roles: array<int, string>, yearsExperience: int}|null
     */
    public static function parseProfileCookie(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }

        $parsed = json_decode($raw, true);
        if (! is_array($parsed) || ! is_array($parsed['skills'] ?? null) || ! is_array($parsed['roles'] ?? null)) {
            return null;
        }

        return [
            'skills' => array_values(array_filter($parsed['skills'], 'is_string')),
            'roles' => array_values(array_filter($parsed['roles'], 'is_string')),
            'yearsExperience' => (int) ($parsed['yearsExperience'] ?? 0),
        ];
    }

    /**
     * @param  array{skills: array<int, string>, roles: array<int, string>}  $profile
     * @param  array{tags: array<int, string>, title: string, description: string}  $job
     */
    public static function computeMatchPercent(array $profile, array $job): int
    {
        $skills = array_values(array_filter(array_map([self::class, 'normalize'], $profile['skills'])));
        $roles = array_values(array_filter(array_map([self::class, 'normalize'], $profile['roles'])));
        if (count($skills) === 0 && count($roles) === 0) {
            return 0;
        }

        $tags = array_map([self::class, 'normalize'], $job['tags']);
        $title = self::normalize($job['title']);
        $description = mb_substr(self::normalize($job['description']), 0, 2000);

        $skillPoints = 0;
        foreach ($skills as $skill) {
            $tagHit = false;
            foreach ($tags as $tag) {
                if (str_contains($tag, $skill) || str_contains($skill, $tag)) {
                    $tagHit = true;
                    break;
                }
            }
            if ($tagHit) {
                $skillPoints += 2;
            } elseif (str_contains($title, $skill) || str_contains($description, $skill)) {
                $skillPoints += 1;
            }
        }
        $skillScore = count($skills) > 0 ? min(1, $skillPoints / (count($skills) * 2)) : 0;

        $roleMatched = false;
        foreach ($roles as $role) {
            if (str_contains($title, $role)) {
                $roleMatched = true;
                break;
            }
        }
        $roleScore = count($roles) > 0 ? ($roleMatched ? 1 : 0) : 0;

        $weights = ['skill' => 0.75, 'role' => 0.25];
        $totalWeight = (count($skills) > 0 ? $weights['skill'] : 0) + (count($roles) > 0 ? $weights['role'] : 0);
        if ($totalWeight === 0.0) {
            return 0;
        }

        $weighted = ($skillScore * (count($skills) > 0 ? $weights['skill'] : 0)
            + $roleScore * (count($roles) > 0 ? $weights['role'] : 0)) / $totalWeight;

        return (int) round($weighted * 100);
    }

    private static function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
