<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceRegistrationController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        return view('student.face_registration', compact('student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'face_embedding' => 'required|string',
        ]);

        $student = Auth::guard('student')->user();

        // Validate JSON format
        $embedding = json_decode($request->input('face_embedding'), true);
        if (!is_array($embedding) || count($embedding) < 68) {
            return response()->json([
                'success' => false,
                'message' => 'Format data biometrik wajah tidak valid. Pastikan wajah terdeteksi jelas di depan kamera.',
            ], 422);
        }

        $student->update([
            'face_embedding' => $request->input('face_embedding'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran biometrik wajah berhasil disimpan!',
            'redirect' => route('student.dashboard'),
        ]);
    }
}
