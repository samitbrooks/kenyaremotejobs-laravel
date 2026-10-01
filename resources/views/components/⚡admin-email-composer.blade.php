<?php

use App\Livewire\Concerns\RequiresAdmin;
use App\Mail\FollowUpInvitationEmail;
use App\Mail\FreeTrialInvitationEmail;
use App\Models\Payment;
use App\Models\User;
use App\Services\BulkMailer;
use App\Services\JobRecommendationService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

new class extends Component
{
    use RequiresAdmin;

    public array $counts;

    public bool $configured;

    public string $audience = 'all';

    public string $subject = '';

    public string $message = '';

    public string $testEmail = '';

    public ?string $result = null;

    public ?string $error = null;

    public ?string $testResult = null;

    public ?string $testError = null;

    public function mount(array $counts, bool $configured): void
    {
        $this->counts = $counts;
        $this->configured = $configured;
        $this->testEmail = auth()->user()?->email ?? '';
    }

    public function selectAudience(string $audience): void
    {
        $this->audience = $audience;
    }

    public function sendTestEmail(BulkMailer $mailer): void
    {
        $this->testResult = null;
        $this->testError = null;

        if (! filter_var($this->testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->testError = 'Please enter a valid email address for the test.';

            return;
        }

        $subject = $this->subject ?: 'KenyaRemoteJobs Test Notification';
        $body = $this->message ?: "This is a test notification confirming that email delivery from KenyaRemoteJobs is working properly.\n\nSent at: ".now()->toDayDateTimeString();

        try {
            $res = $mailer->sendTest($this->testEmail, $subject, $body);
            if ($res['success']) {
                $this->testResult = $res['message'];
            } else {
                $this->testError = $res['message'];
            }
        } catch (\Throwable $e) {
            $this->testError = $e->getMessage();
        }
    }

    public function sendDigestTest(JobRecommendationService $service): void
    {
        $this->testResult = null;
        $this->testError = null;

        if (! filter_var($this->testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->testError = 'Please enter a valid email address for the test.';

            return;
        }

        try {
            $user = User::where('email', $this->testEmail)->first() ?? new User([
                'name' => 'Digest Tester',
                'email' => $this->testEmail,
            ]);

            $success = $service->sendDigestToUser($user, force: true);
            if ($success) {
                $this->testResult = "Curated Job Matches Digest sample dispatched to {$this->testEmail}!";
            } else {
                $this->testError = "Could not send digest to {$this->testEmail}. Verify that active jobs exist in the database.";
            }
        } catch (\Throwable $e) {
            $this->testError = 'Failed to dispatch digest: '.$e->getMessage();
        }
    }

    public function sendFreeTrialTest(): void
    {
        $this->testResult = null;
        $this->testError = null;

        if (! filter_var($this->testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->testError = 'Please enter a valid email address for the test.';

            return;
        }

        try {
            $user = User::where('email', $this->testEmail)->first() ?? new User([
                'name' => 'Trial Tester',
                'email' => $this->testEmail,
            ]);

            if (! $user->exists) {
                $user->id = 1;
            }

            Mail::to($this->testEmail)->send(new FreeTrialInvitationEmail($user));
            $this->testResult = "FlexJobs-style 24-Hour Free Trial invitation sample sent to {$this->testEmail}!";
        } catch (\Throwable $e) {
            $this->testError = 'Failed to dispatch free trial test: '.$e->getMessage();
        }
    }

    public function sendFollowUpTest(): void
    {
        $this->testResult = null;
        $this->testError = null;

        if (! filter_var($this->testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->testError = 'Please enter a valid email address for the test.';

            return;
        }

        try {
            $user = User::where('email', $this->testEmail)->first() ?? new User([
                'name' => 'Follow-Up Tester',
                'email' => $this->testEmail,
            ]);

            if (! $user->exists) {
                $user->id = 1;
            }

            Mail::to($this->testEmail)->send(new FollowUpInvitationEmail($user));
            $this->testResult = "FlexJobs-style Follow-Up ('Still thinking about finding a remote job?') sample sent to {$this->testEmail}!";
        } catch (\Throwable $e) {
            $this->testError = 'Failed to dispatch follow-up test: '.$e->getMessage();
        }
    }

    public function broadcastFollowUp(): void
    {
        $this->result = null;
        $this->error = null;

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
            $this->error = 'No eligible users pending follow-up at this time.';

            return;
        }

        $queued = 0;
        $failed = 0;

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->queue(new FollowUpInvitationEmail($user));
                $user->forceFill(['follow_up_sent_at' => now()])->save();
                $queued++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true, '--max-time' => 10]);
        } catch (\Throwable) {
        }

        $this->result = "✓ Follow-up campaign queued: {$queued} email(s) dispatched to the delivery queue with zero timeout.";
    }

    public function broadcastFreeTrial(string $target = 'pending'): void
    {
        $this->result = null;
        $this->error = null;

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
            $this->error = 'No eligible recipients found in this audience.';

            return;
        }

        $queued = 0;
        $failed = 0;

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->queue(new FreeTrialInvitationEmail($user));
                $queued++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        // Run a safe 10-second in-process queue burst to begin delivery immediately without Cloudflare HTTP timeouts
        try {
            \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true, '--max-time' => 10]);
        } catch (\Throwable) {
            // Background cron scheduler continues automatically
        }

        $this->result = "✓ Free trial campaign queued: {$queued} email(s) dispatched to the delivery queue with zero timeout.";
    }

    public function send(BulkMailer $mailer): void
    {
        $this->result = null;
        $this->error = null;

        if (! $this->subject || ! $this->message) {
            $this->error = 'Subject and message are required.';

            return;
        }

        $recipients = User::query()
            ->when($this->audience !== 'all', fn ($q) => $q->where('subscribed', $this->audience === 'subscribed'))
            ->whereNull('marketing_opt_out_at')
            ->get();

        if ($recipients->isEmpty()) {
            $this->error = 'No recipients currently match that audience in the database.';

            return;
        }

        try {
            $result = $mailer->sendBulk($recipients, $this->subject, $this->message);
            $this->result = "Sent to {$result['sent']} recipient(s)."
                .(count($result['failed']) ? ' Failed: '.implode('; ', $result['failed']) : '');
            $this->subject = '';
            $this->message = '';
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }
};
?>

