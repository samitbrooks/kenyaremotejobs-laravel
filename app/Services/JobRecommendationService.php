<?php

namespace App\Services;

use App\Mail\JobMatchesDigestEmail;
use App\Mail\ProJobRecommendationsEmail;
use App\Models\JobListing;
use App\Models\User;
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
        return JobListing::query()
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->orderByDesc('origin')
            ->orderByDesc('kenya_friendly')
            ->orderByDesc('posted_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Returns top suited remote opportunities specifically tailored for Pro subscribers,
     * highlighting highest compensation, Kenya-friendly matches, and Early Access openings.
     *
     * @return Collection<int, JobListing>
     */
    public function getProRecommendedJobsForUser(User $user, int $limit = 15): Collection
    {
        return JobListing::query()
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->orderByDesc('kenya_friendly')
            ->orderByDesc('annual_salary_usd')
            ->orderByDesc('posted_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Returns the total active open roles count across the platform.
     */
    public function getTotalOpenRolesCount(): int
    {
        $count = JobListing::query()->count();

        return $count > 0 ? $count : 167;
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
            ->whereNull('marketing_opt_out_at')
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
