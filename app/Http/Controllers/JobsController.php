<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Services\CreditsService;
use App\Support\Audience;
use App\Support\JobCategorySeo;
use App\Support\Matching;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tag = trim((string) $request->query('tag', ''));
        $remoteType = trim((string) $request->query('remoteType', ''));
        $kenyaFriendly = $request->query('kenyaFriendly') === 'true';
        $freeOnly = $request->query('freeOnly') === 'true';
        $audience = in_array($request->query('audience'), Audience::ORDER, true)
            ? $request->query('audience')
            : null;
        $page = max(1, (int) $request->query('page', 1));

        // 301 permanent redirect category queries to their dedicated programmatic SEO landing page
        // (Transfers search impressions, resolves canonical clash, and boosts organic rankings)
        if ($q !== '' && $tag === '' && $remoteType === '' && ! $kenyaFriendly && ! $freeOnly && ! $audience && $page === 1) {
            $matchedCategorySlug = JobCategorySeo::findSlugForQuery($q);
            if ($matchedCategorySlug) {
                return redirect()->route('jobs.category', ['slug' => $matchedCategorySlug], 301);
            }
        }

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $sortByMatch = $request->query('sort') === 'match' && $profile !== null;

        $query = JobListing::visible();

        $totalKenyaFriendly = (clone $query)->where('kenya_friendly', true)->count();
        $totalFree = (clone $query)->where('origin', 'employer')->count();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('tags', 'like', "%{$q}%");
            });
        }

        if ($tag !== '') {
            $query->whereJsonContains('tags', $tag);
        }

        if ($remoteType !== '') {
            $query->where('remote_type', 'like', "%{$remoteType}%");
        }

        if ($kenyaFriendly) {
            $query->where('kenya_friendly', true);
        }

        if ($freeOnly) {
            $query->where('origin', 'employer');
        }

        if ($audience) {
            $query->whereJsonContains('audience_segments', $audience);
        }

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $page = min($page, $totalPages);

        if ($sortByMatch) {
            // A "best for you" ranking has to happen across the whole
            // filtered catalog, not just within whatever page would've been
            // shown by date — so this sorts in memory before slicing,
            // rather than paginating in the database first.
            $jobs = $query->get()
                ->sortByDesc(fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])))
                ->slice(($page - 1) * self::PAGE_SIZE, self::PAGE_SIZE)
                ->values();
        } else {
            $jobs = $query->latest('posted_at')
                ->forPage($page, self::PAGE_SIZE)
                ->get();
        }

        $tags = JobListing::pluck('tags')
            ->flatten()
            ->filter(fn ($t) => trim((string) $t) !== '')
            ->map(fn ($t) => trim($t))
            ->unique()
            ->sort()
            ->values();

        $user = $request->user();
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

        return view('jobs.index', [
            'jobs' => $jobs,
            'tags' => $tags,
            'total' => $total,
            'totalKenyaFriendly' => $totalKenyaFriendly,
            'totalFree' => $totalFree,
            'page' => $page,
            'totalPages' => $totalPages,
            'q' => $q,
            'tag' => $tag,
            'remoteType' => $remoteType,
            'kenyaFriendly' => $kenyaFriendly,
            'freeOnly' => $freeOnly,
            'audience' => $audience,
            'sortByMatch' => $sortByMatch,
            'hasProfile' => (bool) $profile,
            'isUnlocked' => $isUnlocked,
            'matchPercent' => $matchPercent,
        ]);
    }

    public function show(Request $request, string $id, CreditsService $credits)
    {
        $user = $request->user();
        $admin = (bool) $user?->isAdmin();

        $job = JobListing::find($id);

        if (! $job) {
            $activeJobs = JobListing::visible()->latest('posted_at')->take(6)->get();

            return response()->view('jobs.expired', [
                'activeJobs' => $activeJobs,
                'requestedId' => $id,
            ], 410);
        }

        $listingLifetimeDays = (int) config('jobs.listing_days', 30);
        $isExpired = ! $admin && $job->posted_at && $job->posted_at->isBefore(now()->subDays($listingLifetimeDays));

        $isEarlyAccess = ! $isExpired && $job->isEarlyAccess();
        $isEmployerDirect = $job->origin === 'employer';
        $canApply = ! $isExpired && (bool) (
            $user?->hasActiveAccess()
            || ($user && $user->jobUnlocks()->where('job_listing_id', $job->id)->exists())
        );

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $matchPercent = $profile ? Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])) : null;

        $firstTag = is_array($job->tags) && ! empty($job->tags) ? $job->tags[0] : null;
        $relatedQuery = JobListing::visible()->where('id', '!=', $job->id);

        $relatedJobs = $firstTag
            ? (clone $relatedQuery)->whereJsonContains('tags', $firstTag)->latest('posted_at')->take(3)->get()
            : collect();

        if ($relatedJobs->isEmpty()) {
            $relatedJobs = (clone $relatedQuery)->latest('posted_at')->take(3)->get();
        }

        $unlockedIds = $user && ! $user->hasActiveAccess()
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $j) => (bool) (
            $user?->hasActiveAccess()
            || ($unlockedIds && $unlockedIds->has($j->id))
        );

        return view('jobs.show', [
            'job' => $job,
            'canApply' => $canApply,
            'isEarlyAccess' => $isEarlyAccess,
            'isExpired' => $isExpired,
            'matchPercent' => $matchPercent,
            'relatedJobs' => $relatedJobs,
            'isUnlocked' => $isUnlocked,
        ]);
    }
}
