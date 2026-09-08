<x-layouts.admin title="Admin: Email">
    <h1 class="text-2xl font-bold">Email users</h1>
    <p class="mt-1 max-w-xl text-sm text-foreground/50">
        Send a one-off message to everyone with an account, or just full-access / pay-per-job accounts.
    </p>

    <div class="mt-6 max-w-2xl">
        <livewire:admin-email-composer :counts="$counts" :configured="$configured" />
    </div>
</x-layouts.admin>
