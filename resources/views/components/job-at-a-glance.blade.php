@props(['job', 'unlocked', 'priceKes'])

@php
    $rows = [
        ['label' => 'Location', 'value' => $job->location],
        ['label' => 'Remote type', 'value' => $job->remote_type],
    ];
    if ($job->salary) {
        $rows[] = ['label' => 'Salary', 'value' => $job->salary];
    }
    $rows[] = ['label' => 'Posted', 'value' => \App\Support\Format::timeAgo($job->posted_at)];
    $rows[] = [
        'label' => 'Access',
        'value' => $job->origin === 'employer'
            ? 'Direct listing — always open'
            : (config('jobs.tier_labels')[$job->tier] ?? $job->tier).' · from KES '.number_format($priceKes),
    ];
    if ($unlocked) {
        $rows[] = ['label' => 'Source', 'value' => $job->source_name];
    }
@endphp

<div class="overflow-hidden rounded-2xl border border-black/5 bg-white shadow-sm">
    <table class="w-full text-sm">
        <caption class="border-b border-black/5 bg-horizon-50/60 px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-foreground/50">
            Job at a glance
        </caption>
        <tbody>
            @foreach ($rows as $i => $row)
                <tr @class(['bg-horizon-50/40' => $i % 2 === 1])>
                    <th scope="row" class="w-2/5 px-5 py-2.5 text-left font-medium text-foreground/50">{{ $row['label'] }}</th>
                    <td class="px-5 py-2.5 text-foreground/80">{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
