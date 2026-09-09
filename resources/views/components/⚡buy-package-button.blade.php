<?php

use App\Livewire\Concerns\HasPendingPayment;
use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    use HasPendingPayment;

    public string $tier;

    public int $priceKes;

    public bool $isAuthed;

    public string $phone = '';

    public ?string $error = null;

    protected function paymentRedirectTo(): string
    {
        return '/pricing';
    }

    public function buy(PaymentService $payments): void
    {
        if (! $this->isAuthed) {
            $this->redirect('/account?next=/pricing', navigate: false);

            return;
        }

        $this->error = null;
        $this->paymentError = null;

        if (config('payments.default') === 'mpesa' && ! \App\Support\KenyanPhone::normalize($this->phone)) {
            $this->error = 'Enter a valid M-Pesa phone number (e.g. 07XXXXXXXX).';

            return;
        }

        try {
            $payment = $payments->purchaseCreditPackage(auth()->user(), $this->tier, $this->phone ?: null);
            if ($payment->isCompleted()) {
                $this->redirect($this->paymentRedirectTo(), navigate: false);
            } elseif ($payment->isPending()) {
                $this->pendingPaymentId = $payment->id;
            }
        } catch (\Throwable $e) {
            $this->error = config('payments.default') === 'mpesa' ? $e->getMessage() : 'Something went wrong. Please try again.';
        }
    }
};
?>

<div>
    @if ($pendingPaymentId)
        <div wire:poll.3s="checkPaymentStatus" class="rounded-2xl border border-sunrise-200 bg-sunrise-50 p-4 text-center text-sm">
            <p class="font-semibold text-sunrise-800">Check your phone</p>
            <p class="mt-1 text-foreground/60">Enter your M-Pesa PIN on the prompt sent to {{ $phone }} to complete the purchase.</p>
        </div>
    @else
        @if (config('payments.default') === 'mpesa' && $isAuthed)
            <input
                type="tel"
                wire:model="phone"
                placeholder="M-Pesa phone (07XXXXXXXX)"
                class="mb-2 w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            >
        @endif
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
    @endif
    @if ($error)
        <p class="mt-2 text-center text-sm text-red-600">{{ $error }}</p>
    @endif
    @if ($paymentError)
        <p class="mt-2 text-center text-sm text-red-600">{{ $paymentError }}</p>
    @endif
</div>
