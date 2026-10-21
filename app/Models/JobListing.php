<?php

namespace App\Models;

use App\Services\DirectAtsResolver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(keyType: 'string', incrementing: false)]
#[Fillable([
    'id', 'source_name', 'source_id', 'source_url', 'title', 'company', 'description',
    'tags', 'location', 'remote_type', 'salary', 'annual_salary_usd', 'posted_at',
    'kenya_friendly', 'kenya_score', 'kenya_reasons', 'origin', 'tier',
    'audience_segments', 'posted_by_user_id',
])]
class JobListing extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'annual_salary_usd' => 'array',
            'posted_at' => 'datetime',
            'kenya_friendly' => 'boolean',
            'kenya_score' => 'integer',
            'kenya_reasons' => 'array',
            'audience_segments' => 'array',
        ];
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_user_id');
    }

    public function unlocks(): HasMany
    {
        return $this->hasMany(JobUnlock::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_listing_id');
    }

    public function isEarlyAccess(): bool
    {
        if ($this->origin === 'employer') {
            return false;
        }

        $hours = config('jobs.early_access_hours', 48);

        return $this->posted_at && $this->posted_at->isAfter(now()->subHours($hours));
    }

    public function earlyAccessHoursRemaining(): int
    {
        if (! $this->isEarlyAccess()) {
            return 0;
        }

        $unlockTime = $this->posted_at->copy()->addHours(config('jobs.early_access_hours', 48));

        return max(1, (int) ceil(now()->diffInRealHours($unlockTime, false)));
    }

    /**
     * Shows all listings posted within the configured listing lifetime (30 days).
     */
    public function scopeVisible(Builder $query): Builder
    {
        $days = config('jobs.listing_days', 30);

        return $query->where('posted_at', '>=', now()->subDays($days));
    }

    public function isDirectAts(): bool
    {
        if (in_array($this->origin, ['employer', 'manual'])) {
            return true;
        }

        return DirectAtsResolver::isDirectUrl($this->source_url);
    }

    public function isAggregator(): bool
    {
        return DirectAtsResolver::isAggregatorUrl($this->source_url);
    }

    public function directApplyUrl(): string
    {
        return DirectAtsResolver::resolve($this->source_url, $this->description);
    }

    /**
     * Compute an accessibility score for homepage prioritization:
     * Heavily favors data annotation, AI training, student/campus, entry-level,
     * transcription, customer support, and writing roles that any random Kenyan or student can get.
     * Heavily penalizes senior, lead, executive, and specialized enterprise roles.
     */
    public function accessibilityScore(): int
    {
        $score = 0;
        $titleLower = strtolower($this->title);
        $tagsLower = array_map('strtolower', $this->tags ?? []);
        $segments = $this->audience_segments ?? [];

        // Highest priority: Curated student / AI / annotation roles
        if (str_starts_with($this->id, 'student--')) {
            $score += 150;
        }

        // Student, highschool leaver, beginner, part-time segments
        if (in_array('students', $segments)) {
            $score += 50;
        }
        if (in_array('highschool', $segments)) {
            $score += 45;
        }
        if (in_array('part-time', $segments)) {
            $score += 30;
        }
        if (in_array('career-changers', $segments)) {
            $score += 25;
        }

        // Specific high-accessibility tags
        foreach ($tagsLower as $tag) {
            if (str_contains($tag, 'data annotation') || str_contains($tag, 'ai training') || str_contains($tag, 'rlhf') || str_contains($tag, 'tutoring') || str_contains($tag, 'tutor') || str_contains($tag, 'ai teaching')) {
                $score += 65;
            } elseif (str_contains($tag, 'transcription') || str_contains($tag, 'label') || str_contains($tag, 'rater') || str_contains($tag, 'prompt') || str_contains($tag, 'teaching')) {
                $score += 40;
            } elseif (str_contains($tag, 'student') || str_contains($tag, 'writing') || str_contains($tag, 'content') || str_contains($tag, 'entry') || str_contains($tag, 'support')) {
                $score += 25;
            }
        }

        // High-accessibility title keywords
        if (preg_match('/\b(data annotation|ai training|annotator|rlhf|prompt|model evaluator|model reviewer|ai tutor|ai teaching|tutor|tutoring)\b/i', $titleLower)) {
            $score += 80;
        } elseif (preg_match('/\b(transcription|transcriber|captioner|rater|evaluator|labeler|data entry|teaching|teacher|curriculum)\b/i', $titleLower)) {
            $score += 50;
        } elseif (preg_match('/\b(virtual assistant|copywriter|content writer|writer|customer support|support specialist|chat agent|intern|junior|entry level)\b/i', $titleLower)) {
            $score += 30;
        }

        // Heavy penalty for senior, lead, staff, principal, director, vp, architect, manager bigwig roles
        if (preg_match('/\b(senior|sr\.|lead|principal|staff|director|vp|vice president|head of|manager|architect|executive|cfo|cto|coo|chief)\b/i', $titleLower)) {
            $score -= 120;
        }

        return $score;
    }

    /**
     * Checks if this job listing belongs to an emphasized remote domain:
     * Data Annotation, Online Tutoring, or AI Teaching / Model Training.
     */
    public function isTelegramEmphasized(): bool
    {
        return $this->telegramEmphasisCategory() !== null;
    }

    /**
     * Identifies the primary emphasis category for Telegram broadcasts and alerts.
     */
    public function telegramEmphasisCategory(): ?string
    {
        $haystack = mb_strtolower($this->title.' '.implode(' ', (array) ($this->tags ?? [])));

        $hasTutoring = (bool) preg_match('/\b(tutor|tutoring|online tutor|academic tutor|curriculum reviewer|academic q&a|teacher|teaching|education practitioner)\b/i', $haystack);
        $hasAnnotation = (bool) preg_match('/\b(data annotation|annotator|annotation|data labeler|data labeling|image annotation|audio annotation|multimodal annotation|lidar|transcription|transcriber|search rater|quality rater)\b/i', $haystack);
        $hasAi = (bool) preg_match('/\b(ai training|ai trainer|ai tutor|ai teaching|prompt evaluator|prompt trainer|prompt specialist|prompt engineering|rlhf|model evaluator|model reviewer|llm evaluation|ai reasoning|ai evaluator|ai agent tester)\b/i', $haystack);

        if ($hasAi && $hasTutoring) {
            return 'AI Tutoring & Model Evaluation';
        }

        if ($hasTutoring) {
            return 'Online Tutoring & Teaching';
        }

        if ($hasAnnotation) {
            return 'Data Annotation & RLHF';
        }

        if ($hasAi) {
            return 'AI Teaching & Model Training';
        }

        return null;
    }

    /**
     * Returns an eye-catching Telegram HTML highlight badge for emphasized roles.
     */
    public function telegramEmphasisBadge(): ?string
    {
        return match ($this->telegramEmphasisCategory()) {
            'AI Tutoring & Model Evaluation' => '🤖 <b>FOCUS: AI TUTORING &amp; MODEL EVALUATION</b>',
            'Online Tutoring & Teaching' => '📚 <b>FOCUS: ONLINE TUTORING &amp; TEACHING</b>',
            'Data Annotation & RLHF' => '🏷️ <b>FOCUS: DATA ANNOTATION &amp; RLHF</b>',
            'AI Teaching & Model Training' => '🤖 <b>FOCUS: AI TEACHING &amp; MODEL TRAINING</b>',
            default => null,
        };
    }

    /**
     * Returns a punchy value-proposition micro-highlight for Kenyan candidates.
     */
    public function telegramEmphasisHighlight(): ?string
    {
        return match ($this->telegramEmphasisCategory()) {
            'AI Tutoring & Model Evaluation' => '✨ <i>Train AI models with your academic reasoning skills • High USD pay</i>',
            'Online Tutoring & Teaching' => '🎓 <i>Share your subject expertise • Flexible hours • Student/campus friendly</i>',
            'Data Annotation & RLHF' => '✨ <i>No degree required • Weekly USD/M-Pesa payouts • Work from home</i>',
            'AI Teaching & Model Training' => '🧠 <i>Train frontier LLMs • Prompt evaluation • Flexible hours from Kenya</i>',
            default => null,
        };
    }
}
