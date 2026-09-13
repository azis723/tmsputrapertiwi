<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->input('class_id');
        $academicYearId = $request->input('academic_year_id');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYearId && $activeYear) {
            $academicYearId = $activeYear->id;
        }

        $classes = SchoolClass::with('major')->get();
        $academicYears = AcademicYear::all();

        $query = Schedule::with(['schoolClass.major', 'subject', 'teacher', 'academicYear', 'scheduleTime']);

        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $schedules = $query->get()->sortBy(function ($schedule) {
            $days = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
            $dayOrder = $days[$schedule->scheduleTime?->day ?? 'Monday'] ?? 7;
            return sprintf('%d-%s', $dayOrder, $schedule->scheduleTime?->start_time ?? '00:00');
        });

        return view('admin.schedules.index', compact('schedules', 'classes', 'academicYears', 'classId', 'academicYearId'));
    }

    public function create()
    {
        $classes = SchoolClass::with('major')->get();
        $subjects = Subject::all();
        $teachers = Teacher::where('status', 'active')->get();
        $academicYears = AcademicYear::all();
        $scheduleTimes = ScheduleTime::ordered()->get();

        return view('admin.schedules.create', compact('classes', 'subjects', 'teachers', 'academicYears', 'scheduleTimes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'schedule_time_id' => 'required|exists:schedules_times,id',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index', [
            'class_id' => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id']
        ])->with('success', 'Jadwal pembelajaran berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $classes = SchoolClass::with('major')->get();
        $subjects = Subject::all();
        $teachers = Teacher::where('status', 'active')->get();
        $academicYears = AcademicYear::all();
        $scheduleTimes = ScheduleTime::ordered()->get();

        return view('admin.schedules.edit', compact('schedule', 'classes', 'subjects', 'teachers', 'academicYears', 'scheduleTimes'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'schedule_time_id' => 'required|exists:schedules_times,id',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index', [
            'class_id' => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id']
        ])->with('success', 'Jadwal pembelajaran berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal pembelajaran berhasil dihapus.');
    }
}
