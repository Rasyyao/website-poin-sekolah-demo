@extends('layouts.app')
@section('title', 'Daftar Guru & Staf')
@section('page-title', 'Manajemen Pengguna')
@section('page-header', 'Daftar Guru & Staf')
@section('page-desc', 'Kelola akun guru, wali kelas, guru BK, dan admin lainnya.')
@section('page-actions')
    <form method="GET" class="flex items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
               class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 w-48 sm:w-64">
        <select name="role" onchange="this.form.submit()" class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 hidden sm:block">
            <option value="">Semua Peran</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer hidden sm:block">Cari</button>
    </form>
    <div class="flex items-center gap-2 border-l border-gray-700/50 pl-3 ml-1">
        <a href="{{ route('admin.exports.staff', 'pdf') }}" target="_blank" class="px-3 py-2 rounded-lg bg-red-500/10 text-red-500 text-sm font-medium hover:bg-red-500 hover:text-white transition-all border border-red-500/20 cursor-pointer flex items-center justify-center" title="Export PDF">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </a>
        <a href="{{ route('admin.exports.staff', 'excel') }}" class="px-3 py-2 rounded-lg bg-green-500/10 text-green-500 text-sm font-medium hover:bg-green-500 hover:text-white transition-all border border-green-500/20 cursor-pointer flex items-center justify-center" title="Export Excel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </a>
    </div>
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">+ Tambah Staf</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-lighter/50">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Staf</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Peran</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @forelse($staffs as $staff)
                    <tr class="hover:bg-surface-lighter/30 transition-colors group" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('staff_id') == $staff->id && $errors->any()) ? 'true' : 'false' }} }">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-200 group-hover:text-primary-300 transition-colors">{{ $staff->name }}</div>
                            @if($staff->id === auth()->id())
                                <span class="text-[10px] uppercase font-bold text-primary-600 dark:text-primary-400 bg-primary-500/20 px-2.5 py-0.5 rounded-full mt-1 inline-block border border-primary-500/30 shadow-sm">Anda</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $staff->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-700/50 text-gray-300 border border-gray-600/50">
                                {{ $staff->role->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $staff->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20 cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            @if($staff->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" 
                                    onclick="Swal.fire({ title: 'Hapus staf?', html: 'Akun <strong>{{ addslashes($staff->name) }}</strong> akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#9ca3af', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', background: '#ffffff', color: '#1f2937', iconColor: '#ef4444' }).then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-colors border border-red-500/20 cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
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
                                    <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 text-left" @click.stop>
                                        <div class="flex justify-between items-center mb-5">
                                            <h3 class="text-lg font-medium text-gray-100">Edit Akun Staf</h3>
                                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-4">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                                                <input type="text" name="name" value="{{ old('staff_id') == $staff->id ? old('name') : $staff->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Akses <span class="text-red-400">*</span></label>
                                                <input type="email" name="email" value="{{ old('staff_id') == $staff->id ? old('email') : $staff->email }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            </div>
                                            
                                            <div class="bg-surface p-4 rounded-lg border border-gray-700/50">
                                                <div x-data="{ show: false }">
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Password Baru (Opsional)</label>
                                                    <div class="relative">
                                                        <input :type="show ? 'text' : 'password'" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10">
                                                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer">
                                                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.52-3.415M15 12a3 3 0 01-3 3m-3-3a3 3 0 013-3m0 0l-3 3m3-3l3 3M3 3l18 18" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div x-data="{ show: false }" class="mt-4">
                                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                                                    <div class="relative">
                                                        <input :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10">
                                                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer">
                                                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.52-3.415M15 12a3 3 0 01-3 3m-3-3a3 3 0 013-3m0 0l-3 3m3-3l3 3M3 3l18 18" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2">Minimal 8 karakter. Biarkan kosong jika tidak ada perubahan.</p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Peran (Role) <span class="text-red-400">*</span></label>
                                                <select name="role" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->value }}" {{ (old('staff_id') == $staff->id ? old('role') : $staff->role->value) == $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                                                    @endforeach
                                                </select>
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
                                <p class="text-base font-medium">Tidak ada staf yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($staffs->hasPages())
    <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
        {{ $staffs->withQueryString()->links() }}
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
             class="fixed inset-0 bg-black/75 z-[40]"></div>

        {{-- Modal Box --}}
        <div x-show="showModal"
             class="fixed inset-0 flex items-center justify-center p-4 z-[50]">
            <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6" @click.stop>
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-medium text-gray-100">Tambah Akun Staf</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Akses <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password Sementara <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.52-3.415M15 12a3 3 0 01-3 3m-3-3a3 3 0 013-3m0 0l-3 3m3-3l3 3M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Peran (Role) <span class="text-red-400">*</span></label>
                        <select name="role" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                            @endforeach
                        </select>
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
