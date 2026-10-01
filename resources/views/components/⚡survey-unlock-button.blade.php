<?php

use App\Livewire\Concerns\HasPendingPayment;
use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    use HasPendingPayment;

    public int $priceKes = 199;

    public string $phone = '';

    public ?string $error = null;

    protected function paymentRedirectTo(): string
    {
        return '/surveys?unlocked=1';
    }

    public function buy(PaymentService $payments): void
    {
        if (! auth()->check()) {
            $this->redirect('/account?next=/surveys', navigate: false);

            return;
        }

        $this->error = null;
        $this->paymentError = null;

        if (config('payments.default') === 'mpesa' && ! \App\Support\KenyanPhone::normalize($this->phone)) {
            $this->error = 'Enter a valid Safaricom M-Pesa number (e.g. 07XXXXXXXX or 01XXXXXXXX).';

            return;
        }

        try {
            $payment = $payments->purchaseSurveyPass(auth()->user(), $this->phone ?: null);
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

<div class="w-full">
    @if ($pendingPaymentId)
        <div wire:poll.3s="checkPaymentStatus" class="rounded-2xl border border-teal-300 bg-teal-50 p-5 text-center shadow-md">
            <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-teal-700 animate-bounce mb-2">
                <x-icon name="coin" class="h-5 w-5" />
            </div>
            <p class="font-extrabold text-teal-950 text-base">Check Your Phone</p>
            <p class="mt-1 text-xs text-teal-800 leading-relaxed max-w-sm mx-auto">
                An M-Pesa STK push for <strong>KES {{ $priceKes }}</strong> was sent to your phone. Enter your M-Pesa PIN to complete unlocking the survey vault.
            </p>
            <p class="mt-3 text-[11px] text-teal-600 font-medium">Waiting for M-Pesa confirmation...</p>
        </div>
    @else
        <div class="rounded-3xl border-2 border-teal-500/80 bg-gradient-to-br from-white via-teal-50/30 to-emerald-50/40 p-6 sm:p-8 shadow-xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-100/80 border border-teal-200 px-3.5 py-1 text-xs font-black text-teal-900 shadow-2xs">
                🔒 Exclusive VIP Side-Income Vault
            </span>

            <h3 class="mt-3 text-xl sm:text-2xl font-black text-slate-900">
                Unlock All 20+ Verified Survey &amp; Micro-Task Hubs
            </h3>

            <p class="mt-2 text-xs sm:text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                Get lifetime access to the 16 remaining high-yield research panels, Outlier AI training, Respondent ($50-$200/study), and direct M-Pesa payout links.
            </p>

            <div class="mt-4 flex items-center justify-center gap-2">
                <span class="text-3xl font-black text-teal-700">KES {{ $priceKes }}</span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">One-Time Fee</span>
            </div>

            <p class="mt-1 text-[11px] text-emerald-700 font-semibold">
                ✓ Included for free with active Pro membership
            </p>

            @if ($error || $paymentError)
                <div class="mt-4 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800 font-semibold">
                    {{ $error ?? $paymentError }}
                </div>
            @endif

            <div class="mt-6 max-w-md mx-auto">
                @auth
                    <form wire:submit.prevent="buy" class="space-y-3">
                        @if (config('payments.default') === 'mpesa')
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400">
                                    🇰🇪 +254
                                </div>
                                <input
                                    type="tel"
                                    wire:model="phone"
                                    placeholder="07XXXXXXXX"
                                    required
                                    class="w-full rounded-full border border-slate-300 bg-white py-2.5 pl-18 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 font-medium"
                                />
                            </div>
                        @endif

                        <button
                            type="submit"
                            class="btn-pop flex w-full items-center justify-center gap-2 rounded-full bg-emerald-600 hover:bg-emerald-700 py-3 px-6 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/25 transition cursor-pointer"
                        >
                            <span>Lipa Na M-Pesa &middot; KES {{ $priceKes }}</span>
                            <span>&rarr;</span>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ url('/account?next=/surveys') }}"
                        class="btn-pop flex w-full items-center justify-center gap-2 rounded-full bg-emerald-600 hover:bg-emerald-700 py-3 px-6 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/25 transition cursor-pointer"
                    >
                        <span>Sign In to Unlock with M-Pesa &rarr;</span>
                    </a>
                @endauth

                <div class="mt-4 flex items-center justify-center gap-4 text-[11px] text-slate-500">
                    <span class="flex items-center gap-1">🔒 Instant M-Pesa STK Push</span>
                    <span>&middot;</span>
                    <span>⚡ Instant Vault Unlock</span>
                </div>
            </div>
        </div>
    @endif
</div>
