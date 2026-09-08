<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\JobListing;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function sitemap()
    {
        $url = config('site.url');

        $staticRoutes = [
            ['loc' => "{$url}/", 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => "{$url}/jobs", 'changefreq' => 'hourly', 'priority' => '0.9'],
            ['loc' => "{$url}/employers", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => "{$url}/journal", 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['loc' => "{$url}/resume-builder", 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => "{$url}/pricing", 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => "{$url}/surveys", 'changefreq' => 'weekly', 'priority' => '0.4'],
            ['loc' => "{$url}/about", 'changefreq' => 'monthly', 'priority' => '0.3'],
        ];

        $jobRoutes = JobListing::select('id', 'posted_at', 'kenya_friendly')->get()
            ->map(fn (JobListing $job) => [
                'loc' => "{$url}/jobs/{$job->id}",
                'lastmod' => $job->posted_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => $job->kenya_friendly ? '0.8' : '0.6',
            ]);

        $postRoutes = BlogPost::where('published', true)->get()
            ->map(fn (BlogPost $post) => [
                'loc' => "{$url}/journal/{$post->slug}",
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ]);

        $entries = collect($staticRoutes)->concat($jobRoutes)->concat($postRoutes);

        $xml = view('sitemap', ['entries' => $entries])->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $url = config('site.url');

        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /account',
            '',
            "Sitemap: {$url}/sitemap.xml",
        ];

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
