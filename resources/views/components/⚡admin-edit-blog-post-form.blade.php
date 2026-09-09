<?php

use App\Models\BlogPost;
use Livewire\Component;

new class extends Component
{
    public string $postId;

    public string $title = '';

    public string $category = '';

    public string $authorName = '';

    public string $excerpt = '';

    public string $content = '';

    public bool $published = false;

    public ?string $error = null;

    public ?string $saved = null;

    public function mount(BlogPost $post): void
    {
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->category = $post->category;
        $this->authorName = $post->author_name;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->published = $post->published;
    }

    public function save(): void
    {
        $this->error = null;
        $this->saved = null;

        if (! $this->title || ! $this->excerpt || ! $this->content || ! $this->category) {
            $this->error = 'Title, excerpt, content, and category are required.';

            return;
        }

        // Load and save (not a mass update()) so BlogPost's saving() model
        // event — which stamps published_at on first publish — still fires.
        BlogPost::findOrFail($this->postId)->update([
            'title' => trim($this->title),
            'category' => trim($this->category),
            'author_name' => trim($this->authorName) ?: 'KenyaRemoteJobs Team',
            'excerpt' => trim($this->excerpt),
            'content' => trim($this->content),
            'published' => $this->published,
        ]);

        $this->saved = 'Saved.';
    }
};
?>

<form wire:submit="save" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
    <div class="grid gap-3">
        <input wire:model="title" required placeholder="Title" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <input wire:model="category" required placeholder="Category (e.g. Career Development)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
            <input wire:model="authorName" placeholder="Author (defaults to KenyaRemoteJobs Team)" class="rounded-lg border border-black/10 px-3 py-2 text-sm">
        </div>
        <textarea wire:model="excerpt" required rows="2" placeholder="Short excerpt shown on the journal grid" class="rounded-lg border border-black/10 px-3 py-2 text-sm"></textarea>
        <textarea wire:model="content" required rows="16" placeholder="Full post content" class="rounded-lg border border-black/10 px-3 py-2 text-sm"></textarea>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="published" class="h-4 w-4 accent-sunrise-500">
            Published
        </label>
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
        <a href="{{ url('/admin/blog') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to Journal</a>
    </div>
</form>
