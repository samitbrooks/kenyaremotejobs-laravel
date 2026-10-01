@props([
    'title' => 'Verified Paid Surveys & Side-Income Platforms in Kenya (Daily Updated)',
    'description' => 'A curated, daily-verified directory of legitimate paid-survey and get-paid-to platforms open to Kenya. Verified payout rails via PayPal, M-Pesa, and airtime.',
])

<x-layouts.app
    :title="$title"
    :description="$description"
    :canonical="url('/surveys')"
>
    {{-- Schema.org ItemList & FAQPage Rich Results for Google --}}
    @isset($itemListJsonLd)
        <script type="application/ld+json">{!! $itemListJsonLd !!}</script>
    @endisset
    @isset($faqJsonLd)
        <script type="application/ld+json">{!! $faqJsonLd !!}</script>
    @endisset

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        {{-- Breadcrumb Navigation --}}
        <nav class="mb-6 flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-teal-600 transition">Home</a>
            <span>&rsaquo;</span>
            <span class="text-slate-900 font-bold">Paid Surveys & Side Income</span>
        </nav>

        {{-- Verification Status Banner --}}
        <div class="mb-8 rounded-2xl border border-teal-200/90 bg-gradient-to-r from-teal-50/90 via-emerald-50/80 to-teal-50/90 p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-600"></span>
                </span>
                <p class="text-xs sm:text-sm font-bold text-teal-950">
                    Active &amp; Verified for Kenya <span class="font-normal text-teal-800">&middot; Daily availability verified: <span class="font-semibold text-teal-950">{{ $lastSyncedAt ?? now()->format('F j, Y') }}</span></span>
                </p>
            </div>
            @if ($hasAccess)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1 text-xs font-black text-white shadow-2xs">
                    ✓ VIP Vault Unlocked
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 border border-teal-200 px-3 py-1 text-xs font-bold text-teal-800 shadow-2xs">
                    🇰🇪 4 Free Previews &middot; 18 in Premium Vault
                </span>
            @endif
        </div>

        {{-- Unlocked Success Banner --}}
        @if (request()->query('unlocked'))
            <div class="mb-8 rounded-3xl border border-emerald-300 bg-emerald-50 p-6 text-center shadow-md animate-fade-in">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 mb-2">
                    <x-icon name="check" class="h-6 w-6" />
                </div>
                <h3 class="text-lg font-extrabold text-emerald-950">🎉 Survey Side-Income Vault Unlocked!</h3>
                <p class="text-xs sm:text-sm text-emerald-800 mt-1 max-w-lg mx-auto">
                    Your M-Pesa payment was confirmed. You now have unrestricted access to all 20+ verified research panels, Outlier AI tasks, and direct sign-up links.
                </p>
            </div>
        @endif

        {{-- Hero Header --}}
        <div class="max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                Earn While You Search <span class="text-teal-600">— Paid Surveys for Kenyans</span>
            </h1>
            <p class="mt-3 text-base text-slate-600 leading-relaxed font-normal">
                Looking for your next remote role takes time. This hand-vetted directory gives Kenyan jobseekers legitimate, tested platforms to earn flexible side-income in USD, airtime, or crypto while you apply. Every platform below accepts Kenyan IP addresses without VPN trickery.
            </p>
        </div>



        {{-- Interactive Filter Bar --}}
        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-5">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('surveys') }}" class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ($activeCategory ?? 'all') === 'all' && ($activePayout ?? 'all') === 'all' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All Platforms ({{ count(\App\Support\SurveyPlatforms::all()) }})
                </a>
                <a href="{{ route('surveys', ['payout' => 'mpesa']) }}" class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ($activePayout ?? '') === 'mpesa' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    🇰🇪 M-Pesa / PayPal Compatible
                </a>
                <a href="{{ route('surveys', ['category' => 'research']) }}" class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ($activeCategory ?? '') === 'research' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    💰 High-Paying ($50+)
                </a>
                <a href="{{ route('surveys', ['category' => 'microtasks']) }}" class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ($activeCategory ?? '') === 'microtasks' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    ⚡ Microtasks &amp; AI
                </a>
                <a href="{{ route('surveys', ['payout' => 'crypto']) }}" class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ($activePayout ?? '') === 'crypto' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    🪙 Crypto Payouts
                </a>
            </div>

            <p class="text-xs font-medium text-slate-500">
                Showing <strong class="text-slate-900">{{ count($platforms ?? \App\Support\SurveyPlatforms::all()) }}</strong> verified panels
            </p>
        </div>

        {{-- Platforms Grid --}}
        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($platforms ?? \App\Support\SurveyPlatforms::all() as $i => $platform)
                @php
                    $isLocked = ! $hasAccess && ($platform['is_premium'] ?? false);
                @endphp

                <x-reveal :delay="min($i, 8) * 40">
                    <div class="group relative flex h-full flex-col justify-between rounded-3xl border {{ $isLocked ? 'border-amber-200/70 bg-gradient-to-b from-white to-amber-50/20' : 'border-slate-200/90 bg-white' }} p-6 shadow-sm hover:shadow-xl hover:border-teal-500/60 hover:-translate-y-1 transition-all duration-200 overflow-hidden">
                        <div>
                            {{-- Top Header Row: Name & Badges --}}
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-black text-slate-900 group-hover:text-teal-700 transition flex items-center gap-1.5">
                                        <span>{{ $platform['name'] }}</span>
                                        @if ($isLocked)
                                            <span class="text-amber-500 text-xs" title="Locked">🔒</span>
                                        @endif
                                    </h2>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <div class="flex text-amber-400 text-xs">
                                            @for ($s = 0; $s < 5; $s++)
                                                <span>★</span>
                                            @endfor
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-500">{{ $platform['rating'] ?? 4.7 }}/5</span>
                                    </div>
                                </div>

                                @if ($isLocked)
                                    <span class="inline-flex rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-[10px] font-black text-amber-900 shrink-0 shadow-2xs">
                                        VIP Vault
                                    </span>
                                @elseif (! empty($platform['badge']))
                                    <span class="inline-flex rounded-full bg-teal-50 border border-teal-200/80 px-2.5 py-0.5 text-[10px] font-bold text-teal-800 shrink-0 shadow-2xs">
                                        {{ $platform['badge'] }}
                                    </span>
                                @endif
                            </div>

                            {{-- Description (Blurred if locked) --}}
                            <div class="mt-3.5 relative">
                                <p class="text-xs text-slate-600 leading-relaxed font-normal {{ $isLocked ? 'filter blur-[3.5px] select-none opacity-40' : '' }}">
                                    {{ $platform['description'] }}
                                </p>
                                @if ($isLocked)
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="rounded-full bg-white/90 backdrop-blur-xs border border-amber-200 px-3 py-1 text-[11px] font-bold text-amber-900 shadow-xs">
                                            🔒 Full Details Locked
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Vital Stats Table --}}
                            <div class="mt-4 space-y-2 rounded-2xl bg-slate-50/80 p-3.5 border border-slate-100 text-xs">
                                <div class="flex items-center justify-between text-slate-700">
                                    <span class="text-slate-400 font-medium">Earning Rate:</span>
                                    <strong class="font-bold text-teal-800">{{ $platform['earning_potential'] ?? 'Varies per survey' }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-700">
                                    <span class="text-slate-400 font-medium">Min. Cashout:</span>
                                    <span class="font-semibold text-slate-900">{{ $platform['min_payout'] ?? '$5.00' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-700">
                                    <span class="text-slate-400 font-medium">Works On:</span>
                                    <span class="font-medium text-slate-700">{{ $platform['device'] ?? 'Web & Mobile' }}</span>
                                </div>
                            </div>

                            {{-- Payout Rails Chips --}}
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach ($platform['payouts'] as $payout)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100/90 border border-slate-200/60 px-2.5 py-0.5 text-[11px] font-semibold text-slate-700">
                                        <x-icon name="check" class="h-3 w-3 text-teal-600" /> {{ $payout }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- CTA Button --}}
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            @if ($isLocked)
                                <a
                                    href="#unlock-vault"
                                    onclick="document.getElementById('unlock-vault')?.scrollIntoView({behavior: 'smooth'}); setTimeout(() => document.querySelector('#unlock-vault input[type=tel]')?.focus(), 300);"
                                    class="btn-pop flex w-full items-center justify-center gap-2 rounded-full bg-slate-900 hover:bg-teal-700 px-4 py-2.5 text-xs font-extrabold text-white shadow-md transition"
                                >
                                    <span>🔒 Unlock Premium Paid Surveys (KES {{ $priceKes ?? 199 }})</span>
                                </a>
                                <p class="mt-2 text-center text-[10px] text-slate-500 font-medium">
                                    Flat KES 199 unlocks <strong>all</strong> 18+ premium platforms (not per survey)
                                </p>
                            @else
                                <a
                                    href="{{ $platform['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn-pop flex w-full items-center justify-center gap-2 rounded-full bg-teal-600 hover:bg-teal-700 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-teal-600/20 transition"
                                >
                                    <span>Sign Up on {{ $platform['name'] }}</span>
                                    <span>&rarr;</span>
                                </a>
                                <p class="mt-2 text-center text-[10px] text-slate-400 font-medium">
                                    Direct official link &middot; Free sign-up
                                </p>
                            @endif
                        </div>
                    </div>
                </x-reveal>
            @endforeach
        </div>

        {{-- Unlock Paywall Component (Shown if not unlocked) --}}
        @if (! $hasAccess)
            <div id="unlock-vault" class="mt-14 max-w-2xl mx-auto">
                <livewire:survey-unlock-button />
            </div>
        @endif

        {{-- Withdrawal Guide Callout --}}
        <div class="mt-14 rounded-3xl border border-teal-200/80 bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 p-6 sm:p-10 text-white shadow-xl">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-500/20 px-3 py-1 text-xs font-bold text-teal-300">
                    🇰🇪 M-Pesa Pro Tip
                </span>
                <h3 class="mt-3 text-xl sm:text-2xl font-black">
                    How to Convert Survey USD Directly to M-Pesa
                </h3>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed font-normal">
                    Most global survey sites pay via PayPal. Link your Kenyan PayPal account with your Safaricom M-Pesa number using the official Safaricom PayPal-to-M-Pesa portal. Withdrawals process in under 2 minutes at standard Central Bank exchange rates.
                </p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <a
                        href="https://www.paypal-mobilemoney.com/m-pesa"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-pop rounded-full bg-teal-400 px-5 py-2 text-xs font-extrabold text-slate-950 hover:bg-teal-300 transition"
                    >
                        Visit Safaricom PayPal-M-Pesa Portal &rarr;
                    </a>
                    <a href="{{ url('/journal/complete-guide-to-legit-remote-jobs-in-kenya') }}" class="text-xs font-bold text-teal-300 hover:underline">
                        Read our Remote Payouts Guide &rarr;
                    </a>
                </div>
            </div>
        </div>

        {{-- Frequently Asked Questions Section --}}
        <div class="mt-14 border-t border-slate-200 pt-12">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">
                    Paid Surveys in Kenya — FAQs
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Everything you need to know about earning legitimately, avoiding disqualifications, and cashing out in Kenya.
                </p>
            </div>

            <div class="mt-8 space-y-4 max-w-4xl" x-data="{ active: null }">
                @foreach ($faqs ?? \App\Support\SurveyPlatforms::faqs() as $index => $faq)
                    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-2xs">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between p-5 text-left text-sm font-bold text-slate-900 transition hover:bg-slate-50"
                            @click="active = (active === {{ $index }} ? null : {{ $index }})"
                        >
                            <span>{{ $faq['question'] }}</span>
                            <span class="ml-4 shrink-0 text-slate-400 font-bold" x-text="active === {{ $index }} ? '−' : '+'"></span>
                        </button>
                        <div
                            x-show="active === {{ $index }}"
                            x-cloak
                            class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed font-normal border-t border-slate-100 pt-3"
                        >
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
