@extends('layouts.app')
@section('title', 'Migrasi Kelas')
@section('page-title', 'Migrasi Kelas Massal')
@section('page-header', 'Migrasi Kelas')
@section('page-desc', 'Pilih siswa dan pindahkan ke kelas tujuan')
@section('page-actions')
    <div class="flex items-center gap-3">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
            <div x-show="open" style="display: none;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                 class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50 transform origin-top-right">
                <a href="{{ route('admin.exports.students', 'pdf') }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 7h2v6h-2zM11 15h2v2h-2z"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.exports.students', 'excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
            </div>
        </div>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-sm font-medium hover:bg-slate-200 transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>
@endsection
@section('content')
<div x-data="{ selectedStudents: [], checkAll(e) { if(e.target.checked) { this.selectedStudents = {{ json_encode($students->pluck('id')->toArray()) }} } else { this.selectedStudents = [] } } }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Tabel Siswa -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
                    <form method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-auto">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN..."
                                class="pl-9 pr-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-full sm:w-72 transition-shadow">
                        </div>
                        <select name="class_id" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hidden sm:block cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <div class="text-sm text-slate-500 whitespace-nowrap">
                        Menampilkan <span class="font-medium text-slate-700">{{ $students->firstItem() ?? 0 }}</span> - <span class="font-medium text-slate-700">{{ $students->lastItem() ?? 0 }}</span> dari <span class="font-medium text-slate-700">{{ $students->total() }}</span> siswa
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-4 w-12 text-center">
                                <input type="checkbox" @change="checkAll" :checked="selectedStudents.length === {{ count($students) }} && {{ count($students) }} > 0" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">NISN</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Asal</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" value="{{ $student->id }}" x-model="selectedStudents" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 font-mono">{{ $student->nisn }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-800 font-medium">{{ $student->name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ $student->currentClass?->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <p class="text-base font-medium">Belum ada data siswa.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $students->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
        
        <!-- Kolom Kanan: Form Migrasi -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm sticky top-6">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        Pengaturan Migrasi
                    </h3>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.students.bulk-migrate') }}" id="migrationForm">
                        @csrf
                        <template x-for="id in selectedStudents" :key="id">
                            <input type="hidden" name="student_ids[]" :value="id">
                        </template>

                        <div class="mb-5 bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col items-center justify-center">
                            <div class="text-4xl font-black text-blue-600" x-text="selectedStudents.length">0</div>
                            <div class="text-sm font-medium text-slate-500 mt-1 uppercase tracking-wider">Siswa Terpilih</div>
                        </div>

                        @error('target_class_id')
                            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('student_ids')
                            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Kelas Tujuan <span class="text-red-500">*</span></label>
                                <select name="target_class_id" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('target_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
                                <select name="academic_year_id" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ (old('academic_year_id') == $ay->id) || (!old('academic_year_id') && $ay->is_active) ? 'selected' : '' }}>
                                            {{ $ay->displayLabel() }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" :disabled="selectedStudents.length === 0" class="w-full px-6 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Proses Migrasi Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
