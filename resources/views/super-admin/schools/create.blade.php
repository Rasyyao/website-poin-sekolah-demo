@extends('layouts.app')
@section('title', 'Tambah Sekolah')
@section('page-title', 'Tambah Sekolah Baru')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('super-admin.schools.store') }}" class="bg-surface-light rounded-xl border border-gray-700/50 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Sekolah <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Status Langganan</label>
            <select name="subscription_status" class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="trial">Trial</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
            </select>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Simpan</button>
            <a href="{{ route('super-admin.schools.index') }}" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection
