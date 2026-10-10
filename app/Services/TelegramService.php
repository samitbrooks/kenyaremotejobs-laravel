<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\TelegramMessage;
use App\Support\SalaryEstimate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    private ?string $botToken;

    private string $botUsername;

    private ?string $defaultChannelId;

    private ?string $defaultGroupId;

    private ?string $adminUserId;

    public function __construct(
        private readonly KenyaCareerBotService $careerBotService
    ) {
        $this->botToken = config('telegram.bot_token');
        $this->botUsername = (string) config('telegram.bot_username', 'KenyaRemoteJobsBot');
        $this->defaultChannelId = config('telegram.channel_id');
        $this->defaultGroupId = config('telegram.group_id');
        $this->adminUserId = config('telegram.admin_user_id') ? (string) config('telegram.admin_user_id') : null;
    }

    /**
     * Checks whether the Telegram Bot Token is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->botToken);
    }

    /**
     * Calls Telegram Bot API endpoint.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function callApi(string $method, array $params = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'ok' => false,
                'description' => 'TELEGRAM_BOT_TOKEN is not configured',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->post("https://api.telegram.org/bot{$this->botToken}/{$method}", $params);

            return $response->json() ?? [
                'ok' => false,
                'description' => 'Empty response from Telegram API',
            ];
        } catch (Throwable $e) {
            Log::error("[telegram] API Exception ({$method}): ".$e->getMessage());

            return [
                'ok' => false,
                'description' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieves information about the current bot user.
     *
     * @return array<string, mixed>
     */
    public function getMe(): array
    {
        return $this->callApi('getMe');
    }

    /**
     * Configures the webhook URL with Telegram Bot API.
     *
     * @return array<string, mixed>
     */
    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        $params = [
            'url' => $url,
            'allowed_updates' => json_encode(['message', 'channel_post']),
        ];

        if ($secretToken) {
            $params['secret_token'] = $secretToken;
        }

        return $this->callApi('setWebhook', $params);
    }

    /**
     * Gets current webhook diagnostic information.
     *
     * @return array<string, mixed>
     */
    public function getWebhookInfo(): array
    {
        return $this->callApi('getWebhookInfo');
    }

    /**
     * Removes the active webhook.
     *
     * @return array<string, mixed>
     */
    public function deleteWebhook(): array
    {
        return $this->callApi('deleteWebhook');
    }

    /**
     * Sends a typing or upload action to indicate activity.
     */
    public function sendChatAction(string|int $chatId, string $action = 'typing'): bool
    {
        $res = $this->callApi('sendChatAction', [
            'chat_id' => (string) $chatId,
            'action' => $action,
        ]);

        return (bool) ($res['ok'] ?? false);
    }

    /**
     * Sets official bot commands visible in Telegram menu.
     *
     * @return array<string, mixed>
     */
    public function setMyCommands(): array
    {
        return $this->callApi('setMyCommands', [
            'commands' => [
                ['command' => 'jobs', 'description' => 'View latest verified remote jobs for Kenyans'],
                ['command' => 'ask', 'description' => 'Ask Google AI career bot about remote work & payments'],
                ['command' => 'help', 'description' => 'Community guide & official website links'],
                ['command' => 'start', 'description' => 'Restart or view bot menu'],
            ],
        ]);
    }

    /**
     * Sets official bot description and short description.
     *
     * @return array<string, mixed>
     */
    public function setBotProfile(): array
    {
        $this->setMyCommands();

        $this->callApi('setMyDescription', [
            'description' => "Official Kenya Remote Jobs Bot 🇰🇪🚀\n\nDaily verified global remote jobs paying in USD, GBP, and EUR for East African professionals. Powered by Daisy AI to answer your career, Wise/M-Pesa, and CV questions 24/7.",
        ]);

        return $this->callApi('setMyShortDescription', [
            'short_description' => 'Verified remote jobs for Kenyans daily & 24/7 Google AI career assistant.',
        ]);
    }

    /**
     * Sends a message to a Telegram chat, group, or channel.
     *
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendMessage(
        string|int $chatId,
        string $text,
        string $parseMode = 'HTML',
        ?int $replyToMessageId = null,
        string $messageType = 'text'
    ): array {
        $chatIdStr = (string) $chatId;

        if (! $this->isConfigured()) {
            // Simulated record for development / testing when tokens are not yet set
            $record = TelegramMessage::create([
                'chat_id' => $chatIdStr,
                'chat_type' => 'unknown',
                'direction' => 'outbound',
                'message_type' => $messageType,
                'content' => $text,
                'status' => 'sent',
                'telegram_message_id' => 'sim_'.uniqid(),
            ]);

            return [
                'success' => true,
                'message_id' => $record->telegram_message_id,
                'error' => null,
            ];
        }

        $params = [
            'chat_id' => $chatIdStr,
            'text' => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => false,
        ];

        if ($replyToMessageId !== null) {
            $params['reply_to_message_id'] = $replyToMessageId;
        }

        $res = $this->callApi('sendMessage', $params);

        if ($res['ok'] ?? false) {
            $tgMsgId = (string) ($res['result']['message_id'] ?? null);

            TelegramMessage::create([
                'chat_id' => $chatIdStr,
                'chat_type' => (string) ($res['result']['chat']['type'] ?? 'unknown'),
                'direction' => 'outbound',
                'message_type' => $messageType,
                'content' => $text,
                'status' => 'sent',
                'telegram_message_id' => $tgMsgId,
                'raw_payload' => $res,
            ]);

            return [
                'success' => true,
                'message_id' => $tgMsgId,
                'error' => null,
            ];
        }

        $errorMsg = (string) ($res['description'] ?? 'Failed to send Telegram message');
        Log::error("[telegram] Message failed to {$chatIdStr}: {$errorMsg}");

        TelegramMessage::create([
            'chat_id' => $chatIdStr,
            'chat_type' => 'unknown',
            'direction' => 'outbound',
            'message_type' => $messageType,
            'content' => $text,
            'status' => 'failed',
            'raw_payload' => $res,
        ]);

        return [
            'success' => false,
            'message_id' => null,
            'error' => $errorMsg,
        ];
    }

    /**
     * Broadcasts the latest verified remote jobs to the configured channel/group.
     *
     * @return array{success: bool, count: int, error: ?string}
     */
    public function broadcastDailyJobs(?string $targetChatId = null, int $limit = 5): array
    {
        $target = $targetChatId ?: ($this->defaultChannelId ?: $this->defaultGroupId);

        if (! $target) {
            return [
                'success' => false,
                'count' => 0,
                'error' => 'No target Telegram channel or group configured (TELEGRAM_CHANNEL_ID or TELEGRAM_GROUP_ID missing)',
            ];
        }

        /** @var Collection<int, JobListing> $jobs */
        $jobs = JobListing::visible()
            ->where('kenya_friendly', true)
            ->orderByDesc('posted_at')
            ->take($limit)
            ->get();

        if ($jobs->isEmpty()) {
            return [
                'success' => false,
                'count' => 0,
                'error' => 'No recent active jobs available to broadcast',
            ];
        }

        $formattedText = $this->formatJobDigest($jobs);

        $result = $this->sendMessage(
            chatId: $target,
            text: $formattedText,
            parseMode: 'HTML',
            messageType: 'broadcast'
        );

        return [
            'success' => $result['success'],
            'count' => $jobs->count(),
            'error' => $result['error'],
        ];
    }

    /**
     * Formats multiple jobs into a high-engagement Telegram post.
     *
     * @param  Collection<int, JobListing>  $jobs
     */
    public function formatJobDigest(Collection $jobs): string
    {
        $today = now()->format('D, M j, Y');

        $html = "🇰🇪 <b>TODAY'S VERIFIED REMOTE JOBS FOR KENYANS</b>\n";
        $html .= "📅 <i>{$today} | Verified Global Roles</i>\n\n";

        foreach ($jobs as $index => $job) {
            $num = $index + 1;
            $kes = SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
            $hourly = SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
            $payInfo = $kes ? "💰 <b>Est. Pay:</b> {$kes}" : ($hourly ? "💰 <b>Est. Rate:</b> ~${$hourly['min']}-${$hourly['max']}/hr" : '💰 <b>Pay:</b> Competitive USD/GBP/EUR');

            $titleSafe = htmlspecialchars($job->title, ENT_QUOTES | ENT_SUBSTITUTE);
            $companySafe = htmlspecialchars($job->company, ENT_QUOTES | ENT_SUBSTITUTE);
            $remoteType = htmlspecialchars($job->remote_type ?? 'Worldwide Remote', ENT_QUOTES | ENT_SUBSTITUTE);
            $jobUrl = url('/jobs/'.$job->id);

            $html .= "<b>{$num}. {$titleSafe}</b>\n";
            $html .= "🏢 <b>Company:</b> {$companySafe}\n";
            $html .= "🌍 <b>Location:</b> {$remoteType} (EAT UTC+3 Friendly)\n";
            $html .= "{$payInfo}\n";
            $html .= "👉 <a href=\"{$jobUrl}\">View & Apply Directly</a>\n\n";
        }

        $html .= "⚡ <i>Want 48-Hour Early Access to apply before listings get saturated?</i>\n";
        $html .= '👉 Join Pro: <a href="'.url('/pricing')."\">KenyaRemoteJobs.com/pricing</a>\n\n";
        $html .= '💬 <i>Have career or payment questions? Type <code>/ask &lt;your question&gt;</code> right here in the chat!</i>';

        return $html;
    }

    /**
     * Ingests and processes incoming webhook payload from Telegram.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload): void
    {
        try {
            $message = $payload['message'] ?? $payload['channel_post'] ?? null;

            if (! is_array($message)) {
                return;
            }

            $chat = $message['chat'] ?? [];
            $chatId = (string) ($chat['id'] ?? '');
            $chatType = (string) ($chat['type'] ?? 'group');
            $from = $message['from'] ?? [];
            $fromUserId = isset($from['id']) ? (string) $from['id'] : null;
            $fromUsername = $from['username'] ?? null;
            $fromFirstName = htmlspecialchars($from['first_name'] ?? 'Friend', ENT_QUOTES | ENT_SUBSTITUTE);
            $messageId = isset($message['message_id']) ? (int) $message['message_id'] : null;
            $text = trim((string) ($message['text'] ?? ''));

            // 1. Check for New Chat Members (Welcome Automation)
            if (! empty($message['new_chat_members']) && is_array($message['new_chat_members'])) {
                foreach ($message['new_chat_members'] as $newMember) {
                    if (! empty($newMember['is_bot'])) {
                        continue;
                    }

                    $name = htmlspecialchars($newMember['first_name'] ?? 'there', ENT_QUOTES | ENT_SUBSTITUTE);
                    $welcomeText = "👋 <b>Karibu sana, {$name}!</b> Welcome to the Kenya Remote Jobs Community 🇰🇪✨\n\n"
                        ."Here you get daily verified remote opportunities open to East African professionals, paying in USD, GBP, and EUR.\n\n"
                        ."📌 <b>Quick Tips to Get Started:</b>\n"
                        ."• Latest jobs are updated daily right here in this channel.\n"
                        ."• Use <code>/jobs</code> to see the current top roles.\n"
                        ."• Ask any career, CV, or payment question anytime with <code>/ask &lt;question&gt;</code>.\n"
                        .'• Need tailored CVs & 48-hour early access? Check <a href="'.url('/pricing').'">KenyaRemoteJobs.com</a>';

                    $this->sendMessage(
                        chatId: $chatId,
                        text: $welcomeText,
                        parseMode: 'HTML',
                        replyToMessageId: $messageId,
                        messageType: 'welcome'
                    );
                }

                return;
            }

            if ($text === '') {
                return;
            }

            // Log inbound message
            TelegramMessage::create([
                'chat_id' => $chatId,
                'chat_type' => $chatType,
                'from_user_id' => $fromUserId,
                'from_username' => $fromUsername,
                'direction' => 'inbound',
                'message_type' => 'text',
                'content' => $text,
                'status' => 'received',
                'telegram_message_id' => $messageId ? (string) $messageId : null,
                'raw_payload' => $message,
            ]);

            // 2. Command Routing
            $command = mb_strtolower(explode(' ', $text)[0]);
            $botHandle = '@'.mb_strtolower($this->botUsername);

            // Strip @botusername suffix from command if present (e.g. /start@KenyaRemoteJobsBot)
            if (str_contains($command, '@')) {
                $command = explode('@', $command)[0];
            }

            if ($command === '/start') {
                $reply = "🇰🇪 <b>Welcome to Kenya Remote Jobs Assistant!</b>\n\n"
                    ."I am Daisy AI, your 24/7 remote career bot. Here is what I can do for you:\n\n"
                    ."🔍 <code>/jobs</code> — View latest verified remote jobs for Kenyans\n"
                    ."🤖 <code>/ask &lt;question&gt;</code> — Ask Google AI any career, CV, Wise, M-Pesa, or tax question\n"
                    ."ℹ️ <code>/help</code> — Community guidelines & helpful links\n\n"
                    .'Ready to level up? Visit <a href="'.url('/jobs').'">KenyaRemoteJobs.com</a> to browse 800+ roles.';

                $this->sendMessage($chatId, $reply, 'HTML', $messageId, 'command');

                return;
            }

            if ($command === '/help') {
                $reply = "📋 <b>Kenya Remote Jobs Bot Commands:</b>\n\n"
                    ."• <code>/jobs</code> — Top 3 latest verified remote positions\n"
                    ."• <code>/ask &lt;your question&gt;</code> — Instant answers on remote working, payments, W-8BEN, CVs, etc.\n"
                    ."• <code>/start</code> — Bot welcome menu\n\n"
                    ."👑 <b>Official Links:</b>\n"
                    .'• Browse All Jobs: <a href="'.url('/jobs')."\">KenyaRemoteJobs.com/jobs</a>\n"
                    .'• Upgrade to Pro: <a href="'.url('/pricing')."\">KenyaRemoteJobs.com/pricing</a>\n"
                    .'• AI CV Tailor: <a href="'.url('/resume-builder').'">KenyaRemoteJobs.com/resume-builder</a>';

                $this->sendMessage($chatId, $reply, 'HTML', $messageId, 'command');

                return;
            }

            if ($command === '/jobs') {
                /** @var Collection<int, JobListing> $jobs */
                $jobs = JobListing::visible()
                    ->where('kenya_friendly', true)
                    ->orderByDesc('posted_at')
                    ->take(3)
                    ->get();

                if ($jobs->isEmpty()) {
                    $this->sendMessage($chatId, 'No active jobs found right now. Check back shortly!', 'HTML', $messageId);

                    return;
                }

                $reply = $this->formatJobDigest($jobs);
                $this->sendMessage($chatId, $reply, 'HTML', $messageId, 'command');

                return;
            }

            // 3. Admin Broadcast Command (/broadcast <text>)
            if ($command === '/broadcast') {
                if (! $this->isAdmin($fromUserId)) {
                    $this->sendMessage($chatId, '⛔ Only the designated administrator can broadcast messages.', 'HTML', $messageId);

                    return;
                }

                $broadcastText = trim(mb_substr($text, 10));

                if ($broadcastText === '') {
                    $this->sendMessage($chatId, '⚠️ Please provide text to broadcast. Usage: <code>/broadcast Your announcement here</code>', 'HTML', $messageId);

                    return;
                }

                $target = $this->defaultChannelId ?: $this->defaultGroupId;

                if (! $target) {
                    $this->sendMessage($chatId, '⚠️ No target channel or group ID configured in TELEGRAM_CHANNEL_ID.', 'HTML', $messageId);

                    return;
                }

                $formattedBroadcast = "📢 <b>COMMUNITY ANNOUNCEMENT</b>\n\n".htmlspecialchars($broadcastText, ENT_QUOTES | ENT_SUBSTITUTE);
                $result = $this->sendMessage($target, $formattedBroadcast, 'HTML', null, 'broadcast');

                if ($result['success']) {
                    $this->sendMessage($chatId, "✅ Broadcast sent successfully to {$target}!", 'HTML', $messageId);
                } else {
                    $this->sendMessage($chatId, "❌ Failed to broadcast: {$result['error']}", 'HTML', $messageId);
                }

                return;
            }

            // 4. Google AI Career Bot Assistant (/ask <question> or direct message or mention)
            $isDirectChat = $chatType === 'private';
            $isMentioned = str_contains(mb_strtolower($text), $botHandle);
            $isAskCommand = $command === '/ask';

            if ($isAskCommand || $isDirectChat || $isMentioned) {
                $query = $text;

                if ($isAskCommand) {
                    $query = trim(mb_substr($text, 4));
                } elseif ($isMentioned) {
                    $query = trim(str_ireplace($botHandle, '', $text));
                }

                if ($query === '') {
                    $this->sendMessage(
                        chatId: $chatId,
                        text: "💡 What question would you like to ask? For example:\n<code>/ask How do I receive money from US clients via Wise or M-Pesa?</code>",
                        parseMode: 'HTML',
                        replyToMessageId: $messageId
                    );

                    return;
                }

                // Show typing indicator
                $this->sendChatAction($chatId, 'typing');

                // Call Google AI Career Bot Service
                $aiResult = $this->careerBotService->ask($query);
                $replyText = $aiResult['reply'] ?? '';

                if ($replyText !== '') {
                    $formattedReply = $this->markdownToTelegramHtml($replyText);

                    // Append suggested actions if available
                    if (! empty($aiResult['suggested_actions'])) {
                        $formattedReply .= "\n\n🔗 <b>Helpful Links:</b>";
                        foreach ($aiResult['suggested_actions'] as $action) {
                            $label = htmlspecialchars($action['label'], ENT_QUOTES | ENT_SUBSTITUTE);
                            $url = htmlspecialchars($action['url'], ENT_QUOTES | ENT_SUBSTITUTE);
                            $formattedReply .= "\n• <a href=\"{$url}\">{$label}</a>";
                        }
                    }

                    $this->sendMessage(
                        chatId: $chatId,
                        text: $formattedReply,
                        parseMode: 'HTML',
                        replyToMessageId: $messageId,
                        messageType: 'ai_reply'
                    );
                }
            }
        } catch (Throwable $e) {
            Log::error('[telegram] Webhook handling error: '.$e->getMessage(), ['payload' => $payload]);
        }
    }

    /**
     * Checks if the sender is the registered admin.
     */
    public function isAdmin(?string $fromUserId): bool
    {
        if (! $fromUserId || ! $this->adminUserId) {
            return false;
        }

        return $fromUserId === $this->adminUserId;
    }

    /**
     * Converts basic markdown syntax into Telegram-safe HTML.
     */
    private function markdownToTelegramHtml(string $markdown): string
    {
        // Convert bold: **text** -> <b>text</b>
        $html = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $markdown);

        // Convert inline code: `code` -> <code>code</code>
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', (string) $html);

        // Convert links: [text](url) -> <a href="url">text</a>
        $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', (string) $html);

        return (string) $html;
    }
}
