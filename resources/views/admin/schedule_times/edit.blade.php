@extends('admin.layouts.admin')

@section('title', 'Edit Slot Waktu')
@section('page-title', 'Edit Slot Waktu Belajar')
@section('page-subtitle', 'Perbarui hari atau batasan jam')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.schedule-times.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Slot Waktu</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.schedule-times.update', $scheduleTime) }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="day" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Hari Operasional</label>
            <select name="day" id="day" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                <option value="Monday" {{ old('day', $scheduleTime->day) === 'Monday' ? 'selected' : '' }}>Senin</option>
                <option value="Tuesday" {{ old('day', $scheduleTime->day) === 'Tuesday' ? 'selected' : '' }}>Selasa</option>
                <option value="Wednesday" {{ old('day', $scheduleTime->day) === 'Wednesday' ? 'selected' : '' }}>Rabu</option>
                <option value="Thursday" {{ old('day', $scheduleTime->day) === 'Thursday' ? 'selected' : '' }}>Kamis</option>
                <option value="Friday" {{ old('day', $scheduleTime->day) === 'Friday' ? 'selected' : '' }}>Jumat</option>
                <option value="Saturday" {{ old('day', $scheduleTime->day) === 'Saturday' ? 'selected' : '' }}>Sabtu</option>
            </select>
            @error('day') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="start_time" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Mulai</label>
                <input type="time" name="start_time" id="start_time" value="{{ old('start_time', substr($scheduleTime->start_time, 0, 5)) }}" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm font-mono">
                @error('start_time') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="end_time" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Selesai</label>
                <input type="time" name="end_time" id="end_time" value="{{ old('end_time', substr($scheduleTime->end_time, 0, 5)) }}" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm font-mono">
                @error('end_time') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.schedule-times.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Perbarui Slot Waktu
            </button>
        </div>
    </form>
</div>
@endsection
