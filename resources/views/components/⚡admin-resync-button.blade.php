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
            $count = $syncService->sync(notifyPro: true);
            $this->result = $count === 0
                ? 'Every upstream source failed or returned nothing — kept your existing listings, nothing was overwritten.'
                : "✓ Real-time sync complete: {$count} jobs active & scored. Pro subscriber alerts processed.";
        } catch (\Throwable $e) {
            $this->error = 'Real-time sync failed: '.$e->getMessage();
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
        class="btn-pop rounded-full bg-gradient-to-r from-red-600 via-rose-600 to-[#2e3760] px-5 py-2 text-sm font-bold text-white shadow-md hover:shadow-lg hover:shadow-red-500/20 transition disabled:opacity-60 flex items-center gap-2"
    >
        <span wire:loading.remove wire:target="resync" class="flex items-center gap-1.5">
            <span>⚡</span> Sync All Jobs Realtime
        </span>
        <span wire:loading wire:target="resync" class="flex items-center gap-1.5">
            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Syncing Realtime&hellip;
        </span>
    </button>
    @if ($result)
        <p class="mt-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">{{ $result }}</p>
    @endif
    @if ($error)
        <p class="mt-2 text-xs font-semibold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-200">{{ $error }}</p>
    @endif
</div>
