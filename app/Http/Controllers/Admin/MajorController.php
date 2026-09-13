<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::withCount(['classes'])->latest()->get();
        $defaultLat = SchoolSetting::get('default_latitude', -6.3472);
        $defaultLng = SchoolSetting::get('default_longitude', 106.7645);

        return view('admin.majors.index', compact('majors', 'defaultLat', 'defaultLng'));
    }

    public function create()
    {
        $defaultLat = SchoolSetting::get('default_latitude', -6.3472);
        $defaultLng = SchoolSetting::get('default_longitude', 106.7645);

        return view('admin.majors.create', compact('defaultLat', 'defaultLng'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:majors,code',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:5|max:5000',
        ]);

        Major::create($validated);

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan dan konfigurasi geofencing berhasil disimpan.');
    }

    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('majors', 'code')->ignore($major->id)],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:5|max:5000',
        ]);

        $major->update($validated);

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan dan konfigurasi geofencing berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}
