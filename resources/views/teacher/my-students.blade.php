@extends('layouts.app')
@section('title', 'Siswa Saya')
@section('page-title', 'Siswa Kelas Saya')
@section('page-header', 'Daftar Siswa Wali')
@section('page-desc', 'Daftar siswa di kelas yang Anda walikan beserta riwayat poinnya')
@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5">
        <p class="text-sm text-gray-400 mb-1">Total Siswa Wali</p>
        <p class="text-2xl font-bold text-gray-100">{{ count($students) }}</p>
    </div>
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5">
        <p class="text-sm text-gray-400 mb-1">Total Pelanggaran</p>
        <p class="text-2xl font-bold text-red-400">{{ collect($students)->sum('violation_points') }}</p>
    </div>
</div>
<div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-gray-700/50">
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">NISN</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Nama</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Kelas</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Pelanggaran</th>
            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Prestasi</th>
            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($students as $s)
                <tr class="hover:bg-surface-lighter/50 {{ $s['violation_points'] >= 50 ? 'bg-red-500/5' : '' }}">
                    <td class="px-5 py-3 text-sm text-gray-300 font-mono">{{ $s['nisn'] }}</td>
                    <td class="px-5 py-3 text-sm text-gray-200 font-medium">{{ $s['name'] }}</td>
                    <td class="px-5 py-3 text-sm text-gray-400">{{ $s['class'] }}</td>
                    <td class="px-5 py-3 text-sm text-center text-red-400">{{ $s['violation_points'] }}</td>
                    <td class="px-5 py-3 text-sm text-center text-green-400">+{{ $s['achievement_points'] }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('teacher.students.history', $s['id']) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors border border-blue-500/20" title="Riwayat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">Anda belum ditugaskan sebagai wali kelas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
