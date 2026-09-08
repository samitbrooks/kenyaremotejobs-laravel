<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return view('account', [
                'user' => null,
                'redirectTo' => $request->query('next', '/account'),
            ]);
        }

        $unlocks = $user->jobUnlocks()->with('jobListing')->latest('unlocked_at')->get();

        return view('account', [
            'user' => $user,
            'unlocks' => $unlocks,
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
