<x-layouts.app
    :title="$collection['title']"
    :description="$collection['meta_description']"
    :canonical="url('/collections/'.$collection['slug'])"
>
    <script type="application/ld+json">{!! \App\Support\Seo::collectionPageJsonLd($collection, $jobs) !!}</script>
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/collections') }}" class="hover:text-foreground">Collections</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80 truncate max-w-[280px] sm:max-w-none">{{ $collection['title'] }}</span>
        </nav>

        {{-- Hero Header --}}
        <x-reveal class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-sm sm:p-12">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200 px-3 py-0.5 text-xs font-bold text-teal-800">
                        <x-icon :name="$collection['icon']" class="h-3.5 w-3.5 text-teal-600" />
                        {{ $collection['badge'] }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-0.5 text-xs font-semibold text-emerald-800">
                        ✓ Kenya Eligible &amp; Verified
                    </span>
                </div>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl leading-tight">
                    {{ $collection['h1'] }}
                </h1>

                <p class="mt-4 text-base text-slate-600 sm:text-lg leading-relaxed">
                    {{ $collection['intro'] }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <x-icon name="check" class="h-4 w-4 text-emerald-600" />
                        <span>Curated for East Africa Time (UTC+3)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <x-icon name="coin" class="h-4 w-4 text-teal-600" />
                        <span>USD &amp; Wise Payouts</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <x-icon name="globe" class="h-4 w-4 text-slate-600" />
                        <span>Zero Relocation Needed</span>
                    </div>
                </div>
            </div>
        </x-reveal>

        {{-- Live Open Jobs Grid --}}
        <div class="mt-12">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Available Remote Roles ({{ $jobs->count() }})</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Updated in real-time from vetted international employers</p>
                </div>
                <a href="{{ url('/jobs') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                    View full job board &rarr;
                </a>
            </div>

            @if ($jobs->isEmpty())
                <div class="mt-6 rounded-3xl border border-dashed border-black/10 bg-white p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-horizon-100 text-horizon-600">
                        <x-icon name="sparkle" class="h-6 w-6" />
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-foreground">New opportunities matching this collection are being vetted</h3>
                    <p class="mt-1 text-xs text-foreground/60 max-w-md mx-auto">
                        We add fresh remote vacancies daily. Check out our main directory or set up an instant job alert:
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-horizon-900 px-6 py-2.5 text-xs font-bold text-white hover:bg-black">
                            Explore All Jobs &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($jobs as $job)
                        <div class="h-full">
                            <x-job-card 
                                :job="$job" 
                                :unlocked="true" 
                                :matchPercent="$matchPercent ? $matchPercent($job) : null" 
                            />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Collection FAQs (Structured SEO Content) --}}
        @if (! empty($collection['faqs']))
            <div class="mt-16 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800">
                        <x-icon name="help" class="h-3.5 w-3.5 text-teal-600" />
                        Frequently Asked Questions
                    </span>
                    <h3 class="mt-3 text-2xl font-bold text-slate-900">
                        Everything Kenyan Remote Workers Need to Know
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Clear answers on compensation, payment rails, tax, and interview expectations.
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    @foreach ($collection['faqs'] as $faq)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5">
                            <h4 class="font-bold text-base text-slate-900 flex items-start gap-2.5">
                                <span class="text-teal-700 font-extrabold">Q:</span>
                                <span>{{ $faq['question'] }}</span>
                            </h4>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed pl-6">
                                {{ $faq['answer'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Job Alert Banner --}}
        <div class="mt-12">
            <x-job-alert-banner 
                :title="'Subscribe to New ' . $collection['badge'] . ' Alerts'"
                subtitle="Never miss an opening. Get instant email & WhatsApp notifications when matching international roles drop."
            />
        </div>

        {{-- Cross Links: Other Collections --}}
        @if (! empty($otherCollections))
            <div class="mt-16 border-t border-slate-200 pt-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Explore Other Curated Collections</h3>
                        <p class="text-xs text-slate-500">Find remote jobs matched to your work style</p>
                    </div>
                    <a href="{{ url('/collections') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                        View all collections &rarr;
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($otherCollections as $other)
                        <a
                            href="{{ url('/collections/'.$other['slug']) }}"
                            class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs transition hover:-translate-y-1 hover:border-teal-500 hover:shadow-md"
                        >
                            <span class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-semibold text-slate-800">
                                {{ $other['badge'] }}
                            </span>
                            <p class="font-bold text-sm text-slate-900 mt-2 group-hover:text-teal-700 transition-colors leading-snug">
                                {{ $other['title'] }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
