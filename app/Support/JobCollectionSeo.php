<?php

namespace App\Support;

class JobCollectionSeo
{
    /**
     * @return array<string, array{
     *     slug: string,
     *     title: string,
     *     h1: string,
     *     meta_description: string,
     *     badge: string,
     *     intro: string,
     *     keywords: string,
     *     icon: string,
     *     filter_type: string,
     *     faqs: array<int, array{question: string, answer: string}>
     * }>
     */
    public static function all(): array
    {
        return [
            'companies-paying-via-wise-mpesa-kenya' => [
                'slug' => 'companies-paying-via-wise-mpesa-kenya',
                'title' => 'Remote Jobs Paying via Wise & M-Pesa in Kenya (2026)',
                'h1' => 'Remote Jobs with Direct Wise, Bank Wire & M-Pesa Payouts',
                'meta_description' => 'Browse verified remote jobs from international companies that pay Kenyan contractors through Wise, SWIFT bank transfer, or direct M-Pesa with low conversion fees.',
                'badge' => 'Verified Payment Rails',
                'intro' => 'Tired of losing 6-8% on predatory currency exchange rates? These international employers pay remote workers in Kenya using fast, modern rails like Wise, ACH to USD accounts, or direct M-Pesa withdrawals.',
                'keywords' => 'remote jobs paying via wise kenya, mpesa remote jobs, international client payments kenya, usd remote salaries nairobi',
                'icon' => 'coin',
                'filter_type' => 'payment_rails',
                'faqs' => [
                    [
                        'question' => 'How does Wise payout to M-Pesa work?',
                        'answer' => 'When your employer sends USD to your Wise account or initiates a Wise transfer to your Kenyan phone number, the funds convert at the mid-market exchange rate and land in your Safaricom M-Pesa wallet within seconds.',
                    ],
                    [
                        'question' => 'Do these companies charge transaction fees to the employee?',
                        'answer' => 'Legitimate international remote employers cover their domestic sending fees, meaning you receive 100% of your gross invoice or salary amount.',
                    ],
                ],
            ],

            'zero-visa-requirement-remote-jobs-kenya' => [
                'slug' => 'zero-visa-requirement-remote-jobs-kenya',
                'title' => 'Worldwide Remote Jobs with Zero Visa Restrictions (Open to Kenya)',
                'h1' => 'True Worldwide Remote Jobs — No US or EU Visa Required',
                'meta_description' => 'Find verified global remote roles that have zero location restrictions. Work from anywhere in Kenya without US green cards, H1B visas, or EU work authorization.',
                'badge' => 'Zero Visa Hurdles',
                'intro' => 'Most "remote" jobs secretly bury US-only or EU-only clauses three paragraphs down. This curated collection includes only 100% location-agnostic roles that are genuinely open to applicants living anywhere in Kenya.',
                'keywords' => 'worldwide remote jobs kenya, no visa remote jobs nairobi, global remote companies, international work from home kenya',
                'icon' => 'globe',
                'filter_type' => 'zero_visa',
                'faqs' => [
                    [
                        'question' => 'How do international companies hire Kenyans without a visa?',
                        'answer' => 'Companies hire you as an Independent International Contractor (B2B) using IRS Form W-8BEN, or through a Kenyan Employer of Record (EOR) entity like Deel, Remote.com, or OysterHR.',
                    ],
                    [
                        'question' => 'Can I work from any town in Kenya?',
                        'answer' => 'Yes. As long as you maintain a stable high-speed internet connection and quiet workspace, you can work from Nairobi, Mombasa, Kisumu, Nakuru, Eldoret, or any other county.',
                    ],
                ],
            ],

            'entry-level-remote-jobs-kenya' => [
                'slug' => 'entry-level-remote-jobs-kenya',
                'title' => 'Entry-Level Remote Jobs Open to Kenya (No Prior Experience)',
                'h1' => 'Entry-Level & Junior Remote Jobs Open to Kenyan Talent',
                'meta_description' => 'Kickstart your international remote career from Nairobi or anywhere in Kenya. Browse junior customer support, data entry, virtual assistance, and junior developer roles.',
                'badge' => 'Early Career & Beginners',
                'intro' => 'Looking to break into international remote work without 5+ years of experience? These roles emphasize trainable skills, strong written English, curiosity, and proactive communication over senior corporate tenure.',
                'keywords' => 'entry level remote jobs kenya, junior work from home jobs nairobi, beginner remote work kenya, online jobs for students kenya',
                'icon' => 'sparkle',
                'filter_type' => 'entry_level',
                'faqs' => [
                    [
                        'question' => 'What are the easiest remote jobs to break into for beginners?',
                        'answer' => 'Customer support (live chat/ticketing), virtual assistance, AI prompt evaluation/data annotation, and content moderation have the lowest technical barrier to entry while still paying $800 - $1,500/month.',
                    ],
                    [
                        'question' => 'Do I need a university degree to qualify?',
                        'answer' => 'Most remote-first companies care far more about your practical skills, typing speed, English fluency, and problem-solving ability than formal degree credentials.',
                    ],
                ],
            ],

            'high-paying-usd-remote-jobs-kenya' => [
                'slug' => 'high-paying-usd-remote-jobs-kenya',
                'title' => 'High-Paying Remote Jobs in Kenya ($3,000+/mo — KES 400,000+)',
                'h1' => 'High-Comp Remote Roles Paying $3,000 to $10,000+ / Month',
                'meta_description' => 'Explore premium international remote roles open to Kenyan senior engineers, product managers, designers, and consultants paying competitive Silicon Valley & European USD salaries.',
                'badge' => '$3,000+ / Month',
                'intro' => 'Top Kenyan talent deserves global market compensation. These senior engineering, architecture, design, and operations roles pay between $36,000 and $120,000+ per year in USD directly to East African professionals.',
                'keywords' => 'high paying remote jobs kenya, 500k remote jobs nairobi, senior remote software engineer kenya, earn in dollars kenya',
                'icon' => 'announce',
                'filter_type' => 'high_salary',
                'faqs' => [
                    [
                        'question' => 'How are high USD salaries taxed in Kenya?',
                        'answer' => 'Under Kenyan tax law, you file your remote contractor income on KRA iTax and can deduct all legitimate business expenses (home office, laptops, fiber internet, software subscriptions) before computing individual income tax.',
                    ],
                    [
                        'question' => 'What is the best way to receive $3,000+/mo in Kenya?',
                        'answer' => 'Direct SWIFT wire into a local Foreign Currency (USD) bank account (Equity, StanChart, I&M, NCBA) or Wise gives you the lowest conversion spreads and maximum control over currency exchange timing.',
                    ],
                ],
            ],

            'night-shift-vs-day-shift-remote-jobs-eat' => [
                'slug' => 'night-shift-vs-day-shift-remote-jobs-eat',
                'title' => 'EAT-Friendly Remote Jobs in Kenya (Day & Evening Shifts)',
                'h1' => 'Remote Roles Aligned with East Africa Time (UTC+3)',
                'meta_description' => 'Find remote jobs with daytime European overlap or afternoon/evening US shifts. Choose the work schedule that fits your lifestyle in Kenya.',
                'badge' => 'EAT (UTC+3) Aligned',
                'intro' => 'Working remotely does not mean sacrificing your health to 3:00 AM graveyard shifts. These listings offer favorable European daytime hours (GMT/CET) or balanced morning-to-evening US shifts.',
                'keywords' => 'eat timezone remote jobs, daytime remote work kenya, evening shift remote kenya, flexible hours work from home nairobi',
                'icon' => 'clock',
                'filter_type' => 'eat_aligned',
                'faqs' => [
                    [
                        'question' => 'What are the best timezones for Kenyan remote workers?',
                        'answer' => 'UK and European companies share a 4 to 6-hour direct overlap with Kenyan business hours (EAT is only 2-3 hours ahead of London/Berlin). For US companies, an afternoon-to-evening shift (e.g. 4 PM to 12 AM) matches US East Coast morning hours.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     slug: string,
     *     title: string,
     *     h1: string,
     *     meta_description: string,
     *     badge: string,
     *     intro: string,
     *     keywords: string,
     *     icon: string,
     *     filter_type: string,
     *     faqs: array<int, array{question: string, answer: string}>
     * }|null
     */
    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}
