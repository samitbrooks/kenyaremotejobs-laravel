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
    private const UNTRACKED_PREFIXES = ['admin', 'livewire', 'webhooks', 'up', 'build', 'api'];

    private const UNTRACKED_EXACT = ['robots.txt', 'sitemap.xml', 'favicon.ico', 'llms.txt', 'llms-full.txt'];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && $this->shouldTrack($request)) {
            $path = Str::limit('/'.ltrim($request->path(), '/'), 250, '');
            PageView::create(['path' => $path, 'created_at' => now()]);
        }

        return $next($request);
    }

    private function shouldTrack(Request $request): bool
    {
        $path = ltrim($request->path(), '/');

        if (Str::startsWith($path, self::UNTRACKED_PREFIXES)) {
            return false;
        }

        if (in_array($path, self::UNTRACKED_EXACT, true)) {
            return false;
        }

        if (Str::endsWith($path, ['.txt', '.xml', '.ico', '.png', '.jpg', '.webp'])) {
            return false;
        }

        $userAgent = (string) $request->userAgent();
        if ($userAgent !== '' && preg_match('/(bot|crawl|spider|slurp|curl|python-requests|headless|wget)/i', $userAgent)) {
            return false;
        }

        return true;
    }
}
