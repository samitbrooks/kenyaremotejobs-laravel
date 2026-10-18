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
    class="group jobs-micro-card relative flex flex-col justify-between p-5 rounded-[20px] bg-white border border-[#e6e8f2] shadow-[0_2px_12px_rgba(15,23,42,0.04)] hover:border-[#3444da] hover:shadow-[0_16px_36px_rgba(52,68,218,0.12)] hover:-translate-y-1 transition-all h-full min-h-[18.5rem] overflow-hidden"
>
    {{-- Left Accent Micro-Glow on Hover --}}
    <span class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full bg-[#3444da] opacity-0 group-hover:opacity-100 transition-opacity duration-200" aria-hidden="true"></span>

    {{-- Top & Middle Content Section --}}
    <div class="space-y-3">
        {{-- Top Meta Row: Date, Platform/Origin Badges --}}
        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
            <span class="text-xs font-medium text-slate-400 whitespace-nowrap">
                {{ \App\Support\Format::timeAgo($job->posted_at) }}
            </span>

            <div class="flex flex-wrap items-center gap-1.5">
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200/80 px-2 py-0.5 text-[10px] font-bold text-blue-800 shadow-2xs">
                        <x-icon name="announce" class="h-3 w-3 text-blue-600" /> Direct Employer
                    </span>
                @elseif ($job->source_name === 'micro1')
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-2 py-0.5 text-[10px] font-bold text-[#3444da] shadow-2xs">
                        <x-icon name="lightning" class="h-3 w-3 text-[#3444da]" /> micro1 verified
                    </span>
                @elseif ($job->source_name === 'Turing')
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200/80 px-2 py-0.5 text-[10px] font-bold text-indigo-800 shadow-2xs">
                        <x-icon name="sparkle" class="h-3 w-3 text-indigo-600" /> Turing
                    </span>
                @elseif (! $isUnlocked)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2 py-0.5 text-[10px] font-bold text-amber-800 shadow-2xs">
                        <x-icon name="lock" class="h-3 w-3 text-amber-600" /> Pro Access
                    </span>
                @elseif ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2 py-0.5 text-[10px] font-bold text-amber-800 shadow-2xs">
                        <x-icon name="lightning" class="h-3 w-3 text-amber-500" /> Early Access
                    </span>
                @endif

                @if ($job->kenya_friendly)
                    <span class="inline-flex items-center gap-1 text-[#3444da] font-bold text-[10px] bg-[#eef0fe] border border-[#c7ccfb] rounded-full px-2 py-0.5 shadow-2xs" title="East Africa Time (UTC+3) overlap & zero foreign visa requirements">
                        <x-icon.kenya-flag class="h-2.5 w-2.5 inline" /> Kenya-Friendly
                    </span>
                @endif
            </div>
        </div>

        {{-- Role Title & Company Logo / Info --}}
        <div class="space-y-1.5">
            <h3 class="font-bold text-slate-900 group-hover:text-[#3444da] transition-colors text-base sm:text-lg tracking-tight leading-snug line-clamp-2">
                {{ $job->title }}
            </h3>

            <div class="flex items-center gap-2 text-xs text-slate-500">
                <x-company-logo :company="$isUnlocked ? $job->company : ''" :size="20" class="shrink-0 rounded-md" />
                @if ($isUnlocked)
                    <span class="font-semibold text-slate-700 flex items-center gap-1 truncate">
                        {{ $job->company }}
                        <svg class="h-3.5 w-3.5 text-[#3444da] inline shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700" title="Unlock with Pro or 24-Hour Free Pass to reveal company name">
                        <x-icon name="lock" class="h-3 w-3 text-amber-600" />
                        <span>Verified Employer</span>
                        <span class="rounded bg-amber-100/90 border border-amber-300/60 px-1.5 py-0.2 text-[9px] font-extrabold uppercase tracking-wider text-amber-900">Pro Only</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- micro1 Signature: "Required skills" capsules list --}}
        @if (count($visibleTags) > 0)
            <div class="pt-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Required skills</div>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach (array_slice($visibleTags, 0, 3) as $tag)
                        <span class="jobs-skill-pill">
                            {{ $tag }}
                        </span>
                    @endforeach
                    @if (count($visibleTags) > 3)
                        <span class="jobs-skill-pill font-bold text-slate-500">
                            +{{ count($visibleTags) - 3 }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom Section: Compensation + micro1 CTA Button --}}
    <div class="pt-3.5 mt-4 border-t border-slate-100 flex items-center justify-between gap-3">
        <div class="min-w-0">
            @if ($displaySalary && str_contains($displaySalary, '/hr'))
                <div class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">
                    <span class="text-xs font-medium text-slate-400 mr-1">Pay:</span>
                    <span class="text-[#3444da] font-black">{{ $displaySalary }}</span>
                </div>
                @if ($kesMonthly)
                    <span class="text-[11px] font-semibold text-slate-400 block truncate">
                        ~{{ $kesMonthly }}
                    </span>
                @endif
            @elseif ($kesMonthly)
                <div class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">
                    <span class="text-xs font-medium text-slate-400 mr-1">Comp:</span>
                    <span class="text-slate-900 font-black">{{ $kesMonthly }}</span>
                </div>
                @if ($displaySalary)
                    <span class="text-[11px] font-semibold text-slate-400 block truncate">
                        {{ $displaySalary }}
                    </span>
                @endif
            @elseif ($hourly)
                <div class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">
                    <span class="text-xs font-medium text-slate-400 mr-1">Pay:</span>
                    <span class="text-[#3444da] font-black">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                </div>
            @else
                <span class="text-xs font-bold text-slate-700">Competitive Pay</span>
            @endif
        </div>

        <div class="shrink-0">
            <span class="jobs-micro-cta">
                <span>{{ $isUnlocked ? 'Apply' : 'View & Unlock' }}</span>
                <svg class="h-3.5 w-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </span>
        </div>
    </div>
</a>
