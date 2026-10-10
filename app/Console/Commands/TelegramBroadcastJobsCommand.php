<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramBroadcastJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:broadcast-jobs {--limit=5 : Number of top jobs to post} {--chat= : Target channel or group ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcasts verified remote jobs to the KenyaRemoteJobs Telegram channel/group';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $targetChat = $this->option('chat') ? (string) $this->option('chat') : null;

        $this->info("Fetching up to {$limit} top verified remote jobs for Telegram broadcast...");

        $result = $telegramService->broadcastDailyJobs($targetChat, $limit);

        if ($result['success']) {
            $this->info("✅ Successfully broadcasted {$result['count']} jobs to Telegram!");

            return Command::SUCCESS;
        }

        $this->error("❌ Broadcast failed: {$result['error']}");

        return Command::FAILURE;
    }
}
