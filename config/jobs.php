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

    // Optional environment fallback password for initial admin portal setup/bootstrap
    'admin_password' => env('ADMIN_PASSWORD'),

    // All listings remain active on the site for 30 days.
    'listing_days' => 30,

    // Fresh listings posted in the last 48 hours are in Early Access for
    // Pro subscribers (recruiter first-look window). After 48 hours,
    // the apply link opens freely to all registered visitors.
    'early_access_hours' => 48,

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
        'pro' => [
            'key' => 'pro',
            'label' => 'Pro Membership',
            'tagline' => 'Everything unlocked. Instant 0-second apply access.',
            'description' => 'The complete remote career accelerator for Kenyan professionals targeting USD contracts.',
            'price_kes' => 250,
            'months' => 1,
            'recommended' => true,
            'button_text' => 'Get Pro Access — KES 250/mo',
            'note' => 'Instant M-Pesa STK push • 7-day money-back guarantee',
            'features' => [
                'Instant 0-second apply access to all 800+ verified remote jobs (bypass the 48h wait).',
                'Direct Kenyan employer listings actively seeking talent in Nairobi & across Kenya.',
                'Unlimited AI CV & ATS tailoring copilot (beat screening algorithms).',
                'Tailored cover letters generated for every role you apply to.',
                'Kenya Remote Contractor Toolkit: USD invoicing templates, Wise/Payoneer setup, W-8BEN tax guide.',
                'VIP WhatsApp support & application CRM tracker.',
            ],
        ],
        'starter' => [
            'key' => 'starter',
            'label' => 'Starter',
            'tagline' => 'See the jobs. Apply.',
            'description' => 'Legacy tier for basic application access.',
            'price_kes' => 100,
            'months' => 1,
            'recommended' => false,
            'button_text' => 'Choose Starter',
            'features' => [
                'Standard job details & requirements.',
                '5 Fit Score checks.',
                '2 Cover Letters.',
            ],
        ],
        'elite' => [
            'key' => 'elite',
            'label' => 'Elite',
            'tagline' => 'Apply more. Prepare for interviews.',
            'description' => 'Legacy tier with interview practice sessions.',
            'price_kes' => 500,
            'months' => 1,
            'recommended' => false,
            'button_text' => 'Choose Elite',
            'features' => [
                'All Pro features included.',
                '5 CV Revamps & 15 Cover Letters.',
                '2 Live Interview Practice simulations.',
            ],
        ],
        // Duration billing options
        'monthly' => ['key' => 'monthly', 'label' => 'Monthly', 'price_kes' => 250, 'months' => 1, 'save_label' => null],
        'quarterly' => ['key' => 'quarterly', 'label' => 'Quarterly', 'price_kes' => 499, 'months' => 3, 'save_label' => 'Save 33%'],
        'yearly' => ['key' => 'yearly', 'label' => 'Yearly', 'price_kes' => 899, 'months' => 12, 'save_label' => 'Save 70%'],
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
