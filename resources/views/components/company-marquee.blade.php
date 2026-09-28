@php
    $companies = [
        ['name' => 'GitLab', 'desc' => '100% All-Remote pioneer'],
        ['name' => 'Automattic', 'desc' => 'WordPress & WooCommerce creator'],
        ['name' => 'Canonical', 'desc' => 'Ubuntu Linux global team'],
        ['name' => 'Deel', 'desc' => 'Global hiring & payroll network'],
        ['name' => 'Buffer', 'desc' => 'Pioneer in global 4-day workweek'],
        ['name' => 'Turing', 'desc' => 'AI & Engineering placements'],
        ['name' => 'Remote.com', 'desc' => 'Global employer of record'],
        ['name' => 'Shopify', 'desc' => 'Digital by design commerce'],
    ];
@endphp

<div class="py-10 border-y border-slate-200/70 bg-slate-50/50 overflow-hidden">
    <div class="mx-auto max-w-6xl px-4 text-center sm:px-6 mb-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
            Top global employers hiring Kenyan talent across East Africa Time (UTC+3)
        </p>
    </div>

    {{-- Infinite Scrolling Flex Marquee --}}
    <div class="relative w-full overflow-hidden mask-gradient-x">
        <style>
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .animate-marquee-smooth {
                display: flex;
                width: 200%;
                animation: marquee 28s linear infinite;
            }
            .animate-marquee-smooth:hover {
                animation-play-state: paused;
            }
        </style>

        <div class="animate-marquee-smooth flex items-center gap-6 sm:gap-10">
            {{-- Loop twice for infinite scroll --}}
            @for ($i = 0; $i < 2; $i++)
                @foreach ($companies as $comp)
                    <div class="inline-flex items-center gap-2.5 rounded-2xl border border-slate-200/80 bg-white px-5 py-2.5 shadow-2xs shrink-0 hover:border-slate-300 transition">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <div>
                            <span class="font-extrabold text-slate-900 text-sm tracking-tight">{{ $comp['name'] }}</span>
                            <span class="hidden sm:inline text-slate-400 text-xs font-normal"> &bull; {{ $comp['desc'] }}</span>
                        </div>
                    </div>
                @endforeach
            @endfor
        </div>
    </div>
</div>
