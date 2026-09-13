@extends('student.layouts.student')

@section('title', $title ?? 'Presensi')

@section('content')
<div class="max-w-md mx-auto my-8 space-y-6">
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 text-center shadow-xl space-y-6">
        <div class="w-16 h-16 mx-auto rounded-2xl {{ $reason === 'holiday' ? 'bg-amber-500/10 border-amber-500/20 text-amber-400' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' }} border flex items-center justify-center">
            @if ($reason === 'holiday')
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>

        <div class="space-y-2">
            <h2 class="text-xl font-bold text-white">{{ $title }}</h2>
            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">{{ $message }}</p>
        </div>

        @if (isset($attendance))
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-left text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-400">Tanggal:</span>
                    <span class="font-bold text-white">{{ date('d/m/Y', strtotime($attendance->date)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jam Presensi:</span>
                    <span class="font-mono text-white">{{ substr($attendance->time, 0, 5) }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Status Kehadiran:</span>
                    <span class="font-bold text-emerald-400">{{ $attendance->status_label }}</span>
                </div>
            </div>
        @endif

        <div class="pt-2 flex flex-col gap-2">
            <a href="{{ route('student.dashboard') }}" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs shadow-lg transition">
                Kembali ke Dashboard
            </a>
            <a href="{{ route('student.history') }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition">
                Lihat Riwayat Presensi
            </a>
        </div>
    </div>
</div>
@endsection
