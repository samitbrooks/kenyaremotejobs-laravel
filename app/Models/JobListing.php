<?php

namespace App\Models;

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
}
