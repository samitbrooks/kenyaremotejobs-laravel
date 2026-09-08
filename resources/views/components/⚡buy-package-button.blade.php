<?php

use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    public string $tier;

    public int $priceKes;

    public bool $isAuthed;

    public ?string $error = null;

    public function buy(PaymentService $payments): void
    {
        if (! $this->isAuthed) {
            $this->redirect('/account?next=/pricing', navigate: false);

            return;
        }

        $this->error = null;

        try {
            $payment = $payments->purchaseCreditPackage(auth()->user(), $this->tier);
            if ($payment->isCompleted()) {
                $this->redirect('/pricing', navigate: false);
            }
        } catch (\Throwable) {
            $this->error = 'Something went wrong. Please try again.';
        }
    }
};
?>

<div>
    <button
        type="button"
        wire:click="buy"
        wire:loading.attr="disabled"
        wire:target="buy"
        class="btn-pop flex w-full items-center justify-center gap-2 rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90 disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="buy">{{ $isAuthed ? 'Buy for KES '.number_format($priceKes) : 'Log in to buy' }}</span>
        <span wire:loading wire:target="buy">Processing&hellip;</span>
    </button>
    @if ($error)
        <p class="mt-2 text-center text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
