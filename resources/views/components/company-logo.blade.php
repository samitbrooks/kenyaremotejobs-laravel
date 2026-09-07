@props(['company', 'size' => 48])

@php
    $gradients = ['from-sunrise-400 to-sunrise-600', 'from-horizon-400 to-horizon-600', 'from-sunrise-400 to-horizon-500'];
    $initial = mb_strtoupper(mb_substr(trim($company), 0, 1)) ?: '?';
    $gradient = $gradients[mb_strlen($company) % count($gradients)];
@endphp

<div
    {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {$gradient} font-bold text-white"]) }}
    style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ $size * 0.4 }}px"
    aria-hidden="true"
>
    {{ $initial }}
</div>
