<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user()->load(['schoolClass.major', 'schoolClass.homeroomTeacher']);
        return view('student.profile', compact('student'));
    }

    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:student'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $student->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }
}
