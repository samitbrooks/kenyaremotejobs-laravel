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
            ['loc' => "{$url}/employers/post", 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => "{$url}/journal", 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => "{$url}/resume-builder", 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => "{$url}/match", 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => "{$url}/faqs", 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => "{$url}/companies", 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => "{$url}/collections", 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => "{$url}/pricing", 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => "{$url}/surveys", 'changefreq' => 'daily', 'priority' => '0.8'],
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
            '',
            '# LLM Documentation & Machine-Readable Feeds (https://llmstxt.org/)',
            "# llms.txt: {$url}/llms.txt",
            "# llms-full.txt: {$url}/llms-full.txt",
        ];

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * Standard llms.txt document serving curated markdown context for LLMs and AI agents.
     * (See specification: https://llmstxt.org/)
     */
    public function llms()
    {
        $url = rtrim(config('site.url'), '/');
        $siteName = config('site.name', 'KenyaRemoteJobs');
        $categories = JobCategorySeo::all();

        $lines = [
            "# {$siteName}",
            '',
            "> {$siteName} ({$url}) is the premier remote job discovery and career advisory platform for Kenyan and East African professionals. Every listing is verified and scored for Kenya fit: East Africa Time (EAT / UTC+3) overlap, no US/EU work visa barriers, and international contractor payout rails (Wise, Payoneer, direct bank wire, and local M-Pesa withdrawals).",
            '',
            '## Core Platform & Tools',
            "- [Browse All Remote Jobs]({$url}/jobs): Curated, real-time database of international remote vacancies vetted for East African candidates.",
            "- [Kenya-Friendly Match Guide]({$url}/jobs?filter=kenya-friendly): Filtered listings with verified Kenya-friendly hiring badges and timezone scores.",
            "- [Remote Companies Hiring in Kenya]({$url}/companies): Profiles of top global organizations (GitLab, Automattic, Deel, Wikimedia, Outlier) actively employing remote Kenyan talent.",
            "- [Curated Collections]({$url}/collections): High-yield curated collections including M-Pesa payouts, direct USD contracts, and entry-level global gigs.",
            "- [AI CV & Resume ATS Tailoring]({$url}/resume-builder): ATS-friendly CV builder and AI resume tailoring engine optimized for international recruiter filters.",
            "- [AI Career Match]({$url}/match): Instant role matchmaking based on career skills, expected compensation, and preferred work arrangements.",
            "- [Pro Membership & Early Access]({$url}/pricing): 48-Hour Early Access first-look window for subscribers before listings open publicly.",
            "- [Employer Job Posting]({$url}/employers): Verified hiring portal for international distributed teams and local companies recruiting Kenyan remote talent.",
            "- [Remote Work Journal & Guides]({$url}/journal): Practical tutorials on international remote contracting, W-8BEN tax filing, and interview prep.",
            "- [Frequently Asked Questions]({$url}/faqs): Essential answers regarding taxes, contracts, currency conversion, and working remotely from Kenya.",
            "- [Paid Surveys & Side Income in Kenya]({$url}/surveys): Curated, daily-verified directory of legitimate survey and microtask platforms with confirmed Kenyan payout rails (PayPal to M-Pesa, Airtime, Crypto).",
            '',
            '## Remote Roles by Discipline',
        ];

        foreach ($categories as $cat) {
            $lines[] = "- [{$cat['name']}]({$url}/remote-jobs/{$cat['slug']}): {$cat['meta_description']}";
        }

        $lines[] = '';
        $lines[] = '## Machine-Readable Feeds & Endpoints';
        $lines[] = "- [Featured Jobs RSS Feed]({$url}/feed/featured.rss): Real-time XML RSS feed of newest verified Kenya-friendly roles.";
        $lines[] = "- [XML Sitemap]({$url}/sitemap.xml): Full search engine crawler indexing map.";
        $lines[] = "- [Comprehensive LLM Context (llms-full.txt)]({$url}/llms-full.txt): Deep context including active job listings, company profiles, and contractor tax/payment guides.";
        $lines[] = '';
        $lines[] = '## Optional';
        $lines[] = "- [About {$siteName}]({$url}/about): Mission, background, and platform overview.";
        $lines[] = "- [Interactive Career Advisor Bot]({$url}/api/career-bot): API endpoint powering Daisy AI, the local career advisor for Kenyan professionals.";

        return Response::make(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Comprehensive llms-full.txt document providing complete context and active listings.
     */
    public function llmsFull()
    {
        $url = rtrim(config('site.url'), '/');
        $siteName = config('site.name', 'KenyaRemoteJobs');
        $categories = JobCategorySeo::all();
        $companies = CompanyDirectory::all();
        $recentJobs = JobListing::visible()
            ->where('kenya_friendly', true)
            ->latest('posted_at')
            ->limit(20)
            ->get();

        $lines = [
            "# {$siteName} — Full System & Knowledge Context",
            '',
            '> Complete machine-readable reference for Large Language Models and AI agents containing verified opportunities, company profiles, compensation benchmarks, and Kenyan remote contracting standards.',
            '',
            '## 1. Remote Contracting Context for Kenya',
            '- Timezone Advantage: Nairobi is on East Africa Time (EAT / UTC+3). This provides 4 to 5 hours of clean daily collaboration overlap with London (BST/GMT) and Western Europe (CEST/CET), as well as 3 hours of overlap with US East Coast mornings (EDT/EST).',
            '- Tax & Forms (IRS W-8BEN): Kenyan contractors working for US-based employers/clients fill out IRS Form W-8BEN (Certificate of Foreign Status of Beneficial Owner). Because Kenya has a double-tax treaty and services are performed outside the US, Kenyan contractors claim 0% US withholding tax by entering their Kenyan KRA PIN in Part I, Line 6a.',
            '- Payout Methods: Modern international employers pay remote contractors via Wise (direct to Kenyan bank accounts or M-Pesa), Payoneer, direct Wire transfer to local USD accounts (e.g. Equity Bank, KCB, Absa, Stanbic), or contractor platforms like Deel and Remote.com.',
            '- Currency Conversion: Direct bank wire or platform transfers can be converted to Kenyan Shillings (KES) or kept in local foreign currency accounts to hedge against currency depreciation.',
            '',
            '## 2. Active Verified Kenya-Friendly Roles',
        ];

        if ($recentJobs->isEmpty()) {
            $lines[] = 'No active listings currently available.';
        } else {
            foreach ($recentJobs as $job) {
                $salary = $job->salary ? " | Pay: {$job->salary}" : '';
                $lines[] = "- [{$job->title} at {$job->company}]({$url}/jobs/{$job->id}) ({$job->remote_type}{$salary})";
                $lines[] = "  Location: {$job->location} | Score: {$job->kenya_score}/100";
            }
        }

        $lines[] = '';
        $lines[] = '## 3. Remote Companies Vetted for Kenya';
        foreach ($companies as $comp) {
            $pay = implode(', ', $comp['payment_methods']);
            $lines[] = "### [{$comp['name']}]({$url}/companies/{$comp['slug']})";
            $lines[] = "- Headquarters: {$comp['headquarters']}";
            $lines[] = "- Industry & Size: {$comp['industry']} ({$comp['size']})";
            $lines[] = "- Supported Payment Rails: {$pay}";
            $lines[] = "- Hiring Overview: {$comp['headline']}";
            $lines[] = '';
        }

        $lines[] = '## 4. Key Career Disciplines';
        foreach ($categories as $cat) {
            $lines[] = "### [{$cat['name']}]({$url}/remote-jobs/{$cat['slug']})";
            $lines[] = "- Salary Benchmark: {$cat['salary_range']}";
            $lines[] = "- Overview: {$cat['intro']}";
            $lines[] = '';
        }

        return Response::make(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * IndexNow verification key endpoint (https://www.indexnow.org/documentation)
     */
    public function indexNowKey(?string $key = null)
    {
        $expectedKey = (string) config('services.indexnow.key', '7b4e07a3c39542a39281a8b34f71a0dc');

        if ($key !== null && $key !== '' && $key !== $expectedKey) {
            abort(404);
        }

        return Response::make($expectedKey, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
