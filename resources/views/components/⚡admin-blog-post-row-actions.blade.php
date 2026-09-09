<?php

use App\Models\BlogPost;
use Livewire\Component;

new class extends Component
{
    public string $postId;

    public bool $published;

    public function togglePublished(): void
    {
        // A mass update() wouldn't fire BlogPost's saving() model event
        // (which is what stamps published_at on first publish) — load and
        // save instead so that still runs.
        $post = BlogPost::findOrFail($this->postId);
        $post->published = ! $this->published;
        $post->save();
        $this->published = $post->published;
    }

    public function delete(): void
    {
        BlogPost::where('id', $this->postId)->delete();
        $this->redirect(request()->fullUrl(), navigate: false);
    }
};
?>

<div class="flex justify-end gap-2">
    <a
        href="{{ url('/admin/blog/'.$postId.'/edit') }}"
        class="btn-pop rounded-full border border-black/10 px-3 py-1 text-xs font-semibold transition hover:bg-black/5"
    >
        Edit
    </a>
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
