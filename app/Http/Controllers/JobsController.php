<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Services\CreditsService;
use App\Support\Audience;
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

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $sortByMatch = $request->query('sort') === 'match' && $profile !== null;

        $query = JobListing::query();

        $totalKenyaFriendly = (clone $query)->where('kenya_friendly', true)->count();
        $totalFree = (clone $query)
            ->where(fn ($w) => $w->where('origin', 'employer')
                ->orWhere('posted_at', '<=', now()->subDays(config('jobs.premium_window_days'))))
            ->count();

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
            $query->where(fn ($w) => $w->where('origin', 'employer')
                ->orWhere('posted_at', '<=', now()->subDays(config('jobs.premium_window_days'))));
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
        $admin = (bool) $user?->isAdmin();
        $unlockedIds = $user && ! $admin
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => $admin
            || $job->origin === 'employer'
            || $job->is_free
            || (bool) $unlockedIds?->has($job->id);

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
        $job = JobListing::findOrFail($id);

        $user = $request->user();
        $admin = (bool) $user?->isAdmin();
        $unlocked = $admin
            || $job->origin === 'employer'
            || (bool) $user?->subscribed
            || $job->is_free
            || (bool) ($user && $user->jobUnlocks()->where('job_listing_id', $job->id)->exists());

        $remainingCredits = $user && ! $unlocked ? $credits->balances($user)[$job->tier]['remaining'] : 0;

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $matchPercent = $profile ? Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description'])) : null;

        return view('jobs.show', [
            'job' => $job,
            'unlocked' => $unlocked,
            'remainingCredits' => $remainingCredits,
            'matchPercent' => $matchPercent,
        ]);
    }
}
