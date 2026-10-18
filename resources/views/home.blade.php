@php
    $categories = [
        ['icon' => 'pen', 'label' => 'Writing & Content', 'slug' => 'writing-content-kenya'],
        ['icon' => 'headset', 'label' => 'Customer Support', 'slug' => 'customer-support-kenya'],
        ['icon' => 'user', 'label' => 'Virtual Assistant', 'slug' => 'virtual-assistant-kenya'],
        ['icon' => 'code', 'label' => 'Software Engineering', 'slug' => 'software-developer-kenya'],
        ['icon' => 'clipboard', 'label' => 'Data Entry & Ops', 'slug' => 'data-entry-kenya'],
        ['icon' => 'megaphone', 'label' => 'Digital Marketing', 'slug' => 'digital-marketing-kenya'],
        ['icon' => 'trending-up', 'label' => 'Sales & Business Dev', 'slug' => 'sales-business-development-kenya'],
        ['icon' => 'palette', 'label' => 'Design & Creative', 'slug' => 'graphic-design-kenya'],
        ['icon' => 'sparkle', 'label' => 'AI Training & Annotation', 'slug' => 'ai-training-annotation-kenya'],
    ];

    $trustFeatures = [
        ['icon' => 'clock', 'title' => 'Timezone overlap, checked', 'body' => 'We read every listing\'s stated hours and flag whether they realistically overlap East Africa Time — not just the word "remote."'],
        ['icon' => 'pin', 'title' => 'Location wording, scanned', 'body' => '"Work from anywhere" often means anywhere in one country. We catch the buried US-only, EU-only, or region-locked fine print.'],
        ['icon' => 'passport', 'title' => 'Visa restrictions, flagged', 'body' => 'Explicit visa-sponsorship or work-authorization limits are surfaced up front, so you\'re never a paragraph away from finding out you didn\'t qualify.'],
    ];

    $howItWorks = [
        ['title' => 'Search & filter', 'body' => 'Browse remote roles from vetted global companies and hiring employers, all in one place — curated and updated hourly.'],
        ['title' => 'Spot your match', 'body' => 'Every listing gets a Kenya-Friendly Match badge when it\'s realistically open to your timezone and location.'],
        ['title' => 'Instant Apply Access', 'body' => 'Pro Membership gives you immediate apply access to all 800+ remote jobs & direct employer listings, with 1-click AI CV tailoring and priority recruiter delivery.'],
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
        ['question' => 'Is KenyaRemoteJobs free to use?', 'answer' => 'Yes! Browsing and discovering verified remote roles is 100% free. Pro Membership unlocks verified employer names, direct 0-second application links (no 48h wait on fresh roles), and direct recruiter apply portals starting from KES 250/mo via M-Pesa.'],
        ['question' => 'How are job listings sourced and verified?', 'answer' => 'We partner directly with remote-first employers and continuously track verified global hiring networks that actively employ international talent. Every role undergoes rigorous screening for East Africa Time (UTC+3) compatibility, legitimate compensation, and zero hidden visa restrictions so you only apply to roles realistically open to Kenyans.'],
    ];

    $rotations = ['-rotate-2', 'rotate-1', '-rotate-1'];
    $offsets = ['top-0 left-4', 'top-44 left-16', 'top-[22rem] left-0'];
@endphp

