<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        // If already authenticated, redirect to proper dashboard
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        $activeTab = $request->query('role', 'student');
        if (!in_array($activeTab, ['student', 'admin'])) {
            $activeTab = 'student';
        }

        $schoolName = SchoolSetting::get('school_name', 'SMK Putra Pertiwi');

        return view('auth.login', compact('activeTab', 'schoolName'));
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            if ($user->role !== 'admin') {
                Auth::guard('web')->logout();
                return back()->withInput()->with('error', 'Akses hanya untuk administrator.');
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        throw ValidationException::withMessages([
            'email' => __('Email atau kata sandi administrator salah.'),
        ]);
    }

    public function studentLogin(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'], // Can be NIS or Email
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->input('identifier');
        $password = $request->input('password');

        $student = Student::where('nis', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$student || !Hash::check($password, $student->password)) {
            throw ValidationException::withMessages([
                'identifier' => __('NIS/Email atau kata sandi siswa tidak sesuai.'),
            ]);
        }

        if ($student->status !== 'active') {
            return back()->withInput()->with('error', 'Akun siswa Anda dinonaktifkan. Silakan hubungi admin sekolah.');
        }

        Auth::guard('student')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'))
            ->with('success', 'Selamat datang di Portal Siswa, ' . $student->name . '!');
    }

    public function adminLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout dari panel Admin.');
    }

    public function studentLogout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'Anda telah berhasil logout dari Portal Siswa.');
    }
}
