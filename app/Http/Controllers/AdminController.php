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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const PAGE_SIZE = 25;

    public function dashboard(Request $request): View
    {
        $filteredJobs = $this->getFilteredJobs($request);
        $now = now();

        return view('admin.dashboard', [
            ...$filteredJobs,
            'totalJobs' => JobListing::count(),
            'kenyaFriendlyTotal' => JobListing::where('kenya_friendly', true)->count(),
            'totalUsers' => User::count(),
            'subscribed' => User::where('subscribed', true)->count(),
            'lastSyncedAt' => SyncMeta::find(1)?->last_synced_at,
            'bySource' => $filteredJobs['sources'],
            'pageViews' => [
                'total' => PageView::count(),
                'last7Days' => PageView::where('created_at', '>=', $now->copy()->subDays(7))->count(),
                'last30Days' => PageView::where('created_at', '>=', $now->copy()->subDays(30))->count(),
            ],
        ]);
    }

    public function jobs(Request $request): View
    {
        $filteredJobs = $this->getFilteredJobs($request);

        return view('admin.jobs', $filteredJobs);
    }

    /**
     * @return array{
     *     jobs: Collection<int, JobListing>,
     *     total: int,
     *     page: int,
     *     totalPages: int,
     *     q: string,
     *     source: string,
     *     order: string,
     *     kenyaFriendly: bool,
     *     sources: array<string, int>,
     * }
     */
    private function getFilteredJobs(Request $request): array
    {
        $q = trim((string) $request->query('q', ''));
        $source = trim((string) $request->query('source', ''));
        $order = trim((string) $request->query('order', 'newest'));
        $kenyaFriendly = $request->query('kenya_friendly') === 'true' || $request->query('kenya_friendly') === '1';
        $page = max(1, (int) $request->query('page', 1));

        $query = JobListing::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('tags', 'like', "%{$q}%");
            });
        }

        if ($source !== '') {
            $query->where('source_name', $source);
        }

        if ($kenyaFriendly) {
            $query->where('kenya_friendly', true);
        }

        match ($order) {
            'oldest' => $query->orderBy('posted_at', 'asc'),
            'recent_sync' => $query->orderByDesc('updated_at'),
            'title' => $query->orderBy('title', 'asc'),
            default => $query->orderByDesc('posted_at'),
        };

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $page = min($page, $totalPages);

        $jobs = $query->forPage($page, self::PAGE_SIZE)->get();

        $bySource = JobListing::selectRaw('source_name, count(*) as c')
            ->groupBy('source_name')
            ->orderByDesc('c')
            ->pluck('c', 'source_name')
            ->all();

        return [
            'jobs' => $jobs,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'q' => $q,
            'source' => $source,
            'order' => $order,
            'kenyaFriendly' => $kenyaFriendly,
            'sources' => $bySource,
        ];
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
