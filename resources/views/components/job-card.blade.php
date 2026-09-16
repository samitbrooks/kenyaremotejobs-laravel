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
    class="group flex h-full flex-col gap-3 rounded-xl border border-slate-200/90 bg-white p-5 shadow-xs transition-all duration-150 hover:-translate-y-0.5 hover:border-teal-500 hover:shadow-md"
>
    <div class="flex items-start gap-3.5">
        <x-company-logo :company="$job->company" />
        <div class="min-w-0 flex-1">
            <h3 class="truncate font-bold text-slate-900 group-hover:text-teal-600 transition-colors text-base">{{ $job->title }}</h3>
            <p class="truncate text-sm font-medium text-slate-500">{{ $job->company }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-1.5 items-center">
        @if ($isNew)
            <span class="inline-flex items-center gap-1 rounded-md bg-teal-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
                New
            </span>
        @endif
        @if ($job->origin === 'employer')
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold text-emerald-900">
                <x-icon name="announce" class="h-3 w-3 text-emerald-700" /> 🇰🇪 Direct Employer
            </span>
        @elseif ($isEarlyAccess)
            <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 text-[11px] font-semibold text-amber-900">
                <x-icon name="sparkle" class="h-3 w-3 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
            </span>
        @else
            <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                <x-icon name="check" class="h-3 w-3 text-teal-600" /> Open
            </span>
        @endif
        @if (is_int($matchPercent))
            <span class="inline-flex items-center gap-1 rounded-md bg-teal-50 border border-teal-200 px-2 py-0.5 text-[11px] font-bold text-teal-800">
                <x-icon name="sparkle" class="h-3 w-3 text-teal-600" /> {{ $matchPercent }}% match
            </span>
        @endif
        @if ($kesMonthly)
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold text-emerald-900" title="Estimated monthly take-home in Kenyan Shillings at ~130 KES/USD">
                <x-icon name="coin" class="h-3 w-3 text-emerald-700" /> {{ $kesMonthly }}
            </span>
        @elseif ($hourly)
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-semibold text-emerald-800" title="Estimated from stated annual salary ÷ 2,080 hours/year">
                <x-icon name="coin" class="h-3 w-3 text-emerald-700" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
            </span>
        @endif
        @if ($job->kenya_friendly)
            <x-kenya-badge compact />
        @endif
    </div>

    <p class="line-clamp-2 text-sm text-slate-600 leading-relaxed">{{ $preview }}</p>

    <div class="flex flex-wrap gap-1.5">
        @foreach (array_slice($visibleTags, 0, 4) as $i => $tag)
            <x-tag-chip :label="$tag" :index="$i" />
        @endforeach
    </div>

    <div class="mt-auto flex items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500 font-medium">
        <span class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600 font-medium">{{ $job->remote_type }}</span>
            @if (count($audienceSegments) > 0)
                <span class="flex items-center gap-1" title="{{ collect($audienceSegments)->map(fn ($s) => \App\Support\Audience::LABELS[$s] ?? $s)->join(', ') }}">
                    @foreach (array_slice($audienceSegments, 0, 2) as $segment)
                        <x-icon :name="\App\Support\Audience::ICONS[$segment] ?? 'globe'" class="h-3.5 w-3.5 text-slate-400" />
                    @endforeach
                </span>
            @endif
        </span>
        <span class="text-slate-400">{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
    </div>
</a>
