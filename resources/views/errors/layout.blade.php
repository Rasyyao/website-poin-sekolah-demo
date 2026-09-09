<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ website_name() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-6 antialiased selection:bg-blue-500 selection:text-white">
    @php
        $homeUrl = route('login');
        if (auth()->check()) {
            $userRole = auth()->user()->role?->value;
            $homeUrl = match($userRole) {
                'super_admin' => route('super-admin.schools.index'),
                'admin', 'kesiswaan' => route('admin.reports.dashboard'),
                default => route('teacher.points.index'),
            };
        } elseif (session()->has('parent_student_id')) {
            $homeUrl = route('parent.dashboard');
        }
    @endphp

    <main class="w-full max-w-md">
        <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-10 shadow-sm text-center">
            {{-- Error Code --}}
            <div class="text-6xl sm:text-7xl font-bold tracking-tight text-blue-600 mb-3 font-mono">
                @yield('code')
            </div>

            {{-- Title & Message --}}
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">
                @yield('headline')
            </h1>
            <p class="text-sm text-slate-500 leading-relaxed mb-8">
                @yield('description')
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ $homeUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium text-sm hover:bg-blue-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>

                <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ $homeUrl }}'" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium text-sm transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>

            @yield('extra')
        </div>
    </main>
</body>
</html>
