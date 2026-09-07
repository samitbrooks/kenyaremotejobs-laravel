@props(['delay' => 0])

<div
    {{ $attributes->merge(['class' => 'reveal']) }}
    x-data="{ visible: false }"
    x-init="
        const io = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) { visible = true; io.disconnect(); }
        }, { threshold: 0, rootMargin: '0px 0px -40px 0px' });
        io.observe($el);
    "
    :class="visible ? 'is-visible' : ''"
    :style="visible ? 'animation-delay: {{ (int) $delay }}ms' : ''"
>
    {{ $slot }}
</div>
