@once
<script data-kazi-assistant>
    window.kaziAssistant = function (config) {
        return {
            isOpen: false,
            isMinimized: false,
            inputMessage: '',
            loading: false,
            error: '',
            messages: [{
                role: 'assistant',
                greeting: true,
                content: 'Hi, I’m Kazi, your free career assistant. Tell me what you can do or where you are stuck, and we’ll work out a useful next step.',
                actions: [
                    { label: 'Find suitable online work', query: 'How can I find online work that fits my skills in Kenya?' },
                    { label: 'Improve my CV', query: 'How can I improve my CV for a remote role?' },
                    { label: 'Prepare for an interview', query: 'How should I prepare for my first remote job interview?' }
                ]
            }],
            open() {
                this.isOpen = true;
                this.isMinimized = false;
                this.focusInput();
                this.scrollToBottom();
            },
            minimize() {
                this.isMinimized = true;
            },
            close() {
                this.isOpen = false;
                this.isMinimized = false;
                this.$nextTick(() => this.$refs.launcher?.focus());
            },
            focusInput() {
                this.$nextTick(() => this.$refs.input?.focus());
            },
            async send(customText = null) {
                const text = Array.from((customText || this.inputMessage || '').trim()).slice(0, 500).join('');
                if (!text || this.loading) {
                    return;
                }
                const history = this.messages.filter(message => !message.greeting)
                    .slice(-4).map(message => ({
                        role: message.role,
                        content: Array.from(message.content).slice(0, 500).join('')
                    }));
                this.messages.push({ role: 'user', content: text });
                this.inputMessage = '';
                this.error = '';
                this.loading = true;
                this.scrollToBottom();
                try {
                    const response = await fetch(config.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken
                        },
                        body: JSON.stringify({ message: text, history })
                    });
                    if (!response.ok) {
                        this.error = response.status === 429
                            ? 'Please wait a moment before asking another question. Your draft is still here.'
                            : 'Couldn’t get a reply. Check your connection and try sending again.';
                        return;
                    }
                    const data = await response.json();
                    this.messages.push({
                        role: 'assistant',
                        content: data.reply || 'Could you tell me a little more about what you need?',
                        actions: Array.isArray(data.suggested_actions) ? data.suggested_actions : []
                    });
                } catch (error) {
                    this.error = 'Couldn’t connect. Your message is saved below — try again when you’re ready.';
                } finally {
                    this.loading = false;
                    if (this.error && !this.inputMessage) {
                        this.inputMessage = text;
                    }
                    this.scrollToBottom();
                }
            },
            safeActionUrl(value) {
                try {
                    const url = new URL(value, window.location.origin);
                    return url.origin === window.location.origin && ['http:', 'https:'].includes(url.protocol)
                        ? url.pathname + url.search + url.hash : null;
                } catch (error) {
                    return null;
                }
            },
            formatMarkdown(text) {
                const escaped = String(text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                return escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*([^*]+)\*/g, '<em>$1</em>').replace(/\n/g, '<br>');
            },
            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.chatContainer;
                    if (container && !this.isMinimized) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            }
        };
    };
</script>
@endonce

