@extends('layouts.app')
@section('title', 'Banding')
@section('page-title', 'Daftar Banding')
@section('page-header', 'Permohonan Banding')
@section('page-desc', 'Tinjau dan kelola permohonan banding dari orang tua atau siswa')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-whiteer/50">
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peraturan</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alasan</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($appeals as $appeal)
                <tr class="hover:bg-whiteer/30 transition-colors group">
                    <td class="px-6 py-4 text-xs text-slate-500">{{ $appeal->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900 font-medium group-hover:text-blue-600 transition-colors">{{ $appeal->pointsLog->student->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-700">{{ $appeal->pointsLog->rule->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-700 max-w-xs truncate" title="{{ $appeal->reason }}">{{ $appeal->reason }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $appeal->status->value === 'accepted' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ($appeal->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') }}">{{ $appeal->status->label() }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($appeal->isPending())
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('admin.appeals.accept', $appeal) }}" class="inline">@csrf 
                                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500/20 transition-colors border border-green-500/20 cursor-pointer" title="Terima">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.appeals.reject', $appeal) }}" class="inline">@csrf 
                                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors border border-red-500/20 cursor-pointer" title="Tolak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-base font-medium">Belum ada banding.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($appeals->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-whiteer/20">
        {{ $appeals->withQueryString()->links() }}
    </div>
    @endif
</div>
<div class="mt-6">{{ $appeals->withQueryString()->links() }}</div>
@endsection
