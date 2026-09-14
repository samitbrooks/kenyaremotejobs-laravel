<x-layouts.app
    title="Top Global Remote Companies Hiring in Kenya (2026 Directory)"
    description="Browse verified international remote-first companies actively hiring Kenyan talent. View hiring models (EOR vs B2B), USD payment methods (Wise/M-Pesa), and live job openings."
    :canonical="url('/companies')"
>
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80">Companies</span>
        </nav>

        {{-- Hero Header --}}
        <x-reveal class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-200 px-3.5 py-1 text-xs font-bold text-emerald-900">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                Verified Employer Directory
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-horizon-950 sm:text-4xl lg:text-5xl leading-tight">
                Top Global Companies Hiring Remotely in Kenya
            </h1>
            <p class="mt-4 text-base text-foreground/70 sm:text-lg leading-relaxed">
                Skip the guesswork. These international remote-first companies explicitly hire across East Africa with competitive USD salaries, zero visa hurdles, and reliable payment rails.
            </p>
        </x-reveal>

        {{-- Quick Stats Banner --}}
        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-black/5 bg-white p-4 text-center shadow-xs">
                <p class="text-2xl font-black text-sunrise-600">100%</p>
                <p class="text-xs text-foreground/60 mt-0.5">Vetted for Kenya</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-4 text-center shadow-xs">
                <p class="text-2xl font-black text-horizon-900">USD &amp; Wise</p>
                <p class="text-xs text-foreground/60 mt-0.5">Reliable Payments</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-4 text-center shadow-xs">
                <p class="text-2xl font-black text-emerald-700">0 Visa</p>
                <p class="text-xs text-foreground/60 mt-0.5">No Relocation Needed</p>
            </div>
            <div class="rounded-2xl border border-black/5 bg-white p-4 text-center shadow-xs">
                <p class="text-2xl font-black text-horizon-800">EAT Fit</p>
                <p class="text-xs text-foreground/60 mt-0.5">UTC+3 Timezone Aligned</p>
            </div>
        </div>

        {{-- Companies Grid --}}
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($companies as $i => $company)
                <x-reveal :delay="min($i, 9) * 50" class="h-full">
                    <a
                        href="{{ url('/companies/'.$company['slug']) }}"
                        class="group flex h-full flex-col justify-between rounded-3xl border border-black/5 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-horizon-300 hover:shadow-xl"
                    >
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-black/5 bg-horizon-50 text-xl font-bold text-horizon-900 shadow-xs">
                                        {{ substr($company['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-lg text-foreground group-hover:text-sunrise-600 transition-colors">
                                            {{ $company['name'] }}
                                        </h2>
                                        <p class="text-xs text-foreground/50">{{ $company['industry'] }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">
                                    ✓ Kenya Open
                                </span>
                            </div>

                            <p class="mt-4 text-xs font-semibold text-horizon-800">{{ $company['headline'] }}</p>
                            <p class="mt-2 line-clamp-3 text-xs text-foreground/70 leading-relaxed">{{ $company['description'] }}</p>

                            <div class="mt-4 space-y-2 border-t border-black/5 pt-3 text-[11px]">
                                <div class="flex items-center justify-between text-foreground/60">
                                    <span>Hiring Model:</span>
                                    <strong class="text-foreground/80 font-medium truncate max-w-[150px]">{{ $company['hiring_model'] }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-foreground/60">
                                    <span>Payment Rails:</span>
                                    <strong class="text-foreground/80 font-medium truncate max-w-[150px]">{{ implode(', ', $company['payment_methods']) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-black/5 pt-3">
                            <span class="text-xs font-semibold text-sunrise-600 group-hover:underline">
                                Explore Company Profile &rarr;
                            </span>
                            @if ($company['open_jobs_count'] > 0)
                                <span class="rounded-full bg-horizon-100 px-2.5 py-0.5 text-[11px] font-bold text-horizon-800">
                                    {{ $company['open_jobs_count'] }} open {{ \Illuminate\Support\Str::plural('role', $company['open_jobs_count']) }}
                                </span>
                            @endif
                        </div>
                    </a>
                </x-reveal>
            @endforeach
        </div>

        {{-- B2B Employer CTA Banner --}}
        <div class="mt-16 rounded-3xl border border-horizon-200 bg-gradient-to-r from-horizon-900 via-horizon-800 to-slate-900 p-8 text-white text-center shadow-xl sm:p-10">
            <h2 class="text-2xl font-bold sm:text-3xl">Are you an employer hiring remote talent in Kenya?</h2>
            <p class="mt-2 text-sm text-white/70 max-w-xl mx-auto">
                Get your company listed in our verified employer directory and deliver your open roles directly to over 25,000 skilled Kenyan professionals.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ url('/employers/post') }}" class="btn-pop rounded-full bg-sunrise-500 px-8 py-3.5 font-bold text-white shadow-lg transition hover:bg-sunrise-600">
                    Post a Job &amp; Get Listed &rarr;
                </a>
                <a href="{{ url('/employers') }}" class="rounded-full border border-white/20 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    Learn Why Companies Hire in Kenya
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
