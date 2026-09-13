@extends('student.layouts.student')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Student Hero Greeting Card -->
    <div class="bg-gradient-to-br from-indigo-900/60 via-slate-900/90 to-slate-900/90 border border-indigo-500/20 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 text-xs font-semibold">
                    <span>Selamat Datang</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ $student->name }}</h2>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs sm:text-sm text-slate-300">
                    <span>NIS: <strong class="font-mono text-white">{{ $student->nis }}</strong></span>
                    <span>&bull;</span>
                    <span>Kelas: <strong class="text-white">{{ $student->schoolClass?->name ?? 'Belum ada kelas' }}</strong></span>
                    <span>&bull;</span>
                    <span>Jurusan: <strong class="text-indigo-400">{{ $student->major?->name ?? '-' }}</strong></span>
                </div>
                @if($student->schoolClass?->homeroomTeacher)
                    <p class="text-xs text-slate-400 pt-1">
                        Wali Kelas: <span class="text-slate-200">{{ $student->schoolClass->homeroomTeacher->name }}</span>
                    </p>
                @endif
            </div>

            <!-- Face Registration Status Chip -->
            <div class="shrink-0">
                @if ($student->hasEnrolledFace())
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Biometrik Wajah Aktif</span>
                        </div>
                        <a href="{{ route('student.face-registration') }}" 
                           title="Pindai ulang biometrik wajah"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Pindai Ulang</span>
                        </a>
                    </div>
                @else
                    <a href="{{ route('student.face-registration') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0a2 2 0 100-4 2 2 0 000 4zm-8 4v-1a4 4 0 014-4h4a4 4 0 014 4v1H4z"/></svg>
                        <span>Daftarkan Wajah Sekarang &rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Today Attendance Action Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-6">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white">Status Presensi Hari Ini</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ date('l, d F Y') }} &bull; Batas Masuk: <span class="font-mono text-amber-400 font-semibold">{{ $lateCutoff }} WIB</span></p>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono text-slate-400 block" id="realtime-clock"></span>
            </div>
        </div>

        @if ($todayHoliday)
            <!-- Holiday Case -->
            <div class="p-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center space-y-2">
                <div class="w-12 h-12 mx-auto rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="text-base font-bold text-white">Hari Ini Libur Sekolah</h4>
                <p class="text-xs text-amber-300">{{ $todayHoliday->title }}. Presensi kehadiran ditiadakan.</p>
            </div>

        @elseif ($todayAttendance)
            <!-- Already Attended Case -->
            <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $todayAttendance->status_badge }}">
                            {{ $todayAttendance->status_label }}
                        </span>
                        <h4 class="text-lg font-bold text-white mt-1.5">Anda Sudah Melakukan Presensi</h4>
                        <p class="text-xs text-slate-400">Tercatat pada pukul <strong class="font-mono text-white">{{ substr($todayAttendance->time, 0, 5) }} WIB</strong></p>
                    </div>
                </div>
                <a href="{{ route('student.history') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition text-center">
                    Lihat Bukti Presensi &rarr;
                </a>
            </div>

        @else
            <!-- Not Attended Yet -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 p-6 rounded-2xl bg-indigo-950/30 border border-indigo-800/40">
                <div class="space-y-1">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 border border-rose-500/20 text-rose-400">
                        Belum Melakukan Presensi
                    </span>
                    <h4 class="text-lg font-bold text-white pt-1">Buka Kamera & GPS Untuk Presensi</h4>
                    <p class="text-xs text-slate-400">Pastikan Anda berada di area radius sekolah ({{ $student->major?->radius_meters ?? 50 }} meter) dan wajah terdeteksi jelas.</p>
                </div>

                @if ($student->hasEnrolledFace())
                    <a href="{{ route('student.attendance') }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-600/30 transition duration-200 active:scale-95 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0a2 2 0 100-4 2 2 0 000 4zm-8 4v-1a4 4 0 014-4h4a4 4 0 014 4v1H4z"/></svg>
                        <span>Presensi Mandiri Sekarang</span>
                    </a>
                @else
                    <a href="{{ route('student.face-registration') }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-xl shadow-amber-500/20 transition shrink-0">
                        <span>Daftarkan Wajah Dulu &rarr;</span>
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Monthly Stats Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Hadir Tepat</p>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-2">{{ $monthlyStats['present'] }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Bulan {{ date('F Y') }}</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Terlambat</p>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-2">{{ $monthlyStats['late'] }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Bulan {{ date('F Y') }}</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Sakit</p>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-2">{{ $monthlyStats['sick'] }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Bulan {{ date('F Y') }}</p>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Izin</p>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-2">{{ $monthlyStats['permission'] }}</h3>
            <p class="text-[11px] text-slate-500 mt-1">Bulan {{ date('F Y') }}</p>
        </div>
    </div>

    <!-- Quick Shortcuts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('student.schedule') }}" class="p-5 rounded-2xl bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">Jadwal Belajar</h4>
                    <p class="text-xs text-slate-400">Mata pelajaran mingguan</p>
                </div>
            </div>
            <span class="text-slate-500 group-hover:text-white transition">&rarr;</span>
        </a>

        <a href="{{ route('student.history') }}" class="p-5 rounded-2xl bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">Riwayat Presensi</h4>
                    <p class="text-xs text-slate-400">Rekapitulasi kehadiran pribadi</p>
                </div>
            </div>
            <span class="text-slate-500 group-hover:text-white transition">&rarr;</span>
        </a>

        <a href="{{ route('student.profile') }}" class="p-5 rounded-2xl bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">Profil & Keamanan</h4>
                    <p class="text-xs text-slate-400">Ganti kata sandi akun</p>
                </div>
            </div>
            <span class="text-slate-500 group-hover:text-white transition">&rarr;</span>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        const el = document.getElementById('realtime-clock');
        if (el) el.textContent = str;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush
