@props(['company', 'size' => 48])

{{--
    Deliberately the same icon and gradient on every card, not a per-company
    letter/color hash — a page of differently-colored, differently-lettered
    circles read as visual noise rather than as company identity (there's no
    real per-company logo system here to justify the variation).
--}}
<div
    {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-xl bg-slate-100 border border-slate-200/80 text-teal-700 shadow-xs']) }}
    style="width: {{ $size }}px; height: {{ $size }}px"
    aria-hidden="true"
>
    <x-icon name="briefcase" class="text-teal-700" style="width: {{ $size * 0.5 }}px; height: {{ $size * 0.5 }}px" />
</div>
