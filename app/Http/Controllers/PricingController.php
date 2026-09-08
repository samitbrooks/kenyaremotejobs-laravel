<?php

namespace App\Http\Controllers;

use App\Services\CreditsService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index(Request $request, CreditsService $credits)
    {
        $user = $request->user();

        return view('pricing', [
            'user' => $user,
            'balances' => $user ? $credits->balances($user) : null,
        ]);
    }
}
