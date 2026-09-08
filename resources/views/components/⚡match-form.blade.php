<?php

use App\Support\Matching;
use App\Support\MatchOptions;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

new class extends Component
{
    public array $skills = [];

    public array $roles = [];

    public int $yearsExperience = 0;

    public bool $hasProfile = false;

    public function mount(): void
    {
        $profile = Matching::parseProfileCookie(request()->cookie(Matching::COOKIE_NAME));
        if ($profile) {
            $this->skills = $profile['skills'];
            $this->roles = $profile['roles'];
            $this->yearsExperience = $profile['yearsExperience'];
            $this->hasProfile = true;
        }
    }

    public function toggleSkill(string $skill): void
    {
        $this->skills = in_array($skill, $this->skills, true)
            ? array_values(array_diff($this->skills, [$skill]))
            : [...$this->skills, $skill];
    }

    public function toggleRole(string $role): void
    {
        $this->roles = in_array($role, $this->roles, true)
            ? array_values(array_diff($this->roles, [$role]))
            : [...$this->roles, $role];
    }

    public function setExperience(int $years): void
    {
        $this->yearsExperience = $years;
    }

    public function save(): void
    {
        Cookie::queue(Cookie::make(
            Matching::COOKIE_NAME,
            json_encode(['skills' => $this->skills, 'roles' => $this->roles, 'yearsExperience' => $this->yearsExperience]),
            Matching::COOKIE_MINUTES,
            httpOnly: true,
        ));

        $this->redirect('/jobs?sort=match', navigate: false);
    }

    public function clear(): void
    {
        Cookie::queue(Cookie::forget(Matching::COOKIE_NAME));
        $this->skills = [];
        $this->roles = [];
        $this->yearsExperience = 0;
        $this->hasProfile = false;
    }
};
?>

<form wire:submit="save" class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm sm:p-8">
    <fieldset>
        <legend class="text-sm font-semibold">Your skills</legend>
        <p class="mt-0.5 text-xs text-foreground/40">Pick as many as apply.</p>
        <div class="mt-3 space-y-4">
            @foreach (MatchOptions::SKILL_GROUPS as $group => $options)
                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-foreground/40">{{ $group }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($options as $skill)
                            <button
                                type="button"
                                wire:click="toggleSkill('{{ $skill }}')"
                                aria-pressed="{{ in_array($skill, $skills, true) ? 'true' : 'false' }}"
                                class="rounded-full border px-3 py-1.5 text-sm font-medium transition {{ in_array($skill, $skills, true) ? 'border-sunrise-500 bg-sunrise-500 text-white' : 'border-black/10 bg-white text-foreground/70 hover:border-sunrise-300 hover:text-foreground' }}"
                            >
                                {{ $skill }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </fieldset>

    <fieldset class="mt-6">
        <legend class="text-sm font-semibold">Roles you want</legend>
        <p class="mt-0.5 text-xs text-foreground/40">Pick as many as apply.</p>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach (MatchOptions::ROLE_OPTIONS as $role)
                <button
                    type="button"
                    wire:click="toggleRole('{{ $role }}')"
                    aria-pressed="{{ in_array($role, $roles, true) ? 'true' : 'false' }}"
                    class="rounded-full border px-3 py-1.5 text-sm font-medium transition {{ in_array($role, $roles, true) ? 'border-sunrise-500 bg-sunrise-500 text-white' : 'border-black/10 bg-white text-foreground/70 hover:border-sunrise-300 hover:text-foreground' }}"
                >
                    {{ $role }}
                </button>
            @endforeach
        </div>
    </fieldset>

    <fieldset class="mt-6">
        <legend class="text-sm font-semibold">Years of experience</legend>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach (MatchOptions::EXPERIENCE_BANDS as $band)
                <button
                    type="button"
                    wire:click="setExperience({{ $band['years'] }})"
                    aria-pressed="{{ $yearsExperience === $band['years'] ? 'true' : 'false' }}"
                    class="rounded-full border px-3 py-1.5 text-sm font-medium transition {{ $yearsExperience === $band['years'] ? 'border-sunrise-500 bg-sunrise-500 text-white' : 'border-black/10 bg-white text-foreground/70 hover:border-sunrise-300 hover:text-foreground' }}"
                >
                    {{ $band['label'] }}
                </button>
            @endforeach
        </div>
    </fieldset>

    <button
        type="submit"
        @disabled(count($skills) === 0 && count($roles) === 0)
        wire:loading.attr="disabled"
        wire:target="save"
        class="btn-pop mt-6 w-full rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg disabled:opacity-60"
    >
        <span wire:loading.remove wire:target="save">Show my matches</span>
        <span wire:loading wire:target="save">Matching&hellip;</span>
    </button>

    @if ($hasProfile)
        <button type="button" wire:click="clear" class="mt-3 w-full text-center text-xs text-foreground/40 hover:underline">
            Clear my saved profile
        </button>
    @endif
</form>
