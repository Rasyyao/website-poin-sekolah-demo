@extends('layouts.public')
@section('title', 'Banding')
@section('page-header', 'Riwayat Banding Saya')
@section('page-desc', 'Pantau status banding yang telah Anda ajukan atas pelanggaran tertentu')

@section('content')
<div x-data="{
    previewModal: {
        show: false,
        url: '',
        title: ''
    },
    openPreview(url, title) {
        this.previewModal = { show: true, url, title };
    }
}">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggaran</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alasan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Bukti</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appeals as $appeal)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $appeal->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-800 font-medium">{{ $appeal->pointsLog->rule->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="{{ $appeal->reason }}">{{ $appeal->reason }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($appeal->evidence_url)
                                    <button type="button" @click="openPreview(@js($appeal->evidence_url), 'Bukti: {{ addslashes($appeal->pointsLog->rule->name ?? 'Banding') }}')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-600 border border-blue-200 text-xs font-medium hover:bg-blue-100 transition-colors cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Lihat Foto
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $appeal->status->value === 'accepted' ? 'bg-green-50 text-green-600 border border-green-200' : ($appeal->status->value === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-red-50 text-red-600 border border-red-200') }}">
                                    {{ $appeal->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                    <p class="text-base font-medium text-slate-500">Belum ada riwayat banding.</p>
                                    <p class="text-sm mt-1 text-slate-400">Anda bisa mengajukan banding dari halaman <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Image Preview Modal --}}
    <template x-teleport="body">
        <div x-show="previewModal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs" @keydown.escape.window="previewModal.show = false">
            <div class="relative bg-white rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl border border-slate-200" @click.away="previewModal.show = false">
                <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50/50">
                    <span class="text-sm font-semibold text-slate-800 truncate" x-text="previewModal.title || 'Foto Bukti'"></span>
                    <button type="button" @click="previewModal.show = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 flex items-center justify-center bg-slate-900/5 max-h-[70vh] overflow-auto">
                    <img :src="previewModal.url" class="max-h-[65vh] max-w-full rounded-lg object-contain shadow-md" alt="Bukti">
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
