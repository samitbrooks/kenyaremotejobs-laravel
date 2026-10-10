<?php

namespace Tests\Feature;

use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('telegram.bot_token', 'test_fake_bot_token_12345');
        Config::set('telegram.bot_username', 'KenyaRemoteJobsBot');
        Config::set('telegram.channel_id', '@kenyaremotejobs');
        Config::set('telegram.group_id', '-100123456789');
        Config::set('telegram.admin_user_id', '99887766');
        Config::set('telegram.webhook_secret', 'test_secret_abc123');

        Http::preventStrayRequests();
    }

    public function test_telegram_webhook_status_endpoint_returns_json(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => [
                    'url' => 'https://kenyaremotejobs.com/webhooks/telegram',
                    'has_custom_certificate' => false,
                    'pending_update_count' => 0,
                ],
            ], 200),
        ]);

        $response = $this->getJson('/webhooks/telegram');

        $response->assertStatus(200);
        $response->assertJson([
            'is_configured' => true,
            'bot_username' => 'KenyaRemoteJobsBot',
            'channel_id' => '@kenyaremotejobs',
        ]);
    }

    public function test_telegram_webhook_rejects_invalid_secret_token(): void
    {
        $response = $this->postJson('/webhooks/telegram', [
            'update_id' => 123456,
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'invalid_token',
        ]);

        $response->assertStatus(403);
    }

    public function test_telegram_webhook_welcomes_new_members(): void
    {
        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 901,
                    'chat' => ['id' => -100123456789, 'type' => 'supergroup'],
                ],
            ], 200),
        ]);

        $payload = [
            'update_id' => 1001,
            'message' => [
                'message_id' => 50,
                'chat' => [
                    'id' => -100123456789,
                    'type' => 'supergroup',
                    'title' => 'Kenya Remote Jobs Community',
                ],
                'new_chat_members' => [
                    [
                        'id' => 45678,
                        'is_bot' => false,
                        'first_name' => 'Wanjiku',
                        'username' => 'wanjiku_k',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Karibu sana, Wanjiku');
        });

        $this->assertDatabaseHas('telegram_messages', [
            'chat_id' => '-100123456789',
            'message_type' => 'welcome',
            'direction' => 'outbound',
        ]);
    }

    public function test_telegram_webhook_handles_start_command(): void
    {
        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 902,
                    'chat' => ['id' => 777, 'type' => 'private'],
                ],
            ], 200),
        ]);

        $payload = [
            'update_id' => 1002,
            'message' => [
                'message_id' => 51,
                'from' => [
                    'id' => 777,
                    'first_name' => 'David',
                ],
                'chat' => [
                    'id' => 777,
                    'type' => 'private',
                ],
                'text' => '/start',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Welcome to Kenya Remote Jobs Assistant');
        });
    }

    public function test_telegram_webhook_handles_ask_command_with_ai(): void
    {
        Http::fake([
            'api.telegram.org/*/sendChatAction' => Http::response(['ok' => true], 200),
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 903,
                    'chat' => ['id' => -100123456789, 'type' => 'supergroup'],
                ],
            ], 200),
        ]);

        $payload = [
            'update_id' => 1003,
            'message' => [
                'message_id' => 52,
                'from' => [
                    'id' => 888,
                    'first_name' => 'Brian',
                ],
                'chat' => [
                    'id' => -100123456789,
                    'type' => 'supergroup',
                ],
                'text' => '/ask How do I receive money from US clients with Wise and M-Pesa?',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Wise');
        });

        $this->assertDatabaseHas('telegram_messages', [
            'chat_id' => '-100123456789',
            'message_type' => 'ai_reply',
            'direction' => 'outbound',
        ]);
    }

    public function test_telegram_broadcast_jobs_artisan_command(): void
    {
        JobListing::query()->create([
            'id' => 'test-job-tg-1',
            'title' => 'Senior Laravel Engineer',
            'company' => 'Supabase Corp',
            'description' => 'Great remote engineering role.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$80k - $110k',
            'annual_salary_usd' => ['min' => 80000, 'max' => 110000],
            'posted_at' => now()->subHours(2),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote', 'East Africa timezone compatible'],
            'tags' => ['PHP', 'Laravel', 'Backend'],
            'audience_segments' => ['tech'],
            'source_name' => 'direct',
            'source_id' => '1',
            'source_url' => 'https://example.com/apply',
            'origin' => 'direct',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 999,
                    'chat' => ['id' => -100123456789, 'type' => 'channel'],
                ],
            ], 200),
        ]);

        $this->artisan('telegram:broadcast-jobs', [
            '--limit' => 3,
            '--chat' => '@kenyaremotejobs',
        ])
            ->expectsOutputToContain('Successfully broadcasted')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Senior Laravel Engineer')
                && str_contains($request['text'], 'Verified Global Employer')
                && ! str_contains($request['text'], 'Supabase Corp');
        });
    }

    public function test_telegram_set_webhook_artisan_command(): void
    {
        Http::fake([
            'api.telegram.org/*/setWebhook' => Http::response([
                'ok' => true,
                'description' => 'Webhook was set',
            ], 200),
        ]);

        $this->artisan('telegram:set-webhook')
            ->expectsOutputToContain('Webhook set successfully')
            ->assertExitCode(0);
    }
}
