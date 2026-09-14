<x-layouts.app
    :title="$post->title"
    :description="\App\Support\Format::truncate($post->excerpt, 155)"
    :canonical="url('/journal/'.$post->slug)"
    :og-image="$post->image_url"
    og-type="article"
>
    <script type="application/ld+json">{!! \App\Support\Seo::articleJsonLd($post) !!}</script>
    <script type="application/ld+json">{!! \App\Support\Seo::articleBreadcrumbJsonLd($post) !!}</script>

    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs text-foreground/50">
            <a href="{{ url('/') }}" class="hover:text-foreground">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ url('/journal') }}" class="hover:text-foreground">The Journal</a>
            <span>&rsaquo;</span>
            <span class="font-medium text-foreground/80 truncate max-w-xs">{{ $post->title }}</span>
        </nav>

        <x-reveal>
            <div class="flex items-center gap-2">
                <span class="inline-block rounded-full bg-horizon-100 px-3 py-1 text-xs font-semibold text-horizon-800">
                    {{ $post->category }}
                </span>
                <span class="text-xs text-foreground/50">
                    {{ \App\Support\Format::estimateReadMinutes($post->content) }} min read
                </span>
            </div>

            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-horizon-900 sm:text-4xl lg:text-5xl leading-tight">
                {{ $post->title }}
            </h1>

            <p class="mt-3 text-sm text-foreground/60">
                By <strong class="text-foreground/80 font-semibold">{{ $post->author_name }}</strong> &middot; Published {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
            </p>

            <div class="mt-6 overflow-hidden rounded-3xl border border-black/5 bg-horizon-100 shadow-sm">
                <img
                    src="{{ $post->image_url }}"
                    alt="{{ $post->title }}"
                    class="aspect-[16/9] w-full object-cover"
                    loading="eager"
                >
            </div>

            <div class="mt-6 rounded-2xl border-l-4 border-sunrise-500 bg-sunrise-50/60 p-4 text-sm leading-relaxed text-foreground/80">
                <strong class="font-semibold text-horizon-900">Key Takeaway:</strong> {{ $post->excerpt }}
            </div>

            {{-- Formatted Content with Semantic HTML Typography --}}
            <article class="mt-8 space-y-4 text-base leading-relaxed text-foreground/80 [&>h2]:mt-8 [&>h2]:text-2xl [&>h2]:font-bold [&>h2]:text-horizon-900 [&>h3]:mt-6 [&>h3]:text-xl [&>h3]:font-bold [&>h3]:text-horizon-800 [&>ul]:list-disc [&>ul]:pl-6 [&>ul]:space-y-2 [&>ol]:list-decimal [&>ol]:pl-6 [&>ol]:space-y-2 [&>p]:leading-relaxed [&>blockquote]:border-l-4 [&>blockquote]:border-horizon-400 [&>blockquote]:pl-4 [&>blockquote]:italic [&>blockquote]:text-foreground/70 [&>a]:text-sunrise-600 [&>a]:underline hover:[&>a]:text-sunrise-700">
                {!! \Illuminate\Support\Str::markdown($post->content) !!}
            </article>

            {{-- In-article CTA --}}
            <div class="mt-12 rounded-3xl border border-horizon-200 bg-gradient-to-r from-horizon-900 via-horizon-800 to-slate-900 p-8 text-white shadow-xl">
                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-sunrise-400">
                        <x-icon name="sparkle" class="h-3.5 w-3.5" /> Verified Remote Roles
                    </span>
                    <h3 class="mt-2 text-2xl font-bold">Ready to find your next remote role?</h3>
                    <p class="mt-2 text-sm text-white/70">
                        Browse 800+ international remote jobs specifically vetted for East Africa Timezone and Kenyan applicant eligibility.
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-sunrise-500 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-sunrise-600">
                            Browse Open Remote Jobs &rarr;
                        </a>
                        <a href="{{ url('/match') }}" class="rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Calculate CV Match Score
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-black/5 pt-6 flex items-center justify-between">
                <a href="{{ url('/journal') }}" class="text-sm font-semibold text-horizon-700 hover:text-horizon-900 hover:underline">
                    &larr; Back to all articles
                </a>
            </div>
        </x-reveal>
    </div>
</x-layouts.app>
