<x-layouts.app
    :title="$company['name'].' Remote Jobs in Kenya — Hiring Guide & Open Roles (2026)'"
    :description="'Everything you need to know about working remotely for '.$company['name'].' from Kenya: USD salaries, payment methods, hiring model (EOR/B2B), and live job listings.'"
    :canonical="url('/companies/'.$company['slug'])"
>
    <script type="application/ld+json">{!! \App\Support\Seo::companyJsonLd($company) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::companyBreadcrumbJsonLd($company) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($company['faqs']) !!}</script>

    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/companies') }}" class="hover:text-foreground">Companies</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80 truncate">{{ $company['name'] }}</span>
        </nav>

        {{-- Company Hero Card --}}
        <x-reveal>
            <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-black/5 pb-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-black/10 bg-horizon-900 text-2xl font-black text-white shadow-md">
                            {{ substr($company['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-foreground">{{ $company['name'] }}</h1>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-900">
                                    <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                                    Hires in Kenya
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-foreground/60">{{ $company['headline'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                            href="{{ $company['website'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-full border border-black/10 bg-horizon-50 px-4 py-2 text-xs font-semibold text-foreground transition hover:bg-horizon-100"
                        >
                            Official Website &#8599;
                        </a>
                        <a
                            href="#open-jobs"
                            class="btn-pop inline-flex items-center gap-1.5 rounded-full gradient-sunrise px-5 py-2 text-xs font-bold text-white shadow-md hover:opacity-95"
                        >
                            View Open Jobs &darr;
                        </a>
                    </div>
                </div>

                {{-- Key Facts Grid --}}
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4 text-xs">
                    <div class="rounded-2xl bg-horizon-50/60 p-4 border border-black/5">
                        <p class="font-semibold text-foreground/50 uppercase tracking-wider text-[10px]">Headquarters</p>
                        <p class="mt-1 font-bold text-foreground">{{ $company['headquarters'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-horizon-50/60 p-4 border border-black/5">
                        <p class="font-semibold text-foreground/50 uppercase tracking-wider text-[10px]">Industry</p>
                        <p class="mt-1 font-bold text-foreground">{{ $company['industry'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-horizon-50/60 p-4 border border-black/5">
                        <p class="font-semibold text-foreground/50 uppercase tracking-wider text-[10px]">Company Size</p>
                        <p class="mt-1 font-bold text-foreground">{{ $company['size'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-horizon-50/60 p-4 border border-black/5">
                        <p class="font-semibold text-foreground/50 uppercase tracking-wider text-[10px]">Timezone Alignment</p>
                        <p class="mt-1 font-bold text-emerald-800">EAT Compatible (UTC+3)</p>
                    </div>
                </div>
            </div>
        </x-reveal>

        {{-- How Hiring in Kenya Works at this Company --}}
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="md:col-span-2 space-y-6">
                {{-- Overview & Why They Hire in Kenya --}}
                <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-foreground">About {{ $company['name'] }}'s Remote Culture</h2>
                    <p class="mt-3 text-sm leading-relaxed text-foreground/80">{{ $company['description'] }}</p>

                    <h3 class="mt-6 font-bold text-foreground">Why {{ $company['name'] }} Hires in Kenya</h3>
                    <p class="mt-2 text-sm leading-relaxed text-foreground/80">{{ $company['why_kenya'] }}</p>

                    <h3 class="mt-6 font-bold text-foreground">Typical Roles Hired in East Africa</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($company['common_roles'] as $role)
                            <span class="rounded-xl bg-horizon-100 px-3 py-1.5 text-xs font-semibold text-horizon-900">
                                {{ $role }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- FAQs Accordion --}}
                <div class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-foreground mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        @foreach ($company['faqs'] as $faq)
                            <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar: Logistics, Payments & Perks --}}
            <div class="space-y-6">
                <div class="rounded-3xl border border-black/5 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-foreground/60 mb-4">Hiring Details</h3>

                    <div class="space-y-4 text-xs">
                        <div>
                            <p class="font-semibold text-foreground/50">Contract / Hiring Model</p>
                            <p class="mt-0.5 font-bold text-foreground">{{ $company['hiring_model'] }}</p>
                        </div>
                        <div class="border-t border-black/5 pt-3">
                            <p class="font-semibold text-foreground/50">Payment Rails</p>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach ($company['payment_methods'] as $method)
                                    <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-100">
                                        {{ $method }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="border-t border-black/5 pt-3">
                            <p class="font-semibold text-foreground/50">Working Hours &amp; Overlap</p>
                            <p class="mt-0.5 text-foreground/80 leading-relaxed">{{ $company['eat_overlap_hours'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-black/5 bg-gradient-to-br from-horizon-50 to-white p-6 shadow-sm">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-foreground/60 mb-3">Key Perks &amp; Benefits</h3>
                    <ul class="space-y-2 text-xs text-foreground/80">
                        @foreach ($company['perks'] as $perk)
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>{{ $perk }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Live Open Jobs Section --}}
        <div id="open-jobs" class="mt-16 scroll-mt-10">
            <div class="flex items-center justify-between border-b border-black/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-foreground">Open Remote Positions at {{ $company['name'] }}</h2>
                    <p class="text-xs text-foreground/60 mt-0.5">Pre-screened for East Africa timezone and Kenyan applicant eligibility</p>
                </div>
                <span class="rounded-full bg-horizon-100 px-3 py-1 text-xs font-bold text-horizon-900">
                    {{ $jobs->count() }} {{ \Illuminate\Support\Str::plural('listing', $jobs->count()) }}
                </span>
            </div>

            @if ($jobs->isEmpty())
                <div class="mt-6 rounded-3xl border border-dashed border-black/10 bg-white p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-horizon-100 text-horizon-600">
                        <x-icon name="clock" class="h-6 w-6" />
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-foreground">No active vacancies at {{ $company['name'] }} right now</h3>
                    <p class="mt-1 text-xs text-foreground/60 max-w-md mx-auto">
                        {{ $company['name'] }} recruits in seasonal waves. In the meantime, explore similar verified international remote roles:
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-horizon-900 px-6 py-2.5 text-xs font-bold text-white hover:bg-black">
                            Browse All 800+ Remote Jobs &rarr;
                        </a>
                        <a href="{{ url('/companies') }}" class="rounded-full border border-black/10 px-5 py-2.5 text-xs font-semibold hover:bg-horizon-50">
                            View Other Hiring Companies
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($jobs as $job)
                        <div class="h-full">
                            <x-job-card :job="$job" :unlocked="true" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Explore Other Companies --}}
        @if (! empty($otherCompanies))
            <div class="mt-16 border-t border-black/5 pt-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-foreground">Other Top Remote Companies Hiring in Kenya</h3>
                        <p class="text-xs text-foreground/50">Explore more vetted international employers</p>
                    </div>
                    <a href="{{ url('/companies') }}" class="text-xs font-semibold text-sunrise-600 hover:underline">
                        View all companies &rarr;
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($otherCompanies as $other)
                        <a
                            href="{{ url('/companies/'.$other['slug']) }}"
                            class="group rounded-2xl border border-black/5 bg-white p-5 shadow-xs transition hover:-translate-y-1 hover:shadow-md"
                        >
                            <p class="font-bold text-foreground group-hover:text-sunrise-600 transition-colors">{{ $other['name'] }}</p>
                            <p class="text-xs text-foreground/50 mt-0.5">{{ $other['industry'] }}</p>
                            <p class="text-xs text-foreground/70 mt-2 line-clamp-2">{{ $other['headline'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
