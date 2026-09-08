<?php

use App\Models\User;
use App\Services\BulkMailer;
use Livewire\Component;

new class extends Component
{
    public array $counts;

    public bool $configured;

    public string $audience = 'all';

    public string $subject = '';

    public string $message = '';

    public ?string $result = null;

    public ?string $error = null;

    public function mount(array $counts, bool $configured): void
    {
        $this->counts = $counts;
        $this->configured = $configured;
    }

    public function selectAudience(string $audience): void
    {
        $this->audience = $audience;
    }

    public function send(BulkMailer $mailer): void
    {
        $this->result = null;
        $this->error = null;

        if (! $this->subject || ! $this->message) {
            $this->error = 'Subject and message are required.';

            return;
        }

        $recipients = User::query()
            ->when($this->audience !== 'all', fn ($q) => $q->where('subscribed', $this->audience === 'subscribed'))
            ->whereNull('marketing_opt_out_at')
            ->get();

        if ($recipients->isEmpty()) {
            $this->error = 'No recipients match that audience.';

            return;
        }

        try {
            $result = $mailer->sendBulk($recipients, $this->subject, $this->message);
            $this->result = "Sent to {$result['sent']} recipient(s)."
                .(count($result['failed']) ? ' Failed: '.implode(', ', $result['failed']) : '');
            $this->subject = '';
            $this->message = '';
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }
};
?>

<form wire:submit="send" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    @if (! $configured)
        <div class="mb-4 rounded-xl border border-sunrise-300 bg-sunrise-50 p-4 text-sm text-sunrise-900">
            Email isn&rsquo;t connected yet &mdash; set <code>MAIL_HOST</code>, <code>MAIL_PORT</code>, <code>MAIL_USERNAME</code>, <code>MAIL_PASSWORD</code>, and <code>MAIL_FROM_ADDRESS</code> in your environment (your cPanel email account&rsquo;s SMTP details, once the domain&rsquo;s live). You can draft below, but sending will fail until then.
        </div>
    @endif

    <label class="block text-sm font-semibold">Audience</label>
    <select wire:model.live="audience" class="mt-1 w-full max-w-sm rounded-lg border border-black/10 px-3 py-2 text-sm">
        <option value="all">Everyone with an account ({{ $counts['all'] }})</option>
        <option value="subscribed">Full-access accounts only ({{ $counts['subscribed'] }})</option>
        <option value="free">Pay-per-job accounts only ({{ $counts['free'] }})</option>
    </select>

    <label class="mt-4 block text-sm font-semibold">Subject</label>
    <input wire:model="subject" required class="mt-1 w-full rounded-lg border border-black/10 px-3 py-2 text-sm">

    <label class="mt-4 block text-sm font-semibold">Message</label>
    <textarea wire:model="message" required rows="10" class="mt-1 w-full rounded-lg border border-black/10 px-3 py-2 text-sm"></textarea>

    @if ($result)
        <p class="mt-3 text-sm text-emerald-700">{{ $result }}</p>
    @endif
    @if ($error)
        <p class="mt-3 text-sm text-red-600">{{ $error }}</p>
    @endif

    <button
        type="submit"
        wire:confirm="Send this to {{ $counts[$audience] }} recipient(s)?"
        wire:loading.attr="disabled"
        wire:target="send"
        class="btn-pop mt-4 rounded-full gradient-sunrise px-6 py-2.5 text-sm font-semibold text-white shadow-md disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="send">Send to {{ $counts[$audience] }} recipient(s)</span>
        <span wire:loading wire:target="send">Sending&hellip;</span>
    </button>
</form>
