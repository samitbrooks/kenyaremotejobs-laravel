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
    'tags', 'location', 'remote_type', 'salary', 'annual_salary_usd', 'posted_at', 'last_seen_at',
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
            'last_seen_at' => 'datetime',
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
     * Shows recent listings and older openings still confirmed in live employer feeds.
     */
    public function scopeVisible(Builder $query): Builder
    {
        $days = config('jobs.listing_days', 30);

        return $query->where(function (Builder $query) use ($days): void {
            $query->where('posted_at', '>=', now()->subDays($days))
                ->orWhere('last_seen_at', '>=', now()->subHours(36));
        });
    }

    public function isDirectAts(): bool
    {
        $url = $this->directApplyUrl();
        if (DirectAtsResolver::isAggregatorUrl($url) || DirectAtsResolver::isHiringPlatformUrl($url)) {
            return false;
        }
        if (in_array($this->origin, ['employer', 'manual'], true)) {
            return filter_var($url, FILTER_VALIDATE_URL) !== false
                && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
        }

        return DirectAtsResolver::isEmployerApplicationUrl($url);
    }

    public function isAggregator(): bool
    {
        return DirectAtsResolver::isAggregatorUrl($this->source_url);
    }

    public function directApplyUrl(): string
    {
        return DirectAtsResolver::resolve($this->source_url, $this->description);
    }

    public function isAccessibleOnlineWork(): bool
    {
        $title = strip_tags($this->title);
        $requirements = strip_tags($this->description ?? '');
        if (preg_match('/\b(senior|sr\.?|lead|principal|staff|director|head of|manager|architect|executive|chief|scientist|engineer|developer|physicist|mathematician|physician|sonographer|coding|quantum|expert)\b/i', $title)) {
            return false;
        }
        if (preg_match('/\b(ph\.?d|doctoral|doctorate|masters? degree|licensed attorney|board.certified)\b|\b(?:[5-9]|1[0-9])\+?\s*(?:years?|yrs?)\s+(?:of\s+)?(?:professional\s+|relevant\s+)?experience\b/i', $requirements)) {
            return false;
        }

        return (bool) preg_match('/\b(annotat(?:or|ion)|data label(?:er|ing)|transcri(?:ber|ption|ptionist)|caption(?:er|ing)|data entry|customer (?:support|service|experience)|chat (?:support|agent)|virtual assistant|administrative assistant|content (?:writer|reviewer|moderator)|proofread(?:er|ing)|translator|translation|search (?:quality |engine )?(?:rater|evaluator)|research assistant|online tutor|ai (?:trainer|tutor)|model evaluator|community moderator|social media assistant|bookkeep(?:er|ing))\b/i', $title);
    }

    public function accessibilityScore(): int
    {
        $score = $this->isAccessibleOnlineWork() ? 100 : 0;
        if (preg_match('/\b(senior|lead|principal|staff|director|head of|architect|chief|expert)\b/i', $this->title)) {
            $score -= 150;
        }
        if (! $this->isAccessibleOnlineWork() && preg_match('/\b(ph\.?d|doctorate|doctoral|masters? degree)\b/i', strip_tags($this->description ?? ''))) {
            $score -= 100;
        }
        if ($this->isDirectAts()) {
            $score += 25;
        }
        if ($this->kenya_friendly) {
            $score += 20;
        }

        return $score;
    }

    public function isCurrentlyVisible(): bool
    {
        return ($this->posted_at && $this->posted_at->gte(now()->subDays(config('jobs.listing_days', 30))))
            || ($this->last_seen_at && $this->last_seen_at->gte(now()->subHours(36)));
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

        $hasTranscription = (bool) preg_match('/\b(transcription|transcriptionist|transcriber|captioner|captioning|subtitling|subtitles)\b/i', $haystack);

        $hasTutoring = (bool) preg_match('/\b(tutor|tutoring|online tutor|academic tutor|curriculum reviewer|academic q&a|teacher|teaching|education practitioner)\b/i', $haystack);
        $hasAnnotation = (bool) preg_match('/\b(data annotation|annotator|annotation|data labeler|data labeling|image annotation|audio annotation|multimodal annotation|lidar|transcription|transcriber|search rater|quality rater)\b/i', $haystack);
        $hasAi = (bool) preg_match('/\b(ai training|ai trainer|ai tutor|ai teaching|prompt evaluator|prompt trainer|prompt specialist|prompt engineering|rlhf|model evaluator|model reviewer|llm evaluation|ai reasoning|ai evaluator|ai agent tester)\b/i', $haystack);
        $hasStudent = (bool) (
            preg_match('/\b(student|campus|intern|internship|campus ambassador|student worker|part-time student|undergraduate|university student|college student)\b/i', $haystack)
            || in_array('students', (array) ($this->audience_segments ?? []), true)
            || str_starts_with((string) ($this->id ?? ''), 'student--')
        );

        if ($hasTranscription) {
            return 'Transcription & Captioning';
        }

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

        if ($hasStudent) {
            return 'Student & Campus Jobs';
        }

        return null;
    }

    /**
     * Returns an eye-catching Telegram HTML highlight badge for emphasized roles.
     */
    public function telegramEmphasisBadge(): ?string
    {
        return match ($this->telegramEmphasisCategory()) {
            'Transcription & Captioning' => '🎧 <b>FOCUS: TRANSCRIPTION &amp; CAPTIONING</b>',
            'AI Tutoring & Model Evaluation' => '🤖 <b>FOCUS: AI TUTORING &amp; MODEL EVALUATION</b>',
            'Online Tutoring & Teaching' => '📚 <b>FOCUS: ONLINE TUTORING &amp; TEACHING</b>',
            'Data Annotation & RLHF' => '🏷️ <b>FOCUS: DATA ANNOTATION &amp; RLHF</b>',
            'AI Teaching & Model Training' => '🤖 <b>FOCUS: AI TEACHING &amp; MODEL TRAINING</b>',
            'Student & Campus Jobs' => '🎓 <b>FOCUS: STUDENT &amp; CAMPUS JOBS</b>',
            default => null,
        };
    }

    /**
     * Returns a punchy value-proposition micro-highlight for Kenyan candidates.
     */
    public function telegramEmphasisHighlight(): ?string
    {
        return match ($this->telegramEmphasisCategory()) {
            'Transcription & Captioning' => '🎧 <i>Explore audio transcription, captions &amp; subtitles • Check each role’s requirements</i>',
            'AI Tutoring & Model Evaluation' => '✨ <i>AI model evaluation • Review subject knowledge and experience requirements</i>',
            'Online Tutoring & Teaching' => '🎓 <i>Share your subject expertise • Flexible hours • Student/campus friendly</i>',
            'Data Annotation & RLHF' => '✨ <i>Annotation work • Check skills, assessment and payment terms</i>',
            'AI Teaching & Model Training' => '🧠 <i>Train frontier LLMs • Prompt evaluation • Flexible hours from Kenya</i>',
            'Student & Campus Jobs' => '🎒 <i>Flexible hours for university &amp; college students • Earn in USD while studying</i>',
            default => null,
        };
    }

    /**
     * Returns a categorized badge for any job in a hybrid Telegram drop.
     */
    public function telegramCategoryBadge(): string
    {
        $badge = $this->telegramEmphasisBadge();
        if ($badge) {
            return $badge;
        }

        $firstTag = ! empty($this->tags) && is_array($this->tags) ? $this->tags[0] : null;
        $tagLabel = $firstTag ? htmlspecialchars(strtoupper($firstTag), ENT_QUOTES | ENT_SUBSTITUTE) : 'GLOBAL REMOTE';

        return "💻 <b>GLOBAL TECH &amp; REMOTE CAREER ({$tagLabel})</b>";
    }

    /**
     * Returns a highlight line for any job in a hybrid Telegram drop.
     */
    public function telegramCategoryHighlight(): string
    {
        $highlight = $this->telegramEmphasisHighlight();
        if ($highlight) {
            return $highlight;
        }

        return '🚀 <i>Remote career role • Review experience, eligibility and stated pay</i>';
    }
}
