<x-layouts.admin title="Admin Dashboard — KenyaRemoteJobs">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">
                Last synced {{ $lastSyncedAt ? \App\Support\Format::timeAgo($lastSyncedAt) : 'never' }}.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <livewire:admin-add-job-form />
            <livewire:admin-resync-button />
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jobs Live</p>
            <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalJobs) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-teal-700">Kenya-Friendly Matches</p>
            <p class="mt-1 text-3xl font-extrabold text-teal-600">{{ number_format($kenyaFriendlyTotal) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Registered Users</p>
            <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Pro Subscribers</p>
            <p class="mt-1 text-3xl font-extrabold text-emerald-700">{{ number_format($subscribed) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Visitors (Last 7 Days)</p>
            <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($pageViews['last7Days']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Visitors (Last 30 Days)</p>
            <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($pageViews['last30Days']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs sm:col-span-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Page Views (All-Time)</p>
            <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($pageViews['total']) }}</p>
        </div>
    </div>

    {{-- Google Search Console SEO Analytics --}}
    <div class="mt-10 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-50 text-teal-700">
                        <x-icon name="sparkle" class="h-4 w-4" />
                    </span>
                    <h2 class="text-xl font-extrabold text-slate-900">Google Search Console Performance</h2>
                </div>
                <p class="mt-1 text-xs text-slate-500">
                    Organic search metrics &amp; search queries from Google for property: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-800">{{ $gscSiteUrl }}</code>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if (!empty($gscMetrics['success']))
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-800">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Connected to GSC
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-800">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        GSC Setup Required
                    </span>
                @endif
            </div>
        </div>

        @if (!empty($gscMetrics['success']))
            {{-- Performance Metrics Row --}}
            <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/60">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Organic Clicks (28d)</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($gscMetrics['totals']['clicks'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/60">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Impressions</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($gscMetrics['totals']['impressions'] ?? 0) }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/60">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Average CTR</p>
                    <p class="mt-1 text-2xl font-black text-teal-600">{{ $gscMetrics['totals']['ctr'] ?? 0 }}%</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/60">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Average Position</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ $gscMetrics['totals']['position'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Top Search Queries Table --}}
            @if (!empty($gscMetrics['rows']))
                <div class="mt-6 overflow-x-auto">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Top Organic Search Queries</h3>
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80">
                            <tr>
                                <th class="px-4 py-2.5">Search Query</th>
                                <th class="px-4 py-2.5 text-right">Clicks</th>
                                <th class="px-4 py-2.5 text-right">Impressions</th>
                                <th class="px-4 py-2.5 text-right">CTR</th>
                                <th class="px-4 py-2.5 text-right">Avg Position</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($gscMetrics['rows'] as $row)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-4 py-2.5 font-bold text-slate-800">{{ $row['keys'][0] ?? '(not set)' }}</td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-slate-900">{{ number_format($row['clicks'] ?? 0) }}</td>
                                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($row['impressions'] ?? 0) }}</td>
                                    <td class="px-4 py-2.5 text-right text-teal-600 font-semibold">{{ round(($row['ctr'] ?? 0) * 100, 2) }}%</td>
                                    <td class="px-4 py-2.5 text-right text-slate-700">{{ round($row['position'] ?? 0, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="mt-6 rounded-2xl bg-amber-50/60 border border-amber-200/70 p-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white font-bold text-sm">!</span>
                    <div class="text-xs text-amber-950 space-y-1.5">
                        <p class="font-bold text-sm text-amber-900">Google Search Console Integration Status</p>
                        <p class="text-slate-600">
                            {{ $gscMetrics['message'] ?? 'Service Account authorization is being connected.' }}
                        </p>
                        <div class="pt-2">
                            <p class="font-semibold text-slate-800">To finalize the connection:</p>
                            <ol class="mt-1 list-decimal list-inside space-y-1 text-slate-600">
                                <li>Enable the <strong>Google Search Console API</strong> on project <code class="font-mono text-slate-800">34788030047</code>.</li>
                                <li>Add the Service Account (<code class="font-mono text-slate-800">google-indexing-bot@gen-lang-client-0673392033.iam.gserviceaccount.com</code>) to your property in <a href="https://search.google.com/search-console/users" target="_blank" rel="noopener" class="font-bold text-teal-700 underline">Search Console Users</a>.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Main Jobs Explorer with Sources & Posting Order Filter --}}
    <div class="mt-10 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <span>All Job Listings & Sources</span>
                    <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-bold text-slate-600">
                        {{ number_format($total) }} total
                    </span>
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Filter by origin source or change the posting order to review listings.
                </p>
            </div>
            @if ($source !== '' || $q !== '' || $order !== 'newest' || $kenyaFriendly)
                <a href="{{ url('/admin') }}" class="text-xs font-bold text-rose-600 hover:underline self-start sm:self-auto">
                    &times; Reset All Filters
                </a>
            @endif
        </div>

        {{-- Quick Source Filter Pills --}}
        <div class="mt-6">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Filter by Job Source:</p>
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ url('/admin') }}?{{ http_build_query(array_filter(['q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null])) }}"
                    class="rounded-full px-3.5 py-1 text-xs font-semibold transition {{ $source === '' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    All Sources ({{ number_format($totalJobs) }})
                </a>
                @foreach ($sources as $srcName => $count)
                    <a
                        href="{{ url('/admin') }}?{{ http_build_query(array_filter(['source' => $srcName, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null])) }}"
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

        {{-- Filter & Posting Order Controls Form --}}
        <form action="{{ url('/admin') }}" method="GET" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
            {{-- Search input --}}
            <div class="lg:col-span-2">
                <label for="search-input" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Search Keywords</label>
                <input
                    id="search-input"
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Search title, company, tag…"
                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
                />
            </div>

            {{-- Source dropdown --}}
            <div>
                <label for="source-select" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Source</label>
                <select
                    id="source-select"
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

            {{-- Posting Order filter dropdown --}}
            <div>
                <label for="order-select" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Posting Order</label>
                <select
                    id="order-select"
                    name="order"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
                >
                    <option value="newest" {{ $order === 'newest' ? 'selected' : '' }}>Newest Posted First</option>
                    <option value="oldest" {{ $order === 'oldest' ? 'selected' : '' }}>Oldest Posted First</option>
                    <option value="recent_sync" {{ $order === 'recent_sync' ? 'selected' : '' }}>Recently Synced / Updated</option>
                    <option value="title" {{ $order === 'title' ? 'selected' : '' }}>Alphabetical by Title</option>
                </select>
            </div>

            {{-- Filter Button & Toggle --}}
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-xs hover:bg-slate-800 transition"
                >
                    Apply Filters
                </button>
            </div>
        </form>

        {{-- Kenya-Friendly quick checkbox --}}
        <div class="mt-3 flex items-center gap-2">
            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                <input
                    type="checkbox"
                    name="kenya_friendly"
                    value="true"
                    {{ $kenyaFriendly ? 'checked' : '' }}
                    onchange="this.form ? this.form.submit() : (window.location.href = '{{ url('/admin') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null])) }}' + (this.checked ? '&kenya_friendly=true' : ''))"
                    class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                />
                <span>Show Only Kenya-Friendly Matches</span>
            </label>
        </div>

        {{-- Jobs Table --}}
        <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200/80 bg-white">
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
                                    <livewire:admin-remove-job-button :job-id="$job->id" :key="'dash-remove-'.$job->id" />
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
            <div class="mt-6 flex items-center justify-between gap-4 text-sm border-t border-slate-100 pt-4">
                <div>
                    @if ($page > 1)
                        <a
                            href="{{ url('/admin') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null, 'page' => $page - 1])) }}"
                            class="font-bold text-teal-700 hover:underline"
                        >
                            &larr; Previous Page
                        </a>
                    @endif
                </div>
                <span class="text-xs font-semibold text-slate-500">
                    Page {{ $page }} of {{ $totalPages }} ({{ number_format($total) }} total listings)
                </span>
                <div>
                    @if ($page < $totalPages)
                        <a
                            href="{{ url('/admin') }}?{{ http_build_query(array_filter(['source' => $source ?: null, 'q' => $q ?: null, 'order' => $order !== 'newest' ? $order : null, 'kenya_friendly' => $kenyaFriendly ? 'true' : null, 'page' => $page + 1])) }}"
                            class="font-bold text-teal-700 hover:underline"
                        >
                            Next Page &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom Links --}}
    <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200/80 pt-6">
        <a href="{{ url('/admin/users') }}" class="font-bold text-sm text-teal-700 hover:underline">
            Manage Registered Users &rarr;
        </a>
        <a href="{{ url('/admin/jobs') }}" class="font-bold text-sm text-teal-700 hover:underline">
            Go to Dedicated Jobs Manager &rarr;
        </a>
    </div>
</x-layouts.admin>
