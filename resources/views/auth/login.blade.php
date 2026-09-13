<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk - {{ $schoolName ?? 'SMK Putra Pertiwi' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 font-sans text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    <!-- Ambient Background Glows -->
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md my-8 relative z-10" x-data="{ tab: '{{ $activeTab ?? 'student' }}' }">
        <!-- School Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-400 text-white shadow-xl shadow-indigo-600/30 mb-4 border border-indigo-400/30">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $schoolName ?? 'SMK Putra Pertiwi' }}</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Absensi & Manajemen Akademik Terpadu</p>
        </div>

        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/50">
            <!-- Role Toggle Switcher -->
            <div class="grid grid-cols-2 p-1 bg-slate-950/80 rounded-2xl border border-slate-800/80 mb-6">
                <button type="button" 
                        @click="tab = 'student'" 
                        :class="tab === 'student' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25 font-semibold' : 'text-slate-400 hover:text-slate-200'"
                        class="py-2.5 text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Portal Siswa
                </button>
                <button type="button" 
                        @click="tab = 'admin'" 
                        :class="tab === 'admin' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25 font-semibold' : 'text-slate-400 hover:text-slate-200'"
                        class="py-2.5 text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Administrator
                </button>
            </div>

            <!-- STUDENT LOGIN FORM -->
            <div x-show="tab === 'student'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-white">Login Siswa</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Gunakan NIS atau Email siswa yang telah terdaftar.</p>
                </div>

                <form method="POST" action="{{ route('student.login.process') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="student_identifier" class="block text-xs font-medium text-slate-300 mb-1.5">NIS atau Email Siswa</label>
                        <input type="text" 
                               name="identifier" 
                               id="student_identifier" 
                               value="{{ old('identifier') }}"
                               required 
                               autocomplete="username"
                               placeholder="Contoh: 1001 atau siswa@putrapertiwi.sch.id"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border @error('identifier') border-rose-500 @else border-slate-800 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition">
                        @error('identifier')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="student_password" class="block text-xs font-medium text-slate-300">Kata Sandi</label>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="student_password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-0">
                            <span class="text-xs text-slate-400">Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition duration-200 active:scale-[0.99] flex items-center justify-center gap-2 mt-6">
                        <span>Masuk ke Portal Siswa</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-800/80 text-center">
                    <p class="text-xs text-slate-500">Akun demo siswa: <span class="text-slate-300 font-mono">1001</span> / <span class="text-slate-300 font-mono">password</span></p>
                </div>
            </div>

            <!-- ADMIN LOGIN FORM -->
            <div x-show="tab === 'admin'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="mb-5">
                    <h2 class="text-lg font-semibold text-white">Login Administrator</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Akses panel kontrol dan manajemen presensi sekolah.</p>
                </div>

                <form method="POST" action="{{ route('admin.login.process') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="admin_email" class="block text-xs font-medium text-slate-300 mb-1.5">Email Administrator</label>
                        <input type="email" 
                               name="email" 
                               id="admin_email" 
                               value="{{ old('email') }}"
                               required 
                               autocomplete="email"
                               placeholder="admin@putrapertiwi.sch.id"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border @error('email') border-rose-500 @else border-slate-800 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition">
                        @error('email')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_password" class="block text-xs font-medium text-slate-300 mb-1.5">Kata Sandi</label>
                        <input type="password" 
                               name="password" 
                               id="admin_password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-0">
                            <span class="text-xs text-slate-400">Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition duration-200 active:scale-[0.99] flex items-center justify-center gap-2 mt-6">
                        <span>Masuk ke Panel Admin</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-800/80 text-center">
                    <p class="text-xs text-slate-500">Akun demo admin: <span class="text-slate-300 font-mono">admin@putrapertiwi.sch.id</span> / <span class="text-slate-300 font-mono">password</span></p>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <p class="text-center text-xs text-slate-500 mt-8">
            &copy; {{ date('Y') }} {{ $schoolName ?? 'SMK Putra Pertiwi' }}. All rights reserved.
        </p>
    </div>
</body>
</html>
