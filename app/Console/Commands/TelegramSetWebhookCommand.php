<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook {--url= : Override webhook URL} {--delete : Delete active webhook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers or deletes the Telegram Bot webhook';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService): int
    {
        if (! $telegramService->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env');

            return Command::FAILURE;
        }

        if ($this->option('delete')) {
            $this->info('Deleting webhook from Telegram API...');
            $res = $telegramService->deleteWebhook();

            if ($res['ok'] ?? false) {
                $this->info('✅ Webhook successfully deleted.');

                return Command::SUCCESS;
            }

            $this->error('❌ Failed: '.($res['description'] ?? 'Unknown error'));

            return Command::FAILURE;
        }

        $url = $this->option('url') ?: url('/webhooks/telegram');
        $secret = config('telegram.webhook_secret');

        $this->info("Setting webhook URL to: {$url}");

        $res = $telegramService->setWebhook($url, $secret);

        if ($res['ok'] ?? false) {
            $this->info('✅ Webhook set successfully!');
            $this->line('Description: '.($res['description'] ?? 'Webhook was set'));

            return Command::SUCCESS;
        }

        $this->error('❌ Failed to set webhook: '.($res['description'] ?? 'Unknown error'));

        return Command::FAILURE;
    }
}
