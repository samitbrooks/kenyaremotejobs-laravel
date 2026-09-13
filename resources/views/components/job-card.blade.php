@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($plainDescription, 140);
    $visibleTags = $job->tags ?? [];
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $audienceSegments = $job->audience_segments ?? [];
    $isNew = $job->posted_at->gt(now()->subDay());
    $isEarlyAccess = $job->isEarlyAccess();
@endphp

<a
    href="{{ url('/jobs/'.$job->id) }}"
    class="group flex h-full flex-col gap-3 rounded-2xl border border-black/5 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:shadow-sunrise-500/10"
>
    <div class="flex items-start gap-3">
        <x-company-logo :company="$job->company" />
        <div class="min-w-0 flex-1">
            <h3 class="truncate font-semibold text-foreground group-hover:text-sunrise-600">{{ $job->title }}</h3>
            <p class="truncate text-sm text-foreground/60">{{ $job->company }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-1.5">
        @if ($isNew)
            <span class="inline-flex items-center gap-1 rounded-full bg-sunrise-500 px-2 py-0.5 text-[11px] font-semibold text-white">
                New
            </span>
        @endif
        @if ($isEarlyAccess)
            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-200 px-2 py-0.5 text-[11px] font-semibold text-amber-900">
                <x-icon name="sparkle" class="h-3 w-3 text-amber-600" /> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
            </span>
        @else
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                <x-icon name="check" class="h-3 w-3 text-emerald-600" /> Open to Apply
            </span>
        @endif
        @if (is_int($matchPercent))
            <span class="inline-flex items-center gap-1 rounded-full bg-horizon-600 px-2 py-0.5 text-[11px] font-semibold text-white">
                <x-icon name="sparkle" class="h-3 w-3" /> {{ $matchPercent }}% match
            </span>
        @endif
        @if ($hourly)
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800" title="Estimated from the stated annual salary ÷ 2,080 hours/year — not a guaranteed rate">
                <x-icon name="coin" class="h-3 w-3" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
            </span>
        @endif
        @if ($job->kenya_friendly)
            <x-kenya-badge compact />
        @endif
        @if ($job->origin === 'employer')
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">
                <x-icon name="announce" class="h-3 w-3" /> Direct employer listing
            </span>
        @endif
    </div>

    <p class="line-clamp-2 text-sm text-foreground/70">{{ $preview }}</p>

    <div class="flex flex-wrap gap-1.5">
        @foreach (array_slice($visibleTags, 0, 4) as $i => $tag)
            <x-tag-chip :label="$tag" :index="$i" />
        @endforeach
    </div>

    <div class="mt-auto flex items-center justify-between gap-2 border-t border-black/5 pt-3 text-xs text-foreground/50">
        <span class="flex items-center gap-2">
            <span>{{ $job->remote_type }}</span>
            @if (count($audienceSegments) > 0)
                <span class="flex items-center gap-1" title="{{ collect($audienceSegments)->map(fn ($s) => \App\Support\Audience::LABELS[$s] ?? $s)->join(', ') }}">
                    @foreach (array_slice($audienceSegments, 0, 2) as $segment)
                        <x-icon :name="\App\Support\Audience::ICONS[$segment] ?? 'globe'" class="h-3.5 w-3.5" />
                    @endforeach
                </span>
            @endif
        </span>
        <span>{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
    </div>
</a>
