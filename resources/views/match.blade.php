<x-layouts.app
    title="Find Your Best-Matching Jobs"
    description="Pick your skills and the roles you want, and we'll score every live listing for how well it matches you — no CV upload, no AI, just a quick form."
>
    <div class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
        <h1 class="text-center text-3xl font-bold">Find your best-matching jobs</h1>
        <p class="mt-3 text-center text-foreground/60">
            No CV upload needed &mdash; pick your skills and the roles you&rsquo;re after, and every job listing shows a match score based on it. Takes about a minute.
        </p>

        <p class="mt-3 text-center text-sm text-slate-600">Explore matches for free before choosing membership. Scores reflect keyword overlap, not interview chances; review experience and eligibility in each listing. Employer identities remain hidden.</p>

        <div class="mt-8">
            <livewire:match-form />
        </div>

        <p class="mt-6 text-center text-xs text-foreground/40">
            Saved in this browser. When signed in, your choices are also saved to your account for job recommendations and email alerts. Nothing is shared with employers.
        </p>
    </div>
</x-layouts.app>
