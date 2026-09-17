@props(['company', 'size' => 48])

{{--
    Deliberately the same icon and gradient on every card, not a per-company
    letter/color hash — a page of differently-colored, differently-lettered
    circles read as visual noise rather than as company identity (there's no
    real per-company logo system here to justify the variation).
--}}
<div
    {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/80 bg-gradient-to-br from-slate-50 via-white to-teal-50/60 text-teal-700 shadow-2xs']) }}
    style="width: {{ $size }}px; height: {{ $size }}px"
    aria-hidden="true"
>
    <x-icon name="briefcase" class="text-teal-600" style="width: {{ $size * 0.48 }}px; height: {{ $size * 0.48 }}px" />
</div>
