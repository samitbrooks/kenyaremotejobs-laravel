<?php

// App-specific constants ported from the Next.js version's src/lib/jobTiers.ts,
// src/lib/premiumWindow.ts, and src/lib/employerPricing.ts. Kept as plain
// config (not DB rows) since they're site-owner-tuned constants, not user
// data — same reasoning as the original.

return [

    // Comma-separated allowlist, e.g. ADMIN_EMAILS="owner@site.com,ops@site.com".
    // Render-time gating alone isn't a security boundary — every admin route
    // handler and mutation must re-check User::isAdmin() itself, not just the
    // page shell.
    'admin_emails' => array_filter(array_map(
        fn (string $email) => strtolower(trim($email)),
        explode(',', (string) env('ADMIN_EMAILS', ''))
    )),

    // A non-employer listing (synced or admin-added) disappears from the
    // site entirely once it's this many days old, unless someone unlocked
    // it first — see JobListing::scopeVisible(). It never becomes free for
    // everyone; unlocking is the only way to see the employer/apply link
    // before it's gone.
    'premium_window_days' => 5,

    // How long an employer-paid listing stays visible before it disappears
    // the same way — matches the "30 days of visibility" every posting plan
    // promises below. Not read from posting_plans directly: JobListing
    // doesn't record which specific plan a listing was posted under (only
    // JobPostingPayment does), and both current plans promise the same
    // duration anyway. If a future plan promises a different duration,
    // this needs to become a per-listing column instead.
    'employer_listing_days' => 30,

    // A purchase grants N unlock credits for one tier, spendable on any job
    // of that tier. See App\Services\CreditsService for the ledger that
    // derives remaining balance as SUM(purchased) - COUNT(spent), never a
    // stored counter.
    'tiers' => ['basic', 'intermediate', 'premium'],

    'tier_labels' => [
        'basic' => 'Basic',
        'intermediate' => 'Intermediate',
        'premium' => 'Premium',
    ],

    'credit_packages' => [
        'basic' => ['price_kes' => 300, 'credits' => 1],
        'intermediate' => ['price_kes' => 599, 'credits' => 2],
        'premium' => ['price_kes' => 999, 'credits' => 3],
    ],

    // Annual-USD thresholds a listing's structured salary is bucketed
    // against, in App\Services\JobTierCalculator.
    'intermediate_min_usd' => 40_000,
    'premium_min_usd' => 80_000,

    // A second pricing model, not a fourth tier: unlimited unlocks across
    // every tier for a flat price. Quarterly/yearly are the same plan
    // prepaid at a discount against paying monthly the whole way through.
    'subscription_plans' => [
        'monthly' => ['label' => 'Monthly', 'price_kes' => 1499, 'months' => 1, 'save_label' => null],
        'quarterly' => ['label' => 'Quarterly', 'price_kes' => 3999, 'months' => 3, 'save_label' => 'Save 11%'],
        'yearly' => ['label' => 'Yearly', 'price_kes' => 12999, 'months' => 12, 'save_label' => 'Save 28%'],
    ],

    // Employer job-posting plans — a completely separate revenue stream
    // from the candidate credit/subscription system above. Employer-origin
    // jobs are never paywalled to candidates regardless of tier.
    'posting_plans' => [
        'basic' => [
            'label' => 'Basic Job',
            'price_kes' => 2500,
            'listing_days' => 30,
            'featured' => false,
            'features' => [
                '1 job advertisement',
                '30 days of visibility',
                'Company logo and details',
                'Reaches every Kenya-Friendly-Match candidate search',
            ],
        ],
        'boost' => [
            'label' => 'Boost Job',
            'price_kes' => 5500,
            'listing_days' => 30,
            'featured' => true,
            'features' => [
                '1 featured job advertisement',
                '30 days of visibility',
                'Company logo and details',
                'Highlighted and top-of-list placement',
                'Included in the weekly candidate digest',
            ],
        ],
    ],

];
