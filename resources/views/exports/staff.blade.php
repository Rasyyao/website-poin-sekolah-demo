<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Staf</title>
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
        <h2>Daftar Guru & Staf</h2>
        <p>Diekspor pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $index => $staff)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->email }}</td>
                <td>{{ $staff->role->label() }}</td>
                <td>{{ $staff->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
