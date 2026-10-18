<x-layouts.app title="Refer Friends & Earn Free Pro Access | Kenya Remote Jobs" description="Invite friends and fellow Kenyan jobseekers to Kenya Remote Jobs and earn free Pro access days and AI CV tailoring credits.">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:py-14">
        {{-- Hero Header --}}
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da] uppercase tracking-wider mb-4">
                <x-icon name="gift" class="h-3.5 w-3.5 text-[#3444da]" /> Kenyan Jobseeker Referral Program
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                Refer Friends, <span class="text-[#3444da]">Earn Free Pro Access</span>
            </h1>
            <p class="mt-3.5 text-base sm:text-lg text-slate-600 leading-relaxed">
                Share verified, high-paying remote roles with your network. Every friend who signs up, tests the free trial, or subscribes unlocks free Pro days and AI CV tailor credits for you.
            </p>
        </div>

        {{-- Share Link & Code Card --}}
        <div
            x-data="{
                copied: false,
                codeCopied: false,
                link: @js($referralLink),
                code: @js($referralCode),
                copyLink() {
                    navigator.clipboard.writeText(this.link);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                },
                copyCode() {
                    navigator.clipboard.writeText(this.code);
                    this.codeCopied = true;
                    setTimeout(() => this.codeCopied = false, 2500);
                }
            }"
            class="overflow-hidden rounded-3xl border border-slate-200/80 bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/50 p-6 sm:p-8 text-slate-900 shadow-sm relative mb-10"
        >
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#3444da]">Your Exclusive Invitation Link</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Share anywhere &bull; Track in real time</h2>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-[#eef0fe] rounded-2xl px-4 py-2 border border-[#c7ccfb] self-start sm:self-auto">
                        <span class="text-xs text-slate-500">Referral Code:</span>
                        <span class="font-mono font-black text-[#3444da] text-sm sm:text-base tracking-wider">{{ $referralCode }}</span>
                        <button
                            type="button"
                            @click="copyCode()"
                            class="ml-1 text-xs text-[#3444da] hover:underline transition font-bold"
                            x-text="codeCopied ? 'Copied!' : 'Copy'"
                        ></button>
                    </div>
                </div>

                {{-- Copyable Link Input Bar --}}
                <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            readonly
                            :value="link"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 text-xs sm:text-sm font-mono text-slate-800 select-all focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 focus:border-[#3444da]"
                        />
                    </div>
                    <button
                        type="button"
                        @click="copyLink()"
                        class="btn-pop shrink-0 rounded-2xl bg-[#3444da] hover:bg-[#2633bf] text-white font-bold px-6 py-3.5 text-sm transition flex items-center justify-center gap-2 shadow-md shadow-[#3444da]/20 active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span x-text="copied ? 'Link Copied to Clipboard!' : 'Copy Referral Link'"></span>
                    </button>
                </div>

                {{-- Social 1-Click Share Buttons --}}
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Instant 1-Click Share to Groups &amp; Contacts:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <a
                            href="{{ $whatsappShareUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-[#25D366] hover:bg-[#20ba59] text-white px-4 py-3 text-xs font-bold transition shadow-sm"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                            </svg>
                            <span>WhatsApp</span>
                        </a>

                        <a
                            href="{{ $telegramShareUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-[#0088cc] hover:bg-[#0077b3] text-white px-4 py-3 text-xs font-bold transition shadow-sm"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/>
                            </svg>
                            <span>Telegram</span>
                        </a>

                        <a
                            href="{{ $twitterShareUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-black hover:bg-neutral-900 border border-white/20 text-white px-4 py-3 text-xs font-bold transition shadow-sm"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            <span>X (Twitter)</span>
                        </a>

                        <a
                            href="{{ $linkedinShareUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2 rounded-xl bg-[#0077b5] hover:bg-[#006699] text-white px-4 py-3 text-xs font-bold transition shadow-sm"
                        >
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-xs">
                <span class="text-xs font-bold text-foreground/50 uppercase tracking-wide">Friends Joined</span>
                <p class="text-3xl font-black text-foreground mt-2">{{ $stats['total_referrals'] }}</p>
                <p class="text-[11px] text-foreground/60 mt-1">Signed up via your link</p>
            </div>

            <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-xs">
                <span class="text-xs font-bold text-[#3444da] uppercase tracking-wide">Free Pro Days Earned</span>
                <p class="text-3xl font-black text-[#3444da] mt-2">+{{ $stats['earned_pro_days'] }} <span class="text-sm font-bold text-foreground/50">days</span></p>
                <p class="text-[11px] text-foreground/60 mt-1">Full 0-second apply access</p>
            </div>

            <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-xs">
                <span class="text-xs font-bold text-[#3444da] uppercase tracking-wide">AI Tailor Credits</span>
                <p class="text-3xl font-black text-[#3444da] mt-2">+{{ $stats['earned_ai_tailors'] }}</p>
                <p class="text-[11px] text-foreground/60 mt-1">ATS CV tailoring passes</p>
            </div>

            <div class="rounded-3xl border border-black/5 bg-white p-5 shadow-xs">
                <span class="text-xs font-bold text-foreground/50 uppercase tracking-wide">Your Pro Status</span>
                @if ($user->subscribed)
                    <p class="text-base font-black text-amber-600 mt-2 flex items-center gap-1.5">
                        <x-icon name="star" class="h-4 w-4 text-amber-500" />
                        <span>Pro Member</span>
                    </p>
                    <p class="text-[11px] text-foreground/60 mt-1">Unlimited Pro subscription active</p>
                @elseif ($user->hasReferralProAccess())
                    <p class="text-base font-black text-[#3444da] mt-2 flex items-center gap-1.5">
                        <x-icon name="sparkle" class="h-4 w-4 text-[#3444da]" />
                        <span>Pro Active</span>
                    </p>
                    <p class="text-[11px] text-foreground/60 mt-1">Until {{ $user->pro_access_until->format('M j, Y') }}</p>
                @elseif ($user->onTrial())
                    <p class="text-base font-black text-sky-600 mt-2">
                        Trial ({{ $user->trialRemainingHuman() }})
                    </p>
                    <p class="text-[11px] text-foreground/60 mt-1">Refer friends to extend by +3 days</p>
                @else
                    <p class="text-base font-black text-foreground/70 mt-2">Free Account</p>
                    <p class="text-[11px] text-foreground/60 mt-1">Earn free Pro by sharing</p>
                @endif
            </div>
        </div>

        {{-- How It Works / Milestone Reward Rules --}}
        <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm mb-10">
            <div class="max-w-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-[#3444da]">Rewarding Every Step</span>
                <h2 class="text-2xl font-black text-foreground tracking-tight mt-1">How the Referral Awarding System Works</h2>
                <p class="text-xs sm:text-sm text-foreground/60 mt-1">
                    You don't have to wait for friends to pay to be rewarded. You earn benefits at every milestone:
                </p>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Milestone 1 --}}
                <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-5 relative">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-sky-600 text-white font-black text-sm mb-3">1</span>
                    <h3 class="text-base font-bold text-slate-900">Friend Signs Up</h3>
                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                        When someone joins Kenya Remote Jobs via your referral link, both of you are immediately awarded:
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-sky-600/10 px-2.5 py-1 text-xs font-bold text-sky-800">
                        <x-icon name="sparkle" class="h-3.5 w-3.5 text-sky-800" />
                        <span>+2 AI CV Tailor Credits</span>
                    </div>
                </div>

                {{-- Milestone 2 --}}
                <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-5 relative">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-amber-500 text-white font-black text-sm mb-3">2</span>
                    <h3 class="text-base font-bold text-slate-900">Friend Tests 24h Free Trial</h3>
                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                        When your referred friend activates their 24-hour free trial pass to test the platform, you receive:
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-amber-600/10 px-2.5 py-1 text-xs font-bold text-amber-900">
                        <x-icon name="rocket" class="h-3.5 w-3.5 text-amber-900" />
                        <span>+3 Full Days of Pro Access</span>
                    </div>
                </div>

                {{-- Milestone 3 --}}
                <div class="rounded-2xl border border-[#c7ccfb] bg-[#eef0fe]/40 p-5 relative">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#3444da] text-white font-black text-sm mb-3">3</span>
                    <h3 class="text-base font-bold text-slate-900">Friend Upgrades to Pro</h3>
                    <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                        When your referred friend unlocks full monthly Pro membership (KES 250), you receive our biggest reward:
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-[#eef0fe] border border-[#c7ccfb] px-2.5 py-1 text-xs font-bold text-[#3444da]">
                        <x-icon name="trophy" class="h-3.5 w-3.5 text-[#3444da]" />
                        <span>+14 Full Days of Pro Access + 5 AI Tailors</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200/80 p-4 text-xs text-slate-600 flex items-center justify-between flex-wrap gap-2">
                <span class="flex items-center gap-1.5">
                    <x-icon name="bulb" class="h-4 w-4 text-amber-500 shrink-0 inline" />
                    <span><strong>Rewards Stack Automatically:</strong> Refer 2 friends who test the trial and you get 6 continuous days of full Pro access. Refer 2 who subscribe and you get nearly an entire month free!</span>
                </span>
                <a href="{{ url('/account') }}" class="font-bold text-[#3444da] hover:underline">View Account Dashboard &rarr;</a>
            </div>
        </div>

        {{-- Referrals History Table --}}
        <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-black/5">
                <div>
                    <h2 class="text-lg font-bold text-foreground">Your Referred Network</h2>
                    <p class="text-xs text-foreground/60">Candidate privacy is protected with email masking</p>
                </div>
                <span class="text-xs font-bold text-[#3444da]">{{ count($stats['referrals']) }} registered</span>
            </div>

            @if (empty($stats['referrals']))
                <div class="py-12 text-center max-w-sm mx-auto">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef0fe] text-[#3444da] mb-3">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-foreground">No referrals yet</h3>
                    <p class="text-xs text-foreground/60 mt-1 mb-5">
                        Share your unique referral link with university coursemates, tech communities, or WhatsApp groups to start earning Pro days!
                    </p>
                    <a
                        href="{{ $whatsappShareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-pop inline-flex items-center gap-2 rounded-2xl bg-[#25D366] px-5 py-2.5 text-xs font-bold text-white shadow-sm"
                    >
                        <span>Share on WhatsApp &rarr;</span>
                    </a>
                </div>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-black/5 text-foreground/50">
                                <th class="pb-3 font-semibold">User</th>
                                <th class="pb-3 font-semibold">Joined</th>
                                <th class="pb-3 font-semibold text-right">Status &amp; Reward</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach ($stats['referrals'] as $ref)
                                <tr>
                                    <td class="py-3.5">
                                        <p class="font-bold text-foreground">{{ $ref['name'] }}</p>
                                        <p class="text-[11px] text-foreground/50 font-mono">{{ $ref['masked_email'] }}</p>
                                    </td>
                                    <td class="py-3.5 text-foreground/60">{{ $ref['joined_at'] }}</td>
                                    <td class="py-3.5 text-right">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $ref['status_badge'] }}">
                                            {{ $ref['status_label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
