@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($plainDescription, 120);
    $visibleTags = $job->tags ?? [];
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
    $audienceSegments = $job->audience_segments ?? [];
    $isNew = $job->posted_at->gt(now()->subDay());
    $isEarlyAccess = $job->isEarlyAccess();
@endphp

<a
    href="{{ url('/jobs/'.$job->id) }}"
    class="group relative flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-5 rounded-2xl border border-slate-200/70 bg-white px-5 py-4 shadow-[0_1px_4px_rgba(15,23,42,0.03)] hover:border-teal-500/60 hover:shadow-[0_8px_24px_rgba(13,148,136,0.08)] hover:bg-slate-50/40 transition-all duration-200"
>
    {{-- Left: Company Logo + Job Details --}}
    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
        <x-company-logo :company="$job->company" class="shrink-0" />

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="font-bold text-slate-900 group-hover:text-teal-600 transition-colors text-base sm:text-[17px] leading-snug truncate">
                    {{ $job->title }}
                </h3>

                @if ($isNew)
                    <span class="inline-flex items-center rounded-full bg-teal-50 border border-teal-200/60 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-teal-700">
                        New
                    </span>
                @endif
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/70 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                        <x-icon name="announce" class="h-3 w-3 text-emerald-600" /> 🇰🇪 Direct Employer
                    </span>
                @elseif ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/70 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                        <x-icon name="sparkle" class="h-3 w-3 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                    </span>
                @endif
                @if (is_int($matchPercent))
                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200/70 px-2.5 py-0.5 text-xs font-bold text-teal-800">
                        <x-icon name="sparkle" class="h-3 w-3 text-teal-600" /> {{ $matchPercent }}% match
                    </span>
                @endif
            </div>

            <div class="mt-1 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500">
                <span class="font-semibold text-slate-700">{{ $job->company }}</span>
                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center rounded-md bg-slate-100/90 px-2 py-0.5 text-[11px] font-medium text-slate-600">{{ $job->remote_type }}</span>

                @if ($job->kenya_friendly)
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-teal-700 font-semibold">
                        <x-icon.kenya-flag class="h-3 w-3" /> Kenya-Friendly
                    </span>
                @endif

                @if (count($visibleTags) > 0)
                    <span class="hidden md:inline text-slate-300">•</span>
                    <span class="hidden md:inline-flex items-center gap-1.5">
                        @foreach (array_slice($visibleTags, 0, 3) as $tag)
                            <span class="rounded-full bg-slate-50 border border-slate-200/60 px-2 py-0.5 text-[11px] text-slate-600">{{ $tag }}</span>
                        @endforeach
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Compensation + Recency + Action --}}
    <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center shrink-0 gap-1.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 text-right">
        @if ($kesMonthly)
            <span class="text-xs sm:text-sm font-bold text-emerald-700" title="Estimated monthly take-home in Kenyan Shillings at ~130 KES/USD">
                {{ $kesMonthly }}
            </span>
        @elseif ($hourly)
            <span class="text-xs sm:text-sm font-bold text-emerald-700" title="Estimated from stated annual salary ÷ 2,080 hours/year">
                ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
            </span>
        @endif

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 font-medium">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover:bg-teal-50 group-hover:text-teal-700 group-hover:translate-x-0.5 transition-all text-xs font-bold">
                &rarr;
            </span>
        </div>
    </div>
</a>

