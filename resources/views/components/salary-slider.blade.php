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
    class="relative overflow-hidden rounded-[24px] border border-[#e6e8f2] bg-white p-6 sm:p-8 text-slate-900 shadow-[0_4px_24px_rgba(15,23,42,0.06)]"
>
    <div class="relative z-10">
        {{-- Section Header Badge --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-[#3444da]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#3444da] animate-pulse"></span>
                Currency Multiplier Calculator
            </span>
            <span class="text-xs text-slate-500 font-medium">
                Exchange benchmark: 1 USD &approx; 130 KES
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            {{-- Left column: Controls --}}
            <div class="lg:col-span-7 space-y-5">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        Calculate Your Remote Earning Power in Kenya
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Earn globally in USD while spending locally in Kenyan Shillings. Slide below to see what international remote contracts translate to in monthly take-home pay:
                    </p>
                </div>

                {{-- Interactive Slider --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                        <span>$1,000/mo (Entry Level)</span>
                        <span class="text-[#3444da] text-base font-extrabold" x-text="'$' + monthlyUsd.toLocaleString() + ' / month'"></span>
                        <span>$8,000/mo (Senior/Lead)</span>
                    </div>

                    <input
                        type="range"
                        min="1000"
                        max="8000"
                        step="250"
                        x-model="monthlyUsd"
                        class="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#3444da]"
                    />

                    <div class="flex justify-between text-[11px] text-slate-400 font-medium">
                        <span>~KES 130,000/mo</span>
                        <span>~KES 520,000/mo</span>
                        <span>~KES 1,040,000/mo</span>
                    </div>
                </div>
            </div>

            {{-- Right column: Dynamic Display Card --}}
            <div class="lg:col-span-5 rounded-[20px] border border-[#e6e8f2] bg-[#f8f9fd] p-6 shadow-xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Estimated Take-Home (Gross)</p>
                
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl sm:text-3xl font-black text-[#3444da]">KES</span>
                    <span class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900" x-text="monthlyKes"></span>
                    <span class="text-xs text-slate-500 font-semibold">/mo</span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 pt-4 border-t border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-500">Annual Equivalent:</span>
                        <p class="font-extrabold text-slate-900 text-sm" x-text="'$' + annualUsd + '/yr'"></p>
                    </div>
                    <div>
                        <span class="text-slate-500">Hourly Rate:</span>
                        <p class="font-extrabold text-slate-900 text-sm" x-text="'~$' + hourlyUsd + '/hr'"></p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between text-xs text-slate-600 mb-3">
                        <span class="flex items-center gap-1.5 font-bold text-[#3444da]">
                            <span>✨</span> <span x-text="matchCount + ' active roles'"></span>
                        </span>
                        <span class="text-slate-400 font-medium">at or above this tier</span>
                    </div>

                    <a
                        href="{{ url('/jobs') }}"
                        class="btn-pop block w-full rounded-full bg-[#3444da] hover:bg-[#2633bf] py-3 text-center text-xs font-bold tracking-wider text-white shadow-md shadow-[#3444da]/20 transition"
                    >
                        Browse Matching Remote Roles &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
