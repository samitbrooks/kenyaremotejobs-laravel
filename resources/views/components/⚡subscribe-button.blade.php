<?php

use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    public bool $isAuthed;

    public bool $alreadySubscribed;

    public string $period = 'monthly';

    public ?string $error = null;

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

        try {
            $payment = $payments->subscribe(auth()->user(), $this->period);
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
    @if ($alreadySubscribed)
        <button type="button" disabled class="w-full rounded-full border-2 border-emerald-300 bg-emerald-50 px-6 py-3 font-semibold text-emerald-700">
            You&rsquo;re subscribed
        </button>
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
    @endif
</div>
