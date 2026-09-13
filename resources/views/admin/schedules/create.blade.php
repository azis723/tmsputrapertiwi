@extends('admin.layouts.admin')

@section('title', 'Tambah Jadwal')
@section('page-title', 'Tambah Jadwal Pembelajaran')
@section('page-subtitle', 'Hubungkan kelas, mata pelajaran, guru, tahun ajaran, dan slot waktu')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.schedules.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Jadwal</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.schedules.store') }}" class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kelas Rombel</label>
                <select name="class_id" id="class_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->major?->code }})
                        </option>
                    @endforeach
                </select>
                @error('class_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="subject_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                <select name="subject_id" id="subject_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->code }})
                        </option>
                    @endforeach
                </select>
                @error('subject_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="teacher_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Guru Pengajar (Opsional)</label>
                <select name="teacher_id" id="teacher_id" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    <option value="">-- Ikuti Pengampu Mapel / Pilih --</option>
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="academic_year_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tahun Akademik</label>
                <select name="academic_year_id" id="academic_year_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                    @foreach ($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : ($ay->is_active ? 'selected' : '') }}>
                            {{ $ay->name }} - {{ ucfirst($ay->semester) }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="schedule_time_id" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Slot Hari & Jam</label>
            <select name="schedule_time_id" id="schedule_time_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                <option value="">-- Pilih Hari & Jam --</option>
                @foreach ($scheduleTimes as $st)
                    <option value="{{ $st->id }}" {{ old('schedule_time_id') == $st->id ? 'selected' : '' }}>
                        {{ match($st->day) {
                            'Monday' => 'Senin',
                            'Tuesday' => 'Selasa',
                            'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis',
                            'Friday' => 'Jumat',
                            'Saturday' => 'Sabtu',
                            default => $st->day
                        } }} : {{ $st->formatted_time }} WIB
                    </option>
                @endforeach
            </select>
            @error('schedule_time_id') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.schedules.index') }}" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                Simpan Jadwal
            </button>
        </div>
    </form>
</div>
@endsection
