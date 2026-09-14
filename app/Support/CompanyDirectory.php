<?php

namespace App\Support;

class CompanyDirectory
{
    /**
     * @return array<string, array{
     *     slug: string,
     *     name: string,
     *     website: string,
     *     logo: string,
     *     headquarters: string,
     *     industry: string,
     *     size: string,
     *     verified_in_kenya: bool,
     *     headline: string,
     *     description: string,
     *     why_kenya: string,
     *     hiring_model: string,
     *     payment_methods: array<int, string>,
     *     eat_overlap_hours: string,
     *     common_roles: array<int, string>,
     *     perks: array<int, string>,
     *     search_terms: array<int, string>,
     *     faqs: array<int, array{question: string, answer: string}>
     * }>
     */
    public static function all(): array
    {
        return [
            'automattic' => [
                'slug' => 'automattic',
                'name' => 'Automattic',
                'website' => 'https://automattic.com',
                'logo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'San Francisco, CA (100% Distributed Worldwide)',
                'industry' => 'Web Software, CMS & Publishing',
                'size' => '2,000+ employees in 95+ countries',
                'verified_in_kenya' => true,
                'headline' => 'Creators of WordPress.com, WooCommerce, and Tumblr',
                'description' => 'Automattic is one of the world\'s original and most successful all-remote companies. They build WordPress.com, WooCommerce, Day One, and Tumblr, operating with zero physical offices across nearly 100 countries.',
                'why_kenya' => 'Automattic hires globally without geographic tiers. Kenyan engineers and customer support specialists (Happiness Engineers) thrive here due to strong English fluency and East Africa Time alignment with European and African clients.',
                'hiring_model' => 'B2B Independent Contractor or Global EOR',
                'payment_methods' => ['Direct USD Bank Wire (SWIFT)', 'Wise', 'PayPal'],
                'eat_overlap_hours' => '4-6 hours core overlap (Operates mostly asynchronously via Slack and P2 blogs)',
                'common_roles' => ['Happiness Engineer (Customer Support)', 'Code Wrangler (PHP/JavaScript/React)', 'Systems Engineer', 'Product Designer'],
                'perks' => ['Home Office Setup Allowance ($2,000+)', 'Annual Global Company Retreats', 'Open Vacation Policy', 'Wellness & Learning Stipends'],
                'search_terms' => ['Automattic', 'WordPress', 'WooCommerce', 'Tumblr'],
                'faqs' => [
                    [
                        'question' => 'Does Automattic hire applicants living in Kenya?',
                        'answer' => 'Yes. Automattic explicitly hires worldwide and has employed staff across East Africa for over a decade. All roles are 100% remote.',
                    ],
                    [
                        'question' => 'What is the interview process like at Automattic?',
                        'answer' => 'Automattic uses a unique, asynchronous hiring process: text-based Slack interviews, followed by a paid trial project where you collaborate with real team members for a few weeks.',
                    ],
                    [
                        'question' => 'How much does Automattic pay remote workers in Kenya?',
                        'answer' => 'Automattic uses global, market-competitive compensation. Customer support Happiness Engineers typically earn $50,000 to $70,000/year, while software engineers earn $90,000 to $140,000+ in USD.',
                    ],
                ],
            ],

            'gitlab' => [
                'slug' => 'gitlab',
                'name' => 'GitLab',
                'website' => 'https://about.gitlab.com',
                'logo' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'All-Remote (Zero Headquarters)',
                'industry' => 'DevOps, Cloud & Developer Tools',
                'size' => '2,200+ team members worldwide',
                'verified_in_kenya' => true,
                'headline' => 'The World\'s Largest Dedicated All-Remote Software Company',
                'description' => 'GitLab pioneered the modern all-remote revolution and wrote the industry-standard Remote Work Handbook. They build an integrated DevSecOps platform used by millions of software developers globally.',
                'why_kenya' => 'GitLab hires engineers and technical support specialists across Africa through registered Employer of Record (EOR) partners and direct contractor agreements, valuing rigorous technical documentation skills.',
                'hiring_model' => 'Employer of Record (EOR via Remote.com) or B2B Contractor',
                'payment_methods' => ['Direct Bank Wire (USD or KES via EOR)', 'Wise'],
                'eat_overlap_hours' => 'Purely asynchronous (No mandatory meeting hours)',
                'common_roles' => ['Backend Engineer (Ruby, Go)', 'Frontend Engineer (Vue.js)', 'Technical Support Engineer', 'Site Reliability Engineer (SRE)'],
                'perks' => ['Competitive Global Compensation', 'Remote Office Budget', 'Growth & Development Allowance', 'Flexible Asynchronous Schedule'],
                'search_terms' => ['GitLab'],
                'faqs' => [
                    [
                        'question' => 'How does GitLab pay Kenyan employees?',
                        'answer' => 'GitLab employs international team members either through global EOR partners (like Remote.com) where statutory Kenyan benefits are handled, or as independent contractors paid in USD.',
                    ],
                    [
                        'question' => 'Do I need to be online during US business hours at GitLab?',
                        'answer' => 'No. GitLab is famous for extreme asynchronous communication. Over 80% of communication happens via GitLab issues, merge requests, and Google Docs rather than synchronous video meetings.',
                    ],
                ],
            ],

            'canonical' => [
                'slug' => 'canonical',
                'name' => 'Canonical',
                'website' => 'https://canonical.com',
                'logo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'London, UK (Fully Distributed Global Team)',
                'industry' => 'Open Source, Linux OS & Cloud Infrastructure',
                'size' => '1,000+ team members in 70+ countries',
                'verified_in_kenya' => true,
                'headline' => 'Publishers of Ubuntu Linux and Open-Source Cloud Platforms',
                'description' => 'Canonical is the technology powerhouse behind Ubuntu Linux. With team members across six continents, Canonical powers clouds, servers, IoT devices, and developer workstations across the globe.',
                'why_kenya' => 'Canonical has a long-standing history of hiring software engineers, QA specialists, and technical writers across Africa. Kenyan developers with strong Linux, Python, Go, and open-source skills are actively recruited.',
                'hiring_model' => 'Independent Contractor (B2B) or Regional Entity',
                'payment_methods' => ['Direct SWIFT Wire', 'Wise'],
                'eat_overlap_hours' => '4-6 hours direct overlap with London/CET business hours',
                'common_roles' => ['Linux Kernel Engineer', 'Python / Go Software Engineer', 'Cloud Solutions Architect', 'Technical Author'],
                'perks' => ['Twice-Yearly Global Engineering Summits', 'Home Tech Equipment Allowance', 'Training & Certification Budget'],
                'search_terms' => ['Canonical', 'Ubuntu'],
                'faqs' => [
                    [
                        'question' => 'What qualifications does Canonical look for in Kenyan applicants?',
                        'answer' => 'Canonical values rigorous academic excellence (strong university math/computer science foundations), deep familiarity with Linux and open-source ecosystems, and crisp written English communication.',
                    ],
                    [
                        'question' => 'Are Canonical remote jobs open to applicants in Nairobi or Mombasa?',
                        'answer' => 'Yes. Canonical job postings explicitly state remote location eligibility by region, and Kenya falls squarely within the EMEA (Europe, Middle East & Africa) hiring region.',
                    ],
                ],
            ],

            'superside' => [
                'slug' => 'superside',
                'name' => 'Superside',
                'website' => 'https://superside.com',
                'logo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'All-Remote (Wilmington, DE)',
                'industry' => 'Creative Tech, Design & Digital Marketing',
                'size' => '800+ creatives in 60+ countries',
                'verified_in_kenya' => true,
                'headline' => 'Subscription Creative & Design Agency for Fortune 500 Brands',
                'description' => 'Superside delivers high-speed design and creative solutions for global enterprises like Amazon, Salesforce, Shopify, and Meta. Their distributed team spans over 60 countries.',
                'why_kenya' => 'Superside has one of the largest active remote creative contingents in Kenya, hiring graphic designers, motion designers, creative project managers, and copywriters across Nairobi.',
                'hiring_model' => 'Independent Contractor (B2B)',
                'payment_methods' => ['Wise (Direct to M-Pesa or Bank)', 'Deel', 'PayPal'],
                'eat_overlap_hours' => 'Flexible shifts covering European and American timezones',
                'common_roles' => ['Creative Project Manager (CPM)', 'Senior Brand Designer', 'Motion Graphics Animator', 'Copywriter'],
                'perks' => ['Stable Monthly USD Retainer', 'Paid Time Off', 'Continuous Design Mentorship', 'High-Speed Internet Allowance'],
                'search_terms' => ['Superside'],
                'faqs' => [
                    [
                        'question' => 'How does Superside evaluate Kenyan creative applicants?',
                        'answer' => 'Applicants complete a portfolio review and a timed creative assessment test. They look for speed, attention to brand guidelines, and mastery of Figma, Adobe Creative Cloud, or After Effects.',
                    ],
                    [
                        'question' => 'Can non-technical professionals get hired at Superside?',
                        'answer' => 'Yes! Creative Project Managers (CPMs) are among Superside\'s highest-volume hires in Kenya, managing client timelines and design briefs.',
                    ],
                ],
            ],

            'modus-create' => [
                'slug' => 'modus-create',
                'name' => 'Modus Create',
                'website' => 'https://moduscreate.com',
                'logo' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'Reston, VA (Fully Distributed Worldwide)',
                'industry' => 'Digital Product Consulting & Cloud Architecture',
                'size' => '600+ consultants across 50+ countries',
                'verified_in_kenya' => true,
                'headline' => 'Global Digital Transformation & Product Development Consultancy',
                'description' => 'Modus Create builds mission-critical applications and cloud systems for major financial, healthcare, and technology clients worldwide.',
                'why_kenya' => 'Modus Create hires senior African developers, QA automation engineers, and cloud architects to collaborate directly with US enterprise accounts.',
                'hiring_model' => 'B2B Contractor (Full-Time Retainer)',
                'payment_methods' => ['Direct Wire', 'Wise'],
                'eat_overlap_hours' => '3-5 hours overlap with US Eastern Standard Time (EST)',
                'common_roles' => ['Senior React / React Native Engineer', 'Senior Java / Spring Boot Developer', 'AWS DevOps Architect', 'QA Automation Engineer'],
                'perks' => ['Premium Hourly & Retainer Rates', 'Conference & Certification Sponsorship', 'Hardware Provisioning'],
                'search_terms' => ['Modus Create', 'Modus'],
                'faqs' => [
                    [
                        'question' => 'What is the compensation range at Modus Create for Kenyans?',
                        'answer' => 'Senior engineering consultants typically earn between $35 and $65 per hour ($6,000 - $10,000+/month) depending on tech stack and seniority.',
                    ],
                ],
            ],

            'outlier-ai' => [
                'slug' => 'outlier-ai',
                'name' => 'Outlier.ai',
                'website' => 'https://outlier.ai',
                'logo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'San Francisco, CA',
                'industry' => 'Artificial Intelligence & Large Language Model Training',
                'size' => '10,000+ AI Specialists Worldwide',
                'verified_in_kenya' => true,
                'headline' => 'Frontier AI Training & RLHF Prompt Evaluation Platform',
                'description' => 'Outlier connects subject matter experts with world-leading AI labs (such as OpenAI, Google, and Anthropic) to train, critique, and evaluate cutting-edge AI models.',
                'why_kenya' => 'Kenya is a primary international market for Outlier across multiple specialties: software developers reviewing AI-generated code, English writing specialists, and African language evaluators.',
                'hiring_model' => 'Freelance / Flexible Independent Contractor',
                'payment_methods' => ['PayPal', 'Airtm', 'Direct Bank via Partner'],
                'eat_overlap_hours' => '100% Flexible (Self-paced task queues available 24/7)',
                'common_roles' => ['AI Code Review Specialist', 'Generalist Reasoning Evaluator', 'Swahili / Regional Dialect Expert', 'STEM Reasoning Specialist'],
                'perks' => ['Weekly USD Payouts', 'Completely Flexible Hours', 'Competitive Hourly Rates ($15 - $40/hr)'],
                'search_terms' => ['Outlier', 'Remotasks'],
                'faqs' => [
                    [
                        'question' => 'How often does Outlier pay Kenyan workers?',
                        'answer' => 'Outlier processes earnings weekly via PayPal or Airtm, which can easily be withdrawn straight into M-Pesa within minutes.',
                    ],
                    [
                        'question' => 'Can university students in Kenya apply to Outlier?',
                        'answer' => 'Yes. Anyone with strong written English, programming literacy, or analytical reasoning can take the entrance screening test.',
                    ],
                ],
            ],

            'toptal' => [
                'slug' => 'toptal',
                'name' => 'Toptal',
                'website' => 'https://toptal.com',
                'logo' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'San Francisco, CA (100% Remote Global Network)',
                'industry' => 'Elite Freelance & Talent Network',
                'size' => 'Distributed global network of top 3% talent',
                'verified_in_kenya' => true,
                'headline' => 'Exclusive Global Talent Network for Elite Developers & Designers',
                'description' => 'Toptal connects the top 3% of freelance software engineers, designers, project managers, and finance experts with Fortune 500 companies and high-growth Silicon Valley startups.',
                'why_kenya' => 'Dozens of elite Kenyan software engineers and product designers have passed Toptal\'s rigorous screening, commanding high US hourly rates while living in Kenya.',
                'hiring_model' => 'Freelance / Contract via Toptal Marketplace',
                'payment_methods' => ['Toptal Payments (Direct Bank Wire / Wise)', 'Payoneer', 'PayPal'],
                'eat_overlap_hours' => 'Client-dependent (Typically 3-5 hours overlap requested)',
                'common_roles' => ['Senior Full-Stack Engineer', 'UI/UX Product Designer', 'DevOps & Cloud Specialist', 'Project Manager'],
                'perks' => ['Premium Hourly Rates ($40 - $100+/hr)', 'Direct Access to Silicon Valley Startups', 'Global Toptal Community Events'],
                'search_terms' => ['Toptal'],
                'faqs' => [
                    [
                        'question' => 'How difficult is the Toptal screening process for Kenyans?',
                        'answer' => 'Toptal has a 5-step screening: language & personality, in-depth algorithmic test (Codility), technical live coding interview, and a test project. Less than 3% pass, but those who do gain access to top-paying clients worldwide.',
                    ],
                ],
            ],

            'boldly' => [
                'slug' => 'boldly',
                'name' => 'Boldly',
                'website' => 'https://boldly.com',
                'logo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'New York, NY (100% Remote Subscription Staffing)',
                'industry' => 'Executive Virtual Assistance & Business Operations',
                'size' => '200+ Executive VAs & Project Managers',
                'verified_in_kenya' => true,
                'headline' => 'Premium Subscription Virtual Assistance for Executives and Founders',
                'description' => 'Boldly provides dedicated, premium executive assistants, project managers, and marketing specialists to founders and executives across North America and Europe.',
                'why_kenya' => 'Boldly hires high-caliber bilingual and English-fluent executive assistants with strong corporate backgrounds in East Africa to support global leaders.',
                'hiring_model' => 'Independent Contractor (W-8BEN compliant)',
                'payment_methods' => ['Wise', 'Direct Bank Wire'],
                'eat_overlap_hours' => '4-6 hours overlap with US Eastern or UK business hours',
                'common_roles' => ['Executive Assistant (EA)', 'Marketing Specialist', 'Project Manager', 'Customer Experience Lead'],
                'perks' => ['Long-Term Client Retainers', 'Above-Market Hourly Pay ($20 - $35/hr)', 'Structured Training & Paid Onboarding'],
                'search_terms' => ['Boldly'],
                'faqs' => [
                    [
                        'question' => 'What experience does Boldly require for Kenyan applicants?',
                        'answer' => 'Boldly typically requires a minimum of 5 to 7 years of prior executive support or corporate project management experience, plus flawless written communication.',
                    ],
                ],
            ],

            'buffer' => [
                'slug' => 'buffer',
                'name' => 'Buffer',
                'website' => 'https://buffer.com',
                'logo' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'All-Remote (Zero Offices Worldwide)',
                'industry' => 'Social Media Management & Marketing Tech',
                'size' => '85+ team members across 20+ countries',
                'verified_in_kenya' => true,
                'headline' => 'Pioneers of Radical Workplace Transparency and 4-Day Workweeks',
                'description' => 'Buffer is famous for publishing its entire salary formula, revenue numbers, and diversity metrics publicly. They build software helping small businesses grow on social media.',
                'why_kenya' => 'Buffer hires across all timezones without geographic penalties. Customer advocates and engineers in East Africa enjoy full parity with Western colleagues.',
                'hiring_model' => 'Contractor or EOR',
                'payment_methods' => ['Wise', 'Direct Bank Wire'],
                'eat_overlap_hours' => '4-Day Workweek (32 hours/week), largely asynchronous',
                'common_roles' => ['Customer Advocate', 'Product Engineer', 'Growth Marketer', 'Content Writer'],
                'perks' => ['4-Day Workweek (Fridays Off)', 'Transparent Salary Formula', 'Home Office & Coworking Stipend', 'Profit Sharing'],
                'search_terms' => ['Buffer'],
                'faqs' => [
                    [
                        'question' => 'Does Buffer really have a 4-day workweek for remote workers?',
                        'answer' => 'Yes. Since 2020, Buffer operates on a 4-day workweek (32 hours) with no salary reduction for all team members globally.',
                    ],
                ],
            ],

            'deel' => [
                'slug' => 'deel',
                'name' => 'Deel',
                'website' => 'https://deel.com',
                'logo' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80',
                'headquarters' => 'San Francisco, CA (100% Remote Global Workforce)',
                'industry' => 'Global Payroll, Compliance & HR Technology',
                'size' => '3,500+ employees in 100+ countries',
                'verified_in_kenya' => true,
                'headline' => 'The Global Platform Powering International Remote Hiring',
                'description' => 'Deel is the platform thousands of companies use to hire, pay, and onboard international contractors and employees. Naturally, Deel\'s own team is 100% remote across over 100 countries.',
                'why_kenya' => 'Deel has a significant local presence in Kenya, hiring customer support, compliance analysts, fintech operations specialists, and sales development representatives (SDRs).',
                'hiring_model' => 'Direct Kenyan Contract / Deel Platform',
                'payment_methods' => ['Deel Card / Instant M-Pesa Withdrawal', 'Direct Bank Transfer', 'Wise', 'Coinbase/Crypto'],
                'eat_overlap_hours' => 'Flexible shifts with local East African team members',
                'common_roles' => ['Customer Support Specialist', 'Fintech Operations Analyst', 'Sales Development Representative (SDR)', 'Onboarding Specialist'],
                'perks' => ['Instant Withdrawal to M-Pesa', 'Flexible Working Hours', 'Global Coworking Access (WeWork)', 'Equipment Budget'],
                'search_terms' => ['Deel'],
                'faqs' => [
                    [
                        'question' => 'How does Deel pay its remote workers in Kenya?',
                        'answer' => 'Deel employees receive pay directly in their Deel balance, which can be withdrawn in under 30 seconds straight into Safaricom M-Pesa or local Kenyan bank accounts in KES or USD.',
                    ],
                    [
                        'question' => 'What non-tech jobs does Deel hire in Nairobi?',
                        'answer' => 'Customer support agents, customer onboarding leads, and compliance specialists who verify identity documents across African jurisdictions.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     slug: string,
     *     name: string,
     *     website: string,
     *     logo: string,
     *     headquarters: string,
     *     industry: string,
     *     size: string,
     *     verified_in_kenya: bool,
     *     headline: string,
     *     description: string,
     *     why_kenya: string,
     *     hiring_model: string,
     *     payment_methods: array<int, string>,
     *     eat_overlap_hours: string,
     *     common_roles: array<int, string>,
     *     perks: array<int, string>,
     *     search_terms: array<int, string>,
     *     faqs: array<int, array{question: string, answer: string}>
     * }|null
     */
    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}
