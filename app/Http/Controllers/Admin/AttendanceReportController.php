<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    protected function getFilteredQuery(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        $classId = $request->input('class_id');
        $majorId = $request->input('major_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Attendance::with(['student.schoolClass.major'])
            ->whereBetween('date', [$fromDate, $toDate]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($majorId) {
            $query->whereHas('student.schoolClass', function ($q) use ($majorId) {
                $q->where('major_id', $majorId);
            });
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        $classId = $request->input('class_id');
        $majorId = $request->input('major_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = $this->getFilteredQuery($request);

        $stats = [
            'total' => (clone $query)->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'sick' => (clone $query)->where('status', 'sick')->count(),
            'permission' => (clone $query)->where('status', 'permission')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
        ];

        $attendances = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(15);
        $classes = SchoolClass::with('major')->get();
        $majors = Major::all();

        return view('admin.attendances.index', compact(
            'attendances',
            'stats',
            'classes',
            'majors',
            'fromDate',
            'toDate',
            'classId',
            'majorId',
            'status',
            'search'
        ));
    }

    public function exportPdf(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        $schoolName = SchoolSetting::get('school_name', 'SMK Putra Pertiwi');

        $attendances = $this->getFilteredQuery($request)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.attendances.pdf', compact('attendances', 'fromDate', 'toDate', 'schoolName'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan_presensi_{$fromDate}_{$toDate}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        $schoolName = SchoolSetting::get('school_name', 'SMK Putra Pertiwi');

        $attendances = $this->getFilteredQuery($request)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        $filename = "rekap_presensi_{$fromDate}_{$toDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($attendances, $schoolName, $fromDate, $toDate) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ["LAPORAN PRESENSI SISWA - {$schoolName}"]);
            fputcsv($file, ["Periode: {$fromDate} s/d {$toDate}"]);
            fputcsv($file, []);

            fputcsv($file, [
                'No',
                'Tanggal',
                'Jam',
                'NIS',
                'Nama Siswa',
                'Kelas',
                'Jurusan',
                'Status Kehadiran',
                'Latitude',
                'Longitude',
                'Keterangan'
            ]);

            $no = 1;
            foreach ($attendances as $row) {
                fputcsv($file, [
                    $no++,
                    $row->date ? Carbon::parse($row->date)->format('d/m/Y') : '-',
                    $row->time ? substr($row->time, 0, 5) : '-',
                    $row->student?->nis ?? '-',
                    $row->student?->name ?? '-',
                    $row->student?->schoolClass?->name ?? '-',
                    $row->student?->schoolClass?->major?->name ?? '-',
                    $row->status_label,
                    $row->latitude ?? '-',
                    $row->longitude ?? '-',
                    $row->notes ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
