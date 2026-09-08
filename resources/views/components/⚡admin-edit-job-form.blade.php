<?php

use App\Models\JobListing;
use Livewire\Component;

new class extends Component
{
    public string $jobId;

    public string $origin;

    public string $title = '';

    public string $company = '';

    public string $location = '';

    public string $remoteType = '';

    public string $salary = '';

    public string $tier = 'basic';

    public bool $kenyaFriendly = false;

    public string $tags = '';

    public string $sourceUrl = '';

    public string $description = '';

    public ?string $error = null;

    public ?string $saved = null;

    public function mount(JobListing $job): void
    {
        $this->jobId = $job->id;
        $this->origin = $job->origin;
        $this->title = $job->title;
        $this->company = $job->company;
        $this->location = $job->location;
        $this->remoteType = $job->remote_type;
        $this->salary = (string) $job->salary;
        $this->tier = $job->tier;
        $this->kenyaFriendly = $job->kenya_friendly;
        $this->tags = implode(', ', $job->tags ?? []);
        $this->sourceUrl = $job->source_url;
        $this->description = $job->description;
    }

    public function save(): void
    {
        $this->error = null;
        $this->saved = null;

        if (! $this->title || ! $this->company || ! $this->description || ! $this->location || ! $this->remoteType || ! $this->sourceUrl) {
            $this->error = 'Title, company, description, location, remote type, and apply link are required.';

            return;
        }

        if (! preg_match('#^https?://#i', $this->sourceUrl)) {
            $this->error = 'Apply link must be a full URL.';

            return;
        }

        JobListing::where('id', $this->jobId)->update([
            'title' => trim($this->title),
            'company' => trim($this->company),
            'location' => trim($this->location),
            'remote_type' => trim($this->remoteType),
            'salary' => trim($this->salary) ?: null,
            'tier' => $this->tier,
            'kenya_friendly' => $this->kenyaFriendly,
            'tags' => array_values(array_filter(array_map('trim', explode(',', $this->tags)))),
            'source_url' => trim($this->sourceUrl),
            'description' => trim($this->description),
        ]);

        $this->saved = 'Saved.';
    }
};
?>

<form wire:submit="save" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    @if ($origin === 'synced')
        <div class="mb-4 rounded-xl border border-sunrise-300 bg-sunrise-50 p-3 text-xs text-sunrise-900">
            This listing comes from an automated sync. Edits here stick until the next resync, which will overwrite it with the source's current data if the listing is still live upstream.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <input wire:model="title" required placeholder="Job title" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
        <input wire:model="company" required placeholder="Company" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
        <input wire:model="location" required placeholder="Location (e.g. Worldwide)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
        <select wire:model="remoteType" required class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            @foreach (['Remote', 'Full-time', 'Part-time', 'Contract', 'Internship'] as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
        <input wire:model="salary" placeholder="Salary (optional)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
        <select wire:model="tier" required class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            @foreach (config('jobs.tiers') as $t)
                <option value="{{ $t }}">{{ config('jobs.tier_labels')[$t] }}</option>
            @endforeach
        </select>
        <input wire:model="tags" placeholder="Tags, comma-separated" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
        <input wire:model="sourceUrl" required type="url" placeholder="Apply link (https://…)" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
        <label class="flex items-center gap-2 text-sm sm:col-span-2">
            <input type="checkbox" wire:model="kenyaFriendly" class="h-4 w-4 accent-emerald-600">
            Kenya-Friendly Match
        </label>
        <textarea wire:model="description" required rows="10" placeholder="Full job description" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2"></textarea>
    </div>

    @if ($error)
        <p class="mt-3 text-sm text-red-600">{{ $error }}</p>
    @endif
    @if ($saved)
        <p class="mt-3 text-sm text-emerald-700">{{ $saved }}</p>
    @endif

    <div class="mt-4 flex items-center gap-3">
        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md disabled:opacity-60">
            <span wire:loading.remove wire:target="save">Save changes</span>
            <span wire:loading wire:target="save">Saving&hellip;</span>
        </button>
        <a href="{{ url('/admin/jobs') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to Jobs</a>
    </div>
</form>
