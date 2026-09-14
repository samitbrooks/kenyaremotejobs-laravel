<footer class="bg-horizon-900 text-white/70">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-lg font-bold text-white">
                    Kenya<span class="text-sunrise-400">Remote</span>Jobs
                </p>
                <p class="mt-2 text-sm leading-relaxed text-white/60">
                    Work from Kenya. Work for the world. Verified remote jobs from international employers scored for timezone overlap and visa fit.
                </p>
                <p class="mt-4 text-xs text-white/40">
                    Nairobi, Kenya &middot; East Africa Time (UTC+3)
                </p>
            </div>

            <div class="text-sm">
                <p class="mb-3 font-semibold text-white">Popular Roles in Kenya</p>
                <ul class="space-y-1.5">
                    <li><a href="{{ url('/remote-jobs/customer-support-kenya') }}" class="hover:text-sunrise-300 transition">Customer Support Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/virtual-assistant-kenya') }}" class="hover:text-sunrise-300 transition">Virtual Assistant Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/software-developer-kenya') }}" class="hover:text-sunrise-300 transition">Software Developer Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/data-entry-kenya') }}" class="hover:text-sunrise-300 transition">Data Entry &amp; Operations</a></li>
                    <li><a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="hover:text-sunrise-300 transition">Writing &amp; Content Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/digital-marketing-kenya') }}" class="hover:text-sunrise-300 transition">Digital Marketing Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/entry-level-kenya') }}" class="hover:text-sunrise-300 transition">Entry-Level Remote Jobs</a></li>
                </ul>
            </div>

            <div class="text-sm">
                <p class="mb-3 font-semibold text-white">Jobseeker Hub</p>
                <ul class="space-y-1.5">
                    <li><a href="{{ url('/jobs') }}" class="hover:text-sunrise-300 transition">Browse all remote jobs</a></li>
                    <li><a href="{{ url('/companies') }}" class="hover:text-sunrise-300 transition">Companies hiring in Kenya</a></li>
                    <li><a href="{{ url('/collections') }}" class="hover:text-sunrise-300 transition">Curated job collections</a></li>
                    <li><a href="{{ url('/journal') }}" class="hover:text-sunrise-300 transition">The Journal (Guides &amp; Advice)</a></li>
                    <li><a href="{{ url('/match') }}" class="hover:text-sunrise-300 transition">Timezone &amp; CV Matcher</a></li>
                    <li><a href="{{ url('/resume-builder') }}" class="hover:text-sunrise-300 transition">CV &amp; Cover Letter Builder</a></li>
                    <li><a href="{{ url('/pricing') }}" class="hover:text-sunrise-300 transition">Pro Early Access Pricing</a></li>
                    <li><a href="{{ url('/faqs') }}" class="hover:text-sunrise-300 transition">Frequently Asked Questions</a></li>
                    <li><a href="{{ url('/surveys') }}" class="hover:text-sunrise-300 transition">Surveys &amp; Side Income</a></li>
                    <li><a href="{{ url('/about') }}" class="hover:text-sunrise-300 transition">About KenyaRemoteJobs</a></li>
                </ul>
            </div>

            <div class="text-sm">
                <p class="mb-3 font-semibold text-white">Employers</p>
                <ul class="space-y-1.5">
                    <li><a href="{{ url('/employers') }}" class="hover:text-sunrise-300 transition">Hire Kenyan talent</a></li>
                    <li><a href="{{ url('/employers/post') }}" class="hover:text-sunrise-300 transition">Post a remote job</a></li>
                    <li><a href="{{ url('/employers/dashboard') }}" class="hover:text-sunrise-300 transition">Employer dashboard</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-white/50">
            <p>&copy; {{ now()->year }} KenyaRemoteJobs. All rights reserved.</p>
            <p class="flex items-center gap-2">
                <span>Pre-screened for East Africa Time (EAT)</span>
                <span>&middot;</span>
                <span>No US/EU Visa Restrictions</span>
            </p>
        </div>
    </div>
</footer>
