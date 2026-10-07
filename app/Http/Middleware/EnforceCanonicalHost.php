<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceCanonicalHost
{
    /**
     * Redirect www domain requests to the canonical apex domain with 301 Permanent Redirect.
     * Prevents split rankings, canonical mismatches in Google Search Console, and duplicate indexing.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Redirect www.kenyaremotejobs.com -> kenyaremotejobs.com (or any www.* subdomain)
        if (str_starts_with($host, 'www.')) {
            $canonicalHost = substr($host, 4);
            $scheme = $request->isSecure() ? 'https' : $request->getScheme();
            $targetUrl = "{$scheme}://{$canonicalHost}".$request->getRequestUri();

            return redirect()->away($targetUrl, 301);
        }

        return $next($request);
    }
}
