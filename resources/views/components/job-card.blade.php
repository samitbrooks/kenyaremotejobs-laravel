@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($plainDescription, 110);
    $visibleTags = $job->tags ?? [];
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd, $job->salary);
    $hourlyFormatted = \App\Support\SalaryEstimate::formatHourly($hourly);
    $isNew = $job->posted_at->gt(now()->subDay());
    $isEarlyAccess = $job->isEarlyAccess();
    $displaySalary = $hourlyFormatted ?: ($job->salary ?: null);
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
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-2 py-0.5 text-[10px] font-bold text-[#3444da] shadow-2xs">
                        <x-icon name="sparkle" class="h-3 w-3 text-[#3444da]" /> Global Talent Network
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
                <x-company-logo :company="''" :size="20" class="shrink-0 rounded-md" />
                <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                    <x-icon name="shield" class="h-3.5 w-3.5 text-[#3444da]" />
                    <span>Verified Direct Employer</span>
                    <span class="rounded bg-[#eef0fe] border border-[#c7ccfb] px-1.5 py-0.2 text-[9px] font-extrabold uppercase tracking-wider text-[#3444da]">Vetted</span>
                </span>
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
            @if ($displaySalary)
                <div class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">
                    <span class="text-xs font-medium text-slate-400 mr-1">Pay:</span>
                    <span class="text-[#3444da] font-black">{{ $displaySalary }}</span>
                </div>
            @else
                <span class="text-xs font-bold text-slate-700">Competitive Pay</span>
            @endif
        </div>

        <div class="shrink-0">
            <span class="jobs-micro-cta">
                <span>{{ $isUnlocked ? 'Apply Directly' : 'View Role' }}</span>
                <svg class="h-3.5 w-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </span>
        </div>
    </div>
</a>
