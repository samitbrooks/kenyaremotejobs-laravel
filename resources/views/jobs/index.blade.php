@php
    $title = $audience ? (\App\Support\Audience::LABELS[$audience] ?? $audience).' jobs' : 'Browse Online & Remote Jobs Open to Kenya';
    $description = 'Search and filter online and remote jobs from Arbeitnow, RemoteOK, Remotive, Jobicy, and Himalayas — filter by category, remote type, or Kenya-Friendly Match to find roles realistically open to you.';

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
@endphp

<x-layouts.app :title="$title" :description="$description">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <h1 class="flex items-center gap-2 text-3xl font-bold">
            @if ($audience)
                <x-icon :name="\App\Support\Audience::ICONS[$audience] ?? 'globe'" class="h-7 w-7" />
            @endif
            {{ $title }}
        </h1>
        <p class="mt-1 text-foreground/60">
            {{ $total }} listings &middot; {{ $totalFree }} fully free to view right now &middot; titles, companies, and short descriptions are always free to browse.
        </p>

        @if ($audience)
            <p class="mt-2 text-sm">
                <a href="{{ url('/jobs') }}" class="font-semibold text-sunrise-600 hover:underline">&larr; Clear filter, see all jobs</a>
            </p>
        @endif

        @if ($sortByMatch)
            <p class="mt-2 flex items-center gap-1 text-sm font-medium text-horizon-700">
                <x-icon name="sparkle" class="h-4 w-4" /> Sorted by your best matches &mdash; <a href="{{ url('/match') }}" class="underline">update your profile</a>
            </p>
        @else
            <p class="mt-2 text-sm">
                <a href="{{ url('/match') }}" class="inline-flex items-center gap-1 font-semibold text-sunrise-600 hover:underline">
                    <x-icon name="sparkle" class="h-4 w-4" /> Find your best-matching jobs &rarr;
                </a>
            </p>
        @endif

        <form action="{{ url('/jobs') }}" method="GET" class="mt-6 grid gap-3 rounded-2xl border border-black/5 bg-white p-4 shadow-sm sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-center">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search job titles, skills, companies…" class="rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400">

            <select name="tag" class="rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                <option value="">All categories</option>
                @foreach ($tags as $t)
                    <option value="{{ $t }}" @selected($tag === $t)>{{ $t }}</option>
                @endforeach
            </select>

            <select name="remoteType" class="rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                <option value="">Any remote type</option>
                <option value="remote" @selected($remoteType === 'remote')>Remote</option>
                <option value="full-time" @selected($remoteType === 'full-time')>Full-time</option>
                <option value="contract" @selected($remoteType === 'contract')>Contract</option>
            </select>

            <button type="submit" class="btn-pop rounded-lg bg-sunrise-500 px-5 py-2.5 font-semibold text-white transition hover:bg-sunrise-600">
                Filter
            </button>

            <div class="flex flex-wrap gap-x-6 gap-y-2 sm:col-span-4">
                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="kenyaFriendly" value="true" @checked($kenyaFriendly) class="h-4 w-4 accent-emerald-600">
                    Show only Kenya-Friendly Matches <x-icon.kenya-flag class="h-4 w-4" />
                </label>
                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="freeOnly" value="true" @checked($freeOnly) class="h-4 w-4 accent-horizon-600">
                    Show only jobs free to view now <x-icon name="unlock" class="h-4 w-4" />
                </label>
            </div>
        </form>

        @if ($jobs->isEmpty())
            <p class="mt-10 rounded-xl bg-horizon-50 p-8 text-center text-foreground/60">
                No jobs match those filters yet. Try widening your search.
            </p>
        @else
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($jobs as $i => $job)
                    <x-reveal :delay="min($i, 8) * 60" class="h-full">
                        <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                    </x-reveal>
                @endforeach
            </div>
        @endif

        @if ($totalPages > 1)
            <div class="mt-10 flex flex-wrap justify-center gap-2">
                @for ($p = 1; $p <= $totalPages; $p++)
                    <a href="{{ $buildPageHref($p) }}" class="rounded-full px-3.5 py-1.5 text-sm font-medium {{ $p === $page ? 'bg-sunrise-500 text-white' : 'bg-white text-foreground/70 hover:bg-horizon-50' }}">
                        {{ $p }}
                    </a>
                @endfor
            </div>
        @endif
    </div>
</x-layouts.app>
