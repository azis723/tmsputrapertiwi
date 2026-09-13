@extends('admin.layouts.admin')

@section('title', 'Edit Mata Pelajaran')
@section('page-title', 'Edit Mata Pelajaran: ' . $subject->name)
@section('page-subtitle', 'Perbarui detail nama, kode, atau guru pengampu')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.subjects.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Mata Pelajaran</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="code" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kode Mapel</label>
            <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}" required
                   class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm uppercase font-mono">
            @error('code') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Mata Pelajaran</label>
            <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" required
                   class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
            @error('name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="teacher_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Guru Pengampu Utama</label>
            <select name="teacher_id" id="teacher_id" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                <option value="">-- Belum Ditugaskan / Pilih Guru --</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" {{ old('teacher_id', $subject->teacher_id) == $t->id ? 'selected' : '' }}>
                        {{ $t->name }} (NIP: {{ $t->nip ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('teacher_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.subjects.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Perbarui Mapel
            </button>
        </div>
    </form>
</div>
@endsection
