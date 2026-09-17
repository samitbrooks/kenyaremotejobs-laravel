@php
    $categories = [
        ['icon' => 'code', 'label' => 'Engineering & Development', 'q' => 'engineer'],
        ['icon' => 'headset', 'label' => 'Customer Support', 'q' => 'support'],
        ['icon' => 'trending-up', 'label' => 'Sales', 'q' => 'sales'],
        ['icon' => 'megaphone', 'label' => 'Marketing', 'q' => 'marketing'],
        ['icon' => 'palette', 'label' => 'Design', 'q' => 'design'],
        ['icon' => 'pen', 'label' => 'Writing & Content', 'q' => 'content'],
    ];

    $trustFeatures = [
        ['icon' => 'clock', 'title' => 'Timezone overlap, checked', 'body' => 'We read every listing\'s stated hours and flag whether they realistically overlap East Africa Time — not just the word "remote."'],
        ['icon' => 'pin', 'title' => 'Location wording, scanned', 'body' => '"Work from anywhere" often means anywhere in one country. We catch the buried US-only, EU-only, or region-locked fine print.'],
        ['icon' => 'passport', 'title' => 'Visa restrictions, flagged', 'body' => 'Explicit visa-sponsorship or work-authorization limits are surfaced up front, so you\'re never a paragraph away from finding out you didn\'t qualify.'],
    ];

    $howItWorks = [
        ['title' => 'Search & filter', 'body' => 'Browse remote roles from vetted global companies and hiring employers, all in one place — 100% free to search and view company names.'],
        ['title' => 'Spot your match', 'body' => 'Every listing gets a Kenya-Friendly Match badge when it\'s realistically open to your timezone and location.'],
        ['title' => 'Early Access: Full Job Access', 'body' => 'Pro Early Access gives you full access to apply to all 800+ remote jobs immediately — bypassing the 48-hour wait, with 1-click AI CV tailoring.'],
    ];

    $testimonials = [
        ['quote' => 'I found a fully-remote support role that actually wanted someone in EAT hours. Game changer.', 'name' => 'Aisha, Nairobi'],
        ['quote' => 'The Kenya-Friendly badge saved me hours of reading fine print about visa requirements.', 'name' => 'Brian, Kisumu'],
        ['quote' => 'Finally a job board that doesn\'t assume everyone remote is in San Francisco.', 'name' => 'Faith, Mombasa'],
    ];

    $faqs = [
        ['question' => 'What are online jobs in Kenya?', 'answer' => '"Online jobs" and "remote jobs" mean the same thing — work you do from a laptop at home, in a café, or anywhere with internet, instead of commuting to a physical office. Kenyans searching for "online jobs" and "remote jobs" are looking for the exact same opportunities, and every listing on this site fits either term.'],
        ['question' => 'Can I really get a remote job while living in Kenya?', 'answer' => 'Yes — thousands of companies hire remote workers with no location restriction. The hard part is finding which listings actually mean it. That\'s what the Kenya-Friendly Match badge is for: we score every job for timezone overlap, location wording, and visa restrictions so you\'re not wasting time on roles that were never open to you.'],
        ['question' => 'Do these remote jobs require a US or EU visa?', 'answer' => 'Some do — and we flag those. Jobs that mention explicit US-only, EU-only, or visa-sponsorship restrictions are excluded from the Kenya-Friendly Match badge, so you can filter them out with one click on the jobs page.'],
        ['question' => 'What does \'Kenya-Friendly Match\' mean?', 'answer' => 'It\'s a badge we calculate automatically for every listing, based on whether the location is worldwide/global/Africa-open, whether the stated timezone window overlaps East Africa Time (UTC+3), and whether the description rules out candidates outside the US or EU.'],
        ['question' => 'Is KenyaRemoteJobs free to use?', 'answer' => 'Yes! Browsing jobs, company names, and descriptions is 100% free. Pro Early Access gives you full access to apply to all 800+ remote jobs immediately (no 48h wait on fresh roles), direct Kenyan employer listings, and unlimited AI CV tailoring for KES 1,499/mo via M-Pesa.'],
        ['question' => 'How are job listings sourced and verified?', 'answer' => 'We partner directly with remote-first employers and continuously track verified global hiring networks that actively employ international talent. Every role undergoes rigorous screening for East Africa Time (UTC+3) compatibility, legitimate compensation, and zero hidden visa restrictions so you only apply to roles realistically open to Kenyans.'],
    ];

    $rotations = ['-rotate-2', 'rotate-1', '-rotate-1'];
    $offsets = ['top-0 left-4', 'top-44 left-16', 'top-[22rem] left-0'];
