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
        ['title' => 'Search & filter', 'body' => 'Browse remote roles from trusted global job boards, all in one place — free to search.'],
        ['title' => 'Spot your match', 'body' => 'Every listing gets a Kenya-Friendly Match badge when it\'s realistically open to timezone and location.'],
        ['title' => 'Unlock & apply', 'body' => 'Buy a credit package once — from KES 300 — and spend it on any job in that tier whenever you find one worth unlocking. Listings don\'t stay up forever, so it\'s worth acting before one you want disappears.'],
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
        ['question' => 'Is KenyaRemoteJobs free to use?', 'answer' => 'Yes. Job titles, full descriptions, and your match score are always free to see. What\'s gated is the employer\'s identity and the Apply link while a listing is new — a Basic package (KES 300) gets you 1 unlock, Intermediate (KES 599) gets you 2, and Premium (KES 999) gets you 3, spendable on any job in that tier. Listings don\'t stay up forever, so unlock the ones you want before they disappear.'],
        ['question' => 'Where do the job listings come from?', 'answer' => 'We aggregate live listings from Arbeitnow, RemoteOK, Remotive, Jobicy, and Himalayas — established remote job boards — and credit and link back to the original source on every listing.'],
    ];

    $rotations = ['-rotate-2', 'rotate-1', '-rotate-1'];
    $offsets = ['top-0 left-4', 'top-44 left-16', 'top-[22rem] left-0'];
@endphp

