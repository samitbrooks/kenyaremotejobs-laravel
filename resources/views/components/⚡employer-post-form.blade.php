<?php

use App\Livewire\Concerns\HasPendingPayment;
use App\Services\PaymentService;
use Livewire\Component;

new class extends Component
{
    use HasPendingPayment;

    public string $plan = 'basic';

    public string $title = '';

    public string $company = '';

    public string $location = 'Worldwide';

    public string $remoteType = '';

    public string $salary = '';

    public string $tags = '';

    public string $sourceUrl = '';

    public string $description = '';

    public string $phone = '';

    public ?string $error = null;

    protected function paymentRedirectTo(): string
    {
        return '/employers/dashboard';
    }

    public function selectPlan(string $plan): void
    {
        $this->plan = $plan;
    }

    public function publish(PaymentService $payments): void
    {
        $this->error = null;
        $this->paymentError = null;

        if (! $this->title || ! $this->company || ! $this->description || ! $this->location || ! $this->remoteType || ! $this->sourceUrl) {
            $this->error = 'Title, company, description, location, remote type, and apply link are required.';

            return;
        }

        if (! preg_match('#^https?://#i', $this->sourceUrl)) {
            $this->error = 'Apply link must be a full URL.';

            return;
        }

        if (config('payments.default') === 'mpesa' && ! \App\Support\KenyanPhone::normalize($this->phone)) {
            $this->error = 'Enter a valid M-Pesa phone number (e.g. 07XXXXXXXX).';

            return;
        }

        $tags = array_values(array_filter(array_map('trim', explode(',', $this->tags))));

        try {
            $payment = $payments->postEmployerJob(auth()->user(), [
                'title' => trim($this->title),
                'company' => trim($this->company),
                'description' => trim($this->description),
                'location' => trim($this->location),
                'remote_type' => trim($this->remoteType),
                'salary' => trim($this->salary) ?: null,
                'source_url' => trim($this->sourceUrl),
                'tags' => $tags,
            ], $this->plan, $this->phone ?: null);

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

@if ($pendingPaymentId)
    <div wire:poll.3s="checkPaymentStatus" class="rounded-2xl border border-sunrise-200 bg-sunrise-50 p-8 text-center">
        <p class="font-semibold text-sunrise-800">Check your phone</p>
        <p class="mt-1 text-sm text-foreground/60">Enter your M-Pesa PIN on the prompt sent to {{ $phone }} to publish this listing.</p>
    </div>
@else
<form wire:submit="publish" class="space-y-8">
    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Choose a plan</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach (config('jobs.posting_plans') as $key => $pkg)
                <button
                    type="button"
                    wire:click="selectPlan('{{ $key }}')"
                    class="rounded-2xl border-2 p-5 text-left transition {{ $plan === $key ? 'border-sunrise-500 bg-sunrise-50' : 'border-black/10 bg-white hover:border-black/20' }}"
                >
                    <p class="text-xs font-semibold uppercase tracking-wide text-sunrise-600">
                        {{ $pkg['label'] }}
                        @if ($pkg['featured'])
                            <span class="ml-2 rounded-full bg-sunrise-500 px-2 py-0.5 text-[10px] text-white">Most visibility</span>
                        @endif
                    </p>
                    <p class="mt-1 text-3xl font-bold">
                        KES {{ number_format($pkg['price_kes']) }}
                        <span class="text-sm font-medium text-foreground/50"> &middot; {{ $pkg['listing_days'] }}-day listing</span>
                    </p>
                    <ul class="mt-3 space-y-1 text-sm text-foreground/60">
                        @foreach ($pkg['features'] ?? [] as $feature)
                            <li class="flex items-start gap-1.5"><x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-600" /> {{ $feature }}</li>
                        @endforeach
                    </ul>
                </button>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Role details</h2>
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <input wire:model="title" required placeholder="Job title" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
            <input wire:model="company" required placeholder="Company name" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            <input wire:model="location" required placeholder="Location (e.g. Worldwide)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            <select wire:model="remoteType" required class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                <option value="" disabled>Remote type</option>
                @foreach (['Remote', 'Full-time', 'Part-time', 'Contract'] as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            <input wire:model="salary" placeholder="Salary (optional)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            <input wire:model="tags" placeholder="Skills/tags, comma-separated" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
            <input wire:model="sourceUrl" required type="url" placeholder="Where candidates should apply (https://…)" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
            <textarea wire:model="description" required rows="8" placeholder="Full job description" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2"></textarea>
            @if (config('payments.default') === 'mpesa')
                <input wire:model="phone" type="tel" required placeholder="M-Pesa phone (07XXXXXXXX)" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
            @endif
        </div>
    </div>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
    @if ($paymentError)
        <p class="text-sm text-red-600">{{ $paymentError }}</p>
    @endif

    <button
        type="submit"
        wire:loading.attr="disabled"
        wire:target="publish"
        class="btn-pop w-full rounded-full gradient-sunrise px-8 py-3.5 font-semibold text-white shadow-lg transition hover:opacity-90 disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="publish">Publish for KES {{ number_format(config('jobs.posting_plans')[$plan]['price_kes']) }}</span>
        <span wire:loading wire:target="publish">Publishing&hellip;</span>
    </button>
</form>
@endif
