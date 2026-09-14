<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Support\CompanyDirectory;
use App\Support\JobCategorySeo;
use App\Support\JobCollectionSeo;
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
            ['loc' => "{$url}/journal", 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => "{$url}/resume-builder", 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => "{$url}/faqs", 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => "{$url}/companies", 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => "{$url}/collections", 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => "{$url}/pricing", 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => "{$url}/surveys", 'changefreq' => 'weekly', 'priority' => '0.4'],
            ['loc' => "{$url}/about", 'changefreq' => 'monthly', 'priority' => '0.3'],
        ];

        $categoryRoutes = collect(JobCategorySeo::all())->map(fn (array $cat) => [
            'loc' => "{$url}/remote-jobs/{$cat['slug']}",
            'changefreq' => 'daily',
            'priority' => '0.8',
        ])->values()->all();

        $companyRoutes = collect(CompanyDirectory::all())->map(fn (array $comp) => [
            'loc' => "{$url}/companies/{$comp['slug']}",
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ])->values()->all();

        $collectionRoutes = collect(JobCollectionSeo::all())->map(fn (array $col) => [
            'loc' => "{$url}/collections/{$col['slug']}",
            'changefreq' => 'daily',
            'priority' => '0.8',
        ])->values()->all();

        $jobRoutes = JobListing::visible()->select('id', 'posted_at', 'kenya_friendly')->get()
            ->map(fn (JobListing $job) => [
                'loc' => "{$url}/jobs/{$job->id}",
                'lastmod' => $job->posted_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => $job->kenya_friendly ? '0.8' : '0.6',
            ]);

        $postRoutes = BlogPost::where('published', true)->get()
            ->map(fn (BlogPost $post) => [
                'loc' => "{$url}/journal/{$post->slug}",
                'lastmod' => ($post->published_at ?? $post->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $entries = collect($staticRoutes)
            ->concat($categoryRoutes)
            ->concat($companyRoutes)
            ->concat($collectionRoutes)
            ->concat($jobRoutes)
            ->concat($postRoutes);

        $xml = view('sitemap', ['entries' => $entries])->render();

        return Response::make($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots()
    {
        $url = config('site.url');

        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /account',
            'Disallow: /auth/',
            'Disallow: /unsubscribe/',
            '',
            "Sitemap: {$url}/sitemap.xml",
        ];

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
