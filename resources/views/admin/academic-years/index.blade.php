@extends('layouts.app')
@section('title', 'Konfigurasi Sistem')
@section('page-title', 'Konfigurasi Sistem')
@section('page-header', 'Konfigurasi Sistem')
@section('page-desc', 'Atur nama website, identitas sekolah, tahun pelajaran, dan penandatangan sertifikat')
@section('page-actions')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Toast Notification --}}
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- Two-column layout for the first two sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Section: Identitas & Nama Website --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-2.5 mb-1">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">Identitas & Nama Website</h3>
                </div>
                <p class="text-xs text-slate-500 ml-9">Konfigurasi label aplikasi dan nama institusi sekolah</p>
            </div>

            <form method="POST" action="{{ route('admin.school.update') }}" class="flex flex-col flex-1">
                @csrf
                @method('PUT')

                <div class="divide-y divide-slate-100 flex-1">
                    <div class="px-6 py-5">
                        <label for="website_name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Website / Aplikasi</label>
                        <p class="text-xs text-slate-500 mb-3">Tampil di sidebar, bilah judul navigasi, tab browser, dan halaman login.</p>
                        <input type="text"
                               name="website_name"
                               id="website_name"
                               value="{{ old('website_name', $school?->settings['website_name'] ?? 'Sistem Poin Sekolah') }}"
                               required
                               placeholder="Contoh: Sistem Poin Sekolah, Portal Kedisiplinan"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="px-6 py-5">
                        <label for="school_name" class="block text-sm font-semibold text-slate-800 mb-1">Nama Sekolah</label>
                        <p class="text-xs text-slate-500 mb-3">Nama resmi sekolah yang tercantum pada dokumen laporan dan ekspor PDF.</p>
                        <input type="text"
                               name="name"
                               id="school_name"
                               value="{{ old('name', $school?->name ?? '') }}"
                               required
                               placeholder="Contoh: SMA Negeri 1 Indonesia"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Identitas</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Section: Tahun Pelajaran & Semester --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-2.5 mb-1">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">Tahun Pelajaran & Semester</h3>
                </div>
                <p class="text-xs text-slate-500 ml-9">Atur periode akademik yang sedang aktif di sistem</p>
            </div>

            <div class="px-6 pt-5">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Periode Aktif</div>
                    @if($activeYear)
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-base font-bold text-slate-900">{{ $activeYear->year_label }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                Semester {{ $activeYear->semester->label() }}
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-slate-500 text-sm">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span>Belum ada tahun pelajaran yang aktif.</span>
                        </div>
                    @endif
                    <p class="text-xs text-slate-500 mt-2">
                        Sistem hanya menggunakan 1 tahun pelajaran aktif. Pengaturan di bawah akan langsung memperbarui periode operasional berjalan.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.academic-years.store') }}" class="flex flex-col flex-1">
                @csrf
                <input type="hidden" name="is_active" value="1">

                <div class="divide-y divide-slate-100 mt-1 flex-1">
                    <div class="px-6 py-5">
                        <label for="year_label" class="block text-sm font-semibold text-slate-800 mb-1">Tahun Pelajaran</label>
                        <p class="text-xs text-slate-500 mb-3">Format wajib: <span class="font-mono font-medium text-slate-600">YYYY/YYYY</span> (contoh: 2026/2027)</p>
                        <input type="text"
                               name="year_label"
                               id="year_label"
                               value="{{ old('year_label', $activeYear?->year_label ?? '') }}"
                               required
                               placeholder="2026/2027"
                               pattern="^\d{4}/\d{4}$"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                    </div>

                    <div class="px-6 py-5">
                        <label for="semester" class="block text-sm font-semibold text-slate-800 mb-1">Semester</label>
                        <p class="text-xs text-slate-500 mb-3">Pilih semester yang berlaku untuk pencatatan poin dan kelas.</p>
                        <select name="semester"
                                id="semester"
                                required
                                class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ old('semester', $activeYear?->semester?->value ?? 1) == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('semester', $activeYear?->semester?->value ?? 1) == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Terapkan Tahun Pelajaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Section: Penandatangan Sertifikat --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900">Penandatangan Sertifikat</h3>
            </div>
            <p class="text-xs text-slate-500 ml-9">Nama & tanda tangan yang tampil pada Sertifikat Penghargaan yang diterbitkan otomatis</p>
        </div>

        <form method="POST" action="{{ route('admin.school.signatories.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Two-column grid for signatories --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">

                {{-- ===== KEPALA SEKOLAH ===== --}}
                <div class="px-6 py-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">1</span>
                        <h4 class="text-sm font-semibold text-slate-700">Kepala Sekolah</h4>
                    </div>

                    {{-- Principal Name --}}
                    <div>
                        <label for="principal_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Kepala Sekolah</label>
                        <p class="text-xs text-slate-400 mb-2">Dicetak di bawah tanda tangan pada sertifikat.</p>
                        <input type="text"
                               name="principal_name"
                               id="principal_name"
                               value="{{ old('principal_name', $school?->principalName()) }}"
                               placeholder="Contoh: Drs. Ahmad Fauzi, M.Pd."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Principal Signature Upload --}}
                    <div x-data="{
                        remove: false,
                        uploading: false,
                        uploaded: false,
                        previewUrl: '{{ $school?->principalSignatureUrl() ?? '' }}',
                        fileName: '',
                        handleFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            this.uploading = true;
                            this.uploaded = false;
                            this.fileName = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                setTimeout(() => {
                                    this.previewUrl = e.target.result;
                                    this.uploading = false;
                                    this.uploaded = true;
                                    this.remove = false;
                                    showToast('Gambar tanda tangan kepala sekolah siap diunggah!', 'success');
                                }, 600);
                            };
                            reader.readAsDataURL(file);
                        }
                    }">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanda Tangan</label>
                        <p class="text-xs text-slate-400 mb-3">PNG/JPG, latar transparan disarankan, maks. 2MB.</p>

                        {{-- Preview area --}}
                        <div class="relative rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-4 flex items-center gap-4 transition-colors"
                             :class="{ 'border-violet-300 bg-violet-50/50': uploading || uploaded, 'border-red-200 bg-red-50/30': remove }">

                            {{-- Image preview / placeholder --}}
                            <div class="shrink-0 relative">
                                <div class="w-20 h-14 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-sm">
                                    <template x-if="uploading">
                                        <div class="flex flex-col items-center gap-1">
                                            <svg class="w-5 h-5 text-violet-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <span class="text-[9px] text-violet-500 font-medium">Memuat...</span>
                                        </div>
                                    </template>
                                    <template x-if="!uploading && previewUrl && !remove">
                                        <img :src="previewUrl" alt="Preview tanda tangan" class="h-full w-full object-contain p-1">
                                    </template>
                                    <template x-if="!uploading && (!previewUrl || remove)">
                                        <div class="flex flex-col items-center gap-1 text-slate-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[9px]">Belum ada</span>
                                        </div>
                                    </template>
                                </div>
                                {{-- Uploaded badge --}}
                                <template x-if="uploaded && !remove">
                                    <div class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-emerald-500 rounded-full flex items-center justify-center shadow">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Info & actions --}}
                            <div class="flex-1 min-w-0">
                                <template x-if="uploading">
                                    <p class="text-xs text-violet-600 font-medium">Memproses gambar...</p>
                                </template>
                                <template x-if="!uploading && uploaded && !remove">
                                    <div>
                                        <p class="text-xs font-semibold text-emerald-600">✓ Gambar siap diunggah</p>
                                        <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="fileName"></p>
                                    </div>
                                </template>
                                <template x-if="!uploading && !uploaded && !remove && previewUrl">
                                    <p class="text-xs text-slate-500">Tanda tangan tersimpan</p>
                                </template>
                                <template x-if="!uploading && (!previewUrl || (!uploaded && !previewUrl))">
                                    <p class="text-xs text-slate-400">Belum ada gambar tanda tangan</p>
                                </template>
                                <template x-if="remove">
                                    <p class="text-xs text-red-500 font-medium">Akan dihapus saat disimpan</p>
                                </template>

                                <div class="flex items-center gap-2 mt-2.5">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-medium cursor-pointer transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span x-text="previewUrl && !remove ? 'Ganti' : 'Upload'"></span>
                                        <input type="file" name="principal_signature" accept="image/*" class="hidden" @change="handleFile($event)">
                                    </label>

                                    @if($school?->principalSignatureUrl())
                                        <input type="checkbox" name="remove_principal_signature" value="1" x-model="remove" class="hidden" id="remove_principal_signature">
                                        <button type="button" @click="remove = !remove"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
                                                :class="remove ? 'bg-slate-100 text-slate-500' : 'bg-red-50 text-red-500 hover:bg-red-100'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span x-text="remove ? 'Batalkan' : 'Hapus'"></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== GURU KESISWAAN ===== --}}
                <div class="px-6 py-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">2</span>
                        <h4 class="text-sm font-semibold text-slate-700">Guru Kesiswaan</h4>
                    </div>

                    {{-- Kesiswaan Name --}}
                    <div>
                        <label for="kesiswaan_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Guru Kesiswaan</label>
                        <p class="text-xs text-slate-400 mb-2">Dicetak di bawah tanda tangan pada sertifikat.</p>
                        <input type="text"
                               name="kesiswaan_name"
                               id="kesiswaan_name"
                               value="{{ old('kesiswaan_name', $school?->kesiswaanName()) }}"
                               placeholder="Contoh: Siti Nurhaliza, S.Pd."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Kesiswaan Signature Upload --}}
                    <div x-data="{
                        remove: false,
                        uploading: false,
                        uploaded: false,
                        previewUrl: '{{ $school?->kesiswaanSignatureUrl() ?? '' }}',
                        fileName: '',
                        handleFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            this.uploading = true;
                            this.uploaded = false;
                            this.fileName = file.name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                setTimeout(() => {
                                    this.previewUrl = e.target.result;
                                    this.uploading = false;
                                    this.uploaded = true;
                                    this.remove = false;
                                    showToast('Gambar tanda tangan guru kesiswaan siap diunggah!', 'success');
                                }, 600);
                            };
                            reader.readAsDataURL(file);
                        }
                    }">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanda Tangan</label>
                        <p class="text-xs text-slate-400 mb-3">PNG/JPG, latar transparan disarankan, maks. 2MB.</p>

                        {{-- Preview area --}}
                        <div class="relative rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-4 flex items-center gap-4 transition-colors"
                             :class="{ 'border-violet-300 bg-violet-50/50': uploading || uploaded, 'border-red-200 bg-red-50/30': remove }">

                            {{-- Image preview / placeholder --}}
                            <div class="shrink-0 relative">
                                <div class="w-20 h-14 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-sm">
                                    <template x-if="uploading">
                                        <div class="flex flex-col items-center gap-1">
                                            <svg class="w-5 h-5 text-violet-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <span class="text-[9px] text-violet-500 font-medium">Memuat...</span>
                                        </div>
                                    </template>
                                    <template x-if="!uploading && previewUrl && !remove">
                                        <img :src="previewUrl" alt="Preview tanda tangan" class="h-full w-full object-contain p-1">
                                    </template>
                                    <template x-if="!uploading && (!previewUrl || remove)">
                                        <div class="flex flex-col items-center gap-1 text-slate-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[9px]">Belum ada</span>
                                        </div>
                                    </template>
                                </div>
                                {{-- Uploaded badge --}}
                                <template x-if="uploaded && !remove">
                                    <div class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-emerald-500 rounded-full flex items-center justify-center shadow">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Info & actions --}}
                            <div class="flex-1 min-w-0">
                                <template x-if="uploading">
                                    <p class="text-xs text-violet-600 font-medium">Memproses gambar...</p>
                                </template>
                                <template x-if="!uploading && uploaded && !remove">
                                    <div>
                                        <p class="text-xs font-semibold text-emerald-600">✓ Gambar siap diunggah</p>
                                        <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="fileName"></p>
                                    </div>
                                </template>
                                <template x-if="!uploading && !uploaded && !remove && previewUrl">
                                    <p class="text-xs text-slate-500">Tanda tangan tersimpan</p>
                                </template>
                                <template x-if="!uploading && (!previewUrl || (!uploaded && !previewUrl))">
                                    <p class="text-xs text-slate-400">Belum ada gambar tanda tangan</p>
                                </template>
                                <template x-if="remove">
                                    <p class="text-xs text-red-500 font-medium">Akan dihapus saat disimpan</p>
                                </template>

                                <div class="flex items-center gap-2 mt-2.5">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-medium cursor-pointer transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span x-text="previewUrl && !remove ? 'Ganti' : 'Upload'"></span>
                                        <input type="file" name="kesiswaan_signature" accept="image/*" class="hidden" @change="handleFile($event)">
                                    </label>

                                    @if($school?->kesiswaanSignatureUrl())
                                        <input type="checkbox" name="remove_kesiswaan_signature" value="1" x-model="remove" class="hidden" id="remove_kesiswaan_signature">
                                        <button type="button" @click="remove = !remove"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer"
                                                :class="remove ? 'bg-slate-100 text-slate-500' : 'bg-red-50 text-red-500 hover:bg-red-100'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span x-text="remove ? 'Batalkan' : 'Hapus'"></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition-colors shadow-sm cursor-pointer inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Penandatangan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Notification Script --}}
<style>
.toast-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
    min-width: 260px;
    max-width: 360px;
    pointer-events: all;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.08);
    transform: translateX(120%);
    opacity: 0;
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
}
.toast-item.show {
    transform: translateX(0);
    opacity: 1;
}
.toast-item.toast-success {
    background: #fff;
    border: 1px solid #d1fae5;
    color: #065f46;
}
.toast-item.toast-error {
    background: #fff;
    border: 1px solid #fecaca;
    color: #991b1b;
}
</style>

<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast-item toast-${type}`;

    const icon = type === 'success'
        ? `<svg style="width:18px;height:18px;color:#10b981;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#d1fae5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12l3 3 5-5" stroke="#059669"/></svg>`
        : `<svg style="width:18px;height:18px;color:#ef4444;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#fee2e2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01" stroke="#dc2626"/></svg>`;

    toast.innerHTML = `${icon}<span style="flex:1">${message}</span>`;
    container.appendChild(toast);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => toast.classList.add('show'));
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}
</script>
@endsection
