<x-layouts.app
    title="Support Ticket #{{ $ticket->ticket_number }}"
    description="View status and communication history for ticket {{ $ticket->ticket_number }} on KenyaRemoteJobs."
>
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
        {{-- Navigation & Top Actions --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-[#3444da] transition">Home</a>
                <span>&rsaquo;</span>
                <a href="{{ route('support.index') }}" class="hover:text-[#3444da] transition">Support Desk</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800">{{ $ticket->ticket_number }}</span>
            </nav>

            <a
                href="{{ route('support.index') }}"
                class="rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:border-[#3444da] hover:text-[#3444da] transition"
            >
                &larr; Open another ticket
            </a>
        </div>

        {{-- Notifications --}}
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900 shadow-2xs flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">&check;</span>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Ticket Header Card --}}
        <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm mb-8">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-slate-100">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="font-mono text-sm font-extrabold text-[#3444da] bg-[#eef0fe] px-3 py-1 rounded-full border border-[#c7ccfb]">
                        {{ $ticket->ticket_number }}
                    </span>
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $ticket->statusBadgeClass() }}">
                        {{ $ticket->statusLabel() }}
                    </span>
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $ticket->priorityBadgeClass() }}">
                        Priority: {{ ucfirst($ticket->priority) }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                        {{ $ticket->categoryLabel() }}
                    </span>
                </div>

                <div class="text-xs text-slate-500">
                    Opened {{ $ticket->created_at->diffForHumans() }}
                </div>
            </div>

            <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                {{ $ticket->subject }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-slate-600">
                <span class="flex items-center gap-1.5 font-medium">
                    <strong class="text-slate-800">Requester:</strong> {{ $ticket->name }} ({{ $ticket->email }})
                </span>
                <span>&bull;</span>
                <span>
                    <strong class="text-slate-800">Last activity:</strong> {{ $ticket->last_replied_at ? $ticket->last_replied_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        {{-- Message Stream --}}
        <div class="space-y-6 mb-8">
            {{-- Original Inquiry --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                            {{ strtoupper(substr($ticket->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $ticket->name }}</p>
                            <p class="text-[10px] text-slate-500">Original Inquiry</p>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400">{{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</span>
                </div>
                <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $ticket->message }}
                </div>
            </div>

            {{-- Replies --}}
            @foreach ($ticket->replies as $reply)
                <div class="rounded-3xl border {{ $reply->is_admin ? 'border-[#c7ccfb] bg-gradient-to-br from-[#f8f9fd] via-white to-[#eef0fe]/40 shadow-xs' : 'border-slate-200/80 bg-white shadow-2xs' }} p-6 sm:p-7">
                    <div class="flex items-center justify-between pb-3 border-b {{ $reply->is_admin ? 'border-[#c7ccfb]/60' : 'border-slate-100' }} mb-4">
                        <div class="flex items-center gap-2.5">
                            @if ($reply->is_admin)
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3444da] text-white text-xs font-bold shadow-2xs">
                                    <x-icon name="sparkle" class="h-4 w-4" />
                                </span>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-xs font-bold text-slate-900">{{ $reply->sender_name }}</p>
                                        <span class="rounded-full bg-[#3444da] text-white px-2 py-0.2 text-[9px] font-bold">Support Staff</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500">Official Advisor Response</p>
                                </div>
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                    {{ strtoupper(substr($reply->sender_name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $reply->sender_name }}</p>
                                    <p class="text-[10px] text-slate-500">Customer Follow-Up</p>
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

        {{-- Reply Submission Box --}}
        @if ($ticket->isClosed())
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                <p class="font-bold text-slate-900 text-sm">This ticket has been marked as closed.</p>
                <p class="text-xs text-slate-500 mt-1">If you need further help with another inquiry, please open a new ticket.</p>
                <a href="{{ route('support.index') }}" class="mt-4 inline-block btn-pop rounded-full bg-[#3444da] px-6 py-2.5 text-xs font-bold text-white shadow-xs">
                    Open New Support Ticket &rarr;
                </a>
            </div>
        @else
            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-1">Add Follow-Up Message</h3>
                <p class="text-xs text-slate-500 mb-4">Have extra details or need clarification? Post a message below.</p>

                <form method="POST" action="{{ route('support.reply', ['ticketNumber' => $ticket->ticket_number, 'token' => $accessToken]) }}">
                    @csrf
                    <div>
                        <textarea
                            name="message"
                            rows="4"
                            required
                            placeholder="Type your follow-up reply or questions here..."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition leading-relaxed"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs text-slate-400">
                            Our support team will receive an immediate notification.
                        </p>
                        <button
                            type="submit"
                            class="btn-pop inline-flex items-center gap-1.5 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-6 py-2.5 text-xs font-bold text-white shadow-md transition"
                        >
                            <span>Send Reply</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-layouts.app>
