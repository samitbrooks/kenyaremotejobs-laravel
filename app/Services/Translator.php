<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class Translator
{
    private const MAX_CHUNK_CHARS = 1200;

    private const MAX_TEXT_LENGTH = 20000;

    /**
     * Translates the given text into English with automatic caching and multi-provider failover.
     */
    public function translate(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            throw new RuntimeException('No text to translate.');
        }

        if (mb_strlen($text) > self::MAX_TEXT_LENGTH) {
            $text = mb_substr($text, 0, self::MAX_TEXT_LENGTH);
        }

        $cacheKey = 'job_trans_en_'.md5($text);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($text) {
            $chunks = $this->chunkText($text);
            $translated = [];

            foreach ($chunks as $chunk) {
                $translated[] = $this->translateChunkWithFallbacks($chunk);
            }

            return implode("\n\n", $translated);
        });
    }

    /**
     * Splits long text into manageable chunks respecting paragraphs, sentences, and line breaks.
     *
     * @return array<int, string>
     */
    private function chunkText(string $text): array
    {
        $paragraphs = preg_split('/\n{2,}/', $text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }

            // If single paragraph is already within limit
            if (mb_strlen($paragraph) <= self::MAX_CHUNK_CHARS) {
                $candidate = $current ? "{$current}\n\n{$paragraph}" : $paragraph;
                if (mb_strlen($candidate) <= self::MAX_CHUNK_CHARS) {
                    $current = $candidate;
                } else {
                    if ($current !== '') {
                        $chunks[] = $current;
                    }
                    $current = $paragraph;
                }

                continue;
            }

            // Paragraph exceeds limit: flush current buffer first
            if ($current !== '') {
                $chunks[] = $current;
                $current = '';
            }

            // Break oversized paragraph into sentence/line pieces
            $subPieces = preg_split('/(?<=[.?!;])\s+|\n+/', $paragraph);
            $subBuffer = '';

            foreach ($subPieces as $piece) {
                $piece = trim($piece);
                if ($piece === '') {
                    continue;
                }

                if (mb_strlen($piece) > self::MAX_CHUNK_CHARS) {
                    // Very long single token/run - hard split
                    $parts = mb_str_split($piece, self::MAX_CHUNK_CHARS);
                    foreach ($parts as $part) {
                        if ($subBuffer !== '') {
                            $chunks[] = $subBuffer;
                            $subBuffer = '';
                        }
                        $chunks[] = $part;
                    }

                    continue;
                }

                $subCandidate = $subBuffer ? "{$subBuffer} {$piece}" : $piece;
                if (mb_strlen($subCandidate) <= self::MAX_CHUNK_CHARS) {
                    $subBuffer = $subCandidate;
                } else {
                    $chunks[] = $subBuffer;
                    $subBuffer = $piece;
                }
            }

            if ($subBuffer !== '') {
                $chunks[] = $subBuffer;
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return ! empty($chunks) ? $chunks : [$text];
    }

    /**
     * Translates a single text chunk with multi-provider failover.
     */
    private function translateChunkWithFallbacks(string $chunk): string
    {
        $chunk = trim($chunk);
        if ($chunk === '') {
            return '';
        }

        // Strategy 1: Google Translate using Chrome Extension client (bypasses datacenter 429 blocks)
        try {
            $res = $this->callGoogleTranslate($chunk, 'dict-chrome-ex');
            if ($res !== '') {
                return $res;
            }
        } catch (Throwable $e) {
            Log::debug('[translator] Google dict-chrome-ex failed: '.$e->getMessage());
        }

        // Strategy 2: Google Translate using webapp client
        try {
            $res = $this->callGoogleTranslate($chunk, 'webapp');
            if ($res !== '') {
                return $res;
            }
        } catch (Throwable $e) {
            Log::debug('[translator] Google webapp failed: '.$e->getMessage());
        }

        // Strategy 3: Google Translate using gtx client
        try {
            $res = $this->callGoogleTranslate($chunk, 'gtx');
            if ($res !== '') {
                return $res;
            }
        } catch (Throwable $e) {
            Log::debug('[translator] Google gtx failed: '.$e->getMessage());
        }

        // Strategy 4: MyMemory Translation API
        try {
            $res = $this->callMyMemoryTranslate($chunk);
            if ($res !== '') {
                return $res;
            }
        } catch (Throwable $e) {
            Log::debug('[translator] MyMemory failed: '.$e->getMessage());
        }

        // Strategy 5: Gemini AI if configured
        try {
            $geminiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
            if ($geminiKey) {
                $res = $this->callGeminiTranslate($geminiKey, $chunk);
                if ($res !== '') {
                    return $res;
                }
            }
        } catch (Throwable $e) {
            Log::debug('[translator] Gemini fallback failed: '.$e->getMessage());
        }

        throw new RuntimeException('All translation providers failed to translate the chunk.');
    }

    private function callGoogleTranslate(string $text, string $client): string
    {
        $response = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36')
            ->timeout(8)
            ->get('https://translate.googleapis.com/translate_a/single', [
                'client' => $client,
                'sl' => 'auto',
                'tl' => 'en',
                'dt' => 't',
                'q' => $text,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Google Translate ({$client}) failed with status {$response->status()}");
        }

        $segments = $response->json(0, []);
        if (! is_array($segments)) {
            return '';
        }

        $translated = '';
        foreach ($segments as $segment) {
            if (isset($segment[0]) && is_string($segment[0])) {
                $translated .= $segment[0];
            }
        }

        return trim($translated);
    }

    private function callMyMemoryTranslate(string $text): string
    {
        $response = Http::timeout(8)
            ->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => 'Autodetect|en',
            ]);

        if ($response->failed()) {
            throw new RuntimeException("MyMemory request failed: {$response->status()}");
        }

        $data = $response->json('responseData', []);
        $translated = $data['translatedText'] ?? '';

        return is_string($translated) ? trim(html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '';
    }

    private function callGeminiTranslate(string $apiKey, string $text): string
    {
        $response = Http::timeout(10)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "Translate the following job description text accurately into English. Preserve formatting, bullet points, and structure. Only output the English translation without preamble or commentary:\n\n{$text}"],
                        ],
                    ],
                ],
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException("Gemini Translate failed: {$response->status()}");
        }

        $reply = $response->json('candidates.0.content.parts.0.text', '');

        return is_string($reply) ? trim($reply) : '';
    }
}
