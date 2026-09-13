@php
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $visibleTags = $job->tags ?? [];
    $rawDescriptionBlocks = \App\Support\DescriptionBlocks::parse($job->description ?? '');
    $descriptionBlocks = $rawDescriptionBlocks;
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $title = $job->title." at {$job->company} — Remote Job".($job->kenya_friendly ? ' (Kenya-Friendly)' : '');
    $preview = \App\Support\Format::truncate($plainDescription, 155);
@endphp

<x-layouts.app :title="$title" :description="$preview">
    <script type="application/ld+json">{!! \App\Support\Seo::jobPostingJsonLd($job) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::breadcrumbJsonLd($job) !!}</script>

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a href="{{ url('/jobs') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to all jobs</a>

        <x-reveal>
            <div class="mt-4 flex items-start gap-4">
                <x-company-logo :company="$job->company" :size="64" />
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold sm:text-3xl text-foreground">{{ $job->title }}</h1>
                    <p class="mt-1 text-base font-medium text-foreground/70">{{ $job->company }}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-200 px-3 py-1 text-xs font-semibold text-amber-900">
                        <x-icon name="sparkle" class="h-3.5 w-3.5 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Open to Apply
                    </span>
                @endif
                @if (is_int($matchPercent))
                    <span class="inline-flex items-center gap-1 rounded-full bg-horizon-600 px-3 py-1 text-xs font-semibold text-white">
                        <x-icon name="sparkle" class="h-3.5 w-3.5" /> {{ $matchPercent }}% match for you
                    </span>
                @endif
                @if ($hourly)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800" title="Estimated from stated salary ÷ 2,080 hours/year">
                        <x-icon name="coin" class="h-3.5 w-3.5" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
                    </span>
                @endif
                @if ($job->kenya_friendly)
                    <x-kenya-badge />
                @endif
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                        <x-icon name="announce" class="h-3.5 w-3.5" /> Direct employer listing
                    </span>
                @endif
                <span class="text-sm text-foreground/50">{{ $job->remote_type }}</span>
                <span class="text-sm text-foreground/50">&middot; {{ $job->location }}</span>
                @if ($job->salary)
                    <span class="text-sm text-foreground/50">&middot; {{ $job->salary }}</span>
                @endif
                <span class="text-sm text-foreground/50">&middot; Posted {{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
            </div>

            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($visibleTags as $i => $tag)
                    <x-tag-chip :label="$tag" :index="$i" />
                @endforeach
            </div>

            @if (! empty($job->audience_segments))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($job->audience_segments as $segment)
                        <a href="{{ url('/jobs') }}?audience={{ $segment }}" class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-800 transition hover:bg-indigo-200">
                            <x-icon :name="\App\Support\Audience::ICONS[$segment] ?? 'globe'" class="h-3.5 w-3.5" /> {{ \App\Support\Audience::LABELS[$segment] ?? $segment }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Smart Career Tools Bar: AI CV Tailoring & Application Tracker --}}
            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-horizon-200 bg-gradient-to-r from-horizon-50/70 via-white to-amber-50/50 p-4 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-horizon-100 text-horizon-700">
                        <x-icon name="sparkle" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-horizon-800">AI Application Copilot</p>
                        <p class="text-xs text-foreground/60">Generate role-tailored resume bullets & cover letter to pass ATS</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <livewire:ai-tailor-modal :job-id="$job->id" />
                    <livewire:track-application-button :job-id="$job->id" />
                </div>
            </div>

            <div class="mt-6">
                <x-job-at-a-glance :job="$job" :can-apply="$canApply" />
            </div>

            {{-- Full Unredacted Role Description --}}
            <div class="mt-6 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="mb-3 font-semibold text-foreground">About this role at {{ $job->company }}</h2>
                @if (\App\Support\Language::looksNonEnglish($plainDescription))
                    <livewire:translate-toggle :text="$plainDescription" :blocks="$descriptionBlocks" />
                @elseif ($descriptionBlocks)
                    <x-description-blocks :blocks="$descriptionBlocks" />
                @else
                    <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{!! nl2br(e($plainDescription)) !!}</div>
                @endif
            </div>

            {{-- Application Section --}}
            <div class="mt-8">
                @if ($canApply)
                    <div class="rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50/60 to-white p-6 sm:p-8 text-center shadow-md">
                        @if ($isEarlyAccess)
                            <p class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                <x-icon name="check" class="h-4 w-4 text-emerald-600" /> Pro Member Early Access Active
                            </p>
                        @endif
                        <h3 class="text-xl font-bold text-foreground">Ready to submit your application?</h3>
                        <p class="mt-1 text-sm text-foreground/60 max-w-md mx-auto">
                            Apply directly through {{ $job->company }}'s official recruitment portal.
                        </p>
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a
                                href="{{ $job->source_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-pop inline-flex items-center justify-center gap-2 rounded-full gradient-sunrise px-8 py-3.5 text-center font-bold text-white shadow-lg transition hover:opacity-95 hover:scale-[1.02]"
                            >
                                Apply Directly at {{ $job->company }} &#8599;
                            </a>
                            <livewire:track-application-button :job-id="$job->id" />
                        </div>
                        <p class="mt-4 text-xs text-foreground/40">
                            Sourced via <a href="{{ $job->source_url }}" target="_blank" rel="noopener noreferrer" class="underline hover:text-sunrise-600">{{ $job->source_name }}</a> &middot; Make sure to tailor your CV before submitting!
                        </p>
                    </div>
                @else
                    {{-- 48-Hour Early Access High-Converting Box --}}
                    <div class="rounded-3xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 via-white to-orange-50 p-6 sm:p-8 text-center shadow-lg">
                        <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-1 text-xs font-bold uppercase tracking-wider text-amber-900 mb-3">
                            <x-icon name="sparkle" class="h-4 w-4 text-amber-600" /> Pro Exclusive: First 48-Hour Recruiter Window
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-foreground">
                            Beat 500+ Applicants to {{ $job->company }}
                        </h3>
                        <p class="mt-2 text-sm text-foreground/70 max-w-lg mx-auto">
                            This remote role was published {{ \App\Support\Format::timeAgo($job->posted_at) }}. Pro members get exclusive 48-hour head-start access to apply directly to recruiters before general public release in <span class="font-bold text-amber-900">{{ $job->earlyAccessHoursRemaining() }} hours</span>.
                        </p>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-lg mx-auto text-left text-xs font-medium text-foreground/80">
                            <div class="rounded-xl bg-white/90 p-3 border border-amber-100 shadow-xs">
                                <p class="font-bold text-amber-900">⚡ Top of Recruiter Inbox</p>
                                <p class="text-foreground/60 mt-0.5">First 20 applicants receive 80% of recruiter interviews.</p>
                            </div>
                            <div class="rounded-xl bg-white/90 p-3 border border-amber-100 shadow-xs">
                                <p class="font-bold text-amber-900">🎯 AI ATS Match Tailoring</p>
                                <p class="text-foreground/60 mt-0.5">Instant role-targeted bullets & cover letters.</p>
                            </div>
                            <div class="rounded-xl bg-white/90 p-3 border border-amber-100 shadow-xs">
                                <p class="font-bold text-amber-900">💼 Remote Contractor Toolkit</p>
                                <p class="text-foreground/60 mt-0.5">USD invoices, W-8BEN guide & CRM pipeline.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <a
                                href="{{ url('/pricing') }}"
                                class="btn-pop w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-horizon-800 px-8 py-3.5 text-sm font-bold text-white shadow-md hover:bg-horizon-900 transition"
                            >
                                Unlock Early Access (KES 1,499/mo)
                            </a>
                            <livewire:ai-tailor-modal :job-id="$job->id" />
                        </div>
                        <p class="mt-3 text-xs text-foreground/50">
                            Instant M-Pesa STK push &middot; Cancel anytime &middot; Public apply opens in {{ $job->earlyAccessHoursRemaining() }} hours
                        </p>
                    </div>
                @endif
            </div>
        </x-reveal>
    </div>
</x-layouts.app>
