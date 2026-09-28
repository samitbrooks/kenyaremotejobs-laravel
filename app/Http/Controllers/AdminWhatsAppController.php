<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWhatsAppController extends Controller
{
    public function index(Request $request, WhatsAppService $whatsAppService): View
    {
        $selectedPhone = $request->query('phone');

        // Fetch list of recent conversation threads grouped by phone number
        $conversations = WhatsAppMessage::select('phone_number')
            ->selectRaw('MAX(created_at) as last_activity')
            ->selectRaw('COUNT(*) as total_messages')
            ->groupBy('phone_number')
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($thread) {
                $latest = WhatsAppMessage::where('phone_number', $thread->phone_number)
                    ->latest()
                    ->first();
                $thread->latest_message = $latest?->content;
                $thread->latest_direction = $latest?->direction;

                return $thread;
            });

        // If no phone selected, default to the most recent conversation
        if (! $selectedPhone && $conversations->isNotEmpty()) {
            $selectedPhone = $conversations->first()->phone_number;
        }

        $activeMessages = collect();
        if ($selectedPhone) {
            $activeMessages = WhatsAppMessage::where('phone_number', $selectedPhone)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $recentJobs = JobListing::latest('posted_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_messages' => WhatsAppMessage::count(),
            'inbound' => WhatsAppMessage::where('direction', 'inbound')->count(),
            'outbound' => WhatsAppMessage::where('direction', 'outbound')->count(),
            'active_threads' => $conversations->count(),
        ];

        return view('admin.whatsapp', [
            'isConfigured' => $whatsAppService->isConfigured(),
            'phoneNumberId' => config('whatsapp.phone_number_id'),
            'conversations' => $conversations,
            'selectedPhone' => $selectedPhone,
            'activeMessages' => $activeMessages,
            'recentJobs' => $recentJobs,
            'stats' => $stats,
        ]);
    }

    public function send(Request $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $phone = $validated['phone_number'];
        $message = trim($validated['message']);

        $res = $whatsAppService->sendMessage($phone, $message);

        if ($res['success']) {
            return redirect()->route('admin.whatsapp', ['phone' => $whatsAppService->normalizePhoneNumber($phone)])
                ->with('success', '✓ WhatsApp message dispatched successfully!');
        }

        return redirect()->route('admin.whatsapp', ['phone' => $whatsAppService->normalizePhoneNumber($phone)])
            ->with('error', 'Message dispatch failed: '.($res['error'] ?? 'Unknown error. Check Meta Cloud API credentials.'));
    }

    public function sendJobAlert(Request $request, WhatsAppService $whatsAppService): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string'],
            'job_id' => ['required', 'exists:job_listings,id'],
        ]);

        $job = JobListing::findOrFail($validated['job_id']);
        $phone = $validated['phone_number'];

        $res = $whatsAppService->sendJobAlert($phone, $job);

        if ($res['success']) {
            return redirect()->route('admin.whatsapp', ['phone' => $whatsAppService->normalizePhoneNumber($phone)])
                ->with('success', "✓ Job alert for '{$job->title}' sent to {$phone}!");
        }

        return redirect()->route('admin.whatsapp', ['phone' => $whatsAppService->normalizePhoneNumber($phone)])
            ->with('error', 'Job alert dispatch failed: '.($res['error'] ?? 'Check Meta API credentials.'));
    }
}
