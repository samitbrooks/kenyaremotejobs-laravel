<?php

use App\Models\JobListing;
use App\Support\Audience;
use App\Support\KenyaRelevance;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public string $title = '';

    public string $company = '';

    public string $location = '';

    public string $remoteType = '';

    public string $salary = '';

    public string $tier = 'basic';

    public string $tags = '';

    public string $sourceUrl = '';

    public string $description = '';

    public ?string $error = null;

    public function show(): void
    {
        $this->open = true;
    }

    public function hide(): void
    {
        $this->open = false;
    }

    // Admin-authored listing — scored for Kenya relevance the same way a
    // synced one is, so it gets the same badge treatment, but flagged
    // origin='manual' so the sync pipeline never overwrites or deletes it.
    public function publish(): void
    {
        $this->error = null;

        if (! $this->title || ! $this->company || ! $this->description || ! $this->location || ! $this->remoteType || ! $this->sourceUrl) {
            $this->error = 'Title, company, description, location, remote type, tier, and apply link are required.';

            return;
        }

        if (! preg_match('#^https?://#i', $this->sourceUrl)) {
            $this->error = 'Apply link must be a full URL.';

            return;
        }

        $input = [
            'title' => trim($this->title),
            'company' => trim($this->company),
            'description' => trim($this->description),
            'location' => trim($this->location),
            'tags' => array_values(array_filter(array_map('trim', explode(',', $this->tags)))),
        ];
        $relevance = KenyaRelevance::score($input);

        JobListing::create([
            'id' => 'manual--'.Str::uuid(),
            'source_name' => 'Manual',
            'source_id' => (string) Str::uuid(),
            'source_url' => trim($this->sourceUrl),
            'title' => $input['title'],
            'company' => $input['company'],
            'description' => $input['description'],
            'tags' => $input['tags'],
            'location' => $input['location'],
            'remote_type' => trim($this->remoteType),
            'salary' => trim($this->salary) ?: null,
            'annual_salary_usd' => null,
            'posted_at' => now(),
            'kenya_friendly' => $relevance['kenyaFriendly'],
            'kenya_score' => $relevance['score'],
            'kenya_reasons' => $relevance['reasons'],
            'origin' => 'manual',
            'tier' => $this->tier,
            'audience_segments' => Audience::classify($input),
        ]);

        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<div>
    @if (! $open)
        <button type="button" wire:click="show" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md">
            + Add a job
        </button>
    @else
        <form wire:submit="publish" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Add a job listing</h2>
                <button type="button" wire:click="hide" class="text-sm text-foreground/50 hover:underline">Cancel</button>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <input wire:model="title" required placeholder="Job title" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
                <input wire:model="company" required placeholder="Company" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                <input wire:model="location" required placeholder="Location (e.g. Worldwide)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                <select wire:model="remoteType" required class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                    <option value="" disabled>Remote type</option>
                    @foreach (['Remote', 'Full-time', 'Part-time', 'Contract', 'Internship'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <input wire:model="salary" placeholder="Salary (optional)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                <select wire:model="tier" required class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                    @foreach (config('jobs.tiers') as $t)
                        <option value="{{ $t }}">{{ config('jobs.tier_labels')[$t] }} &mdash; needs a KES {{ number_format(config('jobs.credit_packages')[$t]['price_kes']) }} package to unlock</option>
                    @endforeach
                </select>
                <input wire:model="tags" placeholder="Tags, comma-separated" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
                <input wire:model="sourceUrl" required type="url" placeholder="Apply link (https://…)" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2">
                <textarea wire:model="description" required rows="5" placeholder="Full job description" class="rounded-lg border border-black/10 px-3 py-2 text-sm sm:col-span-2"></textarea>
            </div>

            @if ($error)
                <p class="mt-3 text-sm text-red-600">{{ $error }}</p>
            @endif

            <button type="submit" wire:loading.attr="disabled" wire:target="publish" class="btn-pop mt-4 rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md disabled:opacity-60">
                <span wire:loading.remove wire:target="publish">Publish listing</span>
                <span wire:loading wire:target="publish">Publishing&hellip;</span>
            </button>
        </form>
    @endif
</div>
