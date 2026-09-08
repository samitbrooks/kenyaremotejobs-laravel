<?php

namespace App\Support;

/**
 * Static, hand-curated list — no external API. Verify each platform still
 * accepts Kenyan sign-ups and actually pays out before publishing; survey
 * platform terms and availability change often. Links go to each platform's
 * plain homepage, not an affiliate/referral link — wire up an official
 * referral program directly with the platform if you want revenue here.
 *
 * Ported from the Next.js version's src/lib/surveyPlatforms.ts.
 */
class SurveyPlatforms
{
    public const LIST = [
        ['name' => 'Swagbucks', 'description' => 'Global get-paid-to platform — surveys, cashback, and simple tasks.', 'payouts' => ['PayPal', 'Gift Cards'], 'url' => 'https://www.swagbucks.com'],
        ['name' => 'Toluna', 'description' => 'Long-running survey panel with points redeemable for cash or gift cards.', 'payouts' => ['Gift Cards', 'Cash'], 'url' => 'https://www.toluna.com'],
        ['name' => 'PrizeRebel', 'description' => 'Get-paid-to site combining surveys with simple online tasks.', 'payouts' => ['PayPal', 'Gift Cards'], 'url' => 'https://www.prizerebel.com'],
        ['name' => 'ySense', 'description' => 'Get-paid-to platform popular across Africa, with a range of paid tasks.', 'payouts' => ['PayPal'], 'url' => 'https://www.ysense.com'],
        ['name' => 'Respondent.io', 'description' => 'Higher-paying research sessions targeting professionals and niche expertise.', 'payouts' => ['PayPal'], 'url' => 'https://www.respondent.io'],
        ['name' => 'Survey Junkie', 'description' => 'One of the largest survey panels, with a straightforward points-to-cash system.', 'payouts' => ['PayPal', 'Gift Cards'], 'url' => 'https://www.surveyjunkie.com'],
        ['name' => 'InboxDollars', 'description' => 'Long-running get-paid-to platform — surveys, reading email, and simple tasks.', 'payouts' => ['PayPal', 'Gift Cards', 'Check'], 'url' => 'https://www.inboxdollars.com'],
        ['name' => 'Freecash', 'description' => 'Get-paid-to platform combining surveys with app and game offers.', 'payouts' => ['PayPal', 'Gift Cards', 'Crypto'], 'url' => 'https://www.freecash.com'],
        ['name' => 'Timebucks', 'description' => 'Get-paid-to platform with a wide mix of surveys, tasks, and paid-to-click offers.', 'payouts' => ['PayPal', 'Crypto', 'Gift Cards'], 'url' => 'https://timebucks.com'],
        ['name' => 'Clickworker', 'description' => 'Microtask and survey platform for short, flexible paid assignments.', 'payouts' => ['PayPal'], 'url' => 'https://www.clickworker.com'],
        ['name' => 'Attapoll', 'description' => 'Mobile-first survey app that markets itself as available to a global audience.', 'payouts' => ['PayPal'], 'url' => 'https://www.attapoll.app'],
    ];
}
