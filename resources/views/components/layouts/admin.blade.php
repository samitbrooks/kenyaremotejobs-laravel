@props(['title' => null])

@php
    $adminLinks = [
        ['href' => '/admin', 'label' => 'Dashboard'],
        ['href' => '/admin/jobs', 'label' => 'Jobs'],
        ['href' => '/admin/blog', 'label' => 'Journal'],
        ['href' => '/admin/users', 'label' => 'Users'],
        ['href' => '/admin/payments', 'label' => 'Payments'],
        ['href' => '/admin/email', 'label' => 'Email'],
    ];
@endphp

<x-layouts.app :title="$title" description="Admin">
    <div class="bg-horizon-800 text-white">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-5 px-4 py-3 text-sm font-medium sm:px-6">
            <div class="flex items-center gap-5">
                <span class="text-xs uppercase tracking-wide text-white/50">Admin</span>
                @foreach ($adminLinks as $link)
                    @php
                        $path = ltrim($link['href'], '/');
                        $patterns = $path === 'admin' ? [$path] : [$path, $path.'/*'];
                    @endphp
                    <a href="{{ url($link['href']) }}" class="transition hover:text-sunrise-300 {{ request()->is(...$patterns) ? 'text-sunrise-300' : '' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="ml-auto flex items-center gap-3">
                <span class="hidden text-xs text-white/60 sm:inline">{{ auth()->user()?->email }}</span>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-white/80 transition hover:bg-white/20 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </nav>
    </div>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        {{ $slot }}
    </div>
</x-layouts.app>