<x-layouts.app :canonical="url('/')">
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($faqs) !!}</script>

    {{-- Live Activity Ticker --}}
    <x-live-ticker />

    {{-- Hero — micro1-inspired high-tech aesthetic with floating search bar and geometric Outfit typography --}}
    <section class="relative bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(52,68,218,0.1),rgba(238,240,254,0.4),rgba(248,249,253,0))] px-4 pt-14 pb-16 sm:px-6 sm:pt-20">
        <div class="relative mx-auto max-w-4xl text-center">
            <x-reveal>
                <a href="{{ url('/referrals') }}" class="group inline-flex items-center gap-2 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-4 py-1.5 text-xs font-bold text-[#3444da] hover:bg-[#e0e4fd] transition shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#3444da] opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-[#3444da]"></span>
                    </span>
                    <span>Refer &amp; Earn KES 500 &bull; Kenya's #1 Remote &amp; AI Talent Network</span>
                    <svg class="h-3 w-3 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" viewBox="0 0 16 16" fill="none" stroke="currentColor">
                        <path d="M4.7 11.6L11.2 5.1M5.6 4.7H11.3V10.4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl sm:leading-[1.12]">
                    Remote Opportunities
                    <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-900 via-[#3444da] to-[#2633bf]">for Kenyan Talent.</span>
                </h1>

                <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    Discover hand-curated international remote opportunities, data annotation gigs, and AI training roles screened for East Africa Time (UTC+3) compatibility, competitive USD compensation, and zero foreign visa barriers.
                </p>
            </x-reveal>

            {{-- micro1-Inspired Floating Search Bar --}}
            <x-reveal :delay="80" class="relative z-10 mx-auto mt-10">
                <div class="rounded-3xl sm:rounded-full border border-slate-200/90 bg-white p-2 sm:p-2.5 shadow-[0_12px_40px_rgba(52,68,218,0.08)] hover:shadow-[0_16px_48px_rgba(52,68,218,0.14)] transition-all">
                    <form action="{{ url('/jobs') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        {{-- Keyword input --}}
                        <div class="relative flex-1 flex items-center pl-4">
                            <x-icon name="search" class="h-4 w-4 text-slate-400 shrink-0" />
                            <input
                                type="text"
                                name="q"
                                placeholder="Job title, skill (e.g. Data Annotation, Python)…"
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
                        <label class="flex items-center justify-center gap-2 rounded-full bg-[#f8f9fd] hover:bg-[#eef0fe] border border-slate-200/80 py-2 px-3.5 text-xs font-semibold text-slate-700 cursor-pointer transition shrink-0">
                            <input type="checkbox" name="kenyaFriendly" value="true" class="h-4 w-4 rounded-full border-slate-300 text-[#3444da] focus:ring-[#3444da]">
                            <span class="text-slate-700">Kenya-Friendly</span>
                            <x-icon.kenya-flag class="h-3.5 w-3.5 shrink-0" />
                        </label>

                        {{-- Search CTA button in micro1 electric cobalt --}}
                        <button
                            type="submit"
                            class="btn-pop rounded-full bg-[#3444da] hover:bg-[#2633bf] px-8 py-3 text-sm font-bold text-white shadow-md shadow-[#3444da]/30 hover:shadow-lg transition-all shrink-0 text-center"
                        >
                            Find Roles &rarr;
                        </button>
                    </form>
                </div>

                {{-- micro1-Inspired Trending Role Capsules --}}
                <div class="mt-5 flex flex-wrap items-center justify-center gap-1.5 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px] mr-1">Trending:</span>
                    <a href="{{ url('/jobs?audience=students&q=annotation') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Data Annotation</a>
                    <a href="{{ url('/jobs?audience=students') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Student &amp; Campus</a>
                    <a href="{{ url('/remote-jobs/ai-training-annotation-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">AI Training &amp; RLHF</a>
                    <a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Writing &amp; Content</a>
                    <a href="{{ url('/remote-jobs/customer-support-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Customer Support</a>
                    <a href="{{ url('/remote-jobs/virtual-assistant-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Virtual Assistant</a>
                    <a href="{{ url('/remote-jobs/software-developer-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Software Engineer</a>
                    <a href="{{ url('/remote-jobs/entry-level-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Entry Level</a>
                </div>

                {{-- Trust Highlights Pill Bar --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200/80 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                        UTC+3 Timezone Aligned
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200/80 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                        Zero Foreign Visa Roadblocks
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200/80 px-3.5 py-1.5 shadow-2xs">
                        <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                        Verified USD, M-Pesa &amp; Wise Rails
                    </span>
                </div>
            </x-reveal>
        </div>
    </section>

    {{-- Official Telegram Channel Alert Banner — micro1 cobalt harmony --}}
    <div class="mx-auto max-w-4xl px-4 -mt-5 sm:-mt-7 mb-4 relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-3.5 rounded-2xl border border-[#c7ccfb] bg-[#eef0fe]/80 p-3.5 sm:p-4 text-xs sm:text-sm text-slate-800 shadow-xs backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#3444da] text-white shadow-xs">
                    <svg class="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/></svg>
                </span>
                <div>
                    <p class="font-extrabold text-slate-900 text-xs sm:text-sm">
                        Get Daily Remote Job Drops &amp; AI Career Support on Telegram
                    </p>
                    <p class="text-[11px] sm:text-xs text-slate-600 mt-0.5">
                        Free instant alerts when verified roles drop &bull; 24/7 Google AI career advice &bull; Pre-screened for Kenya
                    </p>
                </div>
            </div>
            <a
                href="{{ config('site.telegram_url', 'https://t.me/kenyaremotejobs') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-4 py-2 text-xs font-bold text-white shadow-xs transition shrink-0"
            >
                <span>Join @kenyaremotejobs</span> &rarr;
            </a>
        </div>
    </div>

    {{-- Featured Opportunities — micro1 3-Column Card Grid --}}
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Available Opportunities</h2>
                <p class="text-sm text-slate-500 mt-1">Vetted high-paying remote positions, AI training roles, and student contracts</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-4 py-1.5 text-xs font-bold text-[#3444da]">
                    <span class="h-2 w-2 rounded-full bg-[#3444da] animate-pulse"></span>
                    <span>{{ $total }} Live Listings</span>
                </span>
                <a href="{{ url('/jobs') }}" class="hidden sm:inline-flex items-center gap-1 text-xs font-bold text-[#3444da] hover:underline">
                    <span>View all</span> &rarr;
                </a>
            </div>
        </div>

        @if ($feed->isEmpty())
            <p class="rounded-2xl border border-slate-200/70 bg-white p-12 text-center text-slate-500 shadow-xs">
                Jobs are syncing — check back in a moment, or <a href="{{ url('/jobs') }}" class="text-[#3444da] font-semibold underline">browse everything</a>.
            </p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($feed as $i => $job)
                    <x-reveal :delay="min($i, 9) * 30">
                        <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                    </x-reveal>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center">
            <a href="{{ url('/jobs') }}" class="btn-pop inline-flex items-center gap-2 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-9 py-3.5 text-sm font-bold text-white shadow-md shadow-[#3444da]/25 hover:shadow-lg transition">
                <span>Browse All {{ $total }} Remote Jobs</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    {{-- 60-Second Remote Job Match Quiz --}}
    <section class="px-4 py-10 sm:px-6 bg-slate-50/60 border-y border-slate-200/70">
        <div class="mx-auto max-w-4xl">
            <x-match-quiz />
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
                        <a href="{{ url('/remote-jobs/'.$cat['slug']) }}" class="card-hover flex items-center justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white p-5 shadow-xs hover:border-[#3444da]/60 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                            <div class="flex items-center gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eef0fe] text-[#3444da] border border-[#c7ccfb]/60 shadow-2xs group-hover:scale-105 transition-transform">
                                    <x-icon :name="$cat['icon']" class="h-5 w-5" />
                                </span>
                                <span class="font-bold text-slate-800 text-sm group-hover:text-[#3444da] transition-colors">{{ $cat['label'] }}</span>
                            </div>
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover:bg-[#3444da] group-hover:text-white transition-colors text-sm font-semibold">&rarr;</span>
                        </a>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Not every "remote" job means Kenya — trust section --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-12 max-w-2xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da] mb-3">
                <x-icon name="shield" class="h-3.5 w-3.5 text-[#3444da]" />
                Vetting Standards
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900">Not every &ldquo;remote&rdquo; job means Kenya</h2>
            <p class="mt-3 text-slate-600 leading-relaxed">A lot of listings say &ldquo;remote&rdquo; and mean one country. Here&rsquo;s what we check on every single listing before it earns the badge.</p>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($trustFeatures as $i => $f)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-md transition-all duration-200">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#eef0fe] text-[#3444da] border border-[#c7ccfb] shadow-2xs">
                            <x-icon :name="$f['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed font-normal">{{ $f['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        <x-reveal class="mt-8 text-center">
            <a href="{{ url('/match') }}" class="inline-flex items-center gap-2 rounded-full border border-[#c7ccfb] bg-[#eef0fe]/80 px-5 py-2.5 text-sm font-bold text-[#3444da] hover:bg-[#eef0fe] transition shadow-2xs">
                <span>See how the Kenya-Friendly Match score works</span>
                <span>&rarr;</span>
            </a>
        </x-reveal>
    </section>

    {{-- How it works — Open 3-step visual journey --}}
    <section class="px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-5xl">
            <x-reveal class="text-center mb-12">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da] mb-2.5">
                    <x-icon name="sparkle" class="h-3.5 w-3.5 text-[#3444da]" />
                    Simple Process
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900">How KenyaRemoteJobs Works</h2>
            </x-reveal>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($howItWorks as $i => $step)
                    <x-reveal :delay="$i * 100">
                        <div class="relative h-full rounded-2xl border border-slate-200/70 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3444da] font-black text-white text-sm shadow-sm shadow-[#3444da]/20">
                                    {{ $i + 1 }}
                                </span>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#3444da]">Step 0{{ $i + 1 }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-lg">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed font-normal">{{ $step['body'] }}</p>
                        </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Pro Pays For Itself On Day 1 ROI Box --}}
    <section class="px-4 py-12 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-roi-card />
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="text-center mb-12">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da] mb-2.5">
                <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                Proven Results
            </span>
            <h2 class="text-3xl font-extrabold text-slate-900">Success Stories from Kenyan Talent</h2>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($testimonials as $i => $t)
                <x-reveal :delay="$i * 120">
                    <figure class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <blockquote class="text-sm sm:text-base text-slate-700 leading-relaxed font-normal">&ldquo;{{ $t['quote'] }}&rdquo;</blockquote>
                        <figcaption class="mt-6 pt-4 border-t border-slate-100 text-xs font-bold uppercase tracking-wider text-[#3444da] flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#eef0fe] text-[#3444da]">
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
            <a href="{{ url('/faqs') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200/80 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 hover:border-[#3444da] hover:text-[#3444da]">
                View all answered questions &rarr;
            </a>
        </div>
    </section>

    {{-- Final CTA banner --}}
    <section class="px-4 pb-12 sm:px-6">
        <x-reveal class="relative mx-auto max-w-6xl rounded-3xl bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/70 border border-slate-200/80 px-8 py-16 text-center text-slate-900 shadow-xl overflow-hidden">
            <div class="pointer-events-none absolute -right-16 -bottom-16 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-16 -top-16 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>

            <div class="relative z-10">
                <h2 class="text-3xl font-extrabold sm:text-4xl text-slate-900">Make it easier for the right opportunity to find you.</h2>
                <p class="mx-auto mt-4 max-w-xl text-slate-600 leading-relaxed font-normal">Get a match score against every listing, no CV upload required — takes under a minute.</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/match') }}" class="btn-pop rounded-full bg-[#3444da] hover:bg-[#2633bf] px-8 py-3.5 font-bold text-white shadow-md shadow-[#3444da]/20 transition">Get my match score &rarr;</a>
                    <a href="{{ url('/jobs') }}" class="rounded-full border border-slate-300 bg-white px-8 py-3.5 font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-[#3444da]">Browse all jobs</a>
                </div>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <x-reveal>
            <a href="{{ url('/surveys') }}" class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 sm:p-7 shadow-xs transition-all hover:border-[#3444da]/50 hover:shadow-md hover:-translate-y-0.5 sm:flex-row sm:items-center">
                <div>
                    <p class="flex items-center gap-2.5 font-bold text-slate-900 text-base">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#eef0fe] text-[#3444da] border border-[#c7ccfb] shadow-2xs">
                            <x-icon name="bulb" class="h-4 w-4" />
                        </span>
                        <span>Earn while you search</span>
                    </p>
                    <p class="text-sm text-slate-500 mt-1 pl-10.5 leading-relaxed font-normal">A curated list of legit paid-survey platforms as a side-income supplement.</p>
                </div>
                <span class="shrink-0 text-sm font-bold text-[#3444da]">Explore &rarr;</span>
            </a>
        </x-reveal>
    </section>
</x-layouts.app>
