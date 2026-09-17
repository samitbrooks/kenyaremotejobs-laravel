@php
    $title = $audience ? (\App\Support\Audience::LABELS[$audience] ?? $audience).' jobs' : 'Browse Online & Remote Jobs Open to Kenya';
    $description = 'Search and filter online and remote jobs from verified global employers and distributed teams — filter by category, remote type, or Kenya-Friendly Match to find roles realistically open to you.';

    $buildPageHref = function (int $targetPage) use ($q, $tag, $remoteType, $kenyaFriendly, $freeOnly, $audience) {
        $params = array_filter([
            'q' => $q ?: null,
            'tag' => $tag ?: null,
            'remoteType' => $remoteType ?: null,
            'kenyaFriendly' => $kenyaFriendly ? 'true' : null,
            'freeOnly' => $freeOnly ? 'true' : null,
            'audience' => $audience,
            'page' => $targetPage,
        ]);

        return url('/jobs').'?'.http_build_query($params);
    };

    // Always show the first and last page plus a small window around the
    // current page, collapsing any gap into a single "…" — keeps this
    // readable even at 40+ pages instead of listing every page number.
    $pageItems = [];
    $window = 1;
    $prevShown = null;
    for ($p = 1; $p <= $totalPages; $p++) {
        if ($p === 1 || $p === $totalPages || abs($p - $page) <= $window) {
            if ($prevShown !== null && $p - $prevShown > 1) {
                $pageItems[] = '…';
            }
            $pageItems[] = $p;
            $prevShown = $p;
        }
    }
@endphp

