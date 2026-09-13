@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Utama')
@section('page-subtitle', 'Ikhtisar statistik operasional akademik dan presensi harian SMK Putra Pertiwi')

@section('content')
<div class="space-y-8">

    <!-- Active Academic Year & Holiday Alert Banner -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Tahun Akademik Berjalan</p>
                    <h3 class="text-base font-bold text-white mt-0.5">
                        {{ $activeAcademicYear->name ?? 'Belum Diatur' }} - Semester {{ ucfirst($activeAcademicYear->semester ?? '-') }}
                    </h3>
                </div>
            </div>
            <a href="{{ route('admin.academic-years.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">Ubah &rarr;</a>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $todayHoliday ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' }} border flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Status Kalender Hari Ini</p>
                    <h3 class="text-base font-bold text-white mt-0.5">
                        @if ($todayHoliday)
                            <span class="text-amber-400">{{ $todayHoliday->title }} (Libur)</span>
                        @else
                            <span class="text-emerald-400">Hari Pembelajaran Aktif</span>
                        @endif
                    </h3>
                </div>
            </div>
            <a href="{{ route('admin.holidays.index') }}" class="text-xs text-slate-400 hover:text-white font-medium">Kalender &rarr;</a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Siswa -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Siswa Aktif</p>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-3">{{ number_format($totalStudents) }}</h3>
            <p class="text-xs text-slate-500 mt-1">Terdaftar dalam sistem</p>
        </div>

        <!-- Total Kelas -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas Rombel</p>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-white mt-3">{{ number_format($totalClasses) }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ $totalMajors }} Jurusan Keahlian</p>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hadir Hari Ini</p>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-emerald-400 mt-3">{{ number_format($presentCount) }}</h3>
            <p class="text-xs text-slate-500 mt-1">Tepat waktu</p>
        </div>

        <!-- Terlambat Hari Ini -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Terlambat Hari Ini</p>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-amber-400 mt-3">{{ number_format($lateCount) }}</h3>
            <p class="text-xs text-slate-500 mt-1">Lewat batas jam masuk</p>
        </div>
    </div>

    <!-- Attendance Status Pill Summary -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Statistik Kehadiran Hari Ini ({{ date('d F Y') }})</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <p class="text-[11px] text-emerald-400 font-semibold uppercase">Hadir Tepat</p>
                <p class="text-xl font-bold text-white mt-1">{{ $presentCount }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                <p class="text-[11px] text-amber-400 font-semibold uppercase">Terlambat</p>
                <p class="text-xl font-bold text-white mt-1">{{ $lateCount }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20">
                <p class="text-[11px] text-blue-400 font-semibold uppercase">Sakit</p>
                <p class="text-xl font-bold text-white mt-1">{{ $sickCount }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-purple-500/10 border border-purple-500/20">
                <p class="text-[11px] text-purple-400 font-semibold uppercase">Izin</p>
                <p class="text-xl font-bold text-white mt-1">{{ $permissionCount }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 col-span-2 sm:col-span-1">
                <p class="text-[11px] text-rose-400 font-semibold uppercase">Belum Hadir / Alpa</p>
                <p class="text-xl font-bold text-white mt-1">{{ $absentCount }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Feed Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-base text-white">Log Presensi Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan presensi masuk mandiri terkini dari siswa</p>
            </div>
            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition flex items-center gap-2">
                <span>Lihat Semua Rekap</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Kelas & Jurusan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Koordinat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($recentAttendances as $att)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($att->date)) }}</span>
                                <span class="text-xs text-slate-500 block">{{ substr($att->time, 0, 5) }} WIB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-white">{{ $att->student?->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-xs text-slate-500">NIS: {{ $att->student?->nis ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-200">{{ $att->student?->schoolClass?->name ?? '-' }}</span>
                                <span class="text-xs text-indigo-400 block">{{ $att->student?->schoolClass?->major?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $att->status_badge }}">
                                    {{ $att->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                @if ($att->latitude && $att->longitude)
                                    <span class="font-mono text-[11px]">{{ round($att->latitude, 5) }}, {{ round($att->longitude, 5) }}</span>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium">Belum ada riwayat presensi yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
