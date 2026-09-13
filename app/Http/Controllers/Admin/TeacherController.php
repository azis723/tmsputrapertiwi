<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Teacher::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $teachers = $query->withCount(['classes', 'subjects'])->latest()->paginate(10);

        return view('admin.teachers.index', compact('teachers', 'search'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:100|unique:teachers,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:teachers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Teacher::create($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:100', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('teachers', 'email')->ignore($teacher->id)],
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
