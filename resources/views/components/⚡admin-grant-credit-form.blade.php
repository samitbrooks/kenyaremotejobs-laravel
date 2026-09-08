<?php

use App\Models\CreditPurchase;
use Livewire\Component;

new class extends Component
{
    public string $userId;

    public string $tier = 'basic';

    public int $credits = 1;

    // Recorded the same way a real purchase is (amount_kes just stays 0,
    // same as a comped/manual grant already implied by the credits ledger
    // being sum(purchased) - count(spent), never a stored balance) — so it
    // shows up in the user's own purchase history exactly like a paid one,
    // just free. Redirects back to the same page afterward so the plain
    // Blade-rendered balance grid above this form picks up the change —
    // this component's own state isn't what displays it.
    public function grant(): void
    {
        CreditPurchase::create([
            'user_id' => $this->userId,
            'tier' => $this->tier,
            'credits' => $this->credits,
            'amount_kes' => 0,
            'purchased_at' => now(),
        ]);

        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<form wire:submit="grant" class="flex flex-wrap items-end gap-3">
    <label class="text-sm">
        <span class="mb-1 block font-medium text-foreground/70">Tier</span>
        <select wire:model="tier" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            @foreach (config('jobs.tiers') as $t)
                <option value="{{ $t }}">{{ config('jobs.tier_labels')[$t] }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm">
        <span class="mb-1 block font-medium text-foreground/70">Credits</span>
        <input type="number" wire:model="credits" min="1" max="20" class="w-20 rounded-lg border border-black/10 px-3 py-2 text-sm">
    </label>
    <button type="submit" wire:loading.attr="disabled" wire:target="grant" class="btn-pop rounded-full bg-sunrise-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sunrise-600 disabled:opacity-60">
        <span wire:loading.remove wire:target="grant">Grant credits</span>
        <span wire:loading wire:target="grant">Granting&hellip;</span>
    </button>
</form>
