<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Kelas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Kelas</h2>
        <p>Diekspor pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Wali Kelas</th>
                <th>Jumlah Siswa</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classes as $index => $class)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $class->name }}</td>
                <td>{{ $class->homeroomTeacher ? $class->homeroomTeacher->name : '-' }}</td>
                <td>{{ $class->students->count() }}</td>
                <td>{{ $class->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
