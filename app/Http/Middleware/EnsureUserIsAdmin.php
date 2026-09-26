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
        $user = $request->user();

        if (! $user) {
            return redirect()->guest('/account?next='.urlencode($request->getRequestUri()))
                ->with('info', 'Please log in with your administrator account to access the Admin Panel.');
        }

        if (! $user->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. You must be an administrator to access the Admin Panel.');
        }

        return $next($request);
    }
}
