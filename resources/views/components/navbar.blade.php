@php
    $user = auth()->user();
    $isEmployerContext = request()->is('employers*');
    $accountLabel = $user ? ($user->subscribed ? 'My Account' : 'My Account') : 'Log in';
@endphp

<header
    x-data="{ open: false, jobsMenuOpen: false, mobileJobsOpen: false }"
    @keydown.escape.window="open = false; jobsMenuOpen = false"
    class="sticky top-0 z-40 bg-white/90 border-b border-slate-200/70 text-slate-800 shadow-2xs backdrop-blur-md"
>
    <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ url('/') }}" class="shrink-0 flex items-center group transition-transform duration-150 active:scale-95" aria-label="KenyaRemoteJobs Home">
            <picture>
                <source srcset="{{ asset('images/logo.webp') }}" type="image/webp">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="KenyaRemoteJobs"
                    class="h-9 sm:h-10 w-auto object-contain"
                    width="152"
                    height="40"
                    loading="eager"
                    decoding="async"
                />
            </picture>
        </a>

        <div class="flex flex-1 items-center justify-end gap-4">
            <div class="hidden items-center rounded-full bg-slate-100/90 p-1 text-xs font-semibold md:flex border border-slate-200/70 shadow-2xs">
                <a href="{{ url('/') }}" class="rounded-full px-3.5 py-1.5 transition {{ ! $isEmployerContext ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Job Seeker
                </a>
                <a href="{{ url('/employers') }}" class="rounded-full px-3.5 py-1.5 transition {{ $isEmployerContext ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                    Employer
                </a>
            </div>

            <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                @if ($isEmployerContext)
                    <a href="{{ url('/employers') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('employers') ? 'text-teal-600 font-bold' : '' }}">Overview</a>
                    <a href="{{ url('/employers/post') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('employers/post') ? 'text-teal-600 font-bold' : '' }}">Post a Job</a>
                    <a href="{{ url('/employers/dashboard') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('employers/dashboard') ? 'text-teal-600 font-bold' : '' }}">Dashboard</a>
                    <a href="{{ url('/journal') }}" class="nav-underline text-slate-700 transition hover:text-teal-600">Journal</a>
                @else
                    <div class="relative" @mouseenter="jobsMenuOpen = true" @mouseleave="jobsMenuOpen = false">
                        <button
                            type="button"
                            @click="jobsMenuOpen = !jobsMenuOpen"
                            :aria-expanded="jobsMenuOpen"
                            class="flex items-center gap-1 text-slate-700 transition hover:text-teal-600 font-medium py-1"
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
                            <div class="flex gap-4 rounded-3xl border border-slate-200/80 bg-white p-5 text-slate-900 shadow-2xl ring-1 ring-black/5">
                                <div class="flex-1">
                                    <p class="mb-2 px-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Who it&rsquo;s for</p>
                                    @foreach (\App\Support\Audience::ORDER as $segment)
                                        <a href="{{ url('/jobs') }}?audience={{ $segment }}" @click="jobsMenuOpen = false" class="flex items-start gap-2.5 rounded-2xl p-2.5 transition hover:bg-teal-50/60 hover:text-teal-800">
                                            <x-icon :name="\App\Support\Audience::ICONS[$segment]" class="mt-0.5 h-5 w-5 text-teal-600" />
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold text-slate-900">{{ \App\Support\Audience::LABELS[$segment] }}</span>
                                                <span class="block text-xs text-slate-500">{{ \App\Support\Audience::DESCRIPTIONS[$segment] }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>

                                <div class="w-44 shrink-0">
                                    <p class="mb-2 px-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Remote type</p>
                                    @foreach (['remote' => 'Remote', 'full-time' => 'Full-time', 'part-time' => 'Part-time', 'contract' => 'Contract'] as $value => $label)
                                        <a href="{{ url('/jobs') }}?remoteType={{ $value }}" @click="jobsMenuOpen = false" class="block rounded-xl px-2.5 py-2 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-teal-700">{{ $label }}</a>
                                    @endforeach

                                    <p class="mb-2 mt-4 px-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Resources</p>
                                    @foreach ([
                                        ['href' => '/jobs', 'icon' => 'globe', 'label' => 'All Opportunities'],
                                        ['href' => '/companies', 'icon' => 'globe', 'label' => 'Hiring Companies'],
                                        ['href' => '/collections', 'icon' => 'sparkle', 'label' => 'Curated Collections'],
                                        ['href' => '/match', 'icon' => 'sparkle', 'label' => 'Match Score'],
                                        ['href' => '/resume-builder', 'icon' => 'pen', 'label' => 'CV Builder'],
                                        ['href' => '/journal', 'icon' => 'newspaper', 'label' => 'The Journal'],
                                        ['href' => '/pricing', 'icon' => 'card', 'label' => 'Pricing'],
                                        ['href' => '/faqs', 'icon' => 'bulb', 'label' => 'FAQs'],
                                        ['href' => '/surveys', 'icon' => 'coin', 'label' => 'Earn While Searching'],
                                    ] as $r)
                                        <a href="{{ url($r['href']) }}" @click="jobsMenuOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-teal-700">
                                            <x-icon :name="$r['icon']" class="h-4 w-4 text-teal-600" /> {{ $r['label'] }}
                                        </a>
                                    @endforeach
                                </div>

                                <div class="flex w-48 shrink-0 flex-col gap-3">
                                    <a href="{{ url('/employers') }}" @click="jobsMenuOpen = false" class="flex-1 rounded-2xl bg-slate-900 p-4 text-white transition hover:bg-slate-800 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-teal-400">For Employers</p>
                                        <p class="mt-1 text-sm font-bold">Hire Kenya &amp; EA talent</p>
                                        <p class="mt-2 text-xs font-semibold text-teal-300 underline">Post a job &rarr;</p>
                                    </a>
                                    <a href="{{ url('/match') }}" @click="jobsMenuOpen = false" class="flex-1 rounded-2xl bg-teal-600 p-4 text-white transition hover:bg-teal-700 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-teal-100">For Candidates</p>
                                        <p class="mt-1 text-sm font-bold">Stand out, get matched</p>
                                        <p class="mt-2 text-xs font-semibold underline">Get my score &rarr;</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/companies') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('companies*') ? 'text-teal-600 font-bold' : '' }}">Companies</a>
                    <a href="{{ url('/collections') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('collections*') ? 'text-teal-600 font-bold' : '' }}">Collections</a>
                    <a href="{{ url('/journal') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('journal*') ? 'text-teal-600 font-bold' : '' }}">Journal</a>
                    <a href="{{ url('/pricing') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('pricing') ? 'text-teal-600 font-bold' : '' }}">Pricing</a>
                    <a href="{{ url('/faqs') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('faqs*') ? 'text-teal-600 font-bold' : '' }}">FAQs</a>
                    <a href="{{ url('/about') }}" class="nav-underline text-slate-700 transition hover:text-teal-600 {{ request()->is('about') ? 'text-teal-600 font-bold' : '' }}">About</a>
                @endif

                @if ($user?->isAdmin())
                    <a href="{{ url('/admin') }}" class="text-xs font-bold text-teal-700 hover:underline">Admin</a>
                @endif

                <a href="{{ url('/account') }}" class="btn-pop rounded-full bg-gradient-to-r from-[#2e3760] to-[#ff3131] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-md hover:shadow-[#ff3131]/20 transition hover:from-[#232b4b] hover:to-[#e11d27]">
                    @if ($user?->subscribed)
                        <span class="inline-flex items-center gap-1.5">My Account <x-icon name="star" class="h-3.5 w-3.5 text-amber-300" /></span>
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
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-700 md:hidden hover:bg-slate-50 transition shadow-2xs"
            >
                <span class="sr-only">Toggle menu</span>
                <div class="flex h-4 w-5 flex-col justify-between">
                    <span class="h-0.5 w-5 bg-slate-800 transition-transform duration-200" :class="open ? 'translate-y-[7px] rotate-45' : ''"></span>
                    <span class="h-0.5 w-5 bg-slate-800 transition-opacity duration-200" :class="open ? 'opacity-0' : 'opacity-100'"></span>
                    <span class="h-0.5 w-5 bg-slate-800 transition-transform duration-200" :class="open ? '-translate-y-[7px] -rotate-45' : ''"></span>
                </div>
            </button>
        </div>
    </div>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute left-0 right-0 top-full flex flex-col gap-1 border-b border-slate-200/80 bg-white/95 backdrop-blur-md px-5 pb-6 pt-3 text-sm font-medium text-slate-800 shadow-2xl rounded-b-3xl md:hidden"
    >
        <div class="mb-3 flex items-center rounded-full bg-slate-100 p-1 text-xs font-semibold border border-slate-200/70">
            <a href="{{ url('/') }}" @click="open = false" class="flex-1 rounded-full px-3 py-2 text-center transition {{ ! $isEmployerContext ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600' }}">Job Seeker</a>
            <a href="{{ url('/employers') }}" @click="open = false" class="flex-1 rounded-full px-3 py-2 text-center transition {{ $isEmployerContext ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600' }}">Employer</a>
        </div>

        @if ($isEmployerContext)
            <a href="{{ url('/employers') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition">Overview</a>
            <a href="{{ url('/employers/post') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition">Post a Job</a>
            <a href="{{ url('/employers/dashboard') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition">Dashboard</a>
        @else
            <button type="button" @click="mobileJobsOpen = !mobileJobsOpen" :aria-expanded="mobileJobsOpen" class="flex items-center justify-between rounded-xl px-3 py-2 text-left text-slate-700 hover:bg-slate-50 hover:text-teal-700">
                Jobs
                <span class="text-xs transition-transform" :class="mobileJobsOpen ? 'rotate-180' : ''">&#9662;</span>
            </button>
            <div x-show="mobileJobsOpen" x-cloak class="flex flex-col gap-0.5 pl-3 border-l-2 border-teal-100 ml-2">
                <a href="{{ url('/jobs') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="globe" class="h-4 w-4 text-teal-600" /> All Opportunities
                </a>
                <a href="{{ url('/companies') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="globe" class="h-4 w-4 text-teal-600" /> Hiring Companies
                </a>
                <a href="{{ url('/collections') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="sparkle" class="h-4 w-4 text-teal-600" /> Curated Collections
                </a>
                @foreach (\App\Support\Audience::ORDER as $segment)
                    <a href="{{ url('/jobs') }}?audience={{ $segment }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                        <x-icon :name="\App\Support\Audience::ICONS[$segment]" class="h-4 w-4 text-teal-600" /> {{ \App\Support\Audience::LABELS[$segment] }}
                    </a>
                @endforeach
                <a href="{{ url('/match') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="sparkle" class="h-4 w-4 text-teal-600" /> Match Score
                </a>
                <a href="{{ url('/resume-builder') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="pen" class="h-4 w-4 text-teal-600" /> CV Builder
                </a>
                <a href="{{ url('/journal') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="newspaper" class="h-4 w-4 text-teal-600" /> The Journal
                </a>
                <a href="{{ url('/pricing') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="card" class="h-4 w-4 text-teal-600" /> Pricing
                </a>
                <a href="{{ url('/faqs') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="bulb" class="h-4 w-4 text-teal-600" /> FAQs
                </a>
                <a href="{{ url('/surveys') }}" @click="open = false; mobileJobsOpen = false" class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 text-slate-600 hover:bg-teal-50 hover:text-teal-800 transition">
                    <x-icon name="coin" class="h-4 w-4 text-teal-600" /> Earn While You Search
                </a>
            </div>
            <a href="{{ url('/companies') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('companies*') ? 'text-teal-600 font-bold' : '' }}">Companies</a>
            <a href="{{ url('/collections') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('collections*') ? 'text-teal-600 font-bold' : '' }}">Collections</a>
            <a href="{{ url('/journal') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('journal*') ? 'text-teal-600 font-bold' : '' }}">Journal</a>
            <a href="{{ url('/pricing') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('pricing') ? 'text-teal-600 font-bold' : '' }}">Pricing</a>
            <a href="{{ url('/faqs') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('faqs*') ? 'text-teal-600 font-bold' : '' }}">FAQs</a>
            <a href="{{ url('/about') }}" @click="open = false" class="rounded-xl px-3 py-2 text-slate-700 hover:bg-teal-50 hover:text-teal-800 transition {{ request()->is('about') ? 'text-teal-600 font-bold' : '' }}">About</a>
        @endif

        @if ($user?->isAdmin())
            <a href="{{ url('/admin') }}" @click="open = false" class="rounded-xl px-3 py-2 font-bold text-teal-600 hover:bg-slate-50">Admin</a>
        @endif
        <a href="{{ url('/account') }}" @click="open = false" class="mt-3 rounded-full bg-gradient-to-r from-teal-600 to-teal-700 px-5 py-3 text-center font-bold text-white hover:from-teal-700 hover:to-teal-800 shadow-md shadow-teal-600/20">
            {{ $accountLabel }}
        </a>
    </div>
</header>
