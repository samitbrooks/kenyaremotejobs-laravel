@php
    $advantages = [
        [
            'icon' => 'globe',
            'title' => 'Top-Tier English Fluency',
            'body' => 'Kenya consistently ranks in the top tier of the EF English Proficiency Index in Africa. Education and commercial enterprise are conducted 100% in English, ensuring crystal-clear written and verbal collaboration with global teams.',
            'stat' => '#1 in East Africa',
            'stat_label' => 'English Fluency',
        ],
        [
            'icon' => 'clock',
            'title' => 'Ideal Timezone Overlap (UTC+3)',
            'body' => 'Nairobi sits in East Africa Time (UTC+3), giving your team a full 6–7 hours of direct synchronous overlap with London and Berlin, plus 4–5 hours of synchronous morning collaboration with New York and Toronto.',
            'stat' => '6+ Hours',
            'stat_label' => 'Synchronous EU/UK Overlap',
        ],
        [
            'icon' => 'sparkle',
            'title' => 'Silicon Savannah Tech Pedigree',
            'body' => 'Nairobi is home to major engineering hubs for Google, Microsoft ADC, Amazon AWS, and Safaricom. Access elite software engineers, technical product managers, designers, and customer success champions.',
            'stat' => '300,000+',
            'stat_label' => 'Skilled Tech & Knowledge Pros',
        ],
        [
            'icon' => 'coin',
            'title' => 'Unbeatable Cost & Retention ROI',
            'body' => 'Hire top 5% talent at 60–75% lower total cost compared to San Francisco, London, or Sydney — while paying competitive, life-changing compensation locally that creates unmatched loyalty and multi-year retention.',
            'stat' => '65%+',
            'stat_label' => 'Typical Employer Cost Savings',
        ],
    ];

    $timezones = [
        [
            'city' => 'London (GMT / BST)',
            'eat_hours' => '10:00 AM – 6:00 PM EAT',
            'local_hours' => '7:00 AM – 3:00 PM GMT',
            'overlap' => '7–8 hours synchronous overlap',
            'badge' => 'Full Day Match',
        ],
        [
            'city' => 'Berlin / Paris (CET)',
            'eat_hours' => '10:00 AM – 6:00 PM EAT',
            'local_hours' => '8:00 AM – 4:00 PM CET',
            'overlap' => '7 hours synchronous overlap',
            'badge' => 'Full Day Match',
        ],
        [
            'city' => 'New York (EST / EDT)',
            'eat_hours' => '2:00 PM – 10:00 PM EAT',
            'local_hours' => '7:00 AM – 3:00 PM EST',
            'overlap' => '4–5 hours synchronous overlap',
            'badge' => 'High Overlap',
        ],
        [
            'city' => 'San Francisco (PST / PDT)',
            'eat_hours' => '4:00 PM – Midnight EAT',
            'local_hours' => '6:00 AM – 2:00 PM PST',
            'overlap' => '3–4 hours morning overlap',
            'badge' => 'Async + Standup',
        ],
    ];

    $steps = [
        ['title' => '1. Create an account', 'body' => 'Sign up in 30 seconds with your work email — no credit card required to start drafting.'],
        ['title' => '2. Build your job post', 'body' => 'Set title, requirements, salary range, and direct application link (Greenhouse, Lever, or your career site).'],
        ['title' => '3. Choose your plan', 'body' => 'Select Basic or Boost visibility to reach active candidates across Kenya and East Africa.'],
        ['title' => '4. Receive vetted applicants', 'body' => 'Candidates apply directly through your company portal with zero recruiter middlemen.'],
    ];
@endphp

<x-layouts.app
    title="Hire Remote Talent in Kenya & East Africa — Remote Employer Hub"
    description="Hire pre-vetted remote software engineers, customer success specialists, and operations talent in Kenya. Full UK/EU timezone overlap, native English fluency, and 60%+ cost efficiency."
    :canonical="url('/employers')"
