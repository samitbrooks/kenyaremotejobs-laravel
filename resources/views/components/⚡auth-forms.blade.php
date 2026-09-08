<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $redirectTo = '/account';

    public string $mode = 'signup';

    public string $email = '';

    public string $password = '';

    public ?string $error = null;

    public function mount(string $redirectTo = '/account'): void
    {
        $this->redirectTo = $redirectTo;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->error = null;
    }

    public function submit(): void
    {
        $this->mode === 'signup' ? $this->signup() : $this->login();
    }

    private function signup(): void
    {
        $this->error = null;
        $email = strtolower(trim($this->email));

        if (! $email || ! str_contains($email, '@') || strlen($this->password) < 6) {
            $this->error = 'Enter a valid email and a password of at least 6 characters.';

            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->error = 'An account with that email already exists.';

            return;
        }

        $user = User::create(['email' => $email, 'password' => $this->password]);
        Auth::login($user, remember: true);

        $this->redirect($this->redirectTo, navigate: false);
    }

    private function login(): void
    {
        $this->error = null;
        $email = strtolower(trim($this->email));

        if (! Auth::attempt(['email' => $email, 'password' => $this->password], remember: true)) {
            $this->error = 'Invalid email or password.';

            return;
        }

        $this->redirect($this->redirectTo, navigate: false);
    }
};
?>

<div class="mx-auto max-w-sm rounded-3xl border border-black/5 bg-white p-8 shadow-lg">
    <div class="mb-6 flex rounded-full bg-horizon-50 p-1 text-sm font-semibold">
        <button
            type="button"
            wire:click="setMode('signup')"
            class="flex-1 rounded-full py-2 transition {{ $mode === 'signup' ? 'bg-white shadow-sm' : 'text-foreground/50' }}"
        >
            Sign up
        </button>
        <button
            type="button"
            wire:click="setMode('login')"
            class="flex-1 rounded-full py-2 transition {{ $mode === 'login' ? 'bg-white shadow-sm' : 'text-foreground/50' }}"
        >
            Log in
        </button>
    </div>

    <form wire:submit="submit" class="space-y-3">
        <input
            type="email"
            wire:model="email"
            required
            placeholder="Email address"
            class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400"
        >
        <input
            type="password"
            wire:model="password"
            required
            minlength="6"
            placeholder="Password (min 6 characters)"
            class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400"
        >

        @if ($error)
            <p class="text-sm text-red-600">{{ $error }}</p>
        @endif

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="btn-pop w-full rounded-full gradient-sunrise px-6 py-2.5 font-semibold text-white shadow-md transition hover:opacity-90 disabled:opacity-60"
        >
            <span wire:loading.remove>{{ $mode === 'signup' ? 'Create account' : 'Log in' }}</span>
            <span wire:loading>Please wait&hellip;</span>
        </button>
    </form>
</div>
