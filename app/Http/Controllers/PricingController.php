<?php

namespace App\Http\Controllers;

use App\Services\CreditsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request, CreditsService $credits): View
    {
        $user = $request->user();

        $requestedPlan = $request->query('plan', 'pro');
        $selectedPlan = is_string($requestedPlan) && in_array($requestedPlan, ['pro', 'monthly', 'quarterly', 'yearly'], true)
            ? $requestedPlan
            : 'pro';

        return view('pricing', [
            'user' => $user,
            'selectedPlan' => $selectedPlan,
            'balances' => $user ? $credits->balances($user) : null,
        ]);
    }
}
