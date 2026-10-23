@props(['dark' => false, 'iconOnly' => false, 'class' => '', 'imgClass' => ''])

@php
    $logoSrc = $dark ? asset('images/logo-white.webp') : asset('images/logo.webp');
    $logoPng = $dark ? asset('images/logo-white.png') : asset('images/logo.png');
    $iconSrc = asset('images/logo-icon.png');
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center select-none '.$class]) }}>
    @if ($iconOnly)
        <img
            src="{{ $iconSrc }}"
            alt="{{ config('site.name') }}"
            class="h-8 w-8 object-contain transition-transform duration-200 group-hover:scale-105 {{ $imgClass }}"
            width="32"
            height="32"
            loading="eager"
        >
    @else
        <picture class="inline-flex items-center">
            <source srcset="{{ $logoSrc }}" type="image/webp">
            <img
                src="{{ $logoPng }}"
                alt="{{ config('site.name') }}"
                class="h-9 sm:h-10 w-auto max-w-[240px] sm:max-w-[280px] object-contain transition-opacity duration-200 group-hover:opacity-90 {{ $imgClass }}"
                width="280"
                height="74"
                loading="eager"
            >
        </picture>
    @endif
</div>
