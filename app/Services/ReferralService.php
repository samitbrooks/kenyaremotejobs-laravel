<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    public const COOKIE_NAME = 'krj_ref';

    public const COOKIE_LIFETIME_MINUTES = 43200; // 30 days

    public function findReferrerByCode(?string $code, ?int $excludeUserId = null): ?User
    {
        if (! $code) {
            return null;
        }

        $clean = strtoupper(trim($code));

        $user = User::whereRaw('UPPER(referral_code) = ?', [$clean])->first();

        if ($user && $excludeUserId && $user->id === $excludeUserId) {
            return null;
        }

        return $user;
    }

    public function processSignup(User $newUser, ?string $code = null, ?Request $request = null): ?Referral
    {
        // Avoid duplicate referral processing
        if ($newUser->referred_by_id || Referral::where('referred_id', $newUser->id)->exists()) {
            return Referral::where('referred_id', $newUser->id)->first();
        }

        if (! $code && $request) {
            $code = $request->cookie(self::COOKIE_NAME) ?? $request->session()->get(self::COOKIE_NAME);
        }

        if (! $code) {
            return null;
        }

        $referrer = $this->findReferrerByCode($code, $newUser->id);
        if (! $referrer) {
            return null;
        }

        // Link referrer on user
        $newUser->forceFill(['referred_by_id' => $referrer->id])->save();

        // Create Referral model
        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'status' => 'signed_up',
            'signup_reward_applied' => true,
            'trial_reward_applied' => false,
            'conversion_reward_applied' => false,
            'reward_summary' => 'Signup Bonus: +2 AI CV Tailor credits awarded to both users.',
        ]);

        // Award signup rewards: 2 free AI tailors to both referrer and new user
        $referrer->addFreeTailors(2);
        $newUser->addFreeTailors(2);

        Log::info('[referral] New user registered via referral', [
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_code' => $code,
        ]);

        return $referral;
    }

    public function processTrialActivation(User $user): void
    {
        $referral = Referral::where('referred_id', $user->id)->first();
        if (! $referral || $referral->trial_reward_applied) {
            return;
        }

        $referrer = $referral->referrer;
        if (! $referrer) {
            return;
        }

        // Award +3 days Pro access and +1 bonus AI tailor to referrer
        $referrer->extendProAccess(3);
        $referrer->addFreeTailors(1);

        $referral->update([
            'status' => 'trial_activated',
            'trial_reward_applied' => true,
            'reward_summary' => trim($referral->reward_summary."\nTrial Bonus: +3 Days Pro access & +1 AI Tailor awarded to referrer."),
        ]);

        Log::info('[referral] Trial activated bonus awarded', [
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
        ]);
    }

    public function processSubscriptionPayment(User $user): void
    {
        $referral = Referral::where('referred_id', $user->id)->first();
        if (! $referral || $referral->conversion_reward_applied) {
            return;
        }

        $referrer = $referral->referrer;
        if (! $referrer) {
            return;
        }

        // Award +14 days Pro access and +5 bonus AI tailors to referrer
        $referrer->extendProAccess(14);
        $referrer->addFreeTailors(5);

        $referral->update([
            'status' => 'converted',
            'conversion_reward_applied' => true,
            'reward_summary' => trim($referral->reward_summary."\nPro Conversion: +14 Days Pro access & +5 AI Tailors awarded to referrer."),
        ]);

        Log::info('[referral] Pro subscription conversion bonus awarded', [
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
        ]);
    }

    /**
     * @return array{
     *     total_referrals: int,
     *     trial_activated_count: int,
     *     converted_count: int,
     *     earned_pro_days: int,
     *     earned_ai_tailors: int,
     *     referrals: array<int, array{masked_email: string, name: string, status: string, status_badge: string, status_label: string, joined_at: string}>
     * }
     */
    public function getStats(User $user): array
    {
        $referrals = Referral::with('referred')
            ->where('referrer_id', $user->id)
            ->latest()
            ->get();

        $total = $referrals->count();
        $trialCount = $referrals->where('trial_reward_applied', true)->count();
        $convertedCount = $referrals->where('conversion_reward_applied', true)->count();

        $earnedProDays = ($trialCount * 3) + ($convertedCount * 14);
        $earnedAiTailors = ($total * 2) + ($trialCount * 1) + ($convertedCount * 5);

        $list = $referrals->map(function (Referral $ref) {
            $email = $ref->referred?->email ?? 'friend@example.com';
            $parts = explode('@', $email);
            $namePart = $parts[0] ?? '';
            $domainPart = $parts[1] ?? 'email.com';
            $masked = (strlen($namePart) > 2 ? substr($namePart, 0, 2).'***' : substr($namePart, 0, 1).'***').'@'.$domainPart;

            return [
                'masked_email' => $masked,
                'name' => $ref->referred?->firstName() ?? 'Friend',
                'status' => $ref->status,
                'status_badge' => $ref->statusBadge(),
                'status_label' => $ref->statusLabel(),
                'joined_at' => $ref->created_at?->diffForHumans() ?? 'Recently',
            ];
        })->all();

        return [
            'total_referrals' => $total,
            'trial_activated_count' => $trialCount,
            'converted_count' => $convertedCount,
            'earned_pro_days' => $earnedProDays,
            'earned_ai_tailors' => $earnedAiTailors,
            'referrals' => $list,
        ];
    }
}
