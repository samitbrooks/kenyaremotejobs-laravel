<?php

use App\Services\KenyaCareerBotService;
use Livewire\Component;

new class extends Component
{
    public bool $isOpen = false;

    public bool $isMinimized = false;

    public string $inputMessage = '';

    public bool $loading = false;

    /** @var array<int, array{role: string, content: string, actions?: array<int, array{label: string, url?: string, query?: string}>}> */
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => "Hello! 👋 I am **Kariuki AI**, your free KenyaRemoteJobs career copilot.\n\nAsk me anything about landing US/UK/EU remote jobs, getting paid via Wise & M-Pesa, or claiming 0% US withholding tax.",
                'actions' => [
                    ['label' => '💸 Wise & M-Pesa Pay', 'query' => 'How do I receive payments from foreign clients in Kenya?'],
                    ['label' => '📑 W-8BEN Tax Form', 'query' => 'How do I fill Form W-8BEN with my KRA PIN?'],
                    ['label' => '⚡ Pro 48h Early Access', 'query' => 'How does Pro Early Access give me an advantage?'],
                    ['label' => '🎯 Top Jobs Hiring Now', 'query' => 'Which remote roles are actively hiring Kenyans right now?'],
                ],
            ],
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
        $this->isMinimized = false;
        if ($this->isOpen) {
            $this->dispatch('chat-scroll-bottom');
        }
    }

    public function toggleMinimize(): void
    {
        $this->isMinimized = ! $this->isMinimized;
        if (! $this->isMinimized) {
            $this->dispatch('chat-scroll-bottom');
        }
    }

    public function send(?string $customText = null): void
    {
        $text = trim($customText ?? $this->inputMessage);
        if ($text === '') {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => $text,
        ];

        $this->inputMessage = '';
        $this->loading = true;

        $service = app(KenyaCareerBotService::class);
        $result = $service->ask($text, $this->messages);

        $actions = [];
        if (! empty($result['suggested_actions'])) {
            foreach ($result['suggested_actions'] as $act) {
                $actions[] = [
                    'label' => $act['label'],
                    'url' => $act['url'],
                ];
            }
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $result['reply'],
            'actions' => $actions,
        ];

        $this->loading = false;
        $this->dispatch('chat-scroll-bottom');
    }

    public function formatMarkdown(string $text): string
    {
        // Convert bold
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="font-bold text-foreground">$1</strong>', e($text));
        // Convert italics
        $text = preg_replace('/\*([^\*]+)\*/', '<em class="italic">$1</em>', $text);
        // Convert bullets
        $text = preg_replace('/^[•\-\*]\s+(.*)$/m', '<li class="ml-3 list-disc text-xs leading-relaxed text-foreground/80">$1</li>', $text);
        // Convert newlines to breaks
        $text = nl2br($text);

        return $text;
    }
}; ?>

