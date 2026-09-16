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

    {{-- Hero — Remote.co clean aesthetic with unified multi-input search bar --}}
    <section class="relative bg-gradient-to-b from-slate-50 via-white to-slate-50/50 border-b border-slate-200/80 px-4 pt-12 pb-16 sm:px-6 sm:pt-16">
        <div class="relative mx-auto grid max-w-6xl grid-cols-1 gap-12 md:grid-cols-2 md:items-center">
            <x-reveal>
                <p class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200/90 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-teal-800">
                    <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" /> Kenya's Verified Remote Career Platform
                </p>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-5xl leading-[1.15]">
                    Remote jobs from employers
                    <br>
                    <span class="text-teal-600">who actually want Kenyan talent.</span>
                </h1>
                <p class="mt-4 max-w-xl text-base sm:text-lg text-slate-600 leading-relaxed">
                    Every listing here comes from international companies that specifically want to hire Kenyan and East African talent — pre-screened for timezone, location, and visa reality, so you&rsquo;re never wasting time on a role that was never open to you.
                </p>

                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <a href="{{ url('/jobs') }}" class="btn-pop rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white shadow-xs transition hover:bg-teal-700">
                        Browse open jobs &rarr;
                    </a>
                    <a href="{{ url('/employers') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50">
                        Post a job
                    </a>
                </div>

                <p class="mt-5 text-sm">
                    <a href="{{ url('/match') }}" class="inline-flex items-center gap-1.5 font-semibold text-teal-700 hover:text-teal-800 hover:underline">
                        <x-icon name="sparkle" class="h-4 w-4 text-teal-600" /> No CV needed — get a match score for every job in under a minute &rarr;
                    </a>
                </p>
            </x-reveal>

            <x-reveal :delay="120" class="relative hidden md:block">
                <div class="relative mx-auto h-[28rem] w-full max-w-sm">
                    @foreach ($heroPreview as $i => $job)
                        <div class="absolute {{ $offsets[$i % 3] }} w-72 {{ $rotations[$i % 3] }} rounded-xl border border-slate-200 bg-white p-4 shadow-lg transition hover:-translate-y-1 hover:rotate-0 hover:border-teal-500">
                            <div class="flex items-center justify-between">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                    {{ $job->remote_type ?: 'Remote' }}
                                </span>
                                @if ($job->kenya_friendly)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700">
                                        <x-icon.kenya-flag class="h-3 w-3" /> Match
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 line-clamp-1 text-sm font-bold text-slate-900">{{ $job->title }}</p>
                            <p class="mt-0.5 text-xs font-medium text-slate-500">{{ $job->company }}</p>
                            <p class="mt-2 line-clamp-2 text-xs text-slate-500 leading-relaxed">
                                {{ \App\Support\Format::truncate(\App\Support\Format::stripHtml($job->description ?? ''), 90) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-reveal>
        </div>

        {{-- Remote.co signature unified multi-input search box --}}
        <x-reveal :delay="80" class="relative z-10 mx-auto mt-12 max-w-5xl">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-xl shadow-slate-200/60">
                <form action="{{ url('/jobs') }}" method="GET" class="grid grid-cols-1 gap-2.5 sm:grid-cols-[2fr_1.2fr_auto_auto] sm:items-center">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <x-icon name="globe" class="h-4 w-4" />
                        </div>
                        <input type="text" name="q" placeholder="Job title, skill, or keyword…" class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-teal-500">
                    </div>
                    <select name="remoteType" class="rounded-lg border border-slate-200 bg-slate-50/50 py-2.5 px-3 text-sm text-slate-700 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-teal-500">
                        <option value="">Any remote type</option>
                        <option value="remote">Remote (Anywhere)</option>
                        <option value="full-time">Full-time Remote</option>
                        <option value="contract">Contract Remote</option>
                    </select>
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" name="kenyaFriendly" value="true" class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <span>Kenya-Friendly</span>
                        <x-icon.kenya-flag class="h-3.5 w-3.5 shrink-0" />
                    </label>
                    <button type="submit" class="btn-pop rounded-lg bg-teal-600 px-6 py-2.5 text-sm font-bold text-white shadow-xs hover:bg-teal-700 transition">
                        Search Jobs
                    </button>
                </form>
                <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500">
                    <span class="font-semibold text-slate-700">Popular:</span>
                    <a href="{{ url('/jobs') }}?q=Customer+Support" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Customer Support</a>
                    <a href="{{ url('/jobs') }}?q=Virtual+Assistant" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Virtual Assistant</a>
                    <a href="{{ url('/jobs') }}?q=Developer" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Software Engineer</a>
                    <a href="{{ url('/jobs') }}?q=Data+Entry" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Data Entry</a>
                    <a href="{{ url('/jobs') }}?q=Writing" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Writing & Content</a>
                    <a href="{{ url('/jobs') }}?q=Marketing" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-600 hover:bg-teal-50 hover:text-teal-700 transition">Marketing</a>
                </div>
            </div>
        </x-reveal>
    </section>

    {{-- Stat bar --}}
    <section class="border-b border-slate-200/80 bg-white px-4 py-8 sm:px-6">
        <div class="mx-auto grid max-w-4xl grid-cols-1 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 gap-6 text-center sm:grid-cols-3">
            <div>
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$total" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Live listings</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$totalKenyaFriendly" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Kenya-Friendly Matches</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-slate-900"><x-count-up :value="$totalFree" /></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Free to view now</p>
            </div>
        </div>
    </section>

    {{-- Just posted — a real, live feed of the newest listings --}}
    @if ($justPosted->isNotEmpty())
        <section class="border-b border-slate-200/80 bg-slate-50/70 px-4 py-8 sm:px-6">
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
                                    class="card-hover flex w-64 shrink-0 flex-col gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-xs hover:border-teal-500 transition"
                                >
                                    <span class="truncate font-bold text-slate-900">{{ $job->title }}</span>
                                    <span class="truncate text-xs font-medium text-slate-500">{{ $job->company }}</span>
                                    <span class="mt-1 text-xs font-semibold text-teal-600">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
                                </a>
                            </x-reveal>
                        @endforeach
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-slate-50/70 to-transparent" aria-hidden="true"></div>
                </div>
            </div>
        </section>
    @endif

    {{-- Find your next remote role — sidebar + feed --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <x-reveal class="mb-8">
            <h2 class="text-2xl font-extrabold text-slate-900">{{ $hasProfile ? 'Your best-matching jobs' : 'Find your next remote role' }}</h2>
        </x-reveal>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[240px_1fr]">
            <x-reveal class="space-y-6">
                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Browse by audience</p>
                    <ul class="space-y-1">
                        @foreach ($audienceCounts as $a)
                            <li>
                                <a href="{{ url('/jobs') }}?audience={{ $a['segment'] }}" class="flex items-center justify-between rounded-lg px-2.5 py-2 text-sm text-slate-700 transition hover:bg-slate-100 hover:text-teal-700">
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
                                <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-slate-700 font-medium transition hover:bg-slate-100 hover:text-teal-700">
                                    <x-icon :name="$cat['icon']" class="h-4 w-4 text-teal-600" />
                                    {{ $cat['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Curated Collections</p>
                        <a href="{{ url('/collections') }}" class="text-[11px] font-bold text-teal-600 hover:underline">All &rarr;</a>
                    </div>
                    <ul class="space-y-1">
                        @foreach (\App\Support\JobCollectionSeo::all() as $col)
                            <li>
                                <a href="{{ url('/collections/'.$col['slug']) }}" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs text-slate-700 font-medium transition hover:bg-slate-50 hover:text-teal-700">
                                    <x-icon :name="$col['icon']" class="h-3.5 w-3.5 text-teal-600 shrink-0" />
                                    <span class="truncate">{{ $col['badge'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-3 border-t border-slate-100 pt-2.5">
                        <a href="{{ url('/companies') }}" class="flex items-center justify-between text-xs font-bold text-slate-900 hover:text-teal-600 transition">
                            <span>Hiring Companies Directory</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </x-reveal>

            <div>
                @if ($feed->isEmpty())
                    <p class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                        Jobs are syncing — check back in a moment, or <a href="{{ url('/jobs') }}" class="text-teal-600 font-semibold underline">browse everything</a>.
                    </p>
                @else
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach ($feed as $i => $job)
                            <x-reveal :delay="min($i, 6) * 70" class="h-full">
                                <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                            </x-reveal>
                        @endforeach
                    </div>
                @endif
                <div class="mt-8 text-center">
                    <a href="{{ url('/jobs') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-teal-500 hover:text-teal-700 transition">
                        See all {{ $total }} remote jobs &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Start with the work you do best — Remote.co Category Grid --}}
    <section class="bg-slate-50/70 border-y border-slate-200/80 px-4 py-14 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-reveal>
                <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-900">Browse Remote Jobs by Category</h2>
            </x-reveal>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $i => $cat)
                    <x-reveal :delay="$i * 60">
                        <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="card-hover flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:border-teal-500 transition">
                            <div class="flex items-center gap-3.5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                                    <x-icon :name="$cat['icon']" class="h-5 w-5" />
                                </span>
                                <span class="font-bold text-slate-800 text-sm hover:text-teal-700">{{ $cat['label'] }}</span>
                            </div>
                            <span class="text-slate-400 text-sm font-semibold">&rarr;</span>
                        </a>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Not every "remote" job means Kenya — trust section --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-10 max-w-2xl text-center">
            <h2 class="text-2xl font-extrabold text-slate-900">Not every &ldquo;remote&rdquo; job means Kenya</h2>
            <p class="mt-2 text-slate-600">A lot of listings say &ldquo;remote&rdquo; and mean one country. Here&rsquo;s what we check on every single listing before it earns the badge.</p>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($trustFeatures as $i => $f)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-500 transition">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                            <x-icon :name="$f['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-bold text-slate-900">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $f['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        <x-reveal class="mt-8 text-center">
            <a href="{{ url('/match') }}" class="text-sm font-bold text-teal-700 hover:underline">See how the Kenya-Friendly Match score works &rarr;</a>
        </x-reveal>
    </section>

    {{-- How it works — Remote.co clean step layout --}}
    <section class="bg-slate-50/70 border-y border-slate-200/80 px-4 py-14 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <x-reveal>
                <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-900">How It Works</h2>
            </x-reveal>

            <x-reveal>
                <div x-data="{ tab: 0 }">
                    <div class="mx-auto flex max-w-xl items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xs">
                        @foreach ($howItWorks as $i => $step)
                            <button
                                type="button"
                                @click="tab = {{ $i }}"
                                class="flex-1 rounded-lg px-3 py-2 text-sm font-bold transition"
                                :class="tab === {{ $i }} ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            >
                                {{ $i + 1 }}. {{ $step['title'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="relative mt-6 min-h-[9rem] rounded-xl border border-slate-200 bg-white p-8 shadow-xs">
                        @foreach ($howItWorks as $i => $step)
                            <div x-show="tab === {{ $i }}" x-cloak x-transition.opacity class="text-center">
                                <div class="mx-auto mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-teal-600 text-base font-bold text-white shadow-xs">
                                    {{ $i + 1 }}
                                </div>
                                <h3 class="mb-2 font-bold text-slate-900 text-lg">{{ $step['title'] }}</h3>
                                <p class="mx-auto max-w-md text-sm text-slate-600 leading-relaxed">{{ $step['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-reveal>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal>
            <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-900">Success Stories</h2>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($testimonials as $i => $t)
                <x-reveal :delay="$i * 120">
                    <figure class="card-hover h-full rounded-xl border border-slate-200 bg-white p-6 shadow-xs hover:border-teal-500 transition flex flex-col justify-between">
                        <blockquote class="text-sm text-slate-700 leading-relaxed">&ldquo;{{ $t['quote'] }}&rdquo;</blockquote>
                        <figcaption class="mt-4 text-xs font-bold uppercase tracking-wider text-teal-700 flex items-center gap-1.5">
                            <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                            {{ $t['name'] }}
                        </figcaption>
                    </figure>
                </x-reveal>
            @endforeach
        </div>
    </section>

    <section id="faqs" class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <x-reveal>
            <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-900">Frequently Asked Questions</h2>
        </x-reveal>
        <div class="space-y-4">
            @foreach ($faqs as $i => $faq)
                <x-reveal :delay="min($i, 5) * 70">
                    <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                </x-reveal>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="{{ url('/faqs') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 hover:border-teal-500 hover:text-teal-700">
                View all answered questions &rarr;
            </a>
        </div>
    </section>

    {{-- Final CTA banner --}}
    <section class="px-4 pb-12 sm:px-6">
        <x-reveal class="mx-auto max-w-6xl rounded-2xl bg-slate-900 border border-slate-800 px-8 py-12 text-center text-white shadow-xl">
            <h2 class="text-2xl font-bold sm:text-3xl text-white">Make it easier for the right opportunity to find you.</h2>
            <p class="mx-auto mt-3 max-w-xl text-slate-400">Get a match score against every listing, no CV upload required — takes under a minute.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/match') }}" class="btn-pop rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white shadow-xs transition hover:bg-teal-700">Get my match score &rarr;</a>
                <a href="{{ url('/jobs') }}" class="rounded-lg border border-slate-700 bg-slate-800 px-6 py-3 font-semibold text-slate-200 transition hover:bg-slate-700">Browse all jobs</a>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <x-reveal>
            <a href="{{ url('/surveys') }}" class="flex flex-col items-start justify-between gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition hover:border-teal-500 hover:shadow-md sm:flex-row sm:items-center">
                <div>
                    <p class="flex items-center gap-1.5 font-bold text-slate-900">
                        <x-icon name="bulb" class="h-4 w-4 text-teal-600" /> Earn while you search
                    </p>
                    <p class="text-sm text-slate-500 mt-0.5">A curated list of legit paid-survey platforms as a side-income supplement.</p>
                </div>
                <span class="shrink-0 text-sm font-bold text-teal-700">Explore &rarr;</span>
            </a>
        </x-reveal>
    </section>
</x-layouts.app>
