<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

#[Fillable(['name', 'email', 'password', 'referral_code', 'referred_by_id', 'subscribed', 'subscribed_at', 'survey_pass_purchased_at', 'trial_started_at', 'trial_ends_at', 'pro_access_until', 'marketing_opt_out_at', 'last_job_digest_at', 'follow_up_sent_at', 'free_tailors_remaining'])]
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
            'survey_pass_purchased_at' => 'datetime',
            'trial_started_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'pro_access_until' => 'datetime',
            'referred_by_id' => 'integer',
            'marketing_opt_out_at' => 'datetime',
            'last_job_digest_at' => 'datetime',
            'follow_up_sent_at' => 'datetime',
            'free_tailors_remaining' => 'integer',
        ];
    }

    public function hasReceivedFollowUp(): bool
    {
        return $this->follow_up_sent_at !== null;
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function hasUsedTrial(): bool
    {
        return $this->trial_started_at !== null;
    }

    public function trialRemainingHours(): int
    {
        if (! $this->onTrial()) {
            return 0;
        }

        return max(0, (int) now()->diffInHours($this->trial_ends_at, false));
    }

    public function trialRemainingHuman(): string
    {
        if (! $this->onTrial()) {
            return 'Expired';
        }

        $hours = (int) now()->diffInHours($this->trial_ends_at, false);
        if ($hours >= 1) {
            return $hours.' '.($hours === 1 ? 'hour' : 'hours');
        }

        $minutes = max(1, (int) now()->diffInMinutes($this->trial_ends_at, false));

        return $minutes.' '.($minutes === 1 ? 'minute' : 'minutes');
    }

    public function hasReferralProAccess(): bool
    {
        return $this->pro_access_until !== null && $this->pro_access_until->isFuture();
    }

    public function hasActiveAccess(): bool
    {
        return $this->isAdmin()
            || $this->subscribed
            || $this->onTrial()
            || $this->hasReferralProAccess();
    }

    public function hasSurveyAccess(): bool
    {
        return $this->isAdmin()
            || $this->subscribed
            || $this->onTrial()
            || $this->hasReferralProAccess()
            || $this->survey_pass_purchased_at !== null;
    }

    public function startFreeTrial(int $hours = 24): bool
    {
        if ($this->hasUsedTrial() && ! $this->isAdmin()) {
            return false;
        }

        $this->forceFill([
            'trial_started_at' => now(),
            'trial_ends_at' => now()->addHours($hours),
        ])->save();

        return true;
    }

    public function firstName(): string
    {
        $name = trim($this->name ?? '');
        if ($name !== '') {
            $parts = preg_split('/\s+/', $name);

            return $parts[0] ?? $name;
        }

        $emailParts = explode('@', (string) $this->email);

        return ucwords(str_replace(['.', '_', '-'], ' ', $emailParts[0] ?? 'there'));
    }

    public function receivesMarketingEmail(): bool
    {
        return $this->marketing_opt_out_at === null;
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function canUseAiTailor(): bool
    {
        return $this->subscribed || ($this->free_tailors_remaining ?? 0) > 0;
    }

    public function useAiTailor(): void
    {
        if (! $this->subscribed && ($this->free_tailors_remaining ?? 0) > 0) {
            $this->decrement('free_tailors_remaining');
        }
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

    public function isConfiguredAdmin(): bool
    {
        return in_array(strtolower($this->email), config('jobs.admin_emails', []), true);
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower($this->email), config('jobs.admin_emails', []), true);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function receivedReferral(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    public function referralCode(): string
    {
        if (! empty($this->referral_code)) {
            return $this->referral_code;
        }

        $code = 'KRJ-'.strtoupper(substr(md5($this->id.'-'.$this->email.'-kenyaremotejobs'), 0, 7));
        $this->forceFill(['referral_code' => $code])->save();

        return $code;
    }

    public function referralLink(): string
    {
        return url('/?ref='.$this->referralCode());
    }

    public function whatsappShareUrl(): string
    {
        $message = "Hey! Check out Kenya Remote Jobs for high-paying remote roles verified for East Africa & direct employer links. Sign up with my link to get free Pro trial access and AI CV tailor credits:\n".$this->referralLink();

        return 'https://wa.me/?text='.rawurlencode($message);
    }

    public function twitterShareUrl(): string
    {
        $text = 'Find high-paying remote roles open to East Africa & direct employer links on Kenya Remote Jobs. Join with my link for free Pro trial + AI CV tailors: '.$this->referralLink();

        return 'https://twitter.com/intent/tweet?text='.rawurlencode($text);
    }

    public function telegramShareUrl(): string
    {
        $text = 'Find high-paying remote roles open to Kenya & East Africa on Kenya Remote Jobs. Join with my link for free Pro trial + AI CV tailors: '.$this->referralLink();

        return 'https://t.me/share/url?url='.rawurlencode($this->referralLink()).'&text='.rawurlencode($text);
    }

    public function linkedinShareUrl(): string
    {
        return 'https://www.linkedin.com/sharing/share-offsite/?url='.rawurlencode($this->referralLink());
    }

    public function extendProAccess(int $days): Carbon
    {
        $base = ($this->pro_access_until && $this->pro_access_until->isFuture())
            ? $this->pro_access_until
            : Carbon::now();

        $newExpiry = $base->copy()->addDays($days);
        $this->forceFill(['pro_access_until' => $newExpiry])->save();

        return $newExpiry;
    }

    public function addFreeTailors(int $count): void
    {
        $this->increment('free_tailors_remaining', $count);
    }

    public function earnedProDays(): int
    {
        $referrals = $this->referrals()->get();
        $days = 0;

        foreach ($referrals as $ref) {
            if ($ref->trial_reward_applied) {
                $days += 3;
            }
            if ($ref->conversion_reward_applied) {
                $days += 14;
            }
        }

        return $days;
    }
}
