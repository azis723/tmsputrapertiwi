<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        $month = $request->input('month', Carbon::today()->format('Y-m'));

        $query = Attendance::where('student_id', $student->id);

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereYear('date', $date->year)->whereMonth('date', $date->month);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        $summary = [
            'total' => Attendance::where('student_id', $student->id)->count(),
            'present' => Attendance::where('student_id', $student->id)->where('status', 'present')->count(),
            'late' => Attendance::where('student_id', $student->id)->where('status', 'late')->count(),
            'sick' => Attendance::where('student_id', $student->id)->where('status', 'sick')->count(),
            'permission' => Attendance::where('student_id', $student->id)->where('status', 'permission')->count(),
        ];

        return view('student.history', compact('attendances', 'summary', 'month'));
    }
}
