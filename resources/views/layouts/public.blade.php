<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Poin Sekolah</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-gray-100 min-h-screen antialiased">
    <header class="bg-surface/80 backdrop-blur-xl border-b border-gray-700/50 sticky top-0 z-20 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route(session()->has('parent_student_id') ? 'parent.dashboard' : 'student.dashboard') }}" class="flex items-center gap-2 sm:gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-primary-500/25 group-hover:scale-105 transition-transform">P</div>
                <span class="font-bold text-gray-100 text-lg tracking-tight hidden sm:inline group-hover:text-primary-500 transition-colors">Poin Sekolah</span>
            </a>
            <div class="flex items-center gap-4 sm:gap-6 overflow-x-auto whitespace-nowrap hide-scrollbar">
                <div class="flex items-center gap-4 font-medium text-sm text-gray-300">
                    @yield('nav-links')
                </div>
                <div class="w-px h-6 bg-gray-700/50 hidden sm:block"></div>
                <form method="POST" action="{{ route('parent.logout') }}" class="m-0 shrink-0">
                    @csrf
                    <button type="submit" title="Logout" class="inline-flex items-center justify-center gap-2 text-sm font-medium px-3.5 py-1.5 sm:py-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/20 hover:border-red-500 transition-all duration-200 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>
    <main class="max-w-4xl mx-auto px-6 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-success-500/10 border border-success-500/20 text-success-500 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-danger-500/10 border border-danger-500/20 text-danger-500 text-sm">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
