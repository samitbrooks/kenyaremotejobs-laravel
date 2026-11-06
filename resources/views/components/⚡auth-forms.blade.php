<?php

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\JobRecommendationService;
use App\Support\SafeUrl;
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

    public ?string $sentTo = null;

    public ?string $error = null;

    public function mount(string $redirectTo = '/account'): void
    {
        $this->redirectTo = SafeUrl::redirectPath($redirectTo, '/account');
        if (request()->query('mode') === 'login') {
            $this->mode = 'login';
        }
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
        $this->email = '';
        $this->error = null;
    }

    public function submit(): void
    {
        $this->error = null;

        $throttleKey = 'auth-forms:'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->error = "Too many login attempts. Please wait {$seconds} seconds.";

            return;
        }
        RateLimiter::hit($throttleKey, 60);

        $email = strtolower(trim($this->email));

        if (! $email || ! str_contains($email, '@') || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = 'Please enter a valid email address.';

            return;
        }

        if (in_array($email, config('jobs.admin_emails', []), true)) {
            session()->flash('info', 'Administrator accounts must log in through the Administrator Portal.');
            $this->redirect(route('admin.login', ['next' => $this->redirectTo ?: '/admin']), navigate: true);

            return;
        }

        // Clear any previous throttle locks on this email
        RateLimiter::clear('auth-link:'.$email);

        $user = User::where('email', $email)->first();
        if ($user?->isBanned()) {
            $this->error = 'This account is blocked.';

            return;
        }

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
                'email_verified_at' => null,
            ]);
        }

        $safeRedirect = SafeUrl::redirectPath($this->redirectTo, '/account');
        $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'auth.link',
            now()->addMinutes(30),
            ['user' => $user->id, 'redirectTo' => $safeRedirect]
        );

        try {
            Mail::to($user->email)->send(new \App\Mail\LoginLinkEmail($user, $signedUrl, $isNewAccount));
        } catch (\Throwable $e) {
            Log::warning('Login link email dispatch failed: '.$e->getMessage(), ['user_id' => $user->id]);
        }

        $this->sentTo = $email;
    }
};
?>

<div class="mx-auto max-w-sm rounded-3xl border border-black/5 bg-white p-8 shadow-lg">
    @if ($sentTo)
        <div class="text-center py-4">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#eef0fe] text-[#3444da] mb-4">
                <x-icon name="mail" class="h-7 w-7" />
            </div>
            <h2 class="text-xl font-bold text-slate-900">Check your email</h2>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                We sent a secure login link to <strong class="text-slate-900">{{ $sentTo }}</strong>. Click it to {{ $mode === 'signup' ? 'confirm your account' : 'log in' }} &mdash; the link expires in 30 minutes.
            </p>
            <button
                type="button"
                wire:click="useDifferentEmail"
                class="mt-5 inline-block text-xs font-bold text-[#3444da] hover:underline cursor-pointer"
            >
                &larr; Use a different email address
            </button>
        </div>
    @else
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
                <span wire:loading.remove>{{ $mode === 'signup' ? 'Send Confirmation Link' : 'Send Login Link' }} &rarr;</span>
                <span wire:loading>Sending link&hellip;</span>
            </button>

            <p class="text-center text-xs text-foreground/50 pt-1">No password needed &bull; We email you a secure 1-click link</p>
        </form>
    @endif
</div>
