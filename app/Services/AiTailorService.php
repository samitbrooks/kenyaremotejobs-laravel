<?php

namespace App\Services;

use App\Models\JobListing;
use App\Support\Format;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiTailorService
{
    /**
     * @param  array{skills?: array<int, string>, roles?: array<int, string>, yearsExperience?: int}|null  $profile
     * @return array{match_score: int, key_requirements: array<int, string>, tailored_bullets: array<int, string>, tailored_cover_letter: string, kenya_advantage: string}
     */
    public function tailor(JobListing $job, ?array $profile = null, ?string $userExperience = null): array
    {
        $geminiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');

        if ($geminiKey) {
            try {
                $result = $this->callGemini($geminiKey, $job, $profile, $userExperience);
                if ($result) {
                    return $result;
                }
            } catch (Throwable $e) {
                Log::warning('[ai-tailor] Gemini API call failed, falling back to local generator', ['error' => $e->getMessage()]);
            }
        }

        return $this->generateLocalTailoring($job, $profile, $userExperience);
    }

    private function callGemini(string $apiKey, JobListing $job, ?array $profile, ?string $userExperience): ?array
    {
        $prompt = "You are an elite remote career coach and ATS optimization specialist helping a top-tier Kenyan candidate apply for a global remote job.\n\n"
            ."Job Title: {$job->title}\n"
            ."Company: {$job->company}\n"
            ."Location: {$job->location} (Remote: {$job->remote_type})\n"
            .'Job Tags: '.implode(', ', $job->tags ?? [])."\n"
            ."Job Description:\n".Format::stripHtml(mb_substr($job->description, 0, 3000))."\n\n"
            .'Candidate Skills: '.implode(', ', $profile['skills'] ?? ['Remote Communication', 'Problem Solving', 'Project Execution'])."\n"
            .'Candidate Roles: '.implode(', ', $profile['roles'] ?? [$job->title])."\n"
            .'Years Experience: '.($profile['yearsExperience'] ?? 3)."\n"
            .($userExperience ? "Additional Experience Notes: {$userExperience}\n" : '')
            ."\nProvide a JSON response with these exact keys:\n"
            ."1. match_score: integer between 90 and 97 (showing the boosted ATS score after tailoring)\n"
            ."2. key_requirements: array of 4-6 concise keywords/requirements extracted from the job\n"
            ."3. tailored_bullets: array of 3-5 high-impact, action-oriented resume bullet points highlighting relevant achievements matching this role\n"
            ."4. tailored_cover_letter: a compelling 3-paragraph cover letter specifically addressed to {$job->company} hiring team, highlighting timezone readiness (East Africa Time / UTC+3) and proven remote independence.\n"
            ."5. kenya_advantage: a concise 1-sentence interview talking point emphasizing their timezone overlap with Europe/UK/US East mornings.\n"
            .'Return ONLY valid JSON.';

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(15)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.4,
                ],
            ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text');
            $parsed = json_decode($text, true);
            if (is_array($parsed) && isset($parsed['tailored_bullets'], $parsed['tailored_cover_letter'])) {
                return [
                    'match_score' => (int) ($parsed['match_score'] ?? 94),
                    'key_requirements' => (array) ($parsed['key_requirements'] ?? []),
                    'tailored_bullets' => (array) ($parsed['tailored_bullets'] ?? []),
                    'tailored_cover_letter' => (string) ($parsed['tailored_cover_letter'] ?? ''),
                    'kenya_advantage' => (string) ($parsed['kenya_advantage'] ?? ''),
                ];
            }
        }

        return null;
    }

    private function generateLocalTailoring(JobListing $job, ?array $profile, ?string $userExperience): array
    {
        $title = $job->title;
        $company = $job->company;
        $tags = $job->tags ?: ['Remote Collaboration', 'Cross-functional Communication', 'Execution'];
        $skills = $profile['skills'] ?? array_slice($tags, 0, 4);

        $primarySkill = $skills[0] ?? ($tags[0] ?? 'relevant tools');
        $secondarySkill = $skills[1] ?? ($tags[1] ?? 'asynchronous workflows');

        $keyRequirements = array_slice(array_unique(array_merge($tags, [
            'Asynchronous communication & documentation',
            'Timezone-flexible collaboration',
            'Independent problem resolution',
        ])), 0, 5);

        $bullets = [
            "Spearheaded remote delivery of high-priority {$title} initiatives, leveraging {$primarySkill} to increase team execution efficiency by 30%.",
            "Partnered across global cross-functional stakeholders using {$secondarySkill} and structured async updates, maintaining 99%+ on-time milestone delivery.",
            'Optimized daily workflows for East Africa Time (UTC+3) cross-timezone overlap, ensuring seamless handoffs between European and North American teams.',
            'Architected proactive documentation and standard operating procedures that reduced onboarding friction and accelerated sprint velocity.',
        ];

        $coverLetter = "Dear {$company} Hiring Team,\n\n"
            ."I am writing to express my strong interest in the {$title} role. Having followed {$company}'s trajectory, I am deeply impressed by your commitment to high-impact distributed teams. With hands-on experience in ".implode(', ', array_slice($tags, 0, 3)).", I am confident in my ability to hit the ground running and deliver immediate value to your organization.\n\n"
            ."In my previous remote engagements, I have built a track record of driving results autonomously while maintaining clear, transparent asynchronous communication. Operating from Kenya on East Africa Time (EAT / UTC+3), I offer seamless workday overlap with both European afternoon cycles and US Eastern morning standups, allowing for real-time collaboration alongside deep focused work.\n\n"
            ."I welcome the opportunity to discuss how my background in {$primarySkill} and disciplined remote execution align with {$company}'s upcoming milestones. Thank you for your time and consideration.\n\n"
            ."Warm regards,\nCandidate";

        return [
            'match_score' => 94,
            'key_requirements' => $keyRequirements,
            'tailored_bullets' => $bullets,
            'tailored_cover_letter' => $coverLetter,
            'kenya_advantage' => 'Based in Nairobi (EAT / UTC+3), offering 4–6 hours of concurrent business overlap with UK/Europe and full morning coverage with US East Coast teams.',
        ];
    }
}
