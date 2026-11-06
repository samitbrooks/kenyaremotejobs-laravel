<?php

use Livewire\Component;

new class extends Component
{
    public bool $emailAlerts = false;

    public bool $saved = false;

    public function mount(): void
    {
        $this->emailAlerts = auth()->user()?->receivesMarketingEmail() ?? false;
    }

    public function save(): void
    {
        abort_unless(auth()->check(), 403);
        $this->validate(['emailAlerts' => ['boolean']]);
        auth()->user()->update(['marketing_opt_out_at' => $this->emailAlerts ? null : now()]);
        $this->saved = true;
    }
};
?>

<section id="alerts" class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
    <h2 class="text-xl font-bold text-slate-900">Your next opportunity, your way</h2>
    <p class="mt-2 text-sm text-slate-600">Save your skills and preferred roles to get relevant email recommendations. Listings confirmed in live employer feeds stay available; other imports expire after 30 days; check availability with the employer when applying.</p>
    <a href="{{ url('/match') }}" class="mt-3 inline-block text-sm font-bold text-[#3444da]">Update my job preferences &rarr;</a>
    <form wire:submit="save" class="mt-4 space-y-3">
        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" wire:model="emailAlerts" class="rounded border-slate-300 text-[#3444da]">
            Email me new role recommendations and career updates. I can unsubscribe anytime.
        </label>
        <button type="submit" wire:loading.attr="disabled" class="rounded-full bg-[#3444da] px-5 py-2 text-sm font-bold text-white">Save email preference</button>
        @if ($saved) <p role="status" class="text-sm text-emerald-700">Your email preference has been saved.</p> @endif
    </form>
    <div class="mt-5 border-t border-slate-100 pt-4 text-sm text-slate-600">
        <p>Prefer WhatsApp? Follow our shared jobs channel. Channel updates are separate from personalised email alerts.</p>
        @if (config('site.whatsapp_channel_url'))
        <a href="{{ config('site.whatsapp_channel_url') }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block font-bold text-[#3444da]">Choose WhatsApp channel updates &rarr;</a>
        @else
            <a href="{{ route('support.index') }}" class="mt-2 inline-block font-bold text-[#3444da]">Ask support about WhatsApp alerts &rarr;</a>
        @endif
    </div>
</section>
