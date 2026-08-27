@extends('layouts.public')
@section('title', 'Laporan Perilaku')
@section('nav-links')
    <a href="{{ route('parent.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-200">Dashboard</a>
@endsection

@section('content')
<h1 class="text-xl font-bold text-gray-100 mb-6">Laporan Perilaku — {{ $student->name }}</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-4">
        <p class="text-xs text-gray-400 mb-1">Ringkasan</p>
        <div class="space-y-1 text-sm">
            <p class="text-gray-300">Total Pelanggaran: <span class="text-red-400 font-medium">{{ $report['summary']['total_violations'] ?? 0 }}</span></p>
            <p class="text-gray-300">Total Prestasi: <span class="text-green-400 font-medium">{{ $report['summary']['total_achievements'] ?? 0 }}</span></p>
            <p class="text-gray-300">Poin Bersih: <span class="font-medium {{ ($report['summary']['net_points'] ?? 0) < 0 ? 'text-red-400' : 'text-green-400' }}">{{ $report['summary']['net_points'] ?? 0 }}</span></p>
        </div>
    </div>
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-4">
        <p class="text-xs text-gray-400 mb-1">Data Siswa</p>
        <div class="space-y-1 text-sm text-gray-300">
            <p>Nama: {{ $student->name }}</p>
            <p>NISN: {{ $student->nisn }}</p>
            <p>Kelas: {{ $student->currentClass?->name ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
    <div class="px-5 py-3 border-b border-gray-700/50"><h3 class="text-sm font-semibold text-gray-300">Detail Riwayat</h3></div>
    <table class="w-full">
        <thead><tr class="border-b border-gray-700/30">
            <th class="text-left px-5 py-2 text-xs text-gray-400">Tanggal</th>
            <th class="text-left px-5 py-2 text-xs text-gray-400">Peraturan</th>
            <th class="text-left px-5 py-2 text-xs text-gray-400">Tipe</th>
            <th class="text-center px-5 py-2 text-xs text-gray-400">Poin</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($report['logs'] ?? [] as $log)
                <tr>
                    <td class="px-5 py-2.5 text-xs text-gray-400">{{ \Carbon\Carbon::parse($log['occurred_at'])->format('d/m/Y') }}</td>
                    <td class="px-5 py-2.5 text-sm text-gray-200">{{ $log['rule_name'] ?? '-' }}</td>
                    <td class="px-5 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full {{ ($log['points'] ?? 0) < 0 ? 'bg-red-500/10 text-red-400' : 'bg-green-500/10 text-green-400' }}">{{ ($log['points'] ?? 0) < 0 ? 'Pelanggaran' : 'Prestasi' }}</span></td>
                    <td class="px-5 py-2.5 text-sm text-center font-medium {{ ($log['points'] ?? 0) < 0 ? 'text-red-400' : 'text-green-400' }}">{{ ($log['points'] ?? 0) > 0 ? '+' : '' }}{{ $log['points'] ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-gray-500 text-sm">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
