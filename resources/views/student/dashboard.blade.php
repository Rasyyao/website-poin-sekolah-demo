@extends('layouts.public')
@section('title', 'Dashboard Siswa')
@section('nav-links')
    <a href="{{ route('student.rules') }}" class="text-sm text-gray-400 hover:text-gray-200">Peraturan</a>
    <a href="{{ route('student.appeals.index') }}" class="text-sm text-gray-400 hover:text-gray-200">Banding</a>
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-100">Halo, {{ $student->name }} 👋</h1>
        <p class="text-sm text-gray-400 mt-1">NISN: {{ $student->nisn }} — Kelas: {{ $student->currentClass?->name ?? '-' }}</p>
    </div>
    <a target="_blank" href="{{ route('student.export.pdf') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-primary-500/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Download Laporan (PDF)
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-4 text-center">
        <p class="text-xs text-gray-400">Total Poin</p>
        <p class="text-2xl font-bold mt-1 {{ $stats['total_points'] > 0 ? 'text-red-400' : ($stats['total_points'] < 0 ? 'text-green-400' : '') }}">{{ abs($stats['total_points']) }}</p>
    </div>
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-4 text-center">
        <p class="text-xs text-gray-400">Pelanggaran</p>
        <p class="text-2xl font-bold text-red-400 mt-1">{{ $stats['total_violation_points'] }}</p>
    </div>
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-4 text-center">
        <p class="text-xs text-gray-400">Prestasi</p>
        <p class="text-2xl font-bold text-green-400 mt-1">+{{ $stats['total_achievement_points'] }}</p>
    </div>
</div>

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="px-6 py-5 border-b border-gray-700/50 flex items-center justify-between bg-surface-lighter/30">
        <h3 class="text-base font-semibold text-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Poin Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead><tr class="bg-surface-lighter/50">
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peraturan</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Poin</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($recentLogs as $log)
                <tr class="hover:bg-surface-lighter/30 transition-colors group">
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $log->occurred_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-200 group-hover:text-primary-300 transition-colors">{{ $log->rule->name }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-500/20 text-red-400' : 'border-green-500/20 text-green-400' }}">
                            {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-base font-medium">Belum ada riwayat.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
