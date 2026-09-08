@php
    $free = $job->is_free;
    $redactor = app(\App\Services\Redactor::class);
    $plainDescription = \App\Support\Format::stripHtml($job->description ?? '');
    $fullDescription = $unlocked ? $plainDescription : $redactor->redactEmployerIdentity($plainDescription, $job->company);
    $visibleTags = $unlocked ? ($job->tags ?? []) : $redactor->redactTags($job->tags ?? [], $job->company);
    $rawDescriptionBlocks = \App\Support\DescriptionBlocks::parse($job->description ?? '');
    $descriptionBlocks = $rawDescriptionBlocks
        ? ($unlocked ? $rawDescriptionBlocks : \App\Support\DescriptionBlocks::redact($rawDescriptionBlocks, $job->company))
        : null;
    $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
    $tierLabels = config('jobs.tier_labels');
    $creditPackages = config('jobs.credit_packages');
    $pkg = $creditPackages[$job->tier] ?? ['price_kes' => 0];
    $employerVisible = $job->origin === 'employer' || $free;
    $title = $job->title.($employerVisible ? " at {$job->company}" : '')." — Remote Job".($job->kenya_friendly ? ' (Kenya-Friendly)' : '');
    $preview = \App\Support\Format::truncate($fullDescription, 155);
    $daysUntilFree = max(0, (int) now()->diffInDays($job->posted_at->copy()->addDays(config('jobs.premium_window_days')), false));
@endphp

<x-layouts.app :title="$title" :description="$preview">
    <script type="application/ld+json">{!! \App\Support\Seo::jobPostingJsonLd($job) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::breadcrumbJsonLd($job) !!}</script>

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a href="{{ url('/jobs') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to all jobs</a>

        <x-reveal>
            <div class="mt-4 flex items-start gap-4">
                @if ($unlocked)
                    <x-company-logo :company="$job->company" :size="64" />
                @else
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-black/5" aria-hidden="true">
                        <x-icon name="lock" class="h-6 w-6 text-foreground/40" />
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold sm:text-3xl">{{ $job->title }}</h1>
                    <p class="mt-1 text-foreground/60">{{ $unlocked ? $job->company : 'Employer hidden until unlocked' }}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($hourly)
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800" title="Estimated from the stated annual salary ÷ 2,080 hours/year — not a guaranteed rate">
                        <x-icon name="coin" class="h-3.5 w-3.5" /> ~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr
                    </span>
                @endif
                @if ($job->kenya_friendly)
                    <x-kenya-badge />
                @endif
                @if ($job->origin === 'employer')
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                        <x-icon name="announce" class="h-3.5 w-3.5" /> Direct listing from this employer
                    </span>
                @elseif ($free)
                    <span class="inline-flex items-center gap-1 rounded-full bg-horizon-100 px-3 py-1 text-xs font-semibold text-horizon-800">
                        <x-icon name="unlock" class="h-3.5 w-3.5" /> Free to view
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-sunrise-100 px-3 py-1 text-xs font-semibold text-sunrise-800">
                        <x-icon name="lock" class="h-3.5 w-3.5" /> {{ $tierLabels[$job->tier] ?? $job->tier }} &middot; from KES {{ number_format($pkg['price_kes']) }}
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

            {{-- Attribution is withheld along with the rest of the employer's identity
                 until unlocked — several sources' terms require crediting the
                 original listing, but only once a visitor actually has access to it. --}}
            @if ($unlocked)
                <p class="mt-4 text-xs text-foreground/40">
                    Sourced from <a href="{{ $job->source_url }}" target="_blank" rel="noopener noreferrer" class="underline hover:text-sunrise-600">{{ $job->source_name }}</a>
                </p>
            @endif

            <div class="mt-8">
                <x-job-at-a-glance :job="$job" :unlocked="$unlocked" :free="$free" :price-kes="$pkg['price_kes']" />
            </div>

            {{-- The full description is always shown — what's gated is who's
                 offering the role and the link to apply, not the role itself. --}}
            <div class="mt-4 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="mb-3 font-semibold">About this role</h2>
                @if ($descriptionBlocks)
                    <x-description-blocks :blocks="$descriptionBlocks" />
                @else
                    <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{{ $fullDescription }}</div>
                @endif
            </div>

            <div class="mt-6">
                @if ($unlocked)
                    <a href="{{ $job->source_url }}" target="_blank" rel="noopener noreferrer" class="btn-pop inline-block rounded-full gradient-sunrise px-8 py-3 text-center font-semibold text-white shadow-lg transition hover:opacity-90">
                        Apply Now &#8599;
                    </a>
                @else
                    <div class="rounded-2xl border border-sunrise-200 bg-sunrise-50 p-6 text-center">
                        <p class="font-semibold text-sunrise-800">Unlock the employer&rsquo;s name and Apply link</p>
                        <p class="mt-1 text-sm text-foreground/60">
                            You&rsquo;ve already read the full description above &mdash; unlocking just reveals who&rsquo;s hiring and gets you the Apply link.
                        </p>
                        <p class="mt-3 text-xs text-foreground/50">
                            @if ($daysUntilFree > 0)
                                Opens free for everyone in {{ $daysUntilFree }} {{ Str::plural('day', $daysUntilFree) }}, or unlock now.
                            @else
                                This listing is about to open free for everyone.
                            @endif
                        </p>
                        <div class="mx-auto mt-4 max-w-xs">
                            <livewire:unlock-button
                                :job-id="$job->id"
                                :tier="$job->tier"
                                :remaining-credits="$remainingCredits"
                                :package-price-kes="$pkg['price_kes']"
                                :is-authed="(bool) auth()->user()"
                            />
                        </div>
                    </div>
                @endif
            </div>
        </x-reveal>
    </div>
</x-layouts.app>
