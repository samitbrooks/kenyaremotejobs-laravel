<x-layouts.admin title="Admin: Edit Job">
    <h1 class="text-2xl font-bold">Edit job</h1>
    <p class="mt-1 text-sm text-foreground/50">{{ $job->title }} &middot; {{ $job->company }}</p>

    <div class="mt-6">
        <livewire:admin-edit-job-form :job="$job" />
    </div>
</x-layouts.admin>
