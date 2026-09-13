<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount(['classes'])->orderBy('created_at', 'desc')->get();
        return view('admin.academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic_years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'semester' => 'required|in:ganjil,genap',
            'is_active' => 'boolean',
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        AcademicYear::create([
            'name' => $validated['name'],
            'semester' => $validated['semester'],
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'semester' => 'required|in:ganjil,genap',
            'is_active' => 'boolean',
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update([
            'name' => $validated['name'],
            'semester' => $validated['semester'],
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', "Tahun akademik {$academicYear->name} ({$academicYear->semester}) diaktifkan.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun akademik berhasil dihapus.');
    }
}
