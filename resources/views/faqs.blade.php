<x-layouts.app
    title="Frequently Asked Questions (FAQs)"
    description="Everything you need to know about finding legitimate remote jobs in Kenya, receiving international payments via M-Pesa or Wise, and avoiding online scams."
    :canonical="url('/faqs')"
>
    <script type="application/ld+json">{!! \App\Support\Seo::faqJsonLd($allFaqs) !!}</script>

    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80">FAQs</span>
        </nav>

        {{-- Hero Header --}}
        <x-reveal class="text-center max-w-2xl mx-auto">
            <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200 px-3 py-1 text-xs font-bold text-teal-800">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" />
                Help &amp; Documentation
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                Frequently Asked Questions
            </h1>
            <p class="mt-4 text-base text-slate-600 sm:text-lg">
                Practical answers to common questions about landing legit remote work, receiving foreign currency payments, and using KenyaRemoteJobs.
            </p>
        </x-reveal>

        {{-- FAQ Categories Grid --}}
        <div class="mt-12 space-y-12">
            @foreach ($categories as $catKey => $category)
                <section id="{{ $catKey }}" class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-10 shadow-sm">
                    <div class="flex items-center gap-3.5 border-b border-slate-100 pb-5">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-50 text-teal-700 border border-teal-100 shadow-2xs">
                            <x-icon :name="$category['icon']" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
                                {{ $category['title'] }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ count($category['items']) }} questions answered
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($category['items'] as $faq)
                            <x-faq-item :question="$faq['question']" :answer="$faq['answer']" />
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{-- Still have questions CTA --}}
        <div class="mt-16 rounded-3xl border border-slate-800 bg-slate-900 p-8 sm:p-12 text-center text-white shadow-2xl">
            <h2 class="text-2xl sm:text-3xl font-bold">Have a specific question not covered here?</h2>
            <p class="mt-2.5 text-sm text-slate-300 max-w-lg mx-auto leading-relaxed">
                Daisy AI, our free Kenyan contractor advisor, can answer questions about tax withholding, Wise accounts, or tailoring your CV.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/pricing') }}" class="btn-pop rounded-full bg-teal-600 px-8 py-3.5 text-sm font-bold text-white shadow-md shadow-teal-600/20 hover:bg-teal-700 transition">
                    Explore Pro Membership &rarr;
                </a>
                <a href="{{ url('/journal') }}" class="rounded-full border border-slate-700 bg-slate-800/80 px-8 py-3.5 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                    Read The Journal &rarr;
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
