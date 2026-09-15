@extends('layouts.app')
@section('title', $student->name)
@section('page-title', 'Detail Siswa')
@section('page-header', 'Profil Siswa')
@section('page-desc', 'Informasi detail, statistik poin, dan riwayat pelanggaran serta prestasi siswa')

@section('page-actions')
    <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Student Profile Card --}}
    <div class="bg-surface-light rounded-2xl border border-gray-700/50 p-6 flex flex-col items-center text-center shadow-lg relative overflow-hidden h-fit">
        <!-- Background accent -->
        <!-- <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-primary-900/30 to-transparent"></div> -->

        <div class="relative w-28 h-28 rounded-full bg-surface border-4 border-surface-light flex items-center justify-center text-primary-400 text-4xl font-bold mb-4 shadow-xl z-10">
            <div class="absolute inset-0 rounded-full bg-primary-500/10"></div>
            {{ substr($student->name, 0, 1) }}
        </div>
        
        <h3 class="text-xl font-bold text-gray-100 mb-1 z-10">{{ $student->name }}</h3>
        <p class="text-sm font-mono text-primary-400 mb-6 bg-primary-500/20 px-4 py-1.5 rounded-full border border-primary-500/20 z-10">NISN: {{ $student->nisn }}</p>
        
        <div class="w-full space-y-4 text-sm text-left mb-8 bg-surface p-5 rounded-xl border border-gray-700/50 shadow-inner z-10">
            <div class="flex justify-between items-center pb-3 border-b border-gray-700/30">
                <span class="text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Kelas
                </span>
                <span class="text-gray-100 font-medium bg-gray-700/50 px-2 py-0.5 rounded">{{ $student->currentClass?->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-700/30">
                <span class="text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Tgl Lahir
                </span>
                <span class="text-gray-100 font-medium">{{ $student->birth_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Kontak Orang Tua
                </span>
                <span class="text-gray-100 font-medium">{{ $student->parent_contact ?? '-' }}</span>
            </div>
        </div>

        <div class="w-full space-y-3 z-10">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" type="button" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">
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
                     class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50 transform origin-top">
                    <a href="{{ route('admin.reports.student.export.pdf', $student) }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 7h2v6h-2zM11 15h2v2h-2z"/></svg>
                        PDF
                    </a>
                    <a href="{{ route('admin.reports.student.export.excel', $student) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats and History --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-400">Poin Pelanggaran</p>
                        <p class="text-2xl font-bold text-red-400 mt-1">{{ $violationPoints }}</p>
                        <p class="text-xs text-gray-500 mt-1">Akumulasi pelanggaran tata tertib</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-400">Poin Prestasi</p>
                        <p class="text-2xl font-bold text-green-400 mt-1">{{ $achievementPoints }}</p>
                        <p class="text-xs text-gray-500 mt-1">Akumulasi pencapaian & prestasi</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Points History Table --}}
        <div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-gray-700/50 flex items-center justify-between bg-surface-lighter/30">
                <h3 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Poin Siswa
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-lighter/50">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peraturan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Poin</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dilaporkan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($logs as $log)
                            <tr class="hover:bg-surface-lighter/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-200">{{ $log->occurred_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $log->occurred_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-200 group-hover:text-primary-300 transition-colors">{{ $log->rule->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $log->rule->category?->label() ?? 'Umum' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-500/20 text-red-400' : 'border-green-500/20 text-green-400' }}">
                                        {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $log->status->value === 'approved' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ($log->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') }}">
                                        {{ $log->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-300">
                                            {{ substr($log->reporter->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-300">{{ $log->reporter->name }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                        <p class="text-base font-medium">Belum ada riwayat poin.</p>
                                        <p class="text-sm mt-1">Siswa ini belum memiliki catatan poin atau pelanggaran.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
