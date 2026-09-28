<div
    x-data="{
        step: 1,
        field: 'support',
        experience: 'mid',
        setup: 'yes',
        get estimatedRoles() {
            let base = 28;
            if (this.field === 'tech') base = 48;
            if (this.field === 'support') base = 36;
            if (this.field === 'va') base = 32;
            if (this.field === 'marketing') base = 24;
            if (this.field === 'sales') base = 20;
            return base + (this.experience === 'mid' ? 12 : (this.experience === 'senior' ? 18 : 6));
        },
        get averageSalaryKes() {
            if (this.experience === 'entry') return 'KES 180,000';
            if (this.experience === 'mid') return 'KES 320,000';
            return 'KES 580,000';
        },
        reset() {
            this.step = 1;
            this.field = 'support';
            this.experience = 'mid';
            this.setup = 'yes';
        }
    }"
    class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 shadow-lg relative overflow-hidden"
>
    {{-- Progress bar --}}
    <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 border border-rose-200/60 px-3 py-1 text-[11px] font-bold text-rose-700">
                <span>⏱</span> 60-Second Readiness Check
            </span>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">Find Your Remote Job Match Score</h3>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-slate-400" x-text="step <= 3 ? 'Step ' + step + ' of 3' : 'Your Match Results'"></span>
            <div class="mt-1 h-2 w-28 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#2e3760] to-[#ff3131] transition-all duration-300" :style="'width: ' + (step === 1 ? '33%' : (step === 2 ? '66%' : '100%'))"></div>
            </div>
        </div>
    </div>

    {{-- Step 1: Field Selection --}}
    <div x-show="step === 1" x-transition class="space-y-4">
        <p class="text-sm font-semibold text-slate-700">1. What is your primary field or interest?</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <button
                type="button"
                @click="field = 'support'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'support' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">🎧</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Customer Support &amp; Success</span>
                <span class="text-[10px] text-slate-500 mt-0.5">High hiring volume</span>
            </button>

            <button
                type="button"
                @click="field = 'tech'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'tech' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">💻</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Software &amp; Cloud Tech</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Highest USD salary</span>
            </button>

            <button
                type="button"
                @click="field = 'va'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'va' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">📋</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Virtual Assistant &amp; Admin</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Fastest to start</span>
            </button>

            <button
                type="button"
                @click="field = 'marketing'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'marketing' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">📣</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Digital Marketing &amp; Social</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Creative growth roles</span>
            </button>

            <button
                type="button"
                @click="field = 'sales'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'sales' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">📈</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Sales &amp; Lead Generation</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Commissions in USD</span>
            </button>

            <button
                type="button"
                @click="field = 'writing'; step = 2"
                class="rounded-2xl border p-4 text-left transition hover:border-[#ff3131] hover:bg-rose-50/30 flex flex-col justify-between"
                :class="field === 'writing' ? 'border-[#ff3131] bg-rose-50/20' : 'border-slate-200'"
            >
                <span class="text-2xl">✍️</span>
                <span class="font-bold text-xs sm:text-sm text-slate-900 mt-2">Content &amp; Copywriting</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Flexible schedules</span>
            </button>
        </div>
    </div>

    {{-- Step 2: Experience --}}
    <div x-show="step === 2" x-transition class="space-y-4">
        <p class="text-sm font-semibold text-slate-700">2. How much work experience do you have?</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <button
                type="button"
                @click="experience = 'entry'; step = 3"
                class="rounded-2xl border border-slate-200 p-5 text-left transition hover:border-[#ff3131] hover:bg-rose-50/20"
            >
                <span class="text-xl">🌱</span>
                <h4 class="font-bold text-slate-900 mt-2 text-sm">Entry Level / Transitioning</h4>
                <p class="text-xs text-slate-500 mt-1">0 - 2 years or eager learner</p>
            </button>

            <button
                type="button"
                @click="experience = 'mid'; step = 3"
                class="rounded-2xl border border-slate-200 p-5 text-left transition hover:border-[#ff3131] hover:bg-rose-50/20"
            >
                <span class="text-xl">🚀</span>
                <h4 class="font-bold text-slate-900 mt-2 text-sm">Mid-Level Professional</h4>
                <p class="text-xs text-slate-500 mt-1">2 - 5 years demonstrable experience</p>
            </button>

            <button
                type="button"
                @click="experience = 'senior'; step = 3"
                class="rounded-2xl border border-slate-200 p-5 text-left transition hover:border-[#ff3131] hover:bg-rose-50/20"
            >
                <span class="text-xl">👑</span>
                <h4 class="font-bold text-slate-900 mt-2 text-sm">Senior / Specialist</h4>
                <p class="text-xs text-slate-500 mt-1">5+ years or team leadership</p>
            </button>
        </div>
        <button type="button" @click="step = 1" class="text-xs text-slate-400 hover:text-slate-600 font-semibold">&larr; Back to skills</button>
    </div>

    {{-- Step 3: Remote Setup --}}
    <div x-show="step === 3" x-transition class="space-y-4">
        <p class="text-sm font-semibold text-slate-700">3. Do you have a functional laptop &amp; internet connection?</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button
                type="button"
                @click="setup = 'yes'; step = 4"
                class="rounded-2xl border border-slate-200 p-5 text-left transition hover:border-emerald-500 hover:bg-emerald-50/30 flex items-center justify-between"
            >
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Yes, Laptop + Home Internet</h4>
                    <p class="text-xs text-slate-500 mt-1">Ready to work from home</p>
                </div>
                <span class="text-2xl text-emerald-600 font-bold">&check;</span>
            </button>

            <button
                type="button"
                @click="setup = 'backup'; step = 4"
                class="rounded-2xl border border-slate-200 p-5 text-left transition hover:border-emerald-500 hover:bg-emerald-50/30 flex items-center justify-between"
            >
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Yes + Backup Power / MiFi</h4>
                    <p class="text-xs text-slate-500 mt-1">100% reliable uptime</p>
                </div>
                <span class="text-2xl text-emerald-600 font-bold">⚡</span>
            </button>
        </div>
        <button type="button" @click="step = 2" class="text-xs text-slate-400 hover:text-slate-600 font-semibold">&larr; Back</button>
    </div>

    {{-- Step 4: Results Card --}}
    <div x-show="step === 4" x-transition class="text-center py-4">
        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-2xl font-bold text-emerald-700 mb-3 shadow-inner">
            🎉
        </span>
        <h3 class="text-2xl font-black text-slate-900">Boom! You're in high demand.</h3>
        <p class="mt-2 text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
            Based on your skills &amp; experience, you qualify for
            <strong class="text-emerald-700 font-extrabold" x-text="estimatedRoles + ' active Kenya-friendly roles'"></strong>
            with an estimated compensation of
            <strong class="text-slate-900 font-extrabold" x-text="averageSalaryKes + ' / month'"></strong>!
        </p>

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
                href="{{ url('/jobs') }}"
                class="btn-pop w-full sm:w-auto rounded-full bg-gradient-to-r from-red-600 to-[#2e3760] px-8 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-red-500/25 hover:from-red-500 hover:to-[#3b477a] transition"
            >
                View Your Matching Roles Now &rarr;
            </a>

            <button
                type="button"
                @click="reset()"
                class="text-xs font-semibold text-slate-500 hover:text-slate-700 px-4 py-2"
            >
                Retake Quiz
            </button>
        </div>
    </div>
</div>
