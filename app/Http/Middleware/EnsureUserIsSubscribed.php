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

        // Admins and active subscribers have full access to the jobs section
        if ($user && ($user->isAdmin() || $user->subscribed)) {
            return $next($request);
        }

        // Allow verified search engine crawlers to index metadata and rich results
        if ($this->isSearchEngineBot($request)) {
            return $next($request);
        }

        return redirect()->route('pricing')->with(
            'info',
            'KenyaRemoteJobs is a membership-only platform. Subscribe to unlock full access to browse and apply to our curated remote jobs board.'
        );
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
