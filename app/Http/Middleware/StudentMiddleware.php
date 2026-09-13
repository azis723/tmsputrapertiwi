<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('student')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized student access.'], 403);
            }
            return redirect()->route('student.login')->with('error', 'Silakan login sebagai Siswa terlebih dahulu.');
        }

        $student = Auth::guard('student')->user();
        if ($student->status !== 'active') {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')->with('error', 'Akun siswa Anda sedang tidak aktif. Hubungi administrator.');
        }

        return $next($request);
    }
}
