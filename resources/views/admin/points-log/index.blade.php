@extends('layouts.app')
@section('title', 'Log Poin')
@section('page-title', 'Log Poin Siswa')
@section('page-header', 'Log Poin')
@section('page-desc', 'Lihat dan kelola riwayat poin pelanggaran dan prestasi siswa')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="type" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm">
            <option value="">Semua Tipe</option>
            <option value="violation" {{ request('type') === 'violation' ? 'selected' : '' }}>Pelanggaran</option>
            <option value="achievement" {{ request('type') === 'achievement' ? 'selected' : '' }}>Prestasi</option>
        </select>
    </form>
</div>

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-surface-lighter/50">
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peraturan</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Poin</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelapor</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($logs as $log)
                <tr class="hover:bg-surface-lighter/30 transition-colors group">
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-200 font-medium group-hover:text-primary-300 transition-colors">{{ $log->student->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ $log->rule->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $log->points < 0 ? 'border-red-500/20 text-red-400' : 'border-green-500/20 text-green-400' }}">
                            {{ $log->points > 0 ? '+' : '' }}{{ $log->points }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $log->status->value === 'approved' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ($log->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') }}">{{ $log->status->label() }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $log->reporter->name }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($log->isPending())
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.points-log.approve', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Approve" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500/25 transition-colors border border-green-500/20 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.points-log.reject', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Reject" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-gray-600">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-base font-medium">Belum ada log poin.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
