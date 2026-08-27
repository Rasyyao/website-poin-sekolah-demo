@extends('layouts.app')
@section('title', 'Daftar Siswa')
@section('page-title', 'Manajemen Siswa')
@section('page-header', 'Daftar Siswa')
@section('page-desc', 'Kelola data siswa, pencarian, dan histori pelanggaran')
@section('page-actions')
    <form method="GET" class="flex items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN..."
               class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 w-48 sm:w-64">
        <select name="class_id" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 hidden sm:block">
            <option value="">Semua Kelas</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer hidden sm:block">Cari</button>
    </form>
    <div class="flex items-center gap-2 border-l border-gray-700/50 pl-3 ml-1">
        <a href="{{ route('admin.exports.students', 'pdf') }}" target="_blank" class="px-3 py-2 rounded-lg bg-red-500/10 text-red-500 text-sm font-medium hover:bg-red-500 hover:text-white transition-all border border-red-500/20 cursor-pointer flex items-center justify-center" title="Export PDF">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </a>
        <a href="{{ route('admin.exports.students', 'excel') }}" class="px-3 py-2 rounded-lg bg-green-500/10 text-green-500 text-sm font-medium hover:bg-green-500 hover:text-white transition-all border border-green-500/20 cursor-pointer flex items-center justify-center" title="Export Excel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </a>
    </div>
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer ml-3">+ Tambah Siswa</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="bg-surface-lighter/50">
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">NISN</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kelas</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Total Poin</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($students as $student)
                <tr class="hover:bg-surface-lighter/30 transition-colors group" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('student_id') == $student->id && $errors->any()) ? 'true' : 'false' }} }">
                    <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $student->nisn }}</td>
                    <td class="px-6 py-4 text-sm text-gray-200 font-medium group-hover:text-primary-300 transition-colors">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $student->currentClass?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        @php $tp = $student->totalPoints(); @endphp
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $tp > 0 ? 'border-red-500/20 text-red-400' : ($tp < 0 ? 'border-green-500/20 text-green-400' : 'border-gray-600/50 text-gray-400') }}">
                            {{ abs($tp) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors border border-blue-500/20" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                    onclick="Swal.fire({ title: 'Hapus siswa?', html: 'Data <strong>{{ addslashes($student->name) }}</strong> akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937', iconColor: '#ef4444' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
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
                                    <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-5">
                                            <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Data Siswa</h3>
                                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-5 text-left">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">NISN <span class="text-red-400">*</span></label>
                                                    <input type="text" name="nisn" value="{{ old('student_id') == $student->id ? old('nisn') : $student->nisn }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                                                    <input type="text" name="name" value="{{ old('student_id') == $student->id ? old('name') : $student->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Kelas</label>
                                                    <select name="class_id" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                        <option value="">-- Pilih Kelas --</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class->id }}" {{ (old('student_id') == $student->id ? old('class_id') : $student->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Tanggal Lahir</label>
                                                    <input type="date" name="birth_date" value="{{ old('student_id') == $student->id ? old('birth_date') : $student->birth_date?->format('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" style="color-scheme: dark;">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Kontak Orang Tua (WA/Email)</label>
                                                <input type="text" name="parent_contact" value="{{ old('student_id') == $student->id ? old('parent_contact') : $student->parent_contact }}" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="08123456789">
                                            </div>
                                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700/50 mt-6">
                                                <button type="button" @click="showEditModal = false" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                                                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-base font-medium">Belum ada data siswa.</p>
                            <p class="text-sm mt-1">Siswa baru yang ditambahkan akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($students->hasPages())
    <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
        {{ $students->withQueryString()->links() }}
    </div>
    @endif
</div>

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
        <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
            
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Tambah Siswa Baru</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">NISN <span class="text-red-400">*</span></label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Kelas</label>
                            <select name="class_id" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" style="color-scheme: dark;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Kontak Orang Tua (WA/Email)</label>
                        <input type="text" name="parent_contact" value="{{ old('parent_contact') }}" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="08123456789">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-700/50 mt-6">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Simpan</button>
                    </div>
                </form>
            
        </div>
    </div>
    </div>
</template>
</div>
@endsection
