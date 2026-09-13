@extends('admin.layouts.admin')

@section('title', 'Slot Waktu Belajar')
@section('page-title', 'Master Slot Waktu Belajar')
@section('page-subtitle', 'Konfigurasi hari operasional, jam mulai, dan jam selesai pembelajaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Slot Waktu</h2>
            <p class="text-xs text-slate-400 mt-1">Acuan pembagian jam pelajaran harian</p>
        </div>
        <a href="{{ route('admin.schedule-times.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Slot Waktu</span>
        </a>
    </div>

    <!-- Schedule Times Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Hari</th>
                        <th class="px-6 py-4">Jam Mulai</th>
                        <th class="px-6 py-4">Jam Selesai</th>
                        <th class="px-6 py-4">Rentang Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($scheduleTimes as $st)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-white">
                                {{ match($st->day) {
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu',
                                    default => $st->day
                                } }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-400">
                                {{ substr($st->start_time, 0, 5) }} WIB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-indigo-400">
                                {{ substr($st->end_time, 0, 5) }} WIB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $st->formatted_time }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.schedule-times.edit', $st) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.schedule-times.destroy', $st) }}" onsubmit="return confirm('Hapus slot waktu ini?')" class="inline">
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
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Belum ada slot waktu belajar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
