<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Models\PageView;
use App\Models\Payment;
use App\Models\SyncMeta;
use App\Models\User;
use App\Services\BulkMailer;
use App\Services\CreditsService;
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

    public function jobEdit(JobListing $job)
    {
        return view('admin.job-edit', ['job' => $job]);
    }

    public function users()
    {
        return view('admin.users', ['users' => User::latest()->get()]);
    }

    public function userShow(User $user, CreditsService $credits)
    {
        return view('admin.user-show', [
            'user' => $user,
            'balances' => $credits->balances($user),
            'purchases' => $user->creditPurchases()->latest('purchased_at')->get(),
            'unlocks' => $user->jobUnlocks()->with('jobListing')->latest('unlocked_at')->get(),
            'postedJobs' => $user->postedJobs()->latest('posted_at')->get(),
        ]);
    }

    public function blog()
    {
        return view('admin.blog', ['posts' => BlogPost::latest()->get()]);
    }

    public function blogEdit(BlogPost $post)
    {
        return view('admin.blog-edit', ['post' => $post]);
    }

    public function payments()
    {
        return view('admin.payments', [
            'payments' => Payment::with('user')->latest()->paginate(self::PAGE_SIZE),
        ]);
    }

    public function email(BulkMailer $mailer)
    {
        // Opted-out users are excluded from every count here so the numbers
        // shown match what App\Services\BulkMailer will actually send —
        // see the same whereNull('marketing_opt_out_at') filter in
        // resources/views/components/⚡admin-email-composer.blade.php.
        $mailable = User::whereNull('marketing_opt_out_at');
        $subscribed = (clone $mailable)->where('subscribed', true)->count();
        $total = $mailable->count();

        return view('admin.email', [
            'counts' => ['all' => $total, 'subscribed' => $subscribed, 'free' => $total - $subscribed],
            'configured' => $mailer->isConfigured(),
        ]);
    }
}
