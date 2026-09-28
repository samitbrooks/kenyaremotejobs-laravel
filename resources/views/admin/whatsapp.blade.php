<x-layouts.admin title="Admin: WhatsApp Business Hub">
    <div class="space-y-6">
        {{-- Header & API Status Banner --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2.5">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white font-black text-sm shadow-sm">
                        💬
                    </span>
                    WhatsApp Business Hub
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Direct Meta Cloud API messaging &bull; Manage all candidate support &amp; send instant job alerts without a physical phone.
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if ($isConfigured)
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-300 px-3.5 py-1 text-xs font-bold text-emerald-800">
                        <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        Meta Cloud API Live (ID: {{ $phoneNumberId }})
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 border border-amber-300 px-3.5 py-1 text-xs font-bold text-amber-800" title="Add WHATSAPP_ACCESS_TOKEN & WHATSAPP_PHONE_NUMBER_ID to your .env to connect live">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        Simulated Local Mode (Add Keys to Go Live)
                    </span>
                @endif
            </div>
        </div>

        {{-- Flash Session Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-sm font-semibold text-emerald-900 shadow-2xs flex items-center gap-2.5">
                <span class="text-emerald-600 font-bold">&check;</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50/90 p-4 text-sm font-semibold text-red-900 shadow-2xs flex items-center gap-2.5">
                <span class="text-red-600 font-bold">&cross;</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Quick Stats Row --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Messages</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($stats['total_messages']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Active Conversations</p>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['active_threads']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Inbound (Candidates)</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($stats['inbound']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Outbound Alerts Sent</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($stats['outbound']) }}</p>
            </div>
        </div>

        {{-- Main Chat Hub Layout --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[580px]">
            {{-- Left Panel: Threads List & New Chat --}}
            <div class="md:col-span-4 border-r border-slate-200/80 flex flex-col bg-slate-50/50">
                <div class="p-4 border-b border-slate-200/80 flex items-center justify-between bg-white">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Conversations</span>

                    <button
                        type="button"
                        onclick="document.getElementById('newChatModal').classList.remove('hidden')"
                        class="btn-pop rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-2xs hover:bg-emerald-700 transition"
                    >
                        + New Message
                    </button>
                </div>

                {{-- Thread List --}}
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @forelse ($conversations as $thread)
                        @php
                            $isActive = $selectedPhone === $thread->phone_number;
                        @endphp
                        <a
                            href="{{ route('admin.whatsapp', ['phone' => $thread->phone_number]) }}"
                            class="block p-3.5 transition {{ $isActive ? 'bg-emerald-50/80 border-l-4 border-emerald-600' : 'hover:bg-slate-100/70' }}"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-600' : 'bg-slate-300' }}"></span>
                                    +{{ $thread->phone_number }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($thread->last_activity)->diffForHumans(short: true) }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 truncate">
                                @if ($thread->latest_direction === 'outbound')
                                    <span class="text-emerald-600 font-semibold">You: </span>
                                @endif
                                {{ $thread->latest_message ?? 'No content' }}
                            </p>
                        </a>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            No WhatsApp conversations yet.<br>Click <strong>+ New Message</strong> above to start!
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right Panel: Active Chat Stream & Sender --}}
            <div class="md:col-span-8 flex flex-col bg-white">
                @if ($selectedPhone)
                    {{-- Active Conversation Header --}}
                    <div class="p-4 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3 bg-white">
                        <div class="flex items-center gap-2.5">
                            <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center">
                                💬
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">+{{ $selectedPhone }}</h3>
                                <p class="text-[11px] text-slate-400">Direct WhatsApp Session &bull; E.164 verified</p>
                            </div>
                        </div>

                        {{-- Quick Job Alert Dispatch Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 flex items-center gap-1.5 transition"
                            >
                                ⚡ Send Job Alert &dtrif;
                            </button>

                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-cloak
                                class="absolute right-0 mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl z-20"
                            >
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1">Select Active Listing</p>
                                <div class="max-h-56 overflow-y-auto space-y-1">
                                    @foreach ($recentJobs as $job)
                                        <form method="POST" action="{{ route('admin.whatsapp.job-alert') }}">
                                            @csrf
                                            <input type="hidden" name="phone_number" value="{{ $selectedPhone }}">
                                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                                            <button
                                                type="submit"
                                                class="w-full text-left rounded-xl p-2 text-xs hover:bg-emerald-50 transition text-slate-800"
                                            >
                                                <div class="font-bold truncate">{{ $job->title }}</div>
                                                <div class="text-[10px] text-slate-500">{{ $job->company }} &bull; {{ $job->remote_type }}</div>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Chat Stream --}}
                    <div class="flex-1 p-5 overflow-y-auto space-y-3 bg-[#efeae2]/30 max-h-[380px]">
                        @forelse ($activeMessages as $msg)
                            @php
                                $isOutbound = $msg->isOutbound();
                            @endphp
                            <div class="flex {{ $isOutbound ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-xs shadow-2xs {{ $isOutbound ? 'bg-[#d9fdd3] text-slate-900 rounded-tr-none' : 'bg-white text-slate-800 rounded-tl-none border border-slate-200/60' }}">
                                    <div class="whitespace-pre-wrap leading-relaxed">{{ $msg->content }}</div>
                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-slate-400">
                                        <span>{{ $msg->created_at->format('H:i') }}</span>
                                        @if ($isOutbound)
                                            <span class="text-emerald-700 font-bold">&check;&check;</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">
                                No messages in this conversation yet. Send the first message below!
                            </div>
                        @endforelse
                    </div>

                    {{-- Reply Input Bar --}}
                    <div class="p-3 border-t border-slate-200/80 bg-white">
                        <form method="POST" action="{{ route('admin.whatsapp.send') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="phone_number" value="{{ $selectedPhone }}">
                            <input
                                type="text"
                                name="message"
                                required
                                placeholder="Type a WhatsApp reply to +{{ $selectedPhone }}…"
                                class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition"
                            />
                            <button
                                type="submit"
                                class="btn-pop rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-2xs hover:bg-emerald-700 transition shrink-0"
                            >
                                Send &rarr;
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                        <div class="h-16 w-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold mb-4">
                            💬
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No Conversation Selected</h3>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm">
                            Pick an existing conversation from the left, or click <strong>+ New Message</strong> to chat with a candidate or subscriber.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Configuration Guide Modal / Helper --}}
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 shadow-2xs text-xs text-slate-600">
            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-1.5 mb-1.5">
                <span>⚙</span> Meta WhatsApp Business Cloud API Integration Guide
            </h4>
            <p class="leading-relaxed">
                To connect your live WhatsApp Business account without physical SIM/phone hardware, add the following credentials to your <code class="rounded bg-slate-200/70 px-1 py-0.5 text-slate-800 font-mono">.env</code>:
            </p>
            <div class="mt-2 rounded-xl bg-slate-900 p-3 text-slate-200 font-mono text-[11px] space-y-1">
                <div>WHATSAPP_ACCESS_TOKEN="EAA..."</div>
                <div>WHATSAPP_PHONE_NUMBER_ID="1098..."</div>
                <div>WHATSAPP_BUSINESS_ACCOUNT_ID="1234..."</div>
                <div>WHATSAPP_WEBHOOK_VERIFY_TOKEN="krj-wa-verify-token"</div>
            </div>
            <p class="mt-2">
                Webhook URL to enter in Meta Developers portal: <strong class="text-slate-900 font-mono">{{ url('/webhooks/whatsapp') }}</strong> (Verify Token: <code class="font-mono">krj-wa-verify-token</code>).
            </p>
        </div>
    </div>

    {{-- New Chat Modal --}}
    <div id="newChatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Start New WhatsApp Conversation</h3>
                <button type="button" onclick="document.getElementById('newChatModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.whatsapp.send') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Candidate / Client Phone Number</label>
                    <input
                        type="text"
                        name="phone_number"
                        required
                        placeholder="e.g. 0712345678 or +254712345678"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                    />
                    <p class="mt-1 text-[10px] text-slate-400">Accepts both local Kenyan numbers (07...) and international E.164 (+254...).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Initial Message</label>
                    <textarea
                        name="message"
                        rows="3"
                        required
                        placeholder="Hello! Reaching out from KenyaRemoteJobs regarding your application..."
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button
                        type="button"
                        onclick="document.getElementById('newChatModal').classList.add('hidden')"
                        class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn-pop rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-2xs hover:bg-emerald-700"
                    >
                        Send Message &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
