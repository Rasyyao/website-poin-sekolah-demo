@extends('layouts.app')
@section('title', 'Log Poin')
@section('page-title', 'Log Poin Siswa')
@section('page-header', 'Log Poin')
@section('page-desc', 'Lihat dan kelola riwayat poin pelanggaran dan prestasi siswa')
@section('content')
<div x-data="{
    showEditModal: {{ (old('_method') === 'PUT' && $errors->any()) ? 'true' : 'false' }},
    editLog: {
        id: '',
        student_name: '',
        rule_id: '{{ old('rule_id') }}',
        occurred_at: '{{ old('occurred_at') }}',
        note: '{{ old('note') }}',
        evidence_url: '{{ old('evidence_url') }}',
        update_url: ''
    },
    // Full image preview modal
    previewModal: {
        show: false,
        url: '',
        title: ''
    },
    // Edit form evidence file state
    editPreviewUrl: null,
    editFileName: '',
    editFileSize: '',
    removeEvidence: false,

    openEditModal(data) {
        this.editLog = data;
        this.editPreviewUrl = null;
        this.editFileName = '';
        this.editFileSize = '';
        this.removeEvidence = false;
        const input = document.getElementById('admin_edit_evidence_input');
        if (input) input.value = '';
        this.showEditModal = true;
    },
    openImagePreview(url, title) {
        if (!url) return;
        this.previewModal.url = url;
        this.previewModal.title = title || 'Preview Foto Bukti';
        this.previewModal.show = true;
    },
    handleEditFile(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Ukuran Terlalu Besar', text: 'Maksimal ukuran foto adalah 5MB.' });
                e.target.value = '';
                return;
            }
            this.editPreviewUrl = URL.createObjectURL(file);
            this.editFileName = file.name;
            this.editFileSize = (file.size / 1024).toFixed(1) + ' KB';
            this.removeEvidence = false;
        }
    },
    clearEditFile() {
        this.editPreviewUrl = null;
        this.editFileName = '';
        this.editFileSize = '';
        const input = document.getElementById('admin_edit_evidence_input');
        if (input) input.value = '';
    }
}">

<div class="flex items-center gap-3 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="type" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm">
            <option value="">Semua Tipe</option>
            <option value="violation" {{ request('type') === 'violation' ? 'selected' : '' }}>Pelanggaran</option>
            <option value="achievement" {{ request('type') === 'achievement' ? 'selected' : '' }}>Prestasi</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-whiteer/50">
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peraturan</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Poin</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelapor</th>
            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($logs as $log)
                <tr class="hover:bg-whiteer/30 transition-colors group">
                    <td class="px-6 py-4 text-xs text-slate-500">{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-900 font-medium group-hover:text-blue-600 transition-colors">
                        <div class="flex items-center gap-2">
                            <span>{{ $log->student->name }}</span>
                            @if($log->evidence_url)
                                <button type="button" @click="openImagePreview(@js($log->evidence_url), 'Bukti: {{ addslashes($log->student->name ?? '') }} - {{ addslashes($log->rule->name ?? '') }}')" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 border border-blue-200 text-[11px] font-medium hover:bg-blue-100 transition-colors cursor-pointer" title="Lihat Foto Bukti">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Foto
                                </button>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-700">{{ $log->rule->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-100 text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-500/20 text-red-400' : 'border-green-500/20 text-green-400' }}">
                            {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $log->status->value === 'approved' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ($log->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') }}">{{ $log->status->label() }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $log->reporter->name }}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            @if($log->isPending())
                                <form method="POST" action="{{ route('admin.points-log.approve', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Approve" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-500/10 text-green-600 hover:bg-green-500/25 transition-colors border border-green-500/20 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.points-log.reject', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Reject" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-600 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif

                            <button type="button"
                                @click="openEditModal({
                                    id: {{ $log->id }},
                                    student_name: @js($log->student->name ?? '-'),
                                    rule_id: {{ $log->rule_id }},
                                    occurred_at: @js($log->occurred_at ? $log->occurred_at->format('Y-m-d\TH:i:s') : ''),
                                    note: @js($log->note ?? ''),
                                    evidence_url: @js($log->evidence_url ?? ''),
                                    update_url: '{{ route('admin.points-log.update', $log) }}'
                                })"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 transition-colors border border-amber-500/20 cursor-pointer" title="Edit Log Poin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.points-log.destroy', $log) }}" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="Swal.fire({ title: 'Hapus log poin?', text: 'Log poin ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-600 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer" title="Hapus Log Poin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-base font-medium">Belum ada log poin.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-whiteer/20">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Edit Modal --}}
<template x-teleport="body">
<div>
<div x-show="showEditModal"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="showEditModal = false"
     class="fixed inset-0"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px); z-index: 9998;"
     x-cloak></div>

