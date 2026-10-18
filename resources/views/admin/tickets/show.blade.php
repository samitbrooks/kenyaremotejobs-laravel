<x-layouts.admin title="Ticket #{{ $ticket->ticket_number }}">
    <div class="space-y-6 max-w-4xl">
        {{-- Navigation & Top Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('admin.tickets.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition"
            >
                &larr; Back to all tickets
            </a>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('support.show', ['ticketNumber' => $ticket->ticket_number, 'token' => $ticket->access_token]) }}"
                    target="_blank"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-[#3444da] hover:text-[#3444da] transition shadow-2xs"
                >
                    View Public Customer URL &nearr;
                </a>
            </div>
        </div>

        {{-- Flash Feedback --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900 shadow-2xs flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">&check;</span>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Ticket Metadata & Status Header Card --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4 pb-5 border-b border-slate-100">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5 mb-2">
                        <span class="font-mono text-sm font-black text-[#3444da] bg-[#eef0fe] px-3 py-1 rounded-full border border-[#c7ccfb]">
                            {{ $ticket->ticket_number }}
                        </span>
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $ticket->statusBadgeClass() }}">
                            {{ $ticket->statusLabel() }}
                        </span>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $ticket->priorityBadgeClass() }}">
                            Priority: {{ ucfirst($ticket->priority) }}
                        </span>
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                            {{ $ticket->categoryLabel() }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-black text-slate-900 leading-tight">
                        {{ $ticket->subject }}
                    </h1>
                </div>

                {{-- Quick Status / Priority Switcher Form --}}
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-3 text-xs shrink-0">
                    <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="flex items-center gap-2">
                        @csrf
                        <div>
                            <select name="status" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-800">
                                <option value="open" @selected($ticket->status === 'open')>Open</option>
                                <option value="in_progress" @selected($ticket->status === 'in_progress')>In Progress</option>
                                <option value="resolved" @selected($ticket->status === 'resolved')>Resolved</option>
                                <option value="closed" @selected($ticket->status === 'closed')>Closed</option>
                            </select>
                        </div>
                        <div>
                            <select name="priority" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-800">
                                <option value="low" @selected($ticket->priority === 'low')>Low</option>
                                <option value="normal" @selected($ticket->priority === 'normal')>Normal</option>
                                <option value="high" @selected($ticket->priority === 'high')>High</option>
                                <option value="urgent" @selected($ticket->priority === 'urgent')>Urgent</option>
                            </select>
                        </div>
                        <button type="submit" class="rounded-lg bg-slate-800 hover:bg-black px-2.5 py-1 text-xs font-bold text-white shadow-2xs">
                            Update
                        </button>
                    </form>
                </div>
            </div>

            {{-- Customer & Account Details --}}
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-slate-600">
                <div>
                    <span class="text-slate-400 block">Customer Name</span>
                    <strong class="text-slate-900 text-sm">{{ $ticket->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Customer Email</span>
                    <a href="mailto:{{ $ticket->email }}" class="font-bold text-[#3444da] hover:underline text-sm">
                        {{ $ticket->email }}
                    </a>
                </div>
                <div>
                    <span class="text-slate-400 block">Account Status</span>
                    @if ($ticket->user)
                        <span class="inline-flex items-center gap-1 font-bold text-slate-900">
                            Registered Member
                            @if ($ticket->user->isSubscribed())
                                <span class="rounded-full bg-[#eef0fe] text-[#3444da] border border-[#c7ccfb] px-2 py-0.2 text-[10px] font-bold">Pro Active</span>
                            @endif
                        </span>
                        <a href="{{ url('/admin/users/'.$ticket->user->id) }}" class="text-[11px] text-[#3444da] hover:underline block">
                            View User Dashboard &rarr;
                        </a>
                    @else
                        <span class="text-slate-500 font-medium">Guest (Non-registered)</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Message Stream --}}
        <div class="space-y-6">
            {{-- Original Customer Ticket --}}
            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                            {{ strtoupper(substr($ticket->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $ticket->name }} (Customer)</p>
                            <p class="text-[10px] text-slate-500">Original Inquiry Submitted</p>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400">{{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</span>
                </div>
                <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $ticket->message }}
                </div>
            </div>

            {{-- Thread History --}}
            @foreach ($ticket->replies as $reply)
                <div class="rounded-3xl border {{ $reply->is_admin ? 'border-[#c7ccfb] bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/30' : 'border-slate-200/90 bg-white' }} p-6 sm:p-7 shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b {{ $reply->is_admin ? 'border-[#c7ccfb]/60' : 'border-slate-100' }} mb-4">
                        <div class="flex items-center gap-2.5">
                            @if ($reply->is_admin)
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3444da] text-white text-xs font-bold shadow-2xs">
                                    <x-icon name="sparkle" class="h-4 w-4" />
                                </span>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-xs font-bold text-slate-900">{{ $reply->sender_name }}</p>
                                        <span class="rounded-full bg-[#3444da] text-white px-2 py-0.2 text-[9px] font-bold">Admin Reply</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500">Sent to customer inbox</p>
                                </div>
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                    {{ strtoupper(substr($reply->sender_name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $reply->sender_name }}</p>
                                    <p class="text-[10px] text-slate-500">Customer Follow-Up Message</p>
                                </div>
                            @endif
                        </div>
                        <span class="text-xs text-slate-400">{{ $reply->created_at->format('M d, Y \a\t g:i A') }}</span>
                    </div>

                    <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $reply->message }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Admin Reply Form --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-bold text-slate-900">Post Reply to Customer</h3>
                <span class="text-xs text-slate-400">Replying to: {{ $ticket->email }}</span>
            </div>

            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="space-y-4">
                @csrf

                <div>
                    <textarea
                        name="message"
                        rows="5"
                        required
                        placeholder="Type your official response to the customer here…"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-4 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition leading-relaxed"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-4 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700">
                            <input
                                type="checkbox"
                                name="send_email"
                                value="1"
                                checked
                                class="rounded text-[#3444da] focus:ring-[#3444da]"
                            />
                            <span>Email response to customer</span>
                        </label>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">Set status to:</span>
                            <select name="status" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-800">
                                <option value="in_progress" @selected($ticket->status === 'open' || $ticket->status === 'in_progress')>In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                                <option value="open">Keep Open</option>
                            </select>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="btn-pop inline-flex items-center gap-2 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-6 py-2.5 text-xs font-bold text-white shadow-md transition"
                    >
                        <span>Send Response</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
