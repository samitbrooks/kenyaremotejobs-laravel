<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

// Page loads, not unique visitors — no cookie or IP is stored, just a path
// and a timestamp, which is all the admin dashboard's visitor counts need.
class TrackPageView
{
    private const UNTRACKED_PREFIXES = ['admin', 'livewire', 'webhooks', 'up'];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && ! Str::startsWith($request->path(), self::UNTRACKED_PREFIXES)) {
            PageView::create(['path' => '/'.ltrim($request->path(), '/'), 'created_at' => now()]);
        }

        return $next($request);
    }
}
