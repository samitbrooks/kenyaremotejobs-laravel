<x-layouts.app
    title="The Journal"
    description="Practical guidance for finding good remote work, building a career, and understanding how hiring for Kenya and East Africa actually works."
    :canonical="url('/journal')"
>
    <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6">
        <x-reveal>
            <h1 class="text-3xl font-bold sm:text-4xl">Ideas for better work.</h1>
            <p class="mt-3 max-w-xl text-foreground/60">
                Practical guidance for finding good work, building a career thoughtfully, and understanding remote hiring from a Kenya and East Africa vantage point.
            </p>
        </x-reveal>

        @if ($posts->isEmpty())
            <p class="mt-10 rounded-xl bg-horizon-50 p-8 text-center text-foreground/60">
                Nothing published yet &mdash; check back soon.
            </p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $i => $post)
                    <x-reveal :delay="min($i, 8) * 60" class="h-full">
                        <a href="{{ url('/journal/'.$post->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative aspect-[16/9] w-full overflow-hidden bg-horizon-100">
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
                                <div class="absolute left-3 top-3 flex items-center gap-1.5">
                                    <span class="rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-bold text-horizon-900 shadow-xs">{{ $post->category }}</span>
                                    @if ($post->published_at && $post->published_at->gt(now()->subDays(7)))
                                        <span class="rounded-full bg-sunrise-500 px-2.5 py-1 text-[11px] font-bold text-white shadow-xs">New</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col gap-3 p-6">
                                <h2 class="font-bold text-base text-foreground group-hover:text-sunrise-600 transition-colors leading-snug">{{ $post->title }}</h2>
                                <p class="line-clamp-3 flex-1 text-sm text-foreground/60 leading-relaxed">{{ $post->excerpt }}</p>
                                <div class="flex items-center justify-between border-t border-black/5 pt-3 text-xs text-foreground/50">
                                    <span>{{ $post->author_name }}</span>
                                    <span>{{ $post->published_at ? \App\Support\Format::timeAgo($post->published_at) : '' }} &middot; {{ \App\Support\Format::estimateReadMinutes($post->content) }} min read</span>
                                </div>
                            </div>
                        </a>
                    </x-reveal>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
