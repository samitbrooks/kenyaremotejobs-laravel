<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\JobCollectionSeo;
use App\Support\Matching;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = collect(JobCollectionSeo::all())->map(function (array $collection) {
            $count = $this->queryForFilter($collection['filter_type'])->count();
            $collection['jobs_count'] = $count;

            return $collection;
        })->values()->all();

        return view('collections.index', [
            'collections' => $collections,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $collection = JobCollectionSeo::find($slug);

        abort_if(! $collection, 404);

        $query = $this->queryForFilter($collection['filter_type']);
        $jobs = (clone $query)->latest('posted_at')->take(18)->get();

        // If fewer than 6 listings match the specific criteria, backfill with top Kenya-friendly remote listings
        if ($jobs->count() < 6) {
            $existingIds = $jobs->pluck('id')->all();
            $backfill = JobListing::visible()
                ->where('kenya_friendly', true)
                ->whereNotIn('id', $existingIds)
                ->latest('posted_at')
                ->take(18 - $jobs->count())
                ->get();
            $jobs = $jobs->merge($backfill);
        }

        $otherCollections = collect(JobCollectionSeo::all())
            ->filter(fn (array $c) => $c['slug'] !== $slug)
            ->take(4)
            ->values()
            ->all();

        $profile = Matching::parseProfileCookie($request->cookie(Matching::COOKIE_NAME));
        $matchPercent = $profile
            ? fn (JobListing $job) => Matching::computeMatchPercent($profile, $job->only(['tags', 'title', 'description']))
            : fn () => null;

        return view('collections.show', [
            'collection' => $collection,
            'jobs' => $jobs,
            'otherCollections' => $otherCollections,
            'matchPercent' => $matchPercent,
        ]);
    }

    protected function queryForFilter(string $filterType): Builder
    {
        $base = JobListing::visible();

        return match ($filterType) {
            'payment_rails' => (clone $base)
                ->where('kenya_friendly', true)
                ->where(function ($q) {
                    $q->whereNotNull('salary')
                        ->orWhereNotNull('annual_salary_usd')
                        ->orWhere('location', 'like', '%worldwide%')
                        ->orWhere('location', 'like', '%anywhere%');
                }),

            'zero_visa' => (clone $base)
                ->where('kenya_friendly', true)
                ->where(function ($q) {
                    $q->where('location', 'like', '%worldwide%')
                        ->orWhere('location', 'like', '%anywhere%')
                        ->orWhere('remote_type', 'like', '%fully%')
                        ->orWhere('location', 'like', '%kenya%');
                }),

            'entry_level' => (clone $base)
                ->where(function ($q) {
                    $q->where('title', 'like', '%junior%')
                        ->orWhere('title', 'like', '%entry%')
                        ->orWhere('title', 'like', '%assistant%')
                        ->orWhere('title', 'like', '%support%')
                        ->orWhere('title', 'like', '%coordinator%')
                        ->orWhere('title', 'like', '%associate%')
                        ->orWhere('title', 'like', '%intern%')
                        ->orWhere('title', 'like', '%moderator%')
                        ->orWhere('title', 'like', '%analyst%')
                        ->orWhere('tags', 'like', '%support%')
                        ->orWhere('tags', 'like', '%writing%');
                }),

            'high_salary' => (clone $base)
                ->where(function ($q) {
                    $q->whereNotNull('annual_salary_usd')
                        ->orWhere('title', 'like', '%senior%')
                        ->orWhere('title', 'like', '%lead%')
                        ->orWhere('title', 'like', '%principal%')
                        ->orWhere('title', 'like', '%manager%')
                        ->orWhere('title', 'like', '%architect%')
                        ->orWhere('title', 'like', '%staff%');
                }),

            'eat_aligned' => (clone $base)
                ->where(function ($q) {
                    $q->where('location', 'like', '%europe%')
                        ->orWhere('location', 'like', '%emea%')
                        ->orWhere('location', 'like', '%uk%')
                        ->orWhere('location', 'like', '%africa%')
                        ->orWhere('location', 'like', '%worldwide%')
                        ->orWhere('location', 'like', '%anywhere%')
                        ->orWhere('kenya_friendly', true);
                }),

            default => (clone $base)->where('kenya_friendly', true),
        };
    }
}
