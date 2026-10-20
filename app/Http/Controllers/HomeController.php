<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\Audience;
use App\Support\Matching;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $admin = (bool) $user?->isAdmin();

        $total = JobListing::visible()->count();
        $totalKenyaFriendly = JobListing::visible()->where('kenya_friendly', true)->count();
        $totalFree = JobListing::visible()->where('origin', 'employer')->count();

        $profile = Matching::parseProfileCookie(request()->cookie(Matching::COOKIE_NAME));

        if ($profile) {
            $feed = JobListing::visible()->where('kenya_friendly', true)->get()
                ->sortByDesc(fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])) + ($job->accessibilityScore() / 10))
                ->take(12)
                ->values();
        } else {
            // Prioritize accessible, high-acceptance roles (data annotation, AI training, student & campus, transcription)
            $feed = JobListing::visible()->where('kenya_friendly', true)->get()
                ->sortBy([
                    fn (JobListing $a, JobListing $b) => $b->accessibilityScore() <=> $a->accessibilityScore(),
                    fn (JobListing $a, JobListing $b) => ($b->posted_at?->timestamp ?? 0) <=> ($a->posted_at?->timestamp ?? 0),
                ])
                ->take(12)
                ->values();
        }

        if ($feed->isEmpty()) {
            $feed = JobListing::visible()->latest('posted_at')->limit(12)->get();
        }
        $heroPreview = $feed->take(3);

        $unlockedIds = $user && ! $user->hasActiveAccess()
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => (bool) (
            $user?->hasActiveAccess()
            || ($unlockedIds && $unlockedIds->has($job->id))
        );

        $matchPercent = $profile
            ? fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description']))
            : fn () => null;

        $audienceCounts = collect(Audience::ORDER)
            ->map(fn ($segment) => [
                'segment' => $segment,
                'count' => JobListing::visible()->whereJsonContains('audience_segments', $segment)->count(),
            ])
            ->filter(fn ($a) => $a['count'] > 0)
            ->values();

        $justPosted = JobListing::visible()->latest('posted_at')->limit(5)->get();

        return view('home', [
            'total' => $total,
            'totalKenyaFriendly' => $totalKenyaFriendly,
            'totalFree' => $totalFree,
            'feed' => $feed,
            'heroPreview' => $heroPreview,
            'justPosted' => $justPosted,
            'isUnlocked' => $isUnlocked,
            'matchPercent' => $matchPercent,
            'hasProfile' => (bool) $profile,
            'audienceCounts' => $audienceCounts,
        ]);
    }
}
