@extends('layouts.app')
@section('title', 'Peraturan')
@section('page-title', 'Bank Peraturan Sekolah')
@section('page-header', 'Daftar Peraturan')
@section('page-desc', 'Kelola daftar aturan pelanggaran, prestasi, dan bobot poinnya')

@php
    $isAdmin = in_array(auth()->user()?->role?->value, ['super_admin', 'admin']);
@endphp

@section('page-actions')
    @if($isAdmin)
        <button x-data @click="$dispatch('open-modal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Peraturan
        </button>
    @endif
@endsection

@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

{{-- Accordion Groups --}}
<div class="space-y-4">

    @php
        $groups = [
            'achievement' => [
                'label'    => 'Prestasi',
                'icon'     => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                'color'    => 'green',
                'bg'       => 'bg-green-50',
                'border'   => 'border-green-200',
                'text'     => 'text-green-600',
                'dot'      => 'bg-green-500',
            ],
            'violation' => [
                'label'    => 'Pelanggaran',
                'icon'     => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'color'    => 'red',
                'bg'       => 'bg-red-50',
                'border'   => 'border-red-200',
                'text'     => 'text-red-600',
                'dot'      => 'bg-red-500',
            ],
        ];
    @endphp

    @foreach($groups as $typeKey => $group)
        @php $rules = $grouped->get($typeKey, collect()); @endphp
        <div x-data="{ open: true }" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Accordion Header --}}
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-5 py-4 bg-white hover:bg-slate-50 transition-colors cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg {{ $group['bg'] }} {{ $group['border'] }} border flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $group['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $group['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-800">{{ $group['label'] }}</p>
                        <p class="text-xs text-slate-500">{{ $rules->count() }} peraturan terdaftar</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
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
                <div class="overflow-x-auto border-t border-slate-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200">
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Peraturan</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Bobot Poin</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            @if($isAdmin)
                                <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($rules as $rule)
                            @php
                                $catSlug = $rule->category?->value ?? 'default';
                                $validSlugs = ['ringan', 'sedang', 'berat', 'akademik', 'non_akademik', 'kedisiplinan', 'organisasi', 'sosial'];
                                $badgeClass = in_array($catSlug, $validSlugs) ? "cat-{$catSlug}" : 'cat-default';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors group"
                                x-data="{
                                    showEditModal: {{ (old('_method') == 'PUT' && old('rule_id') == $rule->id && $errors->any()) ? 'true' : 'false' }},
                                    editType: '{{ (old('rule_id') == $rule->id ? old('type') : $rule->type->value) }}',
                                    editIsCustomCategory: {{ (old('rule_id') == $rule->id ? (old('category') === '__custom__' || (old('category') && !in_array(old('category'), ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']))) : ($rule->category && !in_array($rule->category?->value, ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']))) ? 'true' : 'false' }},
                                    editSelectedCategory: '{{ (old('rule_id') == $rule->id ? (in_array(old('category'), ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']) ? old('category') : '') : ($rule->category && in_array($rule->category?->value, ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']) ? $rule->category?->value : '')) }}',
                                    editCustomCategory: '{{ (old('rule_id') == $rule->id ? old('custom_category', old('category')) : ($rule->category && !in_array($rule->category?->value, ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']) ? $rule->category?->value : '')) }}'
                                }"
                                {{ !$rule->is_active ? 'class=opacity-50' : '' }}>
                                {{-- Nama & Deskripsi Lurus Sejajar Tanpa Menjorok --}}
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-semibold text-slate-800">{{ $rule->name }}</div>
                                    @if($rule->description)
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $rule->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($rule->category)
                                        <span class="badge-cat {{ $badgeClass }}">
                                            {{ $rule->category->label() }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold border {{ $rule->type->value === 'violation' ? 'border-red-200 bg-red-50 text-red-600' : 'border-green-200 bg-green-50 text-green-600' }}">
                                        {{ $rule->type->value === 'achievement' ? '+' : '' }}{{ $rule->points }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($isAdmin)
                                        <form method="POST" action="{{ route('admin.rules.toggle-active', $rule) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border transition-colors cursor-pointer {{ $rule->is_active ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                                {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $rule->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                            {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    @endif
                                </td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 hover:bg-amber-500/20 transition-colors border border-amber-500/20 cursor-pointer" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.rules.destroy', $rule) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                    onclick="Swal.fire({ title: 'Hapus peraturan?', html: 'Data <strong>{{ addslashes($rule->name) }}</strong> akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937', iconColor: '#ef4444' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-600 hover:bg-red-500/20 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Edit Modal --}}
                                        <template x-teleport="body">
                                            <div>
                                                {{-- Backdrop Gelap --}}
                                                <div x-show="showEditModal"
                                                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                     @click="showEditModal = false"
                                                     class="fixed inset-0"
                                                     style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 9998;"
                                                     x-cloak></div>

                                                {{-- Modal Box Container --}}
                                                <div x-show="showEditModal"
                                                     class="fixed inset-0 flex items-center justify-center p-4"
                                                     style="z-index: 9999;"
                                                     @click.self="showEditModal = false"
                                                     x-cloak>
                                                    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 text-left">
                                                        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100">
                                                            <h3 class="text-lg font-bold text-slate-800">Edit Peraturan</h3>
                                                            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                        </div>

                                                        <form method="POST" action="{{ route('admin.rules.update', $rule) }}" class="space-y-5">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="rule_id" value="{{ $rule->id }}">

                                                            <div>
                                                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Peraturan <span class="text-red-500">*</span></label>
                                                                <input type="text" name="name" value="{{ old('rule_id') == $rule->id ? old('name') : $rule->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                            </div>

                                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                                <div>
                                                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                                                                    <select name="type" x-model="editType"
                                                                            @change="if(editType === 'violation' && !['ringan','sedang','berat'].includes(editSelectedCategory)) { editSelectedCategory = 'ringan'; } if(editType === 'achievement' && !['akademik','non_akademik','kedisiplinan','organisasi','sosial'].includes(editSelectedCategory)) { editSelectedCategory = 'akademik'; }"
                                                                            required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                        <option value="violation">Pelanggaran</option>
                                                                        <option value="achievement">Prestasi</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <div class="flex items-center justify-between mb-1.5">
                                                                        <label class="block text-sm font-medium text-slate-700">Kategori</label>
                                                                        <button type="button"
                                                                                @click.prevent="editIsCustomCategory = !editIsCustomCategory"
                                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer">
                                                                            <span x-show="!editIsCustomCategory">+ Kategori Baru</span>
                                                                            <span x-show="editIsCustomCategory" style="display: none;">← Pilih List</span>
                                                                        </button>
                                                                    </div>

                                                                    {{-- Dropdown Kategori Standar (tanpa opsi kustom di dalam) --}}
                                                                    <div x-show="!editIsCustomCategory">
                                                                        <select x-model="editSelectedCategory"
                                                                                :name="!editIsCustomCategory ? 'category' : ''"
                                                                                class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="">-- Tanpa Kategori --</option>
                                                                            <template x-if="editType === 'violation'">
                                                                                <optgroup label="Kategori Pelanggaran">
                                                                                    <option value="ringan">Ringan</option>
                                                                                    <option value="sedang">Sedang</option>
                                                                                    <option value="berat">Berat</option>
                                                                                </optgroup>
                                                                            </template>
                                                                            <template x-if="editType === 'achievement'">
                                                                                <optgroup label="Kategori Prestasi">
                                                                                    <option value="akademik">Akademik</option>
                                                                                    <option value="non_akademik">Non-Akademik</option>
                                                                                    <option value="kedisiplinan">Kedisiplinan & Karakter</option>
                                                                                    <option value="organisasi">Organisasi & Kepemimpinan</option>
                                                                                    <option value="sosial">Sosial & Lingkungan</option>
                                                                                </optgroup>
                                                                            </template>
                                                                        </select>
                                                                    </div>

                                                                    {{-- Input Kategori Baru (di luar dropdown) --}}
                                                                    <div x-show="editIsCustomCategory" style="display: none;">
                                                                        <input type="text"
                                                                               x-model="editCustomCategory"
                                                                               :name="editIsCustomCategory ? 'custom_category' : ''"
                                                                               placeholder="Ketik nama kategori..."
                                                                               class="w-full px-3 py-2 rounded-lg bg-white border border-blue-400 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                                        <input type="hidden" name="category" value="__custom__" :disabled="!editIsCustomCategory">
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bobot Poin <span class="text-red-500">*</span></label>
                                                                    <input type="number" name="points" value="{{ old('rule_id') == $rule->id ? old('points') : $rule->points }}" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi / Keterangan</label>
                                                                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan tambahan peraturan...">{{ old('rule_id') == $rule->id ? old('description') : $rule->description }}</textarea>
                                                            </div>

                                                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                                                                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors cursor-pointer">Batal</button>
                                                                <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 5 : 4 }}" class="px-6 py-12 text-center text-sm text-slate-400 italic">
                                    Belum ada peraturan {{ $group['label'] }}.
                                    @if($isAdmin)
                                        <button @click="showModal = true" class="block mx-auto mt-2 text-blue-600 hover:underline font-medium not-italic">+ Tambah Peraturan {{ $group['label'] }}</button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Create Modal (Hanya Admin) --}}
