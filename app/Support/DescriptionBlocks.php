<?php

namespace App\Support;

use App\Services\Redactor;

/**
 * Parses the source's own block markup (h1-6/p/ul/ol) into a typed structure
 * instead of flattening everything to plain lines — that's what lets the
 * page render real headings and real lists instead of a wall of text with
 * "•" characters typed into it.
 *
 * Ported from the Next.js version's src/lib/descriptionBlocks.ts.
 */
class DescriptionBlocks
{
    private const HTML_ENTITIES = [
        '&amp;' => '&',
        '&lt;' => '<',
        '&gt;' => '>',
        '&quot;' => '"',
        '&#39;' => "'",
        '&nbsp;' => ' ',
    ];

    private static function decodeEntities(string $input): string
    {
        $decoded = $input;
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

        return $decoded;
    }

    private static function stripInlineTags(string $html): string
    {
        return trim(preg_replace('/<[^>]+>/', '', $html));
    }

    /**
     * Returns null when the source didn't actually contain recognizable
     * block markup (or when the parse looks like it dropped content vs. the
     * trusted plain-text version) so the caller can fall back to the old
     * flat rendering rather than risk showing a truncated description.
     *
     * @return array<int, array{type: string, text?: string, items?: array<int, string>}>|null
     */
    public static function parse(string $html): ?array
    {
        $decoded = preg_replace('#</?div[^>]*>#i', '', self::decodeEntities($html));
        $blocks = [];
        $matchedAny = false;

        preg_match_all('#<(h[1-6]|ul|ol|p)[^>]*>(.*?)</\1>#is', $decoded, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $matchedAny = true;
            $tag = strtolower($match[1]);
            $inner = $match[2];

            if (preg_match('/^h[1-6]$/', $tag)) {
                $text = self::stripInlineTags($inner);
                if ($text !== '') {
                    $blocks[] = ['type' => 'heading', 'text' => $text];
                }
            } elseif ($tag === 'ul' || $tag === 'ol') {
                preg_match_all('#<li[^>]*>(.*?)</li>#is', $inner, $itemMatches);
                $items = array_values(array_filter(array_map(
                    fn ($item) => self::stripInlineTags($item),
                    $itemMatches[1]
                )));
                if (count($items) > 0) {
                    $blocks[] = ['type' => $tag === 'ul' ? 'bullets' : 'numbered', 'items' => $items];
                }
            } elseif ($tag === 'p') {
                $text = self::stripInlineTags($inner);
                if ($text !== '') {
                    $blocks[] = ['type' => 'paragraph', 'text' => $text];
                }
            }
        }

        if (! $matchedAny) {
            return null;
        }

        $reconstructed = trim(preg_replace('/\s+/', ' ', implode(' ', array_map(
            fn ($b) => $b['type'] === 'bullets' || $b['type'] === 'numbered' ? implode(' ', $b['items']) : $b['text'],
            $blocks
        ))));
        $trusted = trim(preg_replace('/\s+/', ' ', Format::stripHtml($html)));

        // A parse that recovered meaningfully less text than the trusted
        // flat strip did means some content fell outside the tags this
        // parser recognizes — safer to fall back than to show a silently
        // truncated description.
        if (mb_strlen($trusted) > 0 && mb_strlen($reconstructed) < mb_strlen($trusted) * 0.8) {
            return null;
        }

        return $blocks;
    }

    /**
     * Redacts every leaf of text across all blocks in one pass so
     * redactEmployerIdentity's "About X" self-declared-name detection still
     * sees the whole description at once — running it block-by-block would
     * miss a name introduced in one paragraph but repeated in another.
     *
     * @param  array<int, array{type: string, text?: string, items?: array<int, string>}>  $blocks
     * @return array<int, array{type: string, text?: string, items?: array<int, string>}>
     */
    public static function redact(array $blocks, string $company): array
    {
        $marker = "\n\u{241F}\u{241F}\u{241F}\n";
        $leaves = [];
        foreach ($blocks as $block) {
            if ($block['type'] === 'bullets' || $block['type'] === 'numbered') {
                array_push($leaves, ...$block['items']);
            } else {
                $leaves[] = $block['text'];
            }
        }

        $redactedLeaves = explode($marker, app(Redactor::class)->redactEmployerIdentity(implode($marker, $leaves), $company));

        $cursor = 0;

        return array_map(function ($block) use ($redactedLeaves, &$cursor) {
            if ($block['type'] === 'bullets' || $block['type'] === 'numbered') {
                $block['items'] = array_map(fn () => $redactedLeaves[$cursor++], $block['items']);
            } else {
                $block['text'] = $redactedLeaves[$cursor++];
            }

            return $block;
        }, $blocks);
    }
}
