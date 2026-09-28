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
@endphp

<a
    href="{{ url('/jobs/'.$job->id) }}"
    class="group relative flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6 rounded-2xl border border-slate-200/90 bg-white/95 backdrop-blur-xs p-4 sm:p-5 shadow-[0_2px_8px_-2px_rgba(15,23,42,0.04)] hover:shadow-[0_16px_36px_-6px_rgba(15,23,42,0.08),0_6px_16px_-4px_rgba(13,148,136,0.12)] hover:border-teal-500/60 hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
>
    {{-- Left Accent Strip on Hover --}}
    <span class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full bg-gradient-to-b from-teal-500 to-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200" aria-hidden="true"></span>

    {{-- Main Content Column --}}
    <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 min-w-0 flex-1">
        {{-- Harmonized Graphic Vector Illustration --}}
        <x-company-logo :company="$job->company" :size="48" class="shrink-0" />

        <div class="min-w-0 flex-1 space-y-1.5">
            {{-- Top Row: Job Title + Badges --}}
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="font-extrabold text-slate-900 group-hover:text-teal-600 transition-colors text-base sm:text-[17px] tracking-tight leading-snug truncate">
                    {{ $job->title }}
                </h3>

                @if ($isNew)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/70 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700 shadow-2xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Fresh
                    </span>
                @endif

                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200/80 px-2.5 py-0.5 text-[11px] font-bold text-blue-800 shadow-2xs">
                        <x-icon name="announce" class="h-3 w-3 text-blue-600" /> Direct Employer
                    </span>
                @elseif ($isEarlyAccess)
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200/80 px-2 py-0.5 text-[11px] font-bold text-amber-800 shadow-2xs">
                        <span class="text-amber-500">⚡</span> Early Access ({{ $job->earlyAccessHoursRemaining() }}h left)
                    </span>
                @endif

                @if (is_int($matchPercent))
                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200/80 px-2 py-0.5 text-[11px] font-bold text-teal-800">
                        🎯 {{ $matchPercent }}% match
                    </span>
                @endif
            </div>

            {{-- Metadata Row: Company, Location, Timezone Fit, Tags --}}
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5 text-xs text-slate-600">
                <span class="font-bold text-slate-800 flex items-center gap-1">
                    {{ $job->company }}
                    <svg class="h-3.5 w-3.5 text-teal-600 inline shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </span>

                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100/90 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                    🌍 {{ $job->remote_type }}
                </span>

                @if ($job->kenya_friendly)
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-bold text-[11px] bg-emerald-50/90 border border-emerald-200/60 rounded-md px-2 py-0.5 shadow-2xs" title="East Africa Time (UTC+3) overlap & zero foreign visa requirements">
                        <x-icon.kenya-flag class="h-3 w-3 inline" /> Kenya-Friendly
                    </span>
                    <span class="hidden sm:inline-flex items-center gap-1 rounded-md bg-teal-50/90 border border-teal-200/60 px-2 py-0.5 text-[10px] font-extrabold text-teal-800" title="Core working hours align with Nairobi time">
                        ⏱ UTC+3 EAT
                    </span>
                @endif

                @if (count($visibleTags) > 0)
                    <span class="hidden lg:inline text-slate-300">•</span>
                    <span class="hidden lg:inline-flex items-center gap-1.5">
                        @foreach (array_slice($visibleTags, 0, 3) as $tag)
                            <span class="rounded-md bg-slate-100/80 border border-slate-200/50 px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:bg-slate-200/60 transition">{{ $tag }}</span>
                        @endforeach
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Prominent Compensation + Call to Action --}}
    <div class="flex items-center md:flex-col md:items-end justify-between md:justify-center shrink-0 gap-2.5 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
        {{-- High-Impact Compensation Badge --}}
        @if ($kesMonthly)
            <div class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50/60 border border-emerald-200/80 px-2.5 py-1 text-emerald-900 shadow-2xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Est.</span>
                <span class="text-xs sm:text-sm font-black text-emerald-900">{{ $kesMonthly }}</span>
                <span class="hidden sm:inline-block rounded bg-emerald-200/70 px-1 py-0.2 text-[9px] font-black uppercase text-emerald-800 tracking-wider">USD/Wire</span>
            </div>
        @elseif ($hourly)
            <div class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50/60 border border-emerald-200/80 px-2.5 py-1 text-emerald-900 shadow-2xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Est.</span>
                <span class="text-xs sm:text-sm font-black text-emerald-900">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                <span class="hidden sm:inline-block rounded bg-emerald-200/70 px-1 py-0.2 text-[9px] font-black uppercase text-emerald-800 tracking-wider">USD</span>
            </div>
        @else
            <div class="inline-flex items-center gap-1 rounded-xl bg-slate-50 border border-slate-200/80 px-2 py-0.5 text-[11px] font-bold text-slate-700">
                <span>Competitive Pay</span>
            </div>
        @endif

        {{-- Recency & Apply Button --}}
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400 font-medium whitespace-nowrap">
                {{ \App\Support\Format::timeAgo($job->posted_at) }}
            </span>

            <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 group-hover:bg-teal-600 text-white px-3.5 py-1.5 text-xs font-black shadow-xs group-hover:shadow-teal-600/20 group-hover:shadow-md transition-all duration-200">
                <span>Apply</span>
                <svg class="h-3 w-3 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </span>
        </div>
    </div>
</a>