<div>
    {{-- Collapsed Floating Action Pill --}}
    @if (! $isOpen)
        <button
            type="button"
            wire:click="toggle"
            aria-label="Open Remote Career Copilot"
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
                    Ask Kariuki AI <span class="hidden sm:inline text-[10px] uppercase font-extrabold tracking-wider text-sunrise-300">🇰🇪 Free Copilot</span>
                </p>
                <p class="text-[10px] text-white/70 leading-none mt-0.5 hidden sm:block">
                    Remote Career, Wise & Tax Guide
                </p>
            </div>
        </button>
    @else
        {{-- Expanded Action Center & Chat Window --}}
        <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col w-[390px] max-w-[calc(100vw-2rem)] {{ $isMinimized ? 'h-auto' : 'h-[550px] max-h-[82vh]' }} rounded-3xl bg-white/95 backdrop-blur-lg border-2 border-horizon-200/90 shadow-2xl overflow-hidden transition-all duration-300">
            
            {{-- Header --}}
            <div class="flex items-center justify-between bg-gradient-to-r from-horizon-900 via-horizon-800 to-horizon-900 px-4 py-3 text-white shadow-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-sunrise-500 to-amber-400 text-white shadow-xs">
                        <x-icon name="sparkle" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold truncate">Kariuki AI Copilot</p>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-1.5 py-0.2 text-[9px] font-bold text-emerald-300 border border-emerald-400/30">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Online
                            </span>
                        </div>
                        <p class="text-[11px] text-white/70 truncate">Kenya Remote Jobs & Contractor Assistant</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 text-white/80 shrink-0">
                    <button
                        type="button"
                        wire:click="toggleMinimize"
                        title="{{ $isMinimized ? 'Expand' : 'Minimize' }}"
                        class="p-1.5 hover:text-white hover:bg-white/10 rounded-lg transition"
                    >
                        <span class="text-xs font-bold">{{ $isMinimized ? '▢' : '—' }}</span>
                    </button>
                    <button
                        type="button"
                        wire:click="toggle"
                        title="Close"
                        class="p-1.5 hover:text-white hover:bg-white/10 rounded-lg transition text-base leading-none"
                    >
                        &times;
                    </button>
                </div>
            </div>

            @if (! $isMinimized)
                {{-- Quick Actions Pill Bar --}}
                <div class="flex items-center gap-1.5 overflow-x-auto px-3 py-2 bg-horizon-50/60 border-b border-horizon-100 text-xs text-foreground/80 no-scrollbar">
                    <button
                        type="button"
                        wire:click="send('How do I receive payments from foreign clients in Kenya via Wise and M-Pesa?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        💸 Wise & M-Pesa
                    </button>
                    <button
                        type="button"
                        wire:click="send('How do I fill IRS Form W-8BEN with my Kenyan KRA PIN?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        📑 W-8BEN Tax
                    </button>
                    <button
                        type="button"
                        wire:click="send('Why should I get Pro Early Access and how does it give me full access?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        ⚡ Pro Early Access
                    </button>
                    <button
                        type="button"
                        wire:click="send('Which remote jobs are hiring Kenyan candidates right now?')"
                        class="shrink-0 rounded-full border border-horizon-200 bg-white px-2.5 py-1 text-[11px] font-medium hover:border-horizon-400 hover:bg-horizon-50 transition"
                    >
                        🎯 Matched Roles
                    </button>
                </div>

                {{-- Chat Message Stream --}}
                <div
                    id="action-center-messages"
                    class="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs text-foreground/80"
                    x-data
                    x-on:chat-scroll-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
                >
                    @foreach ($messages as $msg)
                        @if ($msg['role'] === 'user')
                            <div class="flex justify-end">
                                <div class="max-w-[85%] rounded-2xl rounded-tr-xs bg-gradient-to-r from-sunrise-500 to-sunrise-600 px-3.5 py-2.5 text-white shadow-xs leading-relaxed text-xs">
                                    {{ $msg['content'] }}
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2 items-start">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-horizon-100 text-horizon-800 text-[10px] font-bold shadow-xs">
                                    AI
                                </div>
                                <div class="max-w-[88%] space-y-2">
                                    <div class="rounded-2xl rounded-tl-xs bg-horizon-50/70 border border-horizon-100/90 p-3 shadow-xs text-xs leading-relaxed text-foreground/90 space-y-1.5">
                                        {!! $this->formatMarkdown($msg['content']) !!}
                                    </div>

                                    @if (! empty($msg['actions']))
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            @foreach ($msg['actions'] as $action)
                                                @if (! empty($action['url']))
                                                    <a
                                                        href="{{ url($action['url']) }}"
                                                        class="btn-pop inline-flex items-center gap-1 rounded-full bg-white border border-horizon-300 px-2.5 py-1 text-[11px] font-bold text-horizon-800 shadow-xs hover:border-horizon-500 hover:bg-horizon-50 transition"
                                                    >
                                                        {{ $action['label'] }} &rarr;
                                                    </a>
                                                @elseif (! empty($action['query']))
                                                    <button
                                                        type="button"
                                                        wire:click="send('{{ addslashes($action['query']) }}')"
                                                        class="btn-pop inline-flex items-center gap-1 rounded-full bg-white border border-horizon-300 px-2.5 py-1 text-[11px] font-bold text-horizon-800 shadow-xs hover:border-horizon-500 hover:bg-horizon-50 transition"
                                                    >
                                                        {{ $action['label'] }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if ($loading)
                        <div class="flex gap-2 items-center text-xs text-foreground/50">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-horizon-100 text-horizon-800 text-[10px] font-bold">
                                AI
                            </div>
                            <div class="flex items-center gap-1 rounded-full bg-horizon-50 border border-horizon-100 px-3 py-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce" style="animation-delay: 0.15s"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-horizon-600 animate-bounce" style="animation-delay: 0.3s"></span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Input Bar --}}
                <div class="border-t border-horizon-100/80 bg-white p-2.5">
                    <form wire:submit.prevent="send" class="flex items-center gap-2">
                        <input
                            type="text"
                            wire:model="inputMessage"
                            placeholder="Ask about remote jobs, Wise, taxes, Pro..."
                            class="flex-1 rounded-xl border border-black/10 bg-horizon-50/40 px-3 py-2 text-xs focus:bg-white focus:border-horizon-500 focus:outline-none focus:ring-1 focus:ring-horizon-500"
                            autocomplete="off"
                        />
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="btn-pop inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-horizon-800 text-white shadow-xs hover:bg-horizon-900 transition disabled:opacity-50"
                        >
                            <svg class="h-3.5 w-3.5 transform rotate-45 -translate-y-0.5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                    <div class="mt-1.5 flex items-center justify-between text-[10px] text-foreground/40 px-1">
                        <span>Free AI Career Assistant</span>
                        <a href="{{ url('/pricing') }}" class="text-horizon-700 font-semibold hover:underline">Get Pro Early Access &rarr;</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
