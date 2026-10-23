@props(['title' => null])

@php
    $sectionLabels = [
        'admin' => 'Dashboard Overview',
        'admin/jobs*' => 'Job Catalog & Operations',
        'admin/tickets*' => 'Support Tickets Desk',
        'admin/users*' => 'User CRM & Directory',
        'admin/payments*' => 'Payments & Revenue',
        'admin/whatsapp*' => 'WhatsApp Cloud API Hub',
        'admin/email*' => 'Email Campaigns & Delivery',
        'admin/blog*' => 'Editorial Journal & CMS',
    ];

    $activeLabel = 'MIS Console';
    foreach ($sectionLabels as $pattern => $label) {
        if (request()->is($pattern)) {
            $activeLabel = $label;
            break;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ? "{$title} | KenyaRemoteJobs MIS" : "KenyaRemoteJobs — MIS Admin Console" }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full font-sans bg-slate-100 text-slate-900 antialiased selection:bg-[#3444da] selection:text-white">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row bg-slate-100/90">
            {{-- Mobile Sidebar Drawer Backdrop --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-slate-950/75 backdrop-blur-xs lg:hidden"
                style="display: none;"
            ></div>

            {{-- Mobile Sidebar Off-Canvas Drawer --}}
            <aside
                x-show="sidebarOpen"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-slate-300 flex flex-col border-r border-slate-800 shadow-2xl lg:hidden"
                style="display: none;"
            >
                <x-layouts.admin-sidebar :is-mobile="true" />
            </aside>

            {{-- Desktop Persistent Left Sidebar --}}
            <aside class="hidden lg:flex lg:w-64 xl:w-72 lg:shrink-0 lg:flex-col lg:h-screen lg:sticky lg:top-0 bg-slate-950 text-slate-300 border-r border-slate-800/80 shadow-md">
                <x-layouts.admin-sidebar :is-mobile="false" />
            </aside>

            {{-- Main MIS Workspace --}}
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                {{-- Sticky Top Header Bar --}}
                <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 sm:px-6 lg:px-8 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        {{-- Mobile Menu Trigger --}}
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="lg:hidden rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#3444da]"
                            aria-label="Open sidebar menu"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </button>

                        {{-- Breadcrumbs & Module Title --}}
                        <div class="flex items-center gap-2">
                            <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-400">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                                MIS Portal
                            </span>
                            <span class="hidden sm:inline text-slate-300">/</span>
                            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                {{ $activeLabel }}
                            </h2>
                        </div>
                    </div>

                    {{-- Header Right Actions --}}
                    <div class="flex items-center gap-3 sm:gap-4">
                        {{-- Current Date / Time Badge --}}
                        <span class="hidden md:inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200/60">
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ now()->format('D, M j, Y') }}
                        </span>

                        {{-- System Status Pill --}}
                        <div class="hidden sm:inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-800">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <span>System Healthy</span>
                        </div>

                        {{-- View Public Site Shortcut --}}
                        <a
                            href="{{ url('/') }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:border-[#3444da] hover:text-[#3444da] hover:bg-[#3444da]/5 transition shadow-2xs"
                        >
                            <span>Live Site</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3" />
                            </svg>
                        </a>

                        {{-- Quick Logout --}}
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                title="Sign Out of MIS"
                                class="rounded-lg p-1.5 text-slate-500 hover:bg-red-50 hover:text-red-600 transition border border-transparent hover:border-red-200"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </header>

                {{-- Main Content Canvas --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                    {{ $slot }}
                </main>

                {{-- Clean MIS Footer --}}
                <footer class="mt-auto border-t border-slate-200/80 bg-white px-4 py-3.5 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-700">KenyaRemoteJobs MIS</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Internal Management Console</span>
                        </div>
                        <div class="text-[11px] text-slate-400">
                            Strictly Confidential &bull; Authorized Operations Only
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
