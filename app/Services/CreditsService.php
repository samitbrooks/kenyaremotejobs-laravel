<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\JobUnlock;
use App\Models\User;

/**
 * Ported from the Next.js version's creditsRepo.ts. Balance is derived, not
 * stored: credits purchased for a tier minus job unlocks recorded against
 * that tier — never a mutable counter that purchase/spend code has to keep
 * in sync by hand.
 */
class CreditsService
{
    /**
     * @return array<string, array{purchased: int, used: int, remaining: int}>
     */
    public function balances(User $user): array
    {
        $purchased = CreditPurchase::where('user_id', $user->id)
            ->selectRaw('tier, COALESCE(SUM(credits), 0) as total')
            ->groupBy('tier')
            ->pluck('total', 'tier');

        $used = JobUnlock::where('user_id', $user->id)
            ->selectRaw('tier, COUNT(*) as total')
            ->groupBy('tier')
            ->pluck('total', 'tier');

        $balances = [];
        foreach (config('jobs.tiers') as $tier) {
            $purchasedCount = (int) ($purchased[$tier] ?? 0);
            $usedCount = (int) ($used[$tier] ?? 0);
            $balances[$tier] = [
                'purchased' => $purchasedCount,
                'used' => $usedCount,
                'remaining' => max(0, $purchasedCount - $usedCount),
            ];
        }

        return $balances;
    }

    public function hasAvailableCredit(User $user, string $tier): bool
    {
        return $this->balances($user)[$tier]['remaining'] > 0;
    }
}
