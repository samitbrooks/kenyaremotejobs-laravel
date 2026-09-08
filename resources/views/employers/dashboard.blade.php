<x-layouts.app title="Employer Dashboard">
    @if (! $user)
        <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
            <h1 class="mb-2 text-center text-3xl font-bold">Employer dashboard</h1>
            <p class="mb-8 text-center text-foreground/60">Log in to see your postings.</p>
            <livewire:auth-forms :redirect-to="$redirectTo" />
        </div>
    @else
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold">Your postings</h1>
                <a href="{{ url('/employers/post') }}" class="btn-pop rounded-full gradient-sunrise px-5 py-2 text-sm font-semibold text-white shadow-md">
                    + Post a job
                </a>
            </div>

            @if ($jobs->isEmpty())
                <p class="mt-8 rounded-2xl bg-horizon-50 p-6 text-center text-foreground/60">
                    You haven&rsquo;t posted a role yet.
                </p>
            @else
                <div class="mt-6 overflow-x-auto rounded-2xl border border-black/5 bg-white shadow-sm">
                    <table class="w-full min-w-[600px] border-collapse text-left text-sm">
                        <thead>
                            <tr class="border-b border-black/10 text-foreground/50">
                                <th class="p-4 font-medium">Title</th>
                                <th class="p-4 font-medium">Posted</th>
                                <th class="p-4 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobs as $job)
                                <tr class="border-b border-black/5 last:border-0">
                                    <td class="p-4">
                                        <a href="{{ url('/jobs/'.$job->id) }}" class="font-semibold hover:underline">{{ $job->title }}</a>
                                        <p class="text-foreground/50">{{ $job->company }}</p>
                                    </td>
                                    <td class="p-4 text-foreground/50">{{ \App\Support\Format::timeAgo($job->posted_at) }}</td>
                                    <td class="p-4 text-right">
                                        <form
                                            method="POST"
                                            action="{{ url('/employers/jobs/'.$job->id.'/remove') }}"
                                            onsubmit="return confirm('Take this listing down?')"
                                        >
                                            @csrf
                                            <button type="submit" class="btn-pop rounded-full border border-red-200 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                Take down
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-10 rounded-2xl border border-black/5 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Billing history</h2>
                @if ($payments->isEmpty())
                    <p class="mt-2 text-sm text-foreground/50">No payments yet.</p>
                @else
                    <ul class="mt-3 divide-y divide-black/5">
                        @foreach ($payments as $p)
                            <li class="flex items-center justify-between py-2 text-sm">
                                <span>{{ $p->jobListing->title ?? 'Listing removed' }}</span>
                                <span class="text-xs text-foreground/50">
                                    {{ config('jobs.posting_plans')[$p->plan]['label'] ?? $p->plan }} &middot; KES {{ number_format($p->amount_kes) }} &middot; {{ \App\Support\Format::timeAgo($p->posted_at) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
