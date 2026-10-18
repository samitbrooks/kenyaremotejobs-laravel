<?php

namespace Tests\Feature;

use App\Mail\TicketReplyNotificationMail;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('jobs.admin_emails', ['admin@kenyaremotejobs.com']);
    }

    public function test_guest_can_view_support_desk_and_submit_ticket(): void
    {
        $response = $this->get('/support');
        $response->assertStatus(200);
        $response->assertSee('Help &amp; Support Desk', false);
        $response->assertSee('Open a New Support Ticket');

        $submitResponse = $this->post('/support', [
            'name' => 'Wanjiku Mwangi',
            'email' => 'wanjiku@example.com',
            'category' => 'payment',
            'priority' => 'high',
            'subject' => 'M-Pesa payment confirmation delay',
            'message' => 'I made a payment of KES 250 via M-Pesa but my Pro access has not activated yet.',
        ]);

        $this->assertDatabaseHas('support_tickets', [
            'email' => 'wanjiku@example.com',
            'name' => 'Wanjiku Mwangi',
            'category' => 'payment',
            'priority' => 'high',
            'subject' => 'M-Pesa payment confirmation delay',
            'status' => 'open',
        ]);

        $ticket = SupportTicket::where('email', 'wanjiku@example.com')->first();
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('KRJ-TKT-', $ticket->ticket_number);

        $submitResponse->assertRedirect(route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ]));
    }

    public function test_authenticated_user_ticket_is_automatically_associated_and_shown_in_account(): void
    {
        $user = User::factory()->create([
            'name' => 'Kipchoge Keino',
            'email' => 'kipchoge@example.com',
        ]);

        $this->actingAs($user)->post('/support', [
            'name' => 'Kipchoge Keino',
            'email' => 'kipchoge@example.com',
            'category' => 'account',
            'priority' => 'normal',
            'subject' => 'CV tailoring tips for AWS DevOps role',
            'message' => 'Need advice on tailoring my CV for cloud roles and updating my subscription details.',
        ]);

        $ticket = SupportTicket::where('email', 'kipchoge@example.com')->first();
        $this->assertNotNull($ticket);
        $this->assertEquals($user->id, $ticket->user_id);

        $accountResponse = $this->actingAs($user)->get('/account');
        $accountResponse->assertStatus(200);
        $accountResponse->assertSee('CV tailoring tips for AWS DevOps role');
        $accountResponse->assertSee($ticket->ticket_number);
    }

    public function test_honeypot_submission_is_silently_rejected(): void
    {
        $response = $this->post('/support', [
            'name' => 'Bot Spammer',
            'email' => 'bot@spammer.com',
            'category' => 'general',
            'priority' => 'normal',
            'subject' => 'Buy cheap links',
            'message' => 'Spam content and links here.',
            'website_hp' => 'SPAM_BOT_VALUE', // Honeypot filled
        ]);

        $response->assertRedirect('/support');
        $this->assertDatabaseMissing('support_tickets', [
            'email' => 'bot@spammer.com',
        ]);
    }

    public function test_guest_can_access_ticket_via_access_token_and_post_reply(): void
    {
        $ticket = SupportTicket::factory()->create([
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
            'subject' => 'Question about remote tax withholding',
            'status' => 'resolved',
        ]);

        // Accessing without token or auth redirects to support index with error
        $unauthorizedResponse = $this->get(route('support.show', $ticket->ticket_number));
        $unauthorizedResponse->assertRedirect(route('support.index'));
        $unauthorizedResponse->assertSessionHas('error');

        // Accessing with token works and saves token in session
        $authorizedResponse = $this->get(route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ]));
        $authorizedResponse->assertStatus(200);
        $authorizedResponse->assertSee('Question about remote tax withholding');

        // Customer replies to the ticket
        $replyResponse = $this->post(route('support.reply', $ticket->ticket_number), [
            'message' => 'Thank you, but I have a follow up regarding W-8BEN form.',
        ]);

        $replyResponse->assertRedirect(route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ]));

        $this->assertDatabaseHas('support_ticket_replies', [
            'support_ticket_id' => $ticket->id,
            'message' => 'Thank you, but I have a follow up regarding W-8BEN form.',
            'is_admin' => false,
            'sender_name' => 'Jane Doe',
        ]);

        // Replying to a resolved ticket re-opens it
        $this->assertEquals('open', $ticket->fresh()->status);
    }

    public function test_customer_can_lookup_ticket_with_ticket_number_and_email(): void
    {
        $ticket = SupportTicket::factory()->create([
            'email' => 'candidate@example.com',
            'name' => 'Candidate',
            'subject' => 'Interview inquiry',
        ]);

        // Invalid email lookup fails and redirects with lookup_error
        $failLookup = $this->post('/support/lookup', [
            'ticket_number' => $ticket->ticket_number,
            'email' => 'wrong@example.com',
        ]);
        $failLookup->assertSessionHas('lookup_error');

        // Correct lookup succeeds and redirects to ticket
        $successLookup = $this->post('/support/lookup', [
            'ticket_number' => $ticket->ticket_number,
            'email' => 'candidate@example.com',
        ]);

        $successLookup->assertRedirect(route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ]));

        // Follow up request succeeds because session is authorized
        $showResponse = $this->get(route('support.show', $ticket->ticket_number));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Interview inquiry');
    }

    public function test_admin_can_view_tickets_list_and_filter_by_status(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@kenyaremotejobs.com',
        ]);

        SupportTicket::factory()->create([
            'subject' => 'Ticket One Open',
            'status' => 'open',
        ]);

        SupportTicket::factory()->create([
            'subject' => 'Ticket Two Resolved',
            'status' => 'resolved',
        ]);

        // When viewing all tickets
        $response = $this->actingAs($admin)
            ->withSession(['admin_authenticated' => true])
            ->get(route('admin.tickets.index', ['status' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('Support Tickets');
        $response->assertSee('Ticket One Open');
        $response->assertSee('Ticket Two Resolved');

        // Filter by open only
        $filterResponse = $this->actingAs($admin)
            ->withSession(['admin_authenticated' => true])
            ->get(route('admin.tickets.index', ['status' => 'open']));

        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Ticket One Open');
        $filterResponse->assertDontSee('Ticket Two Resolved');

        // Filter by resolved only
        $resolvedResponse = $this->actingAs($admin)
            ->withSession(['admin_authenticated' => true])
            ->get(route('admin.tickets.index', ['status' => 'resolved']));

        $resolvedResponse->assertStatus(200);
        $resolvedResponse->assertSee('Ticket Two Resolved');
        $resolvedResponse->assertDontSee('Ticket One Open');
    }

    public function test_admin_can_reply_to_ticket_and_notify_customer(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'name' => 'Support Lead',
            'email' => 'admin@kenyaremotejobs.com',
        ]);

        $ticket = SupportTicket::factory()->create([
            'email' => 'customer@example.com',
            'name' => 'Customer Name',
            'status' => 'open',
            'subject' => 'Need help upgrading',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_authenticated' => true])
            ->post(route('admin.tickets.reply', $ticket), [
                'message' => 'Hello! We have reviewed your account and activated your Pro subscription.',
                'status' => 'resolved',
                'send_email' => '1',
            ]);

        $response->assertRedirect(route('admin.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_replies', [
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'is_admin' => true,
            'sender_name' => 'Support Lead',
        ]);

        $this->assertEquals('resolved', $ticket->fresh()->status);

        Mail::assertSent(TicketReplyNotificationMail::class, function ($mail) use ($ticket) {
            return $mail->hasTo('customer@example.com') &&
                   $mail->ticket->id === $ticket->id;
        });
    }

    public function test_admin_can_quick_update_ticket_status(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@kenyaremotejobs.com',
        ]);

        $ticket = SupportTicket::factory()->create([
            'status' => 'open',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_authenticated' => true])
            ->post(route('admin.tickets.status', $ticket), [
                'status' => 'closed',
                'priority' => 'urgent',
            ]);

        $response->assertRedirect(route('admin.tickets.show', $ticket));

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertEquals('urgent', $ticket->priority);
    }
}
