<x-layouts.app
    title="Free CV & Cover Letter Builder"
    description="Build a clean, professional CV and cover letter in your browser — free, no account, no AI, nothing leaves your device. Fill in the form, preview it live, and print or save as PDF."
>
    <div
        x-data="resumeBuilder()"
        x-init="load()"
        class="mx-auto max-w-6xl px-4 py-14 sm:px-6"
    >
        <div class="no-print mx-auto max-w-xl text-center">
            <h1 class="text-3xl font-bold sm:text-4xl">Free CV &amp; cover letter builder</h1>
            <p class="mt-3 text-foreground/60">
                Fill in the form, watch it turn into a clean, ready-to-send document on the right, then print or save it as a PDF. No account, no AI, nothing sent to a server &mdash; your draft stays in this browser only.
            </p>
        </div>

        <div class="mt-10">
            <div class="no-print mb-8 flex justify-center">
                <div class="flex gap-1 rounded-full bg-black/5 p-1">
                    <button type="button" @click="tab = 'cv'" class="rounded-full px-5 py-2 text-sm font-semibold transition" :class="tab === 'cv' ? 'bg-white text-horizon-800 shadow-sm' : 'text-foreground/50'">
                        CV / Resume
                    </button>
                    <button type="button" @click="tab = 'cover-letter'" class="rounded-full px-5 py-2 text-sm font-semibold transition" :class="tab === 'cover-letter' ? 'bg-white text-horizon-800 shadow-sm' : 'text-foreground/50'">
                        Cover Letter
                    </button>
                </div>
            </div>

            {{-- CV editor --}}
            <div x-show="tab === 'cv'" x-cloak class="grid gap-8 lg:grid-cols-2">
                <div class="no-print space-y-6">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Full name</span>
                            <input x-model="cv.fullName" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Title / headline</span>
                            <input x-model="cv.title" placeholder="e.g. Customer Support Specialist" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Email</span>
                            <input x-model="cv.email" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Phone</span>
                            <input x-model="cv.phone" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="col-span-2 block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Location</span>
                            <input x-model="cv.location" placeholder="Nairobi, Kenya" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                    </div>

                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Professional summary</span>
                        <textarea x-model="cv.summary" rows="3" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"></textarea>
                    </label>

                    <div>
                        <p class="mb-2 text-sm font-semibold">Work experience</p>
                        <div class="space-y-4">
                            <template x-for="(entry, i) in cv.experience" :key="i">
                                <div class="rounded-xl border border-black/10 p-3">
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="block text-sm">
                                            <span class="mb-1 block font-medium text-foreground/70">Role</span>
                                            <input x-model="entry.role" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                        </label>
                                        <label class="block text-sm">
                                            <span class="mb-1 block font-medium text-foreground/70">Company</span>
                                            <input x-model="entry.company" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                        </label>
                                    </div>
                                    <label class="mt-2 block text-sm">
                                        <span class="mb-1 block font-medium text-foreground/70">Dates</span>
                                        <input x-model="entry.dates" placeholder="Jan 2023 – Present" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                    </label>
                                    <label class="mt-2 block text-sm">
                                        <span class="mb-1 block font-medium text-foreground/70">Highlights (one per line)</span>
                                        <textarea x-model="entry.bullets" rows="3" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"></textarea>
                                    </label>
                                    <button type="button" x-show="cv.experience.length > 1" @click="cv.experience.splice(i, 1)" class="mt-2 text-xs font-semibold text-red-600 hover:underline">
                                        Remove
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="cv.experience.push({ role: '', company: '', dates: '', bullets: '' })" class="mt-2 text-sm font-semibold text-sunrise-600 hover:underline">
                            + Add another role
                        </button>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-semibold">Education</p>
                        <div class="space-y-3">
                            <template x-for="(entry, i) in cv.education" :key="i">
                                <div class="rounded-xl border border-black/10 p-3">
                                    <label class="block text-sm">
                                        <span class="mb-1 block font-medium text-foreground/70">School / institution</span>
                                        <input x-model="entry.school" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                    </label>
                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                        <label class="block text-sm">
                                            <span class="mb-1 block font-medium text-foreground/70">Qualification</span>
                                            <input x-model="entry.qualification" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                        </label>
                                        <label class="block text-sm">
                                            <span class="mb-1 block font-medium text-foreground/70">Dates</span>
                                            <input x-model="entry.dates" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                                        </label>
                                    </div>
                                    <button type="button" x-show="cv.education.length > 1" @click="cv.education.splice(i, 1)" class="mt-2 text-xs font-semibold text-red-600 hover:underline">
                                        Remove
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="cv.education.push({ school: '', qualification: '', dates: '' })" class="mt-2 text-sm font-semibold text-sunrise-600 hover:underline">
                            + Add another qualification
                        </button>
                    </div>

                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Skills (comma-separated)</span>
                        <input x-model="cv.skills" placeholder="Customer support, Excel, Zendesk, English & Swahili" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                    </label>

                    <button type="button" @click="window.print()" class="btn-pop w-full rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90">
                        Print / Save as PDF
                    </button>
                </div>

                <div class="lg:sticky lg:top-6 lg:self-start">
                    <div class="printable-page mx-auto max-w-[560px] rounded-2xl border border-black/10 bg-white p-8 shadow-lg">
                        <h1 class="text-2xl font-bold text-foreground" x-text="cv.fullName || 'Your Name'"></h1>
                        <p class="text-sm font-medium text-sunrise-600" x-show="cv.title" x-text="cv.title"></p>
                        <p class="mt-1 text-xs text-foreground/50" x-text="[cv.email, cv.phone, cv.location].filter(Boolean).join('  ·  ')"></p>

                        <div class="mt-5" x-show="cv.summary">
                            <p class="text-xs leading-relaxed text-foreground/80" x-text="cv.summary"></p>
                        </div>

                        <div class="mt-5" x-show="cv.experience.some(e => e.role || e.company)">
                            <h2 class="border-b border-black/10 pb-1 text-xs font-bold uppercase tracking-wide text-foreground/60">Experience</h2>
                            <div class="mt-2 space-y-3">
                                <template x-for="(e, i) in cv.experience.filter(e => e.role || e.company)" :key="i">
                                    <div>
                                        <div class="flex items-baseline justify-between gap-2">
                                            <p class="text-sm font-semibold">
                                                <span x-text="e.role"></span>
                                                <span x-show="e.company" x-text="' — ' + e.company"></span>
                                            </p>
                                            <p class="shrink-0 text-xs text-foreground/40" x-text="e.dates"></p>
                                        </div>
                                        <ul class="mt-1 list-disc space-y-0.5 pl-4 text-xs text-foreground/70" x-show="e.bullets">
                                            <template x-for="(b, j) in (e.bullets || '').split('\n').map(s => s.trim()).filter(Boolean)" :key="j">
                                                <li x-text="b"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-5" x-show="cv.education.some(e => e.school)">
                            <h2 class="border-b border-black/10 pb-1 text-xs font-bold uppercase tracking-wide text-foreground/60">Education</h2>
                            <div class="mt-2 space-y-2">
                                <template x-for="(e, i) in cv.education.filter(e => e.school)" :key="i">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <p class="text-sm">
                                            <span x-show="e.qualification" x-text="e.qualification + (e.school ? ' — ' : '')"></span><span x-text="e.school"></span>
                                        </p>
                                        <p class="shrink-0 text-xs text-foreground/40" x-text="e.dates"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-5" x-show="cv.skills">
                            <h2 class="border-b border-black/10 pb-1 text-xs font-bold uppercase tracking-wide text-foreground/60">Skills</h2>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <template x-for="(s, i) in (cv.skills || '').split(',').map(s => s.trim()).filter(Boolean)" :key="i">
                                    <span class="rounded-full bg-horizon-50 px-2 py-0.5 text-[11px] font-medium text-horizon-700" x-text="s"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cover letter editor --}}
            <div x-show="tab === 'cover-letter'" x-cloak class="grid gap-8 lg:grid-cols-2">
                <div class="no-print space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Your full name</span>
                            <input x-model="letter.fullName" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Date</span>
                            <input x-model="letter.date" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Email</span>
                            <input x-model="letter.email" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Phone</span>
                            <input x-model="letter.phone" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Company you&rsquo;re applying to</span>
                            <input x-model="letter.companyName" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                        <label class="block text-sm">
                            <span class="mb-1 block font-medium text-foreground/70">Job title</span>
                            <input x-model="letter.jobTitle" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                        </label>
                    </div>
                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Addressed to</span>
                        <input x-model="letter.hiringManager" placeholder="Hiring Manager" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400">
                    </label>

                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Opening &mdash; why this role, briefly</span>
                        <textarea x-model="letter.opening" rows="3" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"></textarea>
                    </label>
                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Why you&rsquo;re a fit</span>
                        <textarea x-model="letter.body" rows="5" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"></textarea>
                    </label>
                    <label class="block text-sm">
                        <span class="mb-1 block font-medium text-foreground/70">Closing</span>
                        <textarea x-model="letter.closing" rows="2" class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sunrise-400"></textarea>
                    </label>

                    <button type="button" @click="window.print()" class="btn-pop w-full rounded-full gradient-sunrise px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90">
                        Print / Save as PDF
                    </button>
                </div>

                <div class="lg:sticky lg:top-6 lg:self-start">
                    <div class="printable-page mx-auto max-w-[560px] rounded-2xl border border-black/10 bg-white p-8 text-sm leading-relaxed text-foreground/80 shadow-lg">
                        <p class="font-semibold text-foreground" x-text="letter.fullName || 'Your Name'"></p>
                        <p class="text-xs text-foreground/50" x-text="[letter.email, letter.phone].filter(Boolean).join('  ·  ')"></p>
                        <p class="mt-4 text-xs text-foreground/50" x-text="letter.date"></p>

                        <p class="mt-4" x-text="'Dear ' + (letter.hiringManager || 'Hiring Manager') + ','"></p>

                        <p class="mt-4" x-show="letter.opening" x-text="letter.opening + (letter.jobTitle ? ' I\'m writing to apply for the ' + letter.jobTitle + ' role' : '') + (letter.companyName ? ' at ' + letter.companyName : '') + ((letter.jobTitle || letter.companyName) ? '.' : '')"></p>

                        <p class="mt-4 whitespace-pre-line" x-show="letter.body" x-text="letter.body"></p>

                        <p class="mt-4" x-show="letter.closing" x-text="letter.closing"></p>

                        <p class="mt-6">Sincerely,</p>
                        <p class="mt-1 font-semibold text-foreground" x-text="letter.fullName || 'Your Name'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function resumeBuilder() {
            const CV_KEY = 'krj_resume_builder_cv';
            const LETTER_KEY = 'krj_resume_builder_cover_letter';
            const emptyCv = () => ({
                fullName: '', title: '', email: '', phone: '', location: '', summary: '',
                experience: [{ role: '', company: '', dates: '', bullets: '' }],
                education: [{ school: '', qualification: '', dates: '' }],
                skills: '',
            });
            const emptyLetter = () => ({
                fullName: '', email: '', phone: '',
                date: new Date().toLocaleDateString('en-KE', { year: 'numeric', month: 'long', day: 'numeric' }),
                hiringManager: 'Hiring Manager', companyName: '', jobTitle: '',
                opening: '', body: '',
                closing: "Thank you for considering my application. I'd welcome the chance to discuss how I can contribute to your team.",
            });

            return {
                tab: 'cv',
                cv: emptyCv(),
                letter: emptyLetter(),

                load() {
                    try {
                        const rawCv = window.localStorage.getItem(CV_KEY);
                        if (rawCv) this.cv = { ...emptyCv(), ...JSON.parse(rawCv) };
                    } catch {}
                    try {
                        const rawLetter = window.localStorage.getItem(LETTER_KEY);
                        if (rawLetter) this.letter = { ...emptyLetter(), ...JSON.parse(rawLetter) };
                    } catch {}

                    this.$watch('cv', (value) => {
                        try {
                            window.localStorage.setItem(CV_KEY, JSON.stringify(value));
                        } catch {}
                    });
                    this.$watch('letter', (value) => {
                        try {
                            window.localStorage.setItem(LETTER_KEY, JSON.stringify(value));
                        } catch {}
                    });
                },
            };
        }
    </script>
</x-layouts.app>
