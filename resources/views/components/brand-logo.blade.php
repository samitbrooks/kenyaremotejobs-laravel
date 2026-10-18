@props(['dark' => false, 'iconOnly' => false, 'class' => ''])

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
            class="h-8 w-8 object-contain transition-transform duration-200 group-hover:scale-105"
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
                class="h-8 sm:h-9 w-auto max-w-[210px] sm:max-w-[250px] object-contain transition-opacity duration-200 group-hover:opacity-90"
                width="240"
                height="64"
                loading="eager"
            >
        </picture>
    @endif
</div>
