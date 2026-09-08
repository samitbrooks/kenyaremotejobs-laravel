<?php

use App\Models\HiddenJob;
use App\Models\JobListing;
use Livewire\Component;

new class extends Component
{
    public string $jobId;

    // A synced job's id also goes on the hidden list so the next resync
    // doesn't just bring it right back — a manual job is gone for good
    // since sync never re-adds it anyway.
    public function remove(): void
    {
        $job = JobListing::find($this->jobId);
        if (! $job) {
            return;
        }

        if ($job->origin === 'synced') {
            HiddenJob::firstOrCreate(['id' => $job->id], ['hidden_at' => now()]);
        }

        $job->delete();

        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<button
    type="button"
    wire:click="remove"
    wire:confirm="Remove this listing from the live site?"
    wire:loading.attr="disabled"
    wire:target="remove"
    class="btn-pop rounded-full border border-red-200 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:opacity-60"
>
    <span wire:loading.remove wire:target="remove">Remove</span>
    <span wire:loading wire:target="remove">&hellip;</span>
</button>
