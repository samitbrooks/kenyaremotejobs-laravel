<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WhatsAppBusinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('jobs.admin_emails', ['superadmin@kenyaremotejobs.com']);
        Config::set('whatsapp.webhook_verify_token', 'test_verify_token_123');
    }

    public function test_meta_webhook_verification_succeeds_with_correct_token(): void
    {
        $response = $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=test_verify_token_123&hub_challenge=challenge_12345');

        $response->assertStatus(200);
        $this->assertEquals('challenge_12345', $response->getContent());
    }

    public function test_meta_webhook_verification_fails_with_invalid_token(): void
    {
        $response = $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=wrong_token&hub_challenge=challenge_12345');

        $response->assertStatus(403);
    }

    public function test_inbound_whatsapp_webhook_persists_incoming_message(): void
    {
        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => '10001',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '254700000000',
                                    'phone_number_id' => '99999',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Wanjiku Kamau'],
                                        'wa_id' => '254712345678',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '254712345678',
                                        'id' => 'wamid.HBgL12345678',
                                        'timestamp' => '1600000000',
                                        'text' => [
                                            'body' => 'Hello! Do you have any remote customer support roles open today?',
                                        ],
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            'field' => 'messages',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/webhooks/whatsapp', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('whatsapp_messages', [
            'phone_number' => '254712345678',
            'direction' => 'inbound',
            'content' => 'Hello! Do you have any remote customer support roles open today?',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_whatsapp_hub(): void
    {
        $response = $this->get(route('admin.whatsapp'));
        $response->assertRedirect(route('admin.login', ['next' => '/admin/whatsapp']));
    }

    public function test_admin_can_view_whatsapp_management_hub(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('password123'),
        ]);

        WhatsAppMessage::create([
            'phone_number' => '254712345678',
            'direction' => 'inbound',
            'content' => 'Hi, looking for data entry jobs',
            'status' => 'received',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.whatsapp'));

        $response->assertStatus(200);
        $response->assertSee('WhatsApp Business Hub');
        $response->assertSee('254712345678');
        $response->assertSee('looking for data entry jobs');
    }

    public function test_admin_can_send_whatsapp_message_from_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin@kenyaremotejobs.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.whatsapp.send'), [
            'phone_number' => '0712345678',
            'message' => 'Hello from KenyaRemoteJobs Admin! We have 3 new customer support roles open today.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('whatsapp_messages', [
            'phone_number' => '254712345678',
            'direction' => 'outbound',
            'content' => 'Hello from KenyaRemoteJobs Admin! We have 3 new customer support roles open today.',
        ]);
    }
}
