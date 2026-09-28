@props(['company' => '', 'size' => 48])

{{--
    Harmonized graphic illustration vector art across all cards.
    Deliberately identical across listings rather than distracting per-company initials
    or random rainbow gradients, ensuring visual focus remains on the job title,
    salary, and verified hiring details.
--}}
<div
    {{ $attributes->merge(['class' => 'relative flex shrink-0 items-center justify-center rounded-2xl border border-slate-200/80 bg-gradient-to-br from-slate-50 via-white to-slate-100/90 shadow-2xs group-hover:border-teal-500/40 group-hover:shadow-xs group-hover:scale-105 transition-all duration-200 select-none overflow-hidden']) }}
    style="width: {{ $size }}px; height: {{ $size }}px;"
    aria-label="{{ $company ? $company : 'Verified Company' }}"
    role="img"
>
    <svg
        viewBox="0 0 40 40"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="transition-transform duration-200 group-hover:scale-105 shrink-0"
        style="width: {{ round($size * 0.58) }}px; height: {{ round($size * 0.58) }}px;"
        aria-hidden="true"
    >
        <defs>
            <linearGradient id="krj-case-base" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#242c4c" />
                <stop offset="100%" stop-color="#141829" />
            </linearGradient>
            <linearGradient id="krj-case-flap" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#3b4673" />
                <stop offset="100%" stop-color="#242c4c" />
            </linearGradient>
            <linearGradient id="krj-case-clasp" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ff3131" />
                <stop offset="100%" stop-color="#e11d27" />
            </linearGradient>
        </defs>

        <!-- Briefcase Handle -->
        <path
            d="M14 13V10.5C14 8.57 15.57 7 17.5 7H22.5C24.43 7 26 8.57 26 10.5V13"
            stroke="#242c4c"
            stroke-width="2.5"
            stroke-linecap="round"
        />
        <circle cx="14" cy="13" r="1.25" fill="#3b4673" />
        <circle cx="26" cy="13" r="1.25" fill="#3b4673" />

        <!-- Main Body -->
        <rect
            x="6"
            y="12.5"
            width="28"
            height="20"
            rx="4.5"
            fill="url(#krj-case-base)"
        />

        <!-- Top Edge Specular Highlight -->
        <path
            d="M8.5 13.75H31.5"
            stroke="#ffffff"
            stroke-opacity="0.22"
            stroke-width="0.8"
            stroke-linecap="round"
        />

        <!-- Dimensional Front Flap -->
        <path
            d="M6 14.5C6 13.4 6.9 12.5 8 12.5H32C33.1 12.5 34 13.4 34 14.5V18.5C34 19.3 33.5 20.05 32.7 20.35L21.2 24.35C20.4 24.65 19.6 24.65 18.8 24.35L7.3 20.35C6.5 20.05 6 19.3 6 18.5V14.5Z"
            fill="url(#krj-case-flap)"
        />

        <!-- Flap Crease Shadow -->
        <path
            d="M6 19.5L19.2 24C19.7 24.2 20.3 24.2 20.8 24L34 19.5"
            stroke="#0a0d17"
            stroke-width="1.2"
            stroke-opacity="0.35"
            stroke-linecap="round"
        />

        <!-- Precision Accent Stitching -->
        <line
            x1="8.5"
            y1="28.5"
            x2="31.5"
            y2="28.5"
            stroke="#ffffff"
            stroke-opacity="0.16"
            stroke-dasharray="1.2 1.4"
            stroke-width="0.75"
        />

        <!-- Brand Clasp (Sunrise Coral Red) -->
        <rect
            x="17.5"
            y="22"
            width="5"
            height="5.5"
            rx="1.4"
            fill="url(#krj-case-clasp)"
        />
        <!-- Metallic Center Rivet -->
        <circle cx="20" cy="24.5" r="0.9" fill="#ffffff" />
        <circle cx="20" cy="24.5" r="0.35" fill="#881318" />
    </svg>
</div>

