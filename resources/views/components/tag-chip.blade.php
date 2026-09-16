@props(['label', 'index' => 0])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 border border-slate-200/60']) }}>{{ $label }}</span>
