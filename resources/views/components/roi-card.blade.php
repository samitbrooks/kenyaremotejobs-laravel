<div class="rounded-3xl border border-slate-200/80 bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/40 p-6 sm:p-10 text-slate-900 shadow-sm relative overflow-hidden">
    {{-- Glow background --}}
    <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#3444da]/5 blur-3xl"></div>

    <div class="relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-[#3444da]">
                <x-icon name="crown" class="h-3.5 w-3.5 text-[#3444da]" /> Transparent Membership ROI
            </span>
            <span class="text-xs text-slate-500 font-medium">
                M-Pesa Instant Activation &bull; Zero Long-term Contract
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7">
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Why Pro Pays For Itself On Day One
                </h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Most candidates lose out on high-paying USD roles simply because of delayed applications and generic CV formatting. Pro membership removes every barrier between you and global recruiters.
                </p>

                {{-- Side-by-Side Comparison --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Free Box --}}
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Standard Free</span>
                        <p class="text-base font-extrabold text-slate-900 mt-1">KES 0</p>
                        <ul class="mt-3 space-y-2 text-slate-500">
                            <li class="flex items-center gap-2"><span>&bull;</span> Redacted recruiter links & emails</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Locked direct apply on remote roles</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Basic job board browsing</li>
                        </ul>
                    </div>

                    {{-- Pro Box --}}
                    <div class="rounded-2xl border-2 border-[#3444da] bg-[#eef0fe] p-4 relative shadow-sm">
                        <span class="rounded-full bg-[#3444da] text-white font-black text-[9px] px-2 py-0.5 uppercase tracking-wider absolute top-3 right-3">1,000x ROI</span>
                        <span class="text-[#3444da] font-bold uppercase tracking-wider text-[10px]">VIP Pro Early Access</span>
                        <p class="text-base font-extrabold text-slate-900 mt-1">KES 250 <span class="text-xs text-slate-500 font-normal">/ month</span></p>
                        <ul class="mt-3 space-y-2 text-slate-800">
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Instant 0-second apply access</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> 1-on-1 CV review to beat ATS</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Unredacted direct recruiter links</li>
                            <li class="flex items-center gap-2 text-[#3444da] font-semibold"><span>&check;</span> Priority Support Desk &amp; CRM</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Right: The Unbeatable Math --}}
            <div class="lg:col-span-5 rounded-2xl border border-slate-200/80 bg-white p-6 text-center shadow-md">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">The Simple Math</span>
                <div class="mt-3">
                    <p class="text-xs text-slate-500">Average Remote Offer (Gross):</p>
                    <p class="text-2xl sm:text-3xl font-black text-[#3444da] mt-0.5">KES 250,000<span class="text-xs text-slate-400 font-normal">/mo</span></p>
                </div>

                <div class="my-3 flex items-center justify-center gap-2 text-slate-400 text-xs">
                    <span class="h-px w-10 bg-slate-200"></span>
                    <span>divided by your membership</span>
                    <span class="h-px w-10 bg-slate-200"></span>
                </div>

                <div>
                    <p class="text-xs text-slate-500">Pro Plan Cost:</p>
                    <p class="text-xl font-black text-slate-900 mt-0.5">KES 250 <span class="text-xs text-slate-500 font-normal">(less than lunch)</span></p>
                </div>

                <p class="mt-4 text-xs text-slate-600 leading-relaxed">
                    A single remote job interview pays for <strong>over 80 years</strong> of Pro membership (1,000x ROI).
                </p>

                <a
                    href="{{ url('/pricing') }}"
                    class="btn-pop mt-5 block w-full rounded-full bg-[#3444da] py-3 text-center text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-[#3444da]/20 hover:bg-[#2633bf] transition"
                >
                    Unlock Pro Early Access (KES 250) &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
