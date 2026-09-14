<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * @return array<string, array{
     *     title: string,
     *     icon: string,
     *     items: array<int, array{question: string, answer: string}>
     * }>
     */
    public static function categories(): array
    {
        return [
            'general' => [
                'title' => 'General & Getting Started',
                'icon' => 'globe',
                'items' => [
                    [
                        'question' => 'What is KenyaRemoteJobs?',
                        'answer' => 'KenyaRemoteJobs is a specialized remote job platform that connects Kenyan professionals with legitimate international companies hiring remotely worldwide. Every job is pre-screened for timezone compatibility with East Africa Time (UTC+3) and visa eligibility, eliminating the frustration of applying to region-locked postings.',
                    ],
                    [
                        'question' => 'Are these jobs open to candidates living anywhere in Kenya?',
                        'answer' => 'Yes. Whether you are in Nairobi, Mombasa, Kisumu, Nakuru, Eldoret, or any other town in Kenya, as long as you have a reliable internet connection and a laptop, you can apply and work for these global employers.',
                    ],
                    [
                        'question' => 'Is KenyaRemoteJobs free to use?',
                        'answer' => 'Yes! Searching jobs, viewing company names, reading descriptions, and applying to direct employer listings is 100% free. We also offer Pro Early Access for KES 1,499/month (via M-Pesa), which gives you full instant access to apply to freshly posted remote jobs during the 48-hour recruiter early-look window, plus unlimited AI CV tailoring.',
                    ],
                    [
                        'question' => 'How are job listings sourced and verified?',
                        'answer' => 'We continuously curate opportunities from verified global employer networks, distributed tech companies, and direct employer partnerships actively seeking Kenyan talent. Every listing is programmatically and manually vetted for East Africa Time (UTC+3) alignment, transparent pay, and genuine openness to candidates living in Kenya without visa barriers.',
                    ],
                ],
            ],

            'jobs_and_applying' => [
                'title' => 'Finding & Applying for Jobs',
                'icon' => 'sparkle',
                'items' => [
                    [
                        'question' => 'What does the "Kenya-Friendly Match" badge mean?',
                        'answer' => 'Our scoring algorithm analyzes every job description for timezone overlap, international eligibility, and visa restrictions. If a job is open worldwide, overlaps East Africa Time, and does not restrict applicants to the US or EU, it earns the Kenya-Friendly Match badge so you can apply with confidence.',
                    ],
                    [
                        'question' => 'Do these remote jobs require a US or European work visa?',
                        'answer' => 'No. Jobs flagged as Kenya-Friendly do not require US or EU work authorization. Companies hire Kenyan talent either as independent international contractors (B2B) or through global Employer of Record (EOR) services like Deel, Remote.com, or OysterHR.',
                    ],
                    [
                        'question' => 'What is Pro Early Access?',
                        'answer' => 'When a new job is first posted, the first 20 applicants receive over 80% of recruiter interviews. Pro Early Access grants you immediate access to apply to freshly posted jobs during their initial 48-hour window before they open to the general public.',
                    ],
                    [
                        'question' => 'How does the AI CV & Cover Letter Copilot work?',
                        'answer' => 'On any job listing, clicking "AI CV Tailor" lets you paste your existing resume details. Our AI analyzes the exact role description and outputs ATS-optimized bullet points and a targeted cover letter tailored to beat automated screening algorithms.',
                    ],
                    [
                        'question' => 'Can I calculate my match score without uploading a full CV?',
                        'answer' => 'Yes! Visit our Match page (/match) to enter your primary skills, experience level, and preferred roles. We will calculate a personalized percentage match for every active listing on the platform.',
                    ],
                ],
            ],

            'payments_and_work' => [
                'title' => 'Payments & Remote Setup',
                'icon' => 'coin',
                'items' => [
                    [
                        'question' => 'How do international employers pay remote workers in Kenya?',
                        'answer' => 'Most overseas companies pay in US Dollars, Euros, or British Pounds via Wise (TransferWise), direct bank wire, PayPal, Payoneer, or contractor platforms like Deel. These funds can be withdrawn straight into M-Pesa or Kenyan local bank accounts within minutes to hours.',
                    ],
                    [
                        'question' => 'Can I receive international payments directly to M-Pesa?',
                        'answer' => 'Yes! Wise supports direct transfers to M-Pesa at real mid-market exchange rates with minimal fees. PayPal also connects to M-Pesa through the PayPal-M-Pesa portal (powered by Thunes).',
                    ],
                    [
                        'question' => 'What hardware and internet speed do I need?',
                        'answer' => 'A functional laptop or desktop with at least 8GB RAM, a working webcam and headset for Zoom/Google Meet calls, and a reliable home fiber or 4G connection (at least 15–20 Mbps). Having a battery backup (UPS) for your Wi-Fi router is highly recommended.',
                    ],
                    [
                        'question' => 'Do remote workers in Kenya have to pay tax to KRA?',
                        'answer' => 'Yes. Income earned by Kenyan tax residents from overseas clients is subject to local income tax under KRA regulations. Independent contractors declare income and expenses annually via iTax.',
                    ],
                ],
            ],

            'safety_and_scams' => [
                'title' => 'Safety & Scam Protection',
                'icon' => 'check',
                'items' => [
                    [
                        'question' => 'How does KenyaRemoteJobs screen out scams?',
                        'answer' => 'We automatically block listings that demand upfront payments, promote shady multi-level marketing (MLM) schemes, or lack verifiable company domain records. We only index from reputable global networks and direct verified employers.',
                    ],
                    [
                        'question' => 'Will a legitimate employer ever ask me for money?',
                        'answer' => 'NEVER. A legitimate employer or recruiter will never ask you to pay an "application fee", "training fee", "background check fee", or "security deposit". If any listing asks for money, report it immediately.',
                    ],
                ],
            ],

            'employers' => [
                'title' => 'For Employers & Recruiters',
                'icon' => 'announce',
                'items' => [
                    [
                        'question' => 'How do I post a remote job to hire Kenyan talent?',
                        'answer' => 'Visit /employers/post to submit your job details, company logo, and application URL or email. You can pay seamlessly via M-Pesa or card. Your job will be featured to thousands of skilled East African candidates.',
                    ],
                    [
                        'question' => 'How much does it cost to post a job?',
                        'answer' => 'Basic job posts are KES 2,500 for 30 days of visibility. Boosted listings are KES 5,500, which includes top-of-list placement and feature in the candidate newsletter.',
                    ],
                ],
            ],
        ];
    }

    public function index(): View
    {
        $categories = self::categories();

        $allFaqs = [];
        foreach ($categories as $cat) {
            foreach ($cat['items'] as $item) {
                $allFaqs[] = $item;
            }
        }

        return view('faqs', [
            'categories' => $categories,
            'allFaqs' => $allFaqs,
        ]);
    }
}
