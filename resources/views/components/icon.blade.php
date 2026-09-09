@props(['name'])

<svg {{ $attributes->merge([
        'class' => 'h-4 w-4 shrink-0',
        'viewBox' => '0 0 20 20',
        'fill' => 'none',
        'stroke' => 'currentColor',
        'stroke-width' => '1.5',
        'stroke-linecap' => 'round',
        'stroke-linejoin' => 'round',
        'aria-hidden' => 'true',
    ]) }}>
    @switch($name)
        @case('lock')
            <rect x="4.5" y="9" width="11" height="8" rx="1.5" />
            <path d="M6.5 9V6.5a3.5 3.5 0 0 1 7 0V9" />
            @break

        @case('unlock')
            <rect x="4.5" y="9" width="11" height="8" rx="1.5" />
            <path d="M6.5 9V6.5a3.5 3.5 0 0 1 6.5-1.8" />
            @break

        @case('clock')
            <circle cx="10" cy="10" r="7" />
            <path d="M10 6v4l2.8 2" />
            @break

        @case('sparkle')
            <path d="M10 3.5c.4 2.3 1.2 3.6 3.5 4-2.3.4-3.1 1.7-3.5 4-.4-2.3-1.2-3.6-3.5-4 2.3-.4 3.1-1.7 3.5-4Z" />
            <path d="M15.5 13c.2 1.1.6 1.7 1.7 1.9-1.1.2-1.5.8-1.7 1.9-.2-1.1-.6-1.7-1.7-1.9 1.1-.2 1.5-.8 1.7-1.9Z" />
            @break

        @case('check')
            <path d="M4.5 10.5 8 14l7.5-8" />
            @break

        @case('coin')
            <circle cx="10" cy="10" r="7" />
            <path d="M10 6.5v7M8 8h2.7a1.6 1.6 0 0 1 0 3.2H8.3a1.6 1.6 0 0 0 0 3.2H12" />
            @break

        @case('announce')
            <path d="M3.5 8.5v3l2 .4v2.6a1 1 0 0 0 1 1h.5" />
            <path d="M5.5 8.9 13 6.5v7l-7.5-2.4Z" />
            <path d="M13 6.5c2 .5 3.3 1.9 3.3 3.5s-1.3 3-3.3 3.5" />
            @break

        @case('code')
            <path d="M7 6.5 3.5 10 7 13.5M13 6.5 16.5 10 13 13.5" />
            @break

        @case('headset')
            <path d="M4 11v-1a6 6 0 0 1 12 0v1" />
            <rect x="3" y="11" width="3" height="4" rx="1" />
            <rect x="14" y="11" width="3" height="4" rx="1" />
            <path d="M17 15v1a2 2 0 0 1-2 2h-2" />
            @break

        @case('trending-up')
            <path d="M3.5 14 8 9.5l3 3 5.5-6" />
            <path d="M13 6.5h3.5V10" />
            @break

        @case('megaphone')
            <path d="M3.5 8.5h2.3L14 4.5v11l-8.2-4H3.5a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1Z" />
            <path d="M6.5 12.5 7.7 16h1.8" />
            @break

        @case('palette')
            <path d="M10 3.5a6.5 6.5 0 1 0 0 13c.9 0 1.5-.7 1.5-1.5 0-.4-.2-.7-.4-1-.2-.3-.4-.6-.4-1 0-.8.7-1.5 1.5-1.5H14a2.5 2.5 0 0 0 2.5-2.5c0-3.3-3-5.5-6.5-5.5Z" />
            <circle cx="7" cy="9" r=".9" fill="currentColor" stroke="none" />
            <circle cx="10" cy="7" r=".9" fill="currentColor" stroke="none" />
            <circle cx="13" cy="9" r=".9" fill="currentColor" stroke="none" />
            @break

        @case('pen')
            <path d="M12.5 4.5 15.5 7.5 6.5 16.5 3 17l.5-3.5Z" />
            <path d="M11 6 14 9" />
            @break

        @case('pin')
            <path d="M10 17s5.5-5.1 5.5-9A5.5 5.5 0 0 0 4.5 8c0 3.9 5.5 9 5.5 9Z" />
            <circle cx="10" cy="8" r="1.8" />
            @break

        @case('passport')
            <rect x="4" y="3" width="12" height="14" rx="1.5" />
            <circle cx="10" cy="8.5" r="2" />
            <path d="M7 14.5c.6-1.4 1.7-2 3-2s2.4.6 3 2" />
            @break

        @case('target')
            <circle cx="10" cy="10" r="6.5" />
            <circle cx="10" cy="10" r="3.5" />
            <circle cx="10" cy="10" r=".8" fill="currentColor" stroke="none" />
            @break

        @case('link')
            <path d="M8.5 11.5 11.5 8.5" />
            <path d="M9.5 6.5 11 5a3 3 0 0 1 4.2 4.2L13.7 10.7" />
            <path d="M10.5 13.5 9 15a3 3 0 0 1-4.2-4.2l1.5-1.5" />
            @break

        @case('search')
            <circle cx="9" cy="9" r="5.5" />
            <path d="M13.3 13.3 16.5 16.5" />
            @break

        @case('handshake')
            <path d="M3 9.5 6.5 6l3 2 2-1.7 5.5 4" />
            <path d="M6.5 6 10 9.3l1.3-1.1 1.9 1.9-3.4 3.4-4.3-3.4" />
            <path d="M14 8.3 17 10.5l-3 3" />
            @break

        @case('bulb')
            <path d="M7 14.5h6M8 16.5h4" />
            <path d="M10 3.5a5 5 0 0 0-2.7 9.2c.5.3.7.9.7 1.5v.3h4v-.3c0-.6.2-1.2.7-1.5A5 5 0 0 0 10 3.5Z" />
            @break

        @case('warning')
            <path d="M10 3.5 17 16H3Z" />
            <path d="M10 8.3v3.2" />
            <circle cx="10" cy="13.7" r=".2" fill="currentColor" stroke="currentColor" stroke-width="1.2" />
            @break

        @case('globe')
            <circle cx="10" cy="10" r="7" />
            <path d="M3 10h14M10 3c2.1 1.9 3.2 4.8 3.2 7s-1.1 5.1-3.2 7c-2.1-1.9-3.2-4.8-3.2-7S7.9 4.9 10 3Z" />
            @break

        @case('trophy')
            <path d="M6.5 4.5h7v4a3.5 3.5 0 0 1-7 0v-4Z" />
            <path d="M6.5 5.5H4.7A1.7 1.7 0 0 0 3 7.2c0 1.6 1.3 2.8 2.8 2.8H6.5M13.5 5.5h1.8a1.7 1.7 0 0 1 1.7 1.7c0 1.6-1.3 2.8-2.8 2.8h-.7" />
            <path d="M10 12v2.5M7.5 17h5l-.6-2.5H8.1Z" />
            @break

        @case('newspaper')
            <rect x="3" y="5" width="10.5" height="10.5" rx="1" />
            <path d="M13.5 7.5H16a1 1 0 0 1 1 1v6a2 2 0 0 1-2 2h-1.5" />
            <path d="M5.5 8h5.5M5.5 10.5h5.5M5.5 13h3.5" />
            @break

        @case('card')
            <rect x="3" y="5.5" width="14" height="9.5" rx="1.5" />
            <path d="M3 8.5h14" />
            <path d="M5.5 12h3" />
            @break

        @case('star')
            <path d="M10 3.3 12 8l5.1.5-3.9 3.4 1.2 5-4.4-2.7-4.4 2.7 1.2-5-3.9-3.4L7.9 8Z" fill="currentColor" stroke="none" />
            @break

        @case('graduation-cap')
            <path d="M10 5 3 8.3l7 3.2 7-3.2Z" />
            <path d="M6.5 9.8v3c0 1.1 1.6 2 3.5 2s3.5-.9 3.5-2v-3" />
            <path d="M17 8.3v4.2" />
            @break

        @case('school')
            <path d="M4 16.5V9l6-3.5L16 9v7.5" />
            <path d="M4 16.5h12" />
            <path d="M8.5 16.5V12h3v4.5" />
            @break

        @case('refresh')
            <path d="M15.5 8.5A5.5 5.5 0 0 0 5.6 6.3L4.5 7.5" />
            <path d="M4.5 4.5v3h3" />
            <path d="M4.5 11.5a5.5 5.5 0 0 0 9.9 2.2l1.1-1.2" />
            <path d="M15.5 15.5v-3h-3" />
            @break

        @case('tools')
            <path d="M12.7 7.3a2.6 2.6 0 0 1-3.4 3.4L4.5 15.5l-1-1L8.3 9.7a2.6 2.6 0 0 1 3.4-3.4l-2 2 1.3 1.3Z" />
            @break

        @case('accessibility')
            <circle cx="10" cy="4.5" r="1.3" fill="currentColor" stroke="none" />
            <path d="M6 8h8M10 8v2.5l3.5 4M10 10.5 6.5 14.5" />
            <path d="M8 10.5H6a2.5 2.5 0 0 0 0 5h1.2" />
            @break

        @case('mail')
            <rect x="3.5" y="5.5" width="13" height="9" rx="1.5" />
            <path d="M4 6.5l6 4.5 6-4.5" />
            @break

        @case('briefcase')
            <rect x="3" y="7.5" width="14" height="9" rx="1.5" />
            <path d="M7 7.5V6a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 13 6v1.5" />
            <path d="M3 11.5h14" />
            @break
    @endswitch
</svg>
