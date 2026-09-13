<div
    x-data="{
        isOpen: false,
        isMinimized: false,
        inputMessage: '',
        loading: false,
        messages: [
            {
                role: 'assistant',
                content: 'Hello! 👋 I am **Ivy AI**, your free KenyaRemoteJobs career advisor.\n\nAsk me anything about landing US/UK/EU remote jobs, getting paid via Wise & M-Pesa, or claiming 0% US withholding tax with your KRA PIN.',
                actions: [
                    { label: '💸 Wise & M-Pesa Pay', query: 'How do I receive payments from foreign clients in Kenya via Wise and M-Pesa?' },
                    { label: '📑 W-8BEN Tax Form', query: 'How do I fill Form W-8BEN with my KRA PIN to avoid 30% US tax?' },
                    { label: '⚡ Pro 48h Early Access', query: 'Why should I get Pro Early Access and how does it give me full access?' },
                    { label: '🎯 Top Jobs Hiring Now', query: 'Which remote roles are actively hiring Kenyans right now?' }
                ]
            }
        ],
        toggle() {
            this.isOpen = !this.isOpen;
            this.isMinimized = false;
            if (this.isOpen) {
                this.scrollToBottom();
            }
        },
        toggleMinimize() {
            this.isMinimized = !this.isMinimized;
            if (!this.isMinimized) {
                this.scrollToBottom();
            }
        },
        async send(customText = null) {
            const text = (customText || this.inputMessage || '').trim();
            if (!text || this.loading) return;

            this.messages.push({
                role: 'user',
                content: text
            });
            this.inputMessage = '';
            this.loading = true;
            this.scrollToBottom();

            try {
                const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') 
                    || '{{ csrf_token() }}';

                const historyPayload = this.messages.slice(-5).map(m => ({
                    role: m.role,
                    content: m.content
                }));

                const res = await fetch('{{ url('/api/career-bot') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: text,
                        history: historyPayload
                    })
                });

                if (!res.ok) {
                    throw new Error('Server returned ' + res.status);
                }

                const data = await res.json();
                this.messages.push({
                    role: 'assistant',
                    content: data.reply || 'Here is what I found.',
                    actions: data.suggested_actions || []
                });
            } catch (err) {
                this.messages.push({
                    role: 'assistant',
                    content: 'Sorry, I had trouble reaching the network. Please check your connection and try again.',
                    actions: [
                        { label: 'Browse 800+ Remote Jobs', url: '{{ url('/jobs') }}' },
                        { label: 'Explore Pro Membership', url: '{{ url('/pricing') }}' }
                    ]
                });
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },
        formatMarkdown(text) {
            if (!text) return '';
            let out = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            out = out.replace(/\*\*(.*?)\*\*/g, '<strong class=\'font-bold text-foreground\'>$1</strong>');
            out = out.replace(/\*([^\*]+)\*/g, '<em class=\'italic\'>$1</em>');
            out = out.replace(/^[•\-\*]\s+(.*)$/gm, '<li class=\'ml-3 list-disc text-xs leading-relaxed text-foreground/85\'>$1</li>');
            out = out.replace(/\n/g, '<br>');
            return out;
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatContainer;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }"
    class="relative"
