<x-layouts.app
    title="Curated Remote Job Collections for Kenyan Professionals (2026)"
    description="Explore handpicked remote job collections tailored for Kenya: USD payment via Wise/M-Pesa, zero visa restrictions, entry-level opportunities, high-paying $3,000+ roles, and EAT-friendly hours."
    :canonical="url('/collections')"
>
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80">Collections</span>
        </nav>

        {{-- Hero Header --}}
        <x-reveal class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunrise-100 border border-sunrise-200 px-3.5 py-1 text-xs font-bold text-sunrise-900">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-sunrise-600" />
                Curated Remote Job Hubs
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-horizon-950 sm:text-4xl lg:text-5xl leading-tight">
                High-Intent Remote Job Collections for Kenya
            </h1>
            <p class="mt-4 text-base text-foreground/70 sm:text-lg leading-relaxed">
                Filter through the noise. We have hand-curated the most in-demand remote work categories specifically vetted for East African timezones, payment rails, and work authorization.
            </p>
        </x-reveal>

        {{-- Collections Grid --}}
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($collections as $i => $collection)
                <x-reveal :delay="min($i, 5) * 60" class="h-full">
                    <a
                        href="{{ url('/collections/'.$collection['slug']) }}"
                        class="group flex h-full flex-col justify-between rounded-3xl border border-black/5 bg-white p-7 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-horizon-300 hover:shadow-xl"
                    >
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-black/5 bg-horizon-50 text-horizon-900 shadow-xs group-hover:bg-sunrise-50 group-hover:text-sunrise-600 transition-colors">
                                    <x-icon :name="$collection['icon']" class="h-6 w-6" />
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-horizon-50 border border-horizon-200 px-2.5 py-0.5 text-[11px] font-semibold text-horizon-900">
                                    {{ $collection['badge'] }}
                                </span>
                            </div>

                            <h2 class="mt-5 font-bold text-xl text-foreground group-hover:text-sunrise-600 transition-colors leading-snug">
                                {{ $collection['title'] }}
                            </h2>

                            <p class="mt-3 text-xs text-foreground/70 leading-relaxed">
                                {{ $collection['intro'] }}
                            </p>

                            @if (! empty($collection['faqs']))
                                <div class="mt-4 rounded-xl bg-horizon-50/60 p-3 text-[11px] text-foreground/75 border border-horizon-100">
                                    <span class="font-semibold text-horizon-900">FAQ:</span> {{ $collection['faqs'][0]['question'] }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-black/5 pt-4">
                            <span class="text-xs font-semibold text-sunrise-600 group-hover:underline">
                                Browse Collection &rarr;
                            </span>
                            <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                {{ $collection['jobs_count'] }} {{ \Illuminate\Support\Str::plural('role', $collection['jobs_count']) }}
                            </span>
                        </div>
                    </a>
                </x-reveal>
            @endforeach
        </div>

        {{-- Newsletter Job Alert Banner --}}
        <div class="mt-16">
            <x-job-alert-banner 
                title="Never Miss a New Kenya-Friendly Remote Job"
                subtitle="Get instant alerts for USD roles, Wise payouts, and zero-visa openings delivered to your inbox."
            />
        </div>
    </div>
</x-layouts.app>
