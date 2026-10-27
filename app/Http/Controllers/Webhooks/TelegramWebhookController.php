<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    /**
     * Handles inbound webhook updates from Telegram Bot API.
     */
    public function handle(Request $request, TelegramService $telegramService): JsonResponse
    {
        $expectedSecret = (string) config('telegram.webhook_secret');

        if ($expectedSecret === '') {
            return response()->json([
                'ok' => false,
                'error' => 'Webhook secret not configured',
            ], 403);
        }

        $receivedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');

        if ($receivedSecret === '' || ! hash_equals($expectedSecret, $receivedSecret)) {
            return response()->json([
                'ok' => false,
                'error' => 'Invalid webhook secret token',
            ], 403);
        }

        $payload = $request->all();

        $telegramService->handleWebhook($payload);

        return response()->json(['ok' => true]);
    }

    /**
     * Returns diagnostic status of Telegram configuration and webhook.
     */
    public function status(Request $request, TelegramService $telegramService): JsonResponse
    {
        $expectedSecret = (string) config('telegram.webhook_secret');
        $receivedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');
        $hasValidSecret = $expectedSecret !== '' && $receivedSecret !== '' && hash_equals($expectedSecret, $receivedSecret);
        $isAdmin = $request->user()?->isAdmin() && $request->session()->get('admin_authenticated', false);

        if (! $hasValidSecret && ! $isAdmin) {
            return response()->json([
                'ok' => false,
                'error' => 'Unauthorized access to Telegram diagnostics',
            ], 403);
        }

        return response()->json([
            'is_configured' => $telegramService->isConfigured(),
            'bot_username' => config('telegram.bot_username'),
            'channel_id' => config('telegram.channel_id'),
            'group_id' => config('telegram.group_id'),
            'webhook_info' => $telegramService->isConfigured() ? $telegramService->getWebhookInfo() : null,
        ]);
    }
}
