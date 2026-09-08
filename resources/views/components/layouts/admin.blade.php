@props(['title' => null])

@php
    $adminLinks = [
        ['href' => '/admin', 'label' => 'Dashboard'],
        ['href' => '/admin/jobs', 'label' => 'Jobs'],
        ['href' => '/admin/blog', 'label' => 'Journal'],
        ['href' => '/admin/users', 'label' => 'Users'],
        ['href' => '/admin/email', 'label' => 'Email'],
    ];
@endphp

<x-layouts.app :title="$title" description="Admin">
    <div class="bg-horizon-800 text-white">
        <nav class="mx-auto flex max-w-6xl items-center gap-5 px-4 py-3 text-sm font-medium sm:px-6">
            <span class="text-xs uppercase tracking-wide text-white/50">Admin</span>
            @foreach ($adminLinks as $link)
                <a href="{{ url($link['href']) }}" class="transition hover:text-sunrise-300 {{ request()->is(ltrim($link['href'], '/')) ? 'text-sunrise-300' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        {{ $slot }}
    </div>
</x-layouts.app>
