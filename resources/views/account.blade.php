<x-layouts.app title="Your Account" description="Log in or create a free KenyaRemoteJobs account to track the jobs you've unlocked.">
    @if (! $user)
        <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
            <h1 class="mb-2 text-center text-3xl font-bold">Your account</h1>
            <p class="mb-8 text-center text-foreground/60">
                Create a free account to unlock jobs and keep track of what you&rsquo;ve paid for.
            </p>
            <livewire:auth-forms :redirect-to="$redirectTo" />
        </div>
    @else
        <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
            <div class="rounded-3xl border border-black/5 bg-white p-8 text-center shadow-lg">
                <h1 class="text-2xl font-bold">Your account</h1>
                <p class="mt-1 text-foreground/60">{{ $user->email }}</p>
                @if ($user->subscribed)
                    <p class="mt-2 flex items-center justify-center gap-1.5 text-sm font-semibold text-emerald-700">
                        <x-icon name="check" class="h-4 w-4" /> Full access granted &mdash; every job is unlocked for you
                    </p>
                @endif

                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ url('/jobs') }}" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white">
                        Browse jobs
                    </a>
                    <x-logout-button />
                </div>
            </div>

            <div class="mt-8 rounded-3xl border border-black/5 bg-white p-6 text-left shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Your credits</h2>
                    <a href="{{ url('/pricing') }}" class="text-sm font-semibold text-sunrise-600 hover:underline">Buy more &rarr;</a>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-3">
                    @foreach (config('jobs.tiers') as $tier)
                        <div class="rounded-xl bg-horizon-50 p-3 text-center">
                            <p class="text-2xl font-bold">{{ $balances[$tier]['remaining'] }}</p>
                            <p class="text-xs text-foreground/50">{{ config('jobs.tier_labels')[$tier] }}</p>
                        </div>
                    @endforeach
                </div>

                @if ($purchases->isNotEmpty())
                    <ul class="mt-4 divide-y divide-black/5 border-t border-black/5">
                        @foreach ($purchases as $p)
                            <li class="flex items-center justify-between py-2 text-xs text-foreground/50">
                                <span>{{ config('jobs.tier_labels')[$p->tier] ?? $p->tier }} package &middot; +{{ $p->credits }} credit{{ $p->credits === 1 ? '' : 's' }}</span>
                                <span>KES {{ number_format($p->amount_kes) }} &middot; {{ \App\Support\Format::timeAgo($p->purchased_at) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="mt-8 rounded-3xl border border-black/5 bg-white p-6 text-left shadow-sm">
                <h2 class="font-semibold">Jobs you&rsquo;ve unlocked</h2>
                @if ($unlocks->isEmpty())
                    <p class="mt-2 text-sm text-foreground/50">
                        Nothing yet &mdash; <a href="{{ url('/jobs') }}" class="underline hover:text-sunrise-600">browse listings</a> to unlock one.
                    </p>
                @else
                    <ul class="mt-3 divide-y divide-black/5">
                        @foreach ($unlocks as $u)
                            <li class="flex items-center justify-between gap-4 py-3 text-sm">
                                <div class="min-w-0">
                                    @if ($u->jobListing)
                                        <a href="{{ url('/jobs/'.$u->jobListing->id) }}" class="truncate font-medium hover:underline">{{ $u->jobListing->title }}</a>
                                    @else
                                        <span class="truncate text-foreground/40">Listing removed</span>
                                    @endif
                                    <p class="text-xs text-foreground/50">
                                        {{ config('jobs.tier_labels')[$u->tier] ?? $u->tier }}
                                        &middot;
                                        {{ $u->amount_kes > 0 ? 'KES '.number_format($u->amount_kes) : 'via credit' }}
                                        &middot;
                                        {{ \App\Support\Format::timeAgo($u->unlocked_at) }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
