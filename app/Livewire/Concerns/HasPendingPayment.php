<?php

namespace App\Livewire\Concerns;

use App\Models\Payment;

/**
 * Shared by every Livewire purchase component (credit packages,
 * subscriptions, employer job posting). With a real gateway, initiate()
 * leaves the Payment 'pending' and returns immediately — the buyer approves
 * an STK push on their phone some seconds-to-minutes later, resolved async
 * by Webhooks\MpesaCallbackController. This polls the Payment row itself
 * (wire:poll on the component's Blade view) rather than needing any
 * push/broadcast mechanism.
 */
trait HasPendingPayment
{
    public ?string $pendingPaymentId = null;

    public ?string $paymentError = null;

    public function checkPaymentStatus(): void
    {
        if (! $this->pendingPaymentId) {
            return;
        }

        $payment = Payment::find($this->pendingPaymentId);

        if (! $payment || $payment->isFailed()) {
            $this->pendingPaymentId = null;
            $this->paymentError = "The payment wasn't completed — it may have timed out or been cancelled on your phone. Please try again.";

            return;
        }

        if ($payment->isCompleted()) {
            $this->pendingPaymentId = null;
            $this->redirect($this->paymentRedirectTo(), navigate: false);
        }

        // Still pending: nothing to do, the next wire:poll tick checks again.
    }

    abstract protected function paymentRedirectTo(): string;
}
