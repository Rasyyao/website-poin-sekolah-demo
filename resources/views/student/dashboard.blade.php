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
<div x-data="{
    showAppealModal: false,
    appealLogId: null,
    appealRuleName: '',
    evidencePreviewUrl: null,
    evidenceFileName: '',
    evidenceFileSize: '',
    previewModal: {
        show: false,
        url: '',
        title: ''
    },
    handleEvidenceFile(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Ukuran Terlalu Besar', text: 'Maksimal ukuran foto adalah 5MB.' });
                e.target.value = '';
                return;
            }
            this.evidencePreviewUrl = URL.createObjectURL(file);
            this.evidenceFileName = file.name;
            this.evidenceFileSize = (file.size / 1024).toFixed(1) + ' KB';
        }
    },
    clearEvidenceFile() {
        this.evidencePreviewUrl = null;
        this.evidenceFileName = '';
        this.evidenceFileSize = '';
        const input = document.getElementById('appeal_evidence_input');
        if (input) input.value = '';
    },
    openImagePreview(url, title) {
        this.previewModal = { show: true, url, title };
    },
    openAppeal(id, name) {
        this.appealLogId = id;
        this.appealRuleName = name;
        this.clearEvidenceFile();
        this.showAppealModal = true;
    },
    closeAppeal() {
        this.showAppealModal = false;
        this.clearEvidenceFile();
    }
}" @open-appeal.window="openAppeal($event.detail.id, $event.detail.name)">

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
                Sertifikat Saya
            </h3>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($certificates as $certificate)
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $certificate->ruleThreshold->action ?? 'Sertifikat Penghargaan' }}</p>
                        <p class="text-xs text-slate-400">No. {{ $certificate->certificate_number }} &middot; {{ $certificate->issued_at->format('d M Y') }}</p>
                    </div>
                    <a target="_blank" href="{{ route('student.certificates.print', $certificate) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-medium hover:bg-emerald-100 transition-colors shrink-0">
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
                        <td class="px-6 py-4 text-sm text-slate-800 font-medium">
                            <div class="flex items-center gap-2">
                                <span>{{ $log->rule->name }}</span>
                                @if($log->evidence_url)
                                    <button type="button" @click="openImagePreview(@js($log->evidence_url), 'Bukti: {{ addslashes($log->rule->name) }}')" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-600 border border-blue-200 text-[11px] font-medium hover:bg-blue-100 transition-colors cursor-pointer" title="Lihat Foto Bukti">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Foto
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 rounded-full text-sm font-bold border {{ $log->rule->type->value === 'violation' ? 'border-red-200 bg-red-50 text-red-500' : 'border-green-200 bg-green-50 text-green-500' }}">
                                {{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($log->rule->type->value === 'violation')
                                <button type="button" @click="openAppeal({{ $log->id }}, @js($log->rule->name))" class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline cursor-pointer whitespace-nowrap">
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
                 @click="closeAppeal()"
                 class="fixed inset-0 bg-black/50 z-40"></div>

            <div x-show="showAppealModal" x-cloak class="fixed inset-0 flex items-center justify-center p-4 z-50 overflow-y-auto">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg p-6 my-8 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800">Ajukan Banding</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Kirimkan sanggahan beserta bukti pendukung yang valid</p>
                        </div>
                        <button @click="closeAppeal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 cursor-pointer transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('student.appeals.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="points_log_id" :value="appealLogId">
                        
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pelanggaran yang Diajukan</p>
                            <p class="text-sm font-semibold text-slate-800" x-text="appealRuleName"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Banding <span class="text-red-500">*</span></label>
                            <textarea name="reason" required minlength="10" maxlength="2000" rows="3" placeholder="Jelaskan secara rinci alasan banding Anda (minimal 10 karakter)..." class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Foto Bukti Pendukung <span class="text-red-500">*</span>
                            </label>

                            {{-- Dropzone / Upload Box --}}
                            <div x-show="!evidencePreviewUrl">
                                <label for="appeal_evidence_input" class="flex flex-col items-center justify-center w-full px-4 py-5 border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-xl cursor-pointer bg-slate-50/70 hover:bg-blue-50/30 transition-all group">
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
                                    <p class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition-colors text-center">
                                        Ambil dari Kamera atau Pilih dari Galeri
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5 text-center">
                                        Format: JPG, PNG, WEBP (Maksimal 5MB)
                                    </p>
                                </label>
                                <input id="appeal_evidence_input" name="evidence" type="file" accept="image/*" required @change="handleEvidenceFile($event)" class="hidden">
                            </div>

                            {{-- Preview Box when file selected --}}
                            <div x-show="evidencePreviewUrl" style="display: none;">
                                <div class="bg-slate-50 rounded-xl border border-slate-200 p-3">
                                    <div class="flex items-center gap-3">
                                        <div style="width: 84px; height: 84px; min-width: 84px; max-width: 84px; min-height: 84px; max-height: 84px; overflow: hidden; border-radius: 10px; border: 1px solid #cbd5e1; background-color: #f1f5f9; flex-shrink: 0; position: relative;" class="cursor-pointer group/preview shadow-2xs" @click="openImagePreview(evidencePreviewUrl, evidenceFileName)">
                                            <img :src="evidencePreviewUrl" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="Foto Bukti">
                                            <div style="position: absolute; inset: 0; background-color: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: 600;" class="opacity-0 group-hover/preview:opacity-100 transition-opacity">
                                                Perbesar
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-700">Foto Terpilih</span>
                                                <span class="text-xs text-slate-500" x-text="evidenceFileSize"></span>
                                            </div>
                                            <p class="text-xs font-medium text-slate-800 truncate" x-text="evidenceFileName"></p>
                                            <div class="flex items-center gap-2 mt-2.5">
                                                <button type="button" @click="openImagePreview(evidencePreviewUrl, evidenceFileName)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium transition-colors shadow-2xs cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Preview
                                                </button>
                                                <label for="appeal_evidence_input" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium cursor-pointer transition-colors shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Ganti
                                                </label>
                                                <button type="button" @click="clearEvidenceFile()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 text-xs font-medium cursor-pointer transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('evidence')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="closeAppeal()" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer shadow-sm">Kirim Banding</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

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
