<?php

namespace Tests\Feature;

use App\Mail\FollowUpInvitationEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FollowUpEmailCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_up_email_renders_expected_content_and_brand_icon(): void
    {
        $user = User::factory()->create([
            'name' => 'Sammie Brooks',
            'email' => 'sammie@example.com',
            'subscribed' => false,
        ]);

        $mailable = new FollowUpInvitationEmail($user);
        $rendered = $mailable->render();

        $this->assertSame('Still thinking about finding a remote job?', $mailable->envelope()->subject);
        $this->assertStringContainsString('logo-icon.png', $rendered);
        $this->assertStringContainsString('FIND A BETTER WAY TO WORK', $rendered);
        $this->assertStringContainsString('Hello Sammie,', $rendered);
        $this->assertStringContainsString('Our Members Have Been Hired by', $rendered);
        $this->assertStringContainsString('Automattic', $rendered);
        $this->assertStringContainsString('deel.', $rendered);
        $this->assertStringContainsString('KenyaRemoteJobs Success Stories', $rendered);
        $this->assertStringContainsString('Faith M.', $rendered);
        $this->assertStringContainsString('Brian K.', $rendered);
        $this->assertStringContainsString('/pricing', $rendered);
    }

    public function test_send_follow_up_command_targets_only_eligible_users(): void
    {
        Mail::fake();

        // 1. Eligible: registered 2 days ago, not subscribed, no follow-up sent
        $eligibleOld = User::factory()->create([
            'email' => 'eligible.old@example.com',
            'subscribed' => false,
            'marketing_opt_out_at' => null,
            'follow_up_sent_at' => null,
            'created_at' => now()->subHours(48),
        ]);

        // 2. Eligible: registered recently (2h ago) but trial already expired
        $eligibleTrialExpired = User::factory()->create([
            'email' => 'eligible.trial@example.com',
            'subscribed' => false,
            'marketing_opt_out_at' => null,
            'trial_started_at' => now()->subHours(26),
            'trial_ends_at' => now()->subHours(2),
            'follow_up_sent_at' => null,
            'created_at' => now()->subHours(2),
        ]);

        // 3. Ineligible: already subscribed
        $ineligibleSubscribed = User::factory()->create([
            'email' => 'subscribed@example.com',
            'subscribed' => true,
            'created_at' => now()->subDays(3),
        ]);

        // 4. Ineligible: opted out of marketing
        $ineligibleOptOut = User::factory()->create([
            'email' => 'optout@example.com',
            'subscribed' => false,
            'marketing_opt_out_at' => now()->subDay(),
            'created_at' => now()->subDays(3),
        ]);

        // 5. Ineligible: already received follow-up
        $ineligibleAlreadySent = User::factory()->create([
            'email' => 'already.sent@example.com',
            'subscribed' => false,
            'follow_up_sent_at' => now()->subHours(10),
            'created_at' => now()->subDays(3),
        ]);

        // 6. Ineligible: registered only 1 hour ago (within 24h grace window and no expired trial)
        $ineligibleTooNew = User::factory()->create([
            'email' => 'too.new@example.com',
            'subscribed' => false,
            'follow_up_sent_at' => null,
            'created_at' => now()->subHour(),
        ]);

        $this->artisan('email:send-follow-ups')
            ->expectsOutputToContain('Found 2 recipient(s)')
            ->assertSuccessful();

        Mail::assertQueued(FollowUpInvitationEmail::class, 2);
        Mail::assertQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('eligible.old@example.com'));
        Mail::assertQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('eligible.trial@example.com'));
        Mail::assertNotQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('subscribed@example.com'));
        Mail::assertNotQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('optout@example.com'));
        Mail::assertNotQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('already.sent@example.com'));
        Mail::assertNotQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('too.new@example.com'));

        $this->assertNotNull($eligibleOld->fresh()->follow_up_sent_at);
        $this->assertNotNull($eligibleTrialExpired->fresh()->follow_up_sent_at);
        $this->assertNull($ineligibleTooNew->fresh()->follow_up_sent_at);
    }

    public function test_send_follow_up_command_dry_run_does_not_queue_mail_or_mutate_db(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'subscribed' => false,
            'created_at' => now()->subDays(2),
            'follow_up_sent_at' => null,
        ]);

        $this->artisan('email:send-follow-ups', ['--dry-run' => true])
            ->expectsOutputToContain('Found 1 recipient(s)')
            ->assertSuccessful();

        Mail::assertNothingQueued();
        $this->assertNull($user->fresh()->follow_up_sent_at);
    }

    public function test_send_follow_up_command_targets_specific_user_option(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'target.user@example.com',
            'subscribed' => false,
        ]);

        $other = User::factory()->create([
            'email' => 'other.user@example.com',
            'subscribed' => false,
            'created_at' => now()->subDays(3),
        ]);

        $this->artisan('email:send-follow-ups', ['--user' => 'target.user@example.com'])
            ->expectsOutputToContain('Found 1 recipient(s)')
            ->assertSuccessful();

        Mail::assertQueued(FollowUpInvitationEmail::class, 1);
        Mail::assertQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('target.user@example.com'));
        Mail::assertNotQueued(FollowUpInvitationEmail::class, fn ($mail) => $mail->hasTo('other.user@example.com'));
    }
}
