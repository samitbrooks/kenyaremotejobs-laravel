@props(['isMobile' => false])

@php
    $openTicketsCount = \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count();

    $navigationGroups = [
        [
            'group' => 'Analytics & Overview',
            'items' => [
                [
                    'href' => '/admin',
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                    'active' => request()->is('admin'),
                    'badge' => null,
                ],
            ],
        ],
        [
            'group' => 'Operations',
            'items' => [
                [
                    'href' => '/admin/jobs',
                    'label' => 'Job Catalog',
                    'icon' => 'briefcase',
                    'active' => request()->is('admin/jobs', 'admin/jobs/*'),
                    'badge' => null,
                ],
            ],
        ],
        [
            'group' => 'Customer & CRM',
            'items' => [
                [
                    'href' => '/admin/tickets',
                    'label' => 'Support Desk',
                    'icon' => 'tickets',
                    'active' => request()->is('admin/tickets', 'admin/tickets/*'),
                    'badge' => $openTicketsCount > 0 ? $openTicketsCount : null,
                    'badgeColor' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                ],
                [
                    'href' => '/admin/users',
                    'label' => 'User Directory',
                    'icon' => 'users',
                    'active' => request()->is('admin/users', 'admin/users/*'),
                    'badge' => null,
                ],
            ],
        ],
        [
            'group' => 'Finance & Commerce',
            'items' => [
                [
                    'href' => '/admin/payments',
                    'label' => 'Payments & M-Pesa',
                    'icon' => 'payments',
                    'active' => request()->is('admin/payments', 'admin/payments/*'),
                    'badge' => null,
                ],
            ],
        ],
        [
            'group' => 'Communications',
            'items' => [
                [
                    'href' => '/admin/whatsapp',
                    'label' => 'WhatsApp Cloud',
                    'icon' => 'whatsapp',
                    'active' => request()->is('admin/whatsapp', 'admin/whatsapp/*'),
                    'badge' => null,
                ],
                [
                    'href' => '/admin/email',
                    'label' => 'Email Campaigns',
                    'icon' => 'email',
                    'active' => request()->is('admin/email', 'admin/email/*'),
                    'badge' => null,
                ],
                [
                    'href' => '/admin/blog',
                    'label' => 'Editorial Journal',
                    'icon' => 'journal',
                    'active' => request()->is('admin/blog', 'admin/blog/*'),
                    'badge' => null,
                ],
            ],
        ],
    ];

    $adminUser = auth()->user();
    $adminInitials = strtoupper(substr($adminUser?->name ?? $adminUser?->email ?? 'AD', 0, 2));
@endphp

<div class="flex h-full flex-col justify-between overflow-y-auto no-scrollbar">
    <div>
        {{-- Brand / Header --}}
        <div class="flex items-center justify-between border-b border-slate-800/80 px-4 py-4">
            <a href="{{ url('/admin') }}" class="flex items-center gap-2 group transition-transform duration-150 active:scale-95" aria-label="KenyaRemoteJobs Admin Dashboard">
                <x-brand-logo :dark="true" img-class="h-8 sm:h-9 w-auto max-w-[170px] sm:max-w-[190px]" />
                <span class="rounded bg-[#3444da]/30 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-[#a3abf8] border border-[#3444da]/40 shadow-xs">MIS</span>
            </a>

            @if ($isMobile)
                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition focus:outline-none"
                    aria-label="Close sidebar"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- System Health Indicator --}}
        <div class="mx-4 mt-3 rounded-xl bg-slate-900/90 border border-slate-800/80 p-2.5">
            <div class="flex items-center justify-between text-[11px]">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="font-semibold text-slate-300">MIS Console Active</span>
                </div>
                <span class="font-mono text-[10px] text-slate-500">v2.5 Live</span>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="space-y-6 px-3 py-4 text-xs">
            @foreach ($navigationGroups as $group)
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400/90">
                        {{ $group['group'] }}
                    </p>
                    <div class="mt-1.5 space-y-0.5">
                        @foreach ($group['items'] as $item)
                            <a
                                href="{{ url($item['href']) }}"
                                @if ($isMobile) @click="sidebarOpen = false" @endif
                                class="group flex items-center justify-between rounded-xl px-3 py-2.5 font-medium transition-all {{ $item['active'] ? 'bg-gradient-to-r from-[#3444da]/25 to-slate-900/40 text-white font-semibold border-l-3 border-[#3444da] shadow-xs' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-100' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="transition-colors {{ $item['active'] ? 'text-[#a3abf8]' : 'text-slate-400 group-hover:text-slate-200' }}">
                                        @switch($item['icon'])
                                            @case('dashboard')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="7" height="9" x="3" y="3" rx="1" />
                                                    <rect width="7" height="5" x="14" y="3" rx="1" />
                                                    <rect width="7" height="9" x="14" y="12" rx="1" />
                                                    <rect width="7" height="5" x="3" y="16" rx="1" />
                                                </svg>
                                                @break

                                            @case('briefcase')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="7" rx="2" />
                                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                                </svg>
                                                @break

                                            @case('tickets')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                                    <path d="M8 9h8M8 13h5" />
                                                </svg>
                                                @break

                                            @case('users')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                </svg>
                                                @break

                                            @case('payments')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="5" rx="2" />
                                                    <line x1="2" x2="22" y1="10" y2="10" />
                                                    <path d="M6 15h2M10 15h4" />
                                                </svg>
                                                @break

                                            @case('whatsapp')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                                                </svg>
                                                @break

                                            @case('email')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                                </svg>
                                                @break

                                            @case('journal')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2" />
                                                    <path d="M18 14v-8H10v8h8Z" />
                                                </svg>
                                                @break
                                        @endswitch
                                    </span>
                                    <span class="tracking-wide">{{ $item['label'] }}</span>
                                </div>

                                @if (! empty($item['badge']))
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-extrabold leading-none tabular-nums {{ $item['badgeColor'] ?? 'bg-[#3444da] text-white' }}">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </div>

    {{-- Bottom Section: Public Portal Link & Admin Profile --}}
    <div class="border-t border-slate-800/80 bg-slate-950/80 p-3 space-y-2">
        {{-- External Public Portal Link --}}
        <a
            href="{{ url('/') }}"
            target="_blank"
            rel="noopener"
            class="flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold text-slate-400 hover:bg-slate-900 hover:text-slate-200 transition"
        >
            <div class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
                <span>View Public Site</span>
            </div>
            <svg class="h-3 w-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3" />
            </svg>
        </a>

        {{-- Admin Profile & Logout --}}
        <div class="rounded-xl bg-slate-900 border border-slate-800/90 p-3">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-tr from-[#3444da] to-teal-500 font-black text-xs text-white shadow-xs">
                    {{ $adminInitials }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold text-slate-200" title="{{ $adminUser?->email }}">
                        {{ $adminUser?->email ?? 'Administrator' }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Super Admin</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-slate-800/80">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-slate-800/80 hover:bg-red-950/40 hover:text-red-300 hover:border-red-800/50 border border-slate-700/60 py-1.5 px-3 text-xs font-semibold text-slate-300 transition"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
