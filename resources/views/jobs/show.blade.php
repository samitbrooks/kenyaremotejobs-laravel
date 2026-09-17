@php
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $visibleTags = $job->tags ?? [];
    $rawDescriptionBlocks = \App\Support\DescriptionBlocks::parse($job->description ?? '');
    $descriptionBlocks = $rawDescriptionBlocks;
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
    $title = $job->title." at {$job->company} — Remote Job".($job->kenya_friendly ? ' (Kenya-Friendly)' : '');
    $preview = \App\Support\Format::truncate($plainDescription, 155);
@endphp

<x-layouts.app :title="$title" :description="$preview" :canonical="url('/jobs/'.$job->id)">
    <script type="application/ld+json">{!! \App\Support\Seo::jobPostingJsonLd($job) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::breadcrumbJsonLd($job) !!}</script>

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a href="{{ url('/jobs') }}" class="text-sm font-semibold text-teal-700 hover:underline">&larr; Back to all jobs</a>

        <x-reveal>
            <div class="mt-4 flex items-start gap-4">
                <x-company-logo :company="$job->company" :size="64" />
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-extrabold sm:text-3xl text-slate-900">{{ $job->title }}</h1>
                    <p class="mt-1 text-base font-medium text-slate-500">{{ $job->company }}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-3.5 py-1 text-xs font-semibold text-amber-900">
                        <x-icon name="sparkle" class="h-3.5 w-3.5 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100/90 border border-slate-200/70 px-3.5 py-1 text-xs font-medium text-slate-700">
                        <x-icon name="check" class="h-3.5 w-3.5 text-teal-600" /> Open to Apply
                    </span>
                @endif
                @if (is_int($matchPercent))
                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200/80 px-3.5 py-1 text-xs font-bold text-teal-800">
                        <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" /> {{ $matchPercent }}% match for you
                    </span>
                @endif
                @if ($kesMonthly)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50/90 border border-emerald-200/80 px-3.5 py-1 text-xs font-bold text-emerald-950" title="Estimated monthly take-home in Kenyan Shillings (~130 KES/USD)">
                        <x-icon name="coin" class="h-3.5 w-3.5 text-emerald-700" /> {{ $kesMonthly }}
                    </span>
                @endif
                @if ($hourly)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50/90 border border-emerald-200/80 px-3.5 py-1 text-xs font-semibold text-emerald-800" title="Estimated from stated salary ÷ 2,080 hours/year">
                        <x-icon name="coin" class="h-3.5 w-3.5 text-emerald-700" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
                    </span>
                @endif
                @if ($job->kenya_friendly)
                    <x-kenya-badge />
                @endif
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/80 px-3.5 py-1 text-xs font-bold text-emerald-800">
                        <x-icon name="announce" class="h-3.5 w-3.5 text-emerald-700" /> Direct employer listing
                    </span>
                @endif
                <span class="text-sm font-medium text-slate-500">{{ $job->remote_type }}</span>
                <span class="text-sm font-medium text-slate-500">&middot; {{ $job->location }}</span>
                @if ($job->salary)
                    <span class="text-sm font-medium text-slate-500">&middot; {{ $job->salary }}</span>
                @endif
                <span class="text-sm font-medium text-slate-400">&middot; Posted {{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
            </div>

            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($visibleTags as $i => $tag)
                    <x-tag-chip :label="$tag" :index="$i" />
                @endforeach
            </div>

            @if (! empty($job->audience_segments))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($job->audience_segments as $segment)
                        <a href="{{ url('/jobs') }}?audience={{ $segment }}" class="inline-flex items-center gap-1.5 rounded-full bg-slate-100/90 border border-slate-200/80 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300">
                            <x-icon :name="\App\Support\Audience::ICONS[$segment] ?? 'globe'" class="h-3.5 w-3.5 text-teal-600" /> {{ \App\Support\Audience::LABELS[$segment] ?? $segment }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Smart Career Tools Bar: AI CV Tailoring & Application Tracker --}}
            <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200/80 bg-gradient-to-r from-teal-50/40 via-white to-slate-50/60 p-5 sm:p-6 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 border border-teal-100 shadow-2xs">
                        <x-icon name="sparkle" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-900">AI Application Copilot</p>
                        <p class="text-xs text-slate-500 mt-0.5">Generate role-tailored resume bullets & cover letter to pass ATS</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <livewire:ai-tailor-modal :job-id="$job->id" />
                    <livewire:track-application-button :job-id="$job->id" />
                </div>
            </div>

            <div class="mt-6">
                <x-job-at-a-glance :job="$job" :can-apply="$canApply" />
            </div>

            {{-- Full Unredacted Role Description --}}
            <div class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                <h2 class="mb-4 font-bold text-slate-900 text-lg sm:text-xl">About this role at {{ $job->company }}</h2>
                @if (\App\Support\Language::looksNonEnglish($plainDescription))
                    <livewire:translate-toggle :text="$plainDescription" :blocks="$descriptionBlocks" />
                @elseif ($descriptionBlocks)
                    <x-description-blocks :blocks="$descriptionBlocks" />
                @else
                    <div class="whitespace-pre-line text-sm leading-relaxed text-slate-700 font-normal">{!! nl2br(e($plainDescription)) !!}</div>
                @endif
            </div>

            {{-- Application Section --}}
            <div class="mt-8">
                @if ($canApply)
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 text-center shadow-lg">
                        @if ($job->origin === 'employer')
                            <p class="mb-4 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-4 py-1 text-xs font-bold text-emerald-800">
                                🇰🇪 Verified Direct Employer &middot; Pro Access Active
                            </p>
                        @elseif ($isEarlyAccess)
                            <p class="mb-4 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-4 py-1 text-xs font-bold text-emerald-800">
                                <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Pro Member Early Access Active
                            </p>
                        @endif
                        <h3 class="text-2xl font-extrabold text-slate-900">Ready to submit your application?</h3>
                        <p class="mt-1.5 text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                            Apply directly through {{ $job->company }}'s official recruitment portal.
                        </p>
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a
                                href="{{ $job->source_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-pop inline-flex items-center justify-center gap-2 rounded-full bg-teal-600 px-8 py-3.5 text-center font-bold text-white shadow-md shadow-teal-600/20 transition hover:bg-teal-700"
                            >
                                Apply Directly at {{ $job->company }} &#8599;
                            </a>
                            <livewire:track-application-button :job-id="$job->id" />
                        </div>
                        <p class="mt-4 text-xs text-slate-400">
                            Verified for remote applicants in Kenya &middot; Remember to tailor your CV with our AI Copilot before submitting!
                        </p>
                    </div>
                @elseif ($job->origin === 'employer')
                    {{-- Direct Employer Listing Seeking Kenyan Talent (Pro Exclusive) --}}
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 text-center shadow-xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-200 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-900 mb-4">
                            🇰🇪 Verified Employer Actively Seeking Kenyan Talent &middot; Full Access with Pro
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                            {{ $job->company }} is Exclusively Hiring in Kenya
                        </h3>
                        <p class="mt-2.5 text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                            {{ $job->company }} is specifically recruiting Kenyan talent. <strong class="text-slate-900">Pro Early Access gives you full access to apply to all direct employer and remote listings</strong> across the site.
                        </p>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-lg mx-auto text-left text-xs font-medium text-slate-700">
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">🇰🇪 No Visa Hurdles</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">Employer is already set up to hire Kenyan remote contractors.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">🎯 Direct Hiring Manager</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">Your application is delivered directly to their hiring inbox.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">✨ AI CV Copilot</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">Tailor your CV for {{ $job->company }} in 1 click.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a
                                href="{{ url('/pricing') }}"
                                class="btn-pop w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-teal-600 px-8 py-3.5 text-sm font-bold text-white shadow-md shadow-teal-600/20 hover:bg-teal-700 transition"
                            >
                                Get Full Access to All Jobs (KES 1,499/mo)
                            </a>
                            <livewire:ai-tailor-modal :job-id="$job->id" />
                        </div>
                        <p class="mt-3.5 text-xs text-slate-400">
                            Instant M-Pesa STK push &middot; Cancel anytime
                        </p>
                    </div>
                @else
                    {{-- 48-Hour Early Access High-Converting Box --}}
                    <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 text-center shadow-xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 border border-amber-200 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-900 mb-4">
                            <x-icon name="sparkle" class="h-4 w-4 text-amber-600" /> Early Access &middot; Full Access to All 800+ Jobs
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                            Beat 500+ Applicants to {{ $job->company }}
                        </h3>
                        <p class="mt-2.5 text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                            This role was published {{ \App\Support\Format::timeAgo($job->posted_at) }}. <strong class="text-slate-900">Early Access gives you full access to apply to all 800+ jobs immediately</strong> &mdash; public access opens in <span class="font-bold text-amber-900">{{ $job->earlyAccessHoursRemaining() }} hours</span>.
                        </p>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-lg mx-auto text-left text-xs font-medium text-slate-700">
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">⚡ Top of Recruiter Inbox</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">First 20 applicants receive 80% of recruiter interviews.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">🎯 AI ATS Match Tailoring</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">Instant role-targeted bullets & cover letters.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50/80 p-4 border border-slate-200/70 shadow-2xs">
                                <p class="font-bold text-slate-900">💼 Remote Contractor Toolkit</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">USD invoices, W-8BEN guide & CRM pipeline.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a
                                href="{{ url('/pricing') }}"
                                class="btn-pop w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-teal-600 px-8 py-3.5 text-sm font-bold text-white shadow-md shadow-teal-600/20 hover:bg-teal-700 transition"
                            >
                                Get Full Access to All Jobs (KES 1,499/mo)
                            </a>
                            <livewire:ai-tailor-modal :job-id="$job->id" />
                        </div>
                        <p class="mt-3.5 text-xs text-slate-400">
                            Instant M-Pesa STK push &middot; Cancel anytime &middot; Public apply opens in {{ $job->earlyAccessHoursRemaining() }} hours
                        </p>
                    </div>
                @endif
            </div>
        </x-reveal>

        @if (isset($relatedJobs) && $relatedJobs->isNotEmpty())
            <section class="mt-14 border-t border-slate-200 pt-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Related Remote Jobs Open to Kenya</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Explore similar international remote opportunities</p>
                    </div>
                    <a href="{{ url('/jobs') }}" class="text-xs font-bold text-teal-700 hover:underline">
                        See all jobs &rarr;
                    </a>
                </div>
                <div class="mt-6 space-y-3">
                    @foreach ($relatedJobs as $relJob)
                        <x-job-card :job="$relJob" :unlocked="$isUnlocked($relJob)" :match-percent="$matchPercent ? $matchPercent($relJob) : null" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
