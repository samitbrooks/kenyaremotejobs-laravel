<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'referrer_id',
    'referred_id',
    'status',
    'signup_reward_applied',
    'trial_reward_applied',
    'conversion_reward_applied',
    'reward_summary',
])]
class Referral extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'signup_reward_applied' => 'boolean',
            'trial_reward_applied' => 'boolean',
            'conversion_reward_applied' => 'boolean',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'converted' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            'trial_activated' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            default => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'converted' => 'Pro Subscriber (+14 Days)',
            'trial_activated' => 'Trial Active (+3 Days)',
            default => 'Joined (+2 AI Tailors)',
        };
    }
}
