@props(['compact' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] font-bold text-[#3444da] '.($compact ? 'px-2 py-0.5 text-[11px]' : 'px-3 py-1 text-xs')]) }}>
    <span class="relative flex h-2 w-2">
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#3444da] opacity-75"></span>
        <span class="relative inline-flex h-2 w-2 rounded-full bg-[#3444da]"></span>
    </span>
    <x-icon.kenya-flag class="h-3.5 w-3.5" /> Kenya-Friendly Match
</span>
