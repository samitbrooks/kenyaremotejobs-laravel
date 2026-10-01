<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handles Meta webhook verification handshake (GET).
     */
    public function verify(Request $request): Response
    {
        $mode = (string) ($request->query('hub_mode') ?? $request->query('hub.mode'));
        $token = (string) ($request->query('hub_verify_token') ?? $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedToken = (string) config('whatsapp.webhook_verify_token', 'krj-wa-verify-token');

        if ($mode === 'subscribe' && $token !== '' && hash_equals($expectedToken, $token)) {
            return response((string) $challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Ingests incoming WhatsApp messages and delivery statuses from Meta Cloud API (POST).
     */
    public function receive(Request $request, WhatsAppService $whatsAppService): Response
    {
        $appSecret = (string) config('whatsapp.app_secret');

        if ($appSecret !== '') {
            $signature = $request->header('X-Hub-Signature-256');

            if (! $signature || ! str_starts_with($signature, 'sha256=')) {
                return response('Missing or invalid webhook signature', 401);
            }

            $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $appSecret);

            if (! hash_equals($expected, $signature)) {
                return response('Signature verification failed', 401);
            }
        }

        $payload = $request->all();

        $whatsAppService->handleWebhook($payload);

        return response('EVENT_RECEIVED', 200);
    }
}
