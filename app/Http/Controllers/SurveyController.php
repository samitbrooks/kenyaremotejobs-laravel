<?php

namespace App\Http\Controllers;

use App\Support\SurveyPlatforms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(Request $request): View
    {
        $category = (string) $request->query('category', 'all');
        $payout = (string) $request->query('payout', 'all');

        $platforms = collect(SurveyPlatforms::all());

        if ($category !== 'all') {
            $platforms = $platforms->where('category', $category);
        }

        if ($payout === 'mpesa') {
            $platforms = $platforms->where('mpesa_compatible', true);
        } elseif ($payout === 'crypto') {
            $platforms = $platforms->filter(fn ($p) => collect($p['payouts'])->some(fn ($m) => str_contains(strtolower($m), 'crypto')));
        } elseif ($payout === 'airtime') {
            $platforms = $platforms->filter(fn ($p) => collect($p['payouts'])->some(fn ($m) => str_contains(strtolower($m), 'airtime')));
        }

        $lastSyncedAt = Cache::get('surveys_last_synced_at', now()->format('F j, Y'));
        $hasAccess = auth()->user()?->hasSurveyAccess() ?? false;

        return view('surveys', [
            'platforms' => $platforms->values()->all(),
            'hasAccess' => $hasAccess,
            'priceKes' => SurveyPlatforms::PRICE_KES,
            'faqs' => SurveyPlatforms::faqs(),
            'itemListJsonLd' => SurveyPlatforms::itemListJsonLd(),
            'faqJsonLd' => SurveyPlatforms::faqJsonLd(),
            'activeCategory' => $category,
            'activePayout' => $payout,
            'lastSyncedAt' => $lastSyncedAt,
        ]);
    }
}
