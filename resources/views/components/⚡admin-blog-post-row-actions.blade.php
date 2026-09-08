<?php

use App\Models\BlogPost;
use Livewire\Component;

new class extends Component
{
    public string $postId;

    public bool $published;

    public function togglePublished(): void
    {
        BlogPost::where('id', $this->postId)->update(['published' => ! $this->published]);
        $this->published = ! $this->published;
    }

    public function delete(): void
    {
        BlogPost::where('id', $this->postId)->delete();
        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<div class="flex justify-end gap-2">
    <button
        type="button"
        wire:click="togglePublished"
        wire:loading.attr="disabled"
        class="btn-pop rounded-full border border-black/10 px-3 py-1 text-xs font-semibold transition hover:bg-black/5 disabled:opacity-60"
    >
        {{ $published ? 'Unpublish' : 'Publish' }}
    </button>
    <button
        type="button"
        wire:click="delete"
        wire:confirm="Delete this post permanently?"
        wire:loading.attr="disabled"
        class="btn-pop rounded-full border border-red-200 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:opacity-60"
    >
        Delete
    </button>
</div>