<div class="space-y-6">
    {{-- Status Banner --}}
    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-foreground/50">Email Transport</span>
                <p class="mt-0.5 text-sm font-semibold text-foreground">
                    Driver: <span class="rounded-md bg-horizon-100 px-2 py-0.5 font-mono text-xs text-horizon-800">{{ config('mail.default') }}</span>
                    &middot;
                    From: <span class="font-normal text-foreground/70">{{ config('mail.from.address') }}</span>
                </p>
            </div>
            @if ($configured)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span> Ready to send
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                    <span class="h-2 w-2 rounded-full bg-amber-600"></span> Setup recommended
                </span>
            @endif
        </div>

        @if (! $configured)
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-900 leading-relaxed">
                SMTP credentials not detected in <code>.env</code>. On cPanel hosting, you can set <code>MAIL_MAILER=sendmail</code> or enter your cPanel webmail SMTP details (<code>MAIL_HOST</code>, <code>MAIL_PORT=465</code>, <code>MAIL_USERNAME</code>, <code>MAIL_PASSWORD</code>).
            </div>
        @endif
    </div>

    {{-- Instant Test Email Box --}}
    <div class="rounded-2xl border border-horizon-200 bg-gradient-to-r from-horizon-50/80 via-white to-orange-50/40 p-5 shadow-sm">
        <h2 class="text-base font-bold text-horizon-950 flex items-center gap-2">
            <x-icon name="sparkle" class="h-4 w-4 text-sunrise-600" />
            Send a Test Email
        </h2>
        <p class="mt-1 text-xs text-foreground/60">
            Verify deliverability to your personal inbox before broadcasting to registered users.
        </p>

        <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 max-w-3xl">
            <input
                type="email"
                wire:model="testEmail"
                placeholder="Enter your email address…"
                class="flex-1 rounded-xl border border-black/10 bg-white px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            />
            <button
                type="button"
                wire:click="sendFreeTrialTest"
                wire:loading.attr="disabled"
                wire:target="sendFreeTrialTest"
                class="btn-pop shrink-0 rounded-xl bg-gradient-to-r from-orange-600 to-rose-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:from-orange-700 hover:to-rose-700 transition disabled:opacity-60"
                title="Send FlexJobs-style 24-hour free trial sample"
            >
                <span wire:loading.remove wire:target="sendFreeTrialTest">Trial Invite</span>
                <span wire:loading wire:target="sendFreeTrialTest">Sending&hellip;</span>
            </button>
            <button
                type="button"
                wire:click="sendFollowUpTest"
                wire:loading.attr="disabled"
                wire:target="sendFollowUpTest"
                class="btn-pop shrink-0 rounded-xl bg-gradient-to-r from-teal-600 to-cyan-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:from-teal-700 hover:to-cyan-800 transition disabled:opacity-60"
                title="Send FlexJobs-style Follow-Up ('Still thinking about finding a remote job?')"
            >
                <span wire:loading.remove wire:target="sendFollowUpTest">Follow-Up Sample</span>
                <span wire:loading wire:target="sendFollowUpTest">Sending&hellip;</span>
            </button>
            <button
                type="button"
                wire:click="sendDigestTest"
                wire:loading.attr="disabled"
                wire:target="sendDigestTest"
                class="btn-pop shrink-0 rounded-xl bg-orange-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-orange-700 transition disabled:opacity-60"
                title="Send a sample dark-mode Job Matches Digest email"
            >
                <span wire:loading.remove wire:target="sendDigestTest">Matches Digest</span>
                <span wire:loading wire:target="sendDigestTest">Sending&hellip;</span>
            </button>
            <button
                type="button"
                wire:click="sendTestEmail"
                wire:loading.attr="disabled"
                wire:target="sendTestEmail"
                class="btn-pop shrink-0 rounded-xl bg-horizon-800 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-horizon-900 transition disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="sendTestEmail">Text Test</span>
                <span wire:loading wire:target="sendTestEmail">Sending&hellip;</span>
            </button>
        </div>

        @if ($testResult)
            <p class="mt-3 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg p-2.5">
                ✓ {{ $testResult }}
            </p>
        @endif
        @if ($testError)
            <p class="mt-3 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg p-2.5">
                ✕ {{ $testError }}
            </p>
        @endif
    </div>

    {{-- FlexJobs-Style 24-Hour Free Trial Invite Campaign Card --}}
    <div class="rounded-2xl border-2 border-orange-200 bg-gradient-to-br from-orange-50/70 via-white to-amber-50/50 p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-orange-500 text-white font-extrabold text-sm shadow-xs">
                    ⚡
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900">FlexJobs-Style 24-Hour Free Trial Campaign</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Invite registered users who haven't subscribed yet to enjoy a 24-hour free pass with a 1-click magic activation link.
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-orange-100 border border-orange-300 px-3 py-1 text-xs font-bold text-orange-800">
                1-Click Activation Link
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Target 1: Pending checkout users (the visitors who tried M-Pesa checkout) --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Checkout Drop-Offs</span>
                        <span class="rounded-full bg-rose-100 text-rose-800 font-extrabold text-xs px-2.5 py-0.5">
                            {{ $counts['pending'] ?? 0 }} visitors
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mt-1.5">Pending Subscription Visitors</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        These users reached the M-Pesa subscription checkout screen. Send them a complimentary 24h pass to win them back!
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="broadcastFreeTrial('pending')"
                        wire:confirm="Send 24-hour Free Trial invitations to {{ $counts['pending'] ?? 0 }} pending visitor(s)?"
                        wire:loading.attr="disabled"
                        wire:target="broadcastFreeTrial('pending')"
                        @if (($counts['pending'] ?? 0) === 0) disabled @endif
                        class="btn-pop w-full rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="broadcastFreeTrial('pending')">
                            Send Free Trial to {{ $counts['pending'] ?? 0 }} Pending Visitor(s)
                        </span>
                        <span wire:loading wire:target="broadcastFreeTrial('pending')">
                            Dispatching Free Trial Invites&hellip;
                        </span>
                    </button>
                </div>
            </div>

            {{-- Target 2: All free registered users --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">All Registered Accounts</span>
                        <span class="rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs px-2.5 py-0.5">
                            {{ $counts['free'] ?? 0 }} users
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mt-1.5">All Free Registered Users</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Every registered user in the database without an active subscription.
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="broadcastFreeTrial('free')"
                        wire:confirm="Send 24-hour Free Trial invitations to {{ $counts['free'] ?? 0 }} registered user(s)?"
                        wire:loading.attr="disabled"
                        wire:target="broadcastFreeTrial('free')"
                        @if (($counts['free'] ?? 0) === 0) disabled @endif
                        class="btn-pop w-full rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="broadcastFreeTrial('free')">
                            Send Free Trial to {{ $counts['free'] ?? 0 }} Registered User(s)
                        </span>
                        <span wire:loading wire:target="broadcastFreeTrial('free')">
                            Dispatching Free Trial Invites&hellip;
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- FlexJobs Follow-Up Campaign Card ("Still thinking about finding a remote job?") --}}
    <div class="rounded-2xl border-2 border-teal-200 bg-gradient-to-br from-teal-50/70 via-white to-cyan-50/50 p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-teal-600 text-white font-extrabold text-sm shadow-xs">
                    ✉
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900">FlexJobs-Style Follow-Up Campaign ("Still thinking about finding a remote job?")</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Automated follow-up for registered users whose trial ended or who registered &gt;24h ago. Highlights membership benefits, company placements, and success stories.
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-teal-100 border border-teal-300 px-3 py-1 text-xs font-bold text-teal-800">
                Scheduled Daily (09:00 EAT)
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Eligible Pending Follow-Up</span>
                        <span class="rounded-full bg-teal-100 text-teal-800 font-extrabold text-xs px-2.5 py-0.5">
                            {{ $counts['pending_follow_up'] ?? 0 }} users
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mt-1.5">Users Ready for Follow-Up</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Registered users without active subscription who have not received this follow-up yet and are past the 24h window.
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="broadcastFollowUp"
                        wire:confirm="Send Follow-Up ('Still thinking about finding a remote job?') to {{ $counts['pending_follow_up'] ?? 0 }} eligible user(s)?"
                        wire:loading.attr="disabled"
                        wire:target="broadcastFollowUp"
                        @if (($counts['pending_follow_up'] ?? 0) === 0) disabled @endif
                        class="btn-pop w-full rounded-xl bg-teal-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-teal-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="broadcastFollowUp">
                            Queue Follow-Up to {{ $counts['pending_follow_up'] ?? 0 }} Eligible User(s)
                        </span>
                        <span wire:loading wire:target="broadcastFollowUp">
                            Queueing Follow-Up Emails&hellip;
                        </span>
                    </button>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Campaign History</span>
                        <span class="rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs px-2.5 py-0.5">
                            {{ $counts['follow_up_sent'] ?? 0 }} delivered
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mt-1.5">Automated Daily Lifecycle</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        The cron schedule runs <code>email:send-follow-ups</code> automatically every day at 09:00 EAT (06:00 UTC) so each user receives this follow-up once.
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Cron status: <strong class="text-emerald-700 font-bold">Active in routes/console.php</strong></span>
                    <button
                        type="button"
                        wire:click="sendFollowUpTest"
                        wire:loading.attr="disabled"
                        wire:target="sendFollowUpTest"
                        class="text-teal-700 hover:text-teal-900 underline font-semibold"
                    >
                        Preview to admin
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Broadcast Composer Form --}}
    <form wire:submit="send" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-foreground">Compose Broadcast</h2>
        <p class="mt-0.5 text-xs text-foreground/50">Send an announcement or newsletter to your user base.</p>

        <div class="mt-5">
            <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Select Audience</label>
            <select wire:model.live="audience" class="mt-1.5 w-full max-w-sm rounded-xl border border-black/10 px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                <option value="all">Everyone with an account ({{ $counts['all'] }} mailable)</option>
                <option value="subscribed">Full-access accounts only ({{ $counts['subscribed'] }} mailable)</option>
                <option value="free">Pay-per-job accounts only ({{ $counts['free'] }} mailable)</option>
            </select>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Subject Line</label>
            <input
                type="text"
                wire:model="subject"
                required
                placeholder="e.g. 15 New Remote Roles Open to East Africa This Week"
                class="mt-1.5 w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            />
        </div>

        <div class="mt-4">
            <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Message Body</label>
            <textarea
                wire:model="message"
                required
                rows="9"
                placeholder="Write your email content here… (Unsubscribe link is appended automatically to comply with international regulations)"
                class="mt-1.5 w-full rounded-xl border border-black/10 p-3.5 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            ></textarea>
        </div>

        @if ($result)
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-semibold text-emerald-800">
                ✓ {{ $result }}
            </div>
        @endif
        @if ($error)
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-semibold text-red-800">
                ✕ {{ $error }}
            </div>
        @endif

        @php
            $targetCount = $counts[$audience] ?? 0;
        @endphp

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button
                type="submit"
                @if ($targetCount > 0)
                    wire:confirm="Are you sure you want to broadcast this message to {{ $targetCount }} recipient(s)?"
                @else
                    disabled
                @endif
                wire:loading.attr="disabled"
                wire:target="send"
                class="btn-pop rounded-full bg-sunrise-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-sunrise-600 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="send">
                    @if ($targetCount > 0)
                        Send to {{ $targetCount }} recipient(s)
                    @else
                        No recipients in this audience (0)
                    @endif
                </span>
                <span wire:loading wire:target="send">Sending broadcast&hellip;</span>
            </button>

            <span class="text-xs text-foreground/40">
                Automatic RFC 8058 one-click unsubscribe attached to all messages.
            </span>
        </div>
    </form>
</div>
