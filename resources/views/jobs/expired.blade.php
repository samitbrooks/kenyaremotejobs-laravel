<x-layouts.app
    title="Job Listing Closed or Expired | KenyaRemoteJobs"
    description="This job posting has reached its deadline or closed. Explore active verified remote vacancies open to Kenyan applicants."
    :noindex="true"
>
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
        {{-- Friendly Expired Notice --}}
        <div class="rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50/80 via-white to-amber-50/50 p-6 sm:p-8 shadow-xs text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 shadow-2xs">
                <x-icon name="clock" class="h-7 w-7 text-amber-600" />
            </div>
            <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold text-slate-900">
                This Job Posting Has Closed or Expired
            </h1>
            <p class="mx-auto mt-2 max-w-xl text-sm sm:text-base text-slate-600">
                The role you're looking for is no longer accepting new applications. International remote roles fill fast — explore current active listings below screened for East Africa Time (UTC+3) compatibility.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/jobs') }}" class="btn-pop rounded-full bg-teal-700 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-teal-800 shadow-xs transition">
                    Browse All Live Remote Jobs &rarr;
                </a>
                <a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="rounded-full bg-white border border-slate-200/80 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:border-teal-500 hover:text-teal-700 transition shadow-2xs">
                    Writing &amp; Content
                </a>
                <a href="{{ url('/remote-jobs/customer-support-kenya') }}" class="rounded-full bg-white border border-slate-200/80 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:border-teal-500 hover:text-teal-700 transition shadow-2xs">
                    Customer Support
                </a>
                <a href="{{ url('/remote-jobs/virtual-assistant-kenya') }}" class="rounded-full bg-white border border-slate-200/80 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:border-teal-500 hover:text-teal-700 transition shadow-2xs">
                    Virtual Assistant
                </a>
            </div>
        </div>

        {{-- Active Job Opportunities --}}
        @if ($activeJobs->isNotEmpty())
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Fresh Remote Jobs Open Right Now</h2>
                        <p class="text-xs text-slate-500">Verified international employers actively hiring Kenyan applicants</p>
                    </div>
                    <a href="{{ url('/jobs') }}" class="text-xs font-bold text-teal-700 hover:underline">
                        View all &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($activeJobs as $job)
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs hover:border-teal-500/60 hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">
                                        <a href="{{ url('/jobs/'.$job->id) }}" class="hover:text-teal-700 transition">
                                            {{ $job->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">{{ $job->company }}</p>
                                </div>
                                @if ($job->kenya_friendly)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 border border-teal-200 px-2 py-0.5 text-[11px] font-bold text-teal-800 shrink-0">
                                        <x-icon name="check" class="h-3 w-3 text-teal-600" /> Kenya-Friendly
                                    </span>
                                @endif
                            </div>
                            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 pt-3 border-t border-slate-100">
                                <span>{{ $job->remote_type ?: 'Remote' }}</span>
                                <a href="{{ url('/jobs/'.$job->id) }}" class="font-bold text-teal-700 hover:underline">
                                    View role &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
