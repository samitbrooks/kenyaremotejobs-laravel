<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\JobRecommendationService;
use App\Support\SafeUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

// The single destination for every signup-confirmation and login-link
// email — the `signed` route middleware (see routes/web.php) is what
// actually verifies the link hasn't expired or been tampered with; this
// controller only runs once that's already passed.
class AuthLinkController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $redirectTo = SafeUrl::redirectPath($request->query('redirectTo'), '/account');

        if ($user->isConfiguredAdmin()) {
            return redirect()->route('admin.login', ['next' => $redirectTo])
                ->with('info', 'Administrator accounts must authenticate via the secure Admin Portal with credentials.');
        }

        $firstConfirmation = $user->email_verified_at === null;

        if ($firstConfirmation) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        if ($firstConfirmation) {
            try {
                Mail::to($user)->send(new WelcomeEmail($user));
            } catch (Throwable $e) {
                Log::warning('Welcome email failed to send: '.$e->getMessage(), ['user_id' => $user->id]);
            }

            try {
                app(JobRecommendationService::class)->sendDigestToUser($user);
            } catch (Throwable $e) {
                Log::warning('Initial job matches digest failed to send: '.$e->getMessage(), ['user_id' => $user->id]);
            }
        }

        return redirect($redirectTo);
    }
}
