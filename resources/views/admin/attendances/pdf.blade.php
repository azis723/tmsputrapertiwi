<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Presensi Siswa</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 13px;
            color: #475569;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #64748b;
        }
        .meta-info {
            margin-bottom: 12px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-present { background-color: #dcfce7; color: #15803d; }
        .badge-late { background-color: #fef3c7; color: #b45309; }
        .badge-sick { background-color: #dbeafe; color: #1d4ed8; }
        .badge-permission { background-color: #f3e8ff; color: #7e22ce; }
        .badge-absent { background-color: #fee2e2; color: #b91c1c; }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer table {
            border: none;
            margin: 0;
        }
        .footer td {
            border: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $schoolName ?? 'SMK PUTRA PERTIWI' }}</h2>
        <h3>LAPORAN REKAPITULASI PRESENSI SISWA</h3>
        <p>Periode: {{ date('d F Y', strtotime($fromDate)) }} s/d {{ date('d F Y', strtotime($toDate)) }}</p>
    </div>

    <div class="meta-info">
        <strong>Total Catatan Presensi:</strong> {{ count($attendances) }} Data | 
        <strong>Dicetak Pada:</strong> {{ date('d/m/Y H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 75px;">Tanggal</th>
                <th style="width: 50px;">Jam</th>
                <th style="width: 65px;">NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas & Jurusan</th>
                <th style="width: 75px; text-align: center;">Status</th>
                <th style="width: 110px;">Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $idx => $row)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ date('d/m/Y', strtotime($row->date)) }}</td>
                    <td>{{ substr($row->time, 0, 5) }} WIB</td>
                    <td style="font-family: monospace;">{{ $row->student?->nis ?? '-' }}</td>
                    <td><strong>{{ $row->student?->name ?? '-' }}</strong></td>
                    <td>{{ $row->student?->schoolClass?->name ?? '-' }} ({{ $row->student?->schoolClass?->major?->code ?? '-' }})</td>
                    <td style="text-align: center;">
                        <span class="badge badge-{{ $row->status }}">{{ $row->status_label }}</span>
                    </td>
                    <td style="font-size: 9px; font-family: monospace;">
                        @if ($row->latitude && $row->longitude)
                            {{ round($row->latitude, 5) }}, {{ round($row->longitude, 5) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Tidak ada data presensi pada rentang tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td style="width: 70%;"></td>
                <td style="text-align: center;">
                    <p>Tangerang Selatan, {{ date('d F Y') }}</p>
                    <p style="margin-top: 5px; font-weight: bold;">Kepala SMK Putra Pertiwi</p>
                    <br><br><br><br>
                    <p style="text-decoration: underline; font-weight: bold;">( .................................................. )</p>
                    <p style="color: #64748b; font-size: 9px;">NIP. -</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
