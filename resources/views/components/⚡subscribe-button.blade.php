<?php

use App\Livewire\Concerns\HasPendingPayment;
use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    use HasPendingPayment;

    public bool $isAuthed;

    public bool $alreadySubscribed;

    public string $period = 'monthly';

    public string $phone = '';

    public ?string $error = null;

    protected function paymentRedirectTo(): string
    {
        return '/pricing';
    }

    public function selectPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function subscribe(PaymentService $payments): void
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
            $payment = $payments->subscribe(auth()->user(), $this->period, $this->phone ?: null);
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
    @if ($alreadySubscribed)
        <button type="button" disabled class="w-full rounded-full border-2 border-emerald-300 bg-emerald-50 px-6 py-3 font-semibold text-emerald-700">
            You&rsquo;re subscribed
        </button>
    @elseif ($pendingPaymentId)
        <div wire:poll.3s="checkPaymentStatus" class="rounded-2xl border border-sunrise-200 bg-sunrise-50 p-4 text-center text-sm">
            <p class="font-semibold text-sunrise-800">Check your phone</p>
            <p class="mt-1 text-foreground/60">Enter your M-Pesa PIN on the prompt sent to {{ $phone }} to complete the subscription.</p>
        </div>
    @else
        <div class="mb-3 flex gap-1.5 rounded-full bg-black/5 p-1">
            @foreach (config('jobs.subscription_plans') as $key => $plan)
                <button
                    type="button"
                    wire:click="selectPeriod('{{ $key }}')"
                    class="flex flex-1 flex-col items-center gap-0.5 rounded-full px-1 py-1.5 text-xs font-semibold transition {{ $period === $key ? 'bg-white text-horizon-800 shadow-sm' : 'text-foreground/50' }}"
                >
                    {{ $plan['label'] }}
                    <span class="rounded-full px-1.5 py-0.5 text-[9px] font-bold {{ $plan['save_label'] ? 'bg-emerald-100 text-emerald-700' : 'invisible' }}">
                        {{ $plan['save_label'] ?? '—' }}
                    </span>
                </button>
            @endforeach
        </div>

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
            wire:click="subscribe"
            wire:loading.attr="disabled"
            wire:target="subscribe"
            class="btn-pop flex w-full items-center justify-center gap-2 rounded-full bg-horizon-800 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-horizon-900 disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="subscribe">{{ $isAuthed ? 'Subscribe for KES '.number_format(config('jobs.subscription_plans')[$period]['price_kes']) : 'Log in to subscribe' }}</span>
            <span wire:loading wire:target="subscribe">Processing&hellip;</span>
        </button>
        @if ($error)
            <p class="mt-2 text-center text-sm text-red-600">{{ $error }}</p>
        @endif
        @if ($paymentError)
            <p class="mt-2 text-center text-sm text-red-600">{{ $paymentError }}</p>
        @endif
    @endif
</div>
