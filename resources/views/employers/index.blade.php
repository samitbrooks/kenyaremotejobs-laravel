@php
    $steps = [
        ['title' => 'Create an account', 'body' => 'Sign up with your work email — no card required to get started.'],
        ['title' => 'Build the job post', 'body' => 'Title, description, apply link, and the skills you need. Takes a few minutes.'],
        ['title' => 'Choose a plan', 'body' => 'Basic or Boost — pick how much visibility the role needs.'],
        ['title' => 'Manage from your dashboard', 'body' => "See every posting you've made and take one down whenever you're done hiring."],
    ];

    $reasons = [
        ['icon' => 'target', 'title' => 'A focused audience, not a firehose', 'body' => "Every visitor is here specifically because they're looking for Kenya- and East Africa-friendly work — not a generic global feed you're competing for attention in."],
        ['icon' => 'clock', 'title' => 'Timezone-aware by default', 'body' => 'Candidates already filter by EAT overlap before they ever reach your posting — fewer applicants from timezones that were never going to work.'],
        ['icon' => 'link', 'title' => 'Direct application routes', 'body' => 'Candidates apply straight through your link — no inbox to manage on our end, no middleman.'],
    ];
@endphp

<x-layouts.app
    title="Hire Remote Talent in Kenya & East Africa"
    description="Publish a remote role and reach candidates specifically looking for Kenya- and East Africa-friendly work — not lost in a generic global job board."
>
    <section class="relative bg-white px-4 pb-16 pt-16 sm:px-6 sm:pt-20">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-horizon-100 blur-3xl"></div>
            <div class="absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-sunrise-100 blur-3xl"></div>
        </div>

        <x-reveal class="relative mx-auto max-w-3xl text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-horizon-700">&mdash; For Employers</p>
            <h1 class="mt-3 text-4xl font-bold leading-[1.1] text-horizon-900 sm:text-5xl">
                Hire remote talent in <span class="text-sunrise-500">Kenya &amp; East Africa.</span>
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg text-foreground/70">
                Publish a role and reach candidates who are already looking for exactly what you&rsquo;re offering &mdash; not lost in a generic global job board.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/employers/post') }}" class="btn-pop rounded-full bg-sunrise-500 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-sunrise-600">
                    Post a job &rarr;
                </a>
                <a href="{{ url('/employers/dashboard') }}" class="rounded-full border-2 border-horizon-800 px-6 py-3 font-semibold text-horizon-800 transition hover:bg-horizon-50">
                    View dashboard
                </a>
            </div>
        </x-reveal>
    </section>

    <section class="bg-horizon-50 px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-reveal>
                <h2 class="mb-10 text-center text-2xl font-bold">How it works</h2>
            </x-reveal>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $i => $step)
                    <x-reveal :delay="$i * 100" class="text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full gradient-sunrise text-lg font-bold text-white shadow-md">
                            {{ $i + 1 }}
                        </div>
                        <h3 class="mb-2 font-semibold">{{ $step['title'] }}</h3>
                        <p class="text-sm text-foreground/60">{{ $step['body'] }}</p>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <x-reveal class="mx-auto mb-10 max-w-2xl text-center">
            <h2 class="text-2xl font-bold">Why post here instead of a generic board</h2>
        </x-reveal>
        <div class="grid gap-6 sm:grid-cols-3">
            @foreach ($reasons as $i => $r)
                <x-reveal :delay="$i * 120">
                    <div class="card-hover h-full rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-horizon-50">
                            <x-icon :name="$r['icon']" class="h-5 w-5 text-horizon-600" />
                        </span>
                        <h3 class="mt-3 font-semibold">{{ $r['title'] }}</h3>
                        <p class="mt-2 text-sm text-foreground/60">{{ $r['body'] }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>

    <section class="bg-horizon-50 px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <x-reveal>
                <h2 class="mb-10 text-center text-2xl font-bold">Plans</h2>
            </x-reveal>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (config('jobs.posting_plans') as $key => $pkg)
                    <x-reveal>
                        <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-sunrise-600">
                                {{ $pkg['label'] }}
                                @if ($pkg['featured'])
                                    <span class="ml-2 rounded-full bg-sunrise-500 px-2 py-0.5 text-[10px] text-white">Most visibility</span>
                                @endif
                            </p>
                            <p class="mt-1 text-3xl font-bold">
                                KES {{ number_format($pkg['price_kes']) }}
                                <span class="text-sm font-medium text-foreground/50"> &middot; {{ $pkg['listing_days'] }}-day listing</span>
                            </p>
                            <ul class="mt-3 space-y-1 text-sm text-foreground/60">
                                @foreach ($pkg['features'] ?? [] as $feature)
                                    <li class="flex items-start gap-1.5"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-600" /> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    <section class="px-4 pb-16 sm:px-6">
        <x-reveal class="mx-auto max-w-6xl rounded-3xl bg-horizon-900 px-8 py-12 text-center text-white">
            <h2 class="text-2xl font-bold sm:text-3xl">Ready to reach the right candidates?</h2>
            <p class="mx-auto mt-3 max-w-xl text-white/70">Publishing takes a few minutes, and there's no card required to get started.</p>
            <div class="mt-6">
                <a href="{{ url('/employers/post') }}" class="btn-pop inline-block rounded-full bg-sunrise-500 px-6 py-3 font-semibold text-white transition hover:bg-sunrise-600">
                    Post a job &rarr;
                </a>
            </div>
        </x-reveal>
    </section>
</x-layouts.app>
