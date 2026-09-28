<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class HackerNewsSource implements JobSource
{
    /**
     * Algolia public search API for Hacker News.
     * Fetches monthly "Ask HN: Who is hiring?" submissions where startup founders
     * and tech executives post direct remote roles with direct contact information.
     */
    private const ALGOLIA_BASE = 'https://hn.algolia.com/api/v1';

    public function fetch(): array
    {
        try {
            // 1. Identify the latest monthly "Who is hiring?" story
            $storyResponse = Http::timeout(6)
                ->acceptJson()
                ->get(self::ALGOLIA_BASE.'/search_by_date', [
                    'tags' => 'story,author_whoishiring',
                    'query' => 'Ask HN: Who is hiring',
                    'hitsPerPage' => 1,
                ]);

            if ($storyResponse->failed()) {
                Log::warning('[hn] failed to retrieve latest Who is hiring story', ['status' => $storyResponse->status()]);

                return [];
            }

            $story = $storyResponse->json('hits.0');
            if (! $story || empty($story['objectID'])) {
                return [];
            }

            $storyId = $story['objectID'];

            // 2. Fetch comments from this story tagged with REMOTE
            $commentsResponse = Http::timeout(8)
                ->acceptJson()
                ->get(self::ALGOLIA_BASE.'/search', [
                    'tags' => "comment,story_{$storyId}",
                    'query' => 'REMOTE',
                    'hitsPerPage' => 35,
                ]);

            if ($commentsResponse->failed()) {
                Log::warning('[hn] failed to fetch remote comments', ['status' => $commentsResponse->status()]);

                return [];
            }

            $comments = $commentsResponse->json('hits', []);

            return collect($comments)
                ->map(fn ($comment) => $this->normalizeComment($comment, $storyId))
                ->filter()
                ->values()
                ->all();
        } catch (Throwable $e) {
            Log::warning('[hn] fetch failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    private function normalizeComment(array $comment, string|int $storyId): ?array
    {
        $rawHtml = $comment['comment_text'] ?? '';
        if (empty($rawHtml)) {
            return null;
        }

        $plain = trim(html_entity_decode(strip_tags($rawHtml)));
        if (mb_strlen($plain) < 50) {
            return null; // Skip brief meta/discussion comments
        }

        // Must explicitly mention remote
        if (! str_contains(mb_strtolower($plain), 'remote')) {
            return null;
        }

        // Standard HN Who is Hiring format: Company | Role | Location | ...
        $firstLine = strtok($plain, "\n");
        $parts = array_map('trim', explode('|', (string) $firstLine));

        $company = 'Tech Startup (via HN)';
        $title = 'Remote Software Opportunity';
        $location = 'Remote / Worldwide';

        if (count($parts) >= 2) {
            $company = mb_substr($parts[0], 0, 80);
            $title = mb_substr($parts[1], 0, 100);
            if (isset($parts[2])) {
                $location = mb_substr($parts[2], 0, 80);
            }
        } elseif (preg_match('/^([A-Za-z0-9\.\-\s]{2,40})\s+(?:is hiring|seeks|looking for)\s+(.+?)(?:\.|$)/i', $firstLine, $matches)) {
            $company = trim($matches[1]);
            $title = trim($matches[2]);
        }

        // Clean up title if it contains boilerplate
        if (mb_stripos($title, 'is hiring') !== false) {
            $title = str_ireplace('is hiring', '', $title);
        }
        $title = trim($title, " :-–—\t\n\r\0\x0B");
        if (empty($title)) {
            $title = 'Remote Engineering & Technology Role';
        }

        $commentId = $comment['objectID'];
        $postedAt = isset($comment['created_at']) ? Carbon::parse($comment['created_at']) : now();

        return [
            'id' => "hn-{$storyId}--{$commentId}",
            'source_name' => 'Hacker News Direct',
            'source_id' => (string) $commentId,
            'source_url' => "https://news.ycombinator.com/item?id={$commentId}",
            'title' => $title,
            'company' => $company,
            'description' => "<p><strong>Direct Hiring Post from Y Combinator's Hacker News:</strong></p>"
                ."<div class=\"mt-3 space-y-2 text-sm\">{$rawHtml}</div>"
                ."<p class=\"mt-4 text-xs text-slate-500\">Apply or contact the founder/team directly on the Hacker News discussion thread: <a href=\"https://news.ycombinator.com/item?id={$commentId}\" target=\"_blank\" rel=\"noopener\" class=\"text-primary underline font-medium\">Open Hacker News Post &rarr;</a></p>",
            'tags' => ['Hacker News', 'Startup', 'Direct Founder'],
            'location' => $location ?: 'Remote / Worldwide',
            'remote_type' => 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => $postedAt,
        ];
    }
}
