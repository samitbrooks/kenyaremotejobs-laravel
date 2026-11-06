@props(['job', 'unlocked' => null, 'matchPercent' => null])

@php
    $isUnlocked = (bool) $unlocked;
    $redactor = app(\App\Services\Redactor::class);
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $preview = \App\Support\Format::truncate($redactor->redactEmployerIdentity($plainDescription, $job->company), 150);
    $visibleTags = $redactor->redactTags($job->tags ?? [], $job->company);
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd, $job->salary);
    $hourlyFormatted = \App\Support\SalaryEstimate::formatHourly($hourly);
    $displaySalary = $hourlyFormatted ?: ($job->salary ?: null);
@endphp

<a href="{{ url('/jobs/'.$job->id) }}" class="group jobs-micro-card flex h-full flex-col p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
        <span>{{ \App\Support\Format::timeAgo($job->posted_at) }}</span>
        @if ($job->kenya_friendly)
            <span class="font-medium text-[#3444da]" title="Location and timezone wording signals; confirm eligibility with the employer.">Kenya-Friendly</span>
        @endif
    </div>

    <h3 class="mt-3 text-lg font-semibold leading-snug text-slate-900 group-hover:text-[#3444da]">
        {{ $redactor->redactEmployerIdentity($job->title, $job->company) }}
    </h3>
    <p class="mt-2 text-xs text-slate-500">Verified Direct Employer <span class="mx-1 text-slate-300">&middot;</span> Vetted</p>
    <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $preview }}</p>

    @if (count($visibleTags) > 0)
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach (array_slice($visibleTags, 0, 3) as $tag)
                <span class="jobs-skill-pill">{{ $tag }}</span>
            @endforeach
            @if (count($visibleTags) > 3)
                <span class="px-1 py-1 text-xs text-slate-500">+{{ count($visibleTags) - 3 }}</span>
            @endif
        </div>
    @endif

    <div class="mb-4 mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
        @if ($job->isDirectAts())
            <span>Employer application</span>
        @endif
        @if (! $isUnlocked)
            <span>Pro Access</span>
        @endif
        @if (is_int($matchPercent))
            <span class="font-medium text-[#3444da]">{{ $matchPercent }}% keyword match</span>
        @endif
    </div>

    <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-4 text-sm">
        <span class="font-medium text-slate-900">
            @if ($displaySalary)
                @if ($hourlyFormatted) <span class="text-xs font-normal text-slate-500">Est.</span> @endif
                {{ $redactor->redactEmployerIdentity($displaySalary, $job->company) }}
            @else
                <span class="text-xs font-normal text-slate-500">Pay not listed</span>
            @endif
        </span>
        <span class="jobs-micro-cta">{{ $isUnlocked ? 'View application' : 'View role' }} &rarr;</span>
    </div>
</a>
