<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SchoolSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'school_name' => SchoolSetting::get('school_name', 'SMK Putra Pertiwi'),
            'late_cutoff_time' => SchoolSetting::get('late_cutoff_time', '07:15'),
            'default_latitude' => SchoolSetting::get('default_latitude', -6.3472),
            'default_longitude' => SchoolSetting::get('default_longitude', 106.7645),
            'school_address' => SchoolSetting::get('school_address', 'Jl. Pondok Cabe Ilir, Pamulang, Tangerang Selatan'),
            'school_phone' => SchoolSetting::get('school_phone', '(021) 7490000'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'late_cutoff_time' => 'required|date_format:H:i',
            'default_latitude' => 'required|numeric|between:-90,90',
            'default_longitude' => 'required|numeric|between:-180,180',
            'school_address' => 'nullable|string',
            'school_phone' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            SchoolSetting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sekolah dan presensi berhasil disimpan.');
    }
}
