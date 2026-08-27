@extends('layouts.public')
@section('title', 'Banding')
@section('nav-links')
    <a href="{{ route('student.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-200">Dashboard</a>
    <a href="{{ route('student.rules') }}" class="text-sm text-gray-400 hover:text-gray-200">Peraturan</a>
@endsection

@section('content')
<h1 class="text-xl font-bold text-gray-100 mb-6">Riwayat Banding Saya</h1>

<div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-gray-700/50">
            <th class="text-left px-5 py-3 text-xs text-gray-400">Tanggal</th>
            <th class="text-left px-5 py-3 text-xs text-gray-400">Pelanggaran</th>
            <th class="text-left px-5 py-3 text-xs text-gray-400">Alasan</th>
            <th class="text-center px-5 py-3 text-xs text-gray-400">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($appeals as $appeal)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $appeal->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-sm text-gray-200">{{ $appeal->pointsLog->rule->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-300 max-w-xs truncate">{{ $appeal->reason }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $appeal->status->value === 'accepted' ? 'bg-green-500/10 text-green-400' : ($appeal->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') }}">{{ $appeal->status->label() }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-gray-500 text-sm">Belum ada riwayat banding.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
