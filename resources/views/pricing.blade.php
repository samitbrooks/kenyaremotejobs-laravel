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
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunrise-100 border border-sunrise-200 px-4 py-1 text-xs font-bold uppercase tracking-wider text-sunrise-800">
                <x-icon name="sparkle" class="h-3.5 w-3.5 text-sunrise-600" /> Remote Career Accelerator
            </span>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-5xl text-foreground">
                Land a $2,000+/Month Remote Role.<br />
                <span class="text-transparent bg-clip-text gradient-sunrise">Invest Just KES 50 a Day.</span>
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg text-foreground/70 leading-relaxed">
                When global remote jobs open up, <strong class="text-foreground">over 500 applicants apply within 72 hours</strong>. KenyaRemoteJobs Pro gives you the unfair advantage: 48-hour early access, an AI-tailored resume that passes ATS screener bots, and <strong class="text-foreground">exclusive direct applications to employers actively seeking Kenyan talent</strong>.
            </p>
        </div>

        {{-- Main Checkout Card --}}
        <div class="mt-10 mx-auto max-w-xl rounded-3xl border-2 border-horizon-200 bg-white p-6 sm:p-8 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-gradient-to-l from-sunrise-500 to-horizon-600 px-4 py-1 text-[11px] font-bold uppercase tracking-widest text-white rounded-bl-xl shadow-xs">
                M-Pesa Supported
            </div>

            <div class="text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-horizon-700">Choose Your Plan</p>
                <h2 class="text-2xl font-bold mt-1 text-foreground">KenyaRemoteJobs Pro Membership</h2>
                <p class="text-xs text-foreground/60 mt-1">Instant STK push to your Safaricom line &middot; Cancel anytime</p>
            </div>

            <div class="mt-6">
                <livewire:subscribe-button :is-authed="(bool) $user" :already-subscribed="(bool) $user?->subscribed" />
            </div>

            <div class="mt-6 border-t border-black/5 pt-4 text-center">
                <p class="text-xs text-foreground/50 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Official Safaricom Daraja M-Pesa Integration &middot; Secure & Instant
                </p>
            </div>
        </div>

        {{-- ROI / Value Callout --}}
        <div class="mt-12 rounded-3xl bg-gradient-to-r from-horizon-900 to-horizon-800 p-6 sm:p-8 text-white shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center md:text-left items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-horizon-300">The Math</p>
                    <p class="text-xl font-bold mt-1">100x ROI</p>
                    <p class="text-xs text-horizon-200 mt-1">An entry remote role pays $1,500/mo (~KES 195,000). A single offer covers your membership for years.</p>
                </div>
                <div class="md:border-l md:border-horizon-700/60 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-horizon-300">First 48 Hours</p>
                    <p class="text-xl font-bold mt-1">4x Callbacks</p>
                    <p class="text-xs text-horizon-200 mt-1">Hiring managers interview candidates on a rolling basis. Applying in the first 48h gets you reviewed first.</p>
                </div>
                <div class="md:border-l md:border-horizon-700/60 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-horizon-300">ATS Optimization</p>
                    <p class="text-xl font-bold mt-1">94%+ Match</p>
                    <p class="text-xs text-horizon-200 mt-1">75% of CVs are rejected by automated bots. Our AI CV Tailor optimizes your bullets to pass.</p>
                </div>
                <div class="md:border-l md:border-horizon-700/60 md:pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-400">🇰🇪 Kenya Focus</p>
                    <p class="text-xl font-bold mt-1">Direct Employers</p>
                    <p class="text-xs text-horizon-200 mt-1">Direct access to companies actively seeking Kenyan talent with zero foreign visa barriers.</p>
                </div>
            </div>
        </div>

        {{-- Feature Comparison Table --}}
        <div class="mt-14">
            <h2 class="text-2xl font-bold text-center text-foreground">Free vs Pro Membership</h2>
            <p class="text-sm text-center text-foreground/60 mt-1">Everything you need to compete with global talent</p>

            <div class="mt-6 overflow-hidden rounded-2xl border border-black/10 bg-white shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-black/10 bg-horizon-50/50">
                            <th class="p-4 font-semibold text-foreground">Feature</th>
                            <th class="p-4 font-semibold text-foreground/70 text-center w-36 sm:w-44">Free Visitor</th>
                            <th class="p-4 font-bold text-horizon-800 text-center w-36 sm:w-44 bg-horizon-100/50">Pro Member</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                Browse all 800+ remote jobs
                                <span class="block text-xs text-foreground/50">Transparent companies, verified logos, requirements & salaries</span>
                            </td>
                            <td class="p-4 text-center text-emerald-600 font-bold">✓ Included</td>
                            <td class="p-4 text-center text-emerald-600 font-bold bg-horizon-50/20">✓ Included</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                48-Hour Early Access Window
                                <span class="block text-xs text-foreground/50">Apply to brand new listings before the 500+ general applicant crowd</span>
                            </td>
                            <td class="p-4 text-center text-foreground/40">Wait 48 Hours</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Instant Access</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                Direct Employer Listing Applications 🇰🇪
                                <span class="block text-xs text-foreground/50">Direct submissions from verified companies specifically looking for Kenyan candidates</span>
                            </td>
                            <td class="p-4 text-center text-foreground/40 font-medium">View only</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Included (Pro Exclusive 🇰🇪)</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                Kenya-Targeted Matching Pipeline 🇰🇪
                                <span class="block text-xs text-foreground/50">Employers actively seeking Kenyan populations with zero foreign visa barriers & compatible EAT timezone</span>
                            </td>
                            <td class="p-4 text-center text-foreground/40 font-medium">Preview only</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Full Access 🇰🇪</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                1-Click AI CV & Cover Letter Tailoring
                                <span class="block text-xs text-foreground/50">Generates custom ATS bullet points & cover letters for each role</span>
                            </td>
                            <td class="p-4 text-center text-foreground/60">1 Free Taste</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Unlimited</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                In-App Application CRM & Notes
                                <span class="block text-xs text-foreground/50">Track Saved, Applied, Interviewing, and recruiter contacts</span>
                            </td>
                            <td class="p-4 text-center text-foreground/40">Limited</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Full CRM</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                Kenyan Remote Contractor Toolkit
                                <span class="block text-xs text-foreground/50">USD-to-Mpesa invoice template, W-8BEN cheat sheet, EAT timezone pitch</span>
                            </td>
                            <td class="p-4 text-center text-foreground/40">—</td>
                            <td class="p-4 text-center text-emerald-700 font-bold bg-horizon-50/20">✓ Included Free</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-medium text-foreground">
                                Public Scraped Job Applications (after 48h)
                                <span class="block text-xs text-foreground/50">General public listings from international job boards</span>
                            </td>
                            <td class="p-4 text-center text-emerald-600 font-bold">✓ Included</td>
                            <td class="p-4 text-center text-emerald-600 font-bold bg-horizon-50/20">✓ Included</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kenyan Contractor Toolkit Feature Highlight --}}
        <div class="mt-14 rounded-3xl border border-black/5 bg-white p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-bold">
                    🇰🇪
                </span>
                <div>
                    <h3 class="text-xl font-bold text-foreground">Included Free: The Kenyan Remote Contractor Toolkit</h3>
                    <p class="text-xs text-foreground/60">Practical resources designed specifically for Kenyan remote professionals working with US/UK/EU clients.</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-black/5 bg-horizon-50/50 p-4">
                    <p class="font-bold text-sm text-horizon-900">📄 USD Invoice Template</p>
                    <p class="text-xs text-foreground/70 mt-1">Professional contractor invoice format pre-configured for receiving foreign payments via Wise, Payoneer, or direct Wire to Kenyan banks.</p>
                </div>
                <div class="rounded-2xl border border-black/5 bg-horizon-50/50 p-4">
                    <p class="font-bold text-sm text-horizon-900">📑 US W-8BEN Form Cheat Sheet</p>
                    <p class="text-xs text-foreground/70 mt-1">Step-by-step guidance on filling out IRS W-8BEN for US remote clients without being double-taxed.</p>
                </div>
                <div class="rounded-2xl border border-black/5 bg-horizon-50/50 p-4">
                    <p class="font-bold text-sm text-horizon-900">⏰ EAT Timezone Advantage Pitch</p>
                    <p class="text-xs text-foreground/70 mt-1">Proven email scripts framing Nairobi (GMT+3) as the ideal timezone overlap for European and East Coast US teams.</p>
                </div>
            </div>
        </div>

        {{-- Frequently Asked Questions --}}
        <div class="mt-14 max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-center text-foreground">Frequently Asked Questions</h2>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                    <h3 class="font-semibold text-foreground">What are Direct Employer Listings &amp; Kenya-Matched Roles?</h3>
                    <p class="mt-2 text-sm text-foreground/70 leading-relaxed">
                        These are companies that post directly on KenyaRemoteJobs specifically to recruit Kenyan and East African professionals. Because these employers are actively seeking local talent (meaning zero visa rejections and verified timezone compatibility), direct application access is reserved exclusively for Pro members.
                    </p>
                </div>

                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                    <h3 class="font-semibold text-foreground">Why do you offer 48-Hour Early Access?</h3>
                    <p class="mt-2 text-sm text-foreground/70 leading-relaxed">
                        International remote companies receive 500+ applications within 3–4 days of posting. Recruiters frequently review the first 20–30 candidates and close applications early. Pro members get an exclusive 48-hour head start to apply before the position is opened to the public.
                    </p>
                </div>

                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                    <h3 class="font-semibold text-foreground">How does the 1-Click AI CV Tailoring work?</h3>
                    <p class="mt-2 text-sm text-foreground/70 leading-relaxed">
                        Our AI analyzes the exact technical requirements, keywords, and tone in the job description, then extracts matching achievements from your background to generate high-impact resume bullets and an aligned cover letter with a 94%+ ATS pass rate.
                    </p>
                </div>

                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                    <h3 class="font-semibold text-foreground">How do I pay with M-Pesa?</h3>
                    <p class="mt-2 text-sm text-foreground/70 leading-relaxed">
                        Select your plan above, enter your Safaricom phone number, and click Subscribe. You will receive an instant M-Pesa STK push prompt on your phone. Enter your M-Pesa PIN and your Pro membership activates immediately.
                    </p>
                </div>

                <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                    <h3 class="font-semibold text-foreground">Can I cancel anytime?</h3>
                    <p class="mt-2 text-sm text-foreground/70 leading-relaxed">
                        Yes! There are no contracts or hidden renewal commitments. You keep full Pro benefits for the entirety of the duration you paid for.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-sm text-foreground/50">
                Have questions before joining? Reach our team anytime at <a href="mailto:support@kenyaremotejobs.com" class="font-semibold text-sunrise-600 hover:underline">support@kenyaremotejobs.com</a>
            </p>
        </div>
    </div>
</x-layouts.app>
