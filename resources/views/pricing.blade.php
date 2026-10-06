@php
    $plans = config('jobs.subscription_plans');
    $proPlan = $plans['pro'] ?? ['price_kes' => 250, 'label' => 'Pro Membership'];
@endphp

<x-layouts.app
    title="Pricing — Verified Remote Jobs & Career Tools (KES 250/mo)"
    description="Invest KES 250 to land a KES 250,000/mo remote role. Full access to apply to 800+ verified remote jobs, direct Kenyan employer listings, and unlimited AI CV tailoring via M-Pesa."
>
    {{-- Notification Messages --}}
    @if (session('info'))
        <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-teal-200 bg-teal-50/95 p-4 text-center text-sm font-semibold text-teal-900 shadow-xs flex items-center justify-center gap-2">
                <x-icon name="sparkle" class="h-4 w-4 text-teal-600 shrink-0" />
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-rose-200 bg-rose-50/95 p-4 text-center text-sm font-semibold text-rose-900 shadow-xs flex items-center justify-center gap-2">
                <span class="text-rose-600 font-bold text-base">&times;</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-[#00C978] bg-emerald-50/95 p-4 text-center text-sm font-semibold text-emerald-900 shadow-xs flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[#00A966] shrink-0"><path d="M20 6 9 17l-5-5"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Dark Navy / Slate Hero Header --}}
    <section class="bg-[#0F172A] text-white">
        <div class="mx-auto max-w-6xl px-5 pb-8 pt-8 sm:px-6 sm:pb-12 sm:pt-12 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#00D47E]">
                    KenyaRemoteJobs membership &middot; Remote Career Accelerator &middot; M-Pesa Supported
                </p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.03em] text-white sm:text-4xl lg:text-5xl">
                    Invest KES 250 to Land a KES 250,000/mo Remote Role.
                </h1>
                <p class="mt-3.5 max-w-2xl text-[14px] leading-relaxed text-white/75 sm:text-[16px]">
                    Stop competing with 10,000 applicants on generic job boards. Full Access to Apply to All 800+ Jobs immediately, unlock direct employer contacts, and connect with global teams actively seeking Kenyan talent.
                </p>
            </div>

            {{-- Free Trial Notice for Eligible Candidates --}}
            @if (! $user?->subscribed && ! $user?->hasUsedTrial())
                <div class="mt-6 inline-flex flex-wrap items-center gap-2.5 rounded-full border border-teal-500/30 bg-teal-950/60 px-4 py-1.5 text-xs text-white/80">
                    <span class="inline-flex items-center gap-1 font-bold text-[#00D47E]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#00D47E] animate-ping"></span>
                        Free 24-Hour Pass:
                    </span>
                    <span>Want to test before paying?</span>
                    <a href="{{ route('trial.activate') }}" class="font-extrabold text-[#00D47E] hover:underline underline-offset-2">
                        Start 24h Free Trial &rarr;
                    </a>
                </div>
            @elseif ($user?->onTrial())
                <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-950/60 px-4 py-1.5 text-xs text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-[#00D47E]"></span>
                    <span>Trial Active: {{ $user->trialRemainingHuman() }} remaining.</span>
                    <a href="{{ route('jobs.index') }}" class="font-bold text-white underline ml-1">Browse Jobs &rarr;</a>
                </div>
            @endif
        </div>
    </section>

    {{-- Main Pricing Section: Single Flagship Offer --}}
    <section class="mx-auto max-w-6xl px-4 -mt-6 pb-16 sm:px-6 lg:px-8">
        {{-- Flagship Offer Card --}}
        <div class="relative overflow-hidden rounded-3xl border-2 border-[#00C978] bg-white p-6 sm:p-10 shadow-2xl shadow-slate-900/10">
            <span class="absolute top-0 right-0 rounded-bl-2xl bg-[#00D47E] px-5 py-1.5 text-[11px] font-black uppercase tracking-wider text-[#08291D] shadow-sm">
                1,000x ROI &bull; Most Popular
            </span>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                {{-- Left Column: Price & Action --}}
                <div class="lg:col-span-5 flex flex-col justify-between h-full border-b lg:border-b-0 lg:border-r border-slate-100 pb-8 lg:pb-0 lg:pr-8">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-800">
                            ⭐ Pro Membership Flagship Pass
                        </div>
                        <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Everything Unlocked. Instant 0-Second Apply Access.
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            No complicated tiers or locked features. One simple membership gives you complete VIP access to every verified remote job, AI tool, and recruiter direct link.
                        </p>

                        <div class="mt-6 flex items-baseline gap-2">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-slate-900">KES 250</span>
                            <span class="text-sm font-semibold text-slate-500">/ month</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Less than the price of a cup of coffee &bull; No automatic bank debit</p>
                    </div>

                    <div class="mt-8 space-y-3">
                        <livewire:subscribe-button
                            :period="'pro'"
                            :is-authed="(bool) $user"
                            :already-subscribed="(bool) $user?->subscribed"
                            :button-label="'Unlock Pro Access Now (KES 250) &rarr;'"
                            wire:key="pricing-flagship-pro-btn"
                        />

                        {{-- Duration options (Quarterly / Yearly discounts) --}}
                        <div class="flex items-center justify-between text-xs pt-2 text-slate-500">
                            <span>Prepay & Save:</span>
                            <div class="flex items-center gap-3">
                                <a href="?plan=quarterly" class="font-bold text-slate-700 hover:text-teal-700 hover:underline">Quarterly: KES 499 (Save 33%)</a>
                                <span>&bull;</span>
                                <a href="?plan=yearly" class="font-bold text-slate-700 hover:text-teal-700 hover:underline">Yearly: KES 899 (Save 70%)</a>
                            </div>
                        </div>

                        {{-- Safaricom M-Pesa Security Badge --}}
                        <div class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-center text-xs text-slate-600">
                            <span class="font-extrabold text-[#00A966]">📱 Safaricom M-Pesa:</span>
                            <span>Instant STK push prompt sent directly to your phone.</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Everything Included with Pro --}}
                <div class="lg:col-span-7">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">
                        Included with Pro &bull; Complete Feature List
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">Full Access to Apply to All 800+ Jobs</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Immediate 0-second apply access to newly posted USD roles before public queues open.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">Pro Exclusive: Employers Actively Seeking Kenyan Talent</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Direct employer listings specifically hiring in Nairobi and East Africa with 0 visa hurdles.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">Unlimited AI CV & ATS Tailoring Copilot</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Rewrite your CV bullets to match job descriptions and pass automated ATS filters.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">AI Cover Letter Generator</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Personalized, role-targeted cover letters created in seconds for every application.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">Kenya Remote Contractor Toolkit</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">IRS W-8BEN tax treaty guide (0% US withholding), USD invoice templates, Wise/Payoneer setup.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-[#00A966]"><path d="M20 6 9 17l-5-5"></path></svg>
                            <div>
                                <strong class="font-extrabold text-slate-900 block">Application CRM & WhatsApp Support</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Track application stages, salary negotiations, and message our candidate desk on WhatsApp.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Trust Guarantees Row --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🛡️</span>
                            <span><strong>7-Day 100% Money-Back Guarantee:</strong> Full refund if you don't find verified roles.</span>
                        </div>
                        <a
                            href="https://wa.me/254700000000?text=Hello%20KenyaRemoteJobs%20Team%2C%20I%20have%20a%20question%20before%20joining%20Pro"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 font-bold text-[#08734E] hover:underline"
                        >
                            <span>💬 Ask on WhatsApp</span> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- The 1,000x ROI Math Section --}}
        <div class="mt-12 rounded-3xl border border-slate-200 bg-slate-900 text-white p-6 sm:p-10 shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 border border-amber-400/40 px-3 py-1 text-xs font-black uppercase tracking-wider text-amber-300">
                        💰 The Return On Investment Math
                    </span>
                    <h3 class="mt-3 text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Invest KES 250 Today to Land KES 250,000/mo.
                    </h3>
                    <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                        International companies hiring remote Kenyan talent pay between <strong>$1,500 and $4,000/month (KES 200,000 to KES 550,000)</strong>. Your Pro membership fee is literally 0.1% of your first month's salary.
                    </p>
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                            <p class="text-xs text-slate-400">Monthly Pro Fee</p>
                            <p class="text-xl font-black text-white mt-1">KES 250</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                            <p class="text-xs text-slate-400">Average Remote Offer</p>
                            <p class="text-xl font-black text-emerald-400 mt-1">KES 250,000</p>
                        </div>
                        <div class="col-span-2 sm:col-span-1 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-3">
                            <p class="text-xs text-emerald-300">Target Return</p>
                            <p class="text-xl font-black text-emerald-300 mt-1">1,000x ROI</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 rounded-2xl border border-white/15 bg-white/5 p-6">
                    <h4 class="text-base font-extrabold text-white">Why We Charge a Small Verification Fee</h4>
                    <p class="mt-2 text-xs text-slate-300 leading-relaxed">
                        Free platforms like LinkedIn and Twitter are overrun by <strong>fake recruiters, task scams, and 10,000 bot applicants per job</strong>.
                    </p>
                    <p class="mt-2 text-xs text-slate-300 leading-relaxed">
                        Our KES 250 verification fee protects you:
                    </p>
                    <ul class="mt-3 space-y-1.5 text-xs text-slate-200">
                        <li class="flex items-center gap-2"><span class="text-[#00D47E] font-bold">&check;</span> We manually verify company tax IDs & remote payroll legitimacy.</li>
                        <li class="flex items-center gap-2"><span class="text-[#00D47E] font-bold">&check;</span> We filter out US/EU-only visa restrictions.</li>
                        <li class="flex items-center gap-2"><span class="text-[#00D47E] font-bold">&check;</span> Global employers prioritize applicants from our vetted queue.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3 Verified Kenyan Placement Stories --}}
        <div class="mt-14">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-black uppercase tracking-wider text-[#08734E]">Real Success Stories</span>
                <h3 class="mt-1.5 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Kenyans Earning in USD from Home
                </h3>
                <p class="mt-2 text-sm text-slate-600">
                    Real Kenyan professionals who used KenyaRemoteJobs Pro to land international remote roles.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Story 1 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-800 text-sm">
                                BM
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">Brian M.</h4>
                                <p class="text-xs text-slate-500">Nairobi &bull; Full-Stack Laravel Dev</p>
                            </div>
                        </div>
                        <div class="mt-3 inline-block rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                            Hired: $2,800 / month (~KES 360,000)
                        </div>
                        <p class="mt-3 text-xs leading-relaxed text-slate-600">
                            "I sent over 30 applications on LinkedIn with zero replies. With Pro Early Access, I applied within 2 hours of a role being posted and tailored my CV with the AI copilot. Had an interview 48 hours later. Now earning USD straight to my local bank."
                        </p>
                    </div>
                    <p class="mt-4 text-[11px] text-slate-400 border-t border-slate-100 pt-3">Placed at a Delaware tech startup</p>
                </div>

                {{-- Story 2 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center font-bold text-purple-800 text-sm">
                                FW
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">Faith W.</h4>
                                <p class="text-xs text-slate-500">Mombasa &bull; Customer Operations</p>
                            </div>
                        </div>
                        <div class="mt-3 inline-block rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                            Hired: £1,900 / month (~KES 320,000)
                        </div>
                        <p class="mt-3 text-xs leading-relaxed text-slate-600">
                            "The early access is the difference between being applicant #10 vs applicant #700. The remote contractor toolkit also saved me so much headache when filling out my W-8BEN and sending my first USD invoice."
                        </p>
                    </div>
                    <p class="mt-4 text-[11px] text-slate-400 border-t border-slate-100 pt-3">Placed at a UK e-commerce brand</p>
                </div>

                {{-- Story 3 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-800 text-sm">
                                KO
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">Kevin O.</h4>
                                <p class="text-xs text-slate-500">Eldoret &bull; Technical Content Writer</p>
                            </div>
                        </div>
                        <div class="mt-3 inline-block rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                            Hired: $3,200 / month (~KES 410,000)
                        </div>
                        <p class="mt-3 text-xs leading-relaxed text-slate-600">
                            "Best KES 250 I ever spent in my life. One month of Pro got me 3 interviews and an international contract that completely changed my family's financial situation. You guys are doing God's work for Kenyan talent."
                        </p>
                    </div>
                    <p class="mt-4 text-[11px] text-slate-400 border-t border-slate-100 pt-3">Placed at a SaaS company</p>
                </div>
            </div>
        </div>

        {{-- GEO "Fitness Signal" Comparison Table: Free vs Pro Membership & Us vs Alternatives --}}
        <div class="mt-14">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#08734E]">Comparative Analysis</p>
                    <h3 class="mt-1 text-2xl font-extrabold tracking-[-0.02em] text-slate-900">
                        Compare What Is Included: Free vs Pro Membership
                    </h3>
                </div>
                <p class="text-xs text-slate-500">Transparent comparison across all job channels.</p>
            </div>

            <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="p-4 font-extrabold text-slate-700">Feature / Channel</th>
                            <th class="p-4 font-bold text-slate-500 text-center">Standard Free</th>
                            <th class="p-4 font-black text-slate-900 bg-emerald-50 text-center border-x border-emerald-200">
                                KenyaRemoteJobs Pro (KES 250)
                            </th>
                            <th class="p-4 font-bold text-slate-500 text-center">Upwork / Freelance</th>
                            <th class="p-4 font-bold text-slate-500 text-center">LinkedIn Jobs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Full Access to Apply to All 800+ Jobs</td>
                            <td class="p-4 text-center text-slate-500">Wait 48h (Queue fills)</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                ✓ Instant 0-second apply
                            </td>
                            <td class="p-4 text-center text-slate-500">Pay connects per bid</td>
                            <td class="p-4 text-center text-slate-500">10,000+ public applicants</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Pro Exclusive: Employers Actively Seeking Kenyan Talent</td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                ✓ Direct hiring pipeline
                            </td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                            <td class="p-4 text-center text-slate-400">Rare / Unfiltered</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Scam & Fee-Demand Protection</td>
                            <td class="p-4 text-center text-slate-600">Manual review</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                ✓ 100% Tax & Company Vetted
                            </td>
                            <td class="p-4 text-center text-slate-500">High spam proposals</td>
                            <td class="p-4 text-center text-slate-500">Frequent ghost jobs</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">AI ATS CV Tailoring Copilot</td>
                            <td class="p-4 text-center text-slate-400">1 free check</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                ✓ Unlimited on all jobs
                            </td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                            <td class="p-4 text-center text-slate-500">Requires $39.99/mo Premium</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Platform Commission on Earnings</td>
                            <td class="p-4 text-center text-slate-900 font-bold">0%</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                0% (You keep 100% of salary)
                            </td>
                            <td class="p-4 text-center text-rose-600 font-bold">10% &ndash; 20% cut per payout!</td>
                            <td class="p-4 text-center text-slate-900 font-bold">0%</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Kenya Remote Contractor Toolkit</td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                            <td class="p-4 text-center font-bold text-emerald-800 bg-emerald-50/60 border-x border-emerald-200">
                                ✓ W-8BEN & Invoicing included
                            </td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                            <td class="p-4 text-center text-slate-400">&ndash;</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-bold text-slate-900">Cost / Payment Rail</td>
                            <td class="p-4 text-center text-slate-700">Free</td>
                            <td class="p-4 text-center font-black text-slate-900 bg-emerald-50 border-x border-emerald-200">
                                KES 250 / mo via M-Pesa
                            </td>
                            <td class="p-4 text-center text-slate-700">$0.15–$1.50 per proposal</td>
                            <td class="p-4 text-center text-slate-700">$39.99 / mo (Card only)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- AEO FAQ Section: Direct 50-Word Answers for Search & LLMs --}}
        <section class="mt-14 overflow-hidden rounded-3xl bg-[#0F172A] text-white shadow-xl">
            <div class="px-6 py-6 sm:px-8 sm:py-8 border-b border-white/10">
                <p class="text-xs font-black uppercase tracking-wider text-[#00D47E]">
                    Frequently Answered Questions
                </p>
                <h3 class="mt-1.5 text-2xl font-black tracking-tight">
                    Frequently Asked Questions About Membership
                </h3>
            </div>
            <div class="divide-y divide-white/10">
                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-white/90 hover:text-white transition">
                        <span>How does KES 250 Pro Membership work?</span>
                        <svg class="h-4 w-4 text-white/40 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        Pro Membership gives you immediate 0-second apply access to all 800+ verified remote jobs, direct Kenyan employer listings, unlimited AI CV and cover letter tailoring, and the Kenya contractor toolkit. Payment is KES 250 for 30 days via Safaricom M-Pesa with no automatic recurring charges.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-white/90 hover:text-white transition">
                        <span>How do I pay via M-Pesa?</span>
                        <svg class="h-4 w-4 text-white/40 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        Clicking the upgrade button opens an instant M-Pesa STK prompt on your Safaricom phone. Simply enter your M-Pesa PIN to complete payment. Your Pro access activates immediately within 5 seconds without manual code entry.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-white/90 hover:text-white transition">
                        <span>What is the 7-day money-back guarantee?</span>
                        <svg class="h-4 w-4 text-white/40 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        If you join Pro and do not find verified remote jobs matching your discipline within 7 days, message our support team on WhatsApp or email support@kenyaremotejobs.com for an immediate 100% refund sent back to your M-Pesa line.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-white/90 hover:text-white transition">
                        <span>Why does KenyaRemoteJobs charge a fee when some sites are free?</span>
                        <svg class="h-4 w-4 text-white/40 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        Free sites are overrun with ghost listings, Telegram scams, and 10,000 spam applicants per post. Our small KES 250 fee funds manual verification of corporate tax IDs and remote payroll legitimacy, while ensuring international employers treat applicants from KenyaRemoteJobs as vetted, serious professionals.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-white/90 hover:text-white transition">
                        <span>Do international employers pay directly to Kenya?</span>
                        <svg class="h-4 w-4 text-white/40 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        Yes. Verified remote employers pay Kenyan contractors in USD, EUR, or GBP via Wise (direct to Kenyan bank or M-Pesa), Payoneer, Deel, Remote.com, or direct bank SWIFT transfer to local USD bank accounts (e.g., Equity, KCB, Standard Chartered).
                    </p>
                </details>
            </div>
        </section>

        {{-- Final CTA --}}
        <div class="mt-12 text-center">
            <h3 class="text-xl font-bold text-slate-900">Ready to start earning in USD from Kenya?</h3>
            <p class="mt-1 text-sm text-slate-500">Join over 8,500 Kenyan professionals applying to verified global roles.</p>
            <div class="mt-5 max-w-sm mx-auto">
                <livewire:subscribe-button
                    :period="'pro'"
                    :is-authed="(bool) $user"
                    :already-subscribed="(bool) $user?->subscribed"
                    :button-label="'Get Started with Pro (KES 250) &rarr;'"
                    wire:key="pricing-bottom-cta"
                />
            </div>
        </div>
    </section>
</x-layouts.app>
