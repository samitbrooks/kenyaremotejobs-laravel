@props(['blocks'])

<div class="space-y-3 text-sm leading-relaxed text-foreground/80">
    @foreach ($blocks as $block)
        @switch($block['type'])
            @case('heading')
                <h3 class="pt-2 font-semibold text-foreground">{{ $block['text'] }}</h3>
                @break

            @case('bullets')
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($block['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                @break

            @case('numbered')
                <ol class="list-decimal space-y-1 pl-5">
                    @foreach ($block['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ol>
                @break

            @default
                <p>{{ $block['text'] }}</p>
        @endswitch
    @endforeach
</div>
