@extends('admin.layouts.admin')

@section('title', 'Tambah Siswa Baru')
@section('page-title', 'Tambah Siswa Baru')
@section('page-subtitle', 'Pendaftaran akun siswa untuk akses presensi dan jadwal')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.students.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Data Siswa</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.students.store') }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="nis" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" id="nis" value="{{ old('nis') }}" required placeholder="Contoh: 1001"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm font-mono">
                @error('nis') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap Siswa</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Nama Siswa"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email Siswa</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="siswa@putrapertiwi.sch.id"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('email') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kelas / Rombongan Belajar</label>
                <select name="class_id" id="class_id" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} - {{ $c->major?->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kata Sandi Awal</label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('password') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Status Akun</label>
                <select name="status" id="status" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.students.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan Data Siswa
            </button>
        </div>
    </form>
</div>
@endsection
