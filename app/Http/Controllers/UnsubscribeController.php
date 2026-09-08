<?php

namespace App\Http\Controllers;

use App\Models\User;

class UnsubscribeController extends Controller
{
    // The link is a signed URL (Laravel verifies the signature via the
    // 'signed' middleware before this ever runs), not a session-authed
    // action — it has to work from a cold click in an email client with no
    // login, and a tamper-proof signature is what keeps that safe without
    // one. One click, no confirmation page in between: that's what mailbox
    // providers' one-click List-Unsubscribe expects.
    public function __invoke(User $user)
    {
        $user->update(['marketing_opt_out_at' => now()]);

        return view('unsubscribed', ['email' => $user->email]);
    }
}
