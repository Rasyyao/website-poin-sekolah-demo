@extends('layouts.app')
@section('title', 'Daftar Kelas')
@section('page-title', 'Manajemen Kelas')
@section('page-header', 'Daftar Kelas')
@section('page-desc', 'Kelola data kelas dan wali kelas')
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
                <a href="{{ route('admin.exports.classes', 'pdf') }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 7h2v6h-2zM11 15h2v2h-2z"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.exports.classes', 'excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($classes as $class)
        <div class="bg-white rounded-xl border border-slate-200 p-5 hover:border-primary-500/30 transition-colors" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('class_id') == $class->id && $errors->any()) ? 'true' : 'false' }} }">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-gray-100">{{ $class->name }}</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-blue-600/20 text-primary-400">{{ $class->students_count }} siswa</span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Wali Kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</p>
            <p class="text-xs text-slate-400">{{ $class->academicYear?->year_label ?? '' }} Sem. {{ $class->academicYear?->semester?->label() ?? '' }}</p>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.classes.show', $class) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors border border-blue-500/20" title="Detail">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>

            {{-- Edit Modal --}}
            <template x-teleport="body">
                <div>
                    <div x-show="showEditModal"
                         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="showEditModal = false"
                         class="fixed inset-0 bg-black/75 z-[40]" x-cloak></div>

                    <div x-show="showEditModal"
                         class="fixed inset-0 flex items-center justify-center p-4 z-[50]" x-cloak>
                        <div class="bg-slate-100 rounded-xl shadow-2xl border border-slate-200 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6" @click.stop>
                            <div class="flex justify-between items-center mb-5">
                                <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Kelas</h3>
                                <button @click="showEditModal = false" class="text-slate-500 hover:text-slate-900 cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="space-y-5 text-left">
                                @csrf @method('PUT')
                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kelas <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" value="{{ old('class_id') == $class->id ? old('name') : $class->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-100 border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Wali Kelas</label>
                                    <select name="homeroom_teacher_id" class="w-full px-4 py-2.5 rounded-lg bg-slate-100 border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Tidak ada --</option>
                                        @if(isset($teachers))
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ (old('class_id') == $class->id ? old('homeroom_teacher_id') : $class->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Ajaran <span class="text-red-400">*</span></label>
                                    <select name="academic_year_id" required class="w-full px-4 py-2.5 rounded-lg bg-slate-100 border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @if(isset($academicYears))
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}" {{ (old('class_id') == $class->id ? old('academic_year_id') : $class->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->displayLabel() }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 mt-6 justify-end">
                                    <button type="button" @click="showEditModal = false" class="px-6 py-2.5 rounded-lg bg-whiteer text-slate-700 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer">Perbarui</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @empty
        <div class="col-span-3 text-center py-12 text-slate-400">Belum ada kelas. Buat kelas pertama Anda.</div>
    @endforelse
</div>
    <div class="mt-6">{{ $classes->withQueryString()->links() }}</div>

    {{-- Create Modal --}}
    <template x-teleport="body">
    <div>
    {{-- Backdrop --}}
    <div x-show="showModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="showModal = false"
         class="fixed inset-0 bg-black/75"
         style="z-index:40;"></div>

    {{-- Modal Box --}}
    <div x-show="showModal"
         class="fixed inset-0 flex items-center justify-center p-4"
         style="z-index:50;">
        <div class="bg-slate-100 rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
            
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Tambah Kelas Baru</h3>
                    <button @click="showModal = false" class="text-slate-500 hover:text-slate-900 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kelas <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="contoh: 7A" class="w-full px-4 py-2.5 rounded-lg bg-white border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Wali Kelas</label>
                        <select name="homeroom_teacher_id" class="w-full px-4 py-2.5 rounded-lg bg-white border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Tidak ada --</option>
                            @if(isset($teachers))
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} ({{ $teacher->role->label() }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Ajaran <span class="text-red-400">*</span></label>
                        <select name="academic_year_id" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @if(isset($academicYears))
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->displayLabel() }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 rounded-lg bg-whiteer text-slate-700 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors cursor-pointer">Simpan</button>
                    </div>
                </form>
            
        </div>
    </div>
    </div>
</template>
</div>
@endsection
