@extends('student.layouts.student')

@section('title', 'Jadwal Pembelajaran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Jadwal Pembelajaran Kelas</h2>
            <p class="text-xs text-slate-400 mt-1">
                Kelas: <strong class="text-white">{{ $student->schoolClass?->name ?? '-' }}</strong> &bull; 
                Jurusan: <strong class="text-indigo-400">{{ $student->major?->name ?? '-' }}</strong>
            </p>
        </div>
        @if ($activeYear)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                T.A {{ $activeYear->name }} ({{ ucfirst($activeYear->semester) }})
            </span>
        @endif
    </div>

    @if (empty($schedulesByDay))
        <div class="py-16 text-center text-slate-500 bg-slate-900/40 border border-slate-800 rounded-3xl">
            <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-medium">{{ $message ?? 'Belum ada jadwal pembelajaran yang diatur untuk kelas Anda.' }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($schedulesByDay as $dayName => $daySchedules)
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <h3 class="text-base font-bold text-white tracking-wide">{{ $dayName }}</h3>
                        <span class="text-xs text-indigo-400 font-semibold">{{ $daySchedules->count() }} Mata Pelajaran</span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($daySchedules as $s)
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xs font-bold text-indigo-400">
                                        {{ $s->scheduleTime?->formatted_time ?? '-' }} WIB
                                    </span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono">
                                        {{ $s->subject?->code ?? '-' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-white">{{ $s->subject?->name ?? '-' }}</h4>
                                <p class="text-xs text-slate-400">
                                    Pengajar: <span class="text-slate-300">{{ $s->teacher?->name ?? 'Belum Ditugaskan' }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
