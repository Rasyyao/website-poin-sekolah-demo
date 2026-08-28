@extends('layouts.public')
@section('title', 'Dashboard Siswa')
@section('page-header')Halo, {{ $student->name }} 👋@endsection
@section('page-desc')NISN: {{ $student->nisn }} &mdash; Kelas: {{ $student->currentClass?->name ?? '-' }}@endsection
@section('page-actions')
    <a target="_blank" href="{{ route('student.export.pdf') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Download Laporan (PDF)
    </a>
@endsection

@section('content')
<div x-data="{ showAppealModal: false, appealLogId: null, appealRuleName: '' }" @open-appeal.window="showAppealModal = true; appealLogId = $event.detail.id; appealRuleName = $event.detail.name">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Poin</p>
            <p class="text-3xl font-black mt-2 {{ $stats['total_points'] > 0 ? 'text-red-500' : ($stats['total_points'] < 0 ? 'text-green-500' : 'text-slate-800') }}">{{ abs($stats['total_points']) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pelanggaran</p>
            <p class="text-3xl font-black text-red-500 mt-2">{{ $stats['total_violation_points'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Prestasi</p>
            <p class="text-3xl font-black text-green-500 mt-2">+{{ $stats['total_achievement_points'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Poin Terbaru
            </h3>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-slate-50">
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peraturan</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Poin</th>
                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentLogs as $log)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $log->occurred_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-800 font-medium">{{ $log->rule->name }}</td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 rounded-full text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-200 bg-red-50 text-red-500' : 'border-green-200 bg-green-50 text-green-500' }}">
                                {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($log->rule->type->value === 'violation')
                                <button type="button" @click="$dispatch('open-appeal', { id: {{ $log->id }}, name: @js($log->rule->name) })" class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline cursor-pointer whitespace-nowrap">
                                    Ajukan Banding
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p class="text-base font-medium text-slate-500">Belum ada riwayat.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Modal Ajukan Banding --}}
    <template x-teleport="body">
        <div>
            <div x-show="showAppealModal" x-cloak
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showAppealModal = false"
                 class="fixed inset-0 bg-black/50 z-40"></div>

            <div x-show="showAppealModal" x-cloak class="fixed inset-0 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md p-6" @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-slate-800">Ajukan Banding</h3>
                        <button @click="showAppealModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('student.appeals.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="points_log_id" :value="appealLogId">
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Pelanggaran</p>
                            <p class="text-sm font-medium text-slate-800" x-text="appealRuleName"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Banding <span class="text-red-500">*</span></label>
                            <textarea name="reason" required minlength="10" maxlength="2000" rows="4" placeholder="Jelaskan alasan banding Anda (minimal 10 karakter)..." class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="showAppealModal = false" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer">Kirim Banding</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
