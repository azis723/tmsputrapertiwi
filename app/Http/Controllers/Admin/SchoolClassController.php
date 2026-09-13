<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $majorId = $request->input('major_id');
        $academicYearId = $request->input('academic_year_id');

        $query = SchoolClass::with(['major', 'academicYear', 'homeroomTeacher'])->withCount('students');

        if ($majorId) {
            $query->where('major_id', $majorId);
        }
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $classes = $query->latest()->paginate(10);
        $majors = Major::all();
        $academicYears = AcademicYear::all();

        return view('admin.classes.index', compact('classes', 'majors', 'academicYears', 'majorId', 'academicYearId'));
    }

    public function create()
    {
        $majors = Major::all();
        $academicYears = AcademicYear::all();
        $teachers = Teacher::where('status', 'active')->get();

        return view('admin.classes.create', compact('majors', 'academicYears', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'major_id' => 'required|exists:majors,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class)
    {
        $majors = Major::all();
        $academicYears = AcademicYear::all();
        $teachers = Teacher::where('status', 'active')->get();

        return view('admin.classes.edit', compact('class', 'majors', 'academicYears', 'teachers'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'major_id' => 'required|exists:majors,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
