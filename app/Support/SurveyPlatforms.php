<?php

namespace App\Support;

class SurveyPlatforms
{
    public const PRICE_KES = 199;

    /**
     * Hand-curated, verified list of paid-survey and get-paid-to platforms
     * confirmed to accept Kenyan accounts, operate legitimately, and pay out to Kenyan residents.
     *
     * @return array<int, array{
     *     name: string,
     *     slug: string,
     *     description: string,
     *     payouts: array<int, string>,
     *     mpesa_compatible: bool,
     *     url: string,
     *     earning_potential: string,
     *     min_payout: string,
     *     category: string,
     *     device: string,
     *     badge: string|null,
     *     rating: float,
     *     kenya_verified: bool,
     *     is_premium: bool
     * }>
     */
    public static function all(): array
    {
        return [
            // Tier 1: Free Public Previews (Freely accessible to all visitors and Googlebot)
            [
                'name' => 'ySense',
                'slug' => 'ysense',
                'description' => 'The most established survey and reward panel in East Africa. Earn by taking market research surveys, testing mobile apps, and completing short daily tasks.',
                'payouts' => ['PayPal (M-Pesa)', 'Skrill', 'Payoneer', 'Amazon Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.ysense.com',
                'earning_potential' => '$0.50 – $4.00 per survey',
                'min_payout' => '$10.00',
                'category' => 'surveys',
                'device' => 'Mobile App & Web',
                'badge' => 'Free Preview',
                'rating' => 4.8,
                'kenya_verified' => true,
                'is_premium' => false,
            ],
            [
                'name' => 'Freecash',
                'slug' => 'freecash',
                'description' => 'Fast-growing rewards platform with daily survey routers, app testing, and game milestones. Offers instant payouts with very low minimum thresholds.',
                'payouts' => ['PayPal (M-Pesa)', 'Crypto (LTC/USDT)', 'Visa Prepaid', 'Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://freecash.com',
                'earning_potential' => '$0.80 – $5.00 per offer/survey',
                'min_payout' => '$5.00 (PayPal) / $0.50 (Crypto)',
                'category' => 'microtasks',
                'device' => 'Android App & Web',
                'badge' => 'Free Preview',
                'rating' => 4.9,
                'kenya_verified' => true,
                'is_premium' => false,
            ],
            [
                'name' => 'Toluna Influencers Kenya',
                'slug' => 'toluna',
                'description' => 'Official Kenyan consumer panel operated by Toluna Group. Surveys focus on Kenyan brands, telecoms, and FMCG products with points redeemable for cash.',
                'payouts' => ['PayPal (M-Pesa)', 'Airtime (Safaricom/Airtel)', 'Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.toluna.com',
                'earning_potential' => '$0.50 – $2.50 per survey',
                'min_payout' => '$10.00',
                'category' => 'surveys',
                'device' => 'iOS, Android & Web',
                'badge' => 'Free Preview',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => false,
            ],
            [
                'name' => 'Swagbucks',
                'slug' => 'swagbucks',
                'description' => 'The world’s largest get-paid-to portal. Complete daily answer polls, discover web products, and earn SB points exchangeable for cash.',
                'payouts' => ['PayPal (M-Pesa)', 'Amazon Gift Cards', 'Mastercard Virtual'],
                'mpesa_compatible' => true,
                'url' => 'https://www.swagbucks.com',
                'earning_potential' => '$0.50 – $3.00 per survey',
                'min_payout' => '$5.00',
                'category' => 'surveys',
                'device' => 'Mobile App & Web',
                'badge' => 'Free Preview',
                'rating' => 4.5,
                'kenya_verified' => true,
                'is_premium' => false,
            ],

            // Tier 2: Premium Survey & Side-Income Vault (Unlocked with KES 199 Survey Pass or Pro)
            [
                'name' => 'Respondent.io',
                'slug' => 'respondent',
                'description' => 'Premium qualitative research platform matching professionals with enterprise studies, UX feedback, and 1-on-1 remote video interviews.',
                'payouts' => ['PayPal (M-Pesa)'],
                'mpesa_compatible' => true,
                'url' => 'https://www.respondent.io',
                'earning_potential' => '$50 – $200 per 45-min study',
                'min_payout' => 'No minimum (per project)',
                'category' => 'research',
                'device' => 'Laptop / Desktop with Webcam',
                'badge' => 'Highest Paying',
                'rating' => 4.9,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Outlier AI (Remotasks)',
                'slug' => 'outlier',
                'description' => 'Scale AI’s flagship platform for training frontier AI models. Evaluate chatbot outputs, write prompt responses, and review reasoning steps in English and local domains.',
                'payouts' => ['PayPal (M-Pesa)', 'AirTM'],
                'mpesa_compatible' => true,
                'url' => 'https://outlier.ai',
                'earning_potential' => '$8.00 – $25.00 per hour',
                'min_payout' => 'Weekly automatic direct deposit',
                'category' => 'microtasks',
                'device' => 'Desktop / Laptop',
                'badge' => 'AI Training / RLHF',
                'rating' => 4.8,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'UserTesting',
                'slug' => 'usertesting',
                'description' => 'Leading usability testing platform. Speak your thoughts aloud while testing mobile apps and websites for top global brands.',
                'payouts' => ['PayPal (M-Pesa)'],
                'mpesa_compatible' => true,
                'url' => 'https://www.usertesting.com',
                'earning_potential' => '$10.00 – $60.00 per test',
                'min_payout' => 'Paid 14 days after completion',
                'category' => 'research',
                'device' => 'PC / Mac / Mobile with Microphone',
                'badge' => 'UX Audio Reviews',
                'rating' => 4.9,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Clickworker & UHRS',
                'slug' => 'clickworker',
                'description' => 'Global AI training and microtask marketplace. Qualifying for the UHRS assessment unlocks thousands of search relevance and data categorization tasks.',
                'payouts' => ['PayPal (M-Pesa)', 'Payoneer'],
                'mpesa_compatible' => true,
                'url' => 'https://www.clickworker.com',
                'earning_potential' => '$4.00 – $12.00 per hour (UHRS)',
                'min_payout' => '$10.00',
                'category' => 'microtasks',
                'device' => 'Desktop / Laptop',
                'badge' => 'UHRS Search Tasks',
                'rating' => 4.7,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'AfriSight Panel',
                'slug' => 'afrisight',
                'description' => 'African market research panel purpose-built for East & West African consumers. Surveys address local consumer goods, mobile money, and healthcare.',
                'payouts' => ['Direct M-Pesa', 'Airtime Top-up', 'PayPal'],
                'mpesa_compatible' => true,
                'url' => 'https://www.afrisight.com',
                'earning_potential' => 'KES 50 – KES 350 per survey',
                'min_payout' => 'KES 500 (~$3.80)',
                'category' => 'surveys',
                'device' => 'Mobile App & Web',
                'badge' => 'Direct M-Pesa Payout',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Attapoll',
                'slug' => 'attapoll',
                'description' => 'Mobile-first survey application with frequent short polls targeted to East African demographics. Quick completion times with a very low cashout limit.',
                'payouts' => ['PayPal (M-Pesa)', 'Revolut', 'Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.attapoll.app',
                'earning_potential' => '$0.30 – $1.80 per short poll',
                'min_payout' => '$3.00',
                'category' => 'surveys',
                'device' => 'iOS & Android App',
                'badge' => 'Low $3 Cashout',
                'rating' => 4.7,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'PrizeRebel',
                'slug' => 'prizerebel',
                'description' => 'Reliable daily survey hub with partnership agreements across multiple survey routers (Dynata, Cint, BitLabs). Points process to PayPal within 24 hours.',
                'payouts' => ['PayPal (M-Pesa)', 'Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.prizerebel.com',
                'earning_potential' => '$0.60 – $2.50 per survey',
                'min_payout' => '$5.00',
                'category' => 'surveys',
                'device' => 'Mobile & Web',
                'badge' => '24h Payouts',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'TGM Panel Kenya',
                'slug' => 'tgm-panel',
                'description' => 'Dedicated Kenyan panel by TGM Research. Provides localized consumer opinions on banking, telecommunications, and digital finance.',
                'payouts' => ['PayPal (M-Pesa)', 'GCodes Virtual Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://tgmpanel.co.ke',
                'earning_potential' => '$0.50 – $3.00 per survey',
                'min_payout' => '$10.00',
                'category' => 'surveys',
                'device' => 'Web & Mobile',
                'badge' => 'Dedicated Kenya Hub',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Triaba Kenya',
                'slug' => 'triaba',
                'description' => 'Norwegian consumer research panel with a localized portal exclusively for Kenyan respondents, partnered with the Cint global opinion network.',
                'payouts' => ['PayPal (M-Pesa)', 'Tremendous Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.triaba.com/ke',
                'earning_potential' => '$0.50 – $2.25 per survey',
                'min_payout' => '$10.00',
                'category' => 'surveys',
                'device' => 'Web & Mobile',
                'badge' => 'Cint Network Partner',
                'rating' => 4.5,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'MetroOpinion Kenya',
                'slug' => 'metroopinion',
                'description' => 'Dedicated localized consumer survey portal for Kenya. Earn money per answered opinion and withdraw quickly via PayPal directly to M-Pesa.',
                'payouts' => ['PayPal (M-Pesa)', 'Vouchers'],
                'mpesa_compatible' => true,
                'url' => 'https://www.metroopinion.com/ke/',
                'earning_potential' => 'KES 100 – KES 450 per survey',
                'min_payout' => '$5.00 (KES 650)',
                'category' => 'surveys',
                'device' => 'Mobile & Web',
                'badge' => 'Kenya Portal',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Prolific',
                'slug' => 'prolific',
                'description' => 'Prestigious academic research platform backed by Oxford and global universities. Zero screenouts once invited to a study, with guaranteed ethical hourly minimum pay.',
                'payouts' => ['PayPal (M-Pesa)'],
                'mpesa_compatible' => true,
                'url' => 'https://www.prolific.com',
                'earning_potential' => '$6.00 – $15.00 per study',
                'min_payout' => '£6.00 (Instant after 4 cashouts)',
                'category' => 'research',
                'device' => 'Desktop / Laptop / Mobile',
                'badge' => 'No Screenouts',
                'rating' => 4.9,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'OneForma by Centific',
                'slug' => 'oneforma',
                'description' => 'Global crowd platform offering linguistic transcription, translation, and AI model evaluation projects open to East African freelancers.',
                'payouts' => ['Payoneer', 'PayPal (M-Pesa)'],
                'mpesa_compatible' => true,
                'url' => 'https://www.oneforma.com',
                'earning_potential' => '$5.00 – $15.00 per task/hour',
                'min_payout' => '$10.00',
                'category' => 'microtasks',
                'device' => 'Desktop / Laptop',
                'badge' => 'AI Localization',
                'rating' => 4.7,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Timebucks',
                'slug' => 'timebucks',
                'description' => 'Get-paid-to portal with micro-tasks, surveys, slideshow viewing, and social engagement tasks. Pays automatically every Thursday.',
                'payouts' => ['AirTM (M-Pesa)', 'Crypto (LTC)', 'Skrill'],
                'mpesa_compatible' => true,
                'url' => 'https://timebucks.com',
                'earning_potential' => '$0.40 – $2.00 per task/survey',
                'min_payout' => '$5.00',
                'category' => 'microtasks',
                'device' => 'Mobile & Web',
                'badge' => 'Weekly Auto-Pay',
                'rating' => 4.4,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Surveytime',
                'slug' => 'surveytime',
                'description' => 'Instant-paying survey platform. Complete a 10–15 minute survey and receive an immediate flat $1.00 directly to your PayPal account with no waiting threshold.',
                'payouts' => ['PayPal (M-Pesa)', 'Coinbase', 'Amazon Gift Card'],
                'mpesa_compatible' => true,
                'url' => 'https://surveytime.io',
                'earning_potential' => 'Flat $1.00 per completed survey',
                'min_payout' => '$1.00 (Instant)',
                'category' => 'surveys',
                'device' => 'Web & Mobile',
                'badge' => 'Instant $1 Payout',
                'rating' => 4.5,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Toloka AI',
                'slug' => 'toloka',
                'description' => 'Global AI data labelling and micro-task ecosystem. Complete image classification, audio transcription verification, and search relevance reviews.',
                'payouts' => ['Payoneer', 'AirTM (M-Pesa)'],
                'mpesa_compatible' => true,
                'url' => 'https://toloka.ai',
                'earning_potential' => '$2.00 – $8.00 per hour',
                'min_payout' => '$1.00',
                'category' => 'microtasks',
                'device' => 'iOS, Android & Web',
                'badge' => 'AI Microtasks',
                'rating' => 4.6,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Mobrog Kenya',
                'slug' => 'mobrog',
                'description' => 'German-based international market research panel with dedicated localized surveys for Kenyan consumers via mobile app or email invite.',
                'payouts' => ['PayPal (M-Pesa)', 'Skrill'],
                'mpesa_compatible' => true,
                'url' => 'https://www.mobrog.com/ke',
                'earning_potential' => '$0.50 – $2.00 per survey',
                'min_payout' => '$6.25 (KES 800)',
                'category' => 'surveys',
                'device' => 'Mobile App & Web',
                'badge' => 'Mobrog Kenya Panel',
                'rating' => 4.5,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'TestingTime',
                'slug' => 'testingtime',
                'description' => 'European user research hub conducting live 30 to 60-minute remote testing sessions and focus groups with product managers and UX designers.',
                'payouts' => ['PayPal (M-Pesa)', 'Direct Bank Transfer'],
                'mpesa_compatible' => true,
                'url' => 'https://www.testingtime.com',
                'earning_potential' => '€20 – €50 per study',
                'min_payout' => 'Paid within 10 days of session',
                'category' => 'research',
                'device' => 'Laptop / PC with Webcam',
                'badge' => 'Live UX Studies',
                'rating' => 4.8,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
            [
                'name' => 'Prime Opinion',
                'slug' => 'prime-opinion',
                'description' => 'High survey acceptance rate with low qualification screenouts and instant points processing to PayPal.',
                'payouts' => ['PayPal (M-Pesa)', 'Virtual Visa', 'Gift Cards'],
                'mpesa_compatible' => true,
                'url' => 'https://www.primeopinion.com',
                'earning_potential' => '$0.75 – $3.50 per survey',
                'min_payout' => '$5.00',
                'category' => 'surveys',
                'device' => 'Web & Mobile App',
                'badge' => 'High Match Rate',
                'rating' => 4.8,
                'kenya_verified' => true,
                'is_premium' => true,
            ],
        ];
    }

    /**
     * Essential FAQ content tailored for Kenyan respondents and Google FAQPage rich results.
     *
     * @return array<int, array{question: string, answer: string}>
     */
    public static function faqs(): array
    {
        return [
            [
                'question' => 'How do I withdraw survey earnings to M-Pesa in Kenya?',
                'answer' => 'Most legitimate global survey platforms pay in US Dollars via PayPal. You can instantly withdraw your USD earnings into M-Pesa using Safaricom’s official PayPal-to-M-Pesa portal (powered by Thunes at www.paypal-mobilemoney.com/m-pesa). Platforms like Freecash, Timebucks, and AfriSight also support crypto or direct M-Pesa transfers.',
            ],
            [
                'question' => 'Are legitimate paid survey platforms free to join?',
                'answer' => 'YES. 100% of legitimate market research panels are free to join. Legitimate companies pay YOU for your opinion; they never charge registration fees, "activation deposits", or kit purchases. Never share your M-Pesa PIN, national ID photo, or bank password with any survey site.',
            ],
            [
                'question' => 'How much money can a Kenyan realistically make from paid surveys?',
                'answer' => 'Online surveys should be treated as flexible side-income while searching for a full-time remote role. Active Kenyan participants typically earn between $20 and $100 per month (approx. KES 2,500 – KES 13,000) by registering on 3 to 4 panels and taking surveys during downtime. Specialized platforms like Respondent.io and Outlier AI pay significantly more ($50 – $200 per study/project) for qualified professionals.',
            ],
            [
                'question' => 'Should I use a VPN to get more international surveys?',
                'answer' => 'NO. Do NOT use a VPN or proxy on survey sites. Market research platforms use sophisticated fraud prevention tools (such as IPQualityScore and MaxMind). If they detect an IP address mismatch or VPN usage, your account will be permanently banned and your pending earnings forfeited. Always use your real Kenyan IP address.',
            ],
            [
                'question' => 'Why do I get disqualified from surveys after answering preliminary questions?',
                'answer' => 'Surveys target specific demographic profiles (e.g., car owners, tech buyers, parents with young children). When you answer the initial screener questions and your profile doesn’t match the specific quota the client is looking for, you get disqualified. To maximize completions, fill out your profile questionnaires thoroughly on each platform.',
            ],
        ];
    }

    /**
     * Generate Schema.org ItemList JSON-LD.
     */
    public static function itemListJsonLd(): string
    {
        $at = '@';
        $items = [];
        $position = 1;

        foreach (self::all() as $platform) {
            $items[] = [
                $at.'type' => 'ListItem',
                'position' => $position++,
                'name' => $platform['name'],
                'url' => $platform['url'],
                'description' => $platform['description'],
            ];
        }

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'type' => 'ItemList',
            'name' => 'Verified Paid Survey and Side-Income Platforms for Kenyans',
            'description' => 'A curated, verified directory of legitimate paid-survey and get-paid-to platforms for Kenyan jobseekers with confirmed payout rails.',
            'itemListElement' => $items,
        ]);
    }

    /**
     * Generate Schema.org FAQPage JSON-LD.
     */
    public static function faqJsonLd(): string
    {
        $at = '@';
        $entities = [];

        foreach (self::faqs() as $faq) {
            $entities[] = [
                $at.'type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    $at.'type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return json_encode([
            $at.'context' => 'https://schema.org',
            $at.'type' => 'FAQPage',
            'mainEntity' => $entities,
        ]);
    }

    /**
     * Legacy constant for backward compatibility with existing tests.
     */
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
