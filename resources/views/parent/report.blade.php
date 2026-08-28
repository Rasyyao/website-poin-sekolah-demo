@extends('layouts.public')
@section('title', 'Laporan Perilaku')
@section('page-header')Laporan Perilaku — {{ $student->name }}@endsection
@section('page-desc', 'Rekap lengkap pelanggaran dan prestasi anak Anda')

@section('content')
<form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col sm:flex-row sm:items-end gap-3">
    <div class="flex-1">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
        <input type="date" name="from" value="{{ request('from') }}" class="w-full px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
    </div>
    <div class="flex-1">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ request('to') }}" class="w-full px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer shadow-sm">Terapkan</button>
        @if(request('from') || request('to'))
            <a href="{{ route('parent.report') }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium hover:bg-slate-200 transition-colors">Reset</a>
        @endif
    </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Pelanggaran</p>
        <p class="text-3xl font-black text-red-500 mt-2">{{ $report['summary']['total_violation_points'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $report['summary']['total_violations'] ?? 0 }} kejadian</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Prestasi</p>
        <p class="text-3xl font-black text-green-500 mt-2">+{{ $report['summary']['total_achievement_points'] ?? 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $report['summary']['total_achievements'] ?? 0 }} kejadian</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Poin Bersih</p>
        @php $net = $report['summary']['net_points'] ?? 0; @endphp
        <p class="text-3xl font-black mt-2 {{ $net > 0 ? 'text-red-500' : ($net < 0 ? 'text-green-500' : 'text-slate-800') }}">{{ abs($net) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
            Detail Riwayat
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-slate-50">
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peraturan</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Poin</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($report['logs'] ?? [] as $log)
                    @php $isViolation = $log->rule->type->value === 'violation'; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ $log->occurred_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-3.5 text-sm text-slate-800 font-medium">{{ $log->rule->name ?? '-' }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $isViolation ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-green-50 text-green-600 border border-green-200' }}">
                                {{ $isViolation ? 'Pelanggaran' : 'Prestasi' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-center font-bold {{ $isViolation ? 'text-red-500' : 'text-green-500' }}">
                            {{ $isViolation ? '' : '+' }}{{ $log->points }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p class="text-base font-medium text-slate-500">Belum ada data pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
