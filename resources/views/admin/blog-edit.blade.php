<x-layouts.admin title="Admin: Edit Post">
    <h1 class="text-2xl font-bold">Edit post</h1>
    <p class="mt-1 text-sm text-foreground/50">{{ $post->title }}</p>

    <div class="mt-6">
        <livewire:admin-edit-blog-post-form :post="$post" />
    </div>
</x-layouts.admin>
