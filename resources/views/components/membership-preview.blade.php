<section id="membership-preview" class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
    <p class="text-xs font-bold uppercase tracking-wide text-[#3444da]">Preview the tools before you join</p>
    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Turn a role you like into an application you can send</h2>
    <p class="mt-3 text-sm text-slate-600">Illustrative customer support example. Suggestions should describe your real experience; no tool can guarantee an interview.</p>
    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-4">
            <h3 class="font-bold text-slate-900">Your starting CV bullet</h3>
            <p class="mt-2 text-sm text-slate-600">“Helped customers with questions and kept records.”</p>
        </div>
        <div class="rounded-2xl border border-[#c7ccfb] bg-[#eef0fe] p-4">
            <h3 class="font-bold text-slate-900">A clearer, role-focused version</h3>
            <p class="mt-2 text-sm text-slate-700">“Responded to customer questions, documented resolutions, and escalated unresolved issues to the appropriate team.”</p>
        </div>
    </div>
    <details class="mt-4 rounded-2xl border border-slate-200 p-4">
        <summary class="cursor-pointer text-sm font-bold text-[#3444da]">See a cover-letter opening and application tracker example</summary>
        <p class="mt-3 text-sm text-slate-600">“Dear Hiring Team, I am applying for the remote customer support role. My experience answering customer questions and documenting resolutions aligns with the communication and record-keeping requirements in your listing.”</p>
        <ol class="mt-4 flex flex-wrap gap-3 text-sm text-slate-700" aria-label="Example application stages">
            <li>1. Saved</li><li>2. Applied</li><li>3. Interviewing</li><li>4. Offered</li>
        </ol>
        <p class="mt-3 text-xs text-slate-500">Example stages only. You update your tracker as your application progresses. Employer identities stay hidden on the site.</p>
    </details>
    <div class="mt-5 flex flex-wrap gap-4 text-sm font-bold text-[#3444da]">
        <a href="{{ url('/match') }}">Find roles matching my skills &rarr;</a>
        @if (! auth()->user()?->hasActiveAccess() && ! auth()->user()?->hasUsedTrial())
            <a href="{{ route('trial.activate') }}">Try the workflow with a 24-hour free pass &rarr;</a>
        @else
            <a href="{{ route('jobs.index', ['sort' => 'match']) }}">Prepare my next application &rarr;</a>
        @endif
    </div>
</section>
