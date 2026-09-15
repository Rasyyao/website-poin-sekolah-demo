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

    {{-- Info box --}}
    <div class="mb-5 p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-start gap-3">
        <svg class="w-4 h-4 text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-blue-300 leading-relaxed">
            Masukkan <strong>NISN anak</strong> dan <strong>tanggal lahir anak</strong> untuk mengakses laporan perilaku.
        </p>
    </div>

    <form method="POST" action="{{ route('parent.login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="nisn" class="block text-sm font-medium text-gray-300 mb-1.5">NISN Anak</label>
            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                   placeholder="Nomor Induk Siswa Nasional"
                   inputmode="numeric">
        </div>

        <div>
            <label for="birth_date" class="block text-sm font-medium text-gray-300 mb-1.5">Tanggal Lahir Anak</label>
            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                   max="{{ now()->format('Y-m-d') }}">
            <p class="text-[11px] text-gray-500 mt-1">Format: hari/bulan/tahun (sesuaikan dengan data sekolah)</p>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 rounded-lg bg-gradient-to-r from-green-600 to-emerald-700 text-white font-medium hover:from-green-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-surface transition-all shadow-lg shadow-green-500/20 cursor-pointer flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
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
