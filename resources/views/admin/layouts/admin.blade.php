<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - SMK Putra Pertiwi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-950 font-sans text-slate-100 min-h-screen flex antialiased selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-cloak
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 bottom-0 left-0 w-72 bg-slate-900/95 backdrop-blur-xl border-r border-slate-800/80 z-50 flex flex-col transition-transform duration-300 ease-in-out">
        
        <!-- Brand Header -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800/80 gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-400 text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-600/30 shrink-0">
                PP
            </div>
            <div class="overflow-hidden">
                <h2 class="font-bold text-sm text-white tracking-wide truncate">SMK PUTRA PERTIWI</h2>
                <p class="text-[11px] text-indigo-400 font-medium tracking-wider uppercase">Portal Administrator</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 custom-scrollbar">
            
            <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Utama</p>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.attendances.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.attendances.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rekap & Laporan</span>
            </a>

            <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider pt-5 mb-2">Presensi & Geofence</p>

            <a href="{{ route('admin.majors.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.majors.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Jurusan & Geofencing</span>
            </a>

            <a href="{{ route('admin.students.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Data Siswa & Biometrik</span>
            </a>

            <a href="{{ route('admin.holidays.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.holidays.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Hari Libur Sekolah</span>
            </a>

            <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider pt-5 mb-2">Akademik & Jadwal</p>

            <a href="{{ route('admin.classes.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.classes.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Data Kelas</span>
            </a>

            <a href="{{ route('admin.teachers.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.teachers.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Guru (Referensi)</span>
            </a>

            <a href="{{ route('admin.academic-years.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.academic-years.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tahun Akademik</span>
            </a>

            <a href="{{ route('admin.subjects.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Mata Pelajaran</span>
            </a>

            <a href="{{ route('admin.schedule-times.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.schedule-times.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Slot Waktu Belajar</span>
            </a>

            <a href="{{ route('admin.schedules.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.schedules.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Jadwal Pembelajaran</span>
            </a>

            <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider pt-5 mb-2">Konfigurasi</p>

            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan Sistem</span>
            </a>

            <a href="{{ route('admin.posts.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.posts.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span>Pengumuman & Berita</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Manajemen Admin</span>
            </a>
        </div>

        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-sm text-indigo-400 shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-72 flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-20 bg-slate-900/60 backdrop-blur-xl border-b border-slate-800/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-2 text-slate-400 hover:text-white lg:hidden rounded-lg hover:bg-slate-800 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight">@yield('page-title', 'Admin Panel')</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', 'Sistem Absensi & Geofencing SMK Putra Pertiwi')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 text-xs text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ date('d M Y') }}</span>
                </div>
                <div class="h-8 w-px bg-slate-800 mx-1 hidden sm:block"></div>
                <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 uppercase tracking-wider">
                    Admin
                </span>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center justify-between shadow-lg shadow-emerald-950/20">
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
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center justify-between shadow-lg shadow-rose-950/20">
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
    </div>

    @stack('scripts')
</body>
</html>
