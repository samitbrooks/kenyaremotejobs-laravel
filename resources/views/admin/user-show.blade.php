<x-layouts.admin title="Admin: User">
    <a href="{{ url('/admin/users') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to Users</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">{{ $user->email }}</h1>
            <p class="mt-1 text-sm text-foreground/50">
                Joined {{ \App\Support\Format::timeAgo($user->created_at) }}
                @if ($user->subscribed)
                    &middot; <span class="font-semibold text-emerald-700">Full access</span>
                @else
                    &middot; Pay per job
                @endif
            </p>
        </div>
        <livewire:admin-subscription-toggle :user-id="$user->id" :subscribed="$user->subscribed" />
    </div>

    <div class="mt-8 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Credit balance</h2>
        <div class="mt-3 grid grid-cols-3 gap-3">
            @foreach (config('jobs.tiers') as $tier)
                <div class="rounded-xl bg-horizon-50 p-3 text-center">
                    <p class="text-2xl font-bold">{{ $balances[$tier]['remaining'] }}</p>
                    <p class="text-xs text-foreground/50">{{ config('jobs.tier_labels')[$tier] }}</p>
                    <p class="mt-1 text-[11px] text-foreground/40">{{ $balances[$tier]['purchased'] }} purchased &middot; {{ $balances[$tier]['used'] }} used</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 border-t border-black/5 pt-5">
            <h3 class="text-sm font-semibold">Grant free credits</h3>
            <p class="mt-1 text-xs text-foreground/50">Records a KES 0 purchase — shows in their history as a comp, not a real charge.</p>
            <div class="mt-3">
                <livewire:admin-grant-credit-form :user-id="$user->id" />
            </div>
        </div>

        @if ($purchases->isNotEmpty())
            <div class="mt-5 border-t border-black/5 pt-4">
                <h3 class="mb-2 text-sm font-semibold">Purchase history</h3>
                <ul class="divide-y divide-black/5">
                    @foreach ($purchases as $p)
                        <li class="flex items-center justify-between py-2 text-xs text-foreground/50">
                            <span>{{ config('jobs.tier_labels')[$p->tier] ?? $p->tier }} &middot; +{{ $p->credits }} credit{{ $p->credits === 1 ? '' : 's' }}</span>
                            <span>{{ $p->amount_kes > 0 ? 'KES '.number_format($p->amount_kes) : 'Comped' }} &middot; {{ \App\Support\Format::timeAgo($p->purchased_at) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Jobs unlocked</h2>
        @if ($unlocks->isEmpty())
            <p class="mt-2 text-sm text-foreground/50">None yet.</p>
        @else
            <ul class="mt-3 divide-y divide-black/5">
                @foreach ($unlocks as $u)
                    <li class="flex items-center justify-between gap-4 py-2 text-sm">
                        @if ($u->jobListing)
                            <a href="{{ url('/jobs/'.$u->jobListing->id) }}" class="truncate hover:underline">{{ $u->jobListing->title }}</a>
                        @else
                            <span class="truncate text-foreground/40">Listing removed</span>
                        @endif
                        <span class="shrink-0 text-xs text-foreground/50">{{ \App\Support\Format::timeAgo($u->unlocked_at) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($postedJobs->isNotEmpty())
        <div class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Employer postings</h2>
            <ul class="mt-3 divide-y divide-black/5">
                @foreach ($postedJobs as $job)
                    <li class="flex items-center justify-between gap-4 py-2 text-sm">
                        <a href="{{ url('/jobs/'.$job->id) }}" class="truncate hover:underline">{{ $job->title }}</a>
                        <span class="shrink-0 text-xs text-foreground/50">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-layouts.admin>
