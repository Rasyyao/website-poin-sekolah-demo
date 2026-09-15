@extends('layouts.public')
@section('title', 'Dashboard Orang Tua')
@section('page-header', 'Laporan Perilaku Anak')
@section('page-desc'){{ $student->name }} &mdash; NISN: {{ $student->nisn }} &mdash; Kelas: {{ $student->currentClass?->name ?? '-' }}@endsection
@section('page-actions')
    <a target="_blank" href="{{ route('parent.export.pdf') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Download PDF
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Poin Pelanggaran</p>
        <p class="text-3xl font-black text-red-500 mt-2">{{ $stats['total_violation_points'] }}</p>
        <p class="text-xs text-slate-400 mt-1">Akumulasi poin pelanggaran tata tertib</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Poin Prestasi</p>
        <p class="text-3xl font-black text-green-500 mt-2">+{{ $stats['total_achievement_points'] }}</p>
        <p class="text-xs text-slate-400 mt-1">Akumulasi poin pencapaian & prestasi</p>
    </div>
</div>

@if($certificates->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Sertifikat Anak
        </h3>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($certificates as $certificate)
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ $certificate->ruleThreshold->action ?? 'Sertifikat Penghargaan' }}</p>
                    <p class="text-xs text-slate-400">No. {{ $certificate->certificate_number }} &middot; {{ $certificate->issued_at->format('d M Y') }}</p>
                </div>
                <a target="_blank" href="{{ route('parent.certificates.print', $certificate) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-medium hover:bg-emerald-100 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Unduh
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Aktivitas Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead><tr class="bg-slate-50">
            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
            <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Poin</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($recentLogs as $log)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->occurred_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-800 font-medium">{{ $log->rule->name }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <div class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 rounded-full text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-200 bg-red-50 text-red-500' : 'border-green-200 bg-green-50 text-green-500' }}">
                            {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-base font-medium text-slate-500">Belum ada aktivitas.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
