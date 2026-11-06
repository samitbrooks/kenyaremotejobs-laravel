@php
    $categories = [
        ['icon' => 'headset', 'label' => 'Transcription & Translation', 'slug' => 'transcription-translation-kenya'],
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
        ['question' => 'Is KenyaRemoteJobs free to use?', 'answer' => 'Yes! Browsing and discovering verified remote roles is 100% free. Pro Membership unlocks application links and career tools while employer identities remain hidden starting from KES 250/mo via M-Pesa.'],
        ['question' => 'How are job listings sourced and verified?', 'answer' => 'We partner directly with remote-first employers and continuously track verified global hiring networks that actively employ international talent. Every role undergoes rigorous screening for East Africa Time (UTC+3) compatibility, legitimate compensation, and zero hidden visa restrictions so you only apply to roles realistically open to Kenyans.'],
    ];

    $rotations = ['-rotate-2', 'rotate-1', '-rotate-1'];
    $offsets = ['top-0 left-4', 'top-44 left-16', 'top-[22rem] left-0'];
@endphp

<x-layouts.app :canonical="url('/')">
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($faqs) !!}</script>

    {{-- Listing count and job updates --}}
    <x-live-ticker :count="$total" />

    {{-- Job search introduction --}}
    <section class="relative border-b border-slate-200 bg-white px-4 py-10 sm:px-6 sm:py-14">
        <div class="relative mx-auto max-w-6xl text-left">
            <x-reveal>
                <p class="text-sm font-medium text-[#3444da]">KenyaRemoteJobs &middot; Work from Kenya</p>

                <h1 class="mt-4 max-w-3xl text-3xl font-semibold tracking-tight text-slate-900 sm:text-5xl sm:leading-[1.15]">
                    Find remote roles open to Kenya.
                    <br class="hidden sm:inline">
                    <span class="text-[#3444da]">Prepare your application with Pro.</span>
                </h1>

                <p class="mt-4 max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    Start with roles that fit your skills. Review requirements and Kenya eligibility signals for free, then use Pro application links, CV tailoring, and tracking tools from KES 250/month.
                </p>
            </x-reveal>

            <div class="mt-6 flex flex-wrap justify-start gap-3">
                <a href="{{ url('/match') }}" class="rounded-lg bg-[#3444da] px-6 py-3 text-sm font-bold text-white">Find roles for my skills &rarr;</a>
                <a href="#membership-preview" class="rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700">See what Pro helps me do</a>
            </div>
            <p class="mt-3 text-xs text-slate-500">Free matching. No CV upload needed. Employer details stay hidden.
                <a href="{{ route('jobs.index', ['onlineWork' => 'true', 'directApply' => 'true', 'kenyaFriendly' => 'true']) }}" class="ml-2 font-bold text-[#3444da] underline">Browse practical online work</a></p>

            {{-- Search filters --}}
            <x-reveal :delay="80" class="relative z-10 mt-8">
                <div class="rounded-xl border border-slate-200/90 bg-white p-2 sm:p-2.5 transition-all">
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
                            <input type="checkbox" name="kenyaFriendly" value="true" class="h-4 w-4 rounded-lg border-slate-300 text-[#3444da] focus:ring-[#3444da]">
                            <span class="text-slate-700">Kenya-Friendly</span>
                            <x-icon.kenya-flag class="h-3.5 w-3.5 shrink-0" />
                        </label>

                        {{-- Search CTA button in micro1 electric cobalt --}}
                        <button
                            type="submit"
                            class="btn-pop rounded-lg bg-[#3444da] hover:bg-[#2633bf] px-8 py-3 text-sm font-bold text-white shadow-sm hover:shadow-sm transition-all shrink-0 text-center"
                        >
                            Find Roles &rarr;
                        </button>
                    </form>
                </div>

                {{-- Popular searches --}}
                <div class="mt-5 flex flex-wrap items-center justify-center gap-1.5 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px] mr-1">Trending:</span>
                    <a href="{{ url('/remote-jobs/transcription-translation-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da] shadow-2xs">Transcription</a>
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
                <div class="mt-6 flex flex-wrap items-center justify-start gap-4 text-xs font-medium text-slate-600">
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
    <div class="mx-auto max-w-6xl px-4 py-4 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3.5 rounded-2xl border border-[#c7ccfb] bg-[#eef0fe]/80 p-3.5 sm:p-4 text-xs sm:text-sm text-slate-800 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#3444da] text-white shadow-xs">
                    <svg class="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/></svg>
                </span>
                <div>
                    <p class="font-semibold text-slate-900 text-xs sm:text-sm">
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
                class="btn-pop inline-flex items-center gap-1.5 rounded-lg bg-[#3444da] hover:bg-[#2633bf] px-4 py-2 text-xs font-bold text-white shadow-xs transition shrink-0"
            >
                <span>Join @kenyaremotejobs</span> &rarr;
            </a>
        </div>
    </div>

    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6" aria-labelledby="popular-categories-heading">
        <div class="mb-5">
            <h2 id="popular-categories-heading" class="text-2xl font-semibold text-slate-900">Popular Job Categories</h2>
            <p class="mt-1 text-sm text-slate-500">Explore transcription, writing, and more remote opportunities from Kenya.</p>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($categories as $category)
                <a href="{{ url('/remote-jobs/'.$category['slug']) }}" class="group flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs transition hover:border-[#3444da] hover:bg-[#eef0fe] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3444da]">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#eef0fe] text-[#3444da]">
                        <x-icon :name="$category['icon']" class="h-5 w-5" />
                    </span>
                    <span class="text-sm font-bold text-slate-800 group-hover:text-[#3444da]">{{ $category['label'] }}</span>
                    <span class="text-xs font-semibold text-[#3444da]">Explore jobs &rarr;</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Opportunities — micro1 3-Column Card Grid --}}
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900 tracking-tight">Available Opportunities</h2>
                <p class="text-sm text-slate-500 mt-1">Vetted entry-level remote positions, AI training &amp; data annotation, and flexible campus contracts open to Kenya</p>
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

        {{-- Quick Accessibility Filter Pills --}}
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ url('/remote-jobs/transcription-translation-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">Transcription &amp; Captioning</a>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#3444da] text-white px-3.5 py-1.5 font-bold shadow-xs">
                <x-icon name="sparkle" class="h-3.5 w-3.5" /> Practical Online Work
            </span>
            <a href="{{ url('/remote-jobs/ai-training-annotation-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">
                AI Model Training &amp; Annotation
            </a>
            <a href="{{ url('/jobs?audience=students') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">
                Student &amp; Campus Friendly
            </a>
            <a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">
                Writing &amp; Content
            </a>
            <a href="{{ url('/jobs?audience=highschool') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">
                No Degree Required
            </a>
            <a href="{{ url('/jobs?audience=part-time') }}" class="jobs-skill-pill hover:border-[#3444da] hover:text-[#3444da]">
                Flexible Part-Time
            </a>
        </div>

        @if ($feed->isEmpty())
            <p class="rounded-2xl border border-slate-200/70 bg-white p-12 text-center text-slate-500 shadow-xs">
                Jobs are syncing — check back in a moment, or <a href="{{ url('/jobs') }}" class="text-[#3444da] font-semibold underline">browse everything</a>.
            </p>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($feed as $i => $job)
                    <x-reveal :delay="min($i, 9) * 30">
                        <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                    </x-reveal>
                @endforeach
            </div>
        @endif

        <div class="mt-12 text-center">
            <a href="{{ url('/jobs') }}" class="btn-pop inline-flex items-center gap-2 rounded-lg bg-[#3444da] hover:bg-[#2633bf] px-9 py-3.5 text-sm font-bold text-white shadow-sm hover:shadow-sm transition">
                <span>Browse All {{ $total }} Remote Jobs</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6"><x-membership-preview /></div>

    {{-- 60-Second Remote Job Match Quiz --}}
    <section class="px-4 py-10 sm:px-6 bg-slate-50/60 border-y border-slate-200/70">
        <div class="mx-auto max-w-4xl">
            <x-match-quiz />
        </div>
    </section>



    {{-- Not every "remote" job means Kenya — trust section --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-12 max-w-2xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da] mb-3">
                <x-icon name="shield" class="h-3.5 w-3.5 text-[#3444da]" />
                Vetting Standards
            </span>
            <h2 class="text-3xl font-semibold text-slate-900">Not every &ldquo;remote&rdquo; job means Kenya</h2>
            <p class="mt-3 text-slate-600 leading-relaxed">A lot of listings say &ldquo;remote&rdquo; and mean one country. Here&rsquo;s what we check on every single listing before it earns the badge.</p>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($trustFeatures as $i => $f)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-sm transition-all duration-200">
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
            <a href="{{ url('/match') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#c7ccfb] bg-[#eef0fe]/80 px-5 py-2.5 text-sm font-bold text-[#3444da] hover:bg-[#eef0fe] transition shadow-2xs">
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
                <h2 class="text-3xl font-semibold text-slate-900">How KenyaRemoteJobs Works</h2>
            </x-reveal>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($howItWorks as $i => $step)
                    <x-reveal :delay="$i * 100">
                        <div class="relative h-full rounded-2xl border border-slate-200/70 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-sm transition-all">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#3444da] font-semibold text-white text-sm shadow-sm">
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
            <h2 class="text-3xl font-semibold text-slate-900">Success Stories from Kenyan Talent</h2>
        </x-reveal>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            @foreach ($testimonials as $i => $t)
                <x-reveal :delay="$i * 120">
                    <figure class="card-hover h-full rounded-2xl border border-slate-200/60 bg-white p-7 shadow-xs hover:border-[#3444da]/50 hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
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
            <h2 class="text-3xl font-semibold text-slate-900">Frequently Asked Questions</h2>
        </x-reveal>
        <div class="space-y-4">
            @foreach ($faqs as $i => $faq)
                <x-reveal :delay="min($i, 5) * 70">
                    <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                </x-reveal>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ url('/faqs') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200/80 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 hover:border-[#3444da] hover:text-[#3444da]">
                View all answered questions &rarr;
            </a>
        </div>
    </section>

    {{-- Final CTA banner --}}
    <section class="px-4 pb-12 sm:px-6">
        <x-reveal class="relative mx-auto max-w-6xl rounded-3xl border border-slate-200/80 px-8 py-16 text-center text-slate-900 shadow-sm overflow-hidden">

            <div class="relative z-10">
                <h2 class="text-2xl font-semibold sm:text-3xl text-slate-900">Make it easier for the right opportunity to find you.</h2>
                <p class="mx-auto mt-4 max-w-xl text-slate-600 leading-relaxed font-normal">Get a match score against every listing, no CV upload required — takes under a minute.</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ url('/match') }}" class="btn-pop rounded-lg bg-[#3444da] hover:bg-[#2633bf] px-8 py-3.5 font-bold text-white shadow-sm transition">Get my match score &rarr;</a>
                    <a href="{{ url('/jobs') }}" class="rounded-lg border border-slate-300 bg-white px-8 py-3.5 font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-[#3444da]">Browse all jobs</a>
                </div>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <x-reveal>
            <a href="{{ url('/surveys') }}" class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 sm:p-7 shadow-xs transition-all hover:border-[#3444da]/50 hover:shadow-sm sm:flex-row sm:items-center">
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
