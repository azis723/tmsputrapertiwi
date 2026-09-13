@extends('student.layouts.student')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="space-y-6" x-data="{ photoModal: false, activePhotoUrl: '' }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Riwayat Presensi Pribadi</h2>
            <p class="text-xs text-slate-400 mt-1">Catatan riwayat kehadiran, waktu, dan foto verifikasi presensi Anda</p>
        </div>

        <!-- Month Filter -->
        <form method="GET" action="{{ route('student.history') }}" class="flex items-center gap-2">
            <input type="month" name="month" value="{{ $month }}"
                   class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
            <p class="text-[11px] font-bold text-slate-400 uppercase">Total Kehadiran</p>
            <p class="text-xl font-bold text-white mt-1">{{ $summary['total'] }} Hari</p>
        </div>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
            <p class="text-[11px] font-bold text-emerald-400 uppercase">Hadir Tepat</p>
            <p class="text-xl font-bold text-white mt-1">{{ $summary['present'] }} Hari</p>
        </div>
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20">
            <p class="text-[11px] font-bold text-amber-400 uppercase">Terlambat</p>
            <p class="text-xl font-bold text-white mt-1">{{ $summary['late'] }} Hari</p>
        </div>
        <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20">
            <p class="text-[11px] font-bold text-blue-400 uppercase">Izin / Sakit</p>
            <p class="text-xl font-bold text-white mt-1">{{ $summary['sick'] + $summary['permission'] }} Hari</p>
        </div>
    </div>

    <!-- History Records Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Jam Presensi</th>
                        <th class="px-6 py-4">Status Kehadiran</th>
                        <th class="px-6 py-4">Foto Verifikasi</th>
                        <th class="px-6 py-4">Koordinat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($attendances as $row)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-white">{{ $row->date->format('d F Y') }}</span>
                                <span class="text-xs text-slate-500 block">{{ $row->date->isoFormat('dddd') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-400">
                                {{ substr($row->time, 0, 5) }} WIB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $row->status_badge }}">
                                    {{ $row->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($row->photo_path)
                                    <button type="button" 
                                            @click="activePhotoUrl = '{{ asset('storage/' . $row->photo_path) }}'; photoModal = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-indigo-300 border border-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Lihat Snapshot</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-600">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400 font-mono">
                                @if ($row->latitude && $row->longitude)
                                    {{ round($row->latitude, 5) }}, {{ round($row->longitude, 5) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Belum ada catatan presensi pada bulan ini.
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

    <!-- Photo Modal -->
    <div x-show="photoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="photoModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-5 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-white">Snapshot Presensi Biometrik</h4>
                <button type="button" @click="photoModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="rounded-2xl overflow-hidden bg-slate-950 aspect-[4/3] flex items-center justify-center border border-slate-800">
                <img :src="activePhotoUrl" alt="Bukti Presensi" class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</div>
@endsection
