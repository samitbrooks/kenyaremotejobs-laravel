@php
    $pillars = [
        ['icon' => 'search', 'title' => 'Radically searchable', 'body' => 'Titles, companies, and short descriptions are free to browse forever — no account, no paywall. Good for you, good for trust.'],
        ['icon' => 'kenya-flag', 'title' => 'Kenya-first scoring', 'body' => 'Every listing is checked for location wording, timezone overlap with EAT, and hidden US/EU-only restrictions before it earns a Kenya-Friendly badge.'],
        ['icon' => 'handshake', 'title' => 'Fair to our sources', 'body' => "We don't scrape and hide where a job came from. Every listing credits and links back to the original board, as their terms require."],
    ];
    $total = \App\Models\JobListing::visible()->count();
    $totalKenyaFriendly = \App\Models\JobListing::visible()->where('kenya_friendly', true)->count();
@endphp

<x-layouts.app
    title="About"
    description="Why KenyaRemoteJobs exists: closing the gap between global remote work and Kenyan jobseekers, one Kenya-Friendly Match at a time."
>
    <section class="gradient-sunrise-live relative overflow-hidden px-4 py-20 text-white sm:px-6">
        <div class="animate-float pointer-events-none absolute -right-10 top-6 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="animate-float pointer-events-none absolute -left-6 bottom-0 h-48 w-48 rounded-full bg-white/10 blur-2xl [animation-delay:2s]"></div>

        <x-reveal class="relative mx-auto max-w-3xl text-center">
            <h1 class="text-3xl font-bold leading-tight sm:text-5xl">
                Built for the jobseeker in Nairobi, Kisumu, and every county in between.
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg text-white/90">
                Most remote job boards say &ldquo;work from anywhere&rdquo; and mean &ldquo;anywhere in the US.&rdquo; We built the one that actually checks.
            </p>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <x-reveal>
                <div class="h-full rounded-2xl border-2 border-dashed border-black/10 bg-white p-6">
                    <p class="text-sm font-semibold uppercase tracking-wide text-foreground/40">The problem</p>
                    <p class="mt-3 text-lg leading-relaxed text-foreground/70">
                        Listings say &ldquo;remote&rdquo; and then bury a &ldquo;US applicants only&rdquo; clause three paragraphs down. Timezone requirements assume everyone is a few hours from New York. You find out you never qualified after the application.
                    </p>
                </div>
            </x-reveal>
            <x-reveal :delay="120">
                <div class="h-full rounded-2xl bg-horizon-900 p-6 text-white shadow-lg">
                    <p class="text-sm font-semibold uppercase tracking-wide text-sunrise-300">The fix</p>
                    <p class="mt-3 text-lg leading-relaxed text-white/90">
                        We pull listings from established job boards and score every single one for how realistically open it is to East Africa &mdash; location, timezone overlap, and visa restrictions included &mdash; before you ever click apply.
                    </p>
                </div>
            </x-reveal>
        </div>
    </section>

    <section class="bg-horizon-50 px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-5xl">
            <x-reveal>
                <h2 class="mb-10 text-center text-2xl font-bold">What we stand for</h2>
            </x-reveal>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                @foreach ($pillars as $i => $pillar)
                    <x-reveal :delay="$i * 120" class="text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full gradient-sunrise shadow-md">
                            @if ($pillar['icon'] === 'kenya-flag')
                                <x-icon.kenya-flag class="h-6 w-6" />
                            @else
                                <x-icon :name="$pillar['icon']" class="h-6 w-6 text-white" />
                            @endif
                        </div>
                        <h3 class="mb-2 font-semibold">{{ $pillar['title'] }}</h3>
                        <p class="text-sm text-foreground/60">{{ $pillar['body'] }}</p>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6">
        <x-reveal>
            <div class="grid grid-cols-1 gap-6 rounded-3xl border border-black/5 bg-white p-8 shadow-sm sm:grid-cols-3 sm:p-10">
                <div>
                    <p class="text-4xl font-bold text-sunrise-600"><x-count-up :value="$total" /></p>
                    <p class="mt-1 text-sm text-foreground/60">live listings right now</p>
                </div>
                <div>
                    <p class="text-4xl font-bold text-sunrise-600">6</p>
                    <p class="mt-1 text-sm text-foreground/60">job boards aggregated</p>
                </div>
                <div>
                    <p class="text-4xl font-bold text-sunrise-600"><x-count-up :value="$totalKenyaFriendly" /></p>
                    <p class="mt-1 text-sm text-foreground/60">Kenya-Friendly Match badges live now</p>
                </div>
            </div>
        </x-reveal>
    </section>

    <section class="mx-auto max-w-2xl px-4 pb-20 text-center sm:px-6">
        <x-reveal>
            <p class="text-2xl font-semibold text-gradient-sunrise">
                Work from Kenya. Work for the world. That&rsquo;s the whole idea.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ url('/jobs') }}" class="btn-pop inline-block rounded-full gradient-sunrise px-8 py-3 font-semibold text-white shadow-lg transition hover:opacity-90">
                    Start browsing jobs &rarr;
                </a>
                <a href="{{ url('/pricing') }}" class="btn-pop inline-block rounded-full border border-black/10 px-8 py-3 font-semibold text-foreground transition hover:bg-black/5">
                    See pricing
                </a>
            </div>
        </x-reveal>
    </section>
</x-layouts.app>
