<div x-data="{ open: false }">
    {{-- Floating Floating WhatsApp Trigger Button --}}
    <button
        type="button"
        @click="open = true"
        aria-label="WhatsApp Job Alerts"
        class="fixed bottom-6 right-6 z-40 flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-2.5 text-xs font-extrabold text-white shadow-xl shadow-emerald-600/30 hover:bg-[#20ba59] hover:scale-105 active:scale-95 transition-all duration-200 group"
    >
        <span class="relative flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
        </span>
        <span class="text-base leading-none">💬</span>
        <span class="hidden sm:inline">WhatsApp Job Alerts</span>
    </button>

    {{-- Interactive Modal Backdrop & Content --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
    >
        <div
            @click.away="open = false"
            class="relative w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 text-white shadow-2xl overflow-hidden"
        >
            {{-- Close Button --}}
            <button
                type="button"
                @click="open = false"
                class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl p-1"
            >
                &times;
            </button>

            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#25D366] text-white text-2xl shadow-lg shadow-emerald-500/30">
                    💬
                </span>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Exclusive Broadcast</span>
                    <h3 class="text-lg font-black text-white">VIP WhatsApp Job Alerts</h3>
                </div>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed mb-5">
                Join <strong>4,500+ Kenyan job seekers</strong> receiving real-time alerts the moment verified worldwide remote roles open to East Africa Time (UTC+3) are published.
            </p>

            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4 space-y-2 text-xs text-slate-300 mb-6">
                <div class="flex items-center gap-2 text-emerald-400 font-semibold">
                    <span>✓</span> Instant notification before public applications flood in
                </div>
                <div class="flex items-center gap-2 text-emerald-400 font-semibold">
                    <span>✓</span> Verified compensation in USD &amp; estimated KES take-home
                </div>
                <div class="flex items-center gap-2 text-emerald-400 font-semibold">
                    <span>✓</span> Zero spam &bull; only Kenya-friendly vetted remote roles
                </div>
            </div>

            <a
                href="https://wa.me/{{ preg_replace('/[^\d]/', '', config('whatsapp.default_channel_phone', '254700000000')) }}?text=Hello%20KenyaRemoteJobs%20Team!%20I%20would%20like%20to%20receive%20instant%20WhatsApp%20remote%20job%20alerts."
                target="_blank"
                rel="noopener"
                class="btn-pop block w-full rounded-2xl bg-[#25D366] py-3.5 text-center text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-emerald-600/30 hover:bg-[#20ba59] transition"
            >
                💬 Join Instant WhatsApp Alerts &rarr;
            </a>

            <p class="mt-3 text-center text-[10px] text-slate-500">
                100% Free &bull; Mute or leave anytime &bull; Powered by Meta Cloud API
            </p>
        </div>
    </div>
</div>
