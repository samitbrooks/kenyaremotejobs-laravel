<div class="rounded-3xl border border-slate-200/80 p-6 sm:p-10 text-slate-900 shadow-sm relative overflow-hidden">
    {{-- Glow background --}}

    <div class="relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#3444da]">
                <x-icon name="crown" class="h-3.5 w-3.5 text-[#3444da]" /> What Membership Includes
            </span>
            <span class="text-xs text-slate-500 font-medium">
                M-Pesa Instant Activation &bull; Zero Long-term Contract
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7">
                <h3 class="text-2xl sm:text-3xl font-semibold text-slate-900 tracking-tight">
                    Tools for your next application
                </h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Compare free browsing with Pro application access and career tools, then decide what is useful for your search.
                </p>

                {{-- Side-by-Side Comparison --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Free Box --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Standard Free</span>
                        <p class="text-base font-semibold text-slate-900 mt-1">KES 0</p>
                        <ul class="mt-3 space-y-2 text-slate-500">
                            <li class="flex items-center gap-2"><span>&bull;</span> Redacted recruiter links & emails</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Locked direct apply on remote roles</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Basic job board browsing</li>
                        </ul>
                    </div>

                    {{-- Pro Box --}}
                    <div class="rounded-2xl border-2 border-[#3444da] bg-[#eef0fe] p-4 relative shadow-sm">
                        <span class="rounded-full bg-[#3444da] text-white font-semibold text-[9px] px-2 py-0.5 uppercase tracking-wider absolute top-3 right-3">Career Tools</span>
                        <span class="text-[#3444da] font-bold uppercase tracking-wider text-[10px]">Pro Membership</span>
                        <p class="text-base font-semibold text-slate-900 mt-1">KES 250 <span class="text-xs text-slate-500 font-normal">/ month</span></p>
                        <ul class="mt-3 space-y-2 text-slate-800">
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Instant 0-second apply access</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> AI CV tailoring suggestions</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Direct application links</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Priority Support Desk &amp; CRM</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 rounded-2xl border border-slate-200/80 bg-white p-6 text-center shadow-sm">
                <h4 class="text-xl font-bold text-slate-900">Choose Pro when it helps your job search</h4>
                <p class="mt-3 text-sm text-slate-600">Browse current roles first. Membership gives you application links and career tools; employers decide who to interview and hire.</p>
                <a href="{{ route('pricing') }}" class="mt-5 block rounded-lg bg-[#3444da] px-5 py-3 text-sm font-bold text-white">See Pro membership options &rarr;</a>
            </div>
        </div>
    </div>
</div>
