<div
    x-data="{
        visible: false,
        timeElapsed: false,
        scrolled: false,
        init() {
            if (sessionStorage.getItem('krj_scroll_nudge_dismissed')) {
                return;
            }

            // Require at least 7 seconds of browsing
            setTimeout(() => {
                this.timeElapsed = true;
                this.checkTrigger();
            }, 7000);

            // Require at least 320px of scrolling down
            const onScroll = () => {
                if (window.scrollY > 320) {
                    this.scrolled = true;
                    this.checkTrigger();
                    window.removeEventListener('scroll', onScroll);
                }
            };
            window.addEventListener('scroll', onScroll, { passive: true });
        },
        checkTrigger() {
            if (this.timeElapsed && this.scrolled && !sessionStorage.getItem('krj_scroll_nudge_dismissed')) {
                this.visible = true;
            }
        },
        dismiss() {
            this.visible = false;
            sessionStorage.setItem('krj_scroll_nudge_dismissed', '1');
        }
    }"
    x-cloak
    x-show="visible"
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 translate-y-6 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
    class="fixed bottom-6 left-4 sm:left-6 z-40 max-w-sm rounded-3xl border border-slate-200/80 bg-white/95 backdrop-blur-md p-6 shadow-2xl"
    role="dialog"
    aria-label="Early Access Tip"
>
    <div class="relative">
        {{-- Close Button --}}
        <button
            type="button"
            @click="dismiss"
            aria-label="Dismiss message"
            class="absolute -top-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition text-base leading-none"
        >
            &times;
        </button>

        {{-- Badge --}}
        <div class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200 px-2.5 py-0.5 text-[11px] font-bold text-teal-800 mb-2.5">
            <x-icon name="sparkle" class="h-3 w-3 text-teal-600" />
            <span>48-Hour Recruiter Window</span>
        </div>

        {{-- Headline --}}
        <h3 class="text-sm font-bold text-slate-900 leading-snug pr-6">
            80% of Remote Hires Apply in the First 48 Hours
        </h3>

        {{-- Body --}}
        <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">
            International remote roles receive 500+ applications within 3 days. KenyaRemoteJobs Pro members get 48-hour early access to apply to all 800+ jobs before public release.
        </p>

        {{-- CTAs --}}
        <div class="mt-3.5 flex items-center gap-2">
            <a
                href="{{ url('/pricing') }}"
                @click="dismiss"
                class="btn-pop inline-flex items-center justify-center gap-1.5 rounded-full bg-teal-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700 transition"
            >
                Get Full Early Access &rarr;
            </a>
            <button
                type="button"
                @click="dismiss"
                class="rounded-full px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition"
            >
                Maybe later
            </button>
        </div>
    </div>
</div>
