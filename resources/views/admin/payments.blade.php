@php
    $purposeLabels = [
        'credit_package' => 'Credit package',
        'subscription' => 'Subscription',
        'employer_job_post' => 'Employer job post',
    ];
    $statusStyles = [
        'completed' => 'bg-emerald-100 text-emerald-800',
        'pending' => 'bg-sunrise-100 text-sunrise-800',
        'failed' => 'bg-red-100 text-red-700',
    ];
@endphp

<x-layouts.admin title="Admin: Payments">
    <h1 class="text-2xl font-bold">Payments</h1>
    <p class="mt-1 text-sm text-foreground/50">{{ $payments->total() }} total &middot; credit packages, subscriptions, and employer job posts, across every gateway.</p>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-black/5 bg-white shadow-sm">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-black/10 text-foreground/50">
                    <th class="p-4 font-medium">User</th>
                    <th class="p-4 font-medium">Purpose</th>
                    <th class="p-4 font-medium">Gateway</th>
                    <th class="p-4 font-medium">Amount</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr class="border-b border-black/5 last:border-0">
                        <td class="p-4">
                            @if ($payment->user)
                                <a href="{{ url('/admin/users/'.$payment->user->id) }}" class="hover:underline">{{ $payment->user->email }}</a>
                            @else
                                <span class="text-foreground/40">User removed</span>
                            @endif
                        </td>
                        <td class="p-4">{{ $purposeLabels[$payment->purpose] ?? $payment->purpose }}</td>
                        <td class="p-4 text-foreground/50">{{ ucfirst($payment->gateway) }}</td>
                        <td class="p-4 tabular-nums">KES {{ number_format($payment->amount_kes) }}</td>
                        <td class="p-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$payment->status] ?? 'bg-black/5 text-foreground/60' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="p-4 text-foreground/50">{{ \App\Support\Format::timeAgo($payment->created_at) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-foreground/50">No payments yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payments->hasPages())
        <div class="mt-4 flex items-center justify-center gap-4 text-sm">
            @if ($payments->onFirstPage())
                <span class="text-foreground/30">&larr; Prev</span>
            @else
                <a href="{{ $payments->previousPageUrl() }}" class="font-semibold text-sunrise-600 hover:underline">&larr; Prev</a>
            @endif
            <span class="text-foreground/50">Page {{ $payments->currentPage() }} of {{ $payments->lastPage() }}</span>
            @if ($payments->hasMorePages())
                <a href="{{ $payments->nextPageUrl() }}" class="font-semibold text-sunrise-600 hover:underline">Next &rarr;</a>
            @else
                <span class="text-foreground/30">Next &rarr;</span>
            @endif
        </div>
    @endif
</x-layouts.admin>
