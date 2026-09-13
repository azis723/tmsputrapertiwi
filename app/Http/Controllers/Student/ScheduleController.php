<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user()->load('schoolClass');
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$student->class_id) {
            return view('student.schedule', [
                'schedulesByDay' => [],
                'message' => 'Anda belum terdaftar dalam kelas rombongan belajar manapun.',
            ]);
        }

        $schedules = Schedule::with(['subject', 'teacher', 'scheduleTime'])
            ->where('class_id', $student->class_id)
            ->when($activeYear, function ($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $dayLabels = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $schedulesByDay = [];
        foreach ($days as $day) {
            $daySchedules = $schedules->filter(function ($item) use ($day) {
                return $item->scheduleTime?->day === $day;
            })->sortBy(function ($item) {
                return $item->scheduleTime?->start_time ?? '00:00';
            });

            if ($daySchedules->count() > 0) {
                $schedulesByDay[$dayLabels[$day]] = $daySchedules;
            }
        }

        return view('student.schedule', compact('schedulesByDay', 'student', 'activeYear'));
    }
}
