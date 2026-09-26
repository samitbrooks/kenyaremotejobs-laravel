<?php

namespace App\Console\Commands;

use App\Mail\FreeTrialInvitationEmail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('email:send-free-trial-invites {--user= : Target single user by email} {--pending-only : Send to users who initiated subscription checkout} {--all-free : Send to all free registered users} {--limit= : Limit the number of recipients} {--dry-run : Preview recipients without sending}')]
#[Description('Send FlexJobs-style 24-hour free trial invitation emails with one-click activation')]
class SendFreeTrialInvitesCommand extends Command
{
    public function handle(): int
    {
        $targetEmail = $this->option('user');
        $pendingOnly = $this->option('pending-only');
        $allFree = $this->option('all-free');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = (bool) $this->option('dry-run');

        $query = User::query()
            ->whereNull('marketing_opt_out_at')
            ->where('subscribed', false);

        if ($targetEmail) {
            $query->where('email', strtolower(trim((string) $targetEmail)));
        } elseif ($pendingOnly || ! $allFree) {
            // By default or with --pending-only, target users who attempted subscription checkout
            $pendingUserIds = Payment::query()
                ->where('purpose', 'subscription')
                ->where('status', 'pending')
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->all();

            $query->whereIn('id', $pendingUserIds);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            $this->info('No eligible recipients found matching the specified criteria.');

            return self::SUCCESS;
        }

        $this->info("Found {$recipients->count()} recipient(s) for the Free Trial invitation campaign.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Name', 'Email', 'Created At'],
                $recipients->map(fn (User $u) => [$u->id, $u->name, $u->email, $u->created_at?->diffForHumans()])
            );
            $this->warn('Dry-run mode: No emails were actually dispatched.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new FreeTrialInvitationEmail($user));
                $sentCount++;
                $this->line(" <info>✓</info> Dispatched free trial invitation to {$user->email}");
            } catch (Throwable $e) {
                $failedCount++;
                $this->error(" ✕ Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Campaign complete: {$sentCount} sent, {$failedCount} failed.");

        return self::SUCCESS;
    }
}
