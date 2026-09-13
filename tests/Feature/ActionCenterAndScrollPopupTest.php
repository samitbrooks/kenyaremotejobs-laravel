<?php

namespace Tests\Feature;

use App\Services\KenyaCareerBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActionCenterAndScrollPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_render_action_center_and_scroll_popup(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ask Kariuki AI');
        $response->assertSee('80% of Remote Hires Apply in the First 48 Hours');

        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
        $jobsResponse->assertSee('Ask Kariuki AI');
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

    public function test_action_center_livewire_component_interactivity(): void
    {
        Livewire::test('action-center')
            ->assertSet('isOpen', false)
            ->call('toggle')
            ->assertSet('isOpen', true)
            ->call('send', 'How do I get paid via Wise?')
            ->assertSee('Wise (Recommended)')
            ->assertSee('M-Pesa');
    }
}
