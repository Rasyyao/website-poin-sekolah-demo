@extends('layouts.app')
@section('title', $student->name)
@section('page-title', 'Riwayat Poin — ' . $student->name)
@section('page-header', 'Riwayat Poin: ' . $student->name)
@section('page-desc', 'Detail histori pelanggaran dan prestasi siswa')
@section('content')
<div class="mb-4 flex items-center gap-4">
    <span class="text-sm text-gray-400">NISN: {{ $student->nisn }}</span>
    <span class="text-sm text-gray-400">Kelas: {{ $student->currentClass?->name ?? '-' }}</span>
    <span class="text-sm font-medium {{ $student->totalPoints() > 0 ? 'text-red-400' : ($student->totalPoints() < 0 ? 'text-green-400' : 'text-gray-400') }}">Total: {{ abs($student->totalPoints()) }}</span>
</div>

<div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-gray-700/50">
            <th class="text-left px-5 py-3 text-xs text-gray-400">Tanggal</th>
            <th class="text-left px-5 py-3 text-xs text-gray-400">Peraturan</th>
            <th class="text-center px-5 py-3 text-xs text-gray-400">Poin</th>
            <th class="text-left px-5 py-3 text-xs text-gray-400">Pelapor</th>
            <th class="text-left px-5 py-3 text-xs text-gray-400">Catatan</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($logs as $log)
                <tr class="hover:bg-surface-lighter/30">
                    <td class="px-5 py-2.5 text-xs text-gray-400">{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-2.5 text-sm text-gray-200">{{ $log->rule->name }}</td>
                    <td class="px-5 py-2.5 text-sm text-center font-medium {{ $log->rule->type->value === 'violation' ? 'text-red-400' : 'text-green-400' }}">{{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-400">{{ $log->reporter->name }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $log->note ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">Belum ada riwayat.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $logs->links() }}</div>
@endsection
