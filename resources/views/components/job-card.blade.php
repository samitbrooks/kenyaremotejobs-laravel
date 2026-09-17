@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($plainDescription, 140);
    $visibleTags = $job->tags ?? [];
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
    $audienceSegments = $job->audience_segments ?? [];
    $isNew = $job->posted_at->gt(now()->subDay());
    $isEarlyAccess = $job->isEarlyAccess();
@endphp

<a
    href="{{ url('/jobs/'.$job->id) }}"
    class="group relative flex h-full flex-col justify-between gap-4 rounded-3xl border border-slate-200/60 bg-white p-6 shadow-[0_2px_12px_rgba(15,23,42,0.03)] hover:shadow-[0_16px_36px_rgba(13,148,136,0.1)] hover:border-teal-500/60 hover:-translate-y-1 transition-all duration-300"
>
    <div class="space-y-3.5">
        <div class="flex items-start gap-3.5">
            <x-company-logo :company="$job->company" />
            <div class="min-w-0 flex-1">
                <h3 class="truncate font-bold text-slate-900 group-hover:text-teal-600 transition-colors text-base sm:text-lg leading-snug">{{ $job->title }}</h3>
                <p class="truncate text-sm font-medium text-slate-500 mt-0.5">{{ $job->company }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-1.5 items-center">
            @if ($isNew)
                <span class="inline-flex items-center gap-1 rounded-full bg-teal-600 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-2xs">
                    New
                </span>
            @endif
            @if ($job->origin === 'employer')
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/80 px-3 py-0.5 text-xs font-bold text-emerald-900">
                    <x-icon name="announce" class="h-3 w-3 text-emerald-700" /> 🇰🇪 Direct Employer
                </span>
            @elseif ($isEarlyAccess)
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-3 py-0.5 text-xs font-semibold text-amber-900">
                    <x-icon name="sparkle" class="h-3 w-3 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                </span>
            @endif
            @if (is_int($matchPercent))
                <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200/80 px-3 py-0.5 text-xs font-bold text-teal-800">
                    <x-icon name="sparkle" class="h-3 w-3 text-teal-600" /> {{ $matchPercent }}% match
                </span>
            @endif
            @if ($kesMonthly)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50/80 border border-emerald-200/70 px-3 py-0.5 text-xs font-bold text-emerald-900" title="Estimated monthly take-home in Kenyan Shillings at ~130 KES/USD">
                    <x-icon name="coin" class="h-3 w-3 text-emerald-700" /> {{ $kesMonthly }}
                </span>
            @elseif ($hourly)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50/80 border border-emerald-200/70 px-3 py-0.5 text-xs font-semibold text-emerald-800" title="Estimated from stated annual salary ÷ 2,080 hours/year">
                    <x-icon name="coin" class="h-3 w-3 text-emerald-700" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
                </span>
            @endif
            @if ($job->kenya_friendly)
                <x-kenya-badge compact />
            @endif
        </div>

        <p class="line-clamp-2 text-sm text-slate-600 leading-relaxed font-normal">{{ $preview }}</p>

        <div class="flex flex-wrap gap-1.5 pt-0.5">
            @foreach (array_slice($visibleTags, 0, 4) as $i => $tag)
                <x-tag-chip :label="$tag" :index="$i" />
            @endforeach
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-2 border-t border-slate-100/90 pt-3.5 text-xs text-slate-500 font-medium">
        <span class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-slate-100/80 border border-slate-200/60 px-2.5 py-0.5 text-xs text-slate-600 font-medium">{{ $job->remote_type }}</span>
            @if (count($audienceSegments) > 0)
                <span class="flex items-center gap-1" title="{{ collect($audienceSegments)->map(fn ($s) => \App\Support\Audience::LABELS[$s] ?? $s)->join(', ') }}">
                    @foreach (array_slice($audienceSegments, 0, 2) as $segment)
                        <x-icon :name="\App\Support\Audience::ICONS[$segment] ?? 'globe'" class="h-3.5 w-3.5 text-slate-400" />
                    @endforeach
                </span>
            @endif
        </span>
        <div class="flex items-center gap-2">
            <span class="text-slate-400 text-xs">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover:bg-teal-50 group-hover:text-teal-700 group-hover:translate-x-0.5 transition-all text-xs font-bold">
                &rarr;
            </span>
        </div>
    </div>
</a>
