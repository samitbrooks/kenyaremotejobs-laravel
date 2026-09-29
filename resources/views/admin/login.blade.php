<x-layouts.app title="Admin Sign In — KenyaRemoteJobs" description="Secure administrator portal authentication">
    <div class="min-h-[80vh] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-950">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            {{-- Brand header --}}
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-[#2e3760] to-[#ff3131] shadow-lg shadow-red-500/20">
                        <x-icon name="shield" class="h-6 w-6 text-white" />
                    </span>
                    <span class="text-xl font-black tracking-tight text-white">
                        Kenya<span class="text-[#ff3131]">RemoteJobs</span>
                    </span>
                </a>
            </div>

            <div class="mt-4 text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-950/80 border border-red-800/60 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-red-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Restricted Area &bull; Administrator Credentials Required
                </span>
                <h1 class="mt-3 text-2xl font-extrabold text-white tracking-tight">Admin Portal Sign In</h1>
                <p class="mt-1 text-xs text-slate-400">
                    Authorized personnel only. All access attempts are logged and monitored.
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
                {{-- Flash Messages --}}
                @if (session('info'))
                    <div class="mb-5 rounded-2xl border border-sky-800/60 bg-sky-950/60 p-3.5 text-xs font-semibold text-sky-200 flex items-start gap-2.5">
                        <span class="text-sky-400 font-bold">&#9432;</span>
                        <div class="leading-relaxed">{{ session('info') }}</div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-emerald-800/60 bg-emerald-950/60 p-3.5 text-xs font-semibold text-emerald-200 flex items-start gap-2.5">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <div class="leading-relaxed">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-2xl border border-red-800/60 bg-red-950/60 p-3.5 text-xs font-semibold text-red-200 flex items-start gap-2.5">
                        <span class="text-red-400 font-bold">&cross;</span>
                        <div class="leading-relaxed">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-800/60 bg-red-950/60 p-3.5 text-xs font-semibold text-red-200">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="redirectTo" value="{{ $redirectTo ?? '/admin' }}">

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Administrator Email
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                value="{{ old('email', request()->query('email', auth()->user()?->email)) }}"
                                placeholder="admin@kenyaremotejobs.com"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white placeholder-slate-500 shadow-inner outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                            />
                        </div>
                    </div>

                    <div x-data="{ show: false }">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                Password
                            </label>
                            <button
                                type="button"
                                @click="show = !show"
                                class="text-xs text-slate-400 hover:text-slate-200 transition"
                                x-text="show ? 'Hide' : 'Show'"
                            ></button>
                        </div>
                        <div class="mt-1.5 relative">
                            <input
                                id="password"
                                name="password"
                                :type="show ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••••••"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white placeholder-slate-500 shadow-inner outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded border-slate-700 bg-slate-950 text-red-600 focus:ring-red-500 focus:ring-offset-slate-900"
                            />
                            <span class="text-xs text-slate-400">Keep me signed in</span>
                        </label>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-red-600 to-[#2e3760] py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-red-600/30 transition hover:from-red-500 hover:to-[#3b477a] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-red-500/50"
                        >
                            Sign In to Administrator Portal &rarr;
                        </button>
                    </div>
                </form>

                <div class="mt-6 border-t border-slate-800 pt-4 text-center">
                    <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-slate-200 transition">
                        &larr; Return to KenyaRemoteJobs.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
