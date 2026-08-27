@extends('layouts.app')
@section('title', 'Ambang Batas')
@section('page-title', 'Konfigurasi Ambang Batas (Rules Engine)')
@section('page-header', 'Ambang Batas Pelanggaran')
@section('page-desc', 'Atur aksi otomatis berdasarkan akumulasi poin pelanggaran siswa.')

@section('page-actions')
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">+ Tambah</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

<div class="space-y-3">
    @forelse($thresholds as $t)
        <div class="bg-surface-light rounded-xl border border-gray-700/50 p-5 flex items-center justify-between" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('threshold_id') == $t->id && $errors->any()) ? 'true' : 'false' }} }">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-red-500/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-red-400">{{ $t->min_points }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-200">≥ {{ $t->min_points }} poin → {{ $t->action }}</p>
                    <p class="text-xs text-gray-500">{{ $t->description ?? '-' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <form method="POST" action="{{ route('admin.rule-thresholds.destroy', $t) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="button"
                        onclick="Swal.fire({ title: 'Hapus ambang batas?', text: 'Data akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>

            {{-- Edit Modal --}}
            <template x-teleport="body">
                <div x-cloak>
                    <div x-show="showEditModal"
                         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="showEditModal = false"
                         class="fixed inset-0 bg-black/75 z-[40]"></div>

                    <div x-show="showEditModal"
                         class="fixed inset-0 flex items-center justify-center p-4 z-[50]">
                        <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6" @click.stop>
                            <div class="flex justify-between items-center mb-5">
                                <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Ambang Batas</h3>
                                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('admin.rule-thresholds.update', $t) }}" class="space-y-5 text-left">
                                @csrf @method('PUT')
                                <input type="hidden" name="threshold_id" value="{{ $t->id }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Minimal Poin Pelanggaran</label>
                                    <input type="number" name="min_points" value="{{ old('threshold_id') == $t->id ? old('min_points') : $t->min_points }}" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Aksi</label>
                                    <input type="text" name="action" value="{{ old('threshold_id') == $t->id ? old('action') : $t->action }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Contoh: Panggilan Orang Tua">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Deskripsi</label>
                                    <textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('threshold_id') == $t->id ? old('description') : $t->description }}</textarea>
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
        </div>
    @empty
        <div class="text-center py-12 text-gray-500">Belum ada konfigurasi ambang batas.</div>
    @endforelse
</div>

    <div class="mt-6">{{ $thresholds->withQueryString()->links() }}</div>

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
                    <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Tambah Ambang Batas</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.rule-thresholds.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Minimal Poin Pelanggaran <span class="text-red-400">*</span></label>
                        <input type="number" name="min_points" value="{{ old('min_points') }}" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="25">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Aksi <span class="text-red-400">*</span></label>
                        <input type="text" name="action" value="{{ old('action') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Contoh: Panggilan Orang Tua">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
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
