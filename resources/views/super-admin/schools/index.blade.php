@extends('layouts.app')
@section('title', 'Kelola Sekolah')
@section('page-title', 'Manajemen Sekolah')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex gap-3">
        <div x-data="{ showModal: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }" @open-modal.window="showModal = true">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sekolah..." class="px-4 py-2 rounded-lg bg-surface-light border border-gray-700/50 text-gray-200 text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Cari</button>
    </form>
    <a href="{{ route('super-admin.schools.create') }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors">+ Tambah Sekolah</a>
</div>

<div class="bg-surface-light rounded-2xl border border-gray-700/50 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-surface-lighter/50">
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Slug</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-700/30">
            @forelse($schools as $school)
                <tr class="hover:bg-surface-lighter/30 transition-colors group" x-data="{ showEditModal: {{ (old('_method') == 'PUT' && old('school_id') == $school->id && $errors->any()) ? 'true' : 'false' }} }">
                    <td class="px-6 py-4 text-sm text-gray-200 font-medium group-hover:text-primary-300 transition-colors">{{ $school->name }}</td>
                    <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ $school->slug }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $school->subscription_status->value === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ($school->subscription_status->value === 'trial' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') }}">{{ $school->subscription_status->label() }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('super-admin.schools.show', $school) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors border border-blue-500/20" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button type="button" @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors border border-amber-500/20" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('super-admin.schools.destroy', $school) }}" class="inline" onsubmit="return confirm('Hapus sekolah ini beserta seluruh datanya? TINDAKAN INI TIDAK DAPAT DIBATALKAN.')">
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
                                    <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-5">
                                            <h3 class="text-lg font-medium leading-6 text-gray-100">Edit Sekolah</h3>
                                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('super-admin.schools.update', $school) }}" class="space-y-5 text-left">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="school_id" value="{{ $school->id }}">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Sekolah</label>
                                                <input type="text" name="name" value="{{ old('school_id') == $school->id ? old('name') : $school->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Slug</label>
                                                <p class="text-sm text-gray-400 font-mono">{{ $school->slug }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Status Langganan</label>
                                                <select name="subscription_status" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                    <option value="trial" {{ (old('school_id') == $school->id ? old('subscription_status') : $school->subscription_status->value) === 'trial' ? 'selected' : '' }}>Trial</option>
                                                    <option value="active" {{ (old('school_id') == $school->id ? old('subscription_status') : $school->subscription_status->value) === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="expired" {{ (old('school_id') == $school->id ? old('subscription_status') : $school->subscription_status->value) === 'expired' ? 'selected' : '' }}>Expired</option>
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
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <p class="text-base font-medium">Belum ada sekolah.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($schools->hasPages())
    <div class="px-6 py-4 border-t border-gray-700/50 bg-surface-lighter/20">
        {{ $schools->links() }}
    </div>
    @endif
</div>
@endsection
