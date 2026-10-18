<?php

namespace App\Http\Middleware;

use App\Services\ReferralService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferralCode
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $refCode = $request->query('ref');

        if ($refCode && is_string($refCode)) {
            $cleanCode = strtoupper(trim(substr($refCode, 0, 32)));
            if ($cleanCode !== '') {
                $request->session()->put(ReferralService::COOKIE_NAME, $cleanCode);
                $response = $next($request);

                return $response->withCookie(
                    cookie(
                        name: ReferralService::COOKIE_NAME,
                        value: $cleanCode,
                        minutes: ReferralService::COOKIE_LIFETIME_MINUTES,
                        path: '/',
                        secure: $request->isSecure(),
                        httpOnly: false
                    )
                );
            }
        }

        return $next($request);
    }
}
