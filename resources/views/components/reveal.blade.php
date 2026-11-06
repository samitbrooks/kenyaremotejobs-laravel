@props(['delay' => 0])

<div {{ $attributes->merge(['class' => 'reveal']) }}>
    {{ $slot }}
</div>
