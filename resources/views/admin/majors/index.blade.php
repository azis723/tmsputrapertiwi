@extends('admin.layouts.admin')

@section('title', 'Jurusan & Geofencing')
@section('page-title', 'Manajemen Jurusan & Geofencing')
@section('page-subtitle', 'Pengaturan koordinat titik pusat dan radius toleransi presensi per kompetensi keahlian')

@section('content')
<div class="space-y-6">

    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Daftar Jurusan & Titik Geofence</h2>
            <p class="text-xs text-slate-400 mt-1">Koordinat acuan presensi di sekitar area Pondok Cabe Ilir</p>
        </div>
        <a href="{{ route('admin.majors.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Jurusan Baru</span>
        </a>
    </div>

    <!-- Jurusan Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($majors as $major)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <span class="px-3 py-1 text-xs font-bold rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 tracking-wider">
                            {{ $major->code }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.majors.edit', $major) }}" class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800/80 rounded-lg transition" title="Edit Jurusan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.majors.destroy', $major) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition" title="Hapus Jurusan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-white mt-3">{{ $major->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $major->classes_count }} Kelas Rombongan Belajar</p>

                    <!-- Geofence Info -->
                    <div class="mt-4 pt-4 border-t border-slate-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Radius Toleransi:</span>
                            <span class="font-semibold text-emerald-400 font-mono">{{ $major->radius_meters }} meter</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Titik Pusat (Lat, Lng):</span>
                            <span class="font-mono text-slate-300">{{ round($major->latitude, 5) }}, {{ round($major->longitude, 5) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-3">
                    <a href="{{ route('admin.majors.edit', $major) }}" class="w-full py-2 px-3 text-center bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition block">
                        Buka Visual Map Editor &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/40 border border-slate-800 rounded-2xl">
                <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <p class="text-sm font-medium">Belum ada jurusan yang dikonfigurasi.</p>
                <a href="{{ route('admin.majors.create') }}" class="inline-block mt-3 text-xs text-indigo-400 hover:text-indigo-300 font-semibold">Tambah Jurusan Pertama &rarr;</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