<x-layouts.app :title="$title" :description="$description" :canonical="url('/jobs')">
    <script type="application/ld+json">{!! \App\Support\Seo::itemListJsonLd($jobs, $title, $description) !!}</script>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <h1 class="flex items-center gap-2.5 text-3xl font-extrabold text-slate-900">
            @if ($audience)
                <x-icon :name="\App\Support\Audience::ICONS[$audience] ?? 'globe'" class="h-7 w-7 text-teal-600" />
            @endif
            {{ $title }}
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            {{ $total }} listings &middot; 100% transparent employer details &middot; Verified remote roles open to Kenyan applicants.
        </p>

        @if ($audience)
            <p class="mt-2 text-sm">
                <a href="{{ url('/jobs') }}" class="font-bold text-teal-700 hover:underline">&larr; Clear filter, see all jobs</a>
            </p>
        @endif

        @if ($sortByMatch)
            <p class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-teal-700">
                <x-icon name="sparkle" class="h-4 w-4 text-teal-600" /> Sorted by your best matches &mdash; <a href="{{ url('/match') }}" class="underline">update your profile</a>
            </p>
        @else
            <p class="mt-2 text-sm">
                <a href="{{ url('/match') }}" class="inline-flex items-center gap-1 font-bold text-teal-700 hover:underline">
                    <x-icon name="sparkle" class="h-4 w-4 text-teal-600" /> Find your best-matching jobs &rarr;
                </a>
            </p>
        @endif

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-amber-200/80 bg-gradient-to-r from-amber-50/90 via-orange-50/40 to-amber-50/90 p-5 sm:p-6 text-xs sm:text-sm text-slate-800 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-xs">
                    <x-icon name="sparkle" class="h-4 w-4" />
                </span>
                <p class="leading-relaxed">
                    <strong class="font-bold text-slate-900">Pro Early Access:</strong> Full access to apply to all 800+ remote jobs immediately &mdash; no 48-hour wait on newly posted roles.
                </p>
            </div>
            <a href="{{ url('/pricing') }}" class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition shrink-0 shadow-sm">
                Unlock All Jobs &rarr;
            </a>
        </div>

        <form action="{{ url('/jobs') }}" method="GET" class="mt-6 grid grid-cols-1 gap-3.5 rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-center">
            <div class="relative flex items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-slate-400" />
                <input type="text" name="q" value="{{ $q }}" placeholder="Search job titles, skills, companies…" class="w-full rounded-full border border-slate-200/90 bg-slate-50/60 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition">
            </div>

            <select name="tag" class="rounded-full border border-slate-200/90 bg-slate-50/60 px-4 py-2.5 text-sm text-slate-700 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition cursor-pointer">
                <option value="">All categories</option>
                @foreach ($tags as $t)
                    <option value="{{ $t }}" @selected($tag === $t)>{{ $t }}</option>
                @endforeach
            </select>

            <select name="remoteType" class="rounded-full border border-slate-200/90 bg-slate-50/60 px-4 py-2.5 text-sm text-slate-700 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition cursor-pointer">
                <option value="">Any remote type</option>
                <option value="remote" @selected($remoteType === 'remote')>Remote</option>
                <option value="full-time" @selected($remoteType === 'full-time')>Full-time</option>
                <option value="contract" @selected($remoteType === 'contract')>Contract</option>
            </select>

            <button type="submit" class="btn-pop rounded-full bg-teal-600 hover:bg-teal-700 px-7 py-2.5 text-sm font-bold text-white shadow-md shadow-teal-600/20 transition">
                Filter
            </button>

            <div class="flex flex-wrap gap-2.5 sm:col-span-4 pt-1">
                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-slate-50/80 px-4 py-1.5 text-xs font-semibold text-slate-700 hover:bg-teal-50 hover:border-teal-300 hover:text-teal-800 cursor-pointer transition shadow-2xs">
                    <input type="checkbox" name="kenyaFriendly" value="true" @checked($kenyaFriendly) class="h-4 w-4 rounded-full border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>Show only Kenya-Friendly Matches</span>
                    <x-icon.kenya-flag class="h-3.5 w-3.5" />
                </label>
                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-slate-50/80 px-4 py-1.5 text-xs font-semibold text-slate-700 hover:bg-teal-50 hover:border-teal-300 hover:text-teal-800 cursor-pointer transition shadow-2xs">
                    <input type="checkbox" name="freeOnly" value="true" @checked($freeOnly) class="h-4 w-4 rounded-full border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>Direct employer listings only</span>
                    <x-icon name="announce" class="h-3.5 w-3.5 text-emerald-600" />
                </label>
            </div>
        </form>

        @if ($jobs->isEmpty())
            <p class="mt-10 rounded-3xl border border-slate-200/80 bg-white p-12 text-center text-slate-500 shadow-sm">
                No jobs match those filters yet. Try widening your search.
            </p>
        @else
            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($jobs as $i => $job)
                    <x-reveal :delay="min($i, 8) * 60" class="h-full">
                        <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                    </x-reveal>
                @endforeach
            </div>
        @endif

        @if ($totalPages > 1)
            <div class="mt-12 flex flex-wrap items-center justify-center gap-2">
                @if ($page > 1)
                    <a href="{{ $buildPageHref($page - 1) }}" class="btn-pop rounded-full border border-slate-200/80 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 transition" aria-label="Previous page">
                        &larr; Prev
                    </a>
                @endif

                @foreach ($pageItems as $item)
                    @if ($item === '…')
                        <span class="px-2 text-sm text-slate-400" aria-hidden="true">&hellip;</span>
                    @else
                        <a
                            href="{{ $buildPageHref($item) }}"
                            @if ($item === $page) aria-current="page" @endif
                            class="btn-pop rounded-full px-4 py-2 text-sm font-bold transition {{ $item === $page ? 'bg-teal-600 text-white shadow-sm shadow-teal-600/25' : 'border border-slate-200/80 bg-white text-slate-700 shadow-2xs hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700' }}"
                        >
                            {{ $item }}
                        </a>
                    @endif
                @endforeach

                @if ($page < $totalPages)
                    <a href="{{ $buildPageHref($page + 1) }}" class="btn-pop rounded-full border border-slate-200/80 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 transition" aria-label="Next page">
                        Next &rarr;
                    </a>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
