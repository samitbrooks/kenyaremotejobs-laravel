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
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200/80 bg-slate-900 text-2xl font-black text-white shadow-2xs">
                            {{ substr($company['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $company['name'] }}</h1>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-0.5 text-xs font-bold text-emerald-900">
                                    <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" />
                                    Hires in Kenya
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $company['headline'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                            href="{{ $company['website'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-full border border-slate-200/80 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Official Website &#8599;
                        </a>
                        <a
                            href="#open-jobs"
                            class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700 transition"
                        >
                            View Open Jobs &darr;
                        </a>
                    </div>
                </div>

                {{-- Key Facts Grid --}}
                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4 text-xs">
                    <div class="rounded-2xl bg-slate-50/70 p-4 border border-slate-200/70 shadow-2xs">
                        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Headquarters</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $company['headquarters'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50/70 p-4 border border-slate-200/70 shadow-2xs">
                        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Industry</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $company['industry'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50/70 p-4 border border-slate-200/70 shadow-2xs">
                        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Company Size</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $company['size'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50/70 p-4 border border-slate-200/70 shadow-2xs">
                        <p class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Timezone Alignment</p>
                        <p class="mt-1 font-bold text-emerald-800">EAT Compatible (UTC+3)</p>
                    </div>
                </div>
            </div>
        </x-reveal>

        {{-- How Hiring in Kenya Works at this Company --}}
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="md:col-span-2 space-y-6">
                {{-- Overview & Why They Hire in Kenya --}}
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">About {{ $company['name'] }}'s Remote Culture</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $company['description'] }}</p>

                    <h3 class="mt-6 font-bold text-slate-900">Why {{ $company['name'] }} Hires in Kenya</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $company['why_kenya'] }}</p>

                    <h3 class="mt-6 font-bold text-slate-900">Typical Roles Hired in East Africa</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($company['common_roles'] as $role)
                            <span class="rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-800">
                                {{ $role }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- FAQs Accordion --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        @foreach ($company['faqs'] as $faq)
                            <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar: Logistics, Payments & Perks --}}
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-slate-400 mb-4">Hiring Details</h3>

                    <div class="space-y-4 text-xs">
                        <div>
                            <p class="font-semibold text-slate-500">Contract / Hiring Model</p>
                            <p class="mt-0.5 font-bold text-slate-900">{{ $company['hiring_model'] }}</p>
                        </div>
                        <div class="border-t border-slate-100 pt-3">
                            <p class="font-semibold text-slate-500">Payment Rails</p>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach ($company['payment_methods'] as $method)
                                    <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800 border border-emerald-100">
                                        {{ $method }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="border-t border-slate-100 pt-3">
                            <p class="font-semibold text-slate-500">Working Hours &amp; Overlap</p>
                            <p class="mt-0.5 text-slate-600 leading-relaxed">{{ $company['eat_overlap_hours'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-slate-400 mb-3">Key Perks &amp; Benefits</h3>
                    <ul class="space-y-2 text-xs text-slate-600">
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
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Open Remote Positions at {{ $company['name'] }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pre-screened for East Africa timezone and Kenyan applicant eligibility</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800">
                    {{ $jobs->count() }} {{ \Illuminate\Support\Str::plural('listing', $jobs->count()) }}
                </span>
            </div>

            @if ($jobs->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-teal-700">
                        <x-icon name="clock" class="h-6 w-6" />
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">No active vacancies at {{ $company['name'] }} right now</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                        {{ $company['name'] }} recruits in seasonal waves. In the meantime, explore similar verified international remote roles:
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-teal-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-teal-700">
                            Browse All 800+ Remote Jobs &rarr;
                        </a>
                        <a href="{{ url('/companies') }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            View Other Hiring Companies
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-6 space-y-3">
                    @foreach ($jobs as $job)
                        <x-job-card :job="$job" :unlocked="true" />
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Explore Other Companies --}}
        @if (! empty($otherCompanies))
            <div class="mt-16 border-t border-slate-200 pt-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Other Top Remote Companies Hiring in Kenya</h3>
                        <p class="text-xs text-slate-500">Explore more vetted international employers</p>
                    </div>
                    <a href="{{ url('/companies') }}" class="text-xs font-semibold text-teal-700 hover:underline">
                        View all companies &rarr;
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($otherCompanies as $other)
                        <a
                            href="{{ url('/companies/'.$other['slug']) }}"
                            class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs transition hover:-translate-y-1 hover:border-teal-500 hover:shadow-md"
                        >
                            <p class="font-bold text-slate-900 group-hover:text-teal-700 transition-colors">{{ $other['name'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $other['industry'] }}</p>
                            <p class="text-xs text-slate-600 mt-2 line-clamp-2">{{ $other['headline'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
