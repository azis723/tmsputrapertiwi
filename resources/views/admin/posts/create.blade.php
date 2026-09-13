@extends('admin.layouts.admin')

@section('title', 'Tulis Artikel')
@section('page-title', 'Tulis Artikel / Berita Baru')
@section('page-subtitle', 'Publikasikan informasi kegiatan dan pengumuman sekolah')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.posts.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Artikel</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <div>
            <label for="title" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Judul Artikel</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Masukkan judul berita..."
                   class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm font-semibold">
            @error('title') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kategori</label>
                <select name="category_id" id="category_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Status Publikasi</label>
                <select name="status" id="status" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Terbitkan (Published)</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draf (Draft)</option>
                </select>
                @error('status') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="thumbnail" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Foto Sampul (Thumbnail Opsional)</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
            @error('thumbnail') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Isi Konten Berita</label>
            <textarea name="content" id="content" rows="8" required placeholder="Tulis isi berita lengkap..."
                      class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm leading-relaxed">{{ old('content') }}</textarea>
            @error('content') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.posts.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan Artikel
            </button>
        </div>
    </form>
</div>
@endsection