@if($isAdmin)
<template x-teleport="body">
    <div>
        {{-- Backdrop Gelap --}}
        <div x-show="showModal"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showModal = false"
             class="fixed inset-0"
             style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 9998;"
             x-cloak></div>

        {{-- Modal Box Container --}}
        <div x-show="showModal"
             class="fixed inset-0 flex items-center justify-center p-4"
             style="z-index: 9999;"
             @click.self="showModal = false"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6"
                 x-data="{
                     ruleType: '{{ old('type', 'violation') }}',
                     isCustomCategory: {{ old('category') === '__custom__' || (old('category') && !in_array(old('category'), ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial'])) ? 'true' : 'false' }},
                     selectedCategory: '{{ (in_array(old('category'), ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial'])) ? old('category') : (old('type') === 'achievement' ? 'akademik' : 'ringan') }}',
                     customCategory: '{{ old('custom_category', (!in_array(old('category'), ['ringan','sedang','berat','akademik','non_akademik','kedisiplinan','organisasi','sosial']) ? old('category') : '')) }}'
                 }">

                <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800" id="modal-title">Tambah Peraturan Baru</h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.rules.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Peraturan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh: Terlambat masuk sekolah / Juara 1 OSN">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                            <select name="type" x-model="ruleType"
                                    @change="if(ruleType === 'violation' && !['ringan','sedang','berat'].includes(selectedCategory)) { selectedCategory = 'ringan'; } if(ruleType === 'achievement' && !['akademik','non_akademik','kedisiplinan','organisasi','sosial'].includes(selectedCategory)) { selectedCategory = 'akademik'; }"
                                    required class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                <option value="violation">Pelanggaran</option>
                                <option value="achievement">Prestasi</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-medium text-slate-700">Kategori</label>
                                <button type="button"
                                        @click.prevent="isCustomCategory = !isCustomCategory"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors cursor-pointer">
                                    <span x-show="!isCustomCategory">+ Kategori Baru</span>
                                    <span x-show="isCustomCategory" style="display: none;">← Pilih List</span>
                                </button>
                            </div>

                            {{-- Dropdown Kategori Standar (tanpa opsi kustom di dalam) --}}
                            <div x-show="!isCustomCategory">
                                <select x-model="selectedCategory"
                                        :name="!isCustomCategory ? 'category' : ''"
                                        class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">-- Tanpa Kategori --</option>
                                    <template x-if="ruleType === 'violation'">
                                        <optgroup label="Kategori Pelanggaran">
                                            <option value="ringan">Ringan</option>
                                            <option value="sedang">Sedang</option>
                                            <option value="berat">Berat</option>
                                        </optgroup>
                                    </template>
                                    <template x-if="ruleType === 'achievement'">
                                        <optgroup label="Kategori Prestasi">
                                            <option value="akademik">Akademik</option>
                                            <option value="non_akademik">Non-Akademik</option>
                                            <option value="kedisiplinan">Kedisiplinan & Karakter</option>
                                            <option value="organisasi">Organisasi & Kepemimpinan</option>
                                            <option value="sosial">Sosial & Lingkungan</option>
                                        </optgroup>
                                    </template>
                                </select>
                            </div>

                            {{-- Input Kategori Baru (di luar dropdown) --}}
                            <div x-show="isCustomCategory" style="display: none;">
                                <input type="text"
                                       x-model="customCategory"
                                       :name="isCustomCategory ? 'custom_category' : ''"
                                       placeholder="Ketik kategori baru..."
                                       class="w-full px-4 py-2.5 rounded-lg bg-white border border-blue-400 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="hidden" name="category" value="__custom__" :disabled="!isCustomCategory">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Bobot Poin <span class="text-red-500">*</span></label>
                            <input type="number" name="points" value="{{ old('points', 10) }}" required min="1" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="10">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi / Keterangan</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan detail atau tindak lanjut peraturan...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">Simpan Peraturan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
@endif
</div>
@endsection
