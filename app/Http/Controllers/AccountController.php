<?php

namespace App\Http\Controllers;

use App\Services\CreditsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('account', [
            'user' => $user,
            'unlocks' => $unlocks,
            'balances' => $credits->balances($user),
            'purchases' => $purchases,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
