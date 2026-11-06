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
        return '/account#search-plan';
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

        if ($this->pendingPaymentId) {
            $this->checkPaymentStatus();

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
            } else {
                $this->error = 'The payment was not completed. Check your number and try again. If M-Pesa deducted money, contact support before paying again.';
                $this->showInput = true;
            }
        } catch (\Throwable $e) {
            $this->error = 'We could not start your payment. Check your phone number and try again. If money was deducted, contact support before paying again.';
            $this->showInput = true;
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
        'starter' => 'bg-slate-900 text-white hover:bg-slate-800',
        'pro', 'elite' => 'bg-[#3444da] text-white hover:bg-[#2633bf]',
        default => 'bg-[#3444da] text-white hover:bg-[#2633bf]',
    };
@endphp

<div class="w-full">
    @if ($alreadySubscribed)
        <button type="button" disabled class="w-full rounded-lg border border-[#c7ccfb] bg-[#eef0fe] px-5 py-2.5 text-[13px] font-semibold text-[#3444da]">
            You&rsquo;re Subscribed
        </button>
    @elseif ($pendingPaymentId)
        <div wire:poll.3s="checkPaymentStatus" class="rounded-xl border border-[#c7ccfb] bg-[#eef0fe] p-4 text-center text-xs">
            <div class="flex items-center justify-center gap-1.5 font-bold text-[#3444da]">
                <span class="inline-block h-2 w-2 rounded-full bg-[#3444da]"></span>
                Check your phone
            </div>
            <p role="status" class="mt-1 text-slate-700 leading-snug">
                Enter your M-Pesa PIN on the prompt sent to <strong class="text-slate-900">{{ $phone }}</strong> to complete payment.
            </p>
            <p class="mt-3 text-slate-600">Waiting for payment confirmation. If the prompt is delayed or money was deducted, keep this page open and contact support before paying again.</p>
            <button type="button" wire:click="checkPaymentStatus" wire:loading.attr="disabled" class="mt-3 font-bold text-[#3444da] underline">Check payment status</button>
            <a href="{{ route('support.index') }}" class="ml-3 font-bold text-[#3444da] underline">Get payment help</a>
        </div>
    @elseif ($showInput && $isAuthed)
        <div class="space-y-2 rounded-xl bg-slate-50 p-3 border border-slate-200 text-left">
            <p class="text-sm font-bold text-slate-900">Total: KES {{ number_format($price) }} for {{ $currentPlan['months'] ?? 1 }} {{ ($currentPlan['months'] ?? 1) === 1 ? 'month' : 'months' }} of Pro</p>
            <p class="text-xs text-slate-600">One payment. No automatic renewal. Access activates after payment confirmation.</p>
            <a href="{{ route('pricing') }}#refund-policy" class="inline-block text-xs font-semibold text-[#3444da] underline">Read the 7-day refund conditions</a>
            <label for="mpesa-{{ $this->getId() }}" class="block text-[11px] font-bold text-slate-700">
                M-Pesa Phone Number:
            </label>
            <input
                id="mpesa-{{ $this->getId() }}"
                type="tel"
                autocomplete="tel"
                inputmode="tel"
                wire:model="phone"
                placeholder="07XXXXXXXX"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#3444da] focus:outline-none focus:ring-1 focus:ring-[#3444da]"
                autofocus
            >
            <button
                type="button"
                wire:click="subscribe"
                wire:loading.attr="disabled"
                wire:target="subscribe"
                class="w-full rounded-lg px-5 py-2 text-[12.5px] font-semibold transition {{ $btnClasses }} flex items-center justify-center gap-1.5 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="subscribe">Pay KES {{ number_format($price) }} with M-Pesa</span>
                <span wire:loading wire:target="subscribe">Prompting STK push&hellip;</span>
            </button>
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5">
                <span>Instant Safaricom STK Push</span>
                <button type="button" wire:click="$set('showInput', false)" class="hover:text-slate-600 underline">Cancel</button>
            </div>
            <p class="text-xs text-slate-500">Enter your M-Pesa PIN only on your phone. <a href="{{ route('support.index') }}" class="font-semibold text-[#3444da] underline">Need payment help?</a></p>
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
            class="w-full rounded-lg px-5 py-2.5 text-[13px] font-semibold transition {{ $btnClasses }}"
        >
            {!! $label !!}
        </button>
        @if ($error)
            <p class="mt-2 text-center text-xs text-rose-600 font-medium">{{ $error }}</p>
        @endif
        @if ($paymentError)
            <p class="mt-2 text-center text-xs text-rose-600 font-medium">{{ $paymentError }}</p>
        @endif
    @endif
</div>
