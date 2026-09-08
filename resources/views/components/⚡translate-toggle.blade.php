<?php

use App\Services\Translator;
use App\Support\Format;
use Livewire\Component;

new class extends Component
{
    public string $text;

    public ?array $blocks = null;

    public ?string $translated = null;

    public bool $showingTranslation = false;

    public ?string $error = null;

    public function toggle(Translator $translator): void
    {
        if ($this->translated !== null) {
            $this->showingTranslation = ! $this->showingTranslation;

            return;
        }

        $this->error = null;

        try {
            $this->translated = $translator->translate($this->text);
            $this->showingTranslation = true;
        } catch (Throwable) {
            $this->error = "Couldn't translate this right now — try again in a moment.";
        }
    }
};
?>

<div>
    <button
        type="button"
        wire:click="toggle"
        wire:loading.attr="disabled"
        wire:target="toggle"
        class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-horizon-200 bg-horizon-50 px-3 py-1 text-xs font-semibold text-horizon-700 transition hover:bg-horizon-100 disabled:opacity-60"
    >
        <x-icon name="globe" />
        <span wire:loading.remove wire:target="toggle">{{ $showingTranslation ? 'Show original' : 'Translate to English' }}</span>
        <span wire:loading wire:target="toggle">Translating&hellip;</span>
    </button>
    @if ($error)
        <p class="mb-3 text-xs text-red-600">{{ $error }}</p>
    @endif

    @if ($showingTranslation && $translated)
        <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{!! Format::linkifyEmployerPlaceholder($translated) !!}</div>
    @elseif ($blocks)
        <x-description-blocks :blocks="$blocks" />
    @else
        <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{!! Format::linkifyEmployerPlaceholder($text) !!}</div>
    @endif
</div>
