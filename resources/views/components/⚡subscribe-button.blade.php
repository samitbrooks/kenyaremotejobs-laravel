<?php

use App\Livewire\Concerns\HasPendingPayment;
use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    use HasPendingPayment;

    public bool $isAuthed = false;

    public bool $alreadySubscribed = false;

    public string $period = 'pro';

    public string $phone = '';

    public ?string $error = null;

    public bool $showInput = false;

    public ?string $buttonLabel = null;

    public function mount(
        bool $isAuthed = false,
        bool $alreadySubscribed = false,
        string $period = 'pro',
        string $phone = '',
        ?string $buttonLabel = null
    ): void {
        $this->isAuthed = $isAuthed ?: auth()->check();
        $this->alreadySubscribed = $alreadySubscribed;
        $this->period = $period;
        $this->phone = $phone;
        $this->buttonLabel = $buttonLabel;
    }

    protected function paymentRedirectTo(): string
    {
        return '/pricing';
    }

    public function selectPeriod(string $period): void
    {
        $this->period = $period;
        $this->showInput = false;
        $this->error = null;
    }

    public function promptCheckout(): void
    {
        if (! auth()->check()) {
            $this->redirect('/account?next=/pricing?plan='.$this->period, navigate: false);

            return;
        }

        $this->showInput = true;
        $this->error = null;
    }

    public function subscribe(PaymentService $payments): void
    {
        if (! auth()->check()) {
            $this->redirect('/account?next=/pricing', navigate: false);

            return;
        }

        $this->error = null;
        $this->paymentError = null;

        if (config('payments.default') === 'mpesa' && ! \App\Support\KenyanPhone::normalize($this->phone)) {
            $this->error = 'Enter a valid M-Pesa phone number (e.g. 07XXXXXXXX).';
            $this->showInput = true;

            return;
        }

        try {
            $payment = $payments->subscribe(auth()->user(), $this->period, $this->phone ?: null);
            if ($payment->isCompleted()) {
                session()->flash('success', 'Your subscription is now active! Enjoy full access to verified remote jobs.');
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

@php
    $plans = config('jobs.subscription_plans', []);
    $currentPlan = $plans[$period] ?? ($plans['pro'] ?? ['price_kes' => 250, 'label' => 'Pro']);
    $price = $currentPlan['price_kes'] ?? 250;
    $label = $buttonLabel ?? ($currentPlan['button_text'] ?? 'Choose '.($currentPlan['label'] ?? 'Plan'));
    $btnClasses = match ($period) {
        'starter' => 'bg-slate-950 text-white hover:bg-slate-800',
        'pro', 'elite' => 'bg-[#00D47E] text-[#08291D] hover:bg-[#00BB6F] shadow-sm',
        default => 'bg-[#00D47E] text-[#08291D] hover:bg-[#00BB6F]',
    };
@endphp

<div class="w-full">
    @if ($alreadySubscribed)
        <button type="button" disabled class="w-full rounded-full border border-emerald-300 bg-emerald-50 px-5 py-2.5 text-[13px] font-extrabold text-emerald-800">
            You&rsquo;re Subscribed
        </button>
    @elseif ($pendingPaymentId)
        <div wire:poll.3s="checkPaymentStatus" class="rounded-xl border border-[#00D47E]/40 bg-[#00D47E]/10 p-4 text-center text-xs">
            <div class="flex items-center justify-center gap-1.5 font-bold text-[#08291D]">
                <span class="inline-block h-2 w-2 rounded-full bg-[#00D47E] animate-ping"></span>
                Check your phone
            </div>
            <p class="mt-1 text-slate-700 leading-snug">
                Enter your M-Pesa PIN on the prompt sent to <strong class="text-slate-900">{{ $phone }}</strong> to complete payment.
            </p>
        </div>
    @elseif ($showInput && $isAuthed)
        <div class="space-y-2 rounded-xl bg-slate-50 p-3 border border-slate-200 text-left">
            <label class="block text-[11px] font-bold text-slate-700">
                M-Pesa Phone Number:
            </label>
            <input
                type="tel"
                wire:model="phone"
                placeholder="07XXXXXXXX"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#00D47E] focus:outline-none focus:ring-1 focus:ring-[#00D47E]"
                autofocus
            >
            <button
                type="button"
                wire:click="subscribe"
                wire:loading.attr="disabled"
                wire:target="subscribe"
                class="w-full rounded-full px-5 py-2 text-[12.5px] font-extrabold transition {{ $btnClasses }} flex items-center justify-center gap-1.5 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="subscribe">Pay KES {{ number_format($price) }} with M-Pesa</span>
                <span wire:loading wire:target="subscribe">Prompting STK push&hellip;</span>
            </button>
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5">
                <span>Instant Safaricom STK Push</span>
                <button type="button" wire:click="$set('showInput', false)" class="hover:text-slate-600 underline">Cancel</button>
            </div>
            @if ($error)
                <p class="text-center text-xs text-rose-600 font-medium">{{ $error }}</p>
            @endif
            @if ($paymentError)
                <p class="text-center text-xs text-rose-600 font-medium">{{ $paymentError }}</p>
            @endif
        </div>
    @else
        <button
            type="button"
            wire:click="promptCheckout"
            class="w-full rounded-full px-5 py-2.5 text-[13px] font-extrabold transition {{ $btnClasses }}"
        >
            {{ $label }}
        </button>
        @if ($error)
            <p class="mt-2 text-center text-xs text-rose-600 font-medium">{{ $error }}</p>
        @endif
        @if ($paymentError)
            <p class="mt-2 text-center text-xs text-rose-600 font-medium">{{ $paymentError }}</p>
        @endif
    @endif
</div>
