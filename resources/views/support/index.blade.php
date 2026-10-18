<x-layouts.app
    title="Help & Support Desk — Raise a Support Ticket"
    description="Need help with your M-Pesa subscription, remote applications, or employer account? Submit a support ticket directly to our team."
    :canonical="url('/support')"
>
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:py-16">
        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-[#3444da] transition">Home</a>
            <span>&rsaquo;</span>
            <span class="text-slate-800">Support Desk</span>
        </nav>

        {{-- Hero Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eef0fe] border border-[#c7ccfb] px-3.5 py-1 text-xs font-bold text-[#3444da]">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-[#3444da]" />
                Customer & Candidate Help Desk
            </span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                How can our team help you today?
            </h1>
            <p class="mt-2.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                Have a question regarding an M-Pesa subscription, remote role verification, or account access? Submit a ticket below and an advisor will respond directly.
            </p>
        </div>

        {{-- Notifications --}}
        @if (session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm font-semibold text-emerald-900 shadow-2xs flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">&check;</span>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-8 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm font-semibold text-rose-900 shadow-2xs flex items-center gap-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-600 text-white font-bold">&times;</span>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Ticket Submission Form (8 cols) --}}
            <div class="lg:col-span-8 rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-10 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Open a New Support Ticket</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Please provide as much context as possible for faster resolution.</p>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-[#3444da] bg-[#eef0fe] px-2.5 py-1 rounded-full border border-[#c7ccfb]">
                        Typical reply: &lt; 2 hrs
                    </span>
                </div>

                <form method="POST" action="{{ route('support.store') }}" class="space-y-6">
                    @csrf

                    {{-- Honeypot anti-spam --}}
                    <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                    {{-- Name & Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Your Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $prefillName) }}"
                                required
                                placeholder="e.g. Dennis Kipchumba"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition"
                            />
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $prefillEmail) }}"
                                required
                                placeholder="e.g. dennis@example.com"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition"
                            />
                            @error('email')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Category Picker --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Ticket Category <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            @php
                                $categories = [
                                    ['id' => 'payment', 'label' => 'M-Pesa / Payment', 'desc' => 'STK push, receipts, activations'],
                                    ['id' => 'jobs', 'label' => 'Job Applications', 'desc' => 'Direct links, expired listings'],
                                    ['id' => 'account', 'label' => 'Account / Login', 'desc' => 'Magic link, profile access'],
                                    ['id' => 'employer', 'label' => 'Employer / Hiring', 'desc' => 'Job posting packages, ATS'],
                                    ['id' => 'technical', 'label' => 'Bug / Technical', 'desc' => 'Site issues, resume builder'],
                                    ['id' => 'general', 'label' => 'General Inquiry', 'desc' => 'Other inquiries or feedback'],
                                ];
                            @endphp

                            @foreach ($categories as $cat)
                                <label class="relative flex flex-col justify-between rounded-2xl border p-3 cursor-pointer transition {{ old('category', $selectedCategory) === $cat['id'] ? 'border-[#3444da] bg-[#eef0fe]/40 ring-1 ring-[#3444da]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-900">{{ $cat['label'] }}</span>
                                        <input
                                            type="radio"
                                            name="category"
                                            value="{{ $cat['id'] }}"
                                            class="text-[#3444da] focus:ring-[#3444da]"
                                            @checked(old('category', $selectedCategory) === $cat['id'])
                                        >
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-1 leading-tight">{{ $cat['desc'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Priority & Subject --}}
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="sm:col-span-3">
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Subject / Summary <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                value="{{ old('subject') }}"
                                required
                                placeholder="e.g. M-Pesa confirmed but subscription not reflecting"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition"
                            />
                            @error('subject')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Priority
                            </label>
                            <select
                                id="priority"
                                name="priority"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition"
                            >
                                <option value="low" @selected(old('priority') === 'low')>Low</option>
                                <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                                <option value="high" @selected(old('priority') === 'high')>High</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                            </select>
                        </div>
                    </div>

                    {{-- Detailed Message --}}
                    <div>
                        <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Detailed Message <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            placeholder="Please provide details, error messages, M-Pesa transaction codes (e.g. QK89...), or open role titles so we can help quickly."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-900 focus:bg-white focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da] transition leading-relaxed"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit CTA --}}
                    <div class="pt-2 flex items-center justify-between">
                        <p class="text-xs text-slate-500">
                            You will receive a unique ticket reference and email updates.
                        </p>
                        <button
                            type="submit"
                            class="btn-pop inline-flex items-center gap-2 rounded-full bg-[#3444da] hover:bg-[#2633bf] px-8 py-3.5 text-sm font-bold text-white shadow-lg transition"
                        >
                            <span>Submit Support Ticket</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sidebar (4 cols) --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Track Existing Ticket Box --}}
                <div class="rounded-3xl border border-slate-200/90 bg-[#f8f9fd] p-6 shadow-2xs">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white border border-slate-200 text-[#3444da] shadow-2xs">
                            <x-icon name="document" class="h-4 w-4 text-[#3444da]" />
                        </span>
                        <h3 class="text-base font-bold text-slate-900">Check Existing Ticket</h3>
                    </div>
                    <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                        Already submitted a request? Enter your ticket number (e.g. KRJ-TKT-ABC123) and email to check its progress.
                    </p>

                    @if (session('lookup_error'))
                        <div class="mb-3 rounded-xl border border-rose-200 bg-rose-50 p-2.5 text-xs font-semibold text-rose-800">
                            {{ session('lookup_error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('support.lookup') }}" class="space-y-3">
                        @csrf
                        <div>
                            <input
                                type="text"
                                name="ticket_number"
                                placeholder="Ticket # (e.g. KRJ-TKT-XYZ123)"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#3444da] focus:outline-none"
                            />
                        </div>
                        <div>
                            <input
                                type="email"
                                name="email"
                                placeholder="Your ticket email"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#3444da] focus:outline-none"
                            />
                        </div>
                        <button
                            type="submit"
                            class="btn-pop w-full rounded-xl bg-slate-900 hover:bg-black px-4 py-2.5 text-xs font-bold text-white shadow-xs transition"
                        >
                            View Ticket Status &rarr;
                        </button>
                    </form>
                </div>

                {{-- Direct Support Office Hours --}}
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-2xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-1.5">
                        <x-icon name="clock" class="h-4 w-4 text-[#3444da]" /> Support Operating Hours
                    </h3>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex justify-between pb-1.5 border-b border-slate-100">
                            <span class="font-medium">Monday &ndash; Friday:</span>
                            <span class="font-bold text-slate-900">8:00 AM &ndash; 8:00 PM EAT</span>
                        </li>
                        <li class="flex justify-between pb-1.5 border-b border-slate-100">
                            <span class="font-medium">Saturday &ndash; Sunday:</span>
                            <span class="font-bold text-slate-900">9:00 AM &ndash; 4:00 PM EAT</span>
                        </li>
                        <li class="flex justify-between pt-1">
                            <span class="font-medium">Official Email:</span>
                            <a href="mailto:support@kenyaremotejobs.com" class="font-bold text-[#3444da] hover:underline">
                                support@kenyaremotejobs.com
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Instant Answers Quick Links --}}
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-2xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Quick Help Guides</h3>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li>
                            <a href="{{ url('/faqs') }}#mpesa" class="flex items-center justify-between text-slate-800 hover:text-[#3444da] font-medium transition">
                                <span>M-Pesa payment troubleshooting</span>
                                <span>&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/faqs') }}#trials" class="flex items-center justify-between text-slate-800 hover:text-[#3444da] font-medium transition">
                                <span>Free 24h pass activation rules</span>
                                <span>&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/journal') }}" class="flex items-center justify-between text-slate-800 hover:text-[#3444da] font-medium transition">
                                <span>Wise, KRA tax &amp; remote guide</span>
                                <span>&rarr;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
