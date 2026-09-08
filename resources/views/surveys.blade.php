<x-layouts.app
    title="Earn While You Search — Survey & Side-Income Platforms"
    description="A curated directory of established paid-survey and get-paid-to platforms — a free side-income supplement for Kenyan jobseekers while you search for a remote role."
>
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        <div class="mb-8 rounded-2xl border-2 border-sunrise-300 bg-sunrise-50 p-5 text-center">
            <p class="flex items-center justify-center gap-2 font-semibold text-sunrise-900">
                <x-icon name="warning" class="h-5 w-5 shrink-0" />
                Legitimate survey platforms never charge a fee to join. Never share your M-Pesa PIN, password, or banking details with a survey site.
            </p>
        </div>

        <h1 class="flex items-center gap-2 text-3xl font-bold">
            Earn while you search <x-icon name="bulb" class="h-7 w-7 text-sunrise-500" />
        </h1>
        <p class="mt-2 max-w-2xl text-foreground/60">
            A curated directory of established paid-survey and get-paid-to platforms &mdash; completely free, as a side-income supplement while you look for your next remote role. This is not hosted content; every card links out to a third-party site.
        </p>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (\App\Support\SurveyPlatforms::LIST as $i => $platform)
                <x-reveal :delay="min($i, 8) * 60">
                    <div class="flex h-full flex-col gap-3 rounded-2xl border border-black/5 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                        <h2 class="font-semibold">{{ $platform['name'] }}</h2>
                        <p class="flex-1 text-sm text-foreground/60">{{ $platform['description'] }}</p>

                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($platform['payouts'] as $payout)
                                <span class="rounded-full bg-horizon-100 px-2.5 py-1 text-xs font-medium text-horizon-800">{{ $payout }}</span>
                            @endforeach
                        </div>

                        <a href="{{ $platform['url'] }}" target="_blank" rel="noopener noreferrer" class="btn-pop rounded-full bg-sunrise-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-sunrise-600">
                            Visit {{ $platform['name'] }} &#8599;
                        </a>

                        <p class="text-[11px] text-foreground/40">
                            Third-party platform &mdash; KenyaRemoteJobs is not responsible for their payouts.
                        </p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </div>
</x-layouts.app>
