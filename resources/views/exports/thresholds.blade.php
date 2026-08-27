<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Siswa Melampaui Batas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; }
        .threshold-title { background-color: #e9ecef; padding: 8px; font-weight: bold; margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Siswa Melampaui Batas</h2>
        <p>Diekspor pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    @forelse($groupedStudents as $group)
        <div class="threshold-title">
            Tindakan: {{ $group['threshold']->action ?? 'Diperlukan' }}
            (Total Pelanggaran &ge; {{ $group['threshold']->min_points }} poin)
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th style="text-align:center;">Total Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group['students'] as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nisn }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->currentClass ? $student->currentClass->name : '-' }}</td>
                    <td style="text-align:center;">{{ $student->total_violation_points }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p style="text-align: center; padding: 20px;">Saat ini belum ada siswa yang melampaui batas poin pelanggaran.</p>
    @endforelse
</body>
</html>
