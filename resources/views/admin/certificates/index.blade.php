@extends('layouts.app')
@section('title', 'Sertifikat')
@section('page-title', 'Sertifikat Penghargaan')
@section('page-header', 'Sertifikat Penghargaan')
@section('page-desc', 'Daftar sertifikat yang diterbitkan otomatis saat siswa mencapai ambang batas prestasi.')
@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div
            class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative w-full sm:w-auto">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN..."
                        class="pl-9 pr-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 w-full sm:w-64">
                </div>
                <select name="class_id" onchange="this.form.submit()"
                    class="pl-9 pr-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 hidden sm:block">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer hidden sm:block shadow-sm">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Sertifikat
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Penghargaan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Poin
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Terbit
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($certificates as $certificate)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-xs text-slate-500 font-mono whitespace-nowrap">
                                {{ $certificate->certificate_number }}</td>
                            <td
                                class="px-6 py-4 text-sm text-slate-900 font-medium group-hover:text-emerald-600 transition-colors">
                                {{ $certificate->student->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $certificate->student->currentClass->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $certificate->ruleThreshold->action ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                    +{{ $certificate->points_at_issue }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $certificate->issued_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.certificates.print', $certificate) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors border border-emerald-200 cursor-pointer shadow-2xs"
                                        title="Cetak PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            onclick="Swal.fire({ title: 'Hapus sertifikat?', text: 'Data akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-500">Belum ada sertifikat yang diterbitkan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($certificates->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
@endsection
