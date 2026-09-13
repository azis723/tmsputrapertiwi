@extends('admin.layouts.admin')

@section('title', 'Tambah Tahun Akademik')
@section('page-title', 'Tambah Tahun Akademik')
@section('page-subtitle', 'Setup periode kalender pembelajaran sekolah')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.academic-years.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Tahun Akademik</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.academic-years.store') }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Tahun Ajaran</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: 2026/2027"
                   class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
            @error('name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="semester" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Semester</label>
            <select name="semester" id="semester" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                <option value="ganjil" {{ old('semester') === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                <option value="genap" {{ old('semester') === 'genap' ? 'selected' : '' }}>Semester Genap</option>
            </select>
            @error('semester') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-5 h-5 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-slate-300 font-medium">Jadikan tahun akademik aktif saat ini</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.academic-years.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
