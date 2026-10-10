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

        if ($expectedSecret !== '') {
            $receivedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');

            if (! hash_equals($expectedSecret, $receivedSecret)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Invalid webhook secret token',
                ], 403);
            }
        }

        $payload = $request->all();

        $telegramService->handleWebhook($payload);

        return response()->json(['ok' => true]);
    }

    /**
     * Returns diagnostic status of Telegram configuration and webhook.
     */
    public function status(TelegramService $telegramService): JsonResponse
    {
        return response()->json([
            'is_configured' => $telegramService->isConfigured(),
            'bot_username' => config('telegram.bot_username'),
            'channel_id' => config('telegram.channel_id'),
            'group_id' => config('telegram.group_id'),
            'webhook_info' => $telegramService->isConfigured() ? $telegramService->getWebhookInfo() : null,
        ]);
    }
}
