@php
    $user = auth()->user();
    $isEmployerContext = request()->is('employers*');
    $accountLabel = $user ? ($user->subscribed ? 'My Account' : 'My Account') : 'Log in';
@endphp

<header
    x-data="{ open: false, jobsMenuOpen: false, mobileJobsOpen: false }"
    @keydown.escape.window="open = false; jobsMenuOpen = false"
    class="sticky top-0 z-40 bg-horizon-900 text-white shadow-md"
>
    <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ url('/') }}" class="shrink-0 text-xl font-bold tracking-tight">
            Kenya<span class="text-sunrise-400">Remote</span>Jobs
        </a>

        <div class="flex flex-1 items-center justify-end gap-4">
            <div class="hidden items-center rounded-full bg-white/10 p-0.5 text-xs font-semibold md:flex">
                <a href="{{ url('/') }}" class="rounded-full px-3 py-1.5 transition {{ ! $isEmployerContext ? 'bg-white text-horizon-900' : 'text-white/70 hover:text-white' }}">
                    Job Seeker
                </a>
                <a href="{{ url('/employers') }}" class="rounded-full px-3 py-1.5 transition {{ $isEmployerContext ? 'bg-white text-horizon-900' : 'text-white/70 hover:text-white' }}">
                    Employer
                </a>
            </div>

            <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                @if ($isEmployerContext)
                    <a href="{{ url('/employers') }}" class="nav-underline transition hover:text-sunrise-300">Overview</a>
                    <a href="{{ url('/employers/post') }}" class="nav-underline transition hover:text-sunrise-300">Post a Job</a>
                    <a href="{{ url('/employers/dashboard') }}" class="nav-underline transition hover:text-sunrise-300">Dashboard</a>
                    <a href="{{ url('/journal') }}" class="nav-underline transition hover:text-sunrise-300">Journal</a>
                @else
                    <div class="relative" @mouseenter="jobsMenuOpen = true" @mouseleave="jobsMenuOpen = false">
                        <button
                            type="button"
                            @click="jobsMenuOpen = !jobsMenuOpen"
                            :aria-expanded="jobsMenuOpen"
                            class="flex items-center gap-1 transition hover:text-sunrise-300"
                        >
                            Jobs
                            <span class="text-xs transition-transform" :class="jobsMenuOpen ? 'rotate-180' : ''">&#9662;</span>
                        </button>

                        <div
                            x-show="jobsMenuOpen"
                            x-transition
                            x-cloak
                            class="absolute left-1/2 top-full z-50 w-[46rem] -translate-x-1/2 pt-2"
                        >
                            <div class="flex gap-4 rounded-2xl border border-black/5 bg-white p-4 text-neutral-900 shadow-xl">
                                <div class="flex-1">
                                    <p class="mb-2 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-neutral-400">Who it&rsquo;s for</p>
                                    @foreach (\App\Support\Audience::ORDER as $segment)
                                        <a href="{{ url('/jobs') }}?audience={{ $segment }}" @click="jobsMenuOpen = false" class="flex items-start gap-2.5 rounded-xl p-2.5 transition hover:bg-horizon-50">
                                            <x-icon :name="\App\Support\Audience::ICONS[$segment]" class="mt-0.5 h-5 w-5 text-horizon-600" />
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold text-neutral-900">{{ \App\Support\Audience::LABELS[$segment] }}</span>
                                                <span class="block text-xs text-neutral-500">{{ \App\Support\Audience::DESCRIPTIONS[$segment] }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>

                                <div class="w-44 shrink-0">
                                    <p class="mb-2 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-neutral-400">Remote type</p>
                                    @foreach (['remote' => 'Remote', 'full-time' => 'Full-time', 'part-time' => 'Part-time', 'contract' => 'Contract'] as $value => $label)
                                        <a href="{{ url('/jobs') }}?remoteType={{ $value }}" @click="jobsMenuOpen = false" class="block rounded-xl px-2.5 py-2 text-sm transition hover:bg-horizon-50">{{ $label }}</a>
                                    @endforeach

                                    <p class="mb-2 mt-4 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-neutral-400">Resources</p>
                                    @foreach ([
                                        ['href' => '/jobs', 'icon' => 'globe', 'label' => 'All Opportunities'],
                                        ['href' => '/match', 'icon' => 'sparkle', 'label' => 'Get Your Match Score'],
                                        ['href' => '/resume-builder', 'icon' => 'pen', 'label' => 'CV & Cover Letter Builder'],
                                        ['href' => '/journal', 'icon' => 'newspaper', 'label' => 'The Journal'],
                                        ['href' => '/pricing', 'icon' => 'card', 'label' => 'Pricing'],
                                        ['href' => '/surveys', 'icon' => 'coin', 'label' => 'Earn While You Search'],
                                    ] as $r)
                                        <a href="{{ url($r['href']) }}" @click="jobsMenuOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-2 text-sm transition hover:bg-horizon-50">
                                            <x-icon :name="$r['icon']" class="h-4 w-4 text-horizon-600" /> {{ $r['label'] }}
                                        </a>
                                    @endforeach
                                </div>

                                <div class="flex w-48 shrink-0 flex-col gap-3">
                                    <a href="{{ url('/employers') }}" @click="jobsMenuOpen = false" class="flex-1 rounded-2xl bg-horizon-800 p-4 text-white transition hover:bg-horizon-900">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-horizon-200">For Employers</p>
                                        <p class="mt-1 text-sm font-bold">Hire Kenya &amp; EA talent</p>
                                        <p class="mt-2 text-xs font-semibold underline">Post a job &rarr;</p>
                                    </a>
                                    <a href="{{ url('/match') }}" @click="jobsMenuOpen = false" class="flex-1 rounded-2xl gradient-sunrise p-4 text-white transition hover:opacity-90">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-white/80">For Candidates</p>
                                        <p class="mt-1 text-sm font-bold">Stand out, get matched</p>
                                        <p class="mt-2 text-xs font-semibold underline">Get my score &rarr;</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/about') }}" class="nav-underline transition hover:text-sunrise-300">About</a>
                @endif

                @if ($user?->isAdmin())
                    <a href="{{ url('/admin') }}" class="text-xs font-semibold text-sunrise-300 hover:underline">Admin</a>
                @endif
                <a href="{{ url('/account') }}" class="btn-pop rounded-full bg-sunrise-500 px-4 py-1.5 font-semibold text-white transition hover:bg-sunrise-600">
                    @if ($user?->subscribed)
                        <span class="inline-flex items-center gap-1">My Account <x-icon name="star" class="h-3.5 w-3.5" /></span>
                    @else
                        {{ $accountLabel }}
                    @endif
                </a>
            </nav>

            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open"
                aria-label="Toggle menu"
                class="flex h-9 w-9 items-center justify-center rounded-md md:hidden"
            >
                <span class="sr-only">Toggle menu</span>
                <div class="flex h-4 w-6 flex-col justify-between">
                    <span class="h-0.5 w-6 bg-white transition-transform duration-200" :class="open ? 'translate-y-[7px] rotate-45' : ''"></span>
                    <span class="h-0.5 w-6 bg-white transition-opacity duration-200" :class="open ? 'opacity-0' : 'opacity-100'"></span>
                    <span class="h-0.5 w-6 bg-white transition-transform duration-200" :class="open ? '-translate-y-[7px] -rotate-45' : ''"></span>
                </div>
            </button>
        </div>
    </div>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute left-0 right-0 top-full flex flex-col gap-1 bg-horizon-900 px-4 pb-4 text-sm font-medium shadow-md md:hidden"
    >
        <div class="mb-1 flex items-center rounded-full bg-white/10 p-0.5 text-xs font-semibold">
            <a href="{{ url('/') }}" @click="open = false" class="flex-1 rounded-full px-3 py-2 text-center transition {{ ! $isEmployerContext ? 'bg-white text-horizon-900' : 'text-white/70' }}">Job Seeker</a>
            <a href="{{ url('/employers') }}" @click="open = false" class="flex-1 rounded-full px-3 py-2 text-center transition {{ $isEmployerContext ? 'bg-white text-horizon-900' : 'text-white/70' }}">Employer</a>
        </div>

        @if ($isEmployerContext)
            <a href="{{ url('/employers') }}" @click="open = false" class="rounded px-2 py-2 hover:bg-horizon-800">Overview</a>
            <a href="{{ url('/employers/post') }}" @click="open = false" class="rounded px-2 py-2 hover:bg-horizon-800">Post a Job</a>
            <a href="{{ url('/employers/dashboard') }}" @click="open = false" class="rounded px-2 py-2 hover:bg-horizon-800">Dashboard</a>
        @else
            <button type="button" @click="mobileJobsOpen = !mobileJobsOpen" :aria-expanded="mobileJobsOpen" class="flex items-center justify-between rounded px-2 py-2 text-left hover:bg-horizon-800">
                Jobs
                <span class="text-xs transition-transform" :class="mobileJobsOpen ? 'rotate-180' : ''">&#9662;</span>
            </button>
            <div x-show="mobileJobsOpen" x-cloak class="flex flex-col gap-0.5 pl-2">
                <a href="{{ url('/jobs') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="globe" class="h-4 w-4" /> All Opportunities
                </a>
                @foreach (\App\Support\Audience::ORDER as $segment)
                    <a href="{{ url('/jobs') }}?audience={{ $segment }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                        <x-icon :name="\App\Support\Audience::ICONS[$segment]" class="h-4 w-4" /> {{ \App\Support\Audience::LABELS[$segment] }}
                    </a>
                @endforeach
                <a href="{{ url('/match') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="sparkle" class="h-4 w-4" /> Get Your Match Score
                </a>
                <a href="{{ url('/resume-builder') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="pen" class="h-4 w-4" /> CV & Cover Letter Builder
                </a>
                <a href="{{ url('/journal') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="newspaper" class="h-4 w-4" /> The Journal
                </a>
                <a href="{{ url('/pricing') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="card" class="h-4 w-4" /> Pricing
                </a>
                <a href="{{ url('/surveys') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-1.5 rounded px-2 py-2 text-white/80 hover:bg-horizon-800">
                    <x-icon name="coin" class="h-4 w-4" /> Earn While You Search
                </a>
            </div>
            <a href="{{ url('/about') }}" @click="open = false" class="rounded px-2 py-2 hover:bg-horizon-800">About</a>
        @endif

        @if ($user?->isAdmin())
            <a href="{{ url('/admin') }}" @click="open = false" class="rounded px-2 py-2 font-semibold text-sunrise-300 hover:bg-horizon-800">Admin</a>
        @endif
        <a href="{{ url('/account') }}" @click="open = false" class="mt-1 rounded-full bg-sunrise-500 px-4 py-2 text-center font-semibold hover:bg-sunrise-600">
            {{ $accountLabel }}
        </a>
    </div>
</header>
