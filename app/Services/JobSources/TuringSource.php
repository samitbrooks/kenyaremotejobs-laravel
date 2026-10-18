<?php

namespace App\Services\JobSources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TuringSource implements JobSource
{
    private const GREENHOUSE_API = 'https://api.greenhouse.io/v1/boards/turing/jobs?content=true';

    private const WORK_TURING_URL = 'https://work.turing.com';

    /**
     * Permanent remote tracks on work.turing.com for global AI and engineering talent.
     *
     * @var array<int, array<string, mixed>>
     */
    private const WORK_TURING_TRACKS = [
        [
            'id' => 'wt-ai-trainer',
            'title' => 'AI Trainer & LLM Evaluator (Remote)',
            'tags' => ['AI Training', 'LLM Evaluation', 'Prompt Engineering', 'RLHF', 'Remote'],
            'salary' => '$25 - $60/hr',
            'annual_salary_usd' => ['min' => 50000, 'max' => 120000],
            'description' => '<p>Work remotely with <strong>Turing</strong> to train and advance next-generation frontier AI models.</p>'
                .'<p>As an AI Trainer and LLM Evaluator, you will review model outputs, evaluate reasoning chains, craft complex prompts, and perform RLHF (Reinforcement Learning from Human Feedback) across coding, reasoning, and domain-specific knowledge tracks.</p>'
                .'<p><strong>Highlights:</strong></p><ul>'
                .'<li>100% remote work with flexible hours</li>'
                .'<li>Competitive compensation paid via direct bank transfer or international wire</li>'
                .'<li>Direct involvement in cutting-edge generative AI benchmarks</li>'
                .'</ul>'
                .'<p>Apply directly via the official Turing work portal at work.turing.com.</p>',
        ],
        [
            'id' => 'wt-frontier-swe',
            'title' => 'Remote Software Engineer (Python, React, Node, Go)',
            'tags' => ['Python', 'React', 'Node.js', 'Go', 'Full Stack', 'Remote'],
            'salary' => '$50,000 - $120,000/yr',
            'annual_salary_usd' => ['min' => 50000, 'max' => 120000],
            'description' => '<p>Join the <strong>Turing</strong> talent network as a remote software engineer matched with top US and global tech enterprises.</p>'
                .'<p>Turing connects elite software developers with long-term, full-time, or contract remote roles at high-growth Silicon Valley startups and Fortune 500 innovators.</p>'
                .'<p><strong>Requirements:</strong></p><ul>'
                .'<li>3+ years of professional software engineering experience</li>'
                .'<li>Strong proficiency in modern stacks: Python, TypeScript, React, Go, Java, or Node.js</li>'
                .'<li>Solid problem-solving, clean code design, and asynchronous communication skills</li>'
                .'</ul>'
                .'<p>Apply directly via work.turing.com to complete vetting and begin matching.</p>',
        ],
        [
            'id' => 'wt-ai-code-specialist',
            'title' => 'Frontier AI Coding Specialist (Code Generation & Benchmarks)',
            'tags' => ['AI Coding', 'Algorithms', 'Data Structures', 'Python', 'C++', 'Remote'],
            'salary' => '$30 - $75/hr',
            'annual_salary_usd' => ['min' => 60000, 'max' => 150000],
            'description' => '<p>Work directly on code-generation models and reasoning benchmarks with <strong>Turing</strong>.</p>'
                .'<p>In this remote specialist role, you will create rigorous unit tests, solve advanced algorithmic challenges, and audit model-generated code for correctness, security vulnerabilities, and optimal time complexity.</p>'
                .'<p><strong>Ideal Candidate:</strong></p><ul>'
                .'<li>Background in Computer Science, Software Engineering, or Competitive Programming</li>'
                .'<li>Exceptional knowledge of algorithms, data structures, and edge-case testing</li>'
                .'<li>Experience in Python, C++, Java, or Go</li>'
                .'</ul>'
                .'<p>Apply and get verified via work.turing.com.</p>',
        ],
        [
            'id' => 'wt-machine-learning-engineer',
            'title' => 'Remote Machine Learning / NLP Engineer',
            'tags' => ['Machine Learning', 'NLP', 'PyTorch', 'Transformers', 'Remote'],
            'salary' => '$65,000 - $140,000/yr',
            'annual_salary_usd' => ['min' => 65000, 'max' => 140000],
            'description' => '<p>Collaborate with leading AI labs and enterprise teams via <strong>Turing</strong> on production ML pipelines, fine-tuning, and RAG architectures.</p>'
                .'<p>Opportunities include building vector search retrieval, agentic workflows, fine-tuning open-weights models (Llama, Mistral), and deploying scalable inference engines.</p>'
                .'<p>Apply directly via the Turing remote portal at work.turing.com.</p>',
        ],
    ];

    public function fetch(): array
    {
        $jobs = [];

        // 1. Fetch live openings from Turing Greenhouse board
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->get(self::GREENHOUSE_API);

            if ($response->successful()) {
                $ghJobs = $response->json('jobs', []);
                foreach ($ghJobs as $job) {
                    $location = mb_strtolower($job['location']['name'] ?? '');
                    // Include roles marked Remote or Global
                    if (str_contains($location, 'remote') || str_contains($location, 'global')) {
                        $jobs[] = $this->normalizeGreenhouseJob($job);
                    }
                }
            } else {
                Log::warning('[turing] Greenhouse API returned non-200 status', ['status' => $response->status()]);
            }
        } catch (Throwable $e) {
            Log::warning('[turing] Failed fetching greenhouse jobs: '.$e->getMessage());
        }

        // 2. Add work.turing.com remote tracks
        foreach (self::WORK_TURING_TRACKS as $track) {
            $jobs[] = [
                'id' => 'turing--'.$track['id'],
                'source_name' => 'Turing',
                'source_id' => $track['id'],
                'source_url' => self::WORK_TURING_URL,
                'title' => $track['title'],
                'company' => 'Turing',
                'description' => $track['description'],
                'tags' => $track['tags'],
                'location' => 'Worldwide / Remote (Kenya Eligible)',
                'remote_type' => 'Remote',
                'salary' => $track['salary'],
                'annual_salary_usd' => $track['annual_salary_usd'],
                'posted_at' => Carbon::now()->subHours(6),
            ];
        }

        return $jobs;
    }

    private function normalizeGreenhouseJob(array $job): array
    {
        $id = (string) $job['id'];
        $title = trim((string) ($job['title'] ?? 'Remote Role at Turing'));
        $departments = array_map(fn ($d) => $d['name'] ?? '', $job['departments'] ?? []);
        $departments = array_values(array_filter($departments));

        $tags = array_values(array_unique(array_filter([
            ...$departments,
            'Turing',
            'Remote',
            'AI & Tech',
        ])));

        $url = ! empty($job['absolute_url'])
            ? (string) $job['absolute_url']
            : self::WORK_TURING_URL;

        return [
            'id' => "turing--gh-{$id}",
            'source_name' => 'Turing',
            'source_id' => $id,
            'source_url' => $url,
            'title' => $title,
            'company' => 'Turing',
            'description' => (string) ($job['content'] ?? "<p>Apply for {$title} at Turing.</p>"),
            'tags' => $tags,
            'location' => $job['location']['name'] ?? 'Worldwide / Remote',
            'remote_type' => 'Remote',
            'salary' => null,
            'annual_salary_usd' => null,
            'posted_at' => ! empty($job['updated_at']) ? Carbon::parse($job['updated_at']) : Carbon::now(),
        ];
    }
}
