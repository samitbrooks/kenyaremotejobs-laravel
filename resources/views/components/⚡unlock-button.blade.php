<?php

use App\Models\JobUnlock;
use App\Services\CreditsService;
use Livewire\Component;

new class extends Component
{
    public string $jobId;

    public string $tier;

    public int $remainingCredits;

    public int $packagePriceKes;

    public bool $isAuthed;

    public ?string $error = null;

    public bool $unlocked = false;

    // Unlocking doesn't take a payment directly — it spends one credit from
    // a package the user already bought (see the pricing page). "Spending"
    // a credit is just recording the job_unlocks row; the remaining balance
    // is derived from that against credit_purchases, so there's nothing
    // else to decrement here.
    public function unlock(CreditsService $credits): void
    {
        if (! $this->isAuthed) {
            $this->redirect("/account?next=/jobs/{$this->jobId}", navigate: false);

            return;
        }

        $this->error = null;
        $user = auth()->user();

        if (! $credits->hasAvailableCredit($user, $this->tier)) {
            $this->error = "No {$this->tier} credits available. Buy a package first.";

            return;
        }

        JobUnlock::create([
            'user_id' => $user->id,
            'job_listing_id' => $this->jobId,
            'tier' => $this->tier,
            'amount_kes' => 0,
            'unlocked_at' => now(),
        ]);

        $this->unlocked = true;
        $this->redirect("/jobs/{$this->jobId}", navigate: false);
    }
};
?>

<div>
    @if ($isAuthed && $remainingCredits <= 0)
        <a href="{{ url('/pricing') }}" class="btn-pop block w-full rounded-full gradient-sunrise px-8 py-3.5 text-center font-semibold text-white shadow-lg transition hover:opacity-90">
            Buy a {{ config('jobs.tier_labels')[$tier] }} package &mdash; KES {{ number_format($packagePriceKes) }}
        </a>
        <p class="mt-3 text-center text-xs text-foreground/40">
            You&rsquo;re out of {{ config('jobs.tier_labels')[$tier] }} credits. A package covers more than one unlock.
        </p>
    @else
        <button
            type="button"
            wire:click="unlock"
            wire:loading.attr="disabled"
            wire:target="unlock"
            class="btn-pop flex w-full items-center justify-center gap-2 rounded-full gradient-sunrise px-8 py-3.5 font-semibold text-white shadow-lg transition hover:opacity-90 disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="unlock">{{ $isAuthed ? "Unlock with 1 ".config('jobs.tier_labels')[$tier]." credit ({$remainingCredits} left)" : 'Log in to unlock' }}</span>
            <span wire:loading wire:target="unlock">Unlocking&hellip;</span>
        </button>
        @if ($error)
            <p class="mt-2 text-center text-sm text-red-600">{{ $error }}</p>
        @endif
        <p class="mt-3 text-center text-xs text-foreground/40">
            Payments aren&rsquo;t live yet &mdash; buying a package today is free while we finish setup.
        </p>
    @endif
</div>
