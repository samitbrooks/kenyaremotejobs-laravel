<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the administrator login form.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return redirect('/admin');
        }

        return view('admin.login', [
            'redirectTo' => $request->query('next', '/admin'),
        ]);
    }

    /**
     * Authenticate administrative credentials.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');
        $remember = (bool) $request->boolean('remember', false);
        $redirectTo = (string) $request->input('redirectTo', '/admin');

        $throttleKey = 'admin-login:'.Str::transliterate($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Too many administrative login attempts. Please try again in {$seconds} seconds.",
                ]);
        }

        // Verify the email is on the authorized administrator allowlist
        $adminEmails = config('jobs.admin_emails', []);
        if (! in_array($email, $adminEmails, true)) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'These administrative credentials do not match our authorized records.',
                ]);
        }

        $user = User::where('email', $email)->first();
        $envPassword = config('jobs.admin_password');

        // Check password against existing user record or config/env fallback
        $passwordMatches = false;

        if ($user && $user->password) {
            $passwordMatches = Hash::check($password, $user->password);
        }

        // If env ADMIN_PASSWORD is set and matches the entered password
        if (! $passwordMatches && ! empty($envPassword) && hash_equals((string) $envPassword, $password)) {
            $passwordMatches = true;

            if ($user) {
                $user->password = Hash::make($password);
                $user->save();
            }
        }

        // If the admin user does not yet exist in the DB, but is in ADMIN_EMAILS and matches configured password or first setup
        if (! $user && $passwordMatches) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
        }

        if (! $passwordMatches || ! $user) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'password' => 'Invalid administrator password.',
                ]);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        if (! str_starts_with($redirectTo, '/') || str_starts_with($redirectTo, '//')) {
            $redirectTo = '/admin';
        }

        return redirect()->intended($redirectTo)->with('success', 'Logged in to Administrator Panel successfully.');
    }

    /**
     * Log out of administrative session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been safely logged out of the Administrator Panel.');
    }
}
