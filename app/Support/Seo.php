<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Services\Redactor;

/**
 * JSON-LD builders live in a plain .php file, not a .blade.php one — Blade's
 * compiler rewrites any literal "@context"/"@type" text it finds in a
 * template (it collides with the framework's own @context directive), so
 * schema.org's required @-prefixed keys have to be assembled outside Blade.
 */
class Seo
{
    private const EMPLOYMENT_TYPE_MAP = [
        'full_time' => 'FULL_TIME',
        'full-time' => 'FULL_TIME',
        'fulltime' => 'FULL_TIME',
        'part_time' => 'PART_TIME',
        'part-time' => 'PART_TIME',
        'parttime' => 'PART_TIME',
        'contract' => 'CONTRACTOR',
        'contractor' => 'CONTRACTOR',
        'freelance' => 'CONTRACTOR',
        'temporary' => 'TEMPORARY',
        'temp' => 'TEMPORARY',
        'internship' => 'INTERN',
        'intern' => 'INTERN',
        'volunteer' => 'VOLUNTEER',
    ];

    // Google requires validThrough for JobPosting rich results. The source
    // APIs don't provide an expiry, so we estimate one — long enough that
    // active postings don't get flagged expired, short enough not to
    // mislead crawlers.
    private const ESTIMATED_LISTING_LIFETIME_DAYS = 60;

    /**
     * Every field here must match what an anonymous crawler/visitor actually
     * sees on the page, or it counts as cloaking under Google's guidelines.
     * The full description is shown to everyone regardless of payment, but
     * the employer's identity stays withheld for a non-employer listing
     * until someone unlocks it — both the description text (which can
     * name-drop the employer on its own) and hiringOrganization.name have to
     * reflect that.
     */
    public static function jobPostingJsonLd(JobListing $job): string
    {
        $at = '@';
        $employerVisible = $job->origin === 'employer';
        $plainDescription = Format::stripHtml($job->description ?? '');
        $description = $employerVisible
            ? $plainDescription
            : app(Redactor::class)->redactEmployerIdentity($plainDescription, $job->company);

        $data = [
            $at.'context' => 'https://schema.org',
            $at.'type' => 'JobPosting',
            'title' => $job->title,
            'description' => $description,
            'identifier' => [
                $at.'type' => 'PropertyValue',
                'name' => config('site.name'),
                'value' => $job->id,
            ],
            'datePosted' => $job->posted_at->toIso8601String(),
            'validThrough' => $job->posted_at->copy()->addDays(self::ESTIMATED_LISTING_LIFETIME_DAYS)->toIso8601String(),
            'employmentType' => self::EMPLOYMENT_TYPE_MAP[strtolower(str_replace(' ', '_', trim($job->remote_type ?? '')))] ?? 'OTHER',
            'hiringOrganization' => [
                $at.'type' => 'Organization',
                'name' => $employerVisible ? $job->company : 'Employer withheld until unlocked',
            ],
            'jobLocationType' => 'TELECOMMUTE',
            // Every job on this board is pitched to a Kenya-based audience, so
            // Kenya is always a valid applicant location even when the
            // underlying listing is open more broadly.
            'applicantLocationRequirements' => [
                $at.'type' => 'Country',
                'name' => 'Kenya',
            ],
            'url' => config('site.url').'/jobs/'.$job->id,
        ];

        // Only present for sources with confirmed-currency structured salary
        // data — never a derived/estimated figure, since Google's
        // baseSalary field should be the stated rate.
        if ($job->annual_salary_usd) {
            $min = $job->annual_salary_usd['min'] ?? $job->annual_salary_usd['max'] ?? null;
            $max = $job->annual_salary_usd['max'] ?? $job->annual_salary_usd['min'] ?? null;
            $data['baseSalary'] = [
                $at.'type' => 'MonetaryAmount',
                'currency' => 'USD',
                'value' => [
                    $at.'type' => 'QuantitativeValue',
                    'minValue' => $min,
                    'maxValue' => $max,
                    'unitText' => 'YEAR',
                ],
            ];
        }

        return json_encode($data);
    }

    public static function breadcrumbJsonLd(JobListing $job): string
    {
        $at = '@';
        $url = config('site.url');

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'type' => 'BreadcrumbList',
            'itemListElement' => [
                [$at.'type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $url],
                [$at.'type' => 'ListItem', 'position' => 2, 'name' => 'Jobs', 'item' => $url.'/jobs'],
                [$at.'type' => 'ListItem', 'position' => 3, 'name' => $job->title, 'item' => $url.'/jobs/'.$job->id],
            ],
        ]);
    }

    public static function organizationAndWebsiteJsonLd(): string
    {
        $at = '@';

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'graph' => [
                [
                    $at.'type' => 'Organization',
                    $at.'id' => config('site.url').'/#organization',
                    'name' => config('site.name'),
                    'url' => config('site.url'),
                    'description' => config('site.default_description'),
                ],
                [
                    $at.'type' => 'WebSite',
                    $at.'id' => config('site.url').'/#website',
                    'url' => config('site.url'),
                    'name' => config('site.name'),
                    'description' => config('site.default_description'),
                    'publisher' => [$at.'id' => config('site.url').'/#organization'],
                    'potentialAction' => [
                        $at.'type' => 'SearchAction',
                        'target' => [$at.'type' => 'EntryPoint', 'urlTemplate' => config('site.url').'/jobs?q={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ]);
    }

    public static function articleJsonLd(BlogPost $post): string
    {
        $at = '@';

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'author' => [$at.'type' => 'Person', 'name' => $post->author_name],
            'datePublished' => ($post->published_at ?? $post->created_at)->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'url' => config('site.url').'/journal/'.$post->slug,
        ]);
    }

    /**
     * @param  array<int, array{question: string, answer: string}>  $faqs
     */
    public static function faqJsonLd(array $faqs): string
    {
        $at = '@';

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'type' => 'FAQPage',
            'mainEntity' => array_map(fn ($faq) => [
                $at.'type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [$at.'type' => 'Answer', 'text' => $faq['answer']],
            ], $faqs),
        ]);
    }
}
