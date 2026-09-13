<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user()->load('schoolClass.major');
        $today = Carbon::today()->toDateString();

        // 1. Check if face is registered
        if (!$student->hasEnrolledFace()) {
            return redirect()->route('student.face-registration')
                ->with('error', 'Anda harus mendaftarkan biometrik wajah terlebih dahulu sebelum dapat melakukan presensi.');
        }

        // 2. Check if today is a holiday
        $holiday = Holiday::where('date', $today)->first();
        if ($holiday) {
            return view('student.attendance_blocked', [
                'student' => $student,
                'reason' => 'holiday',
                'holiday' => $holiday,
                'title' => 'Hari Libur Sekolah',
                'message' => "Hari ini ({$today}) adalah {$holiday->title}. Presensi tidak dibuka.",
            ]);
        }

        // 3. Check if already attended today (Strict 1x per day rule)
        $existing = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return view('student.attendance_blocked', [
                'student' => $student,
                'reason' => 'already_attended',
                'attendance' => $existing,
                'title' => 'Sudah Melakukan Presensi',
                'message' => "Anda telah melakukan presensi pada pukul " . substr($existing->time, 0, 5) . " dengan status {$existing->status_label}.",
            ]);
        }

        $major = $student->major;
        if (!$major) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Data jurusan kelas Anda belum diatur oleh admin.');
        }

        $lateCutoff = SchoolSetting::get('late_cutoff_time', '07:15');

        return view('student.attendance', compact('student', 'major', 'lateCutoff'));
    }

    public function store(Request $request)
    {
        $student = Auth::guard('student')->user()->load('schoolClass.major');
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i:s');

        // Check if face enrolled
        if (!$student->hasEnrolledFace()) {
            return response()->json([
                'success' => false,
                'message' => 'Biometrik wajah belum terdaftar.',
            ], 422);
        }

        // Check holiday
        if (Holiday::where('date', $today)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini adalah hari libur sekolah. Presensi ditutup.',
            ], 422);
        }

        // Check already attended
        if (Attendance::where('student_id', $student->id)->where('date', $today)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi hari ini.',
            ], 422);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|string', // Base64 image
            'face_verified' => 'required|boolean',
        ]);

        if (!$validated['face_verified']) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi biometrik wajah tidak cocok dengan data pendaftaran Anda.',
            ], 422);
        }

        $major = $student->major;
        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi jurusan belum lengkap.',
            ], 422);
        }

        // Calculate Haversine Distance (in meters)
        $distance = $this->calculateHaversineDistance(
            $validated['latitude'],
            $validated['longitude'],
            $major->latitude,
            $major->longitude
        );

        if ($distance > $major->radius_meters) {
            $diffMeters = round($distance - $major->radius_meters);
            return response()->json([
                'success' => false,
                'distance' => round($distance, 1),
                'radius' => $major->radius_meters,
                'message' => "Lokasi Anda berada " . round($distance, 1) . " meter dari titik pusat jurusan (melebihi radius toleransi {$major->radius_meters} meter sebesar {$diffMeters} meter). Silakan mendekat ke area sekolah.",
            ], 422);
        }

        // Save Photo from Base64
        $photoPath = null;
        if (preg_match('/^data:image\/(\w+);base64,/', $validated['photo'], $type)) {
            $data = substr($validated['photo'], strpos($validated['photo'], ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif, webp

            $data = base64_decode($data);
            if ($data !== false) {
                $filename = 'attendance_' . $student->id . '_' . date('Ymd_His') . '_' . Str::random(6) . '.' . $type;
                Storage::disk('public')->put('attendances/' . $filename, $data);
                $photoPath = 'attendances/' . $filename;
            }
        }

        // Determine Status based on late_cutoff_time
        $lateCutoff = SchoolSetting::get('late_cutoff_time', '07:15');
        $status = 'present';
        if (strtotime($currentTime) > strtotime($lateCutoff)) {
            $status = 'late';
        }

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'time' => $currentTime,
            'status' => $status,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo_path' => $photoPath,
            'notes' => 'Presensi mandiri Geofencing + Face Recognition',
        ]);

        return response()->json([
            'success' => true,
            'status' => $attendance->status,
            'status_label' => $attendance->status_label,
            'time' => substr($attendance->time, 0, 5),
            'distance' => round($distance, 1),
            'message' => "Presensi berhasil dicatat! Status: {$attendance->status_label}",
            'redirect' => route('student.dashboard'),
        ]);
    }

    /**
     * Calculate Distance between two points in meters using Haversine formula
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // in meters

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
