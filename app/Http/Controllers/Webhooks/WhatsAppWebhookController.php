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
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedToken = config('whatsapp.webhook_verify_token', 'krj-wa-verify-token');

        if ($mode === 'subscribe' && $token === $expectedToken) {
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
        $payload = $request->all();

        $whatsAppService->handleWebhook($payload);

        return response('EVENT_RECEIVED', 200);
    }
}
