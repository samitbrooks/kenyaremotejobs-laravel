<?php

namespace App\Models;

use Database\Factories\JobPostingPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'user_id', 'job_listing_id', 'plan', 'amount_kes', 'posted_at'])]
class JobPostingPayment extends Model
{
    /** @use HasFactory<JobPostingPaymentFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return ['posted_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }
}
