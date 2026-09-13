<?php

use App\Models\JobListing;
use App\Services\AiTailorService;
use App\Support\Matching;
use Livewire\Component;

new class extends Component
{
    public string $jobId;

    public bool $isOpen = false;

    public bool $loading = false;

    public ?array $tailored = null;

    public string $userNotes = '';

    public ?string $copied = null;

    public function open(): void
    {
        if (! auth()->check()) {
            $this->redirect('/account?next=/jobs/'.$this->jobId, navigate: false);

            return;
        }

        $this->isOpen = true;
        if (! $this->tailored && auth()->user()->canUseAiTailor()) {
            $this->tailor();
        }
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->copied = null;
    }

    public function tailor(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canUseAiTailor()) {
            return;
        }

        $this->loading = true;
        $job = JobListing::findOrFail($this->jobId);
        $profile = Matching::parseProfileCookie(request()->cookie(Matching::COOKIE_NAME));

        $ai = app(AiTailorService::class);
        $this->tailored = $ai->tailor($job, $profile, $this->userNotes ?: null);

        // Deduct free credit if not subscribed
        $user->useAiTailor();
        $this->loading = false;
    }

    public function markCopied(string $type): void
    {
        $this->copied = $type;
    }
};
?>

<div>
    {{-- Trigger button --}}
    <button
        type="button"
        wire:click="open"
        class="btn-pop group flex items-center justify-center gap-2 rounded-2xl border-2 border-horizon-300 bg-gradient-to-r from-horizon-50 to-indigo-50 px-5 py-3.5 text-sm font-bold text-horizon-900 shadow-sm transition hover:border-horizon-500 hover:shadow-md"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-horizon-700 text-white shadow-xs">
            <x-icon name="sparkle" class="h-3.5 w-3.5 animate-pulse" />
        </span>
        <span>Tailor CV &amp; Cover Letter with AI</span>
        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-extrabold text-emerald-800">
            Boost to 94%+
        </span>
    </button>

    {{-- Modal Drawer --}}
    @if ($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-xs">
            <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
                {{-- Close button --}}
                <button type="button" wire:click="close" class="absolute top-6 right-6 flex h-8 w-8 items-center justify-center rounded-full bg-black/5 text-foreground/50 hover:bg-black/10 hover:text-foreground">
                    &times;
                </button>

                @php $user = auth()->user(); @endphp

                @if (! $user?->canUseAiTailor())
                    <div class="py-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-horizon-100 text-horizon-800">
                            <x-icon name="sparkle" class="h-8 w-8" />
                        </div>
                        <h3 class="mt-4 text-2xl font-bold">You&rsquo;ve used your free AI tailoring</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm text-foreground/60">
                            Subscribers get unlimited 1-click AI resume &amp; cover letter tailoring on every job to bypass ATS filters and stand out to foreign recruiters.
                        </p>
                        <div class="mx-auto mt-6 max-w-xs">
                            <a href="{{ url('/pricing') }}" class="btn-pop block rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg">
                                Upgrade to Pro &mdash; KES 1,499
                            </a>
                        </div>
                    </div>
                @elseif ($loading)
                    <div class="py-16 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-horizon-100 text-horizon-700">
                            <x-icon name="sparkle" class="h-6 w-6 animate-spin" />
                        </div>
                        <p class="mt-4 font-bold text-horizon-900">Tailoring your CV with AI...</p>
                        <p class="mt-1 text-xs text-foreground/50">Extracting job keywords, optimizing ATS bullet points, and writing your cover letter</p>
                    </div>
                @elseif ($tailored)
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-horizon-700 text-white">
                                <x-icon name="sparkle" class="h-5 w-5" />
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-foreground">AI Role Tailoring Package</h3>
                                <p class="text-xs text-foreground/50">Optimized for ATS scanners and foreign hiring teams</p>
                            </div>
                            <div class="ml-auto text-right">
                                <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                    {{ $tailored['match_score'] }}% ATS Match
                                </span>
                            </div>
                        </div>

                        {{-- Timezone & Kenya Advantage Callout --}}
                        <div class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 text-xs text-indigo-950">
                            <p class="font-bold flex items-center gap-1.5">
                                <x-icon name="globe" class="h-4 w-4 text-indigo-700" />
                                Your Timezone Pitch (Copy into Interview / Intro):
                            </p>
                            <p class="mt-1 leading-relaxed text-indigo-900">{{ $tailored['kenya_advantage'] }}</p>
                        </div>

                        {{-- Tailored Bullet Points --}}
                        <div class="mt-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-foreground">Tailored Resume Bullet Points</h4>
                                <button
                                    type="button"
                                    x-data
                                    @click="navigator.clipboard.writeText(`{{ implode("\n", $tailored['tailored_bullets']) }}`); $wire.markCopied('bullets')"
                                    class="text-xs font-semibold text-sunrise-600 hover:underline"
                                >
                                    {{ $copied === 'bullets' ? '✓ Copied!' : 'Copy bullets' }}
                                </button>
                            </div>
                            <p class="mt-0.5 text-xs text-foreground/40">Paste these directly into your CV work experience section:</p>
                            <div class="mt-2 space-y-2 rounded-2xl border border-black/5 bg-black/[0.02] p-4 text-xs leading-relaxed text-foreground/80">
                                @foreach ($tailored['tailored_bullets'] as $bullet)
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-sunrise-500">&bull;</span>
                                        <p>{{ $bullet }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tailored Cover Letter --}}
                        <div class="mt-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-foreground">Tailored Cover Letter</h4>
                                <button
                                    type="button"
                                    x-data
                                    @click="navigator.clipboard.writeText(`{{ addslashes($tailored['tailored_cover_letter']) }}`); $wire.markCopied('letter')"
                                    class="text-xs font-semibold text-sunrise-600 hover:underline"
                                >
                                    {{ $copied === 'letter' ? '✓ Copied!' : 'Copy letter' }}
                                </button>
                            </div>
                            <div class="mt-2 rounded-2xl border border-black/5 bg-black/[0.02] p-4 text-xs whitespace-pre-line leading-relaxed text-foreground/80">
                                {{ $tailored['tailored_cover_letter'] }}
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <a href="{{ url('/resume-builder') }}" class="btn-pop flex-1 rounded-full gradient-sunrise px-5 py-2.5 text-center text-xs font-semibold text-white shadow-md">
                                Open in CV Builder &rarr;
                            </a>
                            <button type="button" wire:click="close" class="rounded-full border border-black/10 px-5 py-2.5 text-xs font-semibold text-foreground/60 hover:bg-black/5">
                                Done
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
