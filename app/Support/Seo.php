<?php

namespace App\Support;

/**
 * JSON-LD builders live in a plain .php file, not a .blade.php one — Blade's
 * compiler rewrites any literal "@context"/"@type" text it finds in a
 * template (it collides with the framework's own @context directive), so
 * schema.org's required @-prefixed keys have to be assembled outside Blade.
 */
class Seo
{
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
