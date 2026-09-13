<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(15);
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'title' => 'required|string|max:255',
        ]);

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'date' => ['required', 'date', Rule::unique('holidays', 'date')->ignore($holiday->id)],
            'title' => 'required|string|max:255',
        ]);

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
