<x-layouts.app :title="$post->title" :description="\App\Support\Format::truncate($post->excerpt, 155)">
    <script type="application/ld+json">{!! \App\Support\Seo::articleJsonLd($post) !!}</script>

    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
        <a href="{{ url('/journal') }}" class="text-sm text-foreground/50 hover:underline">&larr; Back to the journal</a>

        <x-reveal>
            <span class="mt-4 inline-block rounded-full bg-horizon-100 px-2.5 py-1 text-[11px] font-semibold text-horizon-800">{{ $post->category }}</span>
            <h1 class="mt-3 text-2xl font-bold sm:text-3xl">{{ $post->title }}</h1>
            <p class="mt-2 text-sm text-foreground/50">
                {{ $post->author_name }} &middot; {{ $post->published_at ? \App\Support\Format::timeAgo($post->published_at) : \App\Support\Format::timeAgo($post->created_at) }} &middot; {{ \App\Support\Format::estimateReadMinutes($post->content) }} min read
            </p>

            <div class="mt-8 whitespace-pre-line text-sm leading-relaxed text-foreground/80">{{ $post->content }}</div>
        </x-reveal>
    </div>
</x-layouts.app>
