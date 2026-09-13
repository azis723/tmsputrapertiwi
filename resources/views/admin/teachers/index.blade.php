@extends('admin.layouts.admin')

@section('title', 'Data Guru')
@section('page-title', 'Data Guru (Referensi)')
@section('page-subtitle', 'Entitas referensi tenaga pengajar dan wali kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Guru</h2>
            <p class="text-xs text-slate-400 mt-1">Total {{ $teachers->total() }} tenaga pengajar tercatat</p>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Guru Baru</span>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru, NIP, atau email..."
                   class="flex-1 px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition">
                Cari
            </button>
            @if ($search)
                <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 text-sm transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Teachers Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Nama Guru</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Peran Rombel / Mapel</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($teachers as $teacher)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-white">{{ $teacher->name }}</div>
                                <div class="text-xs text-slate-500">{{ $teacher->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-indigo-300">
                                {{ $teacher->nip ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                {{ $teacher->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="text-slate-300">{{ $teacher->classes_count }} Wali Kelas</span> &bull;
                                <span class="text-indigo-400">{{ $teacher->subjects_count }} Mapel</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $teacher->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $teacher->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Hapus data guru ini?')" class="inline">
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
                                Belum ada data guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($teachers->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
