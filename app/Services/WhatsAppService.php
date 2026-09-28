<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Support\SalaryEstimate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppService
{
    private ?string $accessToken;

    private ?string $phoneNumberId;

    private string $apiVersion;

    public function __construct()
    {
        $this->accessToken = config('whatsapp.access_token');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->apiVersion = config('whatsapp.api_version', 'v21.0');
    }

    /**
     * Checks whether the Meta WhatsApp Cloud API credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accessToken) && ! empty($this->phoneNumberId);
    }

    /**
     * Formats phone number into international E.164 numeric string (e.g. 254712345678).
     */
    public function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/[^\d]/', '', $phone);

        // If local Kenyan number starting with 0 (e.g. 0712345678), convert to 254712345678
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $digits = '254'.substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Sends an outbound WhatsApp text message via Meta Graph API.
     *
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendMessage(string $to, string $text, ?int $userId = null, string $type = 'text'): array
    {
        $normalizedPhone = $this->normalizePhoneNumber($to);

        if (! $this->isConfigured()) {
            // Log local simulated dispatch for development/testing when keys not yet provided
            $record = WhatsAppMessage::create([
                'user_id' => $userId,
                'phone_number' => $normalizedPhone,
                'direction' => 'outbound',
                'message_type' => $type,
                'content' => $text,
                'status' => 'sent',
                'whatsapp_message_id' => 'sim_'.uniqid(),
            ]);

            return [
                'success' => true,
                'message_id' => $record->whatsapp_message_id,
                'error' => null,
            ];
        }

        try {
            $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $response = Http::withToken($this->accessToken)
                ->timeout(15)
                ->post($endpoint, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $normalizedPhone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => true,
                        'body' => $text,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $waMsgId = $body['messages'][0]['id'] ?? null;

                WhatsAppMessage::create([
                    'user_id' => $userId,
                    'phone_number' => $normalizedPhone,
                    'direction' => 'outbound',
                    'message_type' => $type,
                    'content' => $text,
                    'status' => 'sent',
                    'whatsapp_message_id' => $waMsgId,
                    'raw_payload' => $body,
                ]);

                return [
                    'success' => true,
                    'message_id' => $waMsgId,
                    'error' => null,
                ];
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            Log::error('WhatsApp API Error: '.$errorMsg, ['to' => $normalizedPhone]);

            WhatsAppMessage::create([
                'user_id' => $userId,
                'phone_number' => $normalizedPhone,
                'direction' => 'outbound',
                'message_type' => $type,
                'content' => $text,
                'status' => 'failed',
                'raw_payload' => $response->json(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $errorMsg,
            ];
        } catch (Throwable $e) {
            Log::error('WhatsApp Dispatch Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sends a rich job alert message via WhatsApp.
     */
    public function sendJobAlert(string $to, JobListing $job, ?int $userId = null): array
    {
        $kes = SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
        $hourly = SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
        $compString = $kes ? "💰 Est. Salary: {$kes}" : ($hourly ? "💰 Est. Rate: ~${$hourly['min']}-{$hourly['max']}/hr" : '💰 Competitive International Pay');

        $message = "⚡ *New Remote Opportunity for Kenya!* \n\n"
            ."📌 *Role:* {$job->title}\n"
            ."🏢 *Company:* {$job->company}\n"
            ."🌍 *Location:* {$job->remote_type}\n"
            ."{$compString}\n"
            ."🇰🇪 *Timezone:* East Africa Time (UTC+3) Compatible\n\n"
            ."👉 *Apply directly on KenyaRemoteJobs:* \n".url('/jobs/'.$job->id)."\n\n"
            .'_KenyaRemoteJobs VIP Instant Alerts_';

        return $this->sendMessage($to, $message, $userId, 'job_alert');
    }

    /**
     * Sends a VIP Pro Welcome message with direct concierge access.
     */
    public function sendProWelcome(string $to, User $user): array
    {
        $name = $user->firstName();

        $message = "👑 *Welcome to KenyaRemoteJobs Pro, {$name}!* \n\n"
            ."Your membership is now active with 100% unlocked access to all remote vacancies worldwide.\n\n"
            ."✨ *What Our Team Does For You:*\n"
            ."1. Free 1-on-1 CV Optimization for ATS filters\n"
            ."2. Custom Niche Role Scouting\n"
            ."3. Priority Direct Recruiter Introductions\n"
            ."4. Live Placement & Interview Support\n\n"
            ."You can reply directly to this WhatsApp chat anytime with your CV or career questions & our concierge team will assist you!\n\n"
            ."👉 *Explore Unlocked Roles:* \n".url('/jobs');

        return $this->sendMessage($to, $message, $user->id, 'pro_welcome');
    }

    /**
     * Ingests and processes incoming webhooks from Meta Cloud API.
     */
    public function handleWebhook(array $payload): void
    {
        try {
            $entries = $payload['entry'] ?? [];

            foreach ($entries as $entry) {
                $changes = $entry['changes'] ?? [];
                foreach ($changes as $change) {
                    $value = $change['value'] ?? [];

                    // 1. Process inbound incoming messages from candidates/clients
                    if (! empty($value['messages'])) {
                        foreach ($value['messages'] as $msg) {
                            $from = $msg['from'] ?? '';
                            $waMsgId = $msg['id'] ?? null;
                            $type = $msg['type'] ?? 'text';
                            $text = '';

                            if ($type === 'text') {
                                $text = $msg['text']['body'] ?? '';
                            } elseif ($type === 'button') {
                                $text = $msg['button']['text'] ?? '';
                            } else {
                                $text = "[Media / {$type}]";
                            }

                            // Match existing user if phone registered
                            $user = User::where('phone', $from)
                                ->orWhere('phone', '+'.$from)
                                ->first();

                            WhatsAppMessage::create([
                                'user_id' => $user?->id,
                                'phone_number' => $from,
                                'direction' => 'inbound',
                                'message_type' => $type,
                                'content' => $text,
                                'status' => 'received',
                                'whatsapp_message_id' => $waMsgId,
                                'raw_payload' => $msg,
                            ]);
                        }
                    }

                    // 2. Process message delivery & read status callbacks
                    if (! empty($value['statuses'])) {
                        foreach ($value['statuses'] as $statusUpdate) {
                            $waId = $statusUpdate['id'] ?? null;
                            $status = $statusUpdate['status'] ?? null;

                            if ($waId && $status) {
                                WhatsAppMessage::where('whatsapp_message_id', $waId)
                                    ->update(['status' => $status]);
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('WhatsApp Webhook Processing Failed: '.$e->getMessage(), ['payload' => $payload]);
        }
    }
}
