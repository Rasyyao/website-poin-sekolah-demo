@extends('layouts.guest')
@section('title', 'Login Orang Tua')

@section('content')
<div class="bg-surface-light rounded-2xl border border-gray-700/50 p-8 shadow-2xl shadow-black/20">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg shadow-green-500/25">👨‍👩‍👧</div>
        <h2 class="text-2xl font-bold text-gray-100">Akses Orang Tua</h2>
        <p class="text-sm text-gray-400 mt-1">Pantau perkembangan perilaku anak Anda</p>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'Login Gagal',
                    html: @json(implode('<br>', array_map('e', $errors->all()))),
                    icon: 'error',
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    @endif

    {{-- Demo Login Section --}}
    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
        <h3 class="text-sm font-semibold text-green-400 mb-3 text-center">Coba Versi Demo (Satu Klik)</h3>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('demo.login', 'student') }}" class="px-3 py-2 text-xs font-medium text-center text-gray-300 bg-surface rounded-lg hover:bg-green-600 hover:text-white transition-colors border border-gray-600">Siswa</a>
            <a href="{{ route('demo.login', 'parent') }}" class="px-3 py-2 text-xs font-medium text-center text-gray-300 bg-surface rounded-lg hover:bg-green-600 hover:text-white transition-colors border border-gray-600">Orang Tua</a>
            <a href="{{ route('demo.login', 'admin') }}" class="px-3 py-2 text-xs font-medium text-center text-gray-300 bg-surface rounded-lg hover:bg-green-600 hover:text-white transition-colors border border-gray-600">Admin</a>
            <a href="{{ route('demo.login', 'teacher') }}" class="px-3 py-2 text-xs font-medium text-center text-gray-300 bg-surface rounded-lg hover:bg-green-600 hover:text-white transition-colors border border-gray-600">Guru</a>
        </div>
    </div>
    <div class="flex items-center gap-4 mb-6">
        <div class="h-px bg-gray-700/50 flex-1"></div>
        <span class="text-xs text-gray-500 font-medium">Atau login manual</span>
        <div class="h-px bg-gray-700/50 flex-1"></div>
    </div>

    <form method="POST" action="{{ route('parent.login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="school_slug" class="block text-sm font-medium text-gray-300 mb-1.5">Kode Sekolah</label>
            <input type="text" id="school_slug" name="school_slug" value="{{ old('school_slug') }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                   placeholder="contoh: smpn1-demo">
        </div>

        <div>
            <label for="nisn" class="block text-sm font-medium text-gray-300 mb-1.5">NISN Anak</label>
            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                   placeholder="Nomor Induk Siswa Nasional">
        </div>

        <div x-data="{ show: false }">
            <label for="access_code" class="block text-sm font-medium text-gray-300 mb-1.5">Kode Akses</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" id="access_code" name="access_code" required
                       class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all pr-10"
                       placeholder="Kode akses dari sekolah">
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

        <button type="submit"
                class="w-full py-2.5 px-4 rounded-lg bg-gradient-to-r from-green-600 to-emerald-700 text-white font-medium hover:from-green-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-surface transition-all shadow-lg shadow-green-500/20 cursor-pointer">
            Masuk
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-gray-700/50 text-center">
        <a href="{{ route('login') }}" class="text-sm text-primary-400 hover:text-primary-300 transition-colors">
            ← Login Staff/Guru
        </a>
    </div>
</div>
@endsection
