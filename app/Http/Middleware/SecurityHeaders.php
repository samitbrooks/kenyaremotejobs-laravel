<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and enforce modern HTTP security headers.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Clickjacking defense: prevent framing by malicious third-party origins
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // MIME-sniffing defense: instruct browsers to strictly adhere to declared MIME types
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer policy: send origin only when crossing origins, preserve path for same-origin
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy: disable unused sensitive browser APIs
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');

        // Modern XSS protection header directive (disables deprecated and buggy browser XSS auditor)
        $response->headers->set('X-XSS-Protection', '0');

        // Strict-Transport-Security: enforce HTTPS in secure environments
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
