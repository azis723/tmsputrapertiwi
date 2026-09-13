<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $classId = $request->input('class_id');
        $status = $request->input('status');

        $query = Student::with(['schoolClass.major']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $students = $query->latest()->paginate(10);
        $classes = SchoolClass::with('major')->get();

        return view('admin.students.index', compact('students', 'classes', 'search', 'classId', 'status'));
    }

    public function create()
    {
        $classes = SchoolClass::with('major')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'nis' => 'required|string|max:50|unique:students,nis',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive',
        ]);

        Student::create([
            'class_id' => $validated['class_id'],
            'nis' => $validated['nis'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::with('major')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'nis' => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
            'password' => 'nullable|string|min:6',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'class_id' => $validated['class_id'],
            'nis' => $validated['nis'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function resetFace(Student $student)
    {
        $student->update(['face_embedding' => null]);
        return back()->with('success', "Data biometrik wajah siswa {$student->name} berhasil di-reset. Siswa dapat melakukan pendaftaran wajah ulang.");
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
