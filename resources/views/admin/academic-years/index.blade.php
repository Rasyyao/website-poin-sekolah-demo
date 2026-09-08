@extends('layouts.app')
@section('title', 'Konfigurasi Sistem')
@section('page-title', 'Konfigurasi Sistem')
@section('page-header', 'Konfigurasi Sistem')
@section('page-desc', 'Atur nama website, identitas sekolah, dan tahun pelajaran aktif')
@section('page-actions')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        {{-- Card 1: Identitas & Nama Website --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Identitas & Nama Website</h3>
                        <p class="text-xs text-slate-500">Konfigurasi label aplikasi dan nama institusi sekolah</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.school.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="website_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Website / Aplikasi</label>
                        <input type="text"
                               name="website_name"
                               id="website_name"
                               value="{{ old('website_name', $school?->settings['website_name'] ?? 'Sistem Poin Sekolah') }}"
                               required
                               placeholder="Contoh: Sistem Poin Sekolah, Portal Kedisiplinan"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-slate-400 mt-1.5">Nama ini tampil di sidebar, bilah judul navigasi, tab browser, dan halaman login.</p>
                    </div>

                    <div>
                        <label for="school_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Sekolah</label>
                        <input type="text"
                               name="name"
                               id="school_name"
                               value="{{ old('name', $school?->name ?? '') }}"
                               required
                               placeholder="Contoh: SMA Negeri 1 Indonesia"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-slate-400 mt-1.5">Nama resmi sekolah yang tercantum pada dokumen laporan dan ekspor PDF.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
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
        </div>

        {{-- Card 2: Tahun Pelajaran Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Tahun Pelajaran & Semester</h3>
                        <p class="text-xs text-slate-500">Atur periode akademik yang sedang aktif di sistem</p>
                    </div>
                </div>

                {{-- Current Active Status Display --}}
                <div class="mb-5 p-4 rounded-xl bg-slate-50 border border-slate-200">
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

                <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="is_active" value="1">

                    <div>
                        <label for="year_label" class="block text-sm font-medium text-slate-700 mb-1">Tahun Pelajaran</label>
                        <input type="text"
                               name="year_label"
                               id="year_label"
                               value="{{ old('year_label', $activeYear?->year_label ?? '') }}"
                               required
                               placeholder="2026/2027"
                               pattern="^\d{4}/\d{4}$"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        <p class="text-xs text-slate-400 mt-1.5">Format wajib: <span class="font-mono font-medium text-slate-600">YYYY/YYYY</span> (contoh: 2026/2027)</p>
                    </div>

                    <div>
                        <label for="semester" class="block text-sm font-medium text-slate-700 mb-1">Semester</label>
                        <select name="semester"
                                id="semester"
                                required
                                class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ old('semester', $activeYear?->semester?->value ?? 1) == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('semester', $activeYear?->semester?->value ?? 1) == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                        <p class="text-xs text-slate-400 mt-1.5">Pilih semester yang berlaku untuk pencatatan poin dan kelas.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
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
    </div>
</div>
@endsection
