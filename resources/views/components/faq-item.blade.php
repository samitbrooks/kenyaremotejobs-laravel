@props(['question', 'answer'])

<div x-data="{ open: false }" class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm transition-shadow duration-300" :class="open ? 'shadow-md' : ''">
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        class="flex w-full items-center justify-between gap-4 text-left font-semibold"
    >
        {{ $question }}
        <span class="shrink-0 text-sunrise-500 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]" :class="open ? 'rotate-45' : ''">+</span>
    </button>
    <div class="grid transition-[grid-template-rows] duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]" :style="open ? 'grid-template-rows: 1fr' : 'grid-template-rows: 0fr'">
        <div class="overflow-hidden">
            <p class="mt-3 text-sm leading-relaxed text-foreground/70">{{ $answer }}</p>
        </div>
    </div>
</div>
