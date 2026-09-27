<?php

use App\Services\Translator;
use App\Support\Format;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

new class extends Component
{
    public string $text;

    public ?array $blocks = null;

    public ?string $translated = null;

    public bool $showingTranslation = false;

    public ?string $error = null;

    public function mount(): void
    {
        $cacheKey = 'job_trans_en_'.md5(trim($this->text));
        $cached = Cache::get($cacheKey);
        if ($cached) {
            $this->translated = $cached;
        }
    }

    public function toggle(Translator $translator): void
    {
        $this->error = null;

        if ($this->translated !== null && $this->translated !== '') {
            $this->showingTranslation = ! $this->showingTranslation;

            return;
        }

        try {
            $this->translated = $translator->translate($this->text);
            $this->showingTranslation = true;
        } catch (Throwable $e) {
            Log::warning('[translate-toggle] Translation failed: '.$e->getMessage());
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
        class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-horizon-200 bg-horizon-50 px-3.5 py-1 text-xs font-semibold text-horizon-700 transition hover:bg-horizon-100 disabled:opacity-60 cursor-pointer"
        title="Toggle English Translation"
    >
        <x-icon name="globe" class="h-3.5 w-3.5" />
        <span wire:loading.remove wire:target="toggle">{{ $showingTranslation ? 'Show original' : 'Translate to English' }}</span>
        <span wire:loading wire:target="toggle">Translating to English&hellip;</span>
    </button>
    @if ($error)
        <p class="mb-3 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-2.5 flex items-center justify-between">
            <span>{{ $error }}</span>
            <button type="button" wire:click="toggle" class="underline font-semibold text-red-700 hover:text-red-900 ml-2">Retry</button>
        </p>
    @endif

    @if ($showingTranslation && $translated)
        <div class="rounded-xl border border-horizon-100 bg-horizon-50/40 p-4 mb-3">
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-horizon-800 uppercase tracking-wider mb-2">
                <x-icon name="globe" class="h-3 w-3 text-horizon-600" /> English Translation (Auto-translated)
            </span>
            <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{!! Format::linkifyEmployerPlaceholder($translated) !!}</div>
        </div>
    @elseif ($blocks)
        <x-description-blocks :blocks="$blocks" />
    @else
        <div class="whitespace-pre-line text-sm leading-relaxed text-foreground/80">{!! Format::linkifyEmployerPlaceholder($text) !!}</div>
    @endif
</div>
