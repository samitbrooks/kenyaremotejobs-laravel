<x-layouts.app
    title="Find Your Best-Matching Jobs"
    description="Pick your skills and the roles you want, and we'll score every live listing for how well it matches you — no CV upload, no AI, just a quick form."
>
    <div class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
        <h1 class="text-center text-3xl font-bold">Find your best-matching jobs</h1>
        <p class="mt-3 text-center text-foreground/60">
            No CV upload needed &mdash; pick your skills and the roles you&rsquo;re after, and every job listing shows a match score based on it. Takes about a minute.
        </p>

        <div class="mt-8">
            <livewire:match-form />
        </div>

        <p class="mt-6 text-center text-xs text-foreground/40">
            Stored in your browser only, used to score listings you view. Nothing is shared with employers.
        </p>
    </div>
</x-layouts.app>
