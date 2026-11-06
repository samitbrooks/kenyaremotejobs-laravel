<?php

namespace App\Services;

use App\Mail\JobMatchesDigestEmail;
use App\Mail\ProJobRecommendationsEmail;
use App\Models\JobListing;
use App\Models\User;
use App\Support\Matching;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class JobRecommendationService
{
    /**
     * Returns top active remote opportunities for the user.
     *
     * @return Collection<int, JobListing>
     */
    public function getRecommendedJobsForUser(User $user, int $limit = 18): Collection
    {
        $query = JobListing::visible()
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->orderByDesc('kenya_friendly')
            ->latest('posted_at');

        $profile = Matching::profileFor(null, $user);
        if (! $profile || ($profile['skills'] === [] && $profile['roles'] === [])) {
            return $query->get()->sortByDesc(fn (JobListing $job): int => $job->accessibilityScore())->take($limit)->values();
        }

        $jobs = $query->get()->filter(fn (JobListing $job): bool => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])) > 0)
            ->sortByDesc(fn (JobListing $job): int => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])));

        return $jobs->take($limit)->values();
    }

    /**
     * @return Collection<int, JobListing>
     */
    public function getProRecommendedJobsForUser(User $user, int $limit = 15): Collection
    {
        return $this->getRecommendedJobsForUser($user, $limit);
    }

    public function getTotalOpenRolesCount(): int
    {
        return JobListing::visible()->count();
    }

    /**
     * Dispatches the personalized Job Matches Digest email to a user.
     * Automatically delivers the VIP Pro edition to subscribed paying clients.
     */
    public function sendDigestToUser(User $user, bool $force = false, bool $immediate = false): bool
    {
        if (! $force && ! $user->receivesMarketingEmail()) {
            return false;
        }

        // If the client is an active paying Pro subscriber, deliver their differentiated VIP edition
        if ($user->subscribed) {
            return $this->sendProRecommendationsToUser($user, force: $force, immediate: $immediate);
        }

        $jobs = $this->getRecommendedJobsForUser($user, 18);
        if ($jobs->isEmpty()) {
            return false;
        }

        $totalCount = $this->getTotalOpenRolesCount();

        try {
            $mailable = new JobMatchesDigestEmail($user, $jobs, $totalCount);

            if ($immediate) {
                Mail::to($user->email)->send($mailable);
            } else {
                Mail::to($user->email)->queue($mailable);
            }

            if ($user->exists) {
                $user->updateQuietly(['last_job_digest_at' => now()]);
            }

            return true;
        } catch (Throwable $e) {
            Log::error("Failed sending job matches digest to user {$user->id} ({$user->email}): ".$e->getMessage());

            return false;
        }
    }

    /**
     * Dispatches the VIP Pro Job Recommendations email with real-time suited job suggestions
     * and concierge offerings ("What we can do for you") to paying subscribers.
     */
    public function sendProRecommendationsToUser(User $user, bool $force = false, bool $immediate = false): bool
    {
        if (! $force && ! $user->receivesMarketingEmail()) {
            return false;
        }

        $jobs = $this->getProRecommendedJobsForUser($user, 15);
        if ($jobs->isEmpty()) {
            $jobs = $this->getRecommendedJobsForUser($user, 15);
        }

        if ($jobs->isEmpty()) {
            return false;
        }

        $totalCount = $this->getTotalOpenRolesCount();

        try {
            $mailable = new ProJobRecommendationsEmail($user, $jobs, $totalCount);

            if ($immediate) {
                Mail::to($user->email)->send($mailable);
            } else {
                Mail::to($user->email)->queue($mailable);
            }

            if ($user->exists) {
                $user->updateQuietly(['last_job_digest_at' => now()]);
            }

            return true;
        } catch (Throwable $e) {
            Log::error("Failed sending VIP pro recommendations to user {$user->id} ({$user->email}): ".$e->getMessage());

            return false;
        }
    }

    /**
     * Sends real-time suited job alerts to all subscribed paying clients across the platform.
     */
    public function sendRealtimeAlertsToSubscribers(bool $immediate = false): int
    {
        $subscribers = User::query()
            ->where('subscribed', true)
            ->whereNull('banned_at')->whereNull('marketing_opt_out_at')
            ->get();

        $sentCount = 0;
        foreach ($subscribers as $subscriber) {
            $ok = $this->sendProRecommendationsToUser($subscriber, force: true, immediate: $immediate);
            if ($ok) {
                $sentCount++;
            }
        }

        return $sentCount;
    }
}
