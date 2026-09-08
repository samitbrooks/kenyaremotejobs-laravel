<x-layouts.admin title="Admin: Journal">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold">Journal</h1>
        <livewire:admin-add-blog-post-form />
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-black/5 bg-white shadow-sm">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-black/10 text-foreground/50">
                    <th class="p-4 font-medium">Title</th>
                    <th class="p-4 font-medium">Category</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium">Created</th>
                    <th class="p-4 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-b border-black/5 last:border-0">
                        <td class="p-4">
                            <p class="font-semibold">{{ $post->title }}</p>
                            <p class="text-xs text-foreground/40">by {{ $post->author_name }}</p>
                        </td>
                        <td class="p-4">{{ $post->category }}</td>
                        <td class="p-4">
                            @if ($post->published)
                                <span class="font-semibold text-emerald-700">Published</span>
                            @else
                                <span class="text-foreground/50">Draft</span>
                            @endif
                        </td>
                        <td class="p-4 text-foreground/50">{{ \App\Support\Format::timeAgo($post->created_at) }}</td>
                        <td class="p-4">
                            <livewire:admin-blog-post-row-actions :post-id="$post->id" :published="$post->published" :key="'post-'.$post->id" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-foreground/50">No posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
