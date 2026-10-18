@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($plainDescription, 110);
    $visibleTags = $job->tags ?? [];
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
    $isNew = $job->posted_at->gt(now()->subDay());
    $isEarlyAccess = $job->isEarlyAccess();
    $displaySalary = $job->salary ?: ($hourly ? '$' . $hourly['min'] . ' - $' . $hourly['max'] . '/hr' : null);
@endphp

<a
    href="{{ url('/jobs/'.$job->id) }}"
    class="group jobs-micro-card relative flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6 p-4 sm:p-5 overflow-hidden"
>
    {{-- Left Accent Micro-Glow on Hover --}}
    <span class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full bg-[#3444da] opacity-0 group-hover:opacity-100 transition-opacity duration-200" aria-hidden="true"></span>

    {{-- Main Content Column --}}
    <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 min-w-0 flex-1">
        {{-- Company Vector Graphic / Logo --}}
        <x-company-logo :company="$isUnlocked ? $job->company : ''" :size="48" class="shrink-0" />

        <div class="min-w-0 flex-1 space-y-2">
            {{-- Top Meta Row: Date, Platform/Origin Badges, Timezone Fit --}}
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs">
                <span class="text-xs font-medium text-slate-400 whitespace-nowrap">
                    {{ \App\Support\Format::timeAgo($job->posted_at) }}
                </span>

                @if ($isNew)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700 shadow-2xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Fresh
                    </span>
                @endif

                {{-- Source / ATS Badges --}}
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200/80 px-2.5 py-0.5 text-[10px] font-bold text-blue-800 shadow-2xs">
                        <x-icon name="announce" class="h-3 w-3 text-blue-600" /> Direct Employer
                    </span>
                @elseif ($job->source_name === 'micro1')
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-2.5 py-0.5 text-[10px] font-bold text-[#3444da] shadow-2xs">
                        <span class="font-black">⚡</span> micro1 verified
                    </span>
                @elseif ($job->source_name === 'Turing')
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200/80 px-2.5 py-0.5 text-[10px] font-bold text-indigo-800 shadow-2xs">
                        <span class="text-indigo-600 font-bold">✦</span> Turing
                    </span>
                @elseif (! $isUnlocked)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 shadow-2xs">
                        <span class="text-amber-600 font-bold">🔒</span> Pro Access
                    </span>
                @elseif ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 shadow-2xs">
                        <span class="text-amber-500">⚡</span> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                    </span>
                @endif

                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500">
                    🌍 {{ $job->remote_type }}
                </span>

                @if ($job->kenya_friendly)
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-bold text-[11px] bg-emerald-50/90 border border-emerald-200/60 rounded-md px-2 py-0.5 shadow-2xs" title="East Africa Time (UTC+3) overlap & zero foreign visa requirements">
                        <x-icon.kenya-flag class="h-3 w-3 inline" /> Kenya-Friendly
                    </span>
                @endif

                @if (is_int($matchPercent))
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-2.5 py-0.5 text-[10px] font-bold text-[#3444da]">
                        🎯 {{ $matchPercent }}% match
                    </span>
                @endif
            </div>

            {{-- Job Title + Company Name --}}
            <div class="space-y-0.5">
                <h3 class="font-bold text-slate-900 group-hover:text-[#3444da] transition-colors text-base sm:text-lg tracking-tight leading-snug">
                    {{ $job->title }}
                </h3>

                <div class="flex items-center gap-2 text-xs text-slate-500">
                    @if ($isUnlocked)
                        <span class="font-semibold text-slate-700 flex items-center gap-1">
                            {{ $job->company }}
                            <svg class="h-3.5 w-3.5 text-[#3444da] inline shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 font-semibold text-slate-700" title="Unlock with Pro or 24-Hour Free Pass to reveal company name">
                            <span class="text-amber-600">🔒</span>
                            <span>Verified Employer</span>
                            <span class="rounded bg-amber-100/90 border border-amber-300/60 px-1.5 py-0.2 text-[9px] font-extrabold uppercase tracking-wider text-amber-900">Pro Only</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- micro1 Signature: "Required skills" capsules list --}}
            @if (count($visibleTags) > 0)
                <div class="pt-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Required skills</div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach (array_slice($visibleTags, 0, 4) as $tag)
                            <span class="jobs-skill-pill">
                                {{ $tag }}
                            </span>
                        @endforeach
                        @if (count($visibleTags) > 4)
                            <span class="jobs-skill-pill font-bold text-slate-500">
                                {{ count($visibleTags) - 4 }}+
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Prominent Pay + Micro1 CTA Button --}}
    <div class="flex items-center md:flex-col md:items-end justify-between md:justify-center shrink-0 gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
        {{-- High-Impact Compensation Breakdown --}}
        @if ($displaySalary && str_contains($displaySalary, '/hr'))
            <div class="flex flex-col md:items-end">
                <div class="text-xs sm:text-sm font-extrabold text-slate-900">
                    <span class="text-xs font-medium text-slate-400 mr-1">Pay:</span>
                    <span class="text-[#3444da] font-black">{{ $displaySalary }}</span>
                </div>
                @if ($kesMonthly)
                    <span class="text-[11px] font-semibold text-slate-500 mt-0.5">
                        ~{{ $kesMonthly }}
                    </span>
                @endif
            </div>
        @elseif ($kesMonthly)
            <div class="flex flex-col md:items-end">
                <div class="text-xs sm:text-sm font-extrabold text-slate-900">
                    <span class="text-xs font-medium text-slate-400 mr-1">Comp:</span>
                    <span class="text-emerald-700 font-black">{{ $kesMonthly }}</span>
                </div>
                @if ($displaySalary)
                    <span class="text-[11px] font-semibold text-slate-500 mt-0.5">
                        {{ $displaySalary }}
                    </span>
                @endif
            </div>
        @elseif ($hourly)
            <div class="flex flex-col md:items-end">
                <div class="text-xs sm:text-sm font-extrabold text-slate-900">
                    <span class="text-xs font-medium text-slate-400 mr-1">Pay:</span>
                    <span class="text-[#3444da] font-black">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                </div>
            </div>
        @else
            <div class="inline-flex items-center gap-1 rounded-xl bg-slate-50 border border-slate-200/80 px-2 py-0.5 text-[11px] font-bold text-slate-700">
                <span>Competitive Pay</span>
            </div>
        @endif

        {{-- micro1 Signature CTA Action Button --}}
        <div class="flex items-center gap-2">
            <span class="jobs-micro-cta">
                <span>{{ $isUnlocked ? 'Apply' : 'View & Unlock' }}</span>
                <svg class="h-3.5 w-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </span>
        </div>
    </div>
</a>
