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
            <span class="inline-flex items-center gap-1 rounded-full bg-sunrise-100 px-3 py-1 text-xs font-bold text-sunrise-800">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-sunrise-600" />
                Help &amp; Documentation
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-horizon-950 sm:text-4xl lg:text-5xl">
                Frequently Asked Questions
            </h1>
            <p class="mt-4 text-base text-foreground/70 sm:text-lg">
                Practical answers to common questions about landing legit remote work, receiving foreign currency payments, and using KenyaRemoteJobs.
            </p>
        </x-reveal>

        {{-- FAQ Categories Grid --}}
        <div class="mt-12 space-y-12">
            @foreach ($categories as $catKey => $category)
                <section id="{{ $catKey }}" class="rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-black/5 pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-horizon-100 text-horizon-800">
                            <x-icon :name="$category['icon']" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-horizon-950 sm:text-2xl">
                                {{ $category['title'] }}
                            </h2>
                            <p class="text-xs text-foreground/50">
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
        <div class="mt-16 rounded-3xl border border-horizon-200 bg-gradient-to-r from-horizon-900 via-horizon-800 to-slate-900 p-8 text-center text-white shadow-xl">
            <h2 class="text-2xl font-bold">Have a specific question not covered here?</h2>
            <p class="mt-2 text-sm text-white/70 max-w-lg mx-auto">
                Ask our Daisy AI Career Copilot in the Action Center (bottom right) or explore our curated career guides in The Journal.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/journal') }}" class="btn-pop rounded-full bg-sunrise-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-sunrise-600">
                    Read The Journal &rarr;
                </a>
                <a href="{{ url('/jobs') }}" class="rounded-full border border-white/20 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    Browse All Remote Jobs
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
