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

    /**
     * Excludes listings that have aged out — a non-employer listing past
     * premium_window_days, or an employer listing past employer_listing_days
     * — so they stop appearing anywhere on the public site (they still exist
     * for admin, payment history, etc., since nothing outside this scope
     * filters on it). There's no auto-unlock-for-everyone at that point
     * (unlike the old is_free behavior this replaces): a listing you never
     * unlocked simply disappears instead of opening up for free.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where(function ($w) {
            $w->where('origin', 'employer')
                ->where('posted_at', '>=', now()->subDays(config('jobs.employer_listing_days')));
        })->orWhere(function ($w) {
            $w->where('origin', '!=', 'employer')
                ->where('posted_at', '>=', now()->subDays(config('jobs.premium_window_days')));
        });
    }
}
