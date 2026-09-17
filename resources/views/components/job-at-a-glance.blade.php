@props(['job', 'canApply' => true])

@php
    $rows = [
        ['label' => 'Company', 'value' => $job->company],
        ['label' => 'Location', 'value' => $job->location],
        ['label' => 'Remote type', 'value' => $job->remote_type],
    ];
    if ($job->salary) {
        $rows[] = ['label' => 'Salary', 'value' => $job->salary];
    }
    $rows[] = ['label' => 'Posted', 'value' => \App\Support\Format::timeAgo($job->posted_at)];

    if ($job->origin === 'employer') {
        $rows[] = ['label' => 'Application Status', 'value' => '🇰🇪 Direct Employer (Seeking Kenyan Talent — Pro Exclusive)'];
    } elseif ($job->isEarlyAccess()) {
        $rows[] = ['label' => 'Application Status', 'value' => '⚡ 48-Hour Early Access Window (Pro Members)'];
    } else {
        $rows[] = ['label' => 'Application Status', 'value' => 'Public listing — Open to all'];
    }

    if ($job->origin === 'employer') {
        $rows[] = ['label' => 'Vetting', 'value' => 'Verified Direct Employer (Seeking Kenyan Talent)'];
    } elseif ($job->kenya_friendly) {
        $rows[] = ['label' => 'Vetting', 'value' => 'Verified Kenya-Friendly Match (EAT Timezone & Visa Cleared)'];
    }
@endphp

<div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
    <table class="w-full text-sm">
        <caption class="border-b border-slate-200/70 bg-slate-50/80 px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-700">
            Job at a glance
        </caption>
        <tbody class="divide-y divide-slate-100">
            @foreach ($rows as $i => $row)
                <tr @class(['bg-slate-50/40' => $i % 2 === 1])>
                    <th scope="row" class="w-2/5 px-6 py-3 text-left font-medium text-slate-500">{{ $row['label'] }}</th>
                    <td class="px-6 py-3 text-slate-800 font-medium">{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