>
    {{-- Collapsed Floating Action Pill --}}
    <div x-show="!isOpen" x-cloak>
        <button
            type="button"
            @click="toggle"
            aria-label="Open Ivy AI Career Advisor"
            class="btn-pop group fixed bottom-5 right-5 z-50 flex items-center gap-2.5 rounded-full border-2 border-horizon-400/80 bg-gradient-to-r from-horizon-900 via-horizon-800 to-horizon-900 px-4 py-2.5 text-white shadow-xl transition hover:border-sunrise-400 hover:shadow-2xl hover:scale-105"
        >
            <span class="relative flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-tr from-sunrise-500 to-amber-400 text-white shadow-xs">
                <x-icon name="sparkle" class="h-4 w-4 animate-pulse" />
                <span class="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                </span>
            </span>
            <div class="text-left">
                <p class="text-xs font-bold leading-tight tracking-wide flex items-center gap-1.5">
                    Ask Ivy AI <span class="hidden sm:inline text-[10px] uppercase font-extrabold tracking-wider text-sunrise-300">🇰🇪 Free Advisor</span>
                </p>
                <p class="text-[10px] text-white/70 leading-none mt-0.5 hidden sm:block">
                    Remote Career, Wise &amp; Tax Guide
                </p>
            </div>
        </button>
    </div>

    {{-- Expanded Action Center & Chat Window --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col w-[390px] max-w-[calc(100vw-2rem)] rounded-3xl bg-white/95 backdrop-blur-lg border-2 border-horizon-200/90 shadow-2xl overflow-hidden"
        :class="isMinimized ? 'h-auto' : 'h-[550px] max-h-[82vh]'"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between bg-gradient-to-r from-horizon-900 via-horizon-800 to-horizon-900 px-4 py-3 text-white shadow-xs select-none">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-sunrise-500 to-amber-400 text-white shadow-xs">
                    <x-icon name="sparkle" class="h-4 w-4" />
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm font-bold truncate">Ivy AI Career Advisor</p>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-1.5 py-0.2 text-[9px] font-bold text-emerald-300 border border-emerald-400/30">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Online
                        </span>
                    </div>
                    <p class="text-[11px] text-white/70 truncate">Kenya Remote Jobs &amp; Contractor Assistant</p>
                </div>
            </div>
            <div class="flex items-center gap-1 text-white/80 shrink-0">
                <button
                    type="button"
                    @click="toggleMinimize"
                    :title="isMinimized ? 'Expand' : 'Minimize'"
                    class="p-1.5 hover:text-white hover:bg-white/10 rounded-lg transition"
                >
                    <span class="text-xs font-bold" x-text="isMinimized ? '▢' : '—'"></span>
                </button>
                <button
                    type="button"
                    @click="toggle"
                    title="Close"
                    class="p-1.5 hover:text-white hover:bg-white/10 rounded-lg transition text-base leading-none"
                >
                    &times;
                </button>
            </div>
        </div>

        <template x-if="!isMinimized">
            <div class="flex flex-col flex-1 min-h-0">
                {{-- Quick Actions Pill Bar --}}
                <div class="flex items-center gap-1.5 overflow-x-auto px-3 py-2 bg-horizon-50/60 border-b border-horizon-100 text-xs text-foreground/80 no-scrollbar">
                    <button
                        type="button"
                        @click="send('How do I receive payments from foreign clients in Kenya via Wise and M-Pesa?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        💸 Wise &amp; M-Pesa
                    </button>
                    <button
                        type="button"
                        @click="send('How do I fill Form W-8BEN with my KRA PIN to avoid 30% US tax?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        📑 W-8BEN Tax
                    </button>
                    <button
                        type="button"
                        @click="send('Why should I get Pro Early Access and how does it give me full access?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        ⚡ Pro Early Access
                    </button>
                    <button
                        type="button"
                        @click="send('Which remote roles are actively hiring Kenyans right now?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        🎯 Matched Roles
                    </button>
                </div>

                {{-- Chat Message Stream --}}
                <div
                    x-ref="chatContainer"
                    class="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs text-foreground/80"
                >
                    <template x-for="(msg, index) in messages" :key="index">
                        <div>
                            {{-- User Message --}}
                            <template x-if="msg.role === 'user'">
                                <div class="flex justify-end">
                                    <div class="max-w-[85%] rounded-2xl rounded-tr-xs bg-gradient-to-r from-sunrise-500 to-sunrise-600 px-3.5 py-2.5 text-white shadow-xs leading-relaxed text-xs" x-text="msg.content">
                                    </div>
                                </div>
                            </template>

                            {{-- Bot Message --}}
                            <template x-if="msg.role === 'assistant'">
                                <div class="flex gap-2 items-start">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-horizon-100 text-horizon-800 text-[10px] font-bold shadow-xs">
                                        AI
                                    </div>
                                    <div class="max-w-[88%] space-y-2">
                                        <div class="rounded-2xl rounded-tl-xs bg-horizon-50/70 border border-horizon-100/90 p-3 shadow-xs text-xs leading-relaxed text-foreground/90 space-y-1.5" x-html="formatMarkdown(msg.content)">
                                        </div>

                                        {{-- Actions / Suggested Questions --}}
                                        <template x-if="msg.actions && msg.actions.length > 0">
                                            <div class="flex flex-wrap gap-1.5 pt-1">
                                                <template x-for="(act, actIndex) in msg.actions" :key="actIndex">
                                                    <div>
                                                        <template x-if="act.url">
                                                            <a
                                                                :href="act.url"
                                                                class="btn-pop inline-flex items-center gap-1 rounded-full bg-white border border-horizon-300 px-2.5 py-1 text-[11px] font-bold text-horizon-800 shadow-xs hover:border-horizon-500 hover:bg-horizon-50 transition"
                                                            >
                                                                <span x-text="act.label"></span> &rarr;
                                                            </a>
                                                        </template>
                                                        <template x-if="act.query">
                                                            <button
                                                                type="button"
                                                                @click="send(act.query)"
                                                                class="btn-pop inline-flex items-center gap-1 rounded-full bg-white border border-horizon-300 px-2.5 py-1 text-[11px] font-bold text-horizon-800 shadow-xs hover:border-horizon-500 hover:bg-horizon-50 transition"
                                                            >
                                                                <span x-text="act.label"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Loading / Typing Indicator --}}
                    <div x-show="loading" x-cloak class="flex gap-2 items-center text-xs text-foreground/50">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-horizon-100 text-horizon-800 text-[10px] font-bold">
                            AI
                        </div>
                        <div class="flex items-center gap-1 rounded-full bg-horizon-50 border border-horizon-100 px-3 py-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>

                {{-- Input Bar --}}
                <div class="border-t border-horizon-100/80 bg-white p-2.5">
                    <form @submit.prevent="send()" class="flex items-center gap-2">
                        <input
                            type="text"
                            x-model="inputMessage"
                            placeholder="Ask Ivy about remote jobs, Wise, taxes, Pro..."
                            class="flex-1 rounded-xl border border-black/10 bg-horizon-50/40 px-3 py-2 text-xs focus:bg-white focus:border-horizon-500 focus:outline-none focus:ring-1 focus:ring-horizon-500"
                            autocomplete="off"
                        />
                        <button
                            type="submit"
                            :disabled="loading || !inputMessage.trim()"
                            class="btn-pop inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-horizon-800 text-white shadow-xs hover:bg-horizon-900 transition disabled:opacity-50"
                        >
                            <svg class="h-3.5 w-3.5 transform rotate-45 -translate-y-0.5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                    <div class="mt-1.5 flex items-center justify-between text-[10px] text-foreground/40 px-1">
                        <span>Ivy AI &bull; Free Career Assistant</span>
                        <a href="{{ url('/pricing') }}" class="text-horizon-700 font-semibold hover:underline">Get Pro Early Access &rarr;</a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
