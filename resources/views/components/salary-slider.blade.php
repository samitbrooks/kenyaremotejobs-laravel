<div
    x-data="{
        monthlyUsd: 3000,
        rate: 130,
        get monthlyKes() {
            return (this.monthlyUsd * this.rate).toLocaleString('en-KE');
        },
        get annualUsd() {
            return (this.monthlyUsd * 12).toLocaleString();
        },
        get hourlyUsd() {
            return (this.monthlyUsd / 173.33).toFixed(2);
        },
        get matchCount() {
            if (this.monthlyUsd <= 2000) return 340;
            if (this.monthlyUsd <= 3500) return 215;
            if (this.monthlyUsd <= 5000) return 128;
            return 64;
        }
    }"
    class="relative overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-[#131728] to-slate-900 p-6 sm:p-8 text-white shadow-2xl"
>
    {{-- Glow background effects --}}
    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-red-600/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>

    <div class="relative z-10">
        {{-- Section Header Badge --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/40 px-3.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-400">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Currency Multiplier Calculator
            </span>
            <span class="text-xs text-slate-400 font-medium">
                Exchange benchmark: 1 USD &approx; 130 KES
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            {{-- Left column: Controls --}}
            <div class="lg:col-span-7 space-y-5">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Calculate Your Remote Earning Power in Kenya
                    </h2>
                    <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                        Earn globally in USD while spending locally in Kenyan Shillings. Slide below to see what international remote contracts translate to in monthly take-home pay:
                    </p>
                </div>

                {{-- Interactive Slider --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                        <span>$1,000/mo (Entry Level)</span>
                        <span class="text-white text-sm font-extrabold" x-text="'$' + monthlyUsd.toLocaleString() + ' / month'"></span>
                        <span>$8,000/mo (Senior/Lead)</span>
                    </div>

                    <input
                        type="range"
                        min="1000"
                        max="8000"
                        step="250"
                        x-model="monthlyUsd"
                        class="w-full h-3 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-[#ff3131]"
                    />

                    <div class="flex justify-between text-[11px] text-slate-500">
                        <span>~KES 130,000/mo</span>
                        <span>~KES 520,000/mo</span>
                        <span>~KES 1,040,000/mo</span>
                    </div>
                </div>
            </div>

            {{-- Right column: Dynamic Display Card --}}
            <div class="lg:col-span-5 rounded-2xl border border-slate-700/80 bg-slate-900/90 p-6 shadow-xl backdrop-blur-md">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimated Take-Home (Gross)</p>
                
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-400">KES</span>
                    <span class="text-3xl sm:text-4xl font-black tracking-tight text-white" x-text="monthlyKes"></span>
                    <span class="text-xs text-slate-400 font-semibold">/mo</span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 pt-4 border-t border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-400">Annual Equivalent:</span>
                        <p class="font-extrabold text-white text-sm" x-text="'$' + annualUsd + '/yr'"></p>
                    </div>
                    <div>
                        <span class="text-slate-400">Hourly Rate:</span>
                        <p class="font-extrabold text-white text-sm" x-text="'~$' + hourlyUsd + '/hr'"></p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800">
                    <div class="flex items-center justify-between text-xs text-slate-300 mb-3">
                        <span class="flex items-center gap-1.5 font-bold text-amber-300">
                            <span>🔥</span> <span x-text="matchCount + ' active roles'"></span>
                        </span>
                        <span class="text-slate-400">at or above this tier</span>
                    </div>

                    <a
                        href="{{ url('/jobs') }}"
                        class="btn-pop block w-full rounded-xl bg-gradient-to-r from-red-600 to-[#2e3760] py-3 text-center text-xs font-extrabold uppercase tracking-wider text-white shadow-lg shadow-red-600/25 hover:from-red-500 hover:to-[#3b477a] transition"
                    >
                        Browse Matching Remote Roles &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