<x-layouts.app>
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($faqs) !!}</script>

    {{-- Hero — light background with soft gradient-orb accents (borrowed structural
         pattern), real jobs previewed on the right instead of a colorful wash. --}}
    <section class="relative bg-white px-4 pb-16 pt-16 sm:px-6 sm:pt-20">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-sunrise-100 blur-3xl"></div>
            <div class="absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-horizon-100 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid max-w-6xl grid-cols-1 gap-12 md:grid-cols-2 md:items-center">
            <x-reveal>
                <p class="text-xs font-semibold uppercase tracking-widest text-sunrise-600">— Online &amp; Remote Job Search</p>
                <h1 class="mt-3 text-4xl font-bold leading-[1.1] text-horizon-900 sm:text-5xl">
                    Remote jobs from employers
                    <br>
                    <span class="text-sunrise-500">who actually want Kenyan talent.</span>
                </h1>
                <p class="mt-5 max-w-lg text-lg text-foreground/70">
                    Every listing here comes from international companies that specifically want to hire Kenyan and East African talent — pre-screened for timezone, location, and visa reality, so you&rsquo;re never wasting time on a role that was never open to you.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-sunrise-500 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-sunrise-600">
                        Browse open jobs &rarr;
                    </a>
                    <a href="{{ url('/employers') }}" class="rounded-full border-2 border-horizon-800 px-6 py-3 font-semibold text-horizon-800 transition hover:bg-horizon-50">
                        Post a job
                    </a>
                </div>

                <p class="mt-6 text-sm">
                    <a href="{{ url('/match') }}" class="inline-flex items-center gap-1 font-semibold text-horizon-700 underline decoration-horizon-300 underline-offset-4 hover:decoration-horizon-700">
                        <x-icon name="sparkle" class="h-3.5 w-3.5" /> No CV needed — get a match score for every job in under a minute &rarr;
                    </a>
                </p>
            </x-reveal>

            <x-reveal :delay="120" class="relative hidden md:block">
                <div class="relative mx-auto h-[30rem] w-full max-w-sm">
                    @foreach ($heroPreview as $i => $job)
                        <div class="absolute {{ $offsets[$i % 3] }} w-72 {{ $rotations[$i % 3] }} rounded-2xl border border-black/5 bg-white p-4 shadow-xl transition hover:-translate-y-1 hover:rotate-0">
                            <div class="flex items-center justify-between">
                                <span class="rounded-full bg-horizon-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-horizon-700">
                                    {{ $job->remote_type ?: 'Remote' }}
                                </span>
                                @if ($job->kenya_friendly)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700">
                                        <x-icon.kenya-flag class="h-3 w-3" /> Match
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm font-semibold text-foreground">{{ $job->title }}</p>
                            <p class="mt-1 text-xs text-foreground/50">Employer hidden until unlocked</p>
                            <p class="mt-2 line-clamp-2 text-xs text-foreground/50">
                                {{ \App\Support\Format::truncate(app(\App\Services\Redactor::class)->redactEmployerIdentity(\App\Support\Format::stripHtml($job->description ?? ''), $job->company), 90) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-reveal>
        </div>

        {{-- Search card — overlaps the hero's bottom edge, same field names as /jobs so it filters straight through. --}}
        <x-reveal :delay="80" class="relative z-10 mx-auto -mb-24 mt-12 max-w-5xl">
            <form action="{{ url('/jobs') }}" method="GET" class="grid grid-cols-1 gap-3 rounded-2xl border border-black/5 bg-white p-5 shadow-2xl sm:grid-cols-[2fr_1fr_1fr_auto] sm:items-center">
                <input type="text" name="q" placeholder="Search job titles, skills, companies…" class="rounded-lg border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                <select name="remoteType" class="rounded-lg border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                    <option value="">Any remote type</option>
                    <option value="remote">Remote</option>
                    <option value="full-time">Full-time</option>
                    <option value="contract">Contract</option>
                </select>
                <label class="flex items-center gap-2 rounded-lg border border-black/10 px-4 py-3 text-sm font-medium text-foreground/70">
                    <input type="checkbox" name="kenyaFriendly" value="true" class="h-4 w-4 accent-emerald-600">
                    Kenya-Friendly only <x-icon.kenya-flag class="h-4 w-4" />
                </label>
                <button type="submit" class="btn-pop rounded-lg bg-horizon-800 px-6 py-3 font-semibold text-white transition hover:bg-horizon-900">
                    Search
                </button>
            </form>
        </x-reveal>
    </section>

    {{-- Stat bar --}}
    <section class="border-b border-black/5 bg-horizon-50 px-4 pb-10 pt-32 sm:px-6">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-6 text-center sm:grid-cols-4">
            <div>
                <p class="text-3xl font-bold text-horizon-900"><x-count-up :value="$total" /></p>
                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-foreground/50">Live listings</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-horizon-900"><x-count-up :value="$totalKenyaFriendly" /></p>
                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-foreground/50">Kenya-Friendly Matches</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-horizon-900"><x-count-up :value="$totalFree" /></p>
                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-foreground/50">Free to view now</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-horizon-900">5</p>
                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-foreground/50">Boards synced daily</p>
            </div>
        </div>
    </section>

    {{-- Find your next remote role — sidebar + feed, Jobicy-style --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mb-8">
            <h2 class="text-2xl font-bold">{{ $hasProfile ? 'Your best-matching jobs' : 'Find your next remote role' }}</h2>
        </x-reveal>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[240px_1fr]">
            <x-reveal class="space-y-8">
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-foreground/40">Browse by audience</p>
                    <ul class="space-y-1">
                        @foreach ($audienceCounts as $a)
                            <li>
                                <a href="{{ url('/jobs') }}?audience={{ $a['segment'] }}" class="flex items-center justify-between rounded-lg px-2.5 py-2 text-sm transition hover:bg-horizon-50">
                                    <span class="flex items-center gap-2">
                                        <x-icon :name="\App\Support\Audience::ICONS[$a['segment']]" class="h-4 w-4 text-horizon-600" />
                                        {{ \App\Support\Audience::LABELS[$a['segment']] }}
                                    </span>
                                    <span class="text-xs text-foreground/40">{{ $a['count'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-foreground/40">Popular categories</p>
                    <ul class="space-y-1">
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm transition hover:bg-horizon-50">
                                    <x-icon :name="$cat['icon']" class="h-4 w-4 text-horizon-600" />
                                    {{ $cat['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </x-reveal>

            <div>
                @if ($feed->isEmpty())
                    <p class="rounded-xl bg-horizon-50 p-6 text-center text-foreground/60">
                        Jobs are syncing — check back in a moment, or <a href="{{ url('/jobs') }}" class="underline">browse everything</a>.
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
                <div class="mt-6 text-center">
                    <a href="{{ url('/jobs') }}" class="text-sm font-semibold text-sunrise-600 hover:underline">See all {{ $total }} jobs &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Start with the work you do best --}}
    <section class="bg-horizon-50 px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-reveal>
                <h2 class="mb-10 text-center text-2xl font-bold">Start with the work you do best</h2>
            </x-reveal>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $i => $cat)
                    <x-reveal :delay="$i * 60">
                        <a href="{{ url('/jobs') }}?q={{ urlencode($cat['q']) }}" class="card-hover flex items-center gap-4 rounded-2xl border border-black/5 bg-white p-5 shadow-sm">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sunrise-50">
                                <x-icon :name="$cat['icon']" class="h-5 w-5 text-sunrise-600" />
                            </span>
                            <span class="font-semibold text-foreground">{{ $cat['label'] }}</span>
                        </a>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Not every "remote" job means Kenya — trust section --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-10 max-w-2xl text-center">
            <h2 class="text-2xl font-bold">Not every &ldquo;remote&rdquo; job means Kenya</h2>
            <p class="mt-2 text-foreground/60">A lot of listings say &ldquo;remote&rdquo; and mean one country. Here&rsquo;s what we check on every single one before it earns the badge.</p>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($trustFeatures as $i => $f)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-horizon-50">
                            <x-icon :name="$f['icon']" class="h-5 w-5 text-horizon-600" />
                        </span>
                        <h3 class="mt-3 font-semibold">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm text-foreground/60">{{ $f['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        <x-reveal class="mt-8 text-center">
            <a href="{{ url('/match') }}" class="text-sm font-semibold text-sunrise-600 hover:underline">See how the Kenya-Friendly Match score works &rarr;</a>
        </x-reveal>
    </section>

    {{-- How it works — tabbed, borrowed structural pattern from orchid.security,
         kept in our own warm palette instead of their dark enterprise tone. --}}
    <section class="bg-horizon-50 px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <x-reveal>
                <h2 class="mb-10 text-center text-2xl font-bold">How it works</h2>
            </x-reveal>

            <x-reveal>
                <div x-data="{ tab: 0 }">
                    <div class="mx-auto flex max-w-xl items-center justify-center gap-2 rounded-full border border-black/5 bg-white p-1.5 shadow-sm">
                        @foreach ($howItWorks as $i => $step)
                            <button
                                type="button"
                                @click="tab = {{ $i }}"
                                class="flex-1 rounded-full px-4 py-2 text-sm font-semibold transition"
                                :class="tab === {{ $i }} ? 'gradient-sunrise text-white shadow' : 'text-foreground/60 hover:text-foreground'"
                            >
                                {{ $i + 1 }}. {{ $step['title'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="relative mt-8 min-h-[9rem] rounded-2xl border border-black/5 bg-white p-8 shadow-sm">
                        @foreach ($howItWorks as $i => $step)
                            <div x-show="tab === {{ $i }}" x-cloak x-transition.opacity class="text-center">
                                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full gradient-sunrise text-lg font-bold text-white shadow-md">
                                    {{ $i + 1 }}
                                </div>
                                <h3 class="mb-2 font-semibold">{{ $step['title'] }}</h3>
                                <p class="mx-auto max-w-md text-sm text-foreground/60">{{ $step['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-reveal>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal>
            <h2 class="mb-10 text-center text-2xl font-bold">Success stories</h2>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($testimonials as $i => $t)
                <x-reveal :delay="$i * 120">
                    <figure class="card-hover h-full rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                        <blockquote class="text-sm text-foreground/70">&ldquo;{{ $t['quote'] }}&rdquo;</blockquote>
                        <figcaption class="mt-4 text-sm font-semibold text-sunrise-600">{{ $t['name'] }}</figcaption>
                    </figure>
                </x-reveal>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <x-reveal>
            <h2 class="mb-10 text-center text-2xl font-bold">Frequently asked questions</h2>
        </x-reveal>
        <div class="space-y-4">
            @foreach ($faqs as $i => $faq)
                <x-reveal :delay="min($i, 5) * 70">
                    <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                </x-reveal>
            @endforeach
        </div>
    </section>

    {{-- Final CTA banner --}}
    <section class="px-4 pb-6 sm:px-6">
        <x-reveal class="mx-auto max-w-6xl rounded-3xl bg-horizon-900 px-8 py-12 text-center text-white">
            <h2 class="text-2xl font-bold sm:text-3xl">Make it easier for the right opportunity to find you.</h2>
            <p class="mx-auto mt-3 max-w-xl text-white/70">Get a match score against every listing, no CV upload required — takes under a minute.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/match') }}" class="btn-pop rounded-full bg-sunrise-500 px-6 py-3 font-semibold text-white transition hover:bg-sunrise-600">Get my match score &rarr;</a>
                <a href="{{ url('/jobs') }}" class="rounded-full border-2 border-white/30 px-6 py-3 font-semibold text-white transition hover:bg-white/10">Browse all jobs</a>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <x-reveal>
            <a href="{{ url('/surveys') }}" class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-horizon-200 bg-horizon-50 p-6 transition hover:-translate-y-0.5 hover:bg-horizon-100 hover:shadow-md sm:flex-row sm:items-center">
                <div>
                    <p class="flex items-center gap-1.5 font-semibold text-horizon-800">
                        <x-icon name="bulb" class="h-4 w-4" /> Earn while you search
                    </p>
                    <p class="text-sm text-foreground/60">A curated list of legit paid-survey platforms as a side-income supplement.</p>
                </div>
                <span class="shrink-0 text-sm font-semibold text-horizon-700">Explore &rarr;</span>
            </a>
        </x-reveal>
    </section>
</x-layouts.app>
