@php
    $tierDescriptions = [
        'basic' => 'Most listings — anything without a confirmed higher salary starts here.',
        'intermediate' => 'Roles with a confirmed annual salary of roughly $40k–$80k.',
        'premium' => 'Roles with a confirmed annual salary above $80k.',
    ];
@endphp

<x-layouts.app
    title="Pricing"
    description="No per-job paywall — buy a package and spend its credits on any listing in that tier. KES 300 for 1 Basic unlock, 599 for 2 Intermediate unlocks, 999 for 3 Premium unlocks — or go unlimited from KES 1,499/month, with quarterly and yearly plans at a discount."
>
    <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
        <h1 class="text-3xl font-bold sm:text-4xl">Simple, honest pricing</h1>
        <p class="mx-auto mt-3 max-w-xl text-foreground/60">
            No per-job paywall. Buy a package once and spend its credits on any listing in that tier, whenever you find one worth unlocking &mdash; or go unlimited with a monthly subscription.
            Every listing also opens up completely free {{ config('jobs.premium_window_days') }} days after it&rsquo;s posted, credits or not.
        </p>

        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-3">
            @foreach (config('jobs.tiers') as $tier)
                @php $pkg = config('jobs.credit_packages')[$tier]; $remaining = $balances[$tier]['remaining'] ?? 0; @endphp
                <div class="flex flex-col rounded-3xl border border-black/5 bg-white p-6 text-left shadow-lg transition hover:shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-sunrise-600">{{ config('jobs.tier_labels')[$tier] }}</p>
                    <p class="mt-2 text-4xl font-bold">
                        KES {{ number_format($pkg['price_kes']) }}
                        <span class="block text-sm font-medium text-foreground/50">for {{ $pkg['credits'] }} unlock{{ $pkg['credits'] === 1 ? '' : 's' }}</span>
                    </p>
                    <p class="mt-4 flex-1 text-sm text-foreground/60">{{ $tierDescriptions[$tier] }}</p>

                    @if ($remaining > 0)
                        <p class="mt-3 text-xs font-semibold text-emerald-700">
                            You have {{ $remaining }} {{ config('jobs.tier_labels')[$tier] }} credit{{ $remaining === 1 ? '' : 's' }} left
                        </p>
                    @endif

                    <div class="mt-4">
                        <livewire:buy-package-button :tier="$tier" :price-kes="$pkg['price_kes']" :is-authed="(bool) $user" :key="'buy-'.$tier" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-3xl border-2 border-horizon-200 bg-horizon-50 p-6 text-left">
            <p class="text-sm font-semibold uppercase tracking-wide text-horizon-700">Unlimited</p>
            <p class="mt-1 text-sm text-foreground/60">
                Unlock every job you find, in any tier, for as long as you&rsquo;re subscribed &mdash; no counting credits. Worth it once you&rsquo;re unlocking more than a handful of listings a month. Prepay quarterly or yearly for a real discount over paying monthly.
            </p>
            <div class="mx-auto mt-5 max-w-xs">
                <livewire:subscribe-button :is-authed="(bool) $user" :already-subscribed="(bool) $user?->subscribed" />
            </div>
        </div>

        <p class="mt-8 text-sm text-foreground/50">
            Not sure which jobs are worth unlocking? <a href="{{ url('/match') }}" class="font-semibold text-sunrise-600 hover:underline">Get your match score</a> first &mdash; it&rsquo;s free and doesn&rsquo;t require any credits.
        </p>

        <p class="mt-6 text-xs text-foreground/40">
            Payments aren&rsquo;t live yet &mdash; buying a package today is free while we finish integrating a Kenyan payment gateway.
        </p>

        <p class="mt-8 text-sm text-foreground/50">
            Looking for a side income instead? The <a href="{{ url('/surveys') }}" class="underline hover:text-sunrise-600">survey directory</a> is completely free.
        </p>
    </div>
</x-layouts.app>
