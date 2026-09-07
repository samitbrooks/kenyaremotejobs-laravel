<?php

namespace App\Support;

/**
 * Audience segment metadata (order, labels, descriptions, nav icons).
 * The keyword-based classifyAudiences() scoring this feeds belongs to the
 * Phase 2 job-sync pipeline; this class only carries the static display data
 * needed by navigation and filters in Phase 1.
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
}
