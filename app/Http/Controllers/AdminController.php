<?php

namespace App\Http\Controllers;

use App\Mail\FollowUpInvitationEmail;
use App\Mail\FreeTrialInvitationEmail;
use App\Models\BlogPost;
use App\Models\JobListing;
use App\Models\PageView;
use App\Models\Payment;
use App\Models\SyncMeta;
use App\Models\User;
use App\Services\BulkMailer;
use App\Services\CreditsService;
use App\Services\GoogleSearchConsoleService;
use App\Services\JobRecommendationService;
use App\Services\JobSyncService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    private const PAGE_SIZE = 25;

    public function dashboard(Request $request, GoogleSearchConsoleService $gsc): View
    {
        $filteredJobs = $this->getFilteredJobs($request);
        $now = now();

        $gscMetrics = null;
        if ($gsc->isConfigured()) {
            $gscMetrics = $gsc->queryAnalytics(
                null,
                now()->subDays(30)->format('Y-m-d'),
                now()->subDays(2)->format('Y-m-d'),
                ['query'],
                10
            );
        }

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
            'gscMetrics' => $gscMetrics,
            'gscConfigured' => $gsc->isConfigured(),
            'gscSiteUrl' => $gsc->getSiteUrl(),
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

    public function email(BulkMailer $mailer): View
    {
        $mailable = User::whereNull('marketing_opt_out_at');
        $subscribed = (clone $mailable)->where('subscribed', true)->count();
        $total = $mailable->count();

        $pendingUserIds = Payment::where('purpose', 'subscription')
            ->where('status', 'pending')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();
        $pending = (clone $mailable)->whereIn('id', $pendingUserIds)->count();

        $pendingFollowUp = (clone $mailable)
            ->where('subscribed', false)
            ->whereNull('follow_up_sent_at')
            ->where(function ($sub) {
                $sub->where('created_at', '<=', now()->subHours(24))
                    ->orWhere(function ($trialSub) {
                        $trialSub->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<=', now());
                    });
            })
            ->count();
        $followUpSent = (clone $mailable)->whereNotNull('follow_up_sent_at')->count();

        $digestEligible = (clone $mailable)
            ->where(function ($sub) {
                $sub->whereNull('last_job_digest_at')
                    ->orWhere('last_job_digest_at', '<=', now()->subHours(20));
            })
            ->count();
        $digestSentToday = (clone $mailable)
            ->whereNotNull('last_job_digest_at')
            ->where('last_job_digest_at', '>', now()->subHours(20))
            ->count();

        $pendingQueueJobs = DB::table('jobs')->count();

        return view('admin.email', [
            'counts' => [
                'all' => $total,
                'subscribed' => $subscribed,
                'free' => $total - $subscribed,
                'pending' => $pending,
                'pending_follow_up' => $pendingFollowUp,
                'follow_up_sent' => $followUpSent,
                'digest_eligible' => $digestEligible,
                'digest_sent_today' => $digestSentToday,
            ],
            'pendingQueueJobs' => $pendingQueueJobs,
            'configured' => $mailer->isConfigured(),
            'defaultTestEmail' => auth()->user()?->email ?? 'info@kenyaremotejobs.com',
        ]);
    }

    public function sendFreeTrialBroadcast(Request $request): RedirectResponse
    {
        $target = $request->input('target', 'free');
        $query = User::query()
            ->whereNull('marketing_opt_out_at')
            ->where('subscribed', false);

        if ($target === 'pending') {
            $pendingUserIds = Payment::query()
                ->where('purpose', 'subscription')
                ->where('status', 'pending')
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->all();

            $query->whereIn('id', $pendingUserIds);
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.email')->with('error', 'No eligible recipients found in this audience.');
        }

        $queued = 0;
        foreach ($recipients as $user) {
            Mail::to($user->email)->queue(new FreeTrialInvitationEmail($user));
            $queued++;
        }

        $targetLabel = $target === 'pending' ? 'pending checkout visitor(s)' : 'free registered user(s)';

        return redirect()->route('admin.email')->with('success', "✓ 24-Hour Free Trial campaign queued: {$queued} invitation(s) dispatched to {$targetLabel}. Background queue worker is delivering them now.");
    }

    public function sendFollowUpBroadcast(Request $request): RedirectResponse
    {
        $recipients = User::query()
            ->whereNull('marketing_opt_out_at')
            ->where('subscribed', false)
            ->whereNull('follow_up_sent_at')
            ->where(function ($sub) {
                $sub->where('created_at', '<=', now()->subHours(24))
                    ->orWhere(function ($trialSub) {
                        $trialSub->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<=', now());
                    });
            })
            ->get();

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.email')->with('error', 'No eligible users pending follow-up at this time.');
        }

        $queued = 0;
        foreach ($recipients as $user) {
            Mail::to($user->email)->queue(new FollowUpInvitationEmail($user));
            $user->forceFill(['follow_up_sent_at' => now()])->saveQuietly();
            $queued++;
        }

        return redirect()->route('admin.email')->with('success', "✓ Follow-up campaign queued: {$queued} email(s) dispatched to eligible users.");
    }

    public function sendDigestBroadcast(Request $request, JobRecommendationService $recommendationService): RedirectResponse
    {
        $isForce = (bool) $request->boolean('force', false);

        $query = User::query()
            ->whereNull('marketing_opt_out_at')
            ->when(! $isForce, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('last_job_digest_at')
                        ->orWhere('last_job_digest_at', '<=', now()->subHours(20));
                });
            });

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.email')->with('error', "No eligible recipients found for the Job Matches Digest (all active users have already received today's digest). Pass force=1 if you wish to override the daily limit.");
        }

        $queued = 0;
        foreach ($recipients as $user) {
            $ok = $recommendationService->sendDigestToUser($user, force: $isForce);
            if ($ok) {
                $queued++;
            }
        }

        return redirect()->route('admin.email')->with('success', "✓ Daily Job Matches Digest campaign queued: {$queued} personalized digest(s) dispatched to users.");
    }

    public function syncJobsRealtime(JobSyncService $syncService): RedirectResponse
    {
        try {
            $count = $syncService->sync(notifyPro: true);

            if ($count === 0) {
                return back()->with('error', 'Sync skipped — every upstream source failed or returned nothing. Existing listings preserved.');
            }

            return back()->with('success', "✓ Real-time sync complete: {$count} jobs synchronized and scored. Real-time Pro alerts processed.");
        } catch (Throwable $e) {
            return back()->with('error', 'Real-time job sync failed: '.$e->getMessage());
        }
    }

    public function sendProBroadcast(Request $request, JobRecommendationService $recommendationService): RedirectResponse
    {
        $sent = $recommendationService->sendRealtimeAlertsToSubscribers(immediate: false);

        if ($sent === 0) {
            return redirect()->route('admin.email')->with('error', 'No active Pro subscribers found to receive real-time alerts at this time.');
        }

        return redirect()->route('admin.email')->with('success', "✓ VIP Pro real-time recommendations campaign queued: {$sent} email(s) dispatched to paying subscribers.");
    }

    public function sendTestEmail(Request $request, BulkMailer $mailer, JobRecommendationService $recommendationService): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
            'test_type' => 'required|in:trial,follow_up,digest,pro,custom',
            'custom_subject' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string',
        ]);

        $email = $validated['test_email'];
        $type = $validated['test_type'];

        $user = User::where('email', $email)->first() ?? new User([
            'name' => 'Admin Preview',
            'email' => $email,
        ]);

        if (! $user->exists) {
            $user->id = 1;
        }

        try {
            if ($type === 'trial') {
                Mail::to($email)->send(new FreeTrialInvitationEmail($user));

                return redirect()->route('admin.email')->with('success', "✓ 24-Hour Free Trial invitation sample sent immediately to {$email}!");
            }

            if ($type === 'follow_up') {
                Mail::to($email)->send(new FollowUpInvitationEmail($user));

                return redirect()->route('admin.email')->with('success', "✓ Follow-Up ('Still thinking about finding a remote job?') sample sent immediately to {$email}!");
            }

            if ($type === 'digest') {
                $ok = $recommendationService->sendDigestToUser($user, force: true, immediate: true);
                if ($ok) {
                    return redirect()->route('admin.email')->with('success', "✓ Curated Job Matches Digest sample sent immediately to {$email}!");
                }

                return redirect()->route('admin.email')->with('error', "Could not dispatch digest to {$email}. Ensure active job listings exist.");
            }

            if ($type === 'pro') {
                $user->subscribed = true;
                $ok = $recommendationService->sendProRecommendationsToUser($user, force: true, immediate: true);
                if ($ok) {
                    return redirect()->route('admin.email')->with('success', "✓ VIP Pro Real-Time Matches sample (+ Concierge Offers) sent immediately to {$email}!");
                }

                return redirect()->route('admin.email')->with('error', "Could not dispatch Pro matches to {$email}. Ensure active job listings exist.");
            }

            if ($type === 'custom') {
                $subject = $validated['custom_subject'] ?: 'KenyaRemoteJobs Test Notification';
                $message = $validated['custom_message'] ?: "This is a deliverability test from KenyaRemoteJobs confirming that email delivery is working properly.\n\nSent at: ".now()->toDayDateTimeString();
                $res = $mailer->sendTest($email, $subject, $message);
                if ($res['success']) {
                    return redirect()->route('admin.email')->with('success', "✓ Test email delivered to {$email}!");
                }

                return redirect()->route('admin.email')->with('error', $res['message']);
            }
        } catch (Throwable $e) {
            return redirect()->route('admin.email')->with('error', 'Test dispatch failed: '.$e->getMessage());
        }

        return redirect()->route('admin.email');
    }

    public function sendCustomBroadcast(Request $request, BulkMailer $mailer): RedirectResponse
    {
        $validated = $request->validate([
            'audience' => 'required|in:all,subscribed,free',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $audience = $validated['audience'];
        $recipients = User::query()
            ->when($audience !== 'all', fn ($q) => $q->where('subscribed', $audience === 'subscribed'))
            ->whereNull('marketing_opt_out_at')
            ->get();

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.email')->with('error', 'No recipients match the selected audience.');
        }

        try {
            $result = $mailer->sendBulk($recipients, $validated['subject'], $validated['message']);

            $failedCount = count($result['failed']);
            $msg = "✓ Broadcast queued: {$result['sent']} email(s) dispatched to the delivery queue.";
            if ($failedCount > 0) {
                $msg .= " ({$failedCount} failed)";
            }

            return redirect()->route('admin.email')->with('success', $msg);
        } catch (Throwable $e) {
            return redirect()->route('admin.email')->with('error', 'Broadcast failed: '.$e->getMessage());
        }
    }
}
