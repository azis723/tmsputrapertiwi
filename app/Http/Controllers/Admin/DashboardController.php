<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalStudents = Student::where('status', 'active')->count();
        $totalClasses = SchoolClass::count();
        $totalMajors = Major::count();
        $totalTeachers = Teacher::count();

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $todayHoliday = Holiday::whereDate('date', $today)->first();

        $todayAttendancesCount = Attendance::whereDate('date', $today)->count();
        $presentCount = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $lateCount = Attendance::whereDate('date', $today)->where('status', 'late')->count();
        $sickCount = Attendance::whereDate('date', $today)->where('status', 'sick')->count();
        $permissionCount = Attendance::whereDate('date', $today)->where('status', 'permission')->count();
        $absentCount = max(0, $totalStudents - $todayAttendancesCount);

        $recentAttendances = Attendance::with(['student.schoolClass.major'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(10)
            ->get();

        $majors = Major::withCount(['classes'])->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalClasses',
            'totalMajors',
            'totalTeachers',
            'activeAcademicYear',
            'todayHoliday',
            'todayAttendancesCount',
            'presentCount',
            'lateCount',
            'sickCount',
            'permissionCount',
            'absentCount',
            'recentAttendances',
            'majors'
        ));
    }
}
