<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreeTrialController extends Controller
{
    /**
     * Activate a 24-hour free trial for the current authenticated user,
     * or prompt unauthenticated visitors to log in / sign up first.
     */
    public function activate(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/account?next=%2Ftrial%2Factivate&trial=1')->with(
                'info',
                'Enter your email below to activate your 24-hour free trial pass instantly.'
            );
        }

        if ($user->subscribed) {
            return redirect('/jobs')->with(
                'info',
                'You already have unlimited Pro membership access!'
            );
        }

        if ($user->onTrial()) {
            return redirect('/jobs')->with(
                'info',
                "Your 24-hour free trial is already active (expires in {$user->trialRemainingHuman()})."
            );
        }

        if ($user->hasUsedTrial() && ! $user->isAdmin()) {
            return redirect()->route('pricing')->with(
                'error',
                'You have already redeemed your 24-hour free trial pass. Subscribe below for unlimited access.'
            );
        }

        $user->startFreeTrial(24);

        $expiresAt = $user->trial_ends_at ? $user->trial_ends_at->format('M j, g:i A') : 'in 24 hours';

        return redirect('/jobs')->with(
            'success',
            "🎉 Your 24-hour free trial is now active! You have full access to browse and apply to all remote jobs until {$expiresAt}."
        );
    }

    /**
     * One-click magic activation link sent via email (FlexJobs-style).
     * Automatically signs the user in and activates their 24-hour trial.
     */
    public function claimViaSignedLink(Request $request, User $user): RedirectResponse
    {
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($user->subscribed) {
            return redirect('/jobs')->with(
                'info',
                'Welcome back! You already have full unlimited access as a subscribed member.'
            );
        }

        if ($user->onTrial()) {
            return redirect('/jobs')->with(
                'info',
                "Welcome back, {$user->firstName()}! Your 24-hour free pass is currently active (expires in {$user->trialRemainingHuman()})."
            );
        }

        if ($user->hasUsedTrial() && ! $user->isAdmin()) {
            return redirect()->route('pricing')->with(
                'info',
                'Your previous 24-hour trial has expired. Subscribe to keep full access to apply to all remote jobs.'
            );
        }

        $user->startFreeTrial(24);

        $expiresAt = $user->trial_ends_at ? $user->trial_ends_at->format('M j, g:i A') : 'in 24 hours';

        return redirect('/jobs')->with(
            'success',
            "🎉 Welcome, {$user->firstName()}! Your 24-hour free trial has been activated. Enjoy full access to all remote jobs until {$expiresAt}."
        );
    }
}
