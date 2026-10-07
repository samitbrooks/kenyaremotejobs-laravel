<?php

namespace App\Support;

class JobCategorySeo
{
    /**
     * @return array<string, array{
     *     slug: string,
     *     name: string,
     *     title: string,
     *     h1: string,
     *     meta_description: string,
     *     badge: string,
     *     intro: string,
     *     keywords: string,
     *     salary_range: string,
     *     search_terms: array<int, string>,
     *     faqs: array<int, array{question: string, answer: string}>
     * }>
     */
    public static function all(): array
    {
        return [
            'customer-support-kenya' => [
                'slug' => 'customer-support-kenya',
                'name' => 'Customer Support',
                'title' => 'Remote Customer Support Jobs in Kenya (2026)',
                'h1' => 'Remote Customer Support & Success Jobs Open to Kenya',
                'meta_description' => 'Browse verified remote customer support, client success, and helpdesk jobs open to Kenyans. Earn $1,200 - $3,500/mo in USD from Nairobi or anywhere in Kenya.',
                'badge' => 'Customer Support & Success',
                'intro' => 'International companies in the US, UK, and Europe actively hire remote customer support and client success representatives in Kenya due to strong English fluency, cultural empathy, and favorable East Africa Time (EAT) timezone overlap. These roles range from live chat agents to technical customer success managers.',
                'keywords' => 'remote customer support jobs kenya, customer service work from home kenya, remote helpdesk jobs nairobi, online support jobs kenya',
                'salary_range' => '$1,200 - $3,500 / month (~KES 155,000 - 450,000)',
                'search_terms' => ['support', 'customer', 'success', 'client', 'helpdesk', 'service', 'representative'],
                'faqs' => [
                    [
                        'question' => 'What skills do I need for remote customer support jobs from Kenya?',
                        'answer' => 'Most employers look for strong written and spoken English, empathy, problem-solving skills, and familiarity with customer service ticketing tools like Zendesk, Intercom, Freshdesk, or Salesforce. A stable internet connection (fiber or 4G) and a quiet workspace are also required.',
                    ],
                    [
                        'question' => 'How much do remote customer support roles pay Kenyans?',
                        'answer' => 'Entry-level customer support roles typically pay between $800 and $1,500 per month (KES 100,000 - 195,000). Experienced Customer Success Managers (CSMs) and Technical Support Engineers can earn between $2,500 and $4,500+ per month.',
                    ],
                    [
                        'question' => 'How do I receive my salary in Kenya?',
                        'answer' => 'International companies pay via direct bank wire, Wise (formerly TransferWise), PayPal, Payoneer, or Deel/Remote.com, which can easily be withdrawn straight into M-Pesa or Kenyan local bank accounts in KES or USD.',
                    ],
                ],
            ],

            'virtual-assistant-kenya' => [
                'slug' => 'virtual-assistant-kenya',
                'name' => 'Virtual Assistant',
                'title' => 'Remote Virtual Assistant Jobs in Kenya (2026)',
                'h1' => 'Remote Virtual Assistant & Executive Support Jobs in Kenya',
                'meta_description' => 'Find legitimate remote virtual assistant, executive assistant, and admin jobs open to Kenya. Earn in USD working flexible hours from home.',
                'badge' => 'Virtual Assistant & Admin',
                'intro' => 'Virtual assistance is one of the fastest-growing remote career paths for Kenyans. Global founders, executives, and digital agencies hire Kenyan Virtual Assistants (VAs) for calendar management, inbox zero, research, social media management, and customer coordination.',
                'keywords' => 'virtual assistant jobs kenya, remote va jobs kenya, work from home executive assistant kenya, online admin jobs nairobi',
                'salary_range' => '$1,000 - $2,800 / month (~KES 130,000 - 360,000)',
                'search_terms' => ['assistant', 'admin', 'executive assistant', 'coordinator', 'operations', 'calendar'],
                'faqs' => [
                    [
                        'question' => 'Can beginners in Kenya become Virtual Assistants?',
                        'answer' => 'Yes. Virtual assistance does not require a specialized degree. Strong organizational skills, computer literacy, reliability, fast communication, and proficiency in tools like Google Workspace, Notion, Slack, and Trello are the primary qualifications.',
                    ],
                    [
                        'question' => 'Are virtual assistant roles full-time or part-time?',
                        'answer' => 'Both. Many clients hire full-time dedicated assistants (40 hours/week), while others seek part-time or project-based help (10-20 hours/week), making it viable for freelancing with multiple clients.',
                    ],
                ],
            ],

            'software-developer-kenya' => [
                'slug' => 'software-developer-kenya',
                'name' => 'Software Engineering',
                'title' => 'Remote Software Developer Jobs in Kenya (2026)',
                'h1' => 'Remote Software Engineering & Developer Jobs Open to Kenya',
                'meta_description' => 'Browse high-paying remote software engineering, frontend, backend, and full-stack jobs open to Kenyan tech talent. Verified USD compensation.',
                'badge' => 'Engineering & Tech',
                'intro' => 'Kenya has established itself as the Silicon Savannah, with world-class engineers in Nairobi and across East Africa. International companies and venture-backed startups routinely hire Kenyan developers for Full Stack, Backend (Python, Node.js, PHP/Laravel, Go), Frontend (React, Vue, TypeScript), and DevOps roles with top-tier international salaries.',
                'keywords' => 'remote software developer jobs kenya, remote tech jobs nairobi, full stack developer kenya remote, backend engineer remote kenya',
                'salary_range' => '$3,000 - $8,000+ / month (~KES 390,000 - 1,000,000+)',
                'search_terms' => ['engineer', 'developer', 'software', 'frontend', 'backend', 'fullstack', 'react', 'python', 'laravel', 'typescript'],
                'faqs' => [
                    [
                        'question' => 'Do US tech companies hire developers directly from Kenya?',
                        'answer' => 'Yes. Many global tech firms hire Kenyans as remote contractors or via Employer of Record (EOR) services like Deel, Remote, and OysterHR, providing full benefits, health insurance, and paid time off.',
                    ],
                    [
                        'question' => 'How can Kenyan software engineers stand out to international employers?',
                        'answer' => 'Have a clean, verifiable GitHub profile, a tailored resume focusing on business outcomes (e.g., "reduced latency by 35%"), strong asynchronous communication skills, and demonstration of full system ownership.',
                    ],
                ],
            ],

            'data-entry-kenya' => [
                'slug' => 'data-entry-kenya',
                'name' => 'Data Entry & Operations',
                'title' => 'Remote Data Entry & Admin Jobs in Kenya (2026)',
                'h1' => 'Legit Remote Data Entry, Annotation & Operations Jobs in Kenya',
                'meta_description' => 'Discover legitimate remote data entry, AI training, transcription, and data operations jobs for Kenyans. Avoid scams with our pre-screened listings.',
                'badge' => 'Data Entry & Operations',
                'intro' => 'Looking for legit data entry, transcription, and data annotation jobs in Kenya? Our platform screens out upfront-fee scams and predatory listings, surfacing genuine data entry and operational roles with verified companies worldwide.',
                'keywords' => 'data entry jobs kenya, legit online data entry kenya, transcription jobs kenya, remote data clerk nairobi',
                'salary_range' => '$800 - $2,000 / month (~KES 100,000 - 260,000)',
                'search_terms' => ['data', 'entry', 'annotation', 'transcription', 'analyst', 'spreadsheet', 'excel'],
                'faqs' => [
                    [
                        'question' => 'How do I avoid online data entry scams in Kenya?',
                        'answer' => 'Never pay an "application fee", "training fee", or "account verification fee". Legitimate employers will never ask candidates for money to get hired. Every job verified on KenyaRemoteJobs is completely free to view and apply.',
                    ],
                    [
                        'question' => 'What equipment is needed for remote data entry work?',
                        'answer' => 'A reliable laptop or desktop computer, stable internet connection, and familiarity with Microsoft Excel or Google Sheets. High typing speed (50+ WPM) and attention to detail are key advantages.',
                    ],
                ],
            ],

            'writing-content-kenya' => [
                'slug' => 'writing-content-kenya',
                'name' => 'Writing & Content',
                'title' => 'Remote Writing & Copywriting Jobs in Kenya (2026)',
                'h1' => 'Remote Content Writing, Copywriting & Editorial Jobs in Kenya',
                'meta_description' => 'Explore remote freelance and full-time writing jobs open to Kenyans. Blog writing, copywriting, technical writing, and SEO content roles.',
                'badge' => 'Writing & Content',
                'intro' => 'Kenyan writers are among the most sought-after English-language content creators in Africa. Explore remote copywriting, SEO article writing, technical documentation, and scriptwriting jobs open to global applicants without geographic restrictions.',
                'keywords' => 'remote writing jobs kenya, online content writer kenya, freelance copywriting jobs kenya, technical writer remote kenya',
                'salary_range' => '$1,000 - $3,500 / month (~KES 130,000 - 455,000)',
                'search_terms' => ['writing', 'writer', 'content', 'copywriter', 'copywriting', 'editor', 'editorial', 'technical writer', 'author', 'documentation', 'scriptwriter', 'curriculum', 'journalist', 'proofreader', 'reviewer'],
                'faqs' => [
                    [
                        'question' => 'How can I build a portfolio as a remote writer in Kenya?',
                        'answer' => 'Publish well-researched samples on Medium, Substack, or a personal LinkedIn newsletter. Having 3-5 published live clips in a specific niche (SaaS, finance, tech, healthcare) dramatically increases your hire rate.',
                    ],
                ],
            ],

            'digital-marketing-kenya' => [
                'slug' => 'digital-marketing-kenya',
                'name' => 'Digital Marketing',
                'title' => 'Remote Digital Marketing Jobs in Kenya (2026)',
                'h1' => 'Remote Digital Marketing, SEO & Social Media Jobs in Kenya',
                'meta_description' => 'Find remote digital marketing, SEO, paid media, and social media manager jobs open to Kenya. Work with global brands from anywhere.',
                'badge' => 'Digital Marketing & Growth',
                'intro' => 'Global startups and scale-ups look for digital marketers who can drive traffic, manage social media communities, run paid ad campaigns (Meta, Google, TikTok), and execute content marketing strategies asynchronously.',
                'keywords' => 'remote digital marketing jobs kenya, seo jobs kenya remote, social media manager jobs kenya, online growth marketer kenya',
                'salary_range' => '$1,500 - $4,500 / month (~KES 195,000 - 585,000)',
                'search_terms' => ['marketing', 'seo', 'social media', 'growth', 'campaign', 'paid media', 'brand'],
                'faqs' => [
                    [
                        'question' => 'What digital marketing roles are easiest to land remotely from Kenya?',
                        'answer' => 'Social Media Management, SEO Content Optimization, Email Marketing (Klaviyo, Mailchimp), and Paid Ads Management (Google Ads, Meta Ads) are the most frequently outsourced remote roles.',
                    ],
                ],
            ],

            'sales-business-development-kenya' => [
                'slug' => 'sales-business-development-kenya',
                'name' => 'Sales & SDR',
                'title' => 'Remote Sales & Business Development Jobs in Kenya (2026)',
                'h1' => 'Remote Sales, SDR & Business Development Jobs in Kenya',
                'meta_description' => 'High-commission remote sales, Sales Development Representative (SDR), and account executive jobs open to Kenyan professionals.',
                'badge' => 'Sales & Business Development',
                'intro' => 'US, European, and Pan-African tech companies hire Kenyan Sales Development Representatives (SDRs) and Account Executives to prospect, book meetings, and close deals across European and African time zones with generous USD base salaries plus commission.',
                'keywords' => 'remote sales jobs kenya, remote sdr jobs kenya, business development remote kenya, commission remote jobs nairobi',
                'salary_range' => '$1,500 - $5,000+ / month (Base + Commission)',
                'search_terms' => ['sales', 'sdr', 'business development', 'account executive', 'prospecting', 'lead'],
                'faqs' => [
                    [
                        'question' => 'Can Kenyans succeed in remote tech sales (SDR/BDR)?',
                        'answer' => 'Absolutely. Tech companies value strong verbal communication, resilience, consultative sales acumen, and timezone coverage. Many SDRs in Kenya earn substantial uncapped commission on top of their base pay.',
                    ],
                ],
            ],

            'entry-level-kenya' => [
                'slug' => 'entry-level-kenya',
                'name' => 'Entry-Level Jobs',
                'title' => 'Entry-Level Remote Jobs in Kenya (No Experience, 2026)',
                'h1' => 'Entry-Level Remote Jobs in Kenya (No Experience Required)',
                'meta_description' => 'Discover entry-level remote jobs open to Kenya. Start your international remote career with training provided and no prior experience required.',
                'badge' => 'Entry-Level & Junior',
                'intro' => 'Breaking into international remote work does not always require years of experience. We curate remote positions that offer on-the-job training, entry-level customer support, basic moderation, data labeling, and junior administrative roles suitable for recent graduates and career switchers.',
                'keywords' => 'entry level remote jobs kenya, online jobs for students kenya, remote work no experience kenya, junior remote jobs nairobi',
                'salary_range' => '$600 - $1,500 / month (~KES 78,000 - 195,000)',
                'search_terms' => ['junior', 'entry level', 'trainee', 'intern', 'assistant', 'support'],
                'faqs' => [
                    [
                        'question' => 'Can university students in Kenya apply for remote jobs?',
                        'answer' => 'Yes! Many remote roles offer flexible part-time hours or evening shifts (especially US timezone overlap), allowing Kenyan university students to earn significant income while completing their studies.',
                    ],
                    [
                        'question' => 'What should I put on my CV if I have no remote experience?',
                        'answer' => 'Highlight transferable skills, academic projects, volunteer work, computer literacy, personal portfolio projects, and fast typing speed. Use our built-in AI CV tool to tailor your resume for junior roles.',
                    ],
                ],
            ],

            'graphic-design-kenya' => [
                'slug' => 'graphic-design-kenya',
                'name' => 'Design & Creative',
                'title' => 'Remote Graphic Design & UI/UX Jobs in Kenya (2026)',
                'h1' => 'Remote Graphic Design, UI/UX & Creative Jobs Open to Kenya',
                'meta_description' => 'Browse remote graphic design, UI/UX, product design, and creative roles for Kenyans. Work with international brands and earn in USD from Nairobi or anywhere in Kenya.',
                'badge' => 'Design & Creative',
                'intro' => 'International tech companies and digital marketing agencies actively hire Kenyan visual designers, UI/UX practitioners, Figma specialists, and multimedia editors. These roles allow Kenyan creatives to build portfolios with world-class brands without geographical barriers.',
                'keywords' => 'remote graphic design jobs kenya, ui ux jobs kenya remote, figma designer jobs nairobi, online creative jobs kenya',
                'salary_range' => '$1,200 - $4,000 / month (~KES 155,000 - 520,000)',
                'search_terms' => ['design', 'designer', 'ui/ux', 'graphic', 'figma', 'creative', 'illustrator', 'visual', 'video'],
                'faqs' => [
                    [
                        'question' => 'What design tools are most in demand for remote design jobs in Kenya?',
                        'answer' => 'Figma is the industry standard for product and UI/UX design. Adobe Creative Cloud (Illustrator, Photoshop, Premiere Pro) and Canva are essential for brand identity and social media content creation.',
                    ],
                    [
                        'question' => 'Where should Kenyan designers host their portfolio?',
                        'answer' => 'Behance, Dribbble, or a personal website built with Framer or Webflow. International clients look for 3 to 5 case studies demonstrating problem solving and business impact.',
                    ],
                ],
            ],

            'accounting-finance-kenya' => [
                'slug' => 'accounting-finance-kenya',
                'name' => 'Accounting & Finance',
                'title' => 'Remote Accounting & Bookkeeping Jobs in Kenya (2026)',
                'h1' => 'Remote Accounting, Bookkeeping & Financial Analyst Jobs in Kenya',
                'meta_description' => 'Find legitimate remote accounting, QuickBooks bookkeeping, and financial analyst roles open to Kenyan CPAs and finance professionals. Verified USD payouts.',
                'badge' => 'Accounting & Finance',
                'intro' => 'US, UK, and European small-to-medium businesses frequently outsource bookkeeping, accounts payable/receivable, financial reconciliation, and payroll management to qualified Kenyan accountants and CPAs who provide rigorous accuracy and cost efficiency.',
                'keywords' => 'remote accounting jobs kenya, remote bookkeeping jobs kenya, quickbooks remote kenya, online finance jobs nairobi',
                'salary_range' => '$1,500 - $4,500 / month (~KES 195,000 - 585,000)',
                'search_terms' => ['accounting', 'accountant', 'bookkeeper', 'finance', 'cpa', 'quickbooks', 'financial', 'audit', 'payroll'],
                'faqs' => [
                    [
                        'question' => 'Can Kenyan CPAs work for US or UK companies remotely?',
                        'answer' => 'Yes. While tax filing differs by country, general ledger bookkeeping, accounts reconciliation, and financial modeling are universal. Familiarity with US GAAP or IFRS and cloud software like QuickBooks Online or Xero is highly valued.',
                    ],
                ],
            ],

            'ai-training-annotation-kenya' => [
                'slug' => 'ai-training-annotation-kenya',
                'name' => 'AI Training & Annotation',
                'title' => 'Remote AI Data Annotation & Training Jobs in Kenya (2026)',
                'h1' => 'Remote AI Training, Data Annotation & Prompt Engineering Jobs in Kenya',
                'meta_description' => 'Discover legitimate remote AI data labeling, RLHF prompt evaluation, and model training positions for Kenyans. Reliable USD payouts to PayPal, Payoneer and M-Pesa.',
                'badge' => 'AI Training & Annotation',
                'intro' => 'With the global boom in Large Language Models (LLMs), AI companies (such as Outlier, Scale AI, Alignerr, and Remotasks) actively hire thousands of Kenyan specialists for RLHF (Reinforcement Learning from Human Feedback), prompt evaluation, coding model tutoring, and domain-specific dataset labeling.',
                'keywords' => 'ai training jobs kenya, rlhf remote jobs kenya, data annotation jobs nairobi, outlier ai kenya, prompt engineer remote kenya',
                'salary_range' => '$800 - $2,500 / month (~KES 100,000 - 325,000)',
                'search_terms' => ['ai', 'annotation', 'labeling', 'prompt', 'rlhf', 'evaluator', 'machine learning', 'data trainer'],
                'faqs' => [
                    [
                        'question' => 'What is AI data annotation and RLHF work in Kenya?',
                        'answer' => 'AI annotation involves reviewing and rating model outputs for accuracy, harmlessness, and helpfulness, writing high-quality answers to complex prompts, or labeling datasets. Subject matter experts in math, coding, literature, and science are in particularly high demand.',
                    ],
                    [
                        'question' => 'How are Kenyan AI trainers paid?',
                        'answer' => 'Payouts are typically weekly or bi-weekly via PayPal, Payoneer, or direct bank transfer, which can be withdrawn directly into Safaricom M-Pesa.',
                    ],
                ],
            ],

            'transcription-translation-kenya' => [
                'slug' => 'transcription-translation-kenya',
                'name' => 'Transcription & Translation',
                'title' => 'Remote Audio Transcription & Translation Jobs in Kenya (2026)',
                'h1' => 'Remote Audio Transcription, Captioning & Translation Jobs in Kenya',
                'meta_description' => 'Legitimate remote audio transcription, video captioning, and English-Swahili translation jobs open to Kenyan freelancers. Flexible hours working from home.',
                'badge' => 'Transcription & Translation',
                'intro' => 'Kenyan linguists and fast typists are in high demand for legal, medical, academic, and general audio transcription, as well as English-Swahili localization and video subtitling for international media and research organizations.',
                'keywords' => 'transcription jobs kenya, online transcriber kenya, english swahili translation jobs remote, captioning jobs nairobi',
                'salary_range' => '$700 - $2,000 / month (~KES 90,000 - 260,000)',
                'search_terms' => ['transcription', 'transcriber', 'translation', 'translator', 'captioning', 'subtitles', 'swahili', 'audio', 'linguist'],
                'faqs' => [
                    [
                        'question' => 'How can I pass transcription tests for global platforms?',
                        'answer' => 'Practice active listening, use noise-canceling headphones, master strict verbatim vs clean verbatim guidelines, and use tools like Express Scribe. A typing speed of 60+ WPM with 98% accuracy ensures passing.',
                    ],
                ],
            ],

            'human-resources-kenya' => [
                'slug' => 'human-resources-kenya',
                'name' => 'Human Resources & Recruiting',
                'title' => 'Remote HR & Talent Acquisition Jobs in Kenya (2026)',
                'h1' => 'Remote Human Resources, Talent Sourcing & Recruiter Jobs in Kenya',
                'meta_description' => 'Browse international remote HR, technical recruiting, and people operations jobs open to Kenyan professionals. Work with global remote-first companies.',
                'badge' => 'Human Resources & Recruiting',
                'intro' => 'Global remote-first companies hire Kenyan HR coordinators, technical recruiters, and talent sourcers to manage international hiring pipelines, schedule interviews across time zones, and handle employee onboarding asynchronously.',
                'keywords' => 'remote hr jobs kenya, remote technical recruiter kenya, talent acquisition remote nairobi, online human resources jobs kenya',
                'salary_range' => '$1,500 - $4,200 / month (~KES 195,000 - 545,000)',
                'search_terms' => ['hr', 'recruiter', 'recruiting', 'talent', 'human resources', 'sourcing', 'people operations', 'onboarding'],
                'faqs' => [
                    [
                        'question' => 'What HR tools do remote companies expect candidates to know?',
                        'answer' => 'Applicant Tracking Systems (ATS) such as Greenhouse, Lever, Ashby, and Workable, along with LinkedIn Recruiter, Deel, and Slack for asynchronous team coordination.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     slug: string,
     *     name: string,
     *     title: string,
     *     h1: string,
     *     meta_description: string,
     *     badge: string,
     *     intro: string,
     *     keywords: string,
     *     salary_range: string,
     *     search_terms: array<int, string>,
     *     faqs: array<int, array{question: string, answer: string}>
     * }|null
     */
    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    /**
     * Map common search queries to their dedicated programmatic SEO category landing slug.
     */
    public static function findSlugForQuery(string $query): ?string
    {
        $normalized = strtolower(trim($query));

        $directMap = [
            'writing' => 'writing-content-kenya',
            'writer' => 'writing-content-kenya',
            'content' => 'writing-content-kenya',
            'copywriting' => 'writing-content-kenya',
            'content writer' => 'writing-content-kenya',
            'copywriter' => 'writing-content-kenya',
            'technical writer' => 'writing-content-kenya',
            'editorial' => 'writing-content-kenya',

            'support' => 'customer-support-kenya',
            'customer support' => 'customer-support-kenya',
            'customer service' => 'customer-support-kenya',
            'helpdesk' => 'customer-support-kenya',
            'client success' => 'customer-support-kenya',

            'virtual assistant' => 'virtual-assistant-kenya',
            'va' => 'virtual-assistant-kenya',
            'executive assistant' => 'virtual-assistant-kenya',
            'admin assistant' => 'virtual-assistant-kenya',

            'developer' => 'software-developer-kenya',
            'software engineer' => 'software-developer-kenya',
            'engineer' => 'software-developer-kenya',
            'software developer' => 'software-developer-kenya',
            'frontend' => 'software-developer-kenya',
            'backend' => 'software-developer-kenya',
            'fullstack' => 'software-developer-kenya',
            'full stack' => 'software-developer-kenya',

            'data entry' => 'data-entry-kenya',
            'data clerk' => 'data-entry-kenya',

            'marketing' => 'digital-marketing-kenya',
            'digital marketing' => 'digital-marketing-kenya',
            'social media' => 'digital-marketing-kenya',
            'seo' => 'digital-marketing-kenya',

            'sales' => 'sales-business-development-kenya',
            'sdr' => 'sales-business-development-kenya',
            'bdr' => 'sales-business-development-kenya',
            'business development' => 'sales-business-development-kenya',

            'entry level' => 'entry-level-kenya',
            'junior' => 'entry-level-kenya',
            'intern' => 'entry-level-kenya',
            'internship' => 'entry-level-kenya',

            'design' => 'graphic-design-kenya',
            'designer' => 'graphic-design-kenya',
            'graphic design' => 'graphic-design-kenya',
            'ui/ux' => 'graphic-design-kenya',
            'ui ux' => 'graphic-design-kenya',

            'accounting' => 'accounting-finance-kenya',
            'accountant' => 'accounting-finance-kenya',
            'bookkeeper' => 'accounting-finance-kenya',
            'finance' => 'accounting-finance-kenya',

            'ai' => 'ai-training-annotation-kenya',
            'ai training' => 'ai-training-annotation-kenya',
            'annotation' => 'ai-training-annotation-kenya',
            'data annotation' => 'ai-training-annotation-kenya',

            'transcription' => 'transcription-translation-kenya',
            'transcriber' => 'transcription-translation-kenya',
            'translation' => 'transcription-translation-kenya',
            'translator' => 'transcription-translation-kenya',

            'hr' => 'human-resources-kenya',
            'recruiter' => 'human-resources-kenya',
            'human resources' => 'human-resources-kenya',
        ];

        return $directMap[$normalized] ?? null;
    }
}
