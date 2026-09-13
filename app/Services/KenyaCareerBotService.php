<?php

namespace App\Services;

use App\Models\JobListing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class KenyaCareerBotService
{
    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{reply: string, suggested_actions: array<int, array{label: string, url: string}>}
     */
    public function ask(string $message, array $history = []): array
    {
        $geminiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');

        if ($geminiKey) {
            try {
                $response = $this->callGemini($geminiKey, $message, $history);
                if ($response) {
                    return $response;
                }
            } catch (Throwable $e) {
                Log::warning('[career-bot] Gemini API error, falling back to local engine', ['error' => $e->getMessage()]);
            }
        }

        return $this->generateLocalReply($message);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{reply: string, suggested_actions: array<int, array{label: string, url: string}>}|null
     */
    private function callGemini(string $apiKey, string $message, array $history): ?array
    {
        $systemInstruction = "You are 'Ivy AI', the expert remote career advisor on KenyaRemoteJobs.com.\n"
            ."Your mission is to guide Kenyan and East African professionals into landing high-paying global remote roles (US, UK, Europe, Canada, Worldwide).\n"
            ."Tone: Warm, highly practical, Kenyan-grounded, encouraging, and authoritative on remote contracting.\n"
            ."Key Knowledge:\n"
            ."- Payment methods: Wise (fastest, best rate), Payoneer, direct Wire to Kenyan bank accounts (Equity, KCB, Absa, Stanbic USD accounts), and local M-Pesa withdrawals via Wise/Payoneer.\n"
            ."- Tax / Forms: For US clients, Kenyan remote contractors fill IRS Form W-8BEN to claim 0% US withholding tax as non-resident foreign contractors (enter your Kenyan KRA PIN in Part I Line 6a).\n"
            ."- Timezone Advantage: Nairobi is East Africa Time (EAT / UTC+3), giving 4-5 hours daily working overlap with London, Berlin, Amsterdam and 3 hours with New York/Boston mornings. Frame this as superior to Asian timezones.\n"
            ."- KenyaRemoteJobs Pro: Monthly KES 1,499 (via M-Pesa). Unlocks 48-Hour Early Access (apply before 500+ applicants flood recruiter inboxes), full access to all 800+ remote listings, direct Kenyan employer postings, and unlimited AI CV ATS tailoring.\n"
            ."- Keep responses concise, formatted in clean Markdown with bullet points, under 180 words.\n"
            .'- Always suggest relevant platform links: /jobs, /pricing, /match, /resume-builder.';

        $contents = [];
        foreach (array_slice($history, -4) as $item) {
            $contents[] = [
                'role' => $item['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $item['content']]],
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        $res = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(12)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 450,
                ],
            ]);

        if ($res->successful()) {
            $text = $res->json('candidates.0.content.parts.0.text');
            if ($text) {
                return [
                    'reply' => trim($text),
                    'suggested_actions' => $this->determineSuggestedActions($message),
                ];
            }
        }

        return null;
    }

    /**
     * @return array{reply: string, suggested_actions: array<int, array{label: string, url: string}>}
     */
    private function generateLocalReply(string $message): array
    {
        $lower = mb_strtolower($message);

        // 1. Foreign Payments (Wise, Payoneer, M-Pesa, Bank Wire)
        if (str_contains($lower, 'pay') || str_contains($lower, 'wise') || str_contains($lower, 'money') || str_contains($lower, 'm-pesa') || str_contains($lower, 'mpesa') || str_contains($lower, 'salary') || str_contains($lower, 'dollar') || str_contains($lower, 'bank') || str_contains($lower, 'usd')) {
            return [
                'reply' => "**Getting paid from foreign remote clients is straightforward for Kenyans:**\n\n"
                    ."1. **Wise (Recommended):** Open a multi-currency Wise account. Your client pays in USD/GBP/EUR, and you convert to KES and send directly to **M-Pesa or Equity/KCB in seconds** at the real mid-market rate.\n"
                    ."2. **Payoneer:** Provides a virtual US/UK checking account routing number for direct client ACH deposits.\n"
                    ."3. **Direct Bank Wire:** Share your Kenyan bank SWIFT code + USD account number.\n\n"
                    .'💡 *Pro members get our pre-formatted USD Contractor Invoice template in the remote toolkit!*',
                'suggested_actions' => [
                    ['label' => 'View Pro Membership', 'url' => '/pricing'],
                    ['label' => 'Browse Remote Jobs', 'url' => '/jobs'],
                ],
            ];
        }

        // 2. W-8BEN / Tax
        if (str_contains($lower, 'tax') || str_contains($lower, 'w8') || str_contains($lower, 'w-8') || str_contains($lower, 'w-8ben') || str_contains($lower, 'withholding') || str_contains($lower, 'kra') || str_contains($lower, 'irs')) {
            return [
                'reply' => "**How to handle US remote taxes (IRS Form W-8BEN):**\n\n"
                    ."When working with US remote companies as an independent contractor in Kenya:\n"
                    ."- **You are NOT subject to US 30% withholding tax.**\n"
                    ."- You will be asked to sign **Form W-8BEN** (Certificate of Foreign Status of Beneficial Owner).\n"
                    ."- **Critical Tip:** In **Part I Line 6a** (Foreign Tax Identifying Number), enter your **Kenyan KRA PIN**.\n"
                    ."- This certifies your tax residency in Kenya, so your US client pays you **100% of your invoice without deductions**.\n\n"
                    .'📑 *Our complete W-8BEN step-by-step cheat sheet is included with Pro.*',
                'suggested_actions' => [
                    ['label' => 'Unlock Contractor Toolkit', 'url' => '/pricing'],
                    ['label' => 'Read Journal Articles', 'url' => '/journal'],
                ],
            ];
        }

        // 3. Pro Early Access & Pricing
        if (str_contains($lower, 'pro') || str_contains($lower, 'price') || str_contains($lower, 'pricing') || str_contains($lower, 'early access') || str_contains($lower, 'cost') || str_contains($lower, 'subscription') || str_contains($lower, 'fee')) {
            return [
                'reply' => "**Why serious job seekers upgrade to Pro:**\n\n"
                    ."When global remote jobs open, **over 500 applicants apply within 72 hours**. Hiring teams interview on a rolling basis and close listings once they find 10–20 strong candidates.\n\n"
                    ."**Pro gives you:**\n"
                    ."• **Full Access to All 800+ Jobs:** Immediate apply access to every listing.\n"
                    ."• **48-Hour Early Access Window:** Apply to fresh roles before public release.\n"
                    ."• **Direct Kenyan Employer Roles:** Apply to companies actively recruiting local talent.\n"
                    ."• **1-Click AI CV & Cover Letter Tailoring:** 94%+ ATS keyword alignment.\n"
                    ."• **Remote Contractor Toolkit:** USD invoice generator & W-8BEN guide.\n\n"
                    .'*Plans start at KES 1,499/mo (just KES 50/day) via instant M-Pesa STK push.*',
                'suggested_actions' => [
                    ['label' => 'Get Full Access to All Jobs', 'url' => '/pricing'],
                    ['label' => 'Browse Jobs First', 'url' => '/jobs'],
                ],
            ];
        }

        // 4. Timezone advantage
        if (str_contains($lower, 'timezone') || str_contains($lower, 'eat') || str_contains($lower, 'hour') || str_contains($lower, 'europe') || str_contains($lower, 'london') || str_contains($lower, 'overlap')) {
            return [
                'reply' => "**The East Africa Time (EAT / UTC+3) Superpower:**\n\n"
                    ."Nairobi has one of the best timezones in the world for international remote work:\n"
                    ."• **UK & Europe (GMT / CET):** 4 to 5 hours of clean working overlap every single afternoon.\n"
                    ."• **US East Coast (EST):** 3 hours of overlap with their morning standups (2 PM to 5 PM EAT).\n"
                    ."• **Unlike candidates in Asia**, you don't need to work vampire graveyard shifts to collaborate in real-time.\n\n"
                    ."💡 *In your cover letters, always state: 'Based in Nairobi (EAT / UTC+3) with 5+ hours of real-time collaboration with European and US teams.'*",
                'suggested_actions' => [
                    ['label' => 'Build AI Cover Letter', 'url' => '/resume-builder'],
                    ['label' => 'Search Europe & US Remote Jobs', 'url' => '/jobs'],
                ],
            ];
        }

        // 5. Job Search / Recommend Roles
        if (str_contains($lower, 'job') || str_contains($lower, 'hiring') || str_contains($lower, 'role') || str_contains($lower, 'work') || str_contains($lower, 'apply') || str_contains($lower, 'developer') || str_contains($lower, 'assistant') || str_contains($lower, 'support') || str_contains($lower, 'marketing') || str_contains($lower, 'data')) {
            $recentCount = JobListing::visible()->count();
            $recentCount = $recentCount > 0 ? $recentCount : 840;

            return [
                'reply' => "**We currently have {$recentCount}+ active remote roles open to Kenyan applicants!**\n\n"
                    ."Top categories currently hiring:\n"
                    ."• **Customer Support & Virtual Assistance:** Technical chat, email triage, and executive support ($1,200–$2,800/mo).\n"
                    ."• **Software Engineering & DevOps:** Fullstack, Python, Laravel, React, and Cloud specialists ($2,500–$7,000/mo).\n"
                    ."• **Marketing & Content Writing:** Copywriting, SEO, social media managers ($1,500–$3,500/mo).\n"
                    ."• **Data Entry & Operations:** Remote coordination and QA ($1,000–$2,000/mo).\n\n"
                    .'All listings show 100% transparent company details and verified Kenya-Friendly match scores.',
                'suggested_actions' => [
                    ['label' => "Explore {$recentCount}+ Live Jobs", 'url' => '/jobs'],
                    ['label' => 'Find Your Best Match', 'url' => '/match'],
                ],
            ];
        }

        // 6. CV & Resume Advice
        if (str_contains($lower, 'cv') || str_contains($lower, 'resume') || str_contains($lower, 'cover letter') || str_contains($lower, 'ats') || str_contains($lower, 'interview')) {
            return [
                'reply' => "**Top 3 rules to get remote interview callbacks:**\n\n"
                    ."1. **Ditch the 5-page academic CV:** International remote recruiters want a **clean 1-to-2 page resume** with quantified bullet points (e.g. *'Managed 12 client accounts resulting in 98% retention'*).\n"
                    ."2. **Target the ATS (Applicant Tracking System):** Mirror the exact technical keywords from the job description so screener bots don't filter you out.\n"
                    ."3. **Emphasize Remote Readiness:** Highlight asynchronous communication, Slack/Notion/Jira proficiency, and reliable high-speed fiber internet.\n\n"
                    .'✨ *You can use our 1-Click AI Tailoring tool on any job listing to generate role-specific ATS bullets instantly!*',
                'suggested_actions' => [
                    ['label' => 'Try CV Builder', 'url' => '/resume-builder'],
                    ['label' => 'Browse Jobs with AI Tailor', 'url' => '/jobs'],
                ],
            ];
        }

        // Default warm response
        return [
            'reply' => "Hello! I am **Ivy AI**, your KenyaRemoteJobs career advisor 🇰🇪\n\n"
                ."I can help you with:\n"
                ."• **Foreign Payments:** How to receive USD/EUR via Wise, Payoneer & M-Pesa.\n"
                ."• **US Taxes:** How to complete IRS Form W-8BEN with your KRA PIN.\n"
                ."• **Pro Early Access:** How to apply in the first 48 hours before 500+ candidates flood in.\n"
                ."• **Job Recommendations:** Finding roles that match your skills with EAT timezone overlap.\n\n"
                .'What can I help you with today?',
            'suggested_actions' => [
                ['label' => 'Browse 800+ Jobs', 'url' => '/jobs'],
                ['label' => 'Check Pro Membership', 'url' => '/pricing'],
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    private function determineSuggestedActions(string $message): array
    {
        $lower = mb_strtolower($message);

        if (str_contains($lower, 'job') || str_contains($lower, 'find') || str_contains($lower, 'work')) {
            return [
                ['label' => 'Browse 800+ Jobs', 'url' => '/jobs'],
                ['label' => 'Find Your Match', 'url' => '/match'],
            ];
        }

        if (str_contains($lower, 'cv') || str_contains($lower, 'resume')) {
            return [
                ['label' => 'Resume & Cover Letter Builder', 'url' => '/resume-builder'],
                ['label' => 'Browse Jobs', 'url' => '/jobs'],
            ];
        }

        return [
            ['label' => 'Browse 800+ Jobs', 'url' => '/jobs'],
            ['label' => 'Unlock Pro Full Access', 'url' => '/pricing'],
        ];
    }
}
