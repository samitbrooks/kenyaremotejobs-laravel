@php
    $plans = config('jobs.subscription_plans');
@endphp

<x-layouts.app
    title="KenyaRemoteJobs Pro — Land Your Next Global Remote Job"
    description="Accelerate your remote career. Get 48-Hour Early Access to top international remote jobs, exclusive access to direct employers actively seeking Kenyan talent, unlimited AI CV & cover letter tailoring, and our complete Kenyan Remote Contractor Toolkit. Pay conveniently via M-Pesa."
>
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
        {{-- Hero Header --}}
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200 px-4 py-1 text-xs font-bold uppercase tracking-wider text-teal-800">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-teal-600" /> Remote Career Accelerator
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-5xl text-slate-900">
                Land a $2,000+/Month Remote Role.<br />
                <span class="text-teal-700">Invest Just KES 50 a Day.</span>
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed">
                When global remote jobs open up, <strong class="text-slate-900">over 500 applicants apply within 72 hours</strong>. KenyaRemoteJobs Pro gives you <strong class="text-slate-900">full, immediate access to apply to all 800+ remote jobs</strong> with 48-hour early access, direct Kenyan employer listings, and AI CV tailoring.
            </p>
        </div>

        {{-- Main Checkout Card --}}
        <div class="mt-10 mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-teal-700 px-4 py-1 text-[11px] font-bold uppercase tracking-widest text-white rounded-bl-xl shadow-2xs">
                M-Pesa Supported
            </div>

            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Choose Your Plan</p>
                <h2 class="text-2xl font-bold mt-1 text-slate-900">KenyaRemoteJobs Pro Membership</h2>
                <div class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3.5 py-1 text-xs font-bold text-emerald-800">
                    <x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Early Access Gives Full Access to All 800+ Jobs
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Instant STK push to your Safaricom line &middot; Cancel anytime</p>
            </div>

            <div class="mt-6">
                <livewire:subscribe-button :is-authed="(bool) $user" :already-subscribed="(bool) $user?->subscribed" />
            </div>

            <div class="mt-6 border-t border-slate-200 pt-4 text-center">
                <p class="text-xs text-slate-500 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Official Safaricom Daraja M-Pesa Integration &middot; Secure & Instant
                </p>
            </div>
        </div>

        {{-- ROI / Value Callout --}}
        <div class="mt-12 rounded-2xl bg-slate-900 p-6 sm:p-8 text-white shadow-md border border-slate-800">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center md:text-left items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-400">All 800+ Jobs</p>
                    <p class="text-xl font-bold mt-1">Full Access</p>
                    <p class="text-xs text-slate-300 mt-1">Apply immediately to all listings — no 48h wait or locked apply buttons.</p>
                </div>
                <div class="md:border-l md:border-slate-800 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-400">First 48 Hours</p>
                    <p class="text-xl font-bold mt-1">4x Callbacks</p>
                    <p class="text-xs text-slate-300 mt-1">Hiring managers interview candidates on a rolling basis. Applying early gets you reviewed first.</p>
                </div>
                <div class="md:border-l md:border-slate-800 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-400">ATS Optimization</p>
                    <p class="text-xl font-bold mt-1">94%+ Match</p>
                    <p class="text-xs text-slate-300 mt-1">75% of CVs are rejected by automated bots. Our AI CV Tailor optimizes your bullets to pass.</p>
                </div>
                <div class="md:border-l md:border-slate-800 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-400">🇰🇪 Kenya Focus</p>
                    <p class="text-xl font-bold mt-1">Direct Employers</p>
                    <p class="text-xs text-slate-300 mt-1">Direct access to companies actively seeking Kenyan talent with zero foreign visa barriers.</p>
                </div>
            </div>
        </div>

        {{-- Feature Comparison Table --}}
        <div class="mt-14">
            <h2 class="text-2xl font-bold text-center text-slate-900">Free vs Pro Membership</h2>
            <p class="text-sm text-center text-slate-500 mt-1">Everything you need to compete with global talent</p>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80">
                            <th class="p-4 font-semibold text-slate-900">Feature</th>
                            <th class="p-4 font-semibold text-slate-600 text-center w-36 sm:w-44">Free Visitor</th>
                            <th class="p-4 font-bold text-teal-800 text-center w-36 sm:w-44 bg-teal-50/40">Pro Member</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Full Access to Apply to All 800+ Jobs
                                <span class="block text-xs text-slate-500 font-normal">Early Access gives you immediate apply links on all roles — fresh 48h listings, direct employer postings, and the full catalog</span>
                            </td>
                            <td class="p-4 text-center text-slate-400 font-medium">Partial (Wait 48h on new)</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ 100% Full Access</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Browse all 800+ remote jobs
                                <span class="block text-xs text-slate-500 font-normal">Transparent companies, verified logos, requirements & salaries</span>
                            </td>
                            <td class="p-4 text-center text-emerald-600 font-bold">✓ Included</td>
                            <td class="p-4 text-center text-emerald-600 font-bold bg-teal-50/20">✓ Included</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                48-Hour Early Access Window
                                <span class="block text-xs text-slate-500 font-normal">Apply to brand new listings before the 500+ general applicant crowd</span>
                            </td>
                            <td class="p-4 text-center text-slate-400">Wait 48 Hours</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Instant Access</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Direct Employer Listing Applications 🇰🇪
                                <span class="block text-xs text-slate-500 font-normal">Direct submissions from verified companies specifically looking for Kenyan candidates</span>
                            </td>
                            <td class="p-4 text-center text-slate-400 font-medium">View only</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Included (Pro Exclusive 🇰🇪)</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Kenya-Targeted Matching Pipeline 🇰🇪
                                <span class="block text-xs text-slate-500 font-normal">Employers actively seeking Kenyan populations with zero foreign visa barriers & compatible EAT timezone</span>
                            </td>
                            <td class="p-4 text-center text-slate-400 font-medium">Preview only</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Full Access 🇰🇪</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                1-Click AI CV & Cover Letter Tailoring
                                <span class="block text-xs text-slate-500 font-normal">Generates custom ATS bullet points & cover letters for each role</span>
                            </td>
                            <td class="p-4 text-center text-slate-500">1 Free Taste</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Unlimited</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                In-App Application CRM & Notes
                                <span class="block text-xs text-slate-500 font-normal">Track Saved, Applied, Interviewing, and recruiter contacts</span>
                            </td>
                            <td class="p-4 text-center text-slate-400">Limited</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Full CRM</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Kenyan Remote Contractor Toolkit
                                <span class="block text-xs text-slate-500 font-normal">USD-to-Mpesa invoice template, W-8BEN cheat sheet, EAT timezone pitch</span>
                            </td>
                            <td class="p-4 text-center text-slate-400">—</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-teal-50/20">✓ Included with Pro</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-slate-900">
                                Public Global Job Listings (after 48h window)
                                <span class="block text-xs text-slate-500 font-normal">Standard remote openings from international companies</span>
                            </td>
                            <td class="p-4 text-center text-emerald-600 font-bold">✓ Included</td>
                            <td class="p-4 text-center text-emerald-600 font-bold bg-teal-50/20">✓ Included</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Employers Seeking Kenyan Talent & Contractor Toolkit (Pro Exclusive) --}}
        <div class="mt-14 rounded-2xl border border-emerald-300 bg-gradient-to-br from-emerald-50/40 via-white to-slate-50/40 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 text-2xl font-bold shadow-2xs">
                        🇰🇪
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-slate-900">Pro Exclusive: Employers Actively Seeking Kenyan Talent</h3>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-200">
                                Pro Only
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">Direct hiring manager pipelines with zero foreign visa barriers, plus your complete remote contractor setup.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-emerald-950 flex items-center gap-1.5">
                        <span>🤝</span> Direct Employer Inboxes
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">Companies post directly on KenyaRemoteJobs specifically to recruit Kenyan professionals. Pro members apply directly to hiring decision-makers.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-emerald-950 flex items-center gap-1.5">
                        <span>🎯</span> Kenya-Matching (No Visa Hurdles)
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">Roles verified to accept East African residents without requiring US/EU work permits, pre-screened for friendly payment terms.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-emerald-950 flex items-center gap-1.5">
                        <span>⏰</span> EAT Timezone Advantage Pitch
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">Proven cover letter scripts framing Nairobi (GMT+3) as an advantageous 4–5 hour daily overlap for European and US East Coast teams.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                        <span>📄</span> USD Invoice Template
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">Professional international contractor invoice pre-configured for receiving foreign payments via Wise, Payoneer, or direct Wire to Kenyan banks.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                        <span>📑</span> US W-8BEN Form Cheat Sheet
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">Step-by-step instructions on filling out IRS W-8BEN using your KRA PIN to avoid 30% US withholding tax under international treaties.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <p class="font-bold text-sm text-slate-900 flex items-center gap-1.5">
                        <span>💼</span> Candidate CRM &amp; Tracking
                    </p>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">In-app interview tracking pipeline with recruiter notes, salary discussed, and follow-up reminders in your personal dashboard.</p>
                </div>
            </div>
        </div>

        {{-- Frequently Asked Questions --}}
        <div class="mt-14 max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-center text-slate-900">Frequently Asked Questions</h2>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">What are Direct Employer Listings &amp; Kenya-Matched Roles?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        These are companies that post directly on KenyaRemoteJobs specifically to recruit Kenyan and East African professionals. Because these employers are actively seeking local talent (meaning zero visa rejections and verified timezone compatibility), direct application access is reserved exclusively for Pro members.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">Does Early Access give me full access to all jobs?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Yes. Early Access gives you 100% full, immediate access to apply to all 800+ remote jobs on the platform &mdash; skipping the 48-hour wait on fresh listings and unlocking direct employer applications.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">Why do you offer 48-Hour Early Access?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        International remote companies receive 500+ applications within 3–4 days of posting. Recruiters frequently review the first 20–30 candidates and close applications early. Pro members get an exclusive 48-hour head start to apply before the position is opened to the public.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">How does the 1-Click AI CV Tailoring work?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Our AI analyzes the exact technical requirements, keywords, and tone in the job description, then extracts matching achievements from your background to generate high-impact resume bullets and an aligned cover letter with a 94%+ ATS pass rate.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">How do I pay with M-Pesa?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Select your plan above, enter your Safaricom phone number, and click Subscribe. You will receive an instant M-Pesa STK push prompt on your phone. Enter your M-Pesa PIN and your Pro membership activates immediately.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                    <h3 class="font-semibold text-slate-900">Can I cancel anytime?</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Yes! There are no contracts or hidden renewal commitments. You keep full Pro benefits for the entirety of the duration you paid for.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-sm text-slate-500">
                Have questions before joining? Reach our team anytime at <a href="mailto:support@kenyaremotejobs.com" class="font-semibold text-teal-700 hover:underline">support@kenyaremotejobs.com</a>
            </p>
        </div>
    </div>
</x-layouts.app>
