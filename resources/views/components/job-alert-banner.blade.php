@props([
    'title' => 'Get Kenya Remote Job Alerts in Your Inbox',
    'subtitle' => 'Join 25,000+ Kenyan professionals receiving verified USD roles, Wise/M-Pesa payout notices, and zero-visa remote openings.',
])

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 sm:p-10 text-white shadow-lg relative overflow-hidden">
    {{-- Decorative Background Blobs --}}
    <div class="pointer-events-none absolute -right-12 -bottom-12 h-64 w-64 rounded-full bg-teal-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-12 -top-12 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

    <div class="relative z-10 max-w-2xl">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-500/20 border border-teal-400/30 px-3 py-0.5 text-xs font-bold text-teal-300">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-400" />
                Never Miss an Opening
            </span>
            <span class="text-xs text-slate-400">&bull; 100% Free Alerts</span>
        </div>

        <h3 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl text-white">
            {{ $title }}
        </h3>
        <p class="mt-2 text-sm text-slate-300 leading-relaxed">
            {{ $subtitle }}
        </p>

        {{-- Interactive Email Subscription Form --}}
        <form 
            action="{{ url('/account') }}" 
            method="GET" 
            class="mt-6 flex flex-col sm:flex-row items-stretch gap-2.5 max-w-lg"
        >
            <div class="relative flex-1">
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="Enter your email address..."
                    class="w-full rounded-full border border-slate-700 bg-slate-800/80 px-5 py-3.5 text-sm text-white placeholder-slate-400 focus:border-teal-400 focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-400/20 transition"
                />
            </div>
            <button
                type="submit"
                class="btn-pop rounded-full bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-teal-700 shrink-0 text-center"
            >
                Subscribe Free &rarr;
            </button>
        </form>

        {{-- Benefits List --}}
        <div class="mt-5 flex flex-wrap items-center gap-4 text-xs text-white/60">
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-400" />
                Early 48h notifications
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-400" />
                Vetted for East Africa Time
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-emerald-400" />
                1-click unsubscribe anytime
            </span>
        </div>
    </div>
</div>
