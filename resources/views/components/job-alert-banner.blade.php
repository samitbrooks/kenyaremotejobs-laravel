@props([
    'title' => 'Get Kenya Remote Job Alerts in Your Inbox',
    'subtitle' => 'Save your job preferences and choose email updates in your account. Review each role’s requirements before applying.',
])

<div class="rounded-2xl border border-slate-200/80 p-8 sm:p-10 text-slate-900 shadow-sm relative overflow-hidden">
    {{-- Decorative Background Blobs --}}

    <div class="relative z-10 max-w-2xl">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3 py-0.5 text-xs font-bold text-[#3444da]">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-[#3444da]" />
                Never Miss an Opening
            </span>
            <span class="text-xs text-slate-500">&bull; 100% Free Alerts</span>
        </div>

        <h3 class="mt-4 text-2xl font-semibold tracking-tight sm:text-3xl text-slate-900">
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
                    class="w-full rounded-lg border border-slate-200 bg-white px-5 py-3.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#3444da] focus:outline-none focus:ring-2 focus:ring-[#3444da]/20 transition shadow-2xs"
                />
            </div>
            <button
                type="submit"
                class="btn-pop rounded-lg bg-[#3444da] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#2633bf] shrink-0 text-center"
            >
                Set up my alerts &rarr;
            </button>
        </form>

        {{-- Benefits List --}}
        <div class="mt-5 flex flex-wrap items-center gap-4 text-xs text-slate-600">
            <span class="flex items-center gap-1">
                <x-icon name="check" class="h-3.5 w-3.5 text-[#3444da]" />
                Job match notifications
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
