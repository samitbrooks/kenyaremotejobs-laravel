<footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-lg font-bold text-white flex items-center gap-1.5">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-600 text-white font-black text-sm">
                        K
                    </span>
                    <span>Kenya<span class="text-teal-400 font-extrabold">Remote</span>Jobs</span>
                </p>
                <p class="mt-3 text-sm leading-relaxed text-slate-400">
                    Work from Kenya. Work for the world. Hand-curated, verified remote jobs scored for timezone overlap and visa freedom.
                </p>
                <p class="mt-4 text-xs font-medium text-slate-500">
                    Nairobi, Kenya &middot; East Africa Time (UTC+3)
                </p>
            </div>

            <div class="text-sm">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-200">Popular Roles in Kenya</p>
                <ul class="space-y-2">
                    <li><a href="{{ url('/remote-jobs/customer-support-kenya') }}" class="hover:text-teal-400 transition">Customer Support Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/virtual-assistant-kenya') }}" class="hover:text-teal-400 transition">Virtual Assistant Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/software-developer-kenya') }}" class="hover:text-teal-400 transition">Software Developer Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/data-entry-kenya') }}" class="hover:text-teal-400 transition">Data Entry &amp; Operations</a></li>
                    <li><a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="hover:text-teal-400 transition">Writing &amp; Content Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/digital-marketing-kenya') }}" class="hover:text-teal-400 transition">Digital Marketing Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/entry-level-kenya') }}" class="hover:text-teal-400 transition">Entry-Level Remote Jobs</a></li>
                </ul>
            </div>

            <div class="text-sm">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-200">Jobseeker Hub</p>
                <ul class="space-y-2">
                    <li><a href="{{ url('/jobs') }}" class="hover:text-teal-400 transition">Browse all remote jobs</a></li>
                    <li><a href="{{ url('/companies') }}" class="hover:text-teal-400 transition">Companies hiring in Kenya</a></li>
                    <li><a href="{{ url('/collections') }}" class="hover:text-teal-400 transition">Curated job collections</a></li>
                    <li><a href="{{ url('/journal') }}" class="hover:text-teal-400 transition">The Journal (Guides &amp; Advice)</a></li>
                    <li><a href="{{ url('/match') }}" class="hover:text-teal-400 transition">Timezone &amp; CV Matcher</a></li>
                    <li><a href="{{ url('/resume-builder') }}" class="hover:text-teal-400 transition">CV &amp; Cover Letter Builder</a></li>
                    <li><a href="{{ url('/pricing') }}" class="hover:text-teal-400 transition">Pro Early Access Pricing</a></li>
                    <li><a href="{{ url('/faqs') }}" class="hover:text-teal-400 transition">Frequently Asked Questions</a></li>
                    <li><a href="{{ url('/surveys') }}" class="hover:text-teal-400 transition">Surveys &amp; Side Income</a></li>
                    <li><a href="{{ url('/about') }}" class="hover:text-teal-400 transition">About KenyaRemoteJobs</a></li>
                </ul>
            </div>

            <div class="text-sm">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-200">Employers</p>
                <ul class="space-y-2">
                    <li><a href="{{ url('/employers') }}" class="hover:text-teal-400 transition">Hire Kenyan talent</a></li>
                    <li><a href="{{ url('/employers/post') }}" class="hover:text-teal-400 transition">Post a remote job</a></li>
                    <li><a href="{{ url('/employers/dashboard') }}" class="hover:text-teal-400 transition">Employer dashboard</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-between gap-4 border-t border-slate-800 pt-6 text-xs text-slate-500">
            <p>&copy; {{ now()->year }} KenyaRemoteJobs. All rights reserved.</p>
            <p class="flex items-center gap-2">
                <span>Pre-screened for East Africa Time (EAT)</span>
                <span>&middot;</span>
                <span>No US/EU Visa Restrictions</span>
            </p>
        </div>
    </div>
</footer>
