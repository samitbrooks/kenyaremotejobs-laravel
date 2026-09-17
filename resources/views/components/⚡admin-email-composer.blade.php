<?php

use App\Models\User;
use App\Services\BulkMailer;
use App\Services\JobRecommendationService;
use Livewire\Component;

new class extends Component
{
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

        <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 max-w-2xl">
            <input
                type="email"
                wire:model="testEmail"
                placeholder="Enter your email address…"
                class="flex-1 rounded-xl border border-black/10 bg-white px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            />
            <button
                type="button"
                wire:click="sendTestEmail"
                wire:loading.attr="disabled"
                wire:target="sendTestEmail"
                class="btn-pop shrink-0 rounded-xl bg-horizon-800 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-horizon-900 transition disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="sendTestEmail">Send Test Email</span>
                <span wire:loading wire:target="sendTestEmail">Dispatching&hellip;</span>
            </button>
            <button
                type="button"
                wire:click="sendDigestTest"
                wire:loading.attr="disabled"
                wire:target="sendDigestTest"
                class="btn-pop shrink-0 rounded-xl bg-orange-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-orange-700 transition disabled:opacity-60"
                title="Send a sample dark-mode Job Matches Digest email"
            >
                <span wire:loading.remove wire:target="sendDigestTest">Send Matches Digest</span>
                <span wire:loading wire:target="sendDigestTest">Sending Digest&hellip;</span>
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
