<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kelas - {{ $schoolClass->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        .info table { border: none; margin-top: 0; width: 50%; }
        .info th, .info td { border: none; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Poin Kelas</h2>
        <h3>{{ $schoolClass->school->name ?? 'Poin Sekolah' }}</h3>
    </div>
    
    <div class="info">
        <table>
            <tr><th>Kelas</th><td>: {{ $schoolClass->name }}</td></tr>
            <tr><th>Wali Kelas</th><td>: {{ $schoolClass->homeroomTeacher->name ?? '-' }}</td></tr>
            <tr><th>Tahun Ajaran</th><td>: {{ $schoolClass->academicYear->displayLabel() ?? '-' }}</td></tr>
            <tr><th>Jumlah Siswa</th><td>: {{ count($classStats['students']) }}</td></tr>
        </table>
    </div>

    <h4>Daftar Siswa</h4>
    <table>
        <thead>
            <tr>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Pelanggaran</th>
                <th>Prestasi</th>
                <th>Keterangan / Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classStats['students'] as $student)
                <tr>
                    <td>{{ $student['nisn'] }}</td>
                    <td>{{ $student['name'] }}</td>
                    <td>{{ $student['total_violation_points'] }}</td>
                    <td>{{ $student['total_achievement_points'] }}</td>
                    <td style="color: {{ $student['action_required'] ? '#dc2626' : '#16a34a' }}; font-weight: bold;">
                        {{ $student['action_required'] ?? 'Aman' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
