<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests Telegram Bot connection and displays bot metadata';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService): int
    {
        if (! $telegramService->isConfigured()) {
            $this->warn('⚠️ TELEGRAM_BOT_TOKEN is not yet set in .env.');
            $this->line('To set up your bot:');
            $this->line('1. Message @BotFather on Telegram and send /newbot');
            $this->line('2. Copy the token into your .env: TELEGRAM_BOT_TOKEN=your_token_here');

            return Command::FAILURE;
        }

        $this->info('Testing connection to Telegram Bot API...');

        $res = $telegramService->getMe();

        if ($res['ok'] ?? false) {
            $bot = $res['result'] ?? [];
            $this->info('✅ Telegram Bot connected successfully!');
            $this->table(['Key', 'Value'], [
                ['Bot ID', $bot['id'] ?? 'N/A'],
                ['Bot Name', $bot['first_name'] ?? 'N/A'],
                ['Username', '@'.($bot['username'] ?? 'N/A')],
                ['Can Join Groups', ! empty($bot['can_join_groups']) ? 'Yes' : 'No'],
                ['Can Read All Group Msgs', ! empty($bot['can_read_all_group_messages']) ? 'Yes' : 'No'],
                ['Configured Channel', config('telegram.channel_id') ?: '(None)'],
                ['Configured Group', config('telegram.group_id') ?: '(None)'],
                ['Admin User ID', config('telegram.admin_user_id') ?: '(None)'],
            ]);

            $profileRes = $telegramService->setBotProfile();
            if ($profileRes['ok'] ?? false) {
                $this->info('✅ Telegram Bot description & command menu (/jobs, /ask, /help) registered successfully!');
            }

            return Command::SUCCESS;
        }

        $this->error('❌ Telegram connection failed: '.($res['description'] ?? 'Unknown error'));

        return Command::FAILURE;
    }
}
