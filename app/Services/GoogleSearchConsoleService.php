<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
{
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

    private const SCOPE = 'https://www.googleapis.com/auth/webmasters';

    private const API_BASE = 'https://searchconsole.googleapis.com/webmasters/v3';

    public function __construct(
        protected GoogleIndexingService $indexingService
    ) {}

    /**
     * Get the configured site URL property identifier for Search Console.
     * Default format: sc-domain:kenyaremotejobs.com or https://kenyaremotejobs.com/
     */
    public function getSiteUrl(): string
    {
        return config('services.google_search_console.site_url')
            ?: env('GOOGLE_SEARCH_CONSOLE_SITE_URL', 'sc-domain:kenyaremotejobs.com');
    }

    /**
     * Determine if Google Search Console credentials are configured.
     */
    public function isConfigured(): bool
    {
        return $this->indexingService->isConfigured();
    }

    /**
     * List verified sites accessible to this service account.
     *
     * @return array{success: bool, sites: array, message: ?string}
     */
    public function listSites(): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'sites' => [], 'message' => 'Failed to obtain Google OAuth access token.'];
        }

        try {
            $response = Http::withToken($token)->get(self::API_BASE.'/sites');

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'sites' => $data['siteEntry'] ?? [],
                    'message' => null,
                ];
            }

            return [
                'success' => false,
                'sites' => [],
                'message' => $response->json('error.message') ?? $response->body(),
            ];
        } catch (Exception $e) {
            Log::error('Search Console listSites failed: '.$e->getMessage());

            return ['success' => false, 'sites' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * List all submitted sitemaps for the configured or specified site property.
     *
     * @return array{success: bool, sitemaps: array, message: ?string}
     */
    public function listSitemaps(?string $siteUrl = null): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'sitemaps' => [], 'message' => 'Failed to obtain Google OAuth access token.'];
        }

        $site = urlencode($siteUrl ?: $this->getSiteUrl());

        try {
            $response = Http::withToken($token)->get(self::API_BASE."/sites/{$site}/sitemaps");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'sitemaps' => $data['sitemap'] ?? [],
                    'message' => null,
                ];
            }

            return [
                'success' => false,
                'sitemaps' => [],
                'message' => $response->json('error.message') ?? $response->body(),
            ];
        } catch (Exception $e) {
            Log::error('Search Console listSitemaps failed: '.$e->getMessage());

            return ['success' => false, 'sitemaps' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit a sitemap to Google Search Console via Webmasters API v3.
     *
     * @return array{success: bool, message: ?string}
     */
    public function submitSitemap(string $feedpath, ?string $siteUrl = null): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Failed to obtain Google OAuth access token.'];
        }

        $site = urlencode($siteUrl ?: $this->getSiteUrl());
        $encodedFeedpath = urlencode($feedpath);

        try {
            $response = Http::withToken($token)->put(self::API_BASE."/sites/{$site}/sitemaps/{$encodedFeedpath}");

            if ($response->successful() || $response->status() === 204) {
                return [
                    'success' => true,
                    'message' => 'Sitemap successfully submitted to Google Search Console.',
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('error.message') ?? $response->body(),
            ];
        } catch (Exception $e) {
            Log::error('Search Console submitSitemap failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Query Search Analytics metrics (Clicks, Impressions, CTR, Position).
     *
     * @param  array<int, string>  $dimensions  e.g. ['query'], ['page'], ['query', 'page']
     * @return array{
     *     success: bool,
     *     rows: array,
     *     totals: array{clicks: int, impressions: int, ctr: float, position: float},
     *     message: ?string
     * }
     */
    public function queryAnalytics(
        ?string $siteUrl = null,
        ?string $startDate = null,
        ?string $endDate = null,
        array $dimensions = ['query'],
        int $rowLimit = 25
    ): array {
        $token = $this->getAccessToken();
        if (! $token) {
            return [
                'success' => false,
                'rows' => [],
                'totals' => ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'position' => 0.0],
                'message' => 'Google Service Account token unavailable.',
            ];
        }

        $site = urlencode($siteUrl ?: $this->getSiteUrl());
        $start = $startDate ?: now()->subDays(28)->format('Y-m-d');
        $end = $endDate ?: now()->subDays(2)->format('Y-m-d'); // GSC data has a 2-day reporting lag

        $cacheKey = "gsc_analytics_{$site}_{$start}_{$end}_".implode('_', $dimensions)."_{$rowLimit}";

        return Cache::remember($cacheKey, 3600, function () use ($token, $site, $start, $end, $dimensions, $rowLimit) {
            try {
                $endpoint = self::API_BASE."/sites/{$site}/searchAnalytics/query";
                $response = Http::withToken($token)->post($endpoint, [
                    'startDate' => $start,
                    'endDate' => $end,
                    'dimensions' => $dimensions,
                    'rowLimit' => $rowLimit,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $rows = $data['rows'] ?? [];

                    $totalClicks = 0;
                    $totalImpressions = 0;
                    $weightedPositionSum = 0;

                    foreach ($rows as $row) {
                        $c = (int) ($row['clicks'] ?? 0);
                        $i = (int) ($row['impressions'] ?? 0);
                        $p = (float) ($row['position'] ?? 0);

                        $totalClicks += $c;
                        $totalImpressions += $i;
                        $weightedPositionSum += ($p * $i);
                    }

                    $avgCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.0;
                    $avgPos = $totalImpressions > 0 ? round($weightedPositionSum / $totalImpressions, 1) : 0.0;

                    return [
                        'success' => true,
                        'rows' => $rows,
                        'totals' => [
                            'clicks' => $totalClicks,
                            'impressions' => $totalImpressions,
                            'ctr' => $avgCtr,
                            'position' => $avgPos,
                        ],
                        'message' => null,
                    ];
                }

                $err = $response->json('error.message') ?? $response->body();
                Log::warning('Search Console queryAnalytics error', ['error' => $err]);

                return [
                    'success' => false,
                    'rows' => [],
                    'totals' => ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'position' => 0.0],
                    'message' => $err,
                ];
            } catch (Exception $e) {
                Log::error('Search Console queryAnalytics exception: '.$e->getMessage());

                return [
                    'success' => false,
                    'rows' => [],
                    'totals' => ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'position' => 0.0],
                    'message' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Inspect a URL's Google index status via Google Search Console URL Inspection API.
     *
     * @return array{success: bool, status: ?string, details: array, message: ?string}
     */
    public function inspectUrl(string $inspectionUrl, ?string $siteUrl = null): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'status' => null, 'details' => [], 'message' => 'No access token available.'];
        }

        $site = $siteUrl ?: $this->getSiteUrl();

        try {
            $endpoint = 'https://searchconsole.googleapis.com/v1/urlInspection/index:inspect';
            $response = Http::withToken($token)->post($endpoint, [
                'inspectionUrl' => $inspectionUrl,
                'siteUrl' => $site,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $verdict = $data['inspectionResult']['indexStatusResult']['verdict'] ?? 'UNKNOWN';

                return [
                    'success' => true,
                    'status' => $verdict,
                    'details' => $data['inspectionResult'] ?? [],
                    'message' => null,
                ];
            }

            return [
                'success' => false,
                'status' => null,
                'details' => [],
                'message' => $response->json('error.message') ?? $response->body(),
            ];
        } catch (Exception $e) {
            return ['success' => false, 'status' => null, 'details' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch valid OAuth2 access token with Search Console readonly scope.
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'google_search_console_access_token';
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $credentials = $this->indexingService->getCredentials();
        if (! $credentials) {
            return null;
        }

        try {
            $now = time();
            $jwtHeader = $this->base64UrlEncode((string) json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]));

            $jwtClaim = $this->base64UrlEncode((string) json_encode([
                'iss' => $credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_ENDPOINT,
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $unsignedToken = "{$jwtHeader}.{$jwtClaim}";
            $signature = '';

            $privateKey = openssl_pkey_get_private($credentials['private_key']);
            if (! $privateKey) {
                return null;
            }

            if (! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                return null;
            }

            $jwtAssertion = "{$unsignedToken}.".$this->base64UrlEncode($signature);

            $response = Http::asForm()->post(self::TOKEN_ENDPOINT, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtAssertion,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = (int) ($data['expires_in'] ?? 3600);

                if ($token) {
                    Cache::put($cacheKey, $token, max(60, $expiresIn - 300));

                    return $token;
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error('GSC access token generation exception: '.$e->getMessage());

            return null;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
