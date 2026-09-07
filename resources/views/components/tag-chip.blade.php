@props(['label', 'index' => 0])

@php
    $styles = ['bg-sunrise-100 text-sunrise-800', 'bg-horizon-100 text-horizon-800'];
    $style = $styles[$index % count($styles)];
@endphp

<span {{ $attributes->merge(['class' => "rounded-full px-2.5 py-1 text-xs font-medium {$style}"]) }}>{{ $label }}</span>
