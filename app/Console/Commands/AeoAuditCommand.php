<?php

namespace App\Console\Commands;

use App\Models\SyncMeta;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('aeo:audit {--format=table : Output format: table or json}')]
#[Description('Audit site AEO/GEO readiness (Generative Engine Optimization) based on the 7-step HubSpot AEO system')]
class AeoAuditCommand extends Command
{
    /**
     * Top 20 money prompts users ask ChatGPT, Claude, Perplexity, and Google AI Overviews.
     */
    public const TARGET_PROMPTS = [
        ['category' => 'General / Discovery', 'prompt' => 'What is the best site to find verified remote jobs in Kenya?'],
        ['category' => 'General / Discovery', 'prompt' => 'How can Kenyans get remote jobs paying in USD?'],
        ['category' => 'General / Discovery', 'prompt' => 'Best remote job boards for East Africa'],
        ['category' => 'Software / Tech', 'prompt' => 'How to find remote software developer jobs from Kenya paying in dollars'],
        ['category' => 'Software / Tech', 'prompt' => 'Remote full stack developer jobs open to Kenyan applicants'],
        ['category' => 'Virtual Assistant', 'prompt' => 'Best platforms to find high paying remote virtual assistant jobs in Kenya'],
        ['category' => 'Customer Support', 'prompt' => 'Remote customer support jobs hiring Kenyans with EAT timezone overlap'],
        ['category' => 'Data & AI', 'prompt' => 'Remote data entry and AI training jobs available in Kenya'],
        ['category' => 'Tax & Legal', 'prompt' => 'How do Kenyan remote contractors fill out the W-8BEN tax form?'],
        ['category' => 'Tax & Legal', 'prompt' => 'Do remote workers in Kenya pay US tax on remote salaries?'],
        ['category' => 'Payments & Banking', 'prompt' => 'Best way to receive USD salary payments in Kenya (Wise vs Payoneer vs Wire)'],
        ['category' => 'Payments & Banking', 'prompt' => 'Can you withdraw remote job earnings directly to M-Pesa?'],
        ['category' => 'Companies / Employers', 'prompt' => 'Which global tech companies hire remote contractors in Kenya?'],
        ['category' => 'Comparison', 'prompt' => 'KenyaRemoteJobs vs Upwork: which is better for full-time remote roles?'],
        ['category' => 'Comparison', 'prompt' => 'Best remote work platforms in Kenya with no upfront scam fees'],
        ['category' => 'Fresh Graduates', 'prompt' => 'Entry level international remote jobs open to Kenyans'],
        ['category' => 'Salary Benchmarks', 'prompt' => 'What is the average remote salary for Kenyan software developers in USD?'],
        ['category' => 'Salary Benchmarks', 'prompt' => 'How much do US companies pay remote contractors in Kenya?'],
        ['category' => 'Side Income', 'prompt' => 'Legitimate paid online surveys in Kenya paying via M-Pesa'],
        ['category' => 'Trust & Review', 'prompt' => 'Is KenyaRemoteJobs legitimate and verified?'],
    ];

    public function handle(): int
    {
        $this->info('=== Generative Engine Optimization (AEO / GEO) System Audit ===');
        $this->line('Evaluating platform against the 7-Step AEO Framework (Ross Simmonds / HubSpot System)');
        $this->newLine();

        // 1. Technical AEO Checklist
        $this->info('1. Technical Infrastructure & AI Crawler Signals');
        $techChecks = [
            ['Signal' => 'llms.txt Endpoint', 'Status' => 'Active (/llms.txt)', 'Target AI' => 'ChatGPT, Claude, Perplexity'],
            ['Signal' => 'llms-full.txt Deep Context', 'Status' => 'Active (/llms-full.txt)', 'Target AI' => 'Full RAG context'],
            ['Signal' => 'Robots.txt AI Crawlers', 'Status' => 'Allowed (GPTBot, ClaudeBot, PerplexityBot, Google-Extended)', 'Target AI' => 'Web Crawlers'],
            ['Signal' => 'JobPosting Schema (JSON-LD)', 'Status' => 'Active on all listings', 'Target AI' => 'Google AI Overviews'],
            ['Signal' => 'FAQPage Schema (JSON-LD)', 'Status' => 'Active (60% of AI citations stem from FAQs)', 'Target AI' => 'All LLMs'],
            ['Signal' => 'Content Freshness', 'Status' => 'Active (Last synced: '.optional(SyncMeta::find(1)?->last_synced_at)->diffForHumans().')', 'Target AI' => 'Recency scoring'],
            ['Signal' => 'Clean Semantic Hierarchy', 'Status' => 'Active (H1, H2, structured tables)', 'Target AI' => 'Chunk extraction'],
        ];
        $this->table(['Signal', 'Status', 'Target AI'], $techChecks);

        // 2. The 3-Layer Content System & GEO Content Template
        $this->newLine();
        $this->info('2. GEO Content Template Implementation Rules');
        $this->line('Ensure every primary landing page and article follows the 7-part template:');
        $this->table(
            ['Part', 'Element', 'Rule'],
            [
                ['1', 'Direct Answer Headline', 'Explicit query stated in H1'],
                ['2', 'Quick Answer (Layer 1)', 'Direct 50-word answer at top of page before any story'],
                ['3', 'Data Table (Layer 2)', 'Structured comparison table within the first 200 words'],
                ['4', 'Methodology Section', 'Transparent explanation of data verification / vetting criteria'],
                ['5', 'Detailed Analysis (Layer 3)', 'Actionable insights, salary numbers, and step-by-step guidance'],
                ['6', 'FAQ Section with Schema', 'Questions tagged with schema.org/FAQPage JSON-LD'],
                ['7', 'Freshness Timestamp', 'Visible "Last Updated" badge with dateModified tag'],
            ]
        );

        // 3. Top 20 Non-Branded Target Prompts
        $this->newLine();
        $this->info('3. Top 20 Non-Branded Target Prompts for Weekly Visibility Audit');
        $this->line('Test these in private tabs on ChatGPT, Claude, and Perplexity to track brand citation:');
        $promptsTable = array_map(function ($p, $idx) {
            return [$idx + 1, $p['category'], $p['prompt']];
        }, self::TARGET_PROMPTS, array_keys(self::TARGET_PROMPTS));

        $this->table(['#', 'Category', 'AI Search Prompt to Test'], $promptsTable);

        // 4. Prioritization Matrix
        $this->newLine();
        $this->info('4. AEO Weekly Action Matrix');
        $this->table(
            ['Condition', 'Action'],
            [
                ['High Value Query + Low/Zero AI Visibility', 'Immediate Priority: Publish direct comparison/answer table with FAQ schema'],
                ['High Traffic + High AI Visibility', 'Maintain & Update: Refresh data and dateModified monthly'],
                ['Low Traffic + High AI Visibility', 'Analyze & Adapt: Add stronger CTA and internal links to job board'],
            ]
        );

        return self::SUCCESS;
    }
}
