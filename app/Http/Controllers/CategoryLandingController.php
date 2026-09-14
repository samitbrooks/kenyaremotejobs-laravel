<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\JobCategorySeo;
use App\Support\Matching;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryLandingController extends Controller
{
    private const PAGE_SIZE = 20;

    public function show(Request $request, string $slug): View
    {
        $category = JobCategorySeo::find($slug);

        if (! $category) {
            abort(404);
        }

        $searchTerms = $category['search_terms'];

        $query = JobListing::visible()->where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->orWhere('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('tags', 'like', "%{$term}%");
            }
        });

        $totalJobs = (clone $query)->count();
        $totalKenyaFriendly = (clone $query)->where('kenya_friendly', true)->count();

        $page = max(1, (int) $request->query('page', 1));
        $totalPages = max(1, (int) ceil($totalJobs / self::PAGE_SIZE));
        $page = min($page, $totalPages);

        $jobs = (clone $query)
            ->latest('posted_at')
            ->forPage($page, self::PAGE_SIZE)
            ->get();

        // Fallback: If no direct matches, show latest visible listings so the page is never empty
        if ($jobs->isEmpty()) {
            $jobs = JobListing::visible()->latest('posted_at')->take(self::PAGE_SIZE)->get();
        }

        $allCategories = collect(JobCategorySeo::all())->except($slug)->values()->all();

        $user = $request->user();
        $admin = (bool) $user?->isAdmin();
        $unlockedIds = $user && ! $admin
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => $admin
            || $job->origin === 'employer'
            || (bool) $unlockedIds?->has($job->id);

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $matchPercent = $profile
            ? fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description']))
            : fn () => null;

        return view('jobs.category', [
            'category' => $category,
            'jobs' => $jobs,
            'totalJobs' => $totalJobs,
            'totalKenyaFriendly' => $totalKenyaFriendly,
            'page' => $page,
            'totalPages' => $totalPages,
            'allCategories' => $allCategories,
            'isUnlocked' => $isUnlocked,
            'matchPercent' => $matchPercent,
        ]);
    }
}
