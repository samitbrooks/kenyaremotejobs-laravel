<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\Console\Command;

class SendJobMatchesDigestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:send-digest 
                            {--test= : Send a test preview to a specific email address}
                            {--user= : Target a specific user ID}
                            {--dry-run : Simulate execution without sending emails}
                            {--force : Ignore recent send throttling and send anyway}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically send curated job matches digest emails to registered users';

    /**
     * Execute the console command.
     */
    public function handle(JobRecommendationService $recommendationService): int
    {
        $testEmail = $this->option('test');
        $isDryRun = (bool) $this->option('dry-run');
        $isForce = (bool) $this->option('force');
        $userId = $this->option('user');

        // Test mode: Send a single preview to the provided email
        if ($testEmail) {
            $this->info("Sending test job matches digest preview to: {$testEmail}");

            $previewUser = User::where('email', $testEmail)->first() ?? new User([
                'name' => 'Sammie',
                'email' => $testEmail,
            ]);

            if (! $previewUser->exists) {
                $previewUser->id = 1;
            }

            if ($isDryRun) {
                $this->info('[Dry-run] Would send test email to '.$testEmail);

                return Command::SUCCESS;
            }

            $success = $recommendationService->sendDigestToUser($previewUser, force: true);

            if ($success) {
                $this->info("✓ Test digest successfully delivered to {$testEmail}");

                return Command::SUCCESS;
            }

            $this->error("✗ Failed delivering test digest to {$testEmail}. Check system logs.");

            return Command::FAILURE;
        }

        // Query eligible registered users
        $query = User::query()
            ->whereNull('marketing_opt_out_at')
            ->when($userId, fn ($q) => $q->where('id', $userId))
            ->when(! $isForce, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('last_job_digest_at')
                        ->orWhere('last_job_digest_at', '<=', now()->subDays(2));
                });
            });

        $count = $query->count();
        $this->info("Found {$count} eligible user(s) for the Job Matches Digest.");

        if ($count === 0) {
            $this->info('No eligible recipients found at this time.');

            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $this->info('[DRY RUN MODE] Completed preview. No emails were dispatched.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $sent = 0;
        $failed = 0;

        $query->chunk(50, function ($users) use ($recommendationService, $isForce, &$sent, &$failed, $bar) {
            foreach ($users as $user) {
                $ok = $recommendationService->sendDigestToUser($user, force: $isForce);
                if ($ok) {
                    $sent++;
                } else {
                    $failed++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->info("✓ Job matches digest dispatched: {$sent} sent, {$failed} failed.");

        return Command::SUCCESS;
    }
}
