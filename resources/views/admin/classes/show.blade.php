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
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export
                    </button>
                    <div x-show="open" style="display: none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50 transform origin-top-right">
                        <a href="{{ route('admin.reports.class.export.pdf', $class->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 7h2v6h-2zM11 15h2v2h-2z"/></svg>
                            PDF
                        </a>
                        <a href="{{ route('admin.reports.class.export.excel', $class->id) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Excel
                        </a>
                    </div>
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
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Pelanggaran</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Prestasi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-700/30">
                @forelse($class->students as $student)
                    <tr class="hover:bg-surface-lighter/30 transition-colors group">
                        <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $student->nisn }}</td>
                        <td class="px-6 py-4 text-sm text-gray-200"><a href="{{ route('admin.students.show', $student) }}" class="group-hover:text-primary-400 transition-colors">{{ $student->name }}</a></td>
                        <td class="px-6 py-4 text-sm text-center">
                            @php $vp = $student->totalViolationPoints(); @endphp
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold {{ $vp > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-surface text-gray-400 border border-gray-600/50' }}">
                                {{ $vp }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            @php $ap = $student->totalAchievementPoints(); @endphp
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold {{ $ap > 0 ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-surface text-gray-400 border border-gray-600/50' }}">
                                {{ $ap > 0 ? '+' : '' }}{{ $ap }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
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
