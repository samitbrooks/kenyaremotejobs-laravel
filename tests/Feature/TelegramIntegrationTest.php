<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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

    public function test_transcription_is_prioritized_and_correctly_labelled_in_top_jobs(): void
    {
        $this->freezeTime();

        foreach (['transcription' => 'Remote Audio Transcriptionist', 'student' => 'Campus Student Ambassador', 'tech' => 'Backend Engineer'] as $id => $title) {
            JobListing::query()->create([
                'id' => $id,
                'title' => $title,
                'company' => 'Example Employer',
                'description' => 'Remote opportunity.',
                'source_name' => 'direct',
                'source_id' => $id,
                'source_url' => 'https://example.com/apply',
                'location' => 'Worldwide',
                'remote_type' => 'Worldwide Remote',
                'posted_at' => now(),
                'tags' => [],
                'audience_segments' => [],
                'kenya_friendly' => true,
                'kenya_score' => 90,
                'kenya_reasons' => ['Worldwide'],
            ]);
        }

        $service = app(TelegramService::class);
        $jobs = $service->selectJobsForBroadcast(2);

        $this->assertSame(['transcription', 'tech'], $jobs->pluck('id')->all());
        $digest = $service->formatJobDigest($jobs);
        $this->assertStringContainsString('TRANSCRIPTION &amp; CAPTIONING', $digest);
        $this->assertStringNotContainsString('FOCUS: DATA ANNOTATION', $digest);
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

        $response = $this->getJson('/webhooks/telegram', [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'is_configured' => true,
            'bot_username' => 'KenyaRemoteJobsBot',
            'channel_id' => '@kenyaremotejobs',
        ]);
    }

    public function test_telegram_webhook_status_endpoint_rejects_unauthorized_access(): void
    {
        $response = $this->getJson('/webhooks/telegram');
        $response->assertStatus(403);
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
                && str_contains($request['text'], 'Employer identity protected')
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

    public function test_telegram_broadcast_prioritizes_data_annotation_tutoring_and_ai_teaching(): void
    {
        // 1. General role
        JobListing::query()->create([
            'id' => 'tg-gen-1',
            'title' => 'Senior Vice President of Engineering',
            'company' => 'Acme Corp',
            'description' => 'Enterprise corporate executive role.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$150k',
            'annual_salary_usd' => ['min' => 150000, 'max' => 180000],
            'posted_at' => now()->subMinutes(5), // very fresh
            'kenya_friendly' => true,
            'kenya_score' => 80,
            'kenya_reasons' => ['Worldwide remote'],
            'tags' => ['Management', 'Engineering'],
            'audience_segments' => ['tech'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'g1',
            'source_url' => 'https://example.com/apply',
        ]);

        // 2. Data Annotation
        JobListing::query()->create([
            'id' => 'tg-annot-1',
            'title' => 'AI Data Annotation Specialist (English & General Reasoning)',
            'company' => 'Outlier',
            'description' => 'Annotate multimodal datasets and evaluation tasks.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$20/hr',
            'annual_salary_usd' => ['min' => 38000, 'max' => 48000],
            'posted_at' => now()->subHours(5),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['High accessibility', 'Worldwide remote'],
            'tags' => ['Data Annotation', 'AI', 'RLHF'],
            'audience_segments' => ['students', 'highschool'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'a1',
            'source_url' => 'https://example.com/apply',
        ]);

        // 3. Online Tutoring
        JobListing::query()->create([
            'id' => 'tg-tutor-1',
            'title' => 'Online Subject Matter Tutor & Curriculum Reviewer',
            'company' => 'Study.com',
            'description' => 'Help students globally with STEM and commerce subjects.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$25/hr',
            'annual_salary_usd' => ['min' => 45000, 'max' => 55000],
            'posted_at' => now()->subHours(6),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Student friendly', 'Worldwide remote'],
            'tags' => ['Tutoring', 'Education', 'Teaching'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 't1',
            'source_url' => 'https://example.com/apply',
        ]);

        // 4. AI Teaching / RLHF
        JobListing::query()->create([
            'id' => 'tg-ai-1',
            'title' => 'Generative AI RLHF Evaluator & Prompt Specialist',
            'company' => 'Scale AI',
            'description' => 'Evaluate generative models and prompt responses.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$30/hr',
            'annual_salary_usd' => ['min' => 50000, 'max' => 65000],
            'posted_at' => now()->subHours(7),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['AI training', 'Worldwide remote'],
            'tags' => ['AI Training', 'Prompt Engineering', 'RLHF'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'ai1',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 1005,
                    'chat' => ['id' => -100123456789, 'type' => 'channel'],
                ],
            ], 200),
        ]);

        $this->artisan('telegram:broadcast-jobs', [
            '--limit' => 4,
            '--chat' => '@kenyaremotejobs',
        ])
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            $text = $request['text'];

            return str_contains($text, 'Practical Online Work &amp; Remote Careers')
                && str_contains($text, 'DATA ANNOTATION &amp; RLHF')
                && str_contains($text, 'ONLINE TUTORING &amp; TEACHING')
                && str_contains($text, 'AI TEACHING &amp; MODEL TRAINING')
                && str_contains($text, 'Browse AI Training &amp; Annotation Roles')
                && str_contains($text, 'Browse Online Tutoring &amp; Student Gigs');
        });
    }

    public function test_telegram_webhook_handles_aitraining_command(): void
    {
        JobListing::query()->create([
            'id' => 'tg-ai-drop-1',
            'title' => 'AI Model Reviewer & Data Annotation Contributor',
            'company' => 'DataAnnotation Tech',
            'description' => 'Review and train LLMs.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$25/hr',
            'annual_salary_usd' => ['min' => 45000, 'max' => 55000],
            'posted_at' => now()->subHour(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['AI training', 'Worldwide remote'],
            'tags' => ['AI Training', 'Data Annotation', 'RLHF'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'ai_drop',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 1006, 'chat' => ['id' => 999, 'type' => 'private']],
            ], 200),
        ]);

        $payload = [
            'update_id' => 2001,
            'message' => [
                'message_id' => 77,
                'chat' => ['id' => 999, 'type' => 'private'],
                'text' => '/aitraining',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'TOP VERIFIED AI DATA ANNOTATION')
                && str_contains($request['text'], 'AI Model Reviewer');
        });
    }

    public function test_telegram_webhook_handles_tutoring_command(): void
    {
        JobListing::query()->create([
            'id' => 'tg-tutor-drop-1',
            'title' => 'Online Academic Q&A Tutor (Commerce & Math)',
            'company' => 'Studypool',
            'description' => 'Answer student academic queries.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$20/hr',
            'annual_salary_usd' => ['min' => 35000, 'max' => 45000],
            'posted_at' => now()->subHour(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Tutoring', 'Worldwide remote'],
            'tags' => ['Tutoring', 'Education'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'tutor_drop',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 1007, 'chat' => ['id' => 999, 'type' => 'private']],
            ], 200),
        ]);

        $payload = [
            'update_id' => 2002,
            'message' => [
                'message_id' => 78,
                'chat' => ['id' => 999, 'type' => 'private'],
                'text' => '/tutoring',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'TOP VERIFIED ONLINE TUTORING')
                && str_contains($request['text'], 'Online Academic Q&amp;A Tutor');
        });
    }

    public function test_telegram_webhook_handles_students_command(): void
    {
        JobListing::query()->create([
            'id' => 'tg-student-1',
            'title' => 'Campus Content & Social Media Ambassador',
            'company' => 'Global EdTech',
            'description' => 'Work flexible hours assisting students and creating campus content.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$15/hr',
            'annual_salary_usd' => ['min' => 25000, 'max' => 35000],
            'posted_at' => now()->subHour(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Student friendly', 'Worldwide remote'],
            'tags' => ['Social Media', 'Students', 'Campus'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'student_drop',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 1008, 'chat' => ['id' => 999, 'type' => 'private']],
            ], 200),
        ]);

        $payload = [
            'update_id' => 2003,
            'message' => [
                'message_id' => 79,
                'chat' => ['id' => 999, 'type' => 'private'],
                'text' => '/students',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'TOP VERIFIED CAMPUS &amp; STUDENT REMOTE JOBS')
                && str_contains($request['text'], 'Campus Content &amp; Social Media Ambassador')
                && str_contains($request['text'], 'FOCUS: STUDENT &amp; CAMPUS JOBS');
        });
    }

    public function test_telegram_webhook_handles_tech_command(): void
    {
        JobListing::query()->create([
            'id' => 'tg-tech-role-1',
            'title' => 'Full Stack Laravel & Vue Developer',
            'company' => 'Remote Dev Studio',
            'description' => 'Build high-performance web applications.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$75k - $95k',
            'annual_salary_usd' => ['min' => 75000, 'max' => 95000],
            'posted_at' => now()->subHour(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote'],
            'tags' => ['PHP', 'Laravel', 'Vue'],
            'audience_segments' => ['tech'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'tech_drop',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 1009, 'chat' => ['id' => 999, 'type' => 'private']],
            ], 200),
        ]);

        $payload = [
            'update_id' => 2004,
            'message' => [
                'message_id' => 80,
                'chat' => ['id' => 999, 'type' => 'private'],
                'text' => '/tech',
            ],
        ];

        $response = $this->postJson('/webhooks/telegram', $payload, [
            'X-Telegram-Bot-Api-Secret-Token' => 'test_secret_abc123',
        ]);

        $response->assertStatus(200);

        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'TOP VERIFIED REMOTE TECH &amp; SOFTWARE ENGINEERING ROLES')
                && str_contains($request['text'], 'Full Stack Laravel &amp; Vue Developer')
                && str_contains($request['text'], 'GLOBAL TECH');
        });
    }

    public function test_telegram_broadcast_drops_hybrid_mix_with_tech_and_student_roles(): void
    {
        // 1. Tech career role
        JobListing::query()->create([
            'id' => 'tg-hybrid-tech-1',
            'title' => 'Senior Backend Engineer (Go & PostgreSQL)',
            'company' => 'Supabase',
            'description' => 'Build distributed database tooling.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$110,000',
            'annual_salary_usd' => ['min' => 110000, 'max' => 130000],
            'posted_at' => now()->subHours(2),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote'],
            'tags' => ['Go', 'PostgreSQL', 'Backend'],
            'audience_segments' => ['tech'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'h_tech_1',
            'source_url' => 'https://example.com/apply',
        ]);

        // 2. Student & campus role
        JobListing::query()->create([
            'id' => 'tg-hybrid-student-1',
            'title' => 'Campus Student Ambassador & Peer Guide',
            'company' => 'Global Campus Network',
            'description' => 'Flexible task-based community guidance for campus students.',
            'location' => 'Worldwide Remote',
            'remote_type' => 'Worldwide Remote',
            'salary' => '$20/hr',
            'annual_salary_usd' => ['min' => 38000, 'max' => 45000],
            'posted_at' => now()->subHours(3),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Student friendly', 'Worldwide remote'],
            'tags' => ['Students', 'Campus'],
            'audience_segments' => ['students'],
            'origin' => 'direct',
            'source_name' => 'direct',
            'source_id' => 'h_student_1',
            'source_url' => 'https://example.com/apply',
        ]);

        Http::fake([
            'api.telegram.org/*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 1010,
                    'chat' => ['id' => -100123456789, 'type' => 'channel'],
                ],
            ], 200),
        ]);

        $this->artisan('telegram:broadcast-jobs', [
            '--limit' => 2,
            '--chat' => '@kenyaremotejobs',
        ])
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            $text = $request['text'];

            return str_contains($text, 'Practical Online Work &amp; Remote Careers')
                && str_contains($text, 'Senior Backend Engineer')
                && str_contains($text, 'GLOBAL TECH &amp; REMOTE CAREER')
                && str_contains($text, 'Student Ambassador')
                && str_contains($text, 'FOCUS: STUDENT')
                && str_contains($text, 'Browse Campus &amp; Student Remote Jobs');
        });
    }

    private function createTelegramRole(string $id, array $attributes = []): JobListing
    {
        return JobListing::create(array_merge([
            'id' => $id, 'title' => 'Customer Support Representative', 'company' => 'HiddenBrand Ltd',
            'description' => 'Answer customer questions.', 'source_name' => 'Employer', 'source_id' => $id,
            'source_url' => 'https://jobs.ashbyhq.com/hiddenbrand/'.$id.'/application',
            'location' => 'Remote — Kenya', 'remote_type' => 'Remote', 'posted_at' => now(),
            'kenya_friendly' => true, 'kenya_score' => 3, 'kenya_reasons' => [],
            'tags' => ['Customer Support'], 'audience_segments' => [], 'origin' => 'synced', 'tier' => 'basic',
        ], $attributes));
    }

    public function test_main_bot_feed_does_not_force_senior_or_advanced_annotation_roles_ahead_of_practical_online_work(): void
    {
        $this->createTelegramRole('senior', ['title' => 'Senior Software Engineer']);
        $this->createTelegramRole('advanced', ['title' => 'AI Data Annotator', 'description' => 'PhD in physics required.', 'tags' => ['AI Training', 'Students']]);
        foreach (['support', 'admin', 'writing'] as $id) {
            $this->createTelegramRole($id);
        }

        $selected = app(TelegramService::class)->selectJobsForBroadcast(3);

        $this->assertSame(['support', 'admin', 'writing'], $selected->modelKeys());
    }

    public function test_digest_keeps_identity_hidden_and_does_not_invent_pay_or_account_free_applications(): void
    {
        $job = $this->createTelegramRole('private', ['title' => 'HiddenBrand Customer Support', 'tags' => ['HiddenBrand'], 'location' => 'Kenya — HiddenBrand Ltd']);

        $digest = app(TelegramService::class)->formatJobDigest(collect([$job]));

        $this->assertStringNotContainsString('HiddenBrand', $digest);
        $this->assertStringNotContainsString('jobs.ashbyhq.com', $digest);
        $this->assertStringContainsString('Not stated by employer', $digest);
        $this->assertStringContainsString('Employer application page (account or assessment may be required)', $digest);
        $this->assertStringContainsString('Employer identities remain hidden', $digest);
        $this->assertStringNotContainsString('No degree required', $digest);
    }

    public function test_online_command_excludes_specialists_and_direct_command_excludes_listing_boards(): void
    {
        $this->createTelegramRole('direct-support');
        $this->createTelegramRole('board-support', ['title' => 'Virtual Assistant', 'source_url' => 'https://remoteok.com/role']);
        $this->createTelegramRole('senior', ['title' => 'Senior Research Engineer', 'source_url' => 'https://remoteok.com/senior']);
        Http::fake(['api.telegram.org/*/sendMessage' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);
        $service = app(TelegramService::class);

        $service->handleWebhook(['message' => ['message_id' => 1, 'from' => ['id' => 123], 'chat' => ['id' => 123, 'type' => 'private'], 'text' => '/online']]);
        $service->handleWebhook(['message' => ['message_id' => 2, 'from' => ['id' => 123], 'chat' => ['id' => 123, 'type' => 'private'], 'text' => '/direct']]);

        Http::assertSent(fn ($request): bool => str_contains($request['text'] ?? '', 'PRACTICAL ONLINE WORK')
            && str_contains($request['text'], 'Virtual Assistant') && ! str_contains($request['text'], 'Senior Research Engineer'));
        Http::assertSent(fn ($request): bool => str_contains($request['text'] ?? '', 'ROLES WITH EMPLOYER APPLICATION PAGES')
            && str_contains($request['text'], 'Customer Support Representative') && ! str_contains($request['text'], 'Virtual Assistant'));
    }

    public function test_bot_commands_include_online_work_and_direct_employer_filters(): void
    {
        Http::fake(['api.telegram.org/*/setMyCommands' => Http::response(['ok' => true])]);

        app(TelegramService::class)->setMyCommands();

        Http::assertSent(fn ($request): bool => in_array('online', array_column($request['commands'], 'command'), true)
            && in_array('direct', array_column($request['commands'], 'command'), true));
    }

    public function test_bot_visibility_includes_older_confirmed_employer_openings_but_excludes_expired_rows(): void
    {
        $this->freezeTime();
        $current = $this->createTelegramRole('current', ['posted_at' => now()->subMonths(4), 'last_seen_at' => now()]);
        $this->createTelegramRole('expired', ['posted_at' => now()->subMonths(4)]);

        $this->assertSame([$current->id], app(TelegramService::class)->selectJobsForBroadcast()->modelKeys());
    }

    public function test_failed_broadcast_does_not_mark_roles_as_already_sent(): void
    {
        Cache::forget('telegram_recent_broadcasted_job_ids');
        $this->createTelegramRole('unsent');
        Http::fake(['api.telegram.org/*/sendMessage' => Http::response(['ok' => false, 'description' => 'Unavailable'])]);

        $result = app(TelegramService::class)->broadcastDailyJobs(limit: 1);

        $this->assertFalse($result['success']);
        $this->assertNull(Cache::get('telegram_recent_broadcasted_job_ids'));
        Http::assertSentCount(1);
    }

    public function test_default_five_role_digest_fits_telegram_message_limit(): void
    {
        $jobs = collect();
        foreach (range(1, 5) as $index) {
            $jobs->push($this->createTelegramRole('digest-'.$index));
        }

        $digest = app(TelegramService::class)->formatJobDigest($jobs);

        $this->assertLessThanOrEqual(4096, mb_strlen($digest));
        $this->assertStringContainsString('5. Customer Support Representative', $digest);
    }
}
