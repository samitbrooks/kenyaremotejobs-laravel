@php
    $plans = config('jobs.subscription_plans');
    $currentPlan = $plans[$selectedPlan];
    $billingLabel = $currentPlan['months'] === 1 ? '1 month' : $currentPlan['months'].' months';
@endphp

<x-layouts.app
    title="Pricing — Verified Remote Jobs & Career Tools (KES 250/mo)"
    description="Explore Pro membership for application links, AI CV tailoring, cover letters, and application tracking. From KES 250 per month via M-Pesa."
>
    {{-- Notification Messages --}}
    @if (session('info'))
        <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-[#c7ccfb] bg-[#eef0fe]/95 p-4 text-center text-sm font-semibold text-slate-900 shadow-xs flex items-center justify-center gap-2">
                <x-icon name="sparkle" class="h-4 w-4 text-[#3444da] shrink-0" />
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
            <div class="rounded-xl border border-[#c7ccfb] bg-[#eef0fe]/95 p-4 text-center text-sm font-semibold text-slate-900 shadow-xs flex items-center justify-center gap-2">
                <x-icon name="check" class="h-4 w-4 text-[#3444da] shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Clean Micro1 Light Hero Header --}}
    <section class="border-b border-slate-200/80 text-slate-900">
        <div class="mx-auto max-w-6xl px-5 pb-12 pt-10 sm:px-6 sm:pb-16 sm:pt-14 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.08em] text-[#3444da]">
                    KenyaRemoteJobs membership &middot; Remote Career Accelerator &middot; M-Pesa Supported
                </p>
                <h1 class="mt-3 text-3xl font-semibold leading-tight tracking-[-0.03em] text-slate-900 sm:text-4xl lg:text-5xl">
                    Find remote roles open to Kenya. Prepare your application with Pro.
                </h1>
                <p class="mt-3.5 max-w-2xl text-[14px] leading-relaxed text-slate-600 sm:text-[16px]">
                    Browse roles and requirements for free. Choose Pro for application links, AI CV tailoring, cover letters, and tools to organise your job search.
                </p>
            </div>

            {{-- Free Trial Notice for Eligible Candidates --}}
            @if (! $user?->subscribed && ! $user?->hasUsedTrial())
                <div class="mt-6 inline-flex flex-wrap items-center gap-2.5 rounded-full border border-[#c7ccfb] bg-[#eef0fe] px-4 py-1.5 text-xs text-slate-700">
                    <span class="inline-flex items-center gap-1 font-bold text-[#3444da]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#3444da]"></span>
                        Free 24-Hour Pass:
                    </span>
                    <span>Want to test before paying?</span>
                    <a href="{{ route('trial.activate') }}" class="font-semibold text-[#3444da] hover:underline underline-offset-2">
                        Start 24h Free Trial &rarr;
                    </a>
                </div>
            @elseif ($user?->onTrial())
                <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-[#c7ccfb] bg-[#eef0fe] px-4 py-1.5 text-xs text-[#3444da]">
                    <span class="h-2 w-2 rounded-full bg-[#3444da]"></span>
                    <span>Trial Active: {{ $user->trialRemainingHuman() }} remaining.</span>
                    <a href="{{ route('jobs.index') }}" class="font-bold text-[#3444da] underline ml-1">Browse Jobs &rarr;</a>
                </div>
            @endif
        </div>
    </section>

    {{-- Main Pricing Section: Single Flagship Offer --}}
    <section class="mx-auto max-w-6xl px-4 pt-10 pb-16 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h2 class="font-bold text-slate-900">Start with a role worth applying for</h2>
            <p class="mt-2 text-sm text-slate-600">See roles matched to your skills and review requirements before choosing membership. Employer identities remain hidden on the site.</p>
            <a href="{{ url('/match') }}" class="mt-3 inline-block text-sm font-bold text-[#3444da]">Find my matches for free &rarr;</a>
        </div>
        {{-- Flagship Offer Card --}}
        <div class="relative overflow-hidden rounded-3xl border border-[#c7ccfb] bg-white p-6 sm:p-10 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                {{-- Left Column: Price & Action --}}
                <div class="lg:col-span-5 flex flex-col justify-between h-full border-b lg:border-b-0 lg:border-r border-slate-100 pb-8 lg:pb-0 lg:pr-8">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3 py-1 text-xs font-bold text-[#3444da]">
                            <x-icon name="sparkle" class="h-3.5 w-3.5 text-[#3444da]" /> Pro membership
                        </div>
                        <h2 class="mt-3 text-2xl sm:text-3xl font-semibold text-slate-900 tracking-tight">
                            Application access and tools to help you prepare.
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Choose your membership duration below. All three options include the same Pro features.
                        </p>

                        <div class="mt-6 flex items-baseline gap-2">
                            <span class="text-4xl sm:text-5xl font-semibold tracking-tight text-slate-900">KES {{ number_format($currentPlan['price_kes']) }}</span>
                            <span class="text-sm font-semibold text-slate-500">/ {{ $billingLabel }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">One M-Pesa payment for the selected period &bull; No automatic renewal</p>
                    </div>

                    <div class="mt-8 space-y-3">
                        <livewire:subscribe-button
                            :period="$selectedPlan"
                            :is-authed="(bool) $user"
                            :already-subscribed="(bool) $user?->subscribed"
                            :button-label="'Get Pro for '.$billingLabel.' (KES '.number_format($currentPlan['price_kes']).') →'"
                            wire:key="pricing-flagship-pro-btn"
                        />

                        <nav aria-label="Membership duration" class="flex flex-wrap gap-3 text-xs pt-2 text-slate-600">
                            @foreach (['pro' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'] as $planKey => $planLabel)
                                <a href="{{ route('pricing', ['plan' => $planKey]) }}" @if ($selectedPlan === $planKey || ($selectedPlan === 'monthly' && $planKey === 'pro')) aria-current="page" @endif class="font-bold hover:text-[#3444da] hover:underline">
                                    {{ $planLabel }}: KES {{ number_format($plans[$planKey]['price_kes']) }}
                                </a>
                            @endforeach
                        </nav>

                        {{-- Safaricom M-Pesa Security Badge --}}
                        <div class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-center text-xs text-slate-600">
                            <span class="font-semibold text-slate-800 inline-flex items-center gap-1"><x-icon name="phone" class="h-3.5 w-3.5 text-[#3444da]" /> Safaricom M-Pesa:</span>
                            <span>Instant STK push prompt sent directly to your phone.</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Everything Included with Pro --}}
                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">
                        Included with Pro &bull; Complete Feature List
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">Application links across current job listings</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Open application links and review each employer’s application requirements.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">Explore international and Kenyan employer listings</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Check each listing for location eligibility, experience, and working hours.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">Unlimited AI CV & ATS Tailoring Copilot</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Adapt your CV bullets to the job description and review suggestions before applying.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">AI Cover Letter Generator</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Personalized, role-targeted cover letters created in seconds for every application.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">Kenya Remote Contractor Toolkit</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Contractor guides, invoice templates, and information about payment services.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-200/70">
                            <x-icon name="check" class="mt-0.5 shrink-0 text-[#3444da] h-4 w-4" />
                            <div>
                                <strong class="font-semibold text-slate-900 block">Application CRM &amp; Priority Support</strong>
                                <span class="text-slate-600 text-xs leading-relaxed">Track application stages and contact our support desk when you need help.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Trust Guarantees Row --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <x-icon name="shield" class="h-5 w-5 text-[#3444da] shrink-0" />
                            <span><strong>7-Day 100% Money-Back Guarantee:</strong> Full refund if you don't find verified roles.</span>
                        </div>
                        <a
                            href="{{ route('support.index') }}"
                            class="inline-flex items-center gap-1.5 font-bold text-slate-700 hover:text-[#3444da] transition"
                        >
                            <x-icon name="document" class="h-3.5 w-3.5 text-[#3444da]" />
                            <span>Ask Support Desk</span> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 rounded-3xl border border-slate-200/80 bg-slate-50 p-6 sm:p-10">
            <h3 class="text-2xl font-semibold text-slate-900">Free vs Pro Membership</h3>
            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <div>
                    <h4 class="font-bold text-slate-900">Free account</h4>
                    <p class="mt-2 text-sm text-slate-600">Browse job titles, descriptions, and requirements before deciding whether membership is useful to you. Try one AI CV tailoring credit.</p>
                </div>
                <div>
                    <h4 class="font-bold text-[#3444da]">Pro membership</h4>
                    <p class="mt-2 text-sm text-slate-600">Access application links across current listings, tailor your CV, generate cover letters, and track your applications.</p>
                </div>
            </div>
            <p class="mt-6 text-sm text-slate-600">Your membership pays for access to our job discovery and career tools. It is not an employer application or candidate verification fee. Employers make their own hiring decisions; membership does not guarantee interviews, employment, or a particular salary.</p>
        </div>

        <div class="mt-10"><x-membership-preview /></div>

        {{-- AEO FAQ Section: Direct 50-Word Answers for Search & LLMs --}}
        <section class="mt-14 overflow-hidden rounded-3xl border border-slate-200/80 bg-white text-slate-900 shadow-sm">
            <div class="px-6 py-6 sm:px-8 sm:py-8 border-b border-slate-100 bg-[#f8f9fd]">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#3444da]">
                    Frequently Answered Questions
                </p>
                <h3 class="mt-1.5 text-2xl font-semibold tracking-tight text-slate-900">
                    Frequently Asked Questions About Membership
                </h3>
            </div>
            <div class="divide-y divide-slate-100">
                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-slate-900 hover:text-[#3444da] transition">
                        <span>How does KES 250 Pro Membership work?</span>
                        <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-[#3444da]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Pro includes application links across current job listings, AI CV tailoring, cover letters, application tracking, and contractor guides. Choose monthly, quarterly, or yearly access and pay through M-Pesa. There are no automatic recurring charges.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-slate-900 hover:text-[#3444da] transition">
                        <span>How do I pay via M-Pesa?</span>
                        <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-[#3444da]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Choose a plan, sign in, and enter your M-Pesa phone number. Select Pay to request an STK prompt, then enter your PIN on your phone. Access activates after payment confirmation. If payment succeeds but access is missing, contact our support desk.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-slate-900 hover:text-[#3444da] transition">
                        <span id="refund-policy">What is the 7-day money-back guarantee?</span>
                        <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-[#3444da]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        If you join Pro and do not find verified remote jobs matching your discipline within 7 days, open a ticket on our Support Desk or email support@kenyaremotejobs.com to request a full refund to your M-Pesa line.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-slate-900 hover:text-[#3444da] transition">
                        <span>Why does KenyaRemoteJobs charge a fee when some sites are free?</span>
                        <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-[#3444da]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Membership supports our job discovery service and career tools. You are purchasing platform access, not a job offer, candidate verification, or preferential treatment by employers. You can browse listings and requirements before choosing to join.
                    </p>
                </details>

                <details class="group p-6">
                    <summary class="cursor-pointer list-none flex justify-between items-center font-bold text-base text-slate-900 hover:text-[#3444da] transition">
                        <span>Do international employers pay directly to Kenya?</span>
                        <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180 group-open:text-[#3444da]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        Payment methods and currencies depend on the employer and your contract. Confirm location eligibility and payment arrangements with the employer before accepting an offer.
                    </p>
                </details>
            </div>
        </section>

        {{-- Final CTA --}}
        <div class="mt-12 text-center">
            <h3 class="text-xl font-bold text-slate-900">Ready to organise your next application?</h3>
            <p class="mt-1 text-sm text-slate-500">Review the available roles and choose the membership duration that suits you.</p>
            <div class="mt-5 max-w-sm mx-auto">
                <livewire:subscribe-button
                    :period="$selectedPlan"
                    :is-authed="(bool) $user"
                    :already-subscribed="(bool) $user?->subscribed"
                    :button-label="'Get Pro for '.$billingLabel.' (KES '.number_format($currentPlan['price_kes']).') →'"
                    wire:key="pricing-bottom-cta"
                />
            </div>
        </div>
    </section>
</x-layouts.app>
