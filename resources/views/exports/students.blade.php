<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Siswa</title>
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
        <h2>Daftar Siswa</h2>
        <p>Diekspor pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Terdaftar Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->nisn }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->currentClass ? $student->currentClass->name : '-' }}</td>
                <td>{{ $student->gender->label() }}</td>
                <td>{{ $student->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