<div x-show="showEditModal"
     class="fixed inset-0 flex items-center justify-center p-4"
     style="z-index: 9999;"
     x-cloak>
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-slate-900">Edit Log Poin</h3>
            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form method="POST" :action="editLog.update_url" enctype="multipart/form-data" class="space-y-5"
              x-data="{ isUpdating: false }"
              @submit="if (isUpdating) { $event.preventDefault(); return false; } isUpdating = true">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Siswa</label>
                <input type="text" :value="editLog.student_name" disabled class="w-full px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 text-sm cursor-not-allowed">
            </div>
            <div x-data="{ editRuleFilter: 'all' }">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-700">Peraturan <span class="text-red-500">*</span></label>
                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 border border-slate-200 text-xs">
                        <button type="button" @click="editRuleFilter = 'all'" :class="editRuleFilter === 'all' ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer">Semua</button>
                        <button type="button" @click="editRuleFilter = 'violation'" :class="editRuleFilter === 'violation' ? 'bg-red-50 text-red-600 border border-red-200 font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer">Pelanggaran</button>
                        <button type="button" @click="editRuleFilter = 'achievement'" :class="editRuleFilter === 'achievement' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200 font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer">Prestasi</button>
                    </div>
                </div>
                <select name="rule_id" x-model="editLog.rule_id" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Peraturan --</option>
                    <optgroup label="PELANGGARAN" x-show="editRuleFilter === 'all' || editRuleFilter === 'violation'">
                        @foreach($rules->where('type', \App\Enums\RuleType::Violation) as $rule)
                            <option value="{{ $rule->id }}" x-show="editRuleFilter === 'all' || editRuleFilter === 'violation'">
                                {{ $rule->name }} ({{ $rule->points }} poin) @if($rule->category) [{{ $rule->category->label() }}] @endif [Pelanggaran]
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="PRESTASI" x-show="editRuleFilter === 'all' || editRuleFilter === 'achievement'">
                        @foreach($rules->where('type', \App\Enums\RuleType::Achievement) as $rule)
                            <option value="{{ $rule->id }}" x-show="editRuleFilter === 'all' || editRuleFilter === 'achievement'">
                                {{ $rule->name }} (+{{ $rule->points }} poin) [Prestasi]
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-medium text-slate-700">Tanggal Kejadian</label>
                    <button type="button" @click="const tzoffset = (new Date()).getTimezoneOffset() * 60000; editLog.occurred_at = (new Date(Date.now() - tzoffset)).toISOString().slice(0,19);" class="text-xs text-blue-600 hover:text-blue-700 cursor-pointer">Set Waktu Saat Ini</button>
                </div>
                <input type="datetime-local" step="1" name="occurred_at" x-model="editLog.occurred_at" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan</label>
                <textarea name="note" x-model="editLog.note" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan opsional..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Bukti (Kamera / Galeri)</label>
                <input type="hidden" name="remove_evidence" :value="removeEvidence ? '1' : '0'">

                {{-- Case 1: New file selected to replace --}}
                <div x-show="editPreviewUrl" style="display: none;">
                    <div class="bg-slate-50 rounded-xl border border-blue-200 p-3">
                        <div class="flex items-center gap-3">
                            <div style="width: 84px; height: 84px; min-width: 84px; max-width: 84px; min-height: 84px; max-height: 84px; overflow: hidden; border-radius: 10px; border: 1px solid #cbd5e1; background-color: #f1f5f9; flex-shrink: 0; position: relative;" class="cursor-pointer group/preview shadow-2xs" @click="openImagePreview(editPreviewUrl, editFileName)">
                                <img :src="editPreviewUrl" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="Foto Baru">
                                <div style="position: absolute; inset: 0; background-color: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: 600;" class="opacity-0 group-hover/preview:opacity-100 transition-opacity">
                                    Perbesar
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-700">Foto Baru Terpilih</span>
                                    <span class="text-xs text-slate-500" x-text="editFileSize"></span>
                                </div>
                                <p class="text-xs font-medium text-slate-800 truncate" x-text="editFileName"></p>
                                <div class="flex items-center gap-2 mt-2.5">
                                    <button type="button" @click="openImagePreview(editPreviewUrl, editFileName)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium transition-colors shadow-2xs cursor-pointer">
                                        Preview
                                    </button>
                                    <label for="admin_edit_evidence_input" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium cursor-pointer transition-colors shadow-2xs">
                                        Ganti
                                    </label>
                                    <button type="button" @click="clearEditFile()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 text-xs font-medium cursor-pointer transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Case 2: Current photo from existing log, not yet replaced and not removed --}}
                <div x-show="!editPreviewUrl && editLog.evidence_url && !removeEvidence">
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center gap-3">
                            <div style="width: 84px; height: 84px; min-width: 84px; max-width: 84px; min-height: 84px; max-height: 84px; overflow: hidden; border-radius: 10px; border: 1px solid #cbd5e1; background-color: #f1f5f9; flex-shrink: 0; position: relative;" class="cursor-pointer group/preview shadow-2xs" @click="openImagePreview(editLog.evidence_url, 'Foto Bukti ' + editLog.student_name)">
                                <img :src="editLog.evidence_url" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="Foto Saat Ini">
                                <div style="position: absolute; inset: 0; background-color: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: 600;" class="opacity-0 group-hover/preview:opacity-100 transition-opacity">
                                    Perbesar
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-200 text-slate-700 mb-1">Foto Bukti Saat Ini</span>
                                <p class="text-xs text-slate-500 truncate" x-text="editLog.evidence_url"></p>
                                <div class="flex items-center gap-2 mt-2.5">
                                    <button type="button" @click="openImagePreview(editLog.evidence_url, 'Foto Bukti ' + editLog.student_name)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium transition-colors shadow-2xs cursor-pointer">
                                        Preview
                                    </button>
                                    <label for="admin_edit_evidence_input" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 text-xs font-medium cursor-pointer transition-colors shadow-2xs">
                                        Ganti Foto
                                    </label>
                                    <button type="button" @click="removeEvidence = true" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 text-xs font-medium cursor-pointer transition-colors">
                                        Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Case 3: No photo yet or marked as removed --}}
                <div x-show="!editPreviewUrl && (!editLog.evidence_url || removeEvidence)">
                    <label for="admin_edit_evidence_input" class="flex flex-col items-center justify-center w-full px-4 py-5 border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-xl cursor-pointer bg-slate-50/70 hover:bg-blue-50/30 transition-all group">
                        <div class="flex items-center gap-3 text-slate-500 group-hover:text-blue-600 mb-2">
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 group-hover:border-blue-300 group-hover:bg-blue-50 flex items-center justify-center transition-colors shadow-2xs">
                                <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 group-hover:border-blue-300 group-hover:bg-blue-50 flex items-center justify-center transition-colors shadow-2xs">
                                <svg class="w-5 h-5 text-slate-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors">
                            Upload Foto Bukti (Kamera / Galeri)
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Format: JPG, PNG, WEBP (Maksimal 5MB)
                        </p>
                        <template x-if="removeEvidence && editLog.evidence_url">
                            <div class="mt-2 text-xs text-amber-600 font-medium">
                                Foto lama akan dihapus saat disimpan. <span class="text-blue-600 underline cursor-pointer" @click.stop.prevent="removeEvidence = false">Batalkan Hapus</span>
                            </div>
                        </template>
                    </label>
                </div>

                <input id="admin_edit_evidence_input" name="evidence" type="file" accept="image/*" @change="handleEditFile($event)" class="hidden">
                @error('evidence')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                <button type="button" @click="showEditModal = false" class="px-6 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm hover:bg-slate-200 transition-colors cursor-pointer">Batal</button>
                <button type="submit" :disabled="isUpdating" :class="isUpdating ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer">
                    <svg x-show="isUpdating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span x-text="isUpdating ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</template>

    {{-- Lightbox / Image Preview Modal --}}
    <template x-teleport="body">
        <div x-show="previewModal.show"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 flex items-center justify-center p-4"
             style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(6px); z-index: 99999;"
             @click="previewModal.show = false"
             @keydown.escape.window="previewModal.show = false"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-3xl w-full overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="text-base font-bold text-slate-900 truncate" x-text="previewModal.title">Preview Foto Bukti</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="previewModal.url" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Buka Tab Baru
                        </a>
                        <button type="button" @click="previewModal.show = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-auto flex items-center justify-center bg-slate-950/5">
                    <img :src="previewModal.url" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain shadow-md" alt="Bukti Foto">
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
