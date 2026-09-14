<?php

namespace Tests\Feature;

use App\Mail\LoginLinkEmail;
use App\Mail\MarketingEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\KenyaCareerBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionCenterAndScrollPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_render_action_center_and_scroll_popup(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ask Daisy AI');
        $response->assertSee('80% of Remote Hires Apply in the First 48 Hours');

        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Ask Daisy AI');
    }

    public function test_career_bot_service_answers_foreign_payments(): void
    {
        $bot = app(KenyaCareerBotService::class);
        $result = $bot->ask('How do I receive payments from foreign clients in Kenya via Wise and M-Pesa?');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('reply', $result);
        $this->assertStringContainsString('Wise', $result['reply']);
        $this->assertStringContainsString('M-Pesa', $result['reply']);
    }

    public function test_career_bot_service_answers_w8ben_tax_inquiries(): void
    {
        $bot = app(KenyaCareerBotService::class);
        $result = $bot->ask('What is Form W-8BEN and how do I avoid 30% US tax with my KRA PIN?');

        $this->assertStringContainsString('W-8BEN', $result['reply']);
        $this->assertStringContainsString('KRA PIN', $result['reply']);
    }

    public function test_career_bot_service_explains_pro_early_access(): void
    {
        $bot = app(KenyaCareerBotService::class);
        $result = $bot->ask('Why should I get Pro Early Access and how does it give me full access?');

        $this->assertStringContainsString('48-Hour Early Access', $result['reply']);
        $this->assertStringContainsString('Full Access to All 800+ Jobs', $result['reply']);
    }

    public function test_career_bot_api_endpoint_returns_json_response(): void
    {
        $response = $this->postJson('/api/career-bot', [
            'message' => 'How do I receive payments from foreign clients in Kenya via Wise and M-Pesa?',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'reply',
                'suggested_actions',
            ]);

        $this->assertStringContainsString('Wise', $response->json('reply'));
        $this->assertStringContainsString('M-Pesa', $response->json('reply'));
    }

    public function test_career_bot_service_identifies_as_daisy_ai(): void
    {
        $bot = app(KenyaCareerBotService::class);
        $result = $bot->ask('Hello, who are you?');

        $this->assertStringContainsString('Daisy AI', $result['reply']);
    }

    public function test_career_bot_api_validation(): void
    {
        $response = $this->postJson('/api/career-bot', [
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_marketing_email_is_from_daisy_from_kenya_remote_jobs(): void
    {
        $user = User::factory()->create();
        $mail = new MarketingEmail($user, 'Weekly Remote Jobs', 'Here are your top jobs.');

        $envelope = $mail->envelope();
        $this->assertNotNull($envelope->from);
        $this->assertSame('Daisy from Kenya Remote Jobs', $envelope->from->name);

        $mail->assertSeeInHtml('Daisy from Kenya Remote Jobs');
    }

    public function test_welcome_and_login_emails_are_from_daisy(): void
    {
        $user = User::factory()->create();

        $welcomeMail = new WelcomeEmail($user);
        $this->assertSame('Daisy from Kenya Remote Jobs', $welcomeMail->envelope()->from->name);
        $welcomeMail->assertSeeInHtml('Daisy from Kenya Remote Jobs');

        $loginMail = new LoginLinkEmail($user, 'https://kenyaremotejobs.com/auth/verify', true);
        $this->assertSame('Daisy from Kenya Remote Jobs', $loginMail->envelope()->from->name);
        $loginMail->assertSeeInHtml('Daisy from Kenya Remote Jobs');
    }
}
