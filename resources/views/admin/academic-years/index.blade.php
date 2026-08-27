@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Manajemen Tahun Ajaran')
@section('page-header', 'Tahun Ajaran Aktif')
@section('page-desc', 'Kelola periode tahun ajaran dan semester sekolah')
@section('page-actions')
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">+ Tambah</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-surface-lighter/50">
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahun Ajaran</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Semester</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($years as $year)
                <tr class="hover:bg-surface-lighter/30 transition-colors group" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('year_id') == $year->id && $errors->any()) ? 'true' : 'false' }} }">
                    <td class="px-6 py-4 text-sm text-gray-200 font-medium group-hover:text-primary-300 transition-colors">{{ $year->year_label }}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ $year->semester->label() }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($year->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border bg-green-500/10 text-green-400 border-green-500/20">Aktif</span>
                        @else
                            <form method="POST" action="{{ route('admin.academic-years.activate', $year) }}" class="inline">@csrf
                                <button class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-500/10 text-gray-400 border-gray-500/20 hover:text-green-400 hover:border-green-500/20 transition-colors cursor-pointer">Aktifkan</button>
                            </form>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}" class="inline" onsubmit="return confirm('Hapus tahun ajaran ini?')">@csrf @method('DELETE')
                                <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
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
                                    <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-5">
                                            <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Tahun Ajaran</h3>
                                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.academic-years.update', $year) }}" class="space-y-5 text-left">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="year_id" value="{{ $year->id }}">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tahun Ajaran</label>
                                                <input type="text" name="year_label" value="{{ old('year_id') == $year->id ? old('year_label') : $year->year_label }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Semester</label>
                                                <select name="semester" required class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="1" {{ (old('year_id') == $year->id ? old('semester') : $year->semester->value) == 1 ? 'selected' : '' }}>Ganjil</option>
                                                    <option value="2" {{ (old('year_id') == $year->id ? old('semester') : $year->semester->value) == 2 ? 'selected' : '' }}>Genap</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center gap-3 pt-4 border-t border-gray-700/50 mt-6 justify-end">
                                                <button type="button" @click="showEditModal = false" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors">Batal</button>
                                                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Perbarui</button>
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
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-base font-medium">Belum ada tahun ajaran.</p>
                            <p class="text-sm mt-1">Tambahkan tahun ajaran pertama Anda.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($years->hasPages())
    <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
        {{ $years->withQueryString()->links() }}
    </div>
    @endif
</div>
    <div class="mt-6">{{ $years->withQueryString()->links() }}</div>

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
                    <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Tambah Tahun Ajaran</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Tahun Ajaran <span class="text-red-400">*</span></label>
                        <input type="text" name="year_label" value="{{ old('year_label') }}" required placeholder="2026/2027" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Semester <span class="text-red-400">*</span></label>
                        <select name="semester" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Ganjil</option>
                            <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded bg-surface-light border-gray-600">
                        <label for="is_active" class="text-sm text-gray-300">Jadikan aktif</label>
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
