<div
    x-data="{
        activities: [
            'A candidate in Nairobi just applied to Senior Customer Support ($2,400/mo)',
            'New Kenya-Friendly Full-Stack role added 12 mins ago ($25 - $45/hr)',
            'Recruiter first-look window active for 18 fresh worldwide roles',
            'Faith from Mombasa landed a remote Virtual Assistant contract yesterday',
            '800+ international remote roles currently accepting Kenyan applications'
        ],
        currentIndex: 0,
        init() {
            setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.activities.length;
            }, 4500);
        }
    }"
    class="relative z-20 border-b border-slate-200/70 bg-[#f8f9fd] text-slate-800 py-2 px-4"
>
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 text-xs">
        {{-- Left: Live Activity Pill with Authentic Animated ECG Heartbeat Pulse --}}
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-2.5 py-0.5 text-[#3444da]">
                {{-- Radar / Ping Beacon --}}
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#3444da] opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-[#3444da]"></span>
                </span>

                {{-- Heartbeat Pulse Waveform (ECG Monitor SVG) --}}
                <svg class="h-3.5 w-7 text-[#3444da] shrink-0" viewBox="0 0 32 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 7h6l2.5-5 3.5 10 3-8 2.5 5.5 2-2.5h7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="animate-pulse" />
                </svg>

                <span class="text-[10px] font-black uppercase tracking-wider text-[#3444da]">LIVE</span>
            </div>
            
            {{-- Rotating Activity Stream with Smooth Transition --}}
            <div class="relative h-5 overflow-hidden text-slate-700 font-medium max-w-[220px] sm:max-w-md md:max-w-lg">
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
        <div class="hidden sm:flex items-center gap-4 text-[11px] text-slate-500">
            <span class="flex items-center gap-1.5">
                <strong class="text-slate-900">800+</strong> Open Roles
            </span>
            <span class="text-slate-300">&bull;</span>
            <span class="flex items-center gap-1.5 text-amber-700 font-semibold">
                <x-icon name="lightning" class="h-3 w-3 text-amber-600" /> 48h Early Access Active
            </span>
            <span class="text-slate-300">&bull;</span>
            <a href="{{ url('/pricing') }}" class="font-bold text-[#3444da] hover:text-[#2633bf] hover:underline flex items-center gap-1">
                Get VIP Pro Access &rarr;
            </a>
        </div>
    </div>
</div>
