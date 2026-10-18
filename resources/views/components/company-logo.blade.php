@props(['company' => '', 'size' => 48])

{{--
    Harmonized graphic illustration vector art across all cards.
    Deliberately identical across listings rather than distracting per-company initials
    or random rainbow gradients, ensuring visual focus remains on the job title,
    salary, and verified hiring details.
--}}
<div
    {{ $attributes->merge(['class' => 'relative flex shrink-0 items-center justify-center rounded-xl border border-[#e6e8f2] bg-[#f8f9fd] shadow-2xs group-hover:border-[#3444da] group-hover:bg-[#eef0fe] transition-all duration-200 select-none overflow-hidden']) }}
    style="width: {{ $size }}px; height: {{ $size }}px;"
    aria-label="{{ $company ? $company : 'Verified Company' }}"
    role="img"
>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.6"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="text-[#3444da] transition-transform duration-200 group-hover:scale-105 shrink-0"
        style="width: {{ round($size * 0.54) }}px; height: {{ round($size * 0.54) }}px;"
        aria-hidden="true"
    >
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
    </svg>
</div>

