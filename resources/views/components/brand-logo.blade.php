@props(['dark' => false, 'iconOnly' => false, 'class' => ''])

@php
    $textColor = $dark ? 'text-white' : 'text-slate-900';
    $iconBg = $dark ? 'bg-[#3444da]/15 border border-[#3444da]/40 text-[#60a5fa]' : 'bg-[#eef0fe] border border-[#3444da]/20 text-[#3444da]';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none '.$class]) }}>
    {{-- Minimalist vector glyph --}}
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ $iconBg }} shadow-2xs transition-transform duration-200 group-hover:scale-105">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 3a15 15 0 0 1 0 18" />
            <path d="M12 3a15 15 0 0 0 0 18" />
            <path d="M3 12h18" />
        </svg>
    </span>

    @unless ($iconOnly)
        <div class="flex flex-col leading-none">
            <span class="text-base sm:text-lg font-black tracking-tight {{ $textColor }} font-sans">
                Kenya<span class="text-[#3444da]">RemoteJobs</span>
            </span>
            <span class="text-[9px] font-bold uppercase tracking-widest {{ $dark ? 'text-slate-400' : 'text-slate-400' }} mt-0.5">
                Vetted Global Roles
            </span>
        </div>
    @endunless
</div>
