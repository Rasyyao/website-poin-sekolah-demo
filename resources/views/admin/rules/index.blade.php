@extends('layouts.app')
@section('title', 'Peraturan')
@section('page-title', 'Bank Peraturan Sekolah')
@section('page-header', 'Daftar Peraturan')
@section('page-desc', 'Kelola daftar aturan pelanggaran, prestasi, dan bobot poinnya')
@section('page-actions')
    <p class="text-sm text-gray-400 mr-2">Total: <span class="font-semibold text-gray-200">{{ $grouped->flatten()->count() }}</span> peraturan</p>
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">+ Tambah Peraturan</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

{{-- Accordion Groups --}}
<div class="space-y-3">

    @php
        $groups = [
            'achievement' => [
                'label'    => 'Prestasi',
                'icon'     => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                'color'    => 'green',
                'bg'       => 'bg-green-500/10',
                'border'   => 'border-green-500/30',
                'text'     => 'text-green-400',
                'dot'      => 'bg-green-400',
            ],
            'violation' => [
                'label'    => 'Pelanggaran',
                'icon'     => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'color'    => 'red',
                'bg'       => 'bg-red-500/10',
                'border'   => 'border-red-500/30',
                'text'     => 'text-red-400',
                'dot'      => 'bg-red-400',
            ],
        ];
    @endphp

    @foreach($groups as $typeKey => $group)
        @php $rules = $grouped->get($typeKey, collect()); @endphp
        <div x-data="{ open: true }" class="rounded-xl border border-gray-700/50 overflow-hidden">

            {{-- Accordion Header --}}
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-5 py-4 bg-surface-light hover:bg-surface-lighter transition-colors cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg {{ $group['bg'] }} {{ $group['border'] }} border flex items-center justify-center">
                        <svg class="w-4 h-4 {{ $group['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $group['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-gray-100">{{ $group['label'] }}</p>
                        <p class="text-xs text-gray-500">{{ $rules->count() }} peraturan</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                     :class="open ? 'rotate-180' : 'rotate-0'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Accordion Body --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-t border-gray-700/50 bg-surface-lighter/50">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Poin</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30 bg-surface-light">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-surface-lighter/30 transition-colors group" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('rule_id') == $rule->id && $errors->any()) ? 'true' : 'false' }} }" {{ !$rule->is_active ? 'opacity-40' : '' }}>
                                <td class="px-6 py-4 text-sm text-gray-200">
                                    <div class="flex items-center gap-2 group-hover:text-primary-300 transition-colors">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $group['dot'] }} flex-shrink-0"></div>
                                        {{ $rule->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    @if($rule->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-700/50 text-gray-300 border border-gray-600/50">{{ $rule->category->label() }}</span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-surface text-sm font-bold border {{ $rule->type->value === 'violation' ? 'border-red-500/20 text-red-400' : 'border-green-500/20 text-green-400' }}">
                                        {{ $rule->type->value === 'achievement' ? '+' : '' }}{{ $rule->points }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.rules.toggle-active', $rule) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-colors cursor-pointer {{ $rule->is_active ? 'bg-green-500/10 text-green-400 border-green-500/20 hover:bg-green-500/20' : 'bg-gray-500/10 text-gray-500 border-gray-500/20 hover:bg-gray-500/20 hover:text-gray-300' }}">
                                            {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.rules.destroy', $rule) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="Swal.fire({ title: 'Hapus peraturan?', html: 'Data <strong>{{ addslashes($rule->name) }}</strong> akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937', iconColor: '#ef4444' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
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
                                                        <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Peraturan</h3>
                                                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </div>

                                                    <form method="POST" action="{{ route('admin.rules.update', $rule) }}" class="space-y-5 text-left">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="rule_id" value="{{ $rule->id }}">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Peraturan <span class="text-red-400">*</span></label>
                                                            <input type="text" name="name" value="{{ old('rule_id') == $rule->id ? old('name') : $rule->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                                                                <select name="type" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                    <option value="violation" {{ (old('rule_id') == $rule->id ? old('type') : $rule->type->value) === 'violation' ? 'selected' : '' }}>Pelanggaran</option>
                                                                    <option value="achievement" {{ (old('rule_id') == $rule->id ? old('type') : $rule->type->value) === 'achievement' ? 'selected' : '' }}>Prestasi</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Kategori</label>
                                                                <select name="category" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                                    <option value="">-- Tidak ada --</option>
                                                                    <option value="ringan" {{ (old('rule_id') == $rule->id ? old('category') : $rule->category?->value) === 'ringan' ? 'selected' : '' }}>Ringan</option>
                                                                    <option value="sedang" {{ (old('rule_id') == $rule->id ? old('category') : $rule->category?->value) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                                                    <option value="berat" {{ (old('rule_id') == $rule->id ? old('category') : $rule->category?->value) === 'berat' ? 'selected' : '' }}>Berat</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Bobot Poin <span class="text-red-400">*</span></label>
                                                                <input type="number" name="points" value="{{ old('rule_id') == $rule->id ? old('points') : $rule->points }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Deskripsi</label>
                                                            <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('rule_id') == $rule->id ? old('description') : $rule->description }}</textarea>
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
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">Belum ada peraturan {{ $group['label'] }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    @endforeach
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
                    <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Tambah Peraturan Baru</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.rules.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Peraturan <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="contoh: Terlambat masuk sekolah">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                            <select name="type" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="violation" {{ old('type') === 'violation' ? 'selected' : '' }}>Pelanggaran</option>
                                <option value="achievement" {{ old('type') === 'achievement' ? 'selected' : '' }}>Prestasi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Kategori</label>
                            <select name="category" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Tidak ada --</option>
                                <option value="ringan" {{ old('category') === 'ringan' ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ old('category') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="berat" {{ old('category') === 'berat' ? 'selected' : '' }}>Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Bobot Poin <span class="text-red-400">*</span></label>
                            <input type="number" name="points" value="{{ old('points') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="10">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea>
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
