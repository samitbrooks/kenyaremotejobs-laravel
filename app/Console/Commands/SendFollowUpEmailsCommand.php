<?php

namespace App\Console\Commands;

use App\Mail\FollowUpInvitationEmail;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('email:send-follow-ups {--user= : Target single user by email} {--delay-hours=24 : Minimum hours since registration before follow-up} {--limit= : Limit the number of recipients} {--dry-run : Preview recipients without sending} {--force : Send even if user previously received follow-up}')]
#[Description('Automatically send FlexJobs-style follow-up emails ("Still thinking about finding a remote job?") to non-subscribed users')]
class SendFollowUpEmailsCommand extends Command
{
    public function handle(): int
    {
        $targetEmail = $this->option('user');
        $delayHours = max(0, (int) $this->option('delay-hours'));
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $query = User::query()
            ->whereNull('marketing_opt_out_at')
            ->where('subscribed', false);

        if ($targetEmail) {
            $query->where('email', strtolower(trim((string) $targetEmail)));
        } else {
            if (! $force) {
                $query->whereNull('follow_up_sent_at');
            }

            // Target users whose free trial has ended OR who registered at least $delayHours ago
            $threshold = now()->subHours($delayHours);
            $query->where(function ($sub) use ($threshold) {
                $sub->where('created_at', '<=', $threshold)
                    ->orWhere(function ($trialSub) {
                        $trialSub->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<=', now());
                    });
            });
        }

        if ($limit) {
            $query->limit($limit);
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            $this->info('No eligible recipients found for follow-up emails at this time.');

            return self::SUCCESS;
        }

        $this->info("Found {$recipients->count()} recipient(s) for the Follow-Up campaign.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Name', 'Email', 'Created At', 'Trial Ends At', 'Follow-Up Sent At'],
                $recipients->map(fn (User $u) => [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->created_at?->toDateTimeString(),
                    $u->trial_ends_at?->toDateTimeString() ?? 'N/A',
                    $u->follow_up_sent_at?->toDateTimeString() ?? 'Never',
                ])
            );

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->queue(new FollowUpInvitationEmail($user));

                $user->forceFill([
                    'follow_up_sent_at' => now(),
                ])->save();

                $this->line(" <info>✓</info> Queued follow-up invitation to <comment>{$user->email}</comment>");
                $sent++;
            } catch (Throwable $e) {
                $this->error(" ✗ Failed queuing to {$user->email}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Follow-Up campaign complete: {$sent} queued, {$failed} failed.");

        return self::SUCCESS;
    }
}
