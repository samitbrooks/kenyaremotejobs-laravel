@props([
    'title' => 'Get Kenya Remote Job Alerts in Your Inbox',
    'subtitle' => 'Join 25,000+ Kenyan professionals receiving verified USD roles, Wise/M-Pesa payout notices, and zero-visa remote openings.',
])

<div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/70 p-8 sm:p-10 text-slate-900 shadow-sm relative overflow-hidden">
    {{-- Decorative Background Blobs --}}
    <div class="pointer-events-none absolute -right-12 -bottom-12 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-12 -top-12 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>

    <div class="relative z-10 max-w-2xl">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3 py-0.5 text-xs font-bold text-[#3444da]">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-[#3444da]" />
                Never Miss an Opening
            </span>
            <span class="text-xs text-slate-500">&bull; 100% Free Alerts</span>
        </div>

        <h3 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl text-slate-900">
            {{ $title }}
        </h3>
        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
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
                    class="w-full rounded-full border border-slate-200 bg-white px-5 py-3.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#3444da] focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 transition shadow-2xs"
                />
            </div>
            <button
                type="submit"
                class="btn-pop rounded-full bg-[#3444da] px-6 py-3.5 text-sm font-bold text-white shadow-md shadow-[#3444da]/20 transition hover:bg-[#2633bf] shrink-0 text-center"
            >
                Subscribe Free &rarr;
            </button>
        </form>

        {{-- Benefits List --}}
        <div class="mt-5 flex flex-wrap items-center gap-4 text-xs text-slate-600">
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                Early 48h notifications
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                Vetted for East Africa Time
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                1-click unsubscribe anytime
            </span>
        </div>
    </div>
</div>
