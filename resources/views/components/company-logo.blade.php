@props(['company', 'size' => 48])

@php
    $name = trim((string) $company);
    $initials = '';
    if (!empty($name)) {
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr($name, 0, 2));
        }
    }
    if (empty($initials)) {
        $initials = 'KR';
    }

    // Deterministic curated brand palettes based on company name hash
    $palettes = [
        ['bg' => 'from-slate-900 to-slate-800', 'text' => 'text-white', 'border' => 'border-slate-800/80'],
        ['bg' => 'from-teal-600 to-emerald-700', 'text' => 'text-white', 'border' => 'border-teal-700/80'],
        ['bg' => 'from-indigo-600 to-blue-700', 'text' => 'text-white', 'border' => 'border-indigo-700/80'],
        ['bg' => 'from-violet-600 to-purple-800', 'text' => 'text-white', 'border' => 'border-violet-700/80'],
        ['bg' => 'from-rose-600 to-amber-700', 'text' => 'text-white', 'border' => 'border-rose-700/80'],
        ['bg' => 'from-cyan-700 to-teal-800', 'text' => 'text-white', 'border' => 'border-cyan-800/80'],
    ];
    $index = abs(crc32($name)) % count($palettes);
    $palette = $palettes[$index];
@endphp

<div
    {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {$palette['bg']} {$palette['text']} font-black tracking-tight shadow-sm border {$palette['border']} transition-transform group-hover:scale-105 duration-200 select-none"]) }}
    style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ $size * 0.36 }}px;"
    aria-label="{{ $company }}"
>
    {{ $initials }}
</div>
