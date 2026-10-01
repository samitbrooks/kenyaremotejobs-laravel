<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    private const ENDPOINT = 'https://api.indexnow.org/indexnow';

    public function getKey(): string
    {
        return (string) config('services.indexnow.key', '7b4e07a3c39542a39281a8b34f71a0dc');
    }

    /**
     * Submit an array of URLs to the IndexNow API (Bing, Yandex, Seznam, Naver).
     *
     * @param  array<int, string>  $urls
     * @return array{success: bool, status: int, message: string}
     */
    public function submitUrls(array $urls): array
    {
        if (empty($urls)) {
            return [
                'success' => true,
                'status' => 200,
                'message' => 'No URLs provided for submission.',
            ];
        }

        $firstUrl = $urls[0] ?? '';
        $firstUrlHost = parse_url($firstUrl, PHP_URL_HOST);

        $key = $this->getKey();
        $siteUrl = rtrim(config('site.url'), '/');
        $host = $firstUrlHost && $firstUrlHost !== 'localhost' && $firstUrlHost !== '127.0.0.1'
            ? $firstUrlHost
            : (parse_url($siteUrl, PHP_URL_HOST) ?: 'kenyaremotejobs.com');

        if ($host === 'localhost' || $host === '127.0.0.1') {
            $host = 'kenyaremotejobs.com';
        }

        $keyLocation = "https://{$host}/{$key}.txt";

        $payload = [
            'host' => $host,
            'key' => $key,
            'keyLocation' => $keyLocation,
            'urlList' => array_values(array_unique($urls)),
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json; charset=utf-8'])
                ->timeout(10)
                ->post(self::ENDPOINT, $payload);

            $status = $response->status();

            if (in_array($status, [200, 202], true)) {
                return [
                    'success' => true,
                    'status' => $status,
                    'message' => sprintf('Successfully submitted %d URL(s) to IndexNow.', count($payload['urlList'])),
                ];
            }

            Log::warning('IndexNow API error response', [
                'status' => $status,
                'body' => $response->body(),
                'urls_count' => count($urls),
            ]);

            return [
                'success' => false,
                'status' => $status,
                'message' => 'IndexNow API returned status '.$status.': '.$response->body(),
            ];
        } catch (Exception $e) {
            Log::error('IndexNow submission failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'status' => 500,
                'message' => 'Exception during IndexNow request: '.$e->getMessage(),
            ];
        }
    }
}
