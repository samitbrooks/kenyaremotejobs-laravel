<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Support\CompanyDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = collect(CompanyDirectory::all())->map(function (array $company) {
            $terms = $company['search_terms'];
            $openJobsCount = JobListing::visible()
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('company', 'like', "%{$term}%");
                    }
                })
                ->count();

            $company['open_jobs_count'] = $openJobsCount;

            return $company;
        })->values()->all();

        return view('companies.index', [
            'companies' => $companies,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $company = CompanyDirectory::find($slug);

        abort_if(! $company, 404);

        $user = $request->user();
        $admin = (bool) $user?->isAdmin();
        $hasActiveAccess = (bool) $user?->hasActiveAccess();
        $unlockedIds = $user && ! $admin && ! $hasActiveAccess
            ? $user->jobUnlocks()->pluck('job_listing_id')->flip()
            : null;

        $isUnlocked = fn (JobListing $job) => $admin
            || $hasActiveAccess
            || (bool) $unlockedIds?->has($job->id);

        $terms = $company['search_terms'];
        $jobs = JobListing::visible()
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('company', 'like', "%{$term}%");
                }
            })
            ->latest('posted_at')
            ->take(12)
            ->get();

        $otherCompanies = collect(CompanyDirectory::all())
            ->filter(fn (array $c) => $c['slug'] !== $slug)
            ->take(3)
            ->values()
            ->all();

        return view('companies.show', [
            'company' => $company,
            'jobs' => $jobs,
            'isUnlocked' => $isUnlocked,
            'otherCompanies' => $otherCompanies,
        ]);
    }
}
