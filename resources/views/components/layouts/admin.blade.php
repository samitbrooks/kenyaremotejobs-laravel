@props(['title' => null])

@php
    $openTicketsCount = \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
    $adminLinks = [
        ['href' => '/admin', 'label' => 'Dashboard'],
        ['href' => '/admin/tickets', 'label' => 'Tickets', 'badge' => $openTicketsCount],
        ['href' => '/admin/jobs', 'label' => 'Jobs'],
        ['href' => '/admin/blog', 'label' => 'Journal'],
        ['href' => '/admin/users', 'label' => 'Users'],
        ['href' => '/admin/payments', 'label' => 'Payments'],
        ['href' => '/admin/email', 'label' => 'Email'],
    ];
@endphp

<x-layouts.app :title="$title" description="Admin">
    <div class="bg-slate-900 text-white border-b border-slate-800">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-5 px-4 py-3 text-sm font-medium sm:px-6">
            <div class="flex items-center gap-5 overflow-x-auto no-scrollbar">
                <span class="text-xs uppercase tracking-wide text-white/50">Admin</span>
                @foreach ($adminLinks as $link)
                    @php
                        $path = ltrim($link['href'], '/');
                        $patterns = $path === 'admin' ? [$path] : [$path, $path.'/*'];
                    @endphp
                    <a href="{{ url($link['href']) }}" class="transition flex items-center gap-1.5 hover:text-[#a3abf8] whitespace-nowrap {{ request()->is(...$patterns) ? 'text-[#a3abf8] font-bold' : 'text-slate-300' }}">
                        <span>{{ $link['label'] }}</span>
                        @if (! empty($link['badge']) && $link['badge'] > 0)
                            <span class="rounded-full bg-[#3444da] text-white px-1.5 py-0.5 text-[10px] font-extrabold leading-none">
                                {{ $link['badge'] }}
                            </span>
                        @endif
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
