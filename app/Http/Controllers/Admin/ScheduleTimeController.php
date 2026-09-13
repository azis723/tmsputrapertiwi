<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleTime;
use Illuminate\Http\Request;

class ScheduleTimeController extends Controller
{
    public function index()
    {
        $scheduleTimes = ScheduleTime::orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->orderBy('start_time')
            ->get();

        return view('admin.schedule_times.index', compact('scheduleTimes'));
    }

    public function create()
    {
        return view('admin.schedule_times.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        ScheduleTime::create($validated);

        return redirect()->route('admin.schedule-times.index')->with('success', 'Slot waktu belajar berhasil ditambahkan.');
    }

    public function edit(ScheduleTime $scheduleTime)
    {
        return view('admin.schedule_times.edit', compact('scheduleTime'));
    }

    public function update(Request $request, ScheduleTime $scheduleTime)
    {
        $validated = $request->validate([
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $scheduleTime->update($validated);

        return redirect()->route('admin.schedule-times.index')->with('success', 'Slot waktu belajar berhasil diperbarui.');
    }

    public function destroy(ScheduleTime $scheduleTime)
    {
        $scheduleTime->delete();
        return redirect()->route('admin.schedule-times.index')->with('success', 'Slot waktu belajar berhasil dihapus.');
    }
}
