<?php

use App\Models\BlogPost;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public string $title = '';

    public string $category = '';

    public string $authorName = '';

    public string $excerpt = '';

    public string $content = '';

    public bool $published = false;

    public ?string $error = null;

    public function show(): void
    {
        $this->open = true;
    }

    public function hide(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->error = null;

        if (! $this->title || ! $this->excerpt || ! $this->content || ! $this->category) {
            $this->error = 'Title, excerpt, content, and category are required.';

            return;
        }

        $slug = Str::slug($this->title);
        // Guard against two posts titled the same thing colliding on the
        // unique slug column — append a short suffix rather than failing
        // the insert.
        if (BlogPost::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(6));
        }

        BlogPost::create([
            'slug' => $slug,
            'title' => trim($this->title),
            'excerpt' => trim($this->excerpt),
            'content' => trim($this->content),
            'category' => trim($this->category),
            'author_name' => trim($this->authorName) ?: 'KenyaRemoteJobs Team',
            'published' => $this->published,
        ]);

        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<div>
    @if (! $open)
        <button type="button" wire:click="show" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md">
            + New post
        </button>
    @else
        <form wire:submit="save" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">New post</h2>
                <button type="button" wire:click="hide" class="text-sm text-foreground/50 hover:underline">Cancel</button>
            </div>

            <div class="mt-4 grid gap-3">
                <input wire:model="title" required placeholder="Title" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                <div class="grid gap-3 sm:grid-cols-2">
                    <input wire:model="category" required placeholder="Category (e.g. Career Development)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                    <input wire:model="authorName" placeholder="Author (defaults to KenyaRemoteJobs Team)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
                </div>
                <textarea wire:model="excerpt" required rows="2" placeholder="Short excerpt shown on the journal grid" class="rounded-lg border border-black/10 px-3 py-2 text-sm"></textarea>
                <textarea wire:model="content" required rows="12" placeholder="Full post content" class="rounded-lg border border-black/10 px-3 py-2 text-sm"></textarea>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="published" class="h-4 w-4 accent-sunrise-500">
                    Publish immediately
                </label>
            </div>

            @if ($error)
                <p class="mt-3 text-sm text-red-600">{{ $error }}</p>
            @endif

            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-pop mt-4 rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md disabled:opacity-60">
                <span wire:loading.remove wire:target="save">Save post</span>
                <span wire:loading wire:target="save">Saving&hellip;</span>
            </button>
        </form>
    @endif
</div>
