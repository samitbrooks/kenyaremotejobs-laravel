<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'subscribed', 'subscribed_at', 'marketing_opt_out_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscribed' => 'boolean',
            'subscribed_at' => 'datetime',
            'marketing_opt_out_at' => 'datetime',
        ];
    }

    public function receivesMarketingEmail(): bool
    {
        return $this->marketing_opt_out_at === null;
    }

    public function jobUnlocks(): HasMany
    {
        return $this->hasMany(JobUnlock::class);
    }

    public function creditPurchases(): HasMany
    {
        return $this->hasMany(CreditPurchase::class);
    }

    public function jobPostingPayments(): HasMany
    {
        return $this->hasMany(JobPostingPayment::class);
    }

    public function postedJobs(): HasMany
    {
        return $this->hasMany(JobListing::class, 'posted_by_user_id');
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower($this->email), config('jobs.admin_emails'), true);
    }
}
