<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Render-time gating alone isn't a security boundary — every admin mutation
// still has to be reachable only through this middleware (applied to the
// whole /admin route group), never trusted from a hidden form field or a
// client-side check alone.
class EnsureUserIsAdmin
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            return redirect('/');
        }

        return $next($request);
    }
}
