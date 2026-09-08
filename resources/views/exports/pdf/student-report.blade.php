<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Siswa - {{ $student->name }}</title>
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
        <h2>Laporan Poin Siswa</h2>
        <h3>{{ $student->school->name ?? 'Poin Sekolah' }}</h3>
    </div>
    
    <div class="info">
        <table>
            <tr><th>Nama</th><td>: {{ $student->name }}</td></tr>
            <tr><th>NISN</th><td>: {{ $student->nisn }}</td></tr>
            <tr><th>Kelas</th><td>: {{ $student->currentClass->name ?? '-' }}</td></tr>
            <tr><th>Poin Pelanggaran</th><td>: {{ $report['summary']['total_violation_points'] ?? 0 }}</td></tr>
            <tr><th>Poin Prestasi</th><td>: {{ $report['summary']['total_achievement_points'] ?? 0 }}</td></tr>
        </table>
    </div>

    <h4>Riwayat Poin</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Peraturan</th>
                <th>Kategori</th>
                <th>Poin</th>
                <th>Status</th>
                <th>Dilaporkan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->rule->name ?? '-' }}</td>
                    <td>{{ $log->rule->type->label() ?? '-' }}</td>
                    <td>{{ $log->points }}</td>
                    <td>{{ $log->status->label() ?? '-' }}</td>
                    <td>{{ $log->reporter->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada riwayat poin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
