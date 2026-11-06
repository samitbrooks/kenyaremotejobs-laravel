@props(['hasProfile' => false, 'savedCount' => 0, 'appliedCount' => 0])
@php
    $steps = [
        ['title' => 'Set your direction', 'action' => 'Choose your skills, experience, and preferred roles.', 'url' => url('/match'), 'done' => $hasProfile],
        ['title' => 'Build a shortlist', 'action' => 'Save roles whose requirements and location wording fit you.', 'url' => route('jobs.index', ['sort' => 'match']), 'done' => $savedCount > 0],
        ['title' => 'Prepare your CV', 'action' => 'Open a shortlisted role and tailor your CV using real experience.', 'url' => route('jobs.index', ['sort' => 'match']), 'done' => false],
        ['title' => 'Submit an application', 'action' => 'Review your documents, apply through the portal, then mark it Applied.', 'url' => url('/account#applications'), 'done' => $appliedCount > 0],
        ['title' => 'Practise your introduction', 'action' => 'Prepare a short explanation of your skills and a real example of your work.', 'url' => url('/resume-builder'), 'done' => false],
        ['title' => 'Review and follow up', 'action' => 'Check application instructions before sending a polite follow-up.', 'url' => url('/account#applications'), 'done' => false],
        ['title' => 'Plan your next week', 'action' => 'Review what you learned and choose your next relevant roles.', 'url' => url('/account#alerts'), 'done' => false],
    ];
@endphp
<section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8" id="search-plan">
    <p class="text-xs font-bold uppercase tracking-wide text-[#3444da]">One useful step each day</p>
    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Your seven-day application plan</h2>
    <p class="mt-2 text-sm text-slate-600">You do not need to do everything today. Start with one suitable role and build from there. This is a suggested pace, not a hiring deadline.</p>
    <ol class="mt-5 divide-y divide-slate-100">
        @foreach ($steps as $step)
            <li class="flex items-start gap-4 py-4">
                <span class="shrink-0 rounded-full bg-[#eef0fe] px-3 py-1 text-xs font-bold text-[#3444da]">Day {{ $loop->iteration }}</span>
                <div>
                    <a href="{{ $step['url'] }}" class="text-sm font-bold text-slate-900 hover:underline">{{ $step['title'] }}</a>
                    @if ($step['done']) <span class="ml-2 text-xs font-semibold text-emerald-700">Done in your account</span> @endif
                    <p class="mt-1 text-sm text-slate-600">{{ $step['action'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>
