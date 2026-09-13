@extends('admin.layouts.admin')

@section('title', 'Berita & Pengumuman')
@section('page-title', 'Pengumuman & Berita Sekolah')
@section('page-subtitle', 'Publikasi artikel, informasi kegiatan, dan pengumuman akademik')

@section('content')
<div class="space-y-6" x-data="{ addCategoryModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Artikel & Berita</h2>
            <p class="text-xs text-slate-400 mt-1">Total {{ $posts->total() }} artikel publikasi</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @click="addCategoryModal = true" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl transition">
                + Kategori Baru
            </button>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tulis Artikel Baru</span>
            </a>
        </div>
    </div>

    <!-- Category Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <span class="text-xs text-slate-500 shrink-0 font-medium">Kategori:</span>
        @forelse ($categories as $cat)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-slate-900 border border-slate-800 text-slate-300 shrink-0">
                <span>{{ $cat->name }} ({{ $cat->posts_count }})</span>
                @if ($cat->posts_count == 0)
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-500 hover:text-rose-400">&times;</button>
                    </form>
                @endif
            </div>
        @empty
            <span class="text-xs text-slate-600">Belum ada kategori</span>
        @endforelse
    </div>

    <!-- Posts Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Judul Artikel</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Buat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white line-clamp-1">{{ $post->title }}</div>
                                <div class="text-xs text-slate-500 font-mono">slug: {{ $post->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ $post->category?->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md {{ $post->status === 'published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                {{ $post->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Hapus artikel ini?')" class="inline">
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
                                Belum ada artikel atau pengumuman yang ditulis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="addCategoryModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="addCategoryModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <h4 class="text-base font-bold text-white">Tambah Kategori Baru</h4>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="cat_name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Kategori</label>
                    <input type="text" name="name" id="cat_name" required placeholder="Contoh: Prestasi Siswa"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addCategoryModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
