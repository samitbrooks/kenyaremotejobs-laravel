<?php

use App\Services\JobSyncService;
use Livewire\Component;

new class extends Component
{
    public ?string $result = null;

    public ?string $error = null;

    public function resync(JobSyncService $syncService): void
    {
        $this->result = null;
        $this->error = null;

        try {
            $count = $syncService->sync();
            $this->result = $count === 0
                ? 'Every source failed — kept your existing listings, nothing was overwritten. Try again shortly.'
                : "Synced {$count} jobs.";
        } catch (\Throwable) {
            $this->error = 'Resync failed — check the server log.';
        }
    }
};
?>

<div>
    <button
        type="button"
        wire:click="resync"
        wire:loading.attr="disabled"
        wire:target="resync"
        class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md transition disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="resync">Resync jobs now</span>
        <span wire:loading wire:target="resync">Syncing&hellip;</span>
    </button>
    @if ($result)
        <p class="mt-2 text-sm text-emerald-700">{{ $result }}</p>
    @endif
    @if ($error)
        <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
