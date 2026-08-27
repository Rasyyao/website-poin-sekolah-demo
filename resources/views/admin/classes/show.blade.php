@extends('layouts.app')
@section('title', 'Detail Kelas')
@section('page-title', 'Kelas ' . $class->name)
@section('page-header', 'Data Kelas: ' . $class->name)
@section('page-desc', 'Daftar siswa dan statistik pelanggaran dalam kelas')
@section('content')
<div class="space-y-6">
    <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Wali Kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $class->academicYear?->displayLabel() }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-300">{{ $class->students->count() }} siswa</span>
                <div class="flex items-center gap-2">
                    <a target="_blank" href="{{ route('admin.reports.class.export.pdf', $class->id) }}" class="px-3 py-2 rounded-lg bg-red-500/10 text-red-500 text-sm font-medium hover:bg-red-500 hover:text-white transition-all border border-red-500/20 cursor-pointer flex items-center justify-center gap-2" title="Export PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        <span class="hidden sm:inline">PDF</span>
                    </a>
                    <a target="_blank" href="{{ route('admin.reports.class.export.excel', $class->id) }}" class="px-3 py-2 rounded-lg bg-green-500/10 text-green-500 text-sm font-medium hover:bg-green-500 hover:text-white transition-all border border-green-500/20 cursor-pointer flex items-center justify-center gap-2" title="Export Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        <span class="hidden sm:inline">Excel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gray-700/50 flex items-center justify-between bg-surface-lighter/30">
            <h3 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Daftar Siswa
            </h3>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-surface-lighter/50">
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">NISN</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Total Poin</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-700/30">
                @forelse($class->students as $student)
                    <tr class="hover:bg-surface-lighter/30 transition-colors group">
                        <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $student->nisn }}</td>
                        <td class="px-6 py-4 text-sm text-gray-200"><a href="{{ route('admin.students.show', $student) }}" class="group-hover:text-primary-400 transition-colors">{{ $student->name }}</a></td>
                        <td class="px-6 py-4 text-sm text-center">
                            @php $tp = $student->totalPoints(); @endphp
                            <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $tp > 0 ? 'border-red-500/20 text-red-400' : ($tp < 0 ? 'border-green-500/20 text-green-400' : 'border-gray-600/50 text-gray-400') }}">
                                {{ abs($tp) }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p class="text-base font-medium">Tidak ada siswa di kelas ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
