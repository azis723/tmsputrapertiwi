@extends('admin.layouts.admin')

@section('title', 'Tambah Jurusan & Geofencing')
@section('page-title', 'Tambah Jurusan Baru')
@section('page-subtitle', 'Tentukan nama jurusan serta titik koordinat radius geofencing presensi via peta visual')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.majors.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Jurusan</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.majors.store') }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Jurusan</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('name') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kode Jurusan (Singkatan)</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="Contoh: RPL"
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm uppercase">
                @error('code') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Visual Map Geofencing Configuration -->
        <div class="pt-4 border-t border-slate-800 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Konfigurasi Visual Geofencing</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Geser marker pin pada peta di bawah untuk menentukan titik pusat presensi (Default: Pondok Cabe Ilir)</p>
                </div>
                <button type="button" id="btn-reset-cabe" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition self-start sm:self-auto">
                    Reset ke Pondok Cabe Ilir
                </button>
            </div>

            <!-- Leaflet Map Container -->
            <div id="geofence-map" class="w-full h-80 sm:h-96 rounded-2xl border border-slate-700/80 overflow-hidden shadow-inner z-0"></div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="latitude" class="block text-xs font-semibold text-slate-400 mb-1">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" id="latitude" value="{{ old('latitude', $defaultLat) }}" required readonly
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 text-sm font-mono focus:outline-none">
                    @error('latitude') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-xs font-semibold text-slate-400 mb-1">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" id="longitude" value="{{ old('longitude', $defaultLng) }}" required readonly
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 text-sm font-mono focus:outline-none">
                    @error('longitude') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="radius_meters" class="block text-xs font-semibold text-slate-400 mb-1">Radius Toleransi (Meter)</label>
                    <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', 50) }}" min="10" max="5000" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-emerald-400 font-bold text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('radius_meters') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.majors.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan Jurusan & Geofence
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = {{ $defaultLat }};
        const defaultLng = {{ $defaultLng }};
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const radiusInput = document.getElementById('radius_meters');

        const initialLat = parseFloat(latInput.value) || defaultLat;
        const initialLng = parseFloat(lngInput.value) || defaultLng;
        const initialRadius = parseInt(radiusInput.value) || 50;

        // Initialize Leaflet Map
        const map = L.map('geofence-map').setView([initialLat, initialLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Add Draggable Marker
        const marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(map);

        // Add Radius Circle
        const circle = L.circle([initialLat, initialLng], {
            color: '#6366f1',
            fillColor: '#6366f1',
            fillOpacity: 0.25,
            radius: initialRadius
        }).addTo(map);

        // Update when marker dragged
        marker.on('drag', function (e) {
            const pos = e.target.getLatLng();
            latInput.value = pos.lat.toFixed(8);
            lngInput.value = pos.lng.toFixed(8);
            circle.setLatLng(pos);
        });

        // Click on map moves marker
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            circle.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(8);
            lngInput.value = e.latlng.lng.toFixed(8);
        });

        // Radius input update
        radiusInput.addEventListener('input', function () {
            const rad = parseInt(this.value) || 10;
            circle.setRadius(rad);
        });

        // Reset to Pondok Cabe Ilir button
        document.getElementById('btn-reset-cabe').addEventListener('click', function () {
            map.setView([defaultLat, defaultLng], 16);
            marker.setLatLng([defaultLat, defaultLng]);
            circle.setLatLng([defaultLat, defaultLng]);
            latInput.value = defaultLat.toFixed(8);
            lngInput.value = defaultLng.toFixed(8);
        });
    });
</script>
@endpush
