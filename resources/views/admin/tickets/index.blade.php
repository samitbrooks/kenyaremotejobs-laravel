<x-layouts.admin title="Admin: Support Tickets">
    <div class="space-y-6">
        {{-- Top Bar & Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight sm:text-3xl">Support Tickets</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Manage customer support requests, M-Pesa transaction inquiries, and direct candidate questions.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('support.index') }}"
                    target="_blank"
                    class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:border-[#3444da] hover:text-[#3444da] transition shadow-2xs"
                >
                    Public Ticket Form &nearr;
                </a>
            </div>
        </div>

        {{-- Metrics Summary Cards --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <a
                href="{{ route('admin.tickets.index', ['status' => 'active']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'active' ? 'border-[#3444da] bg-[#eef0fe]/40 ring-1 ring-[#3444da]' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Active</p>
                <p class="mt-1 text-2xl font-black text-[#3444da]">{{ number_format($stats['active']) }}</p>
            </a>

            <a
                href="{{ route('admin.tickets.index', ['status' => 'open']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'open' ? 'border-amber-400 bg-amber-50/50 ring-1 ring-amber-400' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Open</p>
                <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['open']) }}</p>
            </a>

            <a
                href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'in_progress' ? 'border-[#3444da] bg-[#eef0fe]/40 ring-1 ring-[#3444da]' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">In Progress</p>
                <p class="mt-1 text-2xl font-black text-indigo-600">{{ number_format($stats['in_progress']) }}</p>
            </a>

            <a
                href="{{ route('admin.tickets.index', ['status' => 'resolved']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'resolved' ? 'border-emerald-400 bg-emerald-50/50 ring-1 ring-emerald-400' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Resolved</p>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['resolved']) }}</p>
            </a>

            <a
                href="{{ route('admin.tickets.index', ['status' => 'closed']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'closed' ? 'border-slate-400 bg-slate-100 ring-1 ring-slate-400' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Closed</p>
                <p class="mt-1 text-2xl font-black text-slate-600">{{ number_format($stats['closed']) }}</p>
            </a>

            <a
                href="{{ route('admin.tickets.index', ['status' => 'all']) }}"
                class="rounded-2xl border p-4 transition shadow-2xs {{ $currentStatus === 'all' ? 'border-slate-400 bg-slate-100 ring-1 ring-slate-400' : 'border-slate-200/80 bg-white hover:border-slate-300' }}"
            >
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">All</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
            </a>
        </div>

        {{-- Filter & Search Form --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <input type="hidden" name="status" value="{{ $currentStatus }}">

                <div class="sm:col-span-6">
                    <input
                        type="text"
                        name="q"
                        value="{{ $currentQuery }}"
                        placeholder="Search ticket #, email, customer name, or subject…"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none"
                    />
                </div>

                <div class="sm:col-span-4">
                    <select
                        name="category"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none"
                    >
                        <option value="">All Categories</option>
                        <option value="payment" @selected($currentCategory === 'payment')>M-Pesa / Payment Issue</option>
                        <option value="jobs" @selected($currentCategory === 'jobs')>Job Applications</option>
                        <option value="account" @selected($currentCategory === 'account')>Account / Login</option>
                        <option value="employer" @selected($currentCategory === 'employer')>Employer / Hiring</option>
                        <option value="technical" @selected($currentCategory === 'technical')>Technical / Bug</option>
                        <option value="general" @selected($currentCategory === 'general')>General Inquiry</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button
                        type="submit"
                        class="btn-pop w-full rounded-xl bg-slate-900 hover:bg-black py-2 text-xs font-bold text-white shadow-xs transition"
                    >
                        Filter
                    </button>
                    @if ($currentQuery || $currentCategory || $currentStatus !== 'active')
                        <a
                            href="{{ route('admin.tickets.index') }}"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-500 hover:text-slate-800 transition flex items-center"
                            title="Reset filters"
                        >
                            &times;
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tickets Table --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white shadow-sm overflow-hidden">
            @if ($tickets->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <p class="font-bold text-slate-800 text-sm">No support tickets found.</p>
                    <p class="text-xs mt-1">Try widening your filters or search keywords.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Ticket</th>
                                <th class="py-3 px-4">Customer</th>
                                <th class="py-3 px-4">Subject &amp; Category</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Priority</th>
                                <th class="py-3 px-4">Last Activity</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-[#eef0fe]/20 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#3444da] whitespace-nowrap">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="hover:underline">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="font-bold text-slate-900">{{ $ticket->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $ticket->email }}</p>
                                        @if ($ticket->user)
                                            <a href="{{ url('/admin/users/'.$ticket->user->id) }}" class="text-[10px] text-[#3444da] hover:underline block mt-0.5">
                                                Registered User &rarr;
                                            </a>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-[#3444da] line-clamp-1 block">
                                            {{ $ticket->subject }}
                                        </a>
                                        <span class="inline-block mt-0.5 text-[10px] text-slate-500">
                                            {{ $ticket->categoryLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $ticket->statusBadgeClass() }}">
                                            {{ $ticket->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $ticket->priorityBadgeClass() }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap text-[11px] text-slate-500">
                                        {{ $ticket->last_replied_at ? $ticket->last_replied_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <a
                                            href="{{ route('admin.tickets.show', $ticket) }}"
                                            class="btn-pop inline-flex items-center gap-1 rounded-xl bg-slate-900 hover:bg-[#3444da] px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition"
                                        >
                                            View &amp; Reply &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.admin>
