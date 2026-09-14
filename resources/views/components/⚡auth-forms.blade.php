<?php

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public string $redirectTo = '/account';

    public string $mode = 'signup';

    public string $name = '';

    public string $email = '';

    public ?string $error = null;

    public function mount(string $redirectTo = '/account'): void
    {
        $this->redirectTo = $redirectTo;
        if (request()->query('mode') === 'login') {
            $this->mode = 'login';
        }
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->error = null;
    }

    public function submit(): void
    {
        $this->error = null;
        $email = strtolower(trim($this->email));

        if (! $email || ! str_contains($email, '@') || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = 'Please enter a valid email address.';

            return;
        }

        // Clear any previous throttle locks on this email
        RateLimiter::clear('auth-link:'.$email);

        $user = User::where('email', $email)->first();
        $isNewAccount = ! $user;

        if (! $user) {
            $displayName = trim($this->name);
            if (! $displayName) {
                $parts = explode('@', $email);
                $displayName = ucwords(str_replace(['.', '_', '-'], ' ', $parts[0]));
            }

            $user = User::create([
                'name' => $displayName,
                'email' => $email,
                'password' => Str::random(40),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        if ($isNewAccount) {
            try {
                Mail::to($user)->send(new WelcomeEmail($user));
            } catch (\Throwable $e) {
                Log::warning('Welcome email dispatch failed: '.$e->getMessage(), ['user_id' => $user->id]);
            }
        }

        $target = $this->redirectTo;
        if (! is_string($target) || ! str_starts_with($target, '/') || str_starts_with($target, '//')) {
            $target = '/account';
        }

        $this->redirect($target);
    }
};
?>

<div class="mx-auto max-w-sm rounded-3xl border border-black/5 bg-white p-8 shadow-lg">
    <div class="mb-6 flex rounded-full bg-horizon-50 p-1 text-sm font-semibold">
        <button
            type="button"
            wire:click="setMode('signup')"
            class="flex-1 rounded-full py-2 transition {{ $mode === 'signup' ? 'bg-white shadow-sm text-foreground font-bold' : 'text-foreground/50 hover:text-foreground' }}"
        >
            Sign up
        </button>
        <button
            type="button"
            wire:click="setMode('login')"
            class="flex-1 rounded-full py-2 transition {{ $mode === 'login' ? 'bg-white shadow-sm text-foreground font-bold' : 'text-foreground/50 hover:text-foreground' }}"
        >
            Log in
        </button>
    </div>

    <form action="{{ route('account.login') }}" method="POST" wire:submit="submit" class="space-y-3">
        @csrf
        <input type="hidden" name="redirectTo" value="{{ $redirectTo }}">
        <input type="hidden" name="mode" value="{{ $mode }}">

        @if ($mode === 'signup')
            <input
                type="text"
                name="name"
                wire:model="name"
                placeholder="Full name (optional)"
                class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
            >
        @endif

        <input
            type="email"
            name="email"
            wire:model="email"
            required
            placeholder="Email address"
            class="w-full rounded-lg border border-black/10 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"
        >

        @if ($error)
            <p class="text-xs text-red-600 font-medium">{{ $error }}</p>
        @endif

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="btn-pop w-full rounded-full gradient-sunrise px-6 py-2.5 font-semibold text-white shadow-md transition hover:opacity-90 disabled:opacity-60 text-sm"
        >
            <span wire:loading.remove>{{ $mode === 'signup' ? 'Create Account & Continue' : 'Log in & Continue' }} &rarr;</span>
            <span wire:loading>Logging in&hellip;</span>
        </button>

        <p class="text-center text-xs text-foreground/50 pt-1">Instant 1-click access &bull; No password needed</p>
    </form>
</div>
