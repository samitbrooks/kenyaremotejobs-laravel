<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\Audience;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $admin = (bool) $user?->isAdmin();

        $total = JobListing::count();
        $totalKenyaFriendly = JobListing::where('kenya_friendly', true)->count();
        $totalFree = JobListing::where('origin', 'employer')
            ->orWhere('posted_at', '<=', now()->subDays(config('jobs.premium_window_days')))
            ->count();

        $feed = JobListing::where('kenya_friendly', true)->latest('posted_at')->limit(8)->get();
        if ($feed->isEmpty()) {
            $feed = JobListing::latest('posted_at')->limit(8)->get();
        }
        $heroPreview = $feed->take(3);

        $unlockedIds = $user && ! $admin
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => $admin
            || $job->origin === 'employer'
            || $job->is_free
            || (bool) $unlockedIds?->has($job->id);

        $audienceCounts = collect(Audience::ORDER)
            ->map(fn ($segment) => [
                'segment' => $segment,
                'count' => JobListing::whereJsonContains('audience_segments', $segment)->count(),
            ])
            ->filter(fn ($a) => $a['count'] > 0)
            ->values();

        return view('home', [
            'total' => $total,
            'totalKenyaFriendly' => $totalKenyaFriendly,
            'totalFree' => $totalFree,
            'feed' => $feed,
            'heroPreview' => $heroPreview,
            'isUnlocked' => $isUnlocked,
            'audienceCounts' => $audienceCounts,
        ]);
    }
}
