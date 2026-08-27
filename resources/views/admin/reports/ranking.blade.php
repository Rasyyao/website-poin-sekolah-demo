@extends('layouts.app')

@section('title', 'Siswa Melampaui Batas')
@section('page-title', 'Laporan')
@section('page-header', 'Siswa Melampaui Batas')
@section('page-desc', 'Daftar siswa yang telah melampaui ambang batas poin pelanggaran, dikelompokkan berdasarkan tindakan.')
@section('page-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.exports.thresholds', 'pdf') }}" target="_blank" class="px-3 py-2 rounded-lg bg-red-500/10 text-red-500 text-sm font-medium hover:bg-red-500 hover:text-white transition-all border border-red-500/20 cursor-pointer flex items-center justify-center gap-2" title="Export PDF">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <span class="hidden sm:inline">PDF</span>
        </a>
        <a href="{{ route('admin.exports.thresholds', 'excel') }}" class="px-3 py-2 rounded-lg bg-green-500/10 text-green-500 text-sm font-medium hover:bg-green-500 hover:text-white transition-all border border-green-500/20 cursor-pointer flex items-center justify-center gap-2" title="Export Excel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <span class="hidden sm:inline">Excel</span>
        </a>
    </div>
@endsection
@section('content')
<div class="space-y-8">
    @forelse($groupedStudents as $group)
        <div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
            <div class="bg-surface-lighter/50 px-6 py-4 border-b border-gray-700/50 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                <div>
                    <h3 class="text-lg font-bold text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Tindakan: {{ $group['threshold']->action ?? 'Diperlukan' }}
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">Siswa dengan total pelanggaran &ge; {{ $group['threshold']->min_points }} poin</p>
                </div>
                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-orange-500/10 text-orange-400 text-sm font-bold border border-orange-500/20">
                    {{ count($group['students']) }} Siswa
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-lighter/30">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($group['students'] as $student)
                        <tr class="hover:bg-surface-lighter/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500/20 to-primary-600/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-xs shrink-0 border border-primary-500/30 shadow-inner">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-200">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $student->nisn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $student->currentClass ? $student->currentClass->name : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-md text-sm font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                    {{ $student->total_violation_points }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-surface-light rounded-2xl border border-gray-700/50 p-12 text-center shadow-sm">
            <div class="w-16 h-16 rounded-full bg-green-500/10 text-green-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-100 mb-1">Semua Aman</h3>
            <p class="text-gray-400 text-sm max-w-md mx-auto">Saat ini belum ada siswa yang melampaui batas poin pelanggaran yang telah ditetapkan.</p>
        </div>
    @endforelse
</div>
@endsection
