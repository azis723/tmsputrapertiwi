<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user()->load(['schoolClass.major', 'schoolClass.homeroomTeacher']);
        $today = Carbon::today()->toDateString();

        $todayHoliday = Holiday::whereDate('date', $today)->first();
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        $monthlyAttendances = Attendance::where('student_id', $student->id)
            ->whereYear('date', Carbon::today()->year)
            ->whereMonth('date', Carbon::today()->month)
            ->get();

        $monthlyStats = [
            'present' => $monthlyAttendances->where('status', 'present')->count(),
            'late' => $monthlyAttendances->where('status', 'late')->count(),
            'sick' => $monthlyAttendances->where('status', 'sick')->count(),
            'permission' => $monthlyAttendances->where('status', 'permission')->count(),
        ];

        $lateCutoff = SchoolSetting::get('late_cutoff_time', '07:15');

        return view('student.dashboard', compact(
            'student',
            'todayHoliday',
            'todayAttendance',
            'monthlyStats',
            'lateCutoff'
        ));
    }
}