<div x-data="kaziAssistant({ endpoint: @js(url('/api/career-bot')), csrfToken: @js(csrf_token()) })" @keydown.escape="if (isOpen) { close(); $event.stopPropagation(); }">
    <button x-ref="launcher" x-show="!isOpen" type="button" @click="open()" :aria-expanded="isOpen" aria-controls="kazi-panel" aria-label="Open Kazi, your free career assistant" class="fixed bottom-4 right-4 z-50 flex items-center gap-3 rounded-xl border border-[#c7ccfb] bg-white px-4 py-3 text-left shadow-lg hover:border-[#3444da] sm:bottom-6 sm:right-6">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#eef0fe] text-sm font-semibold text-[#3444da]" aria-hidden="true">K</span>
        <span>
            <span class="block text-sm font-semibold text-slate-900">Ask Kazi</span>
            <span class="block text-xs text-slate-500">Free career help</span>
        </span>
        <x-icon name="chat" class="ml-1 h-4 w-4 text-[#3444da]" />
    </button>

    <section id="kazi-panel" x-show="isOpen" x-cloak x-transition.opacity.duration.150ms role="region" aria-labelledby="kazi-title" class="fixed bottom-4 right-4 z-50 flex w-[calc(100vw-2rem)] max-w-sm flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl sm:bottom-6 sm:right-6 sm:w-96" :class="isMinimized ? 'h-auto' : 'h-[min(38rem,calc(100dvh-2rem))]'">
        <header class="flex shrink-0 items-center justify-between gap-2 bg-[#3444da] px-4 py-3 text-white">
            <button type="button" @click="open()" :aria-expanded="!isMinimized" aria-controls="kazi-conversation" class="flex min-w-0 items-center gap-2 text-left" :aria-label="isMinimized ? 'Restore Kazi conversation' : 'Focus Kazi message box'">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/25 text-sm font-semibold" aria-hidden="true">K</span>
                <span class="min-w-0">
                    <span id="kazi-title" class="block text-sm font-semibold">Kazi</span>
                    <span class="block text-[11px] text-white/80" x-text="isMinimized && loading ? 'Working on your reply…' : 'Your free career assistant'"></span>
                </span>
            </button>
            <div class="flex shrink-0 items-center gap-1">
                <button type="button" @click="isMinimized ? open() : minimize()" :aria-label="isMinimized ? 'Restore Kazi conversation' : 'Minimise Kazi conversation'" :title="isMinimized ? 'Restore conversation' : 'Minimise conversation'" class="rounded-md px-2 py-2 text-xs font-medium hover:bg-white/15 focus-visible:outline-white" x-text="isMinimized ? 'Restore' : 'Minimise'"></button>
                <button type="button" @click="close()" aria-label="Close Kazi" title="Close assistant" class="flex h-8 w-8 items-center justify-center rounded-md text-xl hover:bg-white/15 focus-visible:outline-white">&times;</button>
            </div>
        </header>

        <div id="kazi-conversation" x-show="!isMinimized" class="flex min-h-0 flex-1 flex-col">
            <div class="shrink-0 border-b border-slate-200 bg-[#eef0fe] px-4 py-2.5 text-xs text-slate-700">
                <strong class="font-semibold text-[#3444da]">Free — no membership needed.</strong> Ask about jobs, CVs, or your next application.
            </div>
            <div x-ref="chatContainer" role="log" aria-label="Conversation with Kazi" aria-live="polite" aria-relevant="additions" class="min-h-0 flex-1 space-y-5 overflow-y-auto overscroll-contain px-4 py-5">
                <template x-for="(message, index) in messages" :key="index">
                    <div :class="message.role === 'user' ? 'ml-6' : 'mr-2'">
                        <p class="mb-1.5 text-[11px] font-medium text-slate-500" x-text="message.role === 'user' ? 'You' : 'Kazi · AI assistant'"></p>
                        <template x-if="message.role === 'user'">
                            <div class="[overflow-wrap:anywhere] rounded-lg bg-[#eef0fe] px-3 py-2.5 text-sm leading-relaxed text-slate-800" x-text="message.content"></div>
                        </template>
                        <template x-if="message.role === 'assistant'">
                            <div class="[overflow-wrap:anywhere] text-sm leading-relaxed text-slate-700" x-html="formatMarkdown(message.content)"></div>
                        </template>
                        <template x-if="message.actions && message.actions.length">
                            <div class="mt-3 flex flex-wrap gap-2">
                                <template x-for="(action, actionIndex) in message.actions" :key="actionIndex">
                                    <span>
                                        <template x-if="action.url && safeActionUrl(action.url)">
                                            <a :href="safeActionUrl(action.url)" class="inline-block rounded-md border border-slate-200 px-2.5 py-2 text-xs font-medium text-[#3444da] hover:bg-[#eef0fe]" x-text="action.label"></a>
                                        </template>
                                        <template x-if="action.query">
                                            <button type="button" @click="send(action.query)" :disabled="loading" class="rounded-md border border-slate-200 px-2.5 py-2 text-left text-xs font-medium text-slate-700 hover:border-[#c7ccfb] hover:bg-slate-50 disabled:opacity-50" x-text="action.label"></button>
                                        </template>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
                <p x-show="loading" role="status" class="text-sm text-slate-500">Kazi is preparing a reply<span aria-hidden="true">…</span></p>
            </div>

            <div class="shrink-0 border-t border-slate-200 bg-white p-3">
                <p x-show="error" x-cloak x-text="error" role="alert" class="mb-2 text-xs leading-relaxed text-rose-700"></p>
                <form @submit.prevent="send()" class="flex items-end gap-2">
                    <div class="min-w-0 flex-1">
                        <label for="kazi-message" class="sr-only">Your question for Kazi</label>
                        <textarea id="kazi-message" x-ref="input" x-model="inputMessage" @keydown.enter="if (!$event.shiftKey && !$event.isComposing) { $event.preventDefault(); send(); }" maxlength="500" rows="2" placeholder="What would you like help with?" class="block w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#3444da]" aria-describedby="kazi-note"></textarea>
                    </div>
                    <button type="submit" :disabled="loading || !inputMessage.trim()" class="inline-flex h-10 shrink-0 items-center justify-center rounded-lg bg-[#3444da] px-3 text-sm font-semibold text-white hover:bg-[#2633bf] disabled:cursor-not-allowed disabled:opacity-50">Send</button>
                </form>
                <p id="kazi-note" class="mt-2 text-[11px] leading-relaxed text-slate-500">AI guidance can be mistaken. Check important details before acting.</p>
                <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2 text-xs">
                    <span class="font-medium text-[#3444da]">Always free to chat</span>
                    <a href="{{ url('/match') }}" class="text-slate-500 hover:text-[#3444da] hover:underline">Find matching roles &rarr;</a>
                </div>
            </div>
        </div>
    </section>
</div>
