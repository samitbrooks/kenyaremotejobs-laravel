<?php

namespace App\Http\Controllers;

use App\Services\KenyaCareerBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerBotController extends Controller
{
    public function ask(Request $request, KenyaCareerBotService $bot): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $result = $bot->ask($validated['message'], $validated['history'] ?? []);

        return response()->json([
            'reply' => $result['reply'],
            'suggested_actions' => $result['suggested_actions'] ?? [],
        ]);
    }
}
