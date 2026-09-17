<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\CreditsService;
use App\Services\JobRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function show(Request $request, CreditsService $credits)
    {
        $user = $request->user();

        if (! $user) {
            return view('account', [
                'user' => null,
                'redirectTo' => $request->query('next', '/account'),
            ]);
        }

        $unlocks = $user->jobUnlocks()->with('jobListing')->latest('unlocked_at')->get();
        $purchases = $user->creditPurchases()->latest('purchased_at')->get();
        $applications = $user->jobApplications()->with('jobListing')->latest('updated_at')->get();

        return view('account', [
            'user' => $user,
            'unlocks' => $unlocks,
            'balances' => $credits->balances($user),
            'purchases' => $purchases,
            'applications' => $applications,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $name = trim((string) $request->input('name', ''));
        $redirectTo = (string) $request->input('redirectTo', '/account');

        $user = User::where('email', $email)->first();
        $isNewAccount = ! $user;

        if (! $user) {
            $displayName = $name;
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
        $request->session()->regenerate();

        RateLimiter::clear('auth-link:'.$email);

        if ($isNewAccount) {
            try {
                Mail::to($user)->send(new WelcomeEmail($user));
            } catch (\Throwable $e) {
                Log::warning('Welcome email failed to send: '.$e->getMessage(), ['user_id' => $user->id]);
            }

            try {
                app(JobRecommendationService::class)->sendDigestToUser($user);
            } catch (\Throwable $e) {
                Log::warning('Initial job matches digest failed to send: '.$e->getMessage(), ['user_id' => $user->id]);
            }
        }

        if (! str_starts_with($redirectTo, '/') || str_starts_with($redirectTo, '//')) {
            $redirectTo = '/account';
        }

        return redirect($redirectTo);
    }
}
