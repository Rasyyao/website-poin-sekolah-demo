@extends('layouts.app')
@section('title', 'Banding')
@section('page-title', 'Daftar Banding')
@section('page-header', 'Permohonan Banding')
@section('page-desc', 'Tinjau dan kelola permohonan banding dari orang tua atau siswa')
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
                <thead><tr class="bg-slate-50/70 border-b border-slate-200">
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peraturan</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alasan</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Bukti</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($appeals as $appeal)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">{{ $appeal->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-900 font-medium group-hover:text-blue-600 transition-colors">{{ $appeal->pointsLog->student->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $appeal->pointsLog->rule->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700 max-w-xs truncate" title="{{ $appeal->reason }}">{{ $appeal->reason }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($appeal->evidence_url)
                                <button type="button" @click="openPreview(@js($appeal->evidence_url), 'Bukti Banding: {{ addslashes($appeal->pointsLog->student->name ?? 'Siswa') }}')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-600 border border-blue-200 text-xs font-medium hover:bg-blue-100 transition-colors cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Lihat Bukti
                                </button>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $appeal->status->value === 'accepted' ? 'bg-green-50 text-green-600 border-green-200' : ($appeal->status->value === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-red-50 text-red-600 border-red-200') }}">{{ $appeal->status->label() }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($appeal->isPending())
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.appeals.accept', $appeal) }}" class="inline">@csrf 
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-colors border border-green-200 cursor-pointer shadow-2xs" title="Terima">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.appeals.reject', $appeal) }}" class="inline">@csrf 
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors border border-red-200 cursor-pointer shadow-2xs" title="Tolak">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="text-base font-medium text-slate-500">Belum ada banding.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($appeals->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $appeals->withQueryString()->links() }}
        </div>
        @endif
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
