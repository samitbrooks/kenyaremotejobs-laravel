<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('jobs:send-pro-alerts {--user= : Specific subscriber user ID to notify} {--immediate : Dispatch immediately instead of background queue}')]
#[Description('Send real-time tailored job recommendations and concierge offerings to subscribed Pro clients')]
class SendProJobAlertsCommand extends Command
{
    public function handle(JobRecommendationService $recommendationService): int
    {
        $userId = $this->option('user');
        $immediate = (bool) $this->option('immediate');

        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->error("User #{$userId} not found.");

                return self::FAILURE;
            }

            if (! $user->subscribed) {
                $this->warn("User #{$userId} ({$user->email}) is not currently marked as subscribed. Sending anyway with force=true...");
            }

            $ok = $recommendationService->sendProRecommendationsToUser($user, force: true, immediate: $immediate);

            if ($ok) {
                $this->info("✓ Real-time VIP Pro matches email successfully dispatched to {$user->email}.");

                return self::SUCCESS;
            }

            $this->error("Failed to send VIP Pro matches email to {$user->email}. Check logs.");

            return self::FAILURE;
        }

        $sentCount = $recommendationService->sendRealtimeAlertsToSubscribers(immediate: $immediate);

        $this->info("Dispatched real-time Pro recommendation alerts to {$sentCount} active paying subscriber(s).");

        return self::SUCCESS;
    }
}
