<x-layouts.app title="Your Account & Application Tracker" description="Manage your KenyaRemoteJobs account, track job applications, and access your remote contractor toolkit.">
    @if (! $user)
        <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
            <h1 class="mb-2 text-center text-3xl font-bold text-foreground">Your account</h1>
            <p class="mb-8 text-center text-foreground/60">
                Log in or create a free account to track your remote applications and access AI CV tailoring.
            </p>
            <livewire:auth-forms :redirect-to="$redirectTo" />
        </div>
    @else
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            {{-- High-Converting New Member Activation Card for Unsubscribed Users --}}
            @if (! $user->subscribed)
                <div class="mb-8 overflow-hidden rounded-3xl border-2 border-[#00C978]/60 bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A1F18] p-6 sm:p-8 text-white shadow-xl relative">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#00D47E]/10 blur-3xl"></div>

                    <div class="relative z-10">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#00D47E]/20 border border-[#00D47E]/50 px-3 py-1 text-[11px] font-black uppercase tracking-wider text-[#00D47E]">
                                <span>⚡</span> Onboarding: Next Step
                            </span>
                            <span class="text-xs text-white/60 font-medium">
                                Instant M-Pesa Activation &bull; 7-Day Refund Guarantee
                            </span>
                        </div>

                        <h2 class="mt-3.5 text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                            Your account is ready! 92% of candidates who land interviews upgrade to Pro.
                        </h2>
                        <p class="mt-2.5 text-sm text-white/75 leading-relaxed max-w-xl">
                            International remote roles receive 500+ applicants within 48 hours. Standard applicants wait in public queues after positions fill. Pro members apply within the first 2 hours with ATS-tailored CVs.
                        </p>

                        {{-- 4-Step Onboarding Checklist --}}
                        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <span class="font-extrabold text-[#00D47E]">✓ Step 1</span>
                                <p class="font-bold text-white mt-1">Account Created</p>
                                <p class="text-[11px] text-white/50 mt-0.5">Email verified</p>
                            </div>
                            <div class="rounded-xl border border-[#00D47E]/80 bg-[#00D47E]/10 p-3 relative">
                                <span class="rounded-full bg-[#00D47E] text-[#08291D] font-black text-[9px] px-1.5 py-0.2 uppercase absolute top-2 right-2">Action</span>
                                <span class="font-extrabold text-[#00D47E]">Step 2</span>
                                <p class="font-bold text-white mt-1">Activate Pro</p>
                                <p class="text-[11px] text-white/70 mt-0.5">KES 250 via M-Pesa</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <span class="font-bold text-white/40">Step 3</span>
                                <p class="font-bold text-white/70 mt-1">Tailor Your CV</p>
                                <p class="text-[11px] text-white/40 mt-0.5">Beat ATS filters</p>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                <span class="font-bold text-white/40">Step 4</span>
                                <p class="font-bold text-white/70 mt-1">Apply in Top 20</p>
                                <p class="text-[11px] text-white/40 mt-0.5">Direct hiring queues</p>
                            </div>
                        </div>

                        {{-- Checkout Trigger Box --}}
                        <div class="mt-6 rounded-2xl bg-white/10 p-4 border border-white/15">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-[#00D47E] uppercase tracking-wide">
                                        Pro Membership Flagship Pass &bull; KES 250 / month
                                    </p>
                                    <p class="text-xs text-white/70 mt-0.5">
                                        Full 0-second apply access to all 800+ jobs, direct employers & unlimited AI CV tailoring.
                                    </p>
                                </div>
                                <div class="shrink-0 w-full sm:w-auto">
                                    <livewire:subscribe-button
                                        :period="'pro'"
                                        :is-authed="true"
                                        :already-subscribed="false"
                                        :button-label="'Activate Pro Access Now (KES 250) &rarr;'"
                                        wire:key="onboarding-activation-btn"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Account Overview Card --}}
            <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">My Account</h1>
                            @if ($user->subscribed)
                                <span class="rounded-full bg-gradient-to-r from-amber-500 to-sunrise-600 px-3 py-0.5 text-xs font-bold text-white shadow-xs">
                                    ⭐ Pro Member
                                </span>
                            @else
                                <span class="rounded-full bg-black/5 px-2.5 py-0.5 text-xs font-semibold text-foreground/60">
                                    Free Account
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-foreground/60">{{ $user->email }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (! $user->subscribed)
                            <a href="{{ url('/pricing') }}" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-xs font-bold text-white shadow-xs">
                                Upgrade to Pro &rarr;
                            </a>
                        @endif
                        <x-logout-button />
                    </div>
                </div>

                {{-- Status Banner --}}
                <div class="mt-6 rounded-2xl border p-4 {{ $user->subscribed ? 'border-emerald-200 bg-emerald-50/70 text-emerald-900' : 'border-horizon-200 bg-horizon-50/50 text-horizon-900' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="{{ $user->subscribed ? 'check' : 'sparkle' }}" class="h-5 w-5 {{ $user->subscribed ? 'text-emerald-600' : 'text-horizon-600' }}" />
                            <span class="text-sm font-semibold">
                                @if ($user->subscribed)
                                    Pro Membership Active: Unlimited 48h Early Access & AI CV Tailoring
                                @else
                                    {{ $user->free_tailors_remaining ?? 1 }} Free AI CV Tailoring credit available
                                @endif
                            </span>
                        </div>
                        @if (! $user->subscribed)
                            <a href="{{ url('/pricing') }}" class="text-xs font-bold text-sunrise-600 hover:underline">Go Unlimited &rarr;</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Application CRM Tracker --}}
            <div class="mt-8 rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-black/5">
                    <div>
                        <h2 class="text-lg font-bold text-foreground">Application CRM Tracker</h2>
                        <p class="text-xs text-foreground/60">Track your interview stages, recruiter contacts, and salary notes</p>
                    </div>
                    <a href="{{ url('/jobs') }}" class="text-xs font-bold text-sunrise-600 hover:underline">Find More Jobs &rarr;</a>
                </div>

                @if ($applications->isEmpty())
                    <div class="py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-horizon-50 text-horizon-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-foreground">No tracked applications yet</p>
                        <p class="mt-1 text-xs text-foreground/50 max-w-sm mx-auto">
                            When viewing any job listing, click "+ Track in My Applications" to organize your pipeline and record recruiter notes here.
                        </p>
                        <a href="{{ url('/jobs') }}" class="mt-4 inline-block rounded-xl bg-black/5 px-4 py-2 text-xs font-semibold text-foreground/80 hover:bg-black/10 transition">
                            Browse Open Roles
                        </a>
                    </div>
                @else
                    <div class="mt-4 divide-y divide-black/5">
                        @foreach ($applications as $app)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        @if ($app->jobListing)
                                            <a href="{{ url('/jobs/'.$app->jobListing->id) }}" class="font-bold text-sm text-foreground hover:text-sunrise-600 hover:underline truncate">
                                                {{ $app->jobListing->title }}
                                            </a>
                                            <span class="text-xs text-foreground/60">&middot; {{ $app->jobListing->company }}</span>
                                        @else
                                            <span class="text-sm font-medium text-foreground/50 italic">Archived listing</span>
                                        @endif
                                    </div>

                                    @if ($app->recruiter_contact)
                                        <p class="text-xs text-horizon-800 font-medium mt-1">
                                            👤 Recruiter: {{ $app->recruiter_contact }}
                                        </p>
                                    @endif

                                    @if ($app->notes)
                                        <p class="text-xs text-foreground/60 mt-1 bg-horizon-50/50 p-2 rounded-lg border border-black/5">
                                            📝 {{ $app->notes }}
                                        </p>
                                    @endif

                                    <p class="text-[11px] text-foreground/40 mt-1">
                                        Updated {{ \App\Support\Format::timeAgo($app->updated_at) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @php
                                        $badgeClasses = [
                                            'applied' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'interviewing' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'offered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'saved' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        ][$app->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <span class="rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $badgeClasses }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                    @if ($app->jobListing)
                                        <livewire:track-application-button :job-id="$app->jobListing->id" :key="'track-'.$app->id" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kenyan Remote Contractor Toolkit --}}
            <div class="mt-8 rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-black/5">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🇰🇪</span>
                        <div>
                            <h2 class="text-lg font-bold text-foreground">Kenyan Remote Contractor Toolkit</h2>
                            <p class="text-xs text-foreground/60">Ready-to-use contractor documents for international clients</p>
                        </div>
                    </div>
                    @if (! $user->subscribed)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                            🔒 Pro Feature
                        </span>
                    @endif
                </div>

                @if ($user->subscribed)
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-black/10 bg-horizon-50/50 p-4">
                            <h3 class="font-bold text-sm text-foreground">USD Invoice Template</h3>
                            <p class="text-xs text-foreground/60 mt-1">Formatted for international wire, Wise, and Payoneer receipts.</p>
                            <a href="data:text/plain;charset=utf-8,INVOICE%20TEMPLATE%0A%0AFrom:%20%5BYour%20Name%5D%0ANairobi,%20Kenya%0APin:%20%5BYour%20KRA%20PIN%5D%0A%0ATo:%20%5BClient%20Company%20Name%5D%0A%0AServices:%20Remote%20Consulting%20%2F%20Engineering%0AAmount:%20%24%5BAmount%5D%20USD%0APayment%20Method:%20Wise%20%2F%20Swift%20to%20Equity%20Bank%20Kenya" download="kenyan-contractor-usd-invoice.txt" class="mt-3 inline-block text-xs font-bold text-sunrise-600 hover:underline">
                                📥 Download (.txt)
                            </a>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-horizon-50/50 p-4">
                            <h3 class="font-bold text-sm text-foreground">US W-8BEN Form Guide</h3>
                            <p class="text-xs text-foreground/60 mt-1">Avoid 30% US withholding tax under Kenya-US tax treaties.</p>
                            <span class="mt-3 inline-block text-xs font-bold text-horizon-700">
                                ✓ Part I Line 6a: Use your KRA PIN
                            </span>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-horizon-50/50 p-4">
                            <h3 class="font-bold text-sm text-foreground">Timezone Pitch Script</h3>
                            <p class="text-xs text-foreground/60 mt-1">How to pitch Nairobi GMT+3 as ideal 4-5hr overlap for EU/US teams.</p>
                            <span class="mt-3 inline-block text-xs font-bold text-horizon-700">
                                ✓ Tested email pitch template
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl bg-horizon-50/60 p-6 text-center">
                        <p class="font-semibold text-foreground">Unlock the Remote Contractor Toolkit with Pro</p>
                        <p class="text-xs text-foreground/60 mt-1 max-w-md mx-auto">
                            Get complete invoice templates, US tax treaty guides, and outreach scripts used by top Kenyan remote engineers and virtual assistants.
                        </p>
                        <a href="{{ url('/pricing') }}" class="mt-4 inline-block rounded-full bg-horizon-800 px-6 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-horizon-900 transition">
                            Unlock with Pro (KES 250/mo)
                        </a>
                    </div>
                @endif
            </div>

            {{-- Unlocks & Purchase History (Legacy & Pro) --}}
            @if ($unlocks->isNotEmpty() || $purchases->isNotEmpty())
                <div class="mt-8 rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                    <h2 class="text-lg font-bold text-foreground">Transaction & Unlock History</h2>
                    <ul class="mt-4 divide-y divide-black/5">
                        @foreach ($unlocks as $u)
                            <li class="py-2.5 flex items-center justify-between text-xs text-foreground/60">
                                <div>
                                    <span class="font-medium text-foreground">{{ $u->jobListing?->title ?? 'Listing' }}</span>
                                    <span class="text-foreground/40">&middot; {{ $u->jobListing?->company ?? '' }}</span>
                                </div>
                                <span>{{ \App\Support\Format::timeAgo($u->unlocked_at) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
