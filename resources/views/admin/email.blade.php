<x-layouts.admin title="Admin: Email Campaigns & Deliverability">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Email Users and Automated Campaigns</h1>
            <p class="mt-1 text-sm text-foreground/60 max-w-2xl">
                Dispatch daily personalized job matches digests, 24-hour free trial passes, win-back follow-ups, or custom broadcasts to registered users.
            </p>
        </div>

        {{-- Flash Session Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm font-semibold text-emerald-900 shadow-xs flex items-start gap-3">
                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">✓</span>
                <div class="pt-0.5 leading-relaxed">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50/90 p-4 text-sm font-semibold text-red-900 shadow-xs flex items-start gap-3">
                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-white text-xs font-bold">✕</span>
                <div class="pt-0.5 leading-relaxed">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-xs">
                <p class="font-bold">Please check the following errors:</p>
                <ul class="list-disc pl-5 mt-1.5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Status Banner --}}
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-foreground/50">Email Transport & Delivery</span>
                    <p class="text-sm font-semibold text-foreground">
                        Driver: <span class="rounded-md bg-horizon-100 px-2 py-0.5 font-mono text-xs text-horizon-800">{{ config('mail.default') }}</span>
                        &middot;
                        From: <span class="font-normal text-foreground/70">{{ config('mail.from.address') }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1 text-xs font-semibold text-slate-700">
                        <span class="h-2 w-2 rounded-full {{ $pendingQueueJobs > 0 ? 'bg-orange-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                        Background Queue: <strong class="ml-1 text-slate-900">{{ $pendingQueueJobs }}</strong> pending job(s)
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
            </div>

            @if (! $configured)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-900 leading-relaxed">
                    SMTP credentials not detected in <code>.env</code>. On cPanel hosting, verify <code>MAIL_MAILER=smtp</code>, <code>MAIL_HOST</code>, <code>MAIL_PORT=465</code>, <code>MAIL_USERNAME</code>, and <code>MAIL_PASSWORD</code>.
                </div>
            @endif
        </div>

        {{-- Instant Deliverability Test Box --}}
        <div class="rounded-2xl border border-horizon-200 bg-gradient-to-r from-horizon-50/80 via-white to-orange-50/40 p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <x-icon name="sparkle" class="h-4 w-4 text-sunrise-600" />
                <h2 class="text-base font-bold text-horizon-950">Send a Deliverability Test Sample</h2>
            </div>
            <p class="mt-1 text-xs text-foreground/60">
                Dispatches a live sample email directly to your personal email to verify deliverability and layout.
            </p>

            <form method="POST" action="{{ route('admin.email.test') }}" class="mt-4 max-w-4xl space-y-3">
                @csrf
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <input
                        type="email"
                        name="test_email"
                        required
                        value="{{ old('test_email', $defaultTestEmail) }}"
                        placeholder="Enter your email address…"
                        class="flex-1 rounded-xl border border-black/10 bg-white px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
                    />

                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="submit"
                            name="test_type"
                            value="trial"
                            class="btn-pop rounded-xl bg-gradient-to-r from-orange-600 to-rose-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:from-orange-700 hover:to-rose-700 transition"
                            title="Send FlexJobs-style 24-hour free trial sample"
                        >
                            Trial Invite Sample
                        </button>
                        <button
                            type="submit"
                            name="test_type"
                            value="follow_up"
                            class="btn-pop rounded-xl bg-gradient-to-r from-teal-600 to-cyan-700 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:from-teal-700 hover:to-cyan-800 transition"
                            title="Send FlexJobs-style Follow-Up sample"
                        >
                            Follow-Up Sample
                        </button>
                        <button
                            type="submit"
                            name="test_type"
                            value="digest"
                            class="btn-pop rounded-xl bg-orange-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-orange-700 transition"
                            title="Send dark-mode Job Matches Digest sample"
                        >
                            Matches Digest Sample
                        </button>
                        <button
                            type="submit"
                            name="test_type"
                            value="custom"
                            class="btn-pop rounded-xl bg-horizon-800 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-horizon-900 transition"
                            title="Send raw text test"
                        >
                            Plain Test
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Campaign 1: Daily Job Matches Digest --}}
        <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-br from-amber-50/70 via-white to-orange-50/50 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white font-black text-sm shadow-xs">
                        ⚡
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daily Job Matches Digest (Daisy's Curation)</h2>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Automated daily email sent to registered users featuring top curated remote roles open to East Africa.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-emerald-100 border border-emerald-300 px-3 py-1 text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span> Automated Daily at 08:00 AM EAT (05:00 UTC)
                    </span>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Audience Status</span>
                            <span class="rounded-full bg-amber-100 text-amber-900 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['digest_eligible'] }} eligible today
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">Registered Users Ready for Digest</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Registered users who haven't received a matches digest within the last 20 hours. All {{ $counts['all'] }} mailable accounts receive this automatically every morning.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.email.digest') }}" onsubmit="return confirm('Send the Daily Job Matches Digest now to {{ $counts['digest_eligible'] }} user(s)?');" class="mt-4 pt-3 border-t border-slate-100">
                        @csrf
                        <div class="flex items-center justify-between gap-3">
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="force" value="1" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                <span>Force all (bypass 20h limit)</span>
                            </label>
                            <button
                                type="submit"
                                class="btn-pop rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:from-amber-700 hover:to-orange-700 transition"
                            >
                                Dispatch Daily Digest Now
                            </button>
                        </div>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Delivery Stats</span>
                            <span class="rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['digest_sent_today'] }} dispatched today
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">Daily Automation Cadence</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            cPanel runs <code>php artisan schedule:run</code> every minute. At 05:00 UTC (08:00 AM Nairobi), <code>jobs:send-digest</code> triggers automatically and queues personalized emails for all registered accounts.
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <span>Schedule: <strong>Daily at 08:00 EAT</strong></span>
                        <span class="text-emerald-700 font-bold">✓ Active in routes/console.php</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Campaign 2: FlexJobs-Style 24-Hour Free Trial Invite Campaign --}}
        <div class="rounded-2xl border-2 border-orange-200 bg-gradient-to-br from-orange-50/70 via-white to-amber-50/50 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-orange-500 text-white font-extrabold text-sm shadow-xs">
                        🎁
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">FlexJobs-Style 24-Hour Free Trial Campaign</h2>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Invite registered users who haven't subscribed yet to enjoy a 24-hour free pass with a 1-click magic activation link.
                        </p>
                    </div>
                </div>
                <span class="rounded-full bg-orange-100 border border-orange-300 px-3 py-1 text-xs font-bold text-orange-800">
                    1-Click Magic Activation Link
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Target 1: Pending checkout users (visitors who reached M-Pesa checkout) --}}
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Checkout Drop-Offs</span>
                            <span class="rounded-full bg-rose-100 text-rose-800 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['pending'] }} visitors
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">Pending Subscription Visitors</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Users who reached the M-Pesa subscription checkout screen. Send them a complimentary 24h pass to win them back!
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.email.trial') }}" onsubmit="return confirm('Send 24-hour Free Trial invitations to {{ $counts['pending'] }} pending visitor(s)?');" class="mt-4 pt-3 border-t border-slate-100">
                        @csrf
                        <input type="hidden" name="target" value="pending">
                        <button
                            type="submit"
                            @if ($counts['pending'] === 0) disabled @endif
                            class="btn-pop w-full rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-rose-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Send Free Trial to {{ $counts['pending'] }} Pending Visitor(s)
                        </button>
                    </form>
                </div>

                {{-- Target 2: All free registered users --}}
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">All Registered Accounts</span>
                            <span class="rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['free'] }} users
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">All Free Registered Users</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Every registered user in the database without an active subscription.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.email.trial') }}" onsubmit="return confirm('Send 24-hour Free Trial invitations to {{ $counts['free'] }} registered user(s)?');" class="mt-4 pt-3 border-t border-slate-100">
                        @csrf
                        <input type="hidden" name="target" value="free">
                        <button
                            type="submit"
                            @if ($counts['free'] === 0) disabled @endif
                            class="btn-pop w-full rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Send Free Trial to {{ $counts['free'] }} Registered User(s)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Campaign 3: FlexJobs Follow-Up Campaign ("Still thinking about finding a remote job?") --}}
        <div class="rounded-2xl border-2 border-teal-200 bg-gradient-to-br from-teal-50/70 via-white to-cyan-50/50 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-teal-600 text-white font-extrabold text-sm shadow-xs">
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

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Eligible Pending Follow-Up</span>
                            <span class="rounded-full bg-teal-100 text-teal-800 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['pending_follow_up'] }} users
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">Users Ready for Follow-Up</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Registered users without active subscription who have not received this follow-up yet and are past the 24h window.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.email.follow-up') }}" onsubmit="return confirm('Queue Follow-Up email to {{ $counts['pending_follow_up'] }} eligible user(s)?');" class="mt-4 pt-3 border-t border-slate-100">
                        @csrf
                        <button
                            type="submit"
                            @if ($counts['pending_follow_up'] === 0) disabled @endif
                            class="btn-pop w-full rounded-xl bg-teal-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-teal-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Queue Follow-Up to {{ $counts['pending_follow_up'] }} Eligible User(s)
                        </button>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Campaign History</span>
                            <span class="rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs px-2.5 py-0.5">
                                {{ $counts['follow_up_sent'] }} delivered
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-1.5">Automated Daily Lifecycle</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            The cron schedule runs <code>email:send-follow-ups</code> automatically every day at 09:00 EAT (06:00 UTC) so each user receives this follow-up once.
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <span>Cron status: <strong class="text-emerald-700 font-bold">Active in routes/console.php</strong></span>
                        <span class="text-slate-500">Runs daily at 09:00 EAT</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Campaign 4: Custom Broadcast Composer Form --}}
        <form method="POST" action="{{ route('admin.email.broadcast') }}" onsubmit="return confirm('Broadcast this custom announcement to the selected audience?');" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="text-lg font-bold text-foreground">Compose Custom Broadcast</h2>
            <p class="mt-0.5 text-xs text-foreground/50">Send an announcement or newsletter to your user base.</p>

            <div class="mt-5">
                <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Select Audience</label>
                <select name="audience" class="mt-1.5 w-full max-w-sm rounded-xl border border-black/10 px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                    <option value="all">Everyone with an account ({{ $counts['all'] }} mailable)</option>
                    <option value="subscribed">Full-access accounts only ({{ $counts['subscribed'] }} mailable)</option>
                    <option value="free">Pay-per-job accounts only ({{ $counts['free'] }} mailable)</option>
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Subject Line</label>
                <input
                    type="text"
                    name="subject"
                    required
                    value="{{ old('subject') }}"
                    placeholder="e.g. 15 New Remote Roles Open to East Africa This Week"
                    class="mt-1.5 w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
                />
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-foreground/60">Message Body</label>
                <textarea
                    name="message"
                    required
                    rows="9"
                    placeholder="Write your email content here… (Unsubscribe link is appended automatically to comply with international regulations)"
                    class="mt-1.5 w-full rounded-xl border border-black/10 p-3.5 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-sunrise-400"
                >{{ old('message') }}</textarea>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <button
                    type="submit"
                    class="btn-pop rounded-full bg-sunrise-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-sunrise-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Send Custom Broadcast
                </button>

                <span class="text-xs text-foreground/40">
                    Automatic RFC 8058 one-click unsubscribe attached to all messages.
                </span>
            </div>
        </form>
    </div>
</x-layouts.admin>
