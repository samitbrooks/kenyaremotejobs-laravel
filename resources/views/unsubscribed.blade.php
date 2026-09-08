<x-layouts.app title="Unsubscribed">
    <div class="mx-auto max-w-md px-4 py-20 text-center sm:px-6">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
            <x-icon name="check" class="h-5 w-5 text-emerald-700" />
        </div>
        <h1 class="mt-4 text-2xl font-bold">You&rsquo;re unsubscribed</h1>
        <p class="mt-2 text-foreground/60">
            {{ $email }} won&rsquo;t receive marketing emails from KenyaRemoteJobs anymore. You&rsquo;ll still get essential account emails (like sign-in confirmations), since those aren&rsquo;t marketing.
        </p>
        <a href="{{ url('/') }}" class="btn-pop mt-6 inline-block rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90">
            Back to KenyaRemoteJobs
        </a>
    </div>
</x-layouts.app>
