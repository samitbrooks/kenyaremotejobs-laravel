@php
    $jobsUrl = function (array $changes = []) use ($source, $q, $order, $kenyaFriendly, $category, $groupBy): string {
        $parameters = array_filter(array_merge([
            'source' => $source,
            'q' => $q,
            'order' => $order !== 'newest' ? $order : null,
            'kenya_friendly' => $kenyaFriendly ? 'true' : null,
            'category' => $category,
            'group_by' => $groupBy !== 'none' ? $groupBy : null,
        ], $changes), fn (mixed $value): bool => $value !== null && $value !== '');

        return url('/admin/jobs').($parameters ? '?'.http_build_query($parameters) : '');
    };
@endphp

<x-layouts.admin title="Admin: Jobs — KenyaRemoteJobs">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Manage Jobs</h1>
            <p class="mt-1 text-sm text-slate-500">
                Explore, inspect, edit, or remove synced and manually posted listings.
            </p>
        </div>
        <livewire:admin-add-job-form />
    </div>

    <div class="mt-6">
        <p class="mb-2 text-xs font-medium text-slate-500">Sources</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $jobsUrl(['source' => null]) }}" @if ($source === '') aria-current="page" @endif class="rounded-lg border px-3 py-1.5 text-xs font-medium {{ $source === '' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">All Sources</a>
            @foreach ($sources as $sourceName => $sourceCount)
                <a href="{{ $jobsUrl(['source' => $sourceName]) }}" @if ($source === $sourceName) aria-current="page" @endif class="rounded-lg border px-3 py-1.5 text-xs font-medium {{ $source === $sourceName ? 'border-[#3444da] bg-[#eef0fe] text-[#3444da]' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">{{ $sourceName }} <span class="ml-1 tabular-nums opacity-70">{{ $sourceCount }}</span></a>
            @endforeach
        </div>
    </div>

    <form action="{{ url('/admin/jobs') }}" method="GET" class="mt-5 grid grid-cols-1 items-end gap-4 rounded-xl border border-slate-200 bg-white p-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2">
            <label for="jobs-search" class="mb-1.5 block text-xs font-medium text-slate-600">Search jobs</label>
            <input id="jobs-search" type="search" name="q" value="{{ $q }}" placeholder="Title, company or tag" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="jobs-source" class="mb-1.5 block text-xs font-medium text-slate-600">Source</label>
            <select id="jobs-source" name="source" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All Sources</option>
                @foreach ($sources as $sourceName => $sourceCount)
                    <option value="{{ $sourceName }}" @selected($source === $sourceName)>{{ $sourceName }} ({{ $sourceCount }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="jobs-category" class="mb-1.5 block text-xs font-medium text-slate-600">Category</label>
            <select id="jobs-category" name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories as $categoryKey => $categoryLabel)
                    <option value="{{ $categoryKey }}" @selected($category === $categoryKey)>{{ $categoryLabel }} ({{ $categoryCounts[$categoryKey] }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="jobs-order" class="mb-1.5 block text-xs font-medium text-slate-600">Order within groups</label>
            <select id="jobs-order" name="order" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @foreach (['newest' => 'Newest posted first', 'oldest' => 'Oldest posted first', 'recent_sync' => 'Recently synced / updated', 'title' => 'Alphabetical by title'] as $value => $label)
                    <option value="{{ $value }}" @selected($order === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="jobs-group" class="mb-1.5 block text-xs font-medium text-slate-600">Group by</label>
            <select id="jobs-group" name="group_by" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="none" @selected($groupBy === 'none')>No grouping</option>
                <option value="category" @selected($groupBy === 'category')>Category</option>
                <option value="source" @selected($groupBy === 'source')>Source</option>
            </select>
        </div>
        <label class="flex items-center gap-2 py-2 text-xs text-slate-600">
            <input type="checkbox" name="kenya_friendly" value="true" @checked($kenyaFriendly) class="rounded border-slate-300 text-[#3444da]">
            Kenya-friendly matches only
        </label>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-[#3444da] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2633bf]">Apply filters</button>
            <a href="{{ url('/admin/jobs') }}" class="text-xs text-slate-500 hover:underline">Reset filters</a>
        </div>
        <p class="text-xs leading-relaxed text-slate-500 sm:col-span-2 lg:col-span-4">Categories are assigned automatically from job titles and tags. Category counts follow the current search, source and Kenya-friendly filters. Grouping covers all matching jobs before pagination.</p>
    </form>

    <p class="mt-4 text-xs font-medium text-slate-500">{{ number_format($total) }} listing(s) match.</p>

    {{-- Jobs Table --}}
    <div class="mt-3 overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="w-full min-w-[760px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="p-4">Title & Company</th>
                    <th class="p-4">Source</th>
                    <th class="p-4">Category</th>
                    <th class="p-4">Tier / Pay</th>
                    <th class="p-4">Kenya-Friendly</th>
                    <th class="p-4">Posting Date</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            @forelse ($jobGroups as $groupLabel => $groupJobs)
                <tbody class="divide-y divide-slate-100">
                    @if ($groupBy !== 'none')
                        <tr class="border-y border-slate-200 bg-[#eef0fe]">
                            <th colspan="7" scope="rowgroup" class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $groupLabel }} <span class="ml-2 text-xs font-normal text-slate-500">{{ $groupTotals[$groupLabel] }} matching &middot; {{ $groupJobs->count() }} on this page</span>
                            </th>
                        </tr>
                    @endif
                    @foreach ($groupJobs as $job)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-4 min-w-[240px]">
                            <a href="{{ url('/jobs/'.$job->id) }}" target="_blank" class="font-bold text-slate-900 hover:text-teal-700 hover:underline">
                                {{ $job->title }}
                            </a>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ $job->company }} &middot; {{ $job->remote_type }}</p>
                            <p class="mt-1 text-[11px] text-slate-500">{{ $job->isDirectAts() ? 'Employer application page' : 'Hiring platform / listing site' }} &middot; {{ $job->isCurrentlyVisible() ? 'Current on site' : 'Archived on site' }}</p>
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
                        <td class="p-4 text-xs text-slate-600">{{ $categories[$jobCategories[$job->id]] }}</td>
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
                            @if ($job->last_seen_at)
                                <p class="mt-1 text-[11px] text-[#3444da]">Seen in employer feed {{ $job->last_seen_at->timezone('Africa/Nairobi')->format('j M, H:i') }} EAT</p>
                            @endif
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
                    @endforeach
                </tbody>
            @empty
                <tbody><tr><td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">No jobs match these filters. Try another category or reset the filters.</td></tr></tbody>
            @endforelse
        </table>
    </div>

    {{-- Pagination --}}
    @if ($totalPages > 1)
        <div class="mt-6 flex items-center justify-between gap-4 text-sm">
            <div>
                @if ($page > 1)
                    <a
                        href="{{ $jobsUrl(['page' => $page - 1]) }}"
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
                        href="{{ $jobsUrl(['page' => $page + 1]) }}"
                        class="font-bold text-teal-700 hover:underline"
                    >
                        Next Page &rarr;
                    </a>
                @endif
            </div>
        </div>
    @endif
</x-layouts.admin>
