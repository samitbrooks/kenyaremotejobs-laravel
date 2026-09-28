<div class="rounded-3xl border-2 border-amber-400/80 bg-gradient-to-br from-slate-950 via-slate-900 to-[#1e1526] p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden">
    {{-- Glow background --}}
    <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-amber-500/15 blur-3xl"></div>

    <div class="relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 border border-amber-400/50 px-3.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-amber-300">
                <span>👑</span> Transparent Membership ROI
            </span>
            <span class="text-xs text-slate-400 font-medium">
                M-Pesa Instant Activation &bull; Zero Long-term Contract
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7">
                <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Why Pro Pays For Itself On Day One
                </h3>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                    Most candidates lose out on high-paying USD roles simply because of delayed applications and generic CV formatting. Pro membership removes every barrier between you and global recruiters.
                </p>

                {{-- Side-by-Side Comparison --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Free Box --}}
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/80 p-4">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Standard Free</span>
                        <p class="text-base font-extrabold text-white mt-1">KES 0</p>
                        <ul class="mt-3 space-y-2 text-slate-400">
                            <li class="flex items-center gap-2"><span>&bull;</span> 48-hour wait on fresh roles</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Standard application queue</li>
                            <li class="flex items-center gap-2"><span>&bull;</span> Basic job board browsing</li>
                        </ul>
                    </div>

                    {{-- Pro Box --}}
                    <div class="rounded-2xl border border-amber-400/60 bg-gradient-to-b from-amber-950/30 to-slate-900 p-4 relative">
                        <span class="rounded-full bg-amber-400 text-slate-950 font-black text-[9px] px-2 py-0.5 uppercase tracking-wider absolute top-3 right-3">836x ROI</span>
                        <span class="text-amber-300 font-bold uppercase tracking-wider text-[10px]">VIP Pro Early Access</span>
                        <p class="text-base font-extrabold text-white mt-1">KES 299 <span class="text-xs text-slate-400 font-normal">/ month</span></p>
                        <ul class="mt-3 space-y-2 text-slate-200">
                            <li class="flex items-center gap-2 text-amber-300 font-semibold"><span>&check;</span> Instant 0-second apply access</li>
                            <li class="flex items-center gap-2 text-amber-300 font-semibold"><span>&check;</span> 1-on-1 CV review to beat ATS</li>
                            <li class="flex items-center gap-2 text-amber-300 font-semibold"><span>&check;</span> Unredacted direct recruiter links</li>
                            <li class="flex items-center gap-2 text-amber-300 font-semibold"><span>&check;</span> WhatsApp VIP Concierge desk</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Right: The Unbeatable Math --}}
            <div class="lg:col-span-5 rounded-2xl border border-slate-700 bg-slate-900/90 p-6 text-center shadow-xl">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">The Simple Math</span>
                <div class="mt-3">
                    <p class="text-xs text-slate-400">Average Remote Offer (Gross):</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-0.5">KES 350,000<span class="text-xs text-slate-400 font-normal">/mo</span></p>
                </div>

                <div class="my-3 flex items-center justify-center gap-2 text-slate-500 text-xs">
                    <span class="h-px w-10 bg-slate-800"></span>
                    <span>divided by your membership</span>
                    <span class="h-px w-10 bg-slate-800"></span>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Pro Plan Cost:</p>
                    <p class="text-xl font-black text-white mt-0.5">KES 299 <span class="text-xs text-slate-400 font-normal">(price of a coffee)</span></p>
                </div>

                <p class="mt-4 text-xs text-slate-300 leading-relaxed">
                    A single remote job interview pays for <strong>over 10 years</strong> of Pro membership.
                </p>

                <a
                    href="{{ url('/pricing') }}"
                    class="btn-pop mt-5 block w-full rounded-xl bg-gradient-to-r from-amber-400 to-[#ff3131] py-3 text-center text-xs font-black uppercase tracking-wider text-slate-950 shadow-lg shadow-amber-400/20 hover:from-amber-300 hover:to-[#ff4545] transition"
                >
                    Unlock Pro Early Access (KES 299) &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
