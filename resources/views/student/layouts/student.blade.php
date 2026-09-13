<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Siswa') - SMK Putra Pertiwi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-950 font-sans text-slate-100 min-h-screen flex flex-col pb-20 md:pb-0 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-600/30">
                    PP
                </div>
                <div>
                    <h1 class="font-bold text-sm text-white tracking-wide">SMK PUTRA PERTIWI</h1>
                    <p class="text-[11px] text-indigo-400 font-medium">Portal Siswa</p>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('student.dashboard') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Dashboard
                </a>
                <a href="{{ route('student.attendance') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.attendance') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Presensi Mandiri
                </a>
                <a href="{{ route('student.history') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.history') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Riwayat
                </a>
                <a href="{{ route('student.schedule') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.schedule') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Jadwal
                </a>
                <a href="{{ route('student.profile') }}" 
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.profile') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                    Profil
                </a>
            </nav>

            <!-- User Badge & Logout -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-semibold text-white">{{ auth()->guard('student')->user()->name }}</p>
                    <p class="text-[11px] text-slate-400">NIS: {{ auth()->guard('student')->user()->nis }}</p>
                </div>
                <form method="POST" action="{{ route('student.logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Biometric Face Enrollment Warning if empty -->
    @if (!auth()->guard('student')->user()->hasEnrolledFace() && !request()->routeIs('student.face-registration'))
        <div class="bg-amber-500/10 border-b border-amber-500/30 text-amber-300 text-xs sm:text-sm py-3 px-4">
            <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Perhatian:</strong> Anda belum mendaftarkan biometrik wajah. Presensi memerlukan verifikasi wajah.</span>
                </div>
                <a href="{{ route('student.face-registration') }}" class="px-3 py-1 bg-amber-500 text-slate-950 font-semibold rounded-lg hover:bg-amber-400 shrink-0 text-xs transition">
                    Daftar Sekarang &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- Main Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-400 hover:text-rose-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800/80 px-2 py-2 z-40 flex items-center justify-around">
        <a href="{{ route('student.dashboard') }}" 
           class="flex flex-col items-center gap-1 p-2 rounded-xl transition {{ request()->routeIs('student.dashboard') ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>

        <a href="{{ route('student.attendance') }}" 
           class="flex flex-col items-center gap-1 p-2 rounded-xl transition {{ request()->routeIs('student.attendance') ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0a2 2 0 100-4 2 2 0 000 4zm-8 4v-1a4 4 0 014-4h4a4 4 0 014 4v1H4z"/></svg>
            <span class="text-[10px] font-medium">Presensi</span>
        </a>

        <a href="{{ route('student.history') }}" 
           class="flex flex-col items-center gap-1 p-2 rounded-xl transition {{ request()->routeIs('student.history') ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[10px] font-medium">Riwayat</span>
        </a>

        <a href="{{ route('student.schedule') }}" 
           class="flex flex-col items-center gap-1 p-2 rounded-xl transition {{ request()->routeIs('student.schedule') ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[10px] font-medium">Jadwal</span>
        </a>

        <a href="{{ route('student.profile') }}" 
           class="flex flex-col items-center gap-1 p-2 rounded-xl transition {{ request()->routeIs('student.profile') ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
