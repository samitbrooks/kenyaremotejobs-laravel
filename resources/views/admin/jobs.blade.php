<x-layouts.admin title="Admin: Jobs — KenyaRemoteJobs">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manage Jobs</h1>
            <p class="mt-1 text-sm text-slate-500">
                Explore, inspect, edit, or remove synced and manually posted listings.
            </p>
        </div>
        <livewire:admin-add-job-form />
    </div>

    {{-- Quick Source Filter Pills --}}
    <div class="mt-6">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Filter by Job Source:</p>
        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null])) }}"
                class="rounded-full px-3.5 py-1 text-xs font-semibold transition {{ $source === '' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                All Sources
            </a>
            @foreach ($sources as $srcName => $count)
                <a
                    href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['source' => $srcName, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null])) }}"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition {{ $source === $srcName ? 'bg-teal-700 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>{{ $srcName }}</span>
                    <span class="rounded-full {{ $source === $srcName ? 'bg-white/20 text-white' : 'bg-white text-slate-600 border border-slate-200' }} px-1.5 py-0.2 text-[10px] tabular-nums font-bold">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Filter Form --}}
    <form action="{{ url('/admin/jobs') }}" method="GET" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
        <div class="lg:col-span-2">
            <label for="jobs-search" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Search Keywords</label>
            <input
                id="jobs-search"
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Search title, company, tag…"
                class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
            />
        </div>

        <div>
            <label for="jobs-source" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Source</label>
            <select
                id="jobs-source"
                name="source"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
            >
                <option value="">All Sources</option>
                @foreach ($sources as $srcName => $count)
                    <option value="{{ $srcName }}" {{ $source === $srcName ? 'selected' : '' }}>
                        {{ $srcName }} ({{ $count }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="jobs-order" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Posting Order</label>
            <select
                id="jobs-order"
                name="order"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
            >
                <option value="newest" {{ $order === 'newest' ? 'selected' : '' }}>Newest Posted First</option>
                <option value="oldest" {{ $order === 'oldest' ? 'selected' : '' }}>Oldest Posted First</option>
                <option value="recent_sync" {{ $order === 'recent_sync' ? 'selected' : '' }}>Recently Synced / Updated</option>
                <option value="title" {{ $order === 'title' ? 'selected' : '' }}>Alphabetical by Title</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="submit"
                class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-xs hover:bg-slate-800 transition"
            >
                Apply Filters
            </button>
        </div>
    </form>

    <div class="mt-3 flex items-center justify-between">
        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
            <input
                type="checkbox"
                name="kenya_friendly"
                value="true"
                {{ $kenyaFriendly ? 'checked' : '' }}
                onchange="window.location.href = '{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null])) }}' + (this.checked ? '&kenya_friendly=true' : '')"
                class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
            />
            <span>Show Only Kenya-Friendly Matches</span>
        </label>

        @if ($source !== '' || $q !== '' || $order !== 'newest' || $kenyaFriendly)
            <a href="{{ url('/admin/jobs') }}" class="text-xs font-bold text-rose-600 hover:underline">
                &times; Reset All Filters
            </a>
        @endif
    </div>

    <p class="mt-4 text-xs font-semibold text-slate-500">{{ number_format($total) }} listing(s) match.</p>

    {{-- Jobs Table --}}
    <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="w-full min-w-[760px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="p-4">Title & Company</th>
                    <th class="p-4">Source</th>
                    <th class="p-4">Tier / Pay</th>
                    <th class="p-4">Kenya-Friendly</th>
                    <th class="p-4">Posting Date</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jobs as $job)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-4 min-w-[240px]">
                            <a href="{{ url('/jobs/'.$job->id) }}" target="_blank" class="font-bold text-slate-900 hover:text-teal-700 hover:underline">
                                {{ $job->title }}
                            </a>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ $job->company }} &middot; {{ $job->remote_type }}</p>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-800">
                                {{ $job->source_name }}
                            </span>
                            @if ($job->origin === 'manual')
                                <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Manual</span>
                            @elseif ($job->origin === 'employer')
                                <span class="ml-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Employer</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs">
                            <span class="font-bold text-slate-700">{{ config('jobs.tier_labels')[$job->tier] ?? ucfirst($job->tier) }}</span>
                            @if ($job->salary)
                                <p class="text-slate-500 mt-0.5">{{ $job->salary }}</p>
                            @endif
                        </td>
                        <td class="p-4">
                            @if ($job->kenya_friendly)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                    <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Yes
                                </span>
                            @else
                                <span class="text-xs text-slate-400">No</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs">
                            <span class="font-bold text-slate-800 block">
                                {{ $job->posted_at ? \App\Support\Format::timeAgo($job->posted_at) : 'N/A' }}
                            </span>
                            <span class="text-slate-400 text-[11px]">
                                {{ $job->posted_at ? $job->posted_at->format('M d, Y H:i') : '' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ url('/admin/jobs/'.$job->id.'/edit') }}"
                                    class="btn-pop rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 shadow-2xs"
                                >
                                    Edit
                                </a>
                                <a
                                    href="{{ url('/jobs/'.$job->id) }}"
                                    target="_blank"
                                    class="btn-pop rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-teal-700 transition hover:bg-teal-50 shadow-2xs"
                                >
                                    View
                                </a>
                                <livewire:admin-remove-job-button :job-id="$job->id" :key="'jobs-remove-'.$job->id" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 text-sm">
                            No job listings match the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($totalPages > 1)
        <div class="mt-6 flex items-center justify-between gap-4 text-sm">
            <div>
                @if ($page > 1)
                    <a
                        href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null, 'page' => $page - 1])) }}"
                        class="font-bold text-teal-700 hover:underline"
                    >
                        &larr; Previous Page
                    </a>
                @endif
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Page {{ $page }} of {{ $totalPages }} ({{ number_format($total) }} listings)
            </span>
            <div>
                @if ($page < $totalPages)
                    <a
                        href="{{ url('/admin/jobs') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null, 'page' => $page + 1])) }}"
                        class="font-bold text-teal-700 hover:underline"
                    >
                        Next Page &rarr;
                    </a>
                @endif
            </div>
        </div>
    @endif
</x-layouts.admin>
