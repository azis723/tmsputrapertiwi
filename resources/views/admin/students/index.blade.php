@extends('admin.layouts.admin')

@section('title', 'Data Siswa')
@section('page-title', 'Manajemen Siswa & Biometrik Wajah')
@section('page-subtitle', 'Pengelolaan data akun siswa dan status pendaftaran biometrik face recognition')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Siswa</h2>
            <p class="text-xs text-slate-400 mt-1">Total {{ $students->total() }} siswa terdaftar dalam sistem</p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Siswa Baru</span>
        </a>
    </div>

    <!-- Filter Form Card -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-400 mb-1">Cari Siswa</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama / NIS / Email..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-400 mb-1">Rombongan Belajar / Kelas</label>
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
                <label for="status" class="block text-xs font-semibold text-slate-400 mb-1">Status Akun</label>
                <select name="status" id="status" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('admin.students.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-400 text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">NIS</th>
                        <th class="px-6 py-4">Kelas & Jurusan</th>
                        <th class="px-6 py-4">Status Biometrik Wajah</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-white">{{ $student->name }}</div>
                                <div class="text-xs text-slate-500">{{ $student->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-indigo-300">
                                {{ $student->nis }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-white">{{ $student->schoolClass?->name ?? '-' }}</span>
                                <span class="text-xs text-indigo-400 block">{{ $student->schoolClass?->major?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($student->hasEnrolledFace())
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Terdaftar
                                        </span>
                                        <form method="POST" action="{{ route('admin.students.reset-face', $student) }}" onsubmit="return confirm('Apakah Anda yakin ingin me-reset biometrik wajah siswa ini? Siswa harus merekam wajah ulang.')" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[11px] text-amber-400 hover:text-amber-300 hover:underline">
                                                Reset Wajah
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 border border-slate-700 text-slate-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        Belum Terdaftar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $student->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $student->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition" title="Edit Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus siswa ini beserta seluruh data presensinya?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition" title="Hapus Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada siswa yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
