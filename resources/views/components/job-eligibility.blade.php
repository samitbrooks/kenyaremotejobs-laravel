@props(['job'])
@php
    $signals = \App\Support\KenyaRelevance::score($job->only(['location', 'description']));
    $redactor = app(\App\Services\Redactor::class);
    $location = $redactor->redactEmployerIdentity($job->location ?: 'Not specified', $job->company);
@endphp
<section class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5" aria-label="Kenya eligibility signals">
    <h2 class="text-lg font-bold text-slate-900">Could this role work from Kenya?</h2>
    <p class="mt-2 text-sm font-semibold text-[#3444da]">{{ $signals['kenyaFriendly'] ? 'Potential Kenya fit — confirm before applying' : 'Kenya eligibility needs confirmation' }}</p>
    <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
        <div><dt class="font-semibold text-slate-900">Stated location</dt><dd class="text-slate-600">{{ $location }}</dd></div>
        <div><dt class="font-semibold text-slate-900">Listing updated on our site</dt><dd class="text-slate-600">{{ $job->updated_at?->timezone('Africa/Nairobi')->format('j M Y, H:i') }} EAT</dd></div>
    </dl>
    @if ($job->last_seen_at)
        <p class="mt-3 text-xs text-slate-600">Last seen in the live employer feed: {{ $job->last_seen_at->timezone('Africa/Nairobi')->format('j M Y, H:i') }} EAT. This confirms a feed listing, not employer endorsement of an application.</p>
    @endif
    <ul class="mt-3 space-y-1 text-xs text-slate-600">
        @forelse ($signals['reasons'] as $reason)
            <li>{{ $redactor->redactEmployerIdentity($reason, $job->company) }}</li>
        @empty
            <li>No clear location or timezone eligibility signal was found in this listing.</li>
        @endforelse
    </ul>
    <p class="mt-3 text-xs text-slate-500">These are automated wording checks, not confirmation from the employer. The update date records a site update, not a hiring check. Review location, working hours, experience, and current availability in the application portal. Employer identity remains hidden here.</p>
</section>
