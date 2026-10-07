<footer class="bg-[#181c33] text-slate-400 border-t border-[#222949]">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ url('/') }}" class="inline-block transition-transform duration-150 hover:opacity-90 active:scale-95" aria-label="KenyaRemoteJobs Home">
                    <picture>
                        <source srcset="{{ asset('images/logo-white.webp') }}" type="image/webp">
                        <img
                            src="{{ asset('images/logo-white.png') }}"
                            alt="KenyaRemoteJobs"
                            class="h-8.5 w-auto object-contain"
                            width="130"
                            height="34"
                            loading="lazy"
                            decoding="async"
                        />
                    </picture>
                </a>
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
                    <li>
                        <a href="{{ url('/remote-jobs/writing-content-kenya') }}" class="inline-flex items-center gap-1.5 font-bold text-teal-300 hover:text-teal-200 transition">
                            <span>Writing &amp; Content Jobs</span>
                            <span class="rounded bg-teal-500/20 border border-teal-500/40 px-1.5 py-0.2 text-[10px] font-semibold text-teal-200">🔥 Top in KE</span>
                        </a>
                    </li>
                    <li><a href="{{ url('/remote-jobs/customer-support-kenya') }}" class="hover:text-teal-400 transition">Customer Support Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/virtual-assistant-kenya') }}" class="hover:text-teal-400 transition">Virtual Assistant Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/software-developer-kenya') }}" class="hover:text-teal-400 transition">Software Developer Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/ai-training-annotation-kenya') }}" class="hover:text-teal-400 transition">AI Training &amp; Annotation</a></li>
                    <li><a href="{{ url('/remote-jobs/data-entry-kenya') }}" class="hover:text-teal-400 transition">Data Entry &amp; Operations</a></li>
                    <li><a href="{{ url('/remote-jobs/transcription-translation-kenya') }}" class="hover:text-teal-400 transition">Transcription &amp; Translation</a></li>
                    <li><a href="{{ url('/remote-jobs/digital-marketing-kenya') }}" class="hover:text-teal-400 transition">Digital Marketing Jobs</a></li>
                    <li><a href="{{ url('/remote-jobs/graphic-design-kenya') }}" class="hover:text-teal-400 transition">Graphic Design &amp; UI/UX</a></li>
                    <li><a href="{{ url('/remote-jobs/accounting-finance-kenya') }}" class="hover:text-teal-400 transition">Accounting &amp; Finance</a></li>
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
            <div class="flex items-center gap-4">
                <a
                    href="https://www.linkedin.com/company/kenyaremotejobs"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-slate-400 hover:text-teal-400 transition font-medium"
                >
                    <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    <span>Follow on LinkedIn</span>
                </a>
                <span class="text-slate-700">&middot;</span>
                <span>Pre-screened for EAT (UTC+3)</span>
                <span class="text-slate-700">&middot;</span>
                <span>No US/EU Visa Barriers</span>
            </div>
        </div>
    </div>
</footer>
