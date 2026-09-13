@extends('admin.layouts.admin')

@section('title', 'Data Kelas')
@section('page-title', 'Manajemen Kelas & Rombel')
@section('page-subtitle', 'Pengelompokan siswa berdasarkan jurusan, tahun akademik, dan wali kelas')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Rombongan Belajar</h2>
            <p class="text-xs text-slate-400 mt-1">Total {{ $classes->total() }} kelas terdaftar</p>
        </div>
        <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kelas Baru</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label for="major_id" class="block text-xs font-semibold text-slate-400 mb-1">Jurusan</label>
                <select name="major_id" id="major_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Jurusan --</option>
                    @foreach ($majors as $m)
                        <option value="{{ $m->id }}" {{ $majorId == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="academic_year_id" class="block text-xs font-semibold text-slate-400 mb-1">Tahun Akademik</label>
                <select name="academic_year_id" id="academic_year_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Periode --</option>
                    @foreach ($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>{{ $ay->name }} - {{ ucfirst($ay->semester) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('admin.classes.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Classes Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Nama Kelas</th>
                        <th class="px-6 py-4">Jurusan</th>
                        <th class="px-6 py-4">Tahun Akademik</th>
                        <th class="px-6 py-4">Wali Kelas</th>
                        <th class="px-6 py-4">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($classes as $class)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-white">
                                {{ $class->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ $class->major?->name ?? '-' }} ({{ $class->major?->code ?? '-' }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                {{ $class->academicYear?->name ?? '-' }} ({{ ucfirst($class->academicYear?->semester ?? '-') }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-slate-200">{{ $class->homeroomTeacher?->name ?? 'Belum ditentukan' }}</span>
                                @if($class->homeroomTeacher)
                                    <span class="text-xs text-slate-500 block">NIP: {{ $class->homeroomTeacher->nip ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-emerald-400">{{ $class->students_count }}</span>
                                <span class="text-xs text-slate-500">Siswa</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?')" class="inline">
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
                                Belum ada data kelas yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($classes->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
