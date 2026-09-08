<x-layouts.admin title="Admin Dashboard">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <livewire:admin-resync-button />
    </div>

    <p class="mt-2 text-sm text-foreground/50">
        Last synced {{ $lastSyncedAt ? \App\Support\Format::timeAgo($lastSyncedAt) : 'never' }}.
    </p>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Total jobs live</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalJobs }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Kenya-Friendly matches</p>
            <p class="mt-1 text-3xl font-bold">{{ $kenyaFriendly }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Total users</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Full-access grants</p>
            <p class="mt-1 text-3xl font-bold">{{ $subscribed }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Visitors, last 7 days</p>
            <p class="mt-1 text-3xl font-bold">{{ $pageViews['last7Days'] }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Visitors, last 30 days</p>
            <p class="mt-1 text-3xl font-bold">{{ $pageViews['last30Days'] }}</p>
        </div>
        <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
            <p class="text-sm text-foreground/50">Visitors, all-time</p>
            <p class="mt-1 text-3xl font-bold">{{ $pageViews['total'] }}</p>
        </div>
    </div>
    <p class="mt-2 text-xs text-foreground/40">
        Visitor counts are page loads, not unique people &mdash; no cookies or IPs are stored.
    </p>

    <div class="mt-8 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Jobs by source</h2>
        <table class="mt-4 w-full border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-black/10 text-foreground/50">
                    <th class="py-2 font-medium">Source</th>
                    <th class="py-2 font-medium">Live listings</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bySource as $source => $count)
                    <tr class="border-b border-black/5">
                        <td class="py-2">{{ $source }}</td>
                        <td class="py-2 tabular-nums">{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-6 text-sm">
        <a href="{{ url('/admin/users') }}" class="font-semibold text-sunrise-600 hover:underline">Manage users &rarr;</a>
    </p>
</x-layouts.admin>
