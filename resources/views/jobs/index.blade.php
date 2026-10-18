@php
    $isStudentAudience = $audience === 'students';
    $title = $isStudentAudience
        ? 'Online & Remote Jobs for Students in Kenya (2026)'
        : ($audience ? (\App\Support\Audience::LABELS[$audience] ?? $audience).' jobs' : 'Browse Online & Remote Jobs Open to Kenya');
    $description = $isStudentAudience
        ? 'Explore verified remote jobs, data annotation gigs, and part-time online roles open to Kenyan university and college students. Flexible hours around classes, weekly M-Pesa payouts, and no prior degree required.'
        : 'Search and filter online and remote jobs from verified global employers and distributed teams — filter by category, remote type, or Kenya-Friendly Match to find roles realistically open to you.';

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

    $canonicalUrl = $page > 1 ? url('/jobs').'?page='.$page : url('/jobs');
    if ($audience) {
        $canonicalUrl = url('/jobs').'?audience='.$audience.($page > 1 ? '&page='.$page : '');
    }
@endphp

<x-layouts.app :title="$title" :description="$description" :canonical="$canonicalUrl">
    <script type="application/ld+json">{!! \App\Support\Seo::itemListJsonLd($jobs, $title, $description) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::jobsIndexBreadcrumbJsonLd($audience ? $title : null, $canonicalUrl) !!}</script>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <h1 class="flex items-center gap-2.5 text-3xl font-extrabold text-slate-900 tracking-tight">
            @if ($audience)
                <x-icon :name="\App\Support\Audience::ICONS[$audience] ?? 'globe'" class="h-7 w-7 text-[#3444da]" />
            @endif
            {{ $title }}
        </h1>
        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ $total }} listings &middot; 100% transparent employer details &middot; Verified remote roles open to Kenyan applicants.
        </p>

        @if ($isStudentAudience)
            <div class="mt-6 rounded-3xl border border-[#e6e8f2] bg-white p-6 sm:p-8 shadow-[0_4px_20px_rgba(15,23,42,0.04)]">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3 py-1 text-xs font-bold text-[#3444da]">
                        Campus & Student Portal
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700">
                        <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" /> 100% Flexible Hours
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700">
                        <x-icon name="lightning" class="h-3.5 w-3.5 text-[#3444da]" /> M-Pesa Weekly / Bi-Weekly Payouts
                    </span>
                </div>

                <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Legit Online Jobs for Kenyan Students (Data Annotation & AI Training)
                </h2>

                <p class="mt-2.5 text-sm sm:text-base text-slate-600 leading-relaxed max-w-3xl">
                    Whether you are at the University of Nairobi, Kenyatta University, JKUAT, Strathmore, Moi, or on campus anywhere in Kenya, international companies hire Kenyan students for <strong>AI data annotation</strong>, <strong>RLHF prompt evaluation</strong>, <strong>audio transcription</strong>, and <strong>flexible virtual assistance</strong>. Work at your own pace between lectures and earn in USD.
                </p>

                {{-- Quick Student Filters --}}
                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 mr-1">Quick Filters:</span>
                    <a href="{{ url('/jobs?audience=students') }}" class="rounded-full px-3.5 py-1 text-xs font-bold transition {{ $q === '' ? 'bg-[#3444da] text-white shadow-xs' : 'bg-[#eef0fe] text-[#3444da] hover:bg-[#3444da] hover:text-white' }}">
                        All Student Jobs ({{ $total }})
                    </a>
                    <a href="{{ url('/jobs?audience=students&q=annotation') }}" class="rounded-full px-3.5 py-1 text-xs font-bold transition {{ str_contains(strtolower($q), 'annotation') ? 'bg-[#3444da] text-white shadow-xs' : 'bg-[#eef0fe] text-[#3444da] hover:bg-[#3444da] hover:text-white' }}">
                        Data Annotation & AI
                    </a>
                    <a href="{{ url('/jobs?audience=students&q=transcription') }}" class="rounded-full px-3.5 py-1 text-xs font-bold transition {{ str_contains(strtolower($q), 'transcription') ? 'bg-[#3444da] text-white shadow-xs' : 'bg-[#eef0fe] text-[#3444da] hover:bg-[#3444da] hover:text-white' }}">
                        Audio Transcription
                    </a>
                    <a href="{{ url('/jobs?audience=students&q=writing') }}" class="rounded-full px-3.5 py-1 text-xs font-bold transition {{ str_contains(strtolower($q), 'writing') ? 'bg-[#3444da] text-white shadow-xs' : 'bg-[#eef0fe] text-[#3444da] hover:bg-[#3444da] hover:text-white' }}">
                        Content & Research
                    </a>
                    <a href="{{ url('/jobs?audience=students&q=assistant') }}" class="rounded-full px-3.5 py-1 text-xs font-bold transition {{ str_contains(strtolower($q), 'assistant') ? 'bg-[#3444da] text-white shadow-xs' : 'bg-[#eef0fe] text-[#3444da] hover:bg-[#3444da] hover:text-white' }}">
                        Virtual Assistant
                    </a>
                    <a href="{{ url('/remote-jobs/online-jobs-for-students-kenya') }}" class="rounded-full bg-slate-900 text-white px-3.5 py-1 text-xs font-bold hover:bg-slate-800 transition sm:ml-auto">
                        Student FAQ & Salary Guide &rarr;
                    </a>
                </div>
            </div>
        @endif

        @if (auth()->user()?->onTrial())
            <div class="mt-4 rounded-2xl border border-[#c7ccfb] bg-[#eef0fe]/60 p-4 shadow-xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 text-xs text-slate-800 font-semibold">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-[#3444da] animate-pulse shrink-0"></span>
                    <span><strong>24-Hour Free Pass Active:</strong> You have unrestricted access to browse, search, and apply to all roles until <strong>{{ auth()->user()->trial_ends_at?->format('M j, g:i A') }}</strong> ({{ auth()->user()->trialRemainingHuman() }} left).</span>
                </div>
                <a href="{{ route('pricing') }}" class="btn-pop rounded-full bg-[#3444da] px-4 py-1.5 text-xs font-bold text-white hover:bg-[#2633bf] shadow-xs transition">
                    Lock In Pro Membership &rarr;
                </a>
            </div>
        @endif

        @if ($audience)
            <p class="mt-2 text-sm">
                <a href="{{ url('/jobs') }}" class="font-bold text-[#3444da] hover:underline">&larr; Clear filter, see all jobs</a>
            </p>
        @endif

        @if ($sortByMatch)
            <p class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-[#3444da]">
                <x-icon name="sparkle" class="h-4 w-4 text-[#3444da]" /> Sorted by your best matches &mdash; <a href="{{ url('/match') }}" class="underline">update your profile</a>
            </p>
        @else
            <p class="mt-2 text-sm">
                <a href="{{ url('/match') }}" class="inline-flex items-center gap-1 font-bold text-[#3444da] hover:underline">
                    <x-icon name="sparkle" class="h-4 w-4 text-[#3444da]" /> Find your best-matching jobs &rarr;
                </a>
            </p>
        @endif


        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-[#c7ccfb] bg-gradient-to-r from-[#eef0fe] via-white to-[#eef0fe] p-4 sm:p-5 text-xs sm:text-sm text-slate-800 shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#3444da] text-white shadow-xs">
                    <x-icon name="sparkle" class="h-4 w-4" />
                </span>
                <p class="leading-relaxed">
                    <strong class="font-bold text-slate-900">Pro Membership:</strong> Instant apply access to verified remote jobs & direct employer listings &mdash; beat 500+ applicants with priority delivery & AI CV tailoring.
                </p>
            </div>
            <a href="{{ url('/pricing') }}" class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-5 py-2.5 text-xs font-bold text-white transition shrink-0 shadow-xs">
                Unlock All Jobs &rarr;
            </a>
        </div>

        {{-- Telegram Official Alerts Card --}}
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[#c7ccfb] bg-[#eef0fe]/80 p-3.5 sm:p-4 text-xs sm:text-sm text-slate-800 shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#3444da] text-white shadow-2xs">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/></svg>
                </span>
                <p class="leading-relaxed">
                    <strong class="font-bold text-slate-900">Join our Official Telegram Channel:</strong> Get instant alerts the moment fresh remote jobs open to Kenya drop &bull; Pre-screened for UTC+3 &bull; 100% Free.
                </p>
            </div>
            <a href="{{ config('site.telegram_url', 'https://t.me/kenyaremotejobs') }}" target="_blank" rel="noopener noreferrer" class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-[#3444da] px-4 py-2 text-xs font-bold text-white hover:bg-[#2633bf] transition shrink-0 shadow-2xs">
                <span>Join @kenyaremotejobs</span> &rarr;
            </a>
        </div>

        <form action="{{ url('/jobs') }}" method="GET" class="mt-6 grid grid-cols-1 gap-3 rounded-[20px] border border-[#e6e8f2] bg-white p-4 sm:p-5 shadow-xs sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-center">
            <div class="relative flex items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-slate-400" />
                <input type="text" name="q" value="{{ $q }}" placeholder="Search job titles, skills, companies…" class="w-full rounded-full border border-slate-200/80 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#3444da] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 transition">
            </div>

            <select name="tag" class="rounded-full border border-slate-200/80 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-700 focus:border-[#3444da] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 transition cursor-pointer">
                <option value="">All categories</option>
                @foreach ($tags as $t)
                    <option value="{{ $t }}" @selected($tag === $t)>{{ $t }}</option>
                @endforeach
            </select>

            <select name="remoteType" class="rounded-full border border-slate-200/80 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-700 focus:border-[#3444da] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 transition cursor-pointer">
                <option value="">Any remote type</option>
                <option value="remote" @selected($remoteType === 'remote')>Remote</option>
                <option value="full-time" @selected($remoteType === 'full-time')>Full-time</option>
                <option value="contract" @selected($remoteType === 'contract')>Contract</option>
            </select>

            <button type="submit" class="btn-pop rounded-full bg-[#3444da] hover:bg-[#2633bf] px-7 py-2.5 text-sm font-bold text-white shadow-md shadow-[#3444da]/20 transition">
                Filter
            </button>

            <div class="flex flex-wrap gap-2 sm:col-span-4 pt-1">
                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-slate-50/70 px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-[#eef0fe] hover:border-[#c7ccfb] hover:text-[#3444da] cursor-pointer transition shadow-2xs">
                    <input type="checkbox" name="kenyaFriendly" value="true" @checked($kenyaFriendly) class="h-4 w-4 rounded-full border-slate-300 text-[#3444da] focus:ring-[#3444da]">
                    <span>Show only Kenya-Friendly Matches</span>
                    <x-icon.kenya-flag class="h-3.5 w-3.5" />
                </label>
                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-slate-50/70 px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-[#eef0fe] hover:border-[#c7ccfb] hover:text-[#3444da] cursor-pointer transition shadow-2xs">
                    <input type="checkbox" name="freeOnly" value="true" @checked($freeOnly) class="h-4 w-4 rounded-full border-slate-300 text-[#3444da] focus:ring-[#3444da]">
                    <span>Direct employer listings only</span>
                    <x-icon name="announce" class="h-3.5 w-3.5 text-[#3444da]" />
                </label>
            </div>
        </form>

        @if ($jobs->isEmpty())
            <p class="mt-8 rounded-2xl border border-slate-200/70 bg-white p-12 text-center text-slate-500 shadow-xs">
                No jobs match those filters yet. Try widening your search.
            </p>
        @else
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($jobs as $i => $job)
                    <x-reveal :delay="min($i, 9) * 30">
                        <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                    </x-reveal>
                @endforeach
            </div>
        @endif

        @if ($totalPages > 1)
            <div class="mt-12 flex flex-wrap items-center justify-center gap-2">
                @if ($page > 1)
                    <a href="{{ $buildPageHref($page - 1) }}" class="btn-pop rounded-full border border-slate-200/80 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-[#eef0fe] hover:border-[#3444da] hover:text-[#3444da] transition" aria-label="Previous page">
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
                            class="btn-pop rounded-full px-4 py-2 text-sm font-bold transition {{ $item === $page ? 'bg-[#3444da] text-white shadow-sm shadow-[#3444da]/25' : 'border border-slate-200/80 bg-white text-slate-700 shadow-2xs hover:bg-[#eef0fe] hover:border-[#3444da] hover:text-[#3444da]' }}"
                        >
                            {{ $item }}
                        </a>
                    @endif
                @endforeach

                @if ($page < $totalPages)
                    <a href="{{ $buildPageHref($page + 1) }}" class="btn-pop rounded-full border border-slate-200/80 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-[#eef0fe] hover:border-[#3444da] hover:text-[#3444da] transition" aria-label="Next page">
                        Next &rarr;
                    </a>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
