<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Models\PageView;
use App\Models\SyncMeta;
use App\Models\User;
use App\Services\BulkMailer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private const PAGE_SIZE = 25;

    public function dashboard()
    {
        $bySource = JobListing::selectRaw('source_name, count(*) as c')
            ->groupBy('source_name')
            ->orderByDesc('c')
            ->pluck('c', 'source_name');

        $now = now();

        return view('admin.dashboard', [
            'totalJobs' => JobListing::count(),
            'kenyaFriendly' => JobListing::where('kenya_friendly', true)->count(),
            'totalUsers' => User::count(),
            'subscribed' => User::where('subscribed', true)->count(),
            'lastSyncedAt' => SyncMeta::find(1)?->last_synced_at,
            'bySource' => $bySource,
            'pageViews' => [
                'total' => PageView::count(),
                'last7Days' => PageView::where('created_at', '>=', $now->copy()->subDays(7))->count(),
                'last30Days' => PageView::where('created_at', '>=', $now->copy()->subDays(30))->count(),
            ],
        ]);
    }

    public function jobs(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));

        $query = JobListing::query();
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('tags', 'like', "%{$q}%");
            });
        }

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $page = min($page, $totalPages);

        $jobs = $query->latest('posted_at')->forPage($page, self::PAGE_SIZE)->get();

        return view('admin.jobs', [
            'jobs' => $jobs,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'q' => $q,
        ]);
    }

    public function users()
    {
        return view('admin.users', ['users' => User::latest()->get()]);
    }

    public function blog()
    {
        return view('admin.blog', ['posts' => BlogPost::latest()->get()]);
    }

    public function email(BulkMailer $mailer)
    {
        $subscribed = User::where('subscribed', true)->count();
        $total = User::count();

        return view('admin.email', [
            'counts' => ['all' => $total, 'subscribed' => $subscribed, 'free' => $total - $subscribed],
            'configured' => $mailer->isConfigured(),
        ]);
    }
}
