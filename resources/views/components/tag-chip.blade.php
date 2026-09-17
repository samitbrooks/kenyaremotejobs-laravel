@props(['label', 'index' => 0])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-slate-50 px-3 py-0.5 text-xs font-medium text-slate-600 border border-slate-200/80 hover:border-teal-300 hover:text-teal-700 transition']) }}>{{ $label }}</span>
