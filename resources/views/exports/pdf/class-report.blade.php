<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kelas - {{ $schoolClass->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        
        .page-header { text-align: center; padding: 24px 0 16px; border-bottom: 3px solid #2563eb; margin-bottom: 24px; }
        .page-header h1 { font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-header .school-name { font-size: 13px; color: #2563eb; font-weight: 600; }
        .page-header .export-date { font-size: 9px; color: #94a3b8; margin-top: 6px; }

        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.8px; padding: 6px 12px; background: #eff6ff; border-left: 4px solid #2563eb; margin-bottom: 10px; }

        .info-grid { width: 100%; margin-bottom: 16px; }
        .info-grid td { padding: 5px 10px; vertical-align: top; }
        .info-grid .label { font-weight: 600; color: #475569; width: 160px; white-space: nowrap; }
        .info-grid .value { color: #0f172a; }
        .info-grid tr:nth-child(even) { background: #f8fafc; }

        .stats-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .stats-grid td { padding: 10px 14px; text-align: center; border: 1px solid #e2e8f0; }
        .stats-grid .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .stats-grid .stat-value { font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .stats-grid .stat-value.danger { color: #dc2626; }
        .stats-grid .stat-value.success { color: #16a34a; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data-table thead th { background: #2563eb; color: #ffffff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; }
        table.data-table tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }

        .text-danger { color: #dc2626; font-weight: 600; }
        .text-success { color: #16a34a; font-weight: 600; }
        .text-muted { color: #94a3b8; font-style: italic; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 9px; font-weight: 700; }
        .badge-safe { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>LAPORAN POIN KELAS {{ strtoupper($schoolClass->name) }}</h1>
        <div class="school-name">{{ $schoolClass->school->name ?? website_name() }}</div>
        <div class="export-date">Diekspor pada {{ now()->format('d M Y, H:i') }}</div>
    </div>

    {{-- Class Info --}}
    <div class="section">
        <div class="section-title">Informasi Kelas</div>
        <table class="info-grid">
            <tr><td class="label">Nama Kelas</td><td class="value">{{ $schoolClass->name }}</td></tr>
            <tr><td class="label">Wali Kelas</td><td class="value">{{ $schoolClass->homeroomTeacher->name ?? 'Belum ditentukan' }}</td></tr>
            <tr><td class="label">Tahun Ajaran</td><td class="value">{{ $schoolClass->academicYear?->displayLabel() ?? '-' }}</td></tr>
            <tr><td class="label">Jumlah Siswa</td><td class="value">{{ count($classStats['students']) }} siswa</td></tr>
        </table>
    </div>

    {{-- Stats Summary --}}
    @php
        $totalVP = collect($classStats['students'])->sum('total_violation_points');
        $totalAP = collect($classStats['students'])->sum('total_achievement_points');
        $studentsWithViolations = $classStats['students_with_violations'];
        $totalStudents = $classStats['total_students'];
        $avgViolation = $totalStudents > 0 ? round($totalVP / $totalStudents, 1) : 0;
    @endphp

    <div class="section">
        <div class="section-title">Statistik Kelas</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-label">Poin Pelanggaran</div>
                    <div class="stat-value danger">{{ number_format($totalVP) }}</div>
                </td>
                <td>
                    <div class="stat-label">Poin Prestasi</div>
                    <div class="stat-value success">{{ number_format($totalAP) }}</div>
                </td>
                <td>
                    <div class="stat-label">Siswa Bermasalah</div>
                    <div class="stat-value">{{ $studentsWithViolations }}/{{ $totalStudents }}</div>
                </td>
                <td>
                    <div class="stat-label">Rata-rata Pelanggaran</div>
                    <div class="stat-value">{{ $avgViolation }}</div>
                </td>
                <td>
                    <div class="stat-label">Nett Poin</div>
                    <div class="stat-value" style="color: {{ ($totalAP - $totalVP) >= 0 ? '#16a34a' : '#dc2626' }}">{{ ($totalAP - $totalVP) >= 0 ? '+' : '' }}{{ number_format($totalAP - $totalVP) }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Students Table --}}
    <div class="section">
        <div class="section-title">Daftar Siswa</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th class="text-center">Pelanggaran</th>
                    <th class="text-center">Prestasi</th>
                    <th class="text-center">Nett</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $sortedStudents = collect($classStats['students'])->sortBy('name')->values(); @endphp
                @forelse($sortedStudents as $i => $student)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $student['nisn'] }}</td>
                        <td>{{ $student['name'] }}</td>
                        <td class="text-center {{ $student['total_violation_points'] > 0 ? 'text-danger' : '' }}">{{ $student['total_violation_points'] }}</td>
                        <td class="text-center {{ $student['total_achievement_points'] > 0 ? 'text-success' : '' }}">{{ $student['total_achievement_points'] }}</td>
                        <td class="text-center" style="font-weight: 600; color: {{ ($student['total_achievement_points'] - $student['total_violation_points']) >= 0 ? '#16a34a' : '#dc2626' }}">
                            {{ $student['total_achievement_points'] - $student['total_violation_points'] }}
                        </td>
                        <td>
                            @if($student['action_required'])
                                <span class="status-badge badge-danger">{{ $student['action_required'] }}</span>
                            @else
                                <span class="status-badge badge-safe">Aman</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 20px;">Tidak ada siswa di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- At-Risk Students --}}
    @php
        $atRisk = collect($classStats['students'])->filter(fn($s) => $s['action_required'] !== null)
            ->sortByDesc('total_violation_points')->values();
    @endphp
    @if($atRisk->isNotEmpty())
    <div class="section">
        <div class="section-title">Siswa Perlu Ditangani</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th class="text-center">Poin Pelanggaran</th>
                    <th>Tindakan yang Diperlukan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($atRisk as $i => $student)
                    <tr style="background: {{ $i % 2 === 0 ? '#fef2f2' : '#fff7ed' }};">
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $student['nisn'] }}</td>
                        <td style="font-weight: 600;">{{ $student['name'] }}</td>
                        <td class="text-center text-danger">{{ $student['total_violation_points'] }}</td>
                        <td><span class="status-badge badge-danger">{{ $student['action_required'] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        {{ website_name() }} — Laporan ini digenerate secara otomatis pada {{ now()->format('d M Y, H:i') }}. Dokumen ini bersifat rahasia.
    </div>
</body>
</html>
