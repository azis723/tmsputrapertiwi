@extends('admin.layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem & Presensi')
@section('page-subtitle', 'Konfigurasi batas waktu keterlambatan, koordinat default sekolah, dan identitas instansi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-8">
        @csrf
        @method('PUT')

        <!-- Bagian Presensi & Jam Masuk -->
        <div class="space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Aturan Waktu & Keterlambatan Presensi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Siswa yang melakukan presensi melewati batas jam ini akan otomatis berstatus Terlambat (Late)</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="late_cutoff_time" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Batas Jam Masuk (Late Cutoff)</label>
                    <input type="time" name="late_cutoff_time" id="late_cutoff_time" value="{{ old('late_cutoff_time', $settings['late_cutoff_time']) }}" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-amber-400 font-bold text-base font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="text-xs text-slate-500 mt-1">Presensi setelah jam ini otomatis berstatus <strong>Terlambat</strong>.</p>
                    @error('late_cutoff_time') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Bagian Koordinat Default Pondok Cabe Ilir -->
        <div class="space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Koordinat Acuan Default (Pondok Cabe Ilir)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Titik koordinat acuan awal saat membuat jurusan baru</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="default_latitude" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Default Latitude</label>
                    <input type="number" step="0.00000001" name="default_latitude" id="default_latitude" value="{{ old('default_latitude', $settings['default_latitude']) }}" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 font-mono text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('default_latitude') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="default_longitude" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Default Longitude</label>
                    <input type="number" step="0.00000001" name="default_longitude" id="default_longitude" value="{{ old('default_longitude', $settings['default_longitude']) }}" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 font-mono text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('default_longitude') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Bagian Profil Sekolah -->
        <div class="space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Identitas Instansi Sekolah</h3>
                <p class="text-xs text-slate-400 mt-0.5">Nama dan informasi kontak yang tampil pada kop laporan PDF dan portal</p>
            </div>

            <div>
                <label for="school_name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Sekolah</label>
                <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $settings['school_name']) }}" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('school_name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="school_phone" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor Telepon Sekolah</label>
                    <input type="text" name="school_phone" id="school_phone" value="{{ old('school_phone', $settings['school_phone']) }}"
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                </div>

                <div>
                    <label for="school_address" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Sekolah</label>
                    <textarea name="school_address" id="school_address" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">{{ old('school_address', $settings['school_address']) }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end">
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan Semua Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
