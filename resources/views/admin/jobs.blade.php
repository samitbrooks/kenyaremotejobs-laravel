<x-layouts.admin title="Admin: Jobs">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold">Jobs</h1>
        <livewire:admin-add-job-form />
    </div>

    <form action="{{ url('/admin/jobs') }}" method="GET" class="mt-6 flex gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search title, company, tag…" class="w-full max-w-sm rounded-lg border border-black/10 px-3 py-2 text-sm">
        <button type="submit" class="rounded-lg bg-horizon-800 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>

    <p class="mt-4 text-sm text-foreground/50">{{ $total }} listing(s).</p>

    <div class="mt-3 overflow-x-auto rounded-2xl border border-black/5 bg-white shadow-sm">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-black/10 text-foreground/50">
                    <th class="p-4 font-medium">Title</th>
                    <th class="p-4 font-medium">Source</th>
                    <th class="p-4 font-medium">Tier</th>
                    <th class="p-4 font-medium">Kenya-Friendly</th>
                    <th class="p-4 font-medium">Posted</th>
                    <th class="p-4 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                    <tr class="border-b border-black/5 last:border-0">
                        <td class="p-4">
                            <a href="{{ url('/jobs/'.$job->id) }}" class="font-semibold hover:underline">{{ $job->title }}</a>
                            <p class="text-foreground/50">{{ $job->company }}</p>
                        </td>
                        <td class="p-4">
                            {{ $job->source_name }}
                            @if ($job->origin === 'manual')
                                <span class="ml-1.5 rounded-full bg-sunrise-100 px-2 py-0.5 text-[10px] font-semibold text-sunrise-700">Manual</span>
                            @endif
                        </td>
                        <td class="p-4">
                            {{ config('jobs.tier_labels')[$job->tier] ?? $job->tier }}
                            <span class="ml-1 text-foreground/40">
                                ({{ config('jobs.credit_packages')[$job->tier]['credits'] ?? '?' }} credit{{ (config('jobs.credit_packages')[$job->tier]['credits'] ?? 0) === 1 ? '' : 's' }}/KES {{ number_format(config('jobs.credit_packages')[$job->tier]['price_kes'] ?? 0) }})
                            </span>
                        </td>
                        <td class="p-4">
                            @if ($job->kenya_friendly)
                                <x-icon name="check" class="h-4 w-4 text-emerald-600" />
                            @endif
                        </td>
                        <td class="p-4 text-foreground/50">{{ \App\Support\Format::timeAgo($job->posted_at) }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ url('/admin/jobs/'.$job->id.'/edit') }}" class="btn-pop rounded-full border border-black/10 px-3 py-1 text-xs font-semibold transition hover:bg-black/5">Edit</a>
                                <livewire:admin-remove-job-button :job-id="$job->id" :key="'remove-'.$job->id" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-foreground/50">No listings match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($totalPages > 1)
        <div class="mt-4 flex items-center justify-center gap-4 text-sm">
            @if ($page > 1)
                <a href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['q' => $q ?: null, 'page' => $page - 1])) }}" class="font-semibold text-sunrise-600 hover:underline">&larr; Prev</a>
            @endif
            <span class="text-foreground/50">Page {{ $page }} of {{ $totalPages }}</span>
            @if ($page < $totalPages)
                <a href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['q' => $q ?: null, 'page' => $page + 1])) }}" class="font-semibold text-sunrise-600 hover:underline">Next &rarr;</a>
            @endif
        </div>
    @endif
</x-layouts.admin>
