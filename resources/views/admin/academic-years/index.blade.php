@extends('layouts.app')
@section('title', 'Konfigurasi Sistem')
@section('page-title', 'Konfigurasi Sistem')
@section('page-header', 'Konfigurasi Sistem')
@section('page-desc', 'Atur nama website, identitas sekolah, tahun pelajaran, dan penandatangan sertifikat')
@section('page-actions')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Section: Identitas & Nama Website --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-900">Identitas & Nama Website</h3>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi label aplikasi dan nama institusi sekolah</p>
        </div>

        <form method="POST" action="{{ route('admin.school.update') }}">
            @csrf
            @method('PUT')

            <div class="divide-y divide-slate-100">
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="website_name" class="block text-sm font-semibold text-slate-800">Nama Website / Aplikasi</label>
                        <p class="text-xs text-slate-500 mt-1">Tampil di sidebar, bilah judul navigasi, tab browser, dan halaman login.</p>
                    </div>
                    <div class="flex-1">
                        <input type="text"
                               name="website_name"
                               id="website_name"
                               value="{{ old('website_name', $school?->settings['website_name'] ?? 'Sistem Poin Sekolah') }}"
                               required
                               placeholder="Contoh: Sistem Poin Sekolah, Portal Kedisiplinan"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="school_name" class="block text-sm font-semibold text-slate-800">Nama Sekolah</label>
                        <p class="text-xs text-slate-500 mt-1">Nama resmi sekolah yang tercantum pada dokumen laporan dan ekspor PDF.</p>
                    </div>
                    <div class="flex-1">
                        <input type="text"
                               name="name"
                               id="school_name"
                               value="{{ old('name', $school?->name ?? '') }}"
                               required
                               placeholder="Contoh: SMA Negeri 1 Indonesia"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-900">Tahun Pelajaran & Semester</h3>
            <p class="text-xs text-slate-500 mt-0.5">Atur periode akademik yang sedang aktif di sistem</p>
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

        <form method="POST" action="{{ route('admin.academic-years.store') }}">
            @csrf
            <input type="hidden" name="is_active" value="1">

            <div class="divide-y divide-slate-100 mt-1">
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="year_label" class="block text-sm font-semibold text-slate-800">Tahun Pelajaran</label>
                        <p class="text-xs text-slate-500 mt-1">Format wajib: <span class="font-mono font-medium text-slate-600">YYYY/YYYY</span> (contoh: 2026/2027)</p>
                    </div>
                    <div class="flex-1">
                        <input type="text"
                               name="year_label"
                               id="year_label"
                               value="{{ old('year_label', $activeYear?->year_label ?? '') }}"
                               required
                               placeholder="2026/2027"
                               pattern="^\d{4}/\d{4}$"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                    </div>
                </div>

                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="semester" class="block text-sm font-semibold text-slate-800">Semester</label>
                        <p class="text-xs text-slate-500 mt-1">Pilih semester yang berlaku untuk pencatatan poin dan kelas.</p>
                    </div>
                    <div class="flex-1">
                        <select name="semester"
                                id="semester"
                                required
                                class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ old('semester', $activeYear?->semester?->value ?? 1) == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('semester', $activeYear?->semester?->value ?? 1) == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                    </div>
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

    {{-- Section: Penandatangan Sertifikat --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-semibold text-slate-900">Penandatangan Sertifikat</h3>
            <p class="text-xs text-slate-500 mt-0.5">Nama & tanda tangan yang tampil pada Sertifikat Penghargaan yang diterbitkan otomatis</p>
        </div>

        <form method="POST" action="{{ route('admin.school.signatories.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="divide-y divide-slate-100">
                {{-- Principal name --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="principal_name" class="block text-sm font-semibold text-slate-800">Nama Kepala Sekolah</label>
                        <p class="text-xs text-slate-500 mt-1">Dicetak di bawah tanda tangan pada sertifikat.</p>
                    </div>
                    <div class="flex-1">
                        <input type="text"
                               name="principal_name"
                               id="principal_name"
                               value="{{ old('principal_name', $school?->principalName()) }}"
                               placeholder="Contoh: Drs. Ahmad Fauzi, M.Pd."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Principal signature --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10" x-data="{ remove: false }">
                    <div class="sm:w-72 shrink-0">
                        <label class="block text-sm font-semibold text-slate-800">Tanda Tangan Kepala Sekolah</label>
                        <p class="text-xs text-slate-500 mt-1">Unggah gambar tanda tangan (PNG/JPG, latar transparan disarankan, maks. 2MB).</p>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-4">
                            @if($school?->principalSignatureUrl())
                                <img x-show="!remove" src="{{ $school->principalSignatureUrl() }}" alt="Tanda tangan kepala sekolah" class="h-12 border border-slate-200 rounded-lg bg-slate-50 px-2 object-contain">
                            @else
                                <div class="h-12 w-24 rounded-lg bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400">Belum ada</div>
                            @endif

                            <div class="flex items-center gap-3 text-xs font-medium">
                                @if($school?->principalSignatureUrl())
                                    <input type="checkbox" name="remove_principal_signature" value="1" x-model="remove" class="hidden" id="remove_principal_signature">
                                    <button type="button" @click="remove = !remove" class="inline-flex items-center gap-1.5 cursor-pointer" :class="remove ? 'text-slate-400' : 'text-red-500 hover:text-red-600'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span x-text="remove ? 'Dibatalkan' : 'Hapus'"></span>
                                    </button>
                                @endif
                                <label class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload
                                    <input type="file" name="principal_signature" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kesiswaan name --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10">
                    <div class="sm:w-72 shrink-0">
                        <label for="kesiswaan_name" class="block text-sm font-semibold text-slate-800">Nama Guru Kesiswaan</label>
                        <p class="text-xs text-slate-500 mt-1">Dicetak di bawah tanda tangan pada sertifikat.</p>
                    </div>
                    <div class="flex-1">
                        <input type="text"
                               name="kesiswaan_name"
                               id="kesiswaan_name"
                               value="{{ old('kesiswaan_name', $school?->kesiswaanName()) }}"
                               placeholder="Contoh: Siti Nurhaliza, S.Pd."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Kesiswaan signature --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start gap-4 sm:gap-10" x-data="{ remove: false }">
                    <div class="sm:w-72 shrink-0">
                        <label class="block text-sm font-semibold text-slate-800">Tanda Tangan Guru Kesiswaan</label>
                        <p class="text-xs text-slate-500 mt-1">Unggah gambar tanda tangan (PNG/JPG, latar transparan disarankan, maks. 2MB).</p>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-4">
                            @if($school?->kesiswaanSignatureUrl())
                                <img x-show="!remove" src="{{ $school->kesiswaanSignatureUrl() }}" alt="Tanda tangan guru kesiswaan" class="h-12 border border-slate-200 rounded-lg bg-slate-50 px-2 object-contain">
                            @else
                                <div class="h-12 w-24 rounded-lg bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400">Belum ada</div>
                            @endif

                            <div class="flex items-center gap-3 text-xs font-medium">
                                @if($school?->kesiswaanSignatureUrl())
                                    <input type="checkbox" name="remove_kesiswaan_signature" value="1" x-model="remove" class="hidden" id="remove_kesiswaan_signature">
                                    <button type="button" @click="remove = !remove" class="inline-flex items-center gap-1.5 cursor-pointer" :class="remove ? 'text-slate-400' : 'text-red-500 hover:text-red-600'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span x-text="remove ? 'Dibatalkan' : 'Hapus'"></span>
                                    </button>
                                @endif
                                <label class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload
                                    <input type="file" name="kesiswaan_signature" accept="image/*" class="hidden">
                                </label>
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
@endsection
