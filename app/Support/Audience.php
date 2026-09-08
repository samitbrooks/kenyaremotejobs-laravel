<?php

namespace App\Support;

/**
 * Audience segment metadata (order, labels, descriptions, nav icons) plus
 * the keyword-based classify() scoring, ported from the Next.js version's
 * src/lib/audienceClassification.ts.
 */
class Audience
{
    public const ORDER = [
        'students',
        'highschool',
        'career-changers',
        'part-time',
        'tvet',
        'disability-friendly',
    ];

    public const LABELS = [
        'students' => 'Students & Campus',
        'highschool' => 'Fresh High School Leavers',
        'career-changers' => 'Career Changers & Beginners',
        'part-time' => 'Part-Time & Flexible',
        'tvet' => 'TVET & Technical Certificates',
        'disability-friendly' => 'Disability-Friendly Employers',
    ];

    public const DESCRIPTIONS = [
        'students' => 'Internships and campus-oriented roles.',
        'highschool' => 'No degree required, will-train, entry-level roles.',
        'career-changers' => 'Beginner-friendly roles open to a first-time switch.',
        'part-time' => 'Flexible or part-time hours, not a fixed 9-to-5.',
        'tvet' => 'Roles suited to a vocational or technical certificate.',
        'disability-friendly' => 'Employers explicit about inclusive hiring.',
    ];

    public const ICONS = [
        'students' => 'graduation-cap',
        'highschool' => 'school',
        'career-changers' => 'refresh',
        'part-time' => 'clock',
        'tvet' => 'tools',
        'disability-friendly' => 'accessibility',
    ];

    // Same method as KenyaRelevance: keyword signals over title/tags/
    // description, no external data source. A job can match more than one
    // segment (an internship can also be beginner-friendly), so classify()
    // returns a list, not a single category.
    private const SIGNALS = [
        // Bare nouns like "student", "college", or "university" were
        // dropped — they fire on things like "student loans" (a benefit),
        // "an accredited college or university" (a degree requirement, not
        // a target audience), or an edtech company's own product
        // description ("drives student outcomes"), none of which mean the
        // *role* is aimed at students. Only compound phrases that actually
        // describe who the posting is for.
        'students' => [
            'intern', 'internship', 'for students', 'currently enrolled',
            'pursuing a degree', 'pursuing your degree', 'expected graduation',
            'graduating in', 'campus recruiting', 'college students',
            'university students', 'student talent', 'graduate program',
        ],
        // "no prior experience" deliberately excluded — a template used
        // across several Himalayas "AI training data" gigs says "no prior
        // experience in AI is required" for roles that actually demand
        // PhD-level domain expertise (child psychology, physics). Matching
        // the generic phrase misclassified senior/expert roles as
        // entry-level; the remaining phrases below don't collide with that
        // template in practice.
        'highschool' => [
            'no experience necessary', 'no experience required', 'no degree required',
            'no degree necessary', 'high school diploma', 'entry level',
            'entry-level', 'will train',
        ],
        'career-changers' => [
            'career change', 'career changers', 'career switch', 'beginner friendly',
            'beginner-friendly', 'no coding experience', 'bootcamp graduates',
            'new to the industry',
        ],
        'part-time' => [
            'part-time', 'part time', 'flexible hours', 'flexible schedule',
            'choose your hours', 'work whenever',
        ],
        'tvet' => [
            'technical certificate', 'vocational', 'tvet', 'diploma in',
            'certificate in', 'trade certificate',
        ],
        // "equal opportunity employer" is deliberately excluded — it's
        // generic EEO boilerplate in nearly every US job posting regardless
        // of actual disability-inclusive practice, and including it swamped
        // this category with false positives (148 of 525 jobs, vs. ~50 on
        // the narrower signals).
        'disability-friendly' => [
            'people with disabilities', 'disability inclusive', 'disability-inclusive',
            'accommodations provided', 'reasonable accommodation', 'accessible workplace',
        ],
    ];

    /**
     * @param  array{title: string, description: string, tags?: array<int, string>}  $job
     * @return array<int, string>
     */
    public static function classify(array $job): array
    {
        $haystack = $job['title'].' '.implode(' ', $job['tags'] ?? []).' '.$job['description'];
        $matched = [];

        foreach (self::ORDER as $segment) {
            $words = array_map(fn ($w) => preg_quote($w, '/'), self::SIGNALS[$segment]);
            // Word-boundary matching, not plain substring — "intern" as a
            // bare substring check matches "international", "internal", and
            // "internet" and was misclassifying roles like "Director of Ad
            // Sales" as student-facing purely because the description
            // mentioned an "international" team.
            $pattern = '/\b('.implode('|', $words).')\b/i';
            if (preg_match($pattern, $haystack)) {
                $matched[] = $segment;
            }
        }

        return $matched;
    }
}
