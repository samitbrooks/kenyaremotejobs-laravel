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
                ->sortByDesc(fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])))
                ->take(8)
                ->values();
        } else {
            $feed = JobListing::visible()->where('kenya_friendly', true)->latest('posted_at')->limit(8)->get();
        }

        if ($feed->isEmpty()) {
            $feed = JobListing::visible()->latest('posted_at')->limit(8)->get();
        }
        $heroPreview = $feed->take(3);

        $unlockedIds = $user && ! $admin
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => $admin
            || $job->origin === 'employer'
            || (bool) $unlockedIds?->has($job->id);

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