>
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-horizon-950 via-horizon-900 to-slate-950 px-4 pb-20 pt-16 text-white sm:px-6 sm:pt-24">
        {{-- Background Glow --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-sunrise-500/15 blur-3xl"></div>
            <div class="absolute -left-24 bottom-0 h-96 w-96 rounded-full bg-indigo-500/15 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-5xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunrise-500/20 border border-sunrise-400/30 px-4 py-1 text-xs font-bold text-sunrise-300">
                <x-icon name="globe" class="h-4 w-4 text-sunrise-400" />
                Trusted Remote Hiring Hub for East Africa
            </span>

            <h1 class="mt-6 text-4xl font-black tracking-tight sm:text-6xl sm:leading-[1.1]">
                Hire world-class remote talent in <br class="hidden sm:inline" />
                <span class="gradient-sunrise bg-clip-text text-transparent">Kenya &amp; East Africa.</span>
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-base text-white/75 sm:text-xl leading-relaxed">
                Connect your open remote roles directly with over 25,000 highly skilled, English-fluent Kenyan developers, designers, product managers, and support champions.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ url('/employers/post') }}" class="btn-pop rounded-full bg-sunrise-500 px-8 py-4 font-bold text-white shadow-xl transition hover:bg-sunrise-600 text-base">
                    Post a Remote Job &rarr;
                </a>
                <a href="#why-kenya" class="rounded-full border border-white/20 px-6 py-4 text-sm font-semibold text-white transition hover:bg-white/10">
                    Why Global Companies Hire in Kenya &darr;
                </a>
            </div>

            {{-- Social Proof Stat Bar --}}
            <div class="mt-14 grid grid-cols-2 gap-4 border-t border-white/10 pt-10 sm:grid-cols-4">
                <div>
                    <p class="text-3xl font-extrabold text-white">25,000+</p>
                    <p class="text-xs text-white/60 mt-1">Active Kenyan Job Seekers</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-sunrise-400">#1</p>
                    <p class="text-xs text-white/60 mt-1">Tech Talent Hub in East Africa</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-emerald-400">UTC+3</p>
                    <p class="text-xs text-white/60 mt-1">Ideal Synchronous Overlap</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-indigo-400">100%</p>
                    <p class="text-xs text-white/60 mt-1">Direct Application Links</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Kenya Section --}}
    <section id="why-kenya" class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <x-reveal class="mx-auto max-w-3xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-sunrise-600">The East African Advantage</span>
            <h2 class="mt-2 text-3xl font-black text-horizon-950 sm:text-4xl">
                Why Top Global Companies Are Building Remote Teams in Kenya
            </h2>
            <p class="mt-4 text-base text-foreground/70">
                From GitLab and Automattic to Superside and Outlier.ai, global employers tap into Nairobi's vibrant, tech-savvy workforce for high-performance distributed teams.
            </p>
        </x-reveal>

        <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2">
            @foreach ($advantages as $i => $adv)
                <x-reveal :delay="$i * 80" class="h-full">
                    <div class="flex h-full flex-col justify-between rounded-3xl border border-black/5 bg-white p-8 shadow-sm transition hover:shadow-lg hover:border-horizon-200">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-horizon-50 text-horizon-800">
                                    <x-icon :name="$adv['icon']" class="h-6 w-6" />
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-sunrise-600">{{ $adv['stat'] }}</p>
                                    <p class="text-[11px] text-foreground/50">{{ $adv['stat_label'] }}</p>
                                </div>
                            </div>
                            <h3 class="mt-6 text-xl font-bold text-foreground">{{ $adv['title'] }}</h3>
                            <p class="mt-3 text-sm text-foreground/70 leading-relaxed">{{ $adv['body'] }}</p>
                        </div>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>

    {{-- Timezone Overlap Matrix --}}
    <section class="bg-horizon-50/60 px-4 py-20 sm:px-6 border-y border-black/5">
        <div class="mx-auto max-w-5xl">
            <x-reveal class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-horizon-700">Synchronous Collaboration</span>
                <h2 class="mt-2 text-3xl font-black text-horizon-950 sm:text-4xl">
                    East Africa Time (UTC+3) Timezone Matrix
                </h2>
                <p class="mt-3 text-sm text-foreground/70">
                    Kenyan professionals work standard business hours that align smoothly with European headquarters and US East Coast mornings.
                </p>
            </x-reveal>

            <div class="mt-12 overflow-hidden rounded-3xl border border-black/5 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-horizon-50/80 text-xs uppercase tracking-wider text-horizon-800 border-b border-black/5">
                            <tr>
                                <th class="py-4 px-6 font-bold">Your City / Region</th>
                                <th class="py-4 px-6 font-bold">Kenya Work Hours (EAT)</th>
                                <th class="py-4 px-6 font-bold">Your Local Working Hours</th>
                                <th class="py-4 px-6 font-bold">Daily Overlap</th>
                                <th class="py-4 px-6 font-bold">Alignment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 text-foreground/80">
                            @foreach ($timezones as $tz)
                                <tr class="hover:bg-horizon-50/30 transition-colors">
                                    <td class="py-4 px-6 font-bold text-foreground">{{ $tz['city'] }}</td>
                                    <td class="py-4 px-6 text-xs text-foreground/70">{{ $tz['eat_hours'] }}</td>
                                    <td class="py-4 px-6 text-xs font-medium text-horizon-900">{{ $tz['local_hours'] }}</td>
                                    <td class="py-4 px-6 text-xs font-semibold text-emerald-700">{{ $tz['overlap'] }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                            {{ $tz['badge'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <x-reveal class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-widest text-sunrise-600">Frictionless Hiring</span>
            <h2 class="mt-2 text-3xl font-black text-horizon-950 sm:text-4xl">
                How Posting on KenyaRemoteJobs Works
            </h2>
            <p class="mt-3 text-sm text-foreground/70">
                Zero complex recruiter contracts. Post in under 5 minutes, direct candidates to your own ATS, and hire.
            </p>
        </x-reveal>

        <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($steps as $i => $step)
                <x-reveal :delay="$i * 90" class="text-center">
                    <div class="rounded-3xl border border-black/5 bg-white p-6 shadow-sm h-full">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-horizon-900 to-slate-900 text-lg font-bold text-white shadow-md">
                            {{ $i + 1 }}
                        </div>
                        <h3 class="font-bold text-base text-foreground">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-xs text-foreground/60 leading-relaxed">{{ $step['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>

    {{-- Transparent Pricing Plans --}}
    <section class="bg-gradient-to-b from-white to-horizon-50/60 px-4 py-20 sm:px-6 border-t border-black/5">
        <div class="mx-auto max-w-4xl">
            <x-reveal class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-horizon-700">Straightforward Pricing</span>
                <h2 class="mt-2 text-3xl font-black text-horizon-950 sm:text-4xl">
                    Simple, Transparent Job Posting Plans
                </h2>
                <p class="mt-3 text-sm text-foreground/70">
                    Fraction of the price of global boards ($299+ on Remote.co/RemoteOK), with 100% targeted East African reach.
                </p>
            </x-reveal>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach (config('jobs.posting_plans') as $key => $pkg)
                    <x-reveal class="h-full">
                        <div class="relative flex h-full flex-col justify-between rounded-3xl border {{ $pkg['featured'] ? 'border-2 border-sunrise-500 shadow-xl' : 'border-black/5 shadow-sm' }} bg-white p-8">
                            @if ($pkg['featured'])
                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-sunrise-500 px-4 py-0.5 text-xs font-bold text-white shadow-md">
                                    ★ Most Popular Choice
                                </span>
                            @endif

                            <div>
                                <h3 class="font-bold text-xl text-foreground">{{ $pkg['label'] }}</h3>
                                <div class="mt-4 flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-horizon-950">KES {{ number_format($pkg['price_kes']) }}</span>
                                    <span class="text-sm font-semibold text-foreground/50">(~${{ round($pkg['price_kes'] / 130) }} USD)</span>
                                </div>
                                <p class="mt-1 text-xs text-foreground/60">{{ $pkg['listing_days'] }} days of live candidate visibility</p>

                                <ul class="mt-6 space-y-2.5 border-t border-black/5 pt-6 text-xs text-foreground/80">
                                    @foreach ($pkg['features'] ?? [] as $feature)
                                        <li class="flex items-start gap-2">
                                            <x-icon name="check" class="h-4 w-4 shrink-0 text-emerald-600 mt-0.5" />
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-8 pt-6 border-t border-black/5">
                                <a
                                    href="{{ url('/employers/post?plan='.$key) }}"
                                    class="btn-pop block w-full rounded-full {{ $pkg['featured'] ? 'bg-sunrise-500 hover:bg-sunrise-600 text-white' : 'bg-horizon-900 hover:bg-black text-white' }} py-3.5 text-center text-sm font-bold shadow-md transition"
                                >
                                    Select {{ $pkg['label'] }} &rarr;
                                </a>
                            </div>
                        </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Final Employer CTA --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="rounded-3xl bg-gradient-to-r from-horizon-950 via-horizon-900 to-slate-900 p-8 text-center text-white shadow-2xl sm:p-12">
            <h2 class="text-2xl font-black sm:text-4xl">Ready to Hire Exceptional Remote Talent?</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm text-white/70 sm:text-base">
                Publish your role in minutes. Receive direct applications from qualified East African candidates already prepared for remote work.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ url('/employers/post') }}" class="btn-pop rounded-full bg-sunrise-500 px-8 py-3.5 font-bold text-white shadow-lg transition hover:bg-sunrise-600">
                    Post a Job Now &rarr;
                </a>
                <a href="{{ url('/employers/dashboard') }}" class="rounded-full border border-white/20 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    Employer Dashboard
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
