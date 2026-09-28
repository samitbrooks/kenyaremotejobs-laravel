<div
    x-data="{
        activities: [
            '🔥 A candidate in Nairobi just applied to Senior Customer Support ($2,400/mo)',
            '⚡ New Kenya-Friendly Full-Stack role added 12 mins ago (KES 450,000/mo)',
            '🇰🇪 Recruiter first-look window active for 18 fresh worldwide roles',
            '✓ Faith from Mombasa landed a remote Virtual Assistant contract yesterday',
            '💼 800+ international remote roles currently accepting Kenyan applications'
        ],
        currentIndex: 0,
        init() {
            setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.activities.length;
            }, 4500);
        }
    }"
    class="relative z-20 border-b border-slate-200/80 bg-gradient-to-r from-slate-900 via-slate-950 to-[#1f274a] text-white py-2 px-4 shadow-inner"
>
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 text-xs">
        {{-- Left: Live Activity Pill --}}
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
            </span>
            <span class="font-bold uppercase tracking-wider text-emerald-400 text-[10px] hidden sm:inline">LIVE PLATFORM PULSE:</span>
            
            {{-- Rotating Activity Stream with Smooth Transition --}}
            <div class="relative h-5 overflow-hidden text-slate-200 font-medium max-w-xs sm:max-w-md">
                <template x-for="(act, idx) in activities" :key="idx">
                    <div
                        x-show="currentIndex === idx"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="truncate"
                        x-text="act"
                    ></div>
                </template>
            </div>
        </div>

        {{-- Right: Live Stats --}}
        <div class="hidden sm:flex items-center gap-4 text-[11px] text-slate-300">
            <span class="flex items-center gap-1.5">
                <strong class="text-white">800+</strong> Open Roles
            </span>
            <span class="text-slate-600">&bull;</span>
            <span class="flex items-center gap-1.5 text-amber-300">
                <span>⚡</span> 48h Early Access Active
            </span>
            <span class="text-slate-600">&bull;</span>
            <a href="{{ url('/pricing') }}" class="font-bold text-[#ff5757] hover:underline flex items-center gap-1">
                Get VIP Pro Access &rarr;
            </a>
        </div>
    </div>
</div>
