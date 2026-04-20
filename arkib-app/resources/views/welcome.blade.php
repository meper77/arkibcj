<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Sistem Arkib UiTM') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="antialiased min-h-screen bg-uitm-purple-900 text-white font-sans">
        <div class="relative min-h-screen flex flex-col overflow-hidden bg-gradient-to-br from-uitm-purple-900 via-uitm-purple-800 to-uitm-purple-700">
            {{-- Decorative gold orbs --}}
            <div aria-hidden="true" class="pointer-events-none absolute -top-32 -right-32 h-96 w-96 rounded-full bg-uitm-gold-400/20 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full bg-uitm-gold-500/10 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(253,185,19,0.08),transparent_50%)]"></div>

            {{-- Top nav --}}
            <header class="relative z-10 w-full">
                <nav class="max-w-6xl mx-auto flex items-center justify-between px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-uitm-gold-400 flex items-center justify-center text-uitm-purple-900 font-bold text-lg shadow-lg ring-1 ring-uitm-gold-300">
                            UiTM
                        </div>
                        <span class="hidden sm:inline text-sm font-medium text-uitm-gold-100 tracking-wide">Sistem Arkib</span>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-2">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-uitm-gold-400 text-uitm-purple-900 text-sm font-semibold shadow hover:bg-uitm-gold-300 transition">
                                    Dashboard
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 transition">
                                    Log Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center px-4 py-2 rounded-md bg-uitm-gold-400 text-uitm-purple-900 text-sm font-semibold shadow hover:bg-uitm-gold-300 transition">
                                        Daftar
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>
            </header>

            {{-- Hero --}}
            <main class="relative z-10 flex-1 flex items-center">
                <div class="max-w-6xl mx-auto w-full px-6 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-uitm-gold-400/15 border border-uitm-gold-400/30 text-uitm-gold-200 text-xs font-medium tracking-wider uppercase">
                            <span class="h-1.5 w-1.5 rounded-full bg-uitm-gold-400"></span>
                            Universiti Teknologi MARA
                        </span>
                        <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight">
                            Sistem <span class="text-uitm-gold-400">Arkib</span><br/>UiTM
                        </h1>
                        <div class="mt-4 h-1 w-24 bg-uitm-gold-400 rounded-full"></div>
                        <p class="mt-6 text-lg text-uitm-gold-50/80 max-w-lg leading-relaxed">
                            Pengurusan rekod, pemisahan dan pelupusan fail rasmi UiTM secara berpusat, selamat dan teratur.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-uitm-gold-400 text-uitm-purple-900 font-semibold shadow-lg hover:bg-uitm-gold-300 transition">
                                    Buka Dashboard
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-uitm-gold-400 text-uitm-purple-900 font-semibold shadow-lg hover:bg-uitm-gold-300 transition">
                                    Log Masuk
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 015.25 2h5.5A2.25 2.25 0 0113 4.25v2a.75.75 0 01-1.5 0v-2a.75.75 0 00-.75-.75h-5.5a.75.75 0 00-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 00.75-.75v-2a.75.75 0 011.5 0v2A2.25 2.25 0 0110.75 18h-5.5A2.25 2.25 0 013 15.75V4.25z"/><path d="M19 10a.75.75 0 00-.22-.53l-3.25-3.25a.75.75 0 10-1.06 1.06l1.97 1.97H8.75a.75.75 0 000 1.5h7.69l-1.97 1.97a.75.75 0 101.06 1.06l3.25-3.25A.75.75 0 0019 10z"/></svg>
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- Decorative card --}}
                    <div class="relative hidden lg:block">
                        <div class="absolute inset-0 bg-uitm-gold-400/20 blur-2xl rounded-3xl"></div>
                        <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8 shadow-2xl">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="h-2 w-2 rounded-full bg-rose-400"></div>
                                <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                                <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
                                    <span class="text-xs font-mono text-uitm-gold-200">100-UiTM(INFO. 1/1)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-200 font-medium">APPROVE</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
                                    <span class="text-xs font-mono text-uitm-gold-200">100-UiTM(ADM. 2/3)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-200 font-medium">PENDING</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
                                    <span class="text-xs font-mono text-uitm-gold-200">100-UiTM(STF. 4/2)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-200 font-medium">APPROVE</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
                                    <span class="text-xs font-mono text-uitm-gold-200">100-UiTM(STD. 5/1)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-200 font-medium">DECLINE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="relative z-10 border-t border-white/10">
                <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between text-xs text-uitm-gold-100/70">
                    <span>&copy; {{ date('Y') }} Universiti Teknologi MARA</span>
                    <span class="font-medium tracking-wider uppercase">Usaha Taqwa Mulia</span>
                </div>
            </footer>
        </div>
    </body>
</html>
