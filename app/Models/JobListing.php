<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * True once PREMIUM_WINDOW_DAYS have passed since posting — every
     * listing opens up free for everyone at that point, credits or not.
     */
    protected function isFree(): Attribute
    {
        return Attribute::get(
            fn () => $this->posted_at->lt(now()->subDays(config('jobs.premium_window_days')))
        );
    }
}
