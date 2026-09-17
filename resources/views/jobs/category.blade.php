@php
    $canonicalUrl = url('/remote-jobs/'.$category['slug']);
@endphp

<x-layouts.app
    :title="$category['title']"
    :description="$category['meta_description']"
    :canonical="$canonicalUrl"
>
    <script type="application/ld+json">{!! \App\Support\Seo::categoryPageJsonLd($category['title'], $category['meta_description'], $canonicalUrl, $category['faqs'], $jobs) !!}</script>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/jobs') }}" class="hover:text-foreground">Remote Jobs</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80">{{ $category['name'] }}</span>
        </nav>

        {{-- Category Hero --}}
        <x-reveal class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 shadow-sm">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200/80 px-3.5 py-1 text-xs font-bold text-teal-800">
                        <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" />
                        {{ $category['badge'] }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/80 px-3.5 py-1 text-xs font-semibold text-emerald-800">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                        Verified for Kenya &amp; EAT Timezone
                    </span>
                </div>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                    {{ $category['h1'] }}
                </h1>

                <p class="mt-4 text-base leading-relaxed text-slate-600 sm:text-lg font-normal">
                    {{ $category['intro'] }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm">
                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 px-5 py-3 shadow-2xs">
                        <span class="block text-xs font-medium text-slate-500">Typical Compensation</span>
                        <strong class="font-bold text-emerald-700">{{ $category['salary_range'] }}</strong>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 px-5 py-3 shadow-2xs">
                        <span class="block text-xs font-medium text-slate-500">Open Positions</span>
                        <strong class="font-bold text-slate-900">{{ $totalJobs }} Active Listings</strong>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 px-5 py-3 shadow-2xs">
                        <span class="block text-xs font-medium text-slate-500">Location Reality</span>
                        <strong class="font-bold text-slate-900">No US/EU Visa Needed</strong>
                    </div>
                </div>
            </div>
        </x-reveal>

        {{-- Matching Jobs Section --}}
        <section class="mt-12">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
                        Open {{ $category['name'] }} Positions
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-600">
                        Real-time listings from global companies that hire remote talent living in Kenya.
                    </p>
                </div>
                <a href="{{ url('/jobs') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800 hover:underline">
                    View all 800+ jobs &rarr;
                </a>
            </div>

            @if ($jobs->isEmpty())
                <p class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
                    No active {{ $category['name'] }} listings right this second. Check back shortly as new roles sync hourly.
                </p>
            @else
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($jobs as $i => $job)
                        <x-reveal :delay="min($i, 8) * 50" class="h-full">
                            <x-job-card :job="$job" :unlocked="$isUnlocked($job)" :match-percent="$matchPercent($job)" />
                        </x-reveal>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Frequently Asked Questions for this category (SEO Anchor + User Help) --}}
        @if (! empty($category['faqs']))
            <section class="mt-16 rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-sm">
                <div class="max-w-3xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Got Questions?</span>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Frequently Asked Questions</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Common questions about finding and working in remote {{ strtolower($category['name']) }} roles from Kenya.
                    </p>

                    <div class="mt-8 space-y-6">
                        @foreach ($category['faqs'] as $faq)
                            <div class="border-b border-slate-100 pb-6 last:border-b-0 last:pb-0">
                                <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                                    {{ $faq['question'] }}
                                </h3>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                    {{ $faq['answer'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Cross-Linking: Explore Other Popular Remote Roles in Kenya --}}
        <section class="mt-14">
            <h2 class="text-xl font-bold text-slate-900">Explore Other Remote Roles in Kenya</h2>
            <p class="mt-1 text-sm text-slate-600">Browse other high-demand remote job categories hiring East African talent.</p>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($allCategories as $cat)
                    <a
                        href="{{ url('/remote-jobs/'.$cat['slug']) }}"
                        class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs transition duration-200 hover:-translate-y-0.5 hover:border-teal-500 hover:shadow-md"
                    >
                        <span class="text-xs font-semibold text-teal-700">{{ $cat['badge'] }}</span>
                        <h3 class="mt-1 text-sm font-bold text-slate-900 group-hover:text-teal-700">
                            {{ $cat['name'] }} Jobs
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 line-clamp-2">
                            {{ $cat['meta_description'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
