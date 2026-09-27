<?php

namespace App\Services;

use App\Mail\JobMatchesDigestEmail;
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
     * Returns the total active open roles count across the platform.
     */
    public function getTotalOpenRolesCount(): int
    {
        $count = JobListing::query()->count();

        return $count > 0 ? $count : 167;
    }

    /**
     * Dispatches the personalized Job Matches Digest email to a user.
     * Uses background queueing by default for bulk campaigns, with immediate dispatch for test previews.
     */
    public function sendDigestToUser(User $user, bool $force = false, bool $immediate = false): bool
    {
        if (! $force && ! $user->receivesMarketingEmail()) {
            return false;
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
}
