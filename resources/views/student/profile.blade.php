@extends('student.layouts.student')

@section('title', 'Profil Siswa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white">Profil & Keamanan Akun</h2>
        <p class="text-xs text-slate-400 mt-1">Informasi data diri dan pengaturan keamanan kata sandi</p>
    </div>

    <!-- Student Info Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-3">Informasi Siswa</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block">Nama Lengkap</span>
                <span class="text-sm font-bold text-white mt-0.5 block">{{ $student->name }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block">NIS (Nomor Induk Siswa)</span>
                <span class="text-sm font-mono font-bold text-indigo-400 mt-0.5 block">{{ $student->nis }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block">Kelas Rombongan Belajar</span>
                <span class="text-sm font-bold text-white mt-0.5 block">{{ $student->schoolClass?->name ?? '-' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                <span class="text-slate-500 block">Kompetensi Keahlian / Jurusan</span>
                <span class="text-sm font-bold text-indigo-400 mt-0.5 block">{{ $student->major?->name ?? '-' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 sm:col-span-2">
                <span class="text-slate-500 block">Email Terdaftar</span>
                <span class="text-sm text-slate-300 mt-0.5 block">{{ $student->email }}</span>
            </div>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <div>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Ubah Kata Sandi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Gunakan kombinasi kata sandi yang aman minimal 6 karakter</p>
        </div>

        <form method="POST" action="{{ route('student.profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" id="current_password" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('current_password') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
                @error('password') <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm">
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition">
                    Simpan Kata Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
