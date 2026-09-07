<footer class="bg-horizon-900 text-white/70">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <div class="grid gap-8 sm:grid-cols-3">
            <div>
                <p class="text-lg font-bold text-white">
                    Kenya<span class="text-sunrise-400">Remote</span>Jobs
                </p>
                <p class="mt-2 text-sm">Work from Kenya. Work for the world.</p>
            </div>

            <div class="text-sm">
                <p class="mb-2 font-semibold text-white">Explore</p>
                <ul class="space-y-1">
                    <li><a href="{{ url('/jobs') }}" class="hover:text-sunrise-300">Browse jobs</a></li>
                    <li><a href="{{ url('/journal') }}" class="hover:text-sunrise-300">The Journal</a></li>
                    <li><a href="{{ url('/success-stories') }}" class="hover:text-sunrise-300">Success stories</a></li>
                    <li><a href="{{ url('/pricing') }}" class="hover:text-sunrise-300">Pricing</a></li>
                    <li><a href="{{ url('/resume-builder') }}" class="hover:text-sunrise-300">CV &amp; cover letter builder</a></li>
                    <li><a href="{{ url('/surveys') }}" class="hover:text-sunrise-300">Surveys &amp; side income</a></li>
                    <li><a href="{{ url('/about') }}" class="hover:text-sunrise-300">About</a></li>
                </ul>
            </div>

            <div class="text-sm">
                <p class="mb-2 font-semibold text-white">Employers</p>
                <ul class="space-y-1">
                    <li><a href="{{ url('/employers') }}" class="hover:text-sunrise-300">Hire remote talent</a></li>
                    <li><a href="{{ url('/employers/post') }}" class="hover:text-sunrise-300">Post a job</a></li>
                    <li><a href="{{ url('/employers/dashboard') }}" class="hover:text-sunrise-300">Employer dashboard</a></li>
                </ul>
            </div>
        </div>

        <p class="mt-8 border-t border-white/10 pt-6 text-xs">
            &copy; {{ now()->year }} KenyaRemoteJobs.
        </p>
    </div>
</footer>