@endphp

<x-layouts.app :canonical="url('/')">
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($faqs) !!}</script>

    {{-- Hero — KenyaRemoteJobs brand aesthetic with unified floating pill search bar and spacious modern layout --}}
    <section class="relative bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(255,49,49,0.08),rgba(46,55,96,0.05),rgba(255,255,255,0))] px-4 pt-14 pb-16 sm:px-6 sm:pt-20">
        <div class="relative mx-auto max-w-4xl text-center">
            <x-reveal>
                <div class="inline-flex items-center gap-2 rounded-full bg-rose-50/80 border border-rose-200/70 px-4 py-1.5 text-xs font-bold text-[#be121c] shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#ff3131] opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-[#ff3131]"></span>
                    </span>
                    <span>Kenya's #1 Verified Remote Job Board</span>
                </div>

                <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl sm:leading-[1.12]">
                    Remote jobs from employers
                    <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#2e3760] via-[#ff3131] to-[#e11d27]">who actually want Kenyan talent.</span>
                </h1>

                <p class="mx-auto mt-5 max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    Discover hand-curated international remote opportunities screened for East Africa Time (UTC+3) compatibility, competitive USD compensation, and zero foreign visa barriers.
                </p>
            </x-reveal>

            {{-- Remote.co signature unified floating pill search bar --}}
            <x-reveal :delay="80" class="relative z-10 mx-auto mt-10">
                <div class="rounded-3xl sm:rounded-full border border-slate-200/80 bg-white p-2 sm:p-2.5 shadow-[0_8px_30px_rgba(46,55,96,0.06)] hover:shadow-[0_12px_40px_rgba(255,49,49,0.12)] transition-shadow">
                    <form action="{{ url('/jobs') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        {{-- Keyword input --}}
                        <div class="relative flex-1 flex items-center pl-4">
                            <x-icon name="search" class="h-4 w-4 text-slate-400 shrink-0" />
                            <input
                                type="text"
                                name="q"
                                placeholder="Job title, skill, or company…"
                                class="w-full bg-transparent py-2.5 pl-3 pr-2 text-sm text-slate-900 placeholder:text-slate-400 border-none focus:outline-none focus:ring-0"
                            />
                        </div>

                        <div class="hidden sm:block h-7 w-px bg-slate-200/80"></div>

                        {{-- Remote type dropdown --}}
                        <div class="sm:w-44 px-2">
                            <select
                                name="remoteType"
                                class="w-full bg-transparent py-2.5 px-2 text-sm text-slate-700 border-none focus:outline-none focus:ring-0 cursor-pointer"
                            >
                                <option value="">Any remote type</option>
                                <option value="remote">Remote (Anywhere)</option>
                                <option value="full-time">Full-time Remote</option>
                                <option value="contract">Contract Remote</option>
                            </select>
                        </div>

                        <div class="hidden sm:block h-7 w-px bg-slate-200/80"></div>

                        {{-- Kenya Friendly Toggle Pill --}}
                        <label class="flex items-center justify-center gap-2 rounded-full bg-slate-50 hover:bg-slate-100/80 border border-slate-200/70 py-2 px-3.5 text-xs font-semibold text-slate-700 cursor-pointer transition shrink-0">
                            <input type="checkbox" name="kenyaFriendly" value="true" class="h-4 w-4 rounded-full border-slate-300 text-[#ff3131] focus:ring-[#ff3131]">
                            <span class="text-slate-700">Kenya-Friendly</span>
                            <x-icon.kenya-flag class="h-3.5 w-3.5 shrink-0" />
                        </label>

                        {{-- Search CTA button --}}
                        <button
                            type="submit"
                            class="btn-pop rounded-full bg-[#ff3131] px-7 py-3 text-sm font-bold text-white shadow-md shadow-[#ff3131]/25 hover:bg-[#e11d27] hover:shadow-lg transition-all shrink-0 text-center"
                        >
                            Find Jobs
                        </button>
                    </form>
                </div>

                {{-- Popular search tags as soft floating pills --}}
                <div class="mt-4 flex flex-wrap items-center justify-center gap-1.5 text-xs text-slate-500">
                    <span class="font-medium text-slate-400 mr-1">Trending:</span>
                    <a href="{{ url('/jobs') }}?q=Customer+Support" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Customer Support</a>
                    <a href="{{ url('/jobs') }}?q=Virtual+Assistant" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Virtual Assistant</a>
                    <a href="{{ url('/jobs') }}?q=Developer" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Software Engineer</a>
                    <a href="{{ url('/jobs') }}?q=Data+Entry" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Data Entry</a>
                    <a href="{{ url('/jobs') }}?q=Writing" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Writing & Content</a>
                    <a href="{{ url('/jobs') }}?q=Marketing" class="rounded-full bg-white border border-slate-200/70 px-3 py-1 text-slate-600 hover:border-teal-400 hover:text-teal-700 hover:bg-teal-50/30 transition shadow-2xs">Marketing</a>
                </div>

                {{-- Quick Trust Highlights Pill Bar --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 border border-slate-200/70 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                        UTC+3 Timezone Aligned
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 border border-slate-200/70 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                        Zero Foreign Visa Roadblocks
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 border border-slate-200/70 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                        Verified USD, M-Pesa &amp; Wise Rails
                    </span>
                </div>
            </x-reveal>
        </div>
    </section>

    {{-- Stat bar — clean borderless metric cards --}}
    <section class="px-4 py-8 sm:px-6">
        <div class="mx-auto grid max-w-4xl grid-cols-1 gap-4 text-center sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200/60 bg-white/80 p-5 shadow-xs">
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$total" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Live listings</p>
            </div>
            <div class="rounded-2xl border border-slate-200/60 bg-white/80 p-5 shadow-xs">
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$totalKenyaFriendly" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Kenya-Friendly Matches</p>
            </div>
            <div class="rounded-2xl border border-slate-200/60 bg-white/80 p-5 shadow-xs">
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$totalFree" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Free to view now</p>
            </div>
        </div>
    </section>

    {{-- Just posted — a real, live feed of the newest listings --}}
    @if ($justPosted->isNotEmpty())
        <section class="px-4 py-6 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <x-reveal class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    Just posted
                </x-reveal>
                <div class="relative mt-4">
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        @foreach ($justPosted as $i => $job)
                            <x-reveal :delay="$i * 60" class="shrink-0">
                                <a
                                    href="{{ url('/jobs/'.$job->id) }}"
                                    class="card-hover flex w-72 shrink-0 flex-col gap-1.5 rounded-2xl border border-slate-200/70 bg-white p-4 text-sm shadow-xs hover:border-teal-500/70 hover:shadow-md hover:-translate-y-0.5 transition-all"
                                >
                                    <span class="truncate font-bold text-slate-900">{{ $job->title }}</span>
                                    <span class="truncate text-xs font-medium text-slate-500">{{ $job->company }}</span>
                                    <span class="mt-1 text-xs font-semibold text-teal-600">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
                                </a>
                            </x-reveal>
                        @endforeach
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white to-transparent" aria-hidden="true"></div>
                </div>
            </div>
        </section>
    @endif

    {{-- Find your next remote role — sidebar + sleek horizontal list feed --}}
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <x-reveal class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-slate-900">{{ $hasProfile ? 'Your best-matching jobs' : 'Find your next remote role' }}</h2>
            <span class="text-xs font-medium text-slate-400">Verified & updated hourly</span>
        </x-reveal>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[250px_1fr]">
            <x-reveal class="space-y-6">
                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Browse by audience</p>
                    <ul class="space-y-1">
                        @foreach ($audienceCounts as $a)
                            <li>
                                <a href="{{ url('/jobs') }}?audience={{ $a['segment'] }}" class="flex items-center justify-between rounded-full px-3.5 py-2 text-sm text-slate-700 transition hover:bg-teal-50/70 hover:text-teal-800">
                                    <span class="flex items-center gap-2 font-medium">
                                        <x-icon :name="\App\Support\Audience::ICONS[$a['segment']]" class="h-4 w-4 text-teal-600" />
                                        {{ \App\Support\Audience::LABELS[$a['segment']] }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-semibold">{{ $a['count'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Popular categories</p>
                    <ul class="space-y-1">
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="flex items-center gap-2.5 rounded-full px-3.5 py-2 text-sm text-slate-700 font-medium transition hover:bg-teal-50/70 hover:text-teal-800">
                                    <x-icon :name="$cat['icon']" class="h-4 w-4 text-teal-600" />
                                    {{ $cat['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200/70 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Curated Collections</p>
                        <a href="{{ url('/collections') }}" class="text-[11px] font-bold text-teal-600 hover:underline">All &rarr;</a>
                    </div>
                    <ul class="space-y-1">
                        @foreach (\App\Support\JobCollectionSeo::all() as $col)
                            <li>
                                <a href="{{ url('/collections/'.$col['slug']) }}" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 font-medium transition hover:bg-slate-50 hover:text-teal-700">
                                    <x-icon :name="$col['icon']" class="h-3.5 w-3.5 text-teal-600 shrink-0" />
                                    <span class="truncate">{{ $col['badge'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-3 border-t border-slate-100 pt-3">
                        <a href="{{ url('/companies') }}" class="flex items-center justify-between text-xs font-bold text-slate-900 hover:text-teal-600 transition">
                            <span>Hiring Companies Directory</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </x-reveal>

            <div>
                @if ($feed->isEmpty())
                    <p class="rounded-2xl border border-slate-200/70 bg-white p-8 text-center text-slate-500 shadow-xs">
                        Jobs are syncing — check back in a moment, or <a href="{{ url('/jobs') }}" class="text-teal-600 font-semibold underline">browse everything</a>.
                    </p>
                @else
                    {{-- Remote.co style streamlined vertical list feed --}}
                    <div class="space-y-3">
                        @foreach ($feed as $i => $job)
                            <x-reveal :delay="min($i, 6) * 40">
                                <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                            </x-reveal>
                        @endforeach
                    </div>
                @endif
                <div class="mt-8 text-center">
                    <a href="{{ url('/jobs') }}" class="btn-pop inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white px-8 py-3 text-sm font-bold text-slate-800 shadow-xs hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 transition">
                        See all {{ $total }} remote jobs &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Start with the work you do best — Remote.co Category Grid --}}
    <section class="px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-reveal>
                <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-900">Browse Remote Jobs by Category</h2>
            </x-reveal>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $i => $cat)
                    <x-reveal :delay="$i * 60">
                        <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="card-hover flex items-center justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white p-5 shadow-xs hover:border-teal-500/60 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 border border-teal-100 shadow-2xs">
                                    <x-icon :name="$cat['icon']" class="h-5 w-5" />
                                </span>
                                <span class="font-bold text-slate-800 text-sm hover:text-teal-700 transition-colors">{{ $cat['label'] }}</span>
                            </div>
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover:bg-teal-50 group-hover:text-teal-700 transition-colors text-sm font-semibold">&rarr;</span>
                        </a>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Not every "remote" job means Kenya — trust section --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-12 max-w-2xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200/70 px-3.5 py-1 text-xs font-bold text-teal-800 mb-3">
                <x-icon name="shield" class="h-3.5 w-3.5 text-teal-600" />
                Vetting Standards
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900">Not every &ldquo;remote&rdquo; job means Kenya</h2>
            <p class="mt-3 text-slate-600 leading-relaxed">A lot of listings say &ldquo;remote&rdquo; and mean one country. Here&rsquo;s what we check on every single listing before it earns the badge.</p>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($trustFeatures as $i => $f)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-teal-500/60 hover:shadow-md transition-all duration-200">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 border border-teal-100 shadow-2xs">
                            <x-icon :name="$f['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed font-normal">{{ $f['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        <x-reveal class="mt-8 text-center">
            <a href="{{ url('/match') }}" class="inline-flex items-center gap-2 rounded-full border border-teal-200 bg-teal-50/60 px-5 py-2.5 text-sm font-bold text-teal-800 hover:bg-teal-100 transition shadow-2xs">
                <span>See how the Kenya-Friendly Match score works</span>
                <span>&rarr;</span>
            </a>
        </x-reveal>
    </section>

    {{-- How it works — Open 3-step visual journey --}}
    <section class="px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-5xl">
            <x-reveal class="text-center mb-12">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200/70 px-3.5 py-1 text-xs font-bold text-teal-800 mb-2.5">
                    <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" />
                    Simple Process
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900">How KenyaRemoteJobs Works</h2>
            </x-reveal>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($howItWorks as $i => $step)
                    <x-reveal :delay="$i * 100">
                        <div class="relative h-full rounded-2xl border border-slate-200/70 bg-white p-7 shadow-xs hover:border-teal-500/60 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 font-black text-white text-sm shadow-sm shadow-teal-600/30">
                                    {{ $i + 1 }}
                                </span>
                                <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Step 0{{ $i + 1 }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed font-normal">{{ $step['body'] }}</p>
                        </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="text-center mb-12">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3.5 py-1 text-xs font-bold text-emerald-800 mb-2.5">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                Proven Results
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900">Success Stories from Kenyan Talent</h2>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($testimonials as $i => $t)
                <x-reveal :delay="$i * 120">
                    <figure class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-teal-500/60 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <blockquote class="text-sm sm:text-base text-slate-700 leading-relaxed font-normal">&ldquo;{{ $t['quote'] }}&rdquo;</blockquote>
                        <figcaption class="mt-6 pt-4 border-t border-slate-100 text-xs font-bold uppercase tracking-wider text-teal-700 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                <x-icon name="check" class="h-3.5 w-3.5" />
                            </span>
                            {{ $t['name'] }}
                        </figcaption>
                    </figure>
                </x-reveal>
            @endforeach
        </div>
    </section>

    {{-- FAQs --}}
    <section id="faqs" class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <x-reveal class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-slate-900">Frequently Asked Questions</h2>
        </x-reveal>
        <div class="space-y-4">
            @foreach ($faqs as $i => $faq)
                <x-reveal :delay="min($i, 5) * 70">
                    <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                </x-reveal>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ url('/faqs') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200/80 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 hover:border-teal-500 hover:text-teal-700">
                View all answered questions &rarr;
            </a>
        </div>
    </section>

    {{-- Final CTA banner --}}
    <section class="px-4 pb-12 sm:px-6">
        <x-reveal class="relative mx-auto max-w-6xl rounded-3xl bg-slate-900 border border-slate-800 px-8 py-16 text-center text-white shadow-2xl overflow-hidden">
            <div class="pointer-events-none absolute -right-16 -bottom-16 h-64 w-64 rounded-full bg-teal-500/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-16 -top-16 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

            <div class="relative z-10">
                <h2 class="text-3xl font-extrabold sm:text-4xl text-white">Make it easier for the right opportunity to find you.</h2>
                <p class="mx-auto mt-4 max-w-xl text-slate-300 leading-relaxed font-normal">Get a match score against every listing, no CV upload required — takes under a minute.</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/match') }}" class="btn-pop rounded-full bg-teal-600 px-8 py-3.5 font-bold text-white shadow-md shadow-teal-600/30 transition hover:bg-teal-700">Get my match score &rarr;</a>
                    <a href="{{ url('/jobs') }}" class="rounded-full border border-slate-700 bg-slate-800/80 px-8 py-3.5 font-semibold text-slate-200 transition hover:bg-slate-800 hover:text-white">Browse all jobs</a>
                </div>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <x-reveal>
            <a href="{{ url('/surveys') }}" class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 sm:p-7 shadow-xs transition-all hover:border-teal-500/70 hover:shadow-md hover:-translate-y-0.5 sm:flex-row sm:items-center">
                <div>
                    <p class="flex items-center gap-2.5 font-bold text-slate-900 text-base">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-teal-50 text-teal-600 border border-teal-100/80 shadow-2xs">
                            <x-icon name="bulb" class="h-4 w-4" />
                        </span>
                        <span>Earn while you search</span>
                    </p>
                    <p class="text-sm text-slate-500 mt-1 pl-10.5 leading-relaxed font-normal">A curated list of legit paid-survey platforms as a side-income supplement.</p>
                </div>
                <span class="shrink-0 text-sm font-bold text-teal-700">Explore &rarr;</span>
            </a>
        </x-reveal>
    </section>
</x-layouts.app>
