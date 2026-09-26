<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admins, active subscribers, and users with an active 24-hour trial have full access
        if ($user && ($user->isAdmin() || $user->subscribed || $user->onTrial())) {
            return $next($request);
        }

        // Allow verified search engine crawlers to index metadata and rich results
        if ($this->isSearchEngineBot($request)) {
            return $next($request);
        }

        $message = ($user && $user->hasUsedTrial())
            ? 'Your 24-hour free trial has ended. Subscribe below to continue browsing and applying to all remote jobs.'
            : 'KenyaRemoteJobs is a membership-only platform. Start your 24-hour free trial or subscribe to browse and apply to all remote jobs.';

        return redirect()->route('pricing')->with('info', $message);
    }

    private function isSearchEngineBot(Request $request): bool
    {
        $userAgent = strtolower((string) $request->header('User-Agent', ''));

        if ($userAgent === '') {
            return false;
        }

        $bots = [
            'googlebot',
            'bingbot',
            'yandexbot',
            'duckduckbot',
            'slurp',
            'baiduspider',
            'facebot',
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'applebot',
        ];

        foreach ($bots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true;
            }
        }

        return false;
    }
}
