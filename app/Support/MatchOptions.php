<?php

namespace App\Support;

/**
 * A fixed, curated set of choices for the match-profile form — built from
 * the tags that actually appear across the job catalog, grouped for a
 * checkbox UI. Free-text skill/role entry was dropped in favor of this: it
 * keeps Matching::computeMatchPercent()'s substring comparison reliable (no
 * typos, no synonyms scoring differently) and keeps the form fast to fill
 * in on mobile.
 *
 * Ported from the Next.js version's src/lib/matchOptions.ts.
 */
class MatchOptions
{
    public const SKILL_GROUPS = [
        'Tech & Development' => [
            'JavaScript', 'Python', 'React', 'Node.js', 'PHP', 'Java', 'Ruby', 'Go',
            'C#/.NET', 'Swift', 'Android', 'Flutter/React Native', 'SQL/Databases',
            'DevOps/Cloud', 'QA/Testing', 'Cybersecurity', 'Data Science/AI', 'WordPress', 'Shopify',
        ],
        'Design' => ['UI/UX Design', 'Graphic Design', 'Video Editing', 'Product Design'],
        'Sales & Marketing' => [
            'Sales', 'Digital Marketing', 'SEO', 'Content Writing', 'Social Media',
            'Business Development', 'Account Management',
        ],
        'Support & Operations' => [
            'Customer Support', 'Virtual Assistant', 'Project Management', 'Operations', 'Recruiting/HR',
        ],
        'Finance & Admin' => [
            'Bookkeeping/Accounting', 'Excel/Spreadsheets', 'Data Entry', 'Legal/Compliance',
        ],
    ];

    public const ROLE_OPTIONS = [
        'Frontend Developer', 'Backend Developer', 'Full-Stack Developer', 'Mobile Developer',
        'DevOps Engineer', 'Data Analyst', 'QA/Test Engineer', 'UI/UX Designer', 'Graphic Designer',
        'Product Manager', 'Project Manager', 'Customer Support Representative', 'Virtual Assistant',
        'Sales Representative', 'Business Development Manager', 'Digital Marketer', 'Content Writer',
        'SEO Specialist', 'Social Media Manager', 'Accountant/Bookkeeper', 'HR/Recruiter', 'Operations Manager',
    ];

    public const EXPERIENCE_BANDS = [
        ['label' => '0–1 years (new/entry-level)', 'years' => 0],
        ['label' => '1–3 years', 'years' => 2],
        ['label' => '3–5 years', 'years' => 4],
        ['label' => '5–10 years', 'years' => 7],
        ['label' => '10+ years', 'years' => 12],
    ];
}
