<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
{
    private const INDEXING_ENDPOINT = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

    private const SCOPE = 'https://www.googleapis.com/auth/indexing';

    /**
     * @return array{client_email: string, private_key: string}|null
     */
    public function getCredentials(): ?array
    {
        $raw = config('services.google_indexing.credentials_json');

        if (! empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['client_email'], $decoded['private_key'])) {
                return $decoded;
            }
        }

        $path = config('services.google_indexing.key_path');
        if ($path && file_exists($path)) {
            $contents = file_get_contents($path);
            $decoded = json_decode($contents, true);
            if (is_array($decoded) && isset($decoded['client_email'], $decoded['private_key'])) {
                return $decoded;
            }
        }

        return null;
    }

    public function isConfigured(): bool
    {
        return $this->getCredentials() !== null;
    }

    /**
     * Publish a single URL update or deletion notification to Google Indexing API.
     *
     * @return array{success: bool, status: int, message: string}
     */
    public function publishUrl(string $url, string $type = 'URL_UPDATED'): array
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return [
                'success' => false,
                'status' => 401,
                'message' => 'Google Service Account credentials not configured or failed to generate token.',
            ];
        }

        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::INDEXING_ENDPOINT, [
                    'url' => $url,
                    'type' => $type,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Successfully submitted to Google Indexing API.',
                ];
            }

            Log::warning('Google Indexing API error response', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'Google Indexing API returned error: '.$response->body(),
            ];
        } catch (Exception $e) {
            Log::error('Google Indexing API request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 500,
                'message' => 'Exception during request: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Obtain a valid OAuth 2.0 access token using the Service Account JWT bearer flow.
     */
    public function getAccessToken(): ?string
    {
        $cached = Cache::get('google_indexing_access_token');
        if ($cached) {
            return $cached;
        }

        $credentials = $this->getCredentials();
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
                Log::error('Invalid Google Service Account private key.');

                return null;
            }

            if (! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                Log::error('Failed to sign Google JWT assertion with private key.');

                return null;
            }

            $jwtAssertion = "{$unsignedToken}.".$this->base64UrlEncode($signature);

            $tokenResponse = Http::asForm()->post(self::TOKEN_ENDPOINT, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtAssertion,
            ]);

            if (! $tokenResponse->successful()) {
                Log::error('Google OAuth token request failed', ['body' => $tokenResponse->body()]);

                return null;
            }

            $data = $tokenResponse->json();
            $token = $data['access_token'] ?? null;
            $expiresIn = (int) ($data['expires_in'] ?? 3600);

            if ($token) {
                Cache::put('google_indexing_access_token', $token, max(60, $expiresIn - 300));

                return $token;
            }

            return null;
        } catch (Exception $e) {
            Log::error('Exception generating Google Indexing access token', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
