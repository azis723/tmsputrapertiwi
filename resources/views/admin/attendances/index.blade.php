@extends('admin.layouts.admin')

@section('title', 'Laporan Presensi')
@section('page-title', 'Rekapitulasi & Laporan Presensi Siswa')
@section('page-subtitle', 'Monitoring presensi mandiri siswa berbasis Geofencing dan Face Recognition')

@section('content')
<div class="space-y-6" x-data="{ photoModal: false, activePhotoUrl: '' }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Laporan Presensi</h2>
            <p class="text-xs text-slate-400 mt-1">Periode: {{ date('d/m/Y', strtotime($fromDate)) }} s/d {{ date('d/m/Y', strtotime($toDate)) }}</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.attendances.export.pdf', request()->query()) }}" 
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-rose-600/25 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export PDF</span>
            </a>
            <a href="{{ route('admin.attendances.export.excel', request()->query()) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Excel / CSV</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5">
        <form method="GET" action="{{ route('admin.attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
            <div>
                <label for="from_date" class="block text-xs font-semibold text-slate-400 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="to_date" class="block text-xs font-semibold text-slate-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-400 mb-1">Kelas</label>
                <select name="class_id" id="class_id" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="major_id" class="block text-xs font-semibold text-slate-400 mb-1">Jurusan</label>
                <select name="major_id" id="major_id" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach ($majors as $m)
                        <option value="{{ $m->id }}" {{ $majorId == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-slate-400 mb-1">Status Kehadiran</label>
                <select name="status" id="status" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Status --</option>
                    <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Hadir Tepat</option>
                    <option value="late" {{ $status == 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="sick" {{ $status == 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="permission" {{ $status == 'permission' ? 'selected' : '' }}>Izin</option>
                    <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('admin.attendances.index') }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-slate-400">Total Rekap</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-emerald-400">Hadir Tepat</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-amber-400">Terlambat</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['late'] }}</p>
        </div>
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-blue-400">Sakit</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['sick'] }}</p>
        </div>
        <div class="bg-purple-500/10 border border-purple-500/20 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-purple-400">Izin</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['permission'] }}</p>
        </div>
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-3 text-center">
            <p class="text-[10px] uppercase font-bold text-rose-400">Alpa</p>
            <p class="text-lg font-bold text-white mt-0.5">{{ $stats['absent'] }}</p>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tanggal & Jam</th>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Kelas & Jurusan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Foto Verifikasi</th>
                        <th class="px-6 py-4">Titik Koordinat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($attendances as $row)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-white">{{ date('d/m/Y', strtotime($row->date)) }}</span>
                                <span class="text-xs text-slate-500 block">{{ substr($row->time, 0, 5) }} WIB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-white">{{ $row->student?->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-xs text-slate-500 font-mono">NIS: {{ $row->student?->nis ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-200">{{ $row->student?->schoolClass?->name ?? '-' }}</span>
                                <span class="text-xs text-indigo-400 block">{{ $row->student?->schoolClass?->major?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $row->status_badge }}">
                                    {{ $row->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($row->photo_path)
                                    <button type="button" 
                                            @click="activePhotoUrl = '{{ asset('storage/' . $row->photo_path) }}'; photoModal = true"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-indigo-300 border border-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Lihat Foto</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-600">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                @if ($row->latitude && $row->longitude)
                                    <span class="font-mono text-[11px]">{{ round($row->latitude, 5) }}, {{ round($row->longitude, 5) }}</span>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada data presensi pada kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attendances->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Photo Verification Modal -->
    <div x-show="photoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="photoModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl p-4 max-w-md w-full shadow-2xl space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-white">Foto Bukti Presensi Biometrik</h4>
                <button type="button" @click="photoModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="rounded-xl overflow-hidden bg-slate-950 aspect-[4/3] flex items-center justify-center border border-slate-800">
                <img :src="activePhotoUrl" alt="Snapshot Presensi" class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</div>
@endsection
