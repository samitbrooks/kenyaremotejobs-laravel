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
        $prompt = "Help a Kenyan candidate prepare an honest application. Use only the candidate experience supplied. Never invent achievements, employers, qualifications, or metrics. Use clearly bracketed placeholders for missing evidence. Never reveal the employer name, website, or contact information in output.\n\n"
            ."Job Title: {$job->title}\n"
            ."Employer: Verified Direct Employer\n"
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
            ."3. tailored_bullets: array of 3-5 suggested CV bullets based only on supplied experience; bracket missing facts and never invent achievements\n"
            ."4. tailored_cover_letter: a compelling 3-paragraph cover letter addressed to the hiring team, highlighting timezone readiness (East Africa Time / UTC+3) and proven remote independence.\n"
            ."5. kenya_advantage: a concise 1-sentence interview talking point emphasizing their timezone overlap with Europe/UK/US East mornings.\n"
            .'Return ONLY valid JSON.';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey,
        ])
            ->timeout(15)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
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
            "Used {$primarySkill} to [describe work you actually completed] for [your real project or team].",
            "Applied {$secondarySkill} to [describe a problem you solved], resulting in [an outcome you can support].",
            'Documented [a real process or resolution] so [describe who benefited and how].',
            'Coordinated [a real task or project] with [your team], using [tools you have actually used].',
        ];

        $coverLetter = "Dear Hiring Team,\n\n"
            ."I am applying for the {$title} role. My experience in [your relevant work or project] includes [a concrete example matching a stated requirement].\n\n"
            ."I have used {$primarySkill} to [describe work you actually completed]. [Explain how your skills meet the role requirements and confirm your availability for the stated working hours.]\n\n"
            ."Thank you for considering my application. I would welcome the opportunity to discuss my experience.\n\nKind regards,\n[Your name]";

        return [
            'match_score' => 94,
            'key_requirements' => $keyRequirements,
            'tailored_bullets' => $bullets,
            'tailored_cover_letter' => $coverLetter,
            'kenya_advantage' => 'Based in Nairobi (EAT / UTC+3), offering 4–6 hours of concurrent business overlap with UK/Europe and full morning coverage with US East Coast teams.',
        ];
    }
}
