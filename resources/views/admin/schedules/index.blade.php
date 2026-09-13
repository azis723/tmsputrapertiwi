@extends('admin.layouts.admin')

@section('title', 'Jadwal Pembelajaran')
@section('page-title', 'Jadwal Pembelajaran Kelas')
@section('page-subtitle', 'Matriks jadwal KBM per rombongan belajar, mata pelajaran, guru, dan waktu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Jadwal Pembelajaran</h2>
            <p class="text-xs text-slate-400 mt-1">Kelola pembagian mata pelajaran mingguan per kelas</p>
        </div>
        <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Jadwal Baru</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-400 mb-1">Pilih Kelas</label>
                <select name="class_id" id="class_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->major?->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="academic_year_id" class="block text-xs font-semibold text-slate-400 mb-1">Tahun Akademik</label>
                <select name="academic_year_id" id="academic_year_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach ($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} - {{ ucfirst($ay->semester) }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition">
                    Tampilkan Jadwal
                </button>
                <a href="{{ route('admin.schedules.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Hari & Jam</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Mata Pelajaran</th>
                        <th class="px-6 py-4">Guru Pengampu</th>
                        <th class="px-6 py-4">Tahun Akademik</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($schedules as $sched)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ match($sched->scheduleTime?->day) {
                                        'Monday' => 'Senin',
                                        'Tuesday' => 'Selasa',
                                        'Wednesday' => 'Rabu',
                                        'Thursday' => 'Kamis',
                                        'Friday' => 'Jumat',
                                        'Saturday' => 'Sabtu',
                                        default => $sched->scheduleTime?->day ?? '-'
                                    } }}
                                </span>
                                <span class="text-xs font-mono text-slate-400 block mt-1">
                                    {{ $sched->scheduleTime?->formatted_time ?? '-' }} WIB
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-white">
                                {{ $sched->schoolClass?->name ?? '-' }}
                                <span class="text-xs text-slate-500 block font-normal">{{ $sched->schoolClass?->major?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-white">{{ $sched->subject?->name ?? '-' }}</span>
                                <span class="text-xs font-mono text-slate-500 block">{{ $sched->subject?->code ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                {{ $sched->teacher?->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                {{ $sched->academicYear?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.schedules.edit', $sched) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.schedules.destroy', $sched) }}" onsubmit="return confirm('Hapus sesi jadwal ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada jadwal pembelajaran yang ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
