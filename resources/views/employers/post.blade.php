<x-layouts.app title="Post a Job" description="Publish a remote role to KenyaRemoteJobs and reach candidates across Kenya and East Africa.">
    @if (! $user)
        <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
            <h1 class="mb-2 text-center text-3xl font-bold">Post a job</h1>
            <p class="mb-8 text-center text-foreground/60">
                Create a free account first &mdash; you&rsquo;ll pick a plan and publish right after.
            </p>
            <livewire:auth-forms :redirect-to="$redirectTo" />
        </div>
    @else
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            <h1 class="text-3xl font-bold">Post a job</h1>
            <p class="mt-2 text-foreground/60">
                Reach candidates who are specifically looking for Kenya- and East Africa-friendly roles.
            </p>
            <div class="mt-8">
                <livewire:employer-post-form />
            </div>
        </div>
    @endif
</x-layouts.app>
