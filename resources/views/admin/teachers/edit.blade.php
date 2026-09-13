@extends('admin.layouts.admin')

@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru: ' . $teacher->name)
@section('page-subtitle', 'Perbarui data kontak dan status mengajar guru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.teachers.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Data Guru</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap Guru</label>
                <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nip" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">NIP (Opsional)</label>
                <input type="text" name="nip" id="nip" value="{{ old('nip', $teacher->nip) }}"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm font-mono">
                @error('nip') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('email') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor Telepon / WA</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $teacher->phone) }}"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('phone') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Domisili</label>
            <textarea name="address" id="address" rows="2"
                      class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">{{ old('address', $teacher->address) }}</textarea>
            @error('address') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="status" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Status Mengajar</label>
            <select name="status" id="status" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                <option value="active" {{ old('status', $teacher->status) === 'active' ? 'selected' : '' }}>Aktif Mengajar</option>
                <option value="inactive" {{ old('status', $teacher->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.teachers.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Perbarui Data Guru
            </button>
        </div>
    </form>
</div>
@endsection
