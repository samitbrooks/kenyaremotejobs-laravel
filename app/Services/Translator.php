<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Ported from the Next.js version's src/app/api/translate/route.ts.
 */
class Translator
{
    // Splits on blank lines / sentence boundaries so each request stays
    // under the free endpoint's URL-length limit without ever cutting a
    // word in half.
    private const MAX_CHUNK_CHARS = 1500;

    private const MAX_TEXT_LENGTH = 8000;

    public function translate(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            throw new RuntimeException('No text to translate.');
        }
        if (mb_strlen($text) > self::MAX_TEXT_LENGTH) {
            throw new RuntimeException('Text too long to translate.');
        }

        $chunks = $this->chunkText($text);
        $translated = array_map(fn ($chunk) => $this->translateChunk($chunk), $chunks);

        return implode("\n\n", $translated);
    }

    /**
     * @return array<int, string>
     */
    private function chunkText(string $text): array
    {
        $paragraphs = preg_split('/\n{2,}/', $text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $candidate = $current ? "{$current}\n\n{$paragraph}" : $paragraph;
            if (mb_strlen($candidate) <= self::MAX_CHUNK_CHARS) {
                $current = $candidate;

                continue;
            }
            if ($current) {
                $chunks[] = $current;
            }
            $current = mb_strlen($paragraph) <= self::MAX_CHUNK_CHARS ? $paragraph : mb_substr($paragraph, 0, self::MAX_CHUNK_CHARS);
        }
        if ($current) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    // No API key required — this is Google's public, unauthenticated "gtx"
    // endpoint that translate.google.com's own web page calls. It's
    // unofficial and could change or start rate-limiting without notice; if
    // that ever happens, swap this one method for a real Cloud Translation
    // API key — nothing else in the feature needs to change.
    private function translateChunk(string $text): string
    {
        $response = Http::withUserAgent('Mozilla/5.0')
            ->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => 'auto',
                'tl' => 'en',
                'dt' => 't',
                'q' => $text,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Translate request failed: {$response->status()}");
        }

        $segments = $response->json(0, []);

        return implode('', array_map(fn ($segment) => $segment[0], $segments));
    }
}
