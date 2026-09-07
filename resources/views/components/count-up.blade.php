@props(['value', 'duration' => 900])

<span
    x-data="{ display: 0 }"
    x-init="
        const target = {{ (int) $value }};
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            display = target;
        } else {
            const io = new IntersectionObserver(([entry]) => {
                if (!entry.isIntersecting) return;
                io.disconnect();
                const start = performance.now();
                const tick = (now) => {
                    const progress = Math.min((now - start) / {{ (int) $duration }}, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    display = Math.round(eased * target);
                    if (progress < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            }, { threshold: 0.5 });
            io.observe($el);
        }
    "
    x-text="display"
>0</span>
