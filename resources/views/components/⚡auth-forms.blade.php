<?php

use App\Mail\LoginLinkEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public string $redirectTo = '/account';

    public string $mode = 'signup';

    public string $name = '';

    public string $email = '';

    public ?string $error = null;

    public ?string $sentTo = null;

    public function mount(string $redirectTo = '/account'): void
    {
        $this->redirectTo = $redirectTo;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->error = null;
        $this->sentTo = null;
    }

    public function useDifferentEmail(): void
    {
        $this->sentTo = null;
        $this->error = null;
    }

    public function submit(): void
    {
        $this->error = null;
        $email = strtolower(trim($this->email));

        if (! $email || ! str_contains($email, '@')) {
            $this->error = 'Enter a valid email address.';

            return;
        }

        if ($this->mode === 'signup' && ! trim($this->name)) {
            $this->error = 'Enter your name.';

            return;
        }

        // Keyed by email (not IP) since the thing being protected against is
        // someone else's inbox getting flooded, regardless of who's sending.
        $throttleKey = 'auth-link:'.$email;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->error = 'Too many attempts for that address — try again in a few minutes.';

            return;
        }

        $user = User::where('email', $email)->first();

        if ($this->mode === 'login' && ! $user) {
            RateLimiter::hit($throttleKey, 600);
            $this->error = "No account found for that email — try Sign up instead.";

            return;
        }

        $isNewAccount = ! $user;
        $user ??= User::create([
            'name' => trim($this->name),
            'email' => $email,
            // Never used to log in — every login goes through the emailed
            // link instead. Kept non-null only because the column still
            // requires a value.
            'password' => Str::random(40),
        ]);

        RateLimiter::hit($throttleKey, 600);

        $url = URL::temporarySignedRoute('auth.link', now()->addMinutes(30), [
            'user' => $user->id,
            'redirectTo' => $this->redirectTo,
        ]);

        try {
            Mail::to($user)->send(new LoginLinkEmail($user, $url, $isNewAccount));
            $this->sentTo = $email;
        } catch (\Throwable $e) {
            Log::warning('Login link email failed to send: '.$e->getMessage(), ['user_id' => $user->id]);
            $this->error = "Couldn't send that email right now — please try again in a moment.";
        }
    }
};
?>

<div class="mx-auto max-w-sm rounded-3xl border border-black/5 bg-white p-8 shadow-lg">
    @if ($sentTo)
        <div class="text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-horizon-50">
                <x-icon name="mail" class="h-6 w-6 text-horizon-600" />
            </div>
            <h2 class="mt-4 font-semibold">Check your email</h2>
            <p class="mt-1 text-sm text-foreground/60">
                We sent a link to <span class="font-medium text-foreground">{{ $sentTo }}</span>. Click it to {{ $mode === 'signup' ? 'confirm your account' : 'log in' }} — it expires in 30 minutes.
            </p>
            <button
                type="button"
                wire:click="useDifferentEmail"
                class="mt-4 text-sm font-medium text-horizon-600 hover:underline"
            >
                Use a different email
            </button>
        </div>
    @else
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
            @if ($mode === 'signup')
                <input
                    type="text"
                    wire:model="name"
                    required
                    placeholder="Full name"
                    class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-sunrise-400"
                >
            @endif
            <input
                type="email"
                wire:model="email"
                required
                placeholder="Email address"
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
                <span wire:loading.remove>{{ $mode === 'signup' ? 'Send confirmation link' : 'Send login link' }}</span>
                <span wire:loading>Please wait&hellip;</span>
            </button>

            <p class="text-center text-xs text-foreground/50">No password needed — we'll email you a link.</p>
        </form>
    @endif
</div>
