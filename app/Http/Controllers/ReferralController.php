<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReferralController extends Controller
{
    public function index(Request $request, ReferralService $referralService): View|Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/account?next=%2Freferrals')->with(
                'info',
                'Log in or sign up with your email to get your unique referral link and earn free Pro access + AI CV tailors.'
            );
        }

        $stats = $referralService->getStats($user);

        return view('referrals', [
            'user' => $user,
            'referralCode' => $user->referralCode(),
            'referralLink' => $user->referralLink(),
            'stats' => $stats,
            'whatsappShareUrl' => $user->whatsappShareUrl(),
            'twitterShareUrl' => $user->twitterShareUrl(),
            'telegramShareUrl' => $user->telegramShareUrl(),
            'linkedinShareUrl' => $user->linkedinShareUrl(),
        ]);
    }
}
