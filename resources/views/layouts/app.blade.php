<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ website_name() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen antialiased">
    <div class="flex min-h-screen relative">
        {{-- Desktop Sidebar --}}
        <div class="hidden lg:block fixed inset-y-0 left-0 z-[10] w-64">
            @include('layouts.partials.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col w-full lg:ml-64 min-w-0">
            {{-- Top Bar --}}
            @php
                $role = auth()->user()?->role?->value;
                $searchRoute = in_array($role, ['super_admin', 'admin']) 
                    ? route('admin.students.index') 
                    : (in_array($role, ['teacher', 'homeroom', 'counselor']) ? route('teacher.my-students') : '#');
                    
                $navLinks = [];
                $iconClass = 'w-4 h-4';
                $svgStart = '<svg class="'.$iconClass.'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">';
                $svgEnd = '</svg>';

                if (in_array($role, ['super_admin', 'admin'])) {
                    $navLinks[] = ['name' => 'Home', 'url' => route('admin.reports.dashboard'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Siswa Melampaui Batas', 'url' => route('admin.reports.ranking'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Guru & Staf', 'url' => route('admin.staff.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Siswa', 'url' => route('admin.students.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Kelas', 'url' => route('admin.classes.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Tahun Ajaran', 'url' => route('admin.academic-years.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Peraturan', 'url' => route('admin.rules.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Ambang Batas', 'url' => route('admin.rule-thresholds.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Log Poin', 'url' => route('admin.points-log.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>' . $svgEnd];
                    $navLinks[] = ['name' => 'Banding', 'url' => route('admin.appeals.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>' . $svgEnd];
                }
                if (in_array($role, ['admin', 'teacher', 'homeroom', 'counselor'])) {
                    $navLinks[] = ['name' => 'Input Poin', 'url' => route('teacher.points.index'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>' . $svgEnd];
                }
                if (in_array($role, ['teacher', 'homeroom', 'counselor'])) {
                    $navLinks[] = ['name' => 'Siswa Saya', 'url' => route('teacher.my-students'), 'icon' => $svgStart . '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>' . $svgEnd];
                }
            @endphp
            <header x-data="{ 
                        isSearchOpen: false, 
                        isMobileMenuOpen: false,
                        searchQuery: '{{ request('search') }}', 
                        navLinks: {{ json_encode($navLinks) }},
                        get filteredNavLinks() {
                            if (this.searchQuery.trim().length === 0) return [];
                            return this.navLinks.filter(link => link.name.toLowerCase().includes(this.searchQuery.toLowerCase())).slice(0, 5);
                        }
                    }" 
                    :class="(isSearchOpen || isMobileMenuOpen) ? 'z-[100]' : 'z-20'"
                    class="h-16 border-b border-slate-200 bg-white flex items-center justify-between px-4 lg:px-6 sticky top-0 shadow-sm transition-all duration-200">
                
                <!-- Overlay backdrop covering entire page for search -->
                <div x-show="isSearchOpen && (filteredNavLinks.length > 0 || searchQuery.trim().length > 0)" 
                     x-transition.opacity 
                     class="fixed inset-0 bg-black/50 z-[90]" 
                     style="display: none;"></div>

                <div class="flex items-center gap-4">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="lg:hidden p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer relative z-[95]">
                        <svg x-show="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="isMobileMenuOpen" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    
                    <a href="/" class="flex items-center gap-2 lg:hidden relative z-[95]">
                        <div class="w-7 h-7 rounded bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight truncate">{{ website_name() }}</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-2 lg:gap-4 flex-1 ml-4 lg:ml-0">
                    @if($searchRoute !== '#')
                    <div @keydown.window.prevent.cmd.k="$refs.searchInput.focus(); isSearchOpen = true" 
                         @keydown.window.prevent.ctrl.k="$refs.searchInput.focus(); isSearchOpen = true"
                         @click.away="isSearchOpen = false"
                         class="hidden md:block relative z-[100] w-full max-w-xl">
                         
                        <form action="{{ $searchRoute }}" method="GET" class="relative items-center bg-slate-100 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 transition-shadow flex w-full">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-ref="searchInput" name="search" x-model="searchQuery" @focus="isSearchOpen = true" @input="isSearchOpen = true" autocomplete="off" placeholder="Search anything..." class="pl-9 pr-14 py-2 bg-transparent border-none outline-none focus:outline-none rounded-lg text-sm text-slate-900 focus:ring-0 w-full placeholder-slate-400">
                            <button type="submit" class="absolute right-2 flex items-center justify-center bg-white rounded border border-slate-200 px-1.5 py-0.5 hover:bg-slate-50 cursor-pointer" title="Press ⌘K to focus">
                                <span class="text-[10px] text-slate-400 font-medium font-sans">⌘K</span>
                            </button>
                        </form>

                        <!-- Dropdown recommendations -->
                        <div x-show="isSearchOpen && (filteredNavLinks.length > 0 || searchQuery.trim().length > 0)" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden"
                             style="display: none;">
                            
                            <div x-show="filteredNavLinks.length > 0" class="p-2">
                                <div class="px-3 py-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Halaman</div>
                                <template x-for="link in filteredNavLinks" :key="link.url">
                                    <a :href="link.url" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors font-medium">
                                        <div class="text-slate-400" x-html="link.icon"></div>
                                        <span x-text="link.name"></span>
                                    </a>
                                </template>
                            </div>

                            <div x-show="searchQuery.trim().length > 0" class="border-t border-slate-100 p-2">
                                <button type="button" @click="$el.closest('.relative').querySelector('form').submit()" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <span>Cari "<span x-text="searchQuery" class="font-bold text-slate-900"></span>" di data siswa</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-2 lg:gap-4">
                    <div class="hidden lg:flex items-center gap-3">
                        <div x-data="{ 
                                theme: localStorage.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
                                toggleTheme() {
                                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                                    localStorage.theme = this.theme;
                                    if (this.theme === 'dark') {
                                        document.documentElement.classList.add('dark');
                                    } else {
                                        document.documentElement.classList.remove('dark');
                                    }
                                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: this.theme }));
                                }
                            }">
                            <button @click="toggleTheme()" type="button" class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors cursor-pointer" title="Toggle Theme">
                                <svg x-show="theme === 'dark'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <svg x-show="theme === 'light'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            </button>
                        </div>
                        <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </button>
                    </div>

                    {{-- User Profile --}}
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                    </div>

                    {{-- Desktop Logout Button --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0 hidden lg:block ml-2">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Mobile Navigation Dropdown --}}
                <div x-show="isMobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     @click.away="isMobileMenuOpen = false"
                     class="absolute top-full left-0 right-0 bg-white shadow-xl border-b border-slate-200 max-h-[calc(100vh-4rem)] overflow-y-auto lg:hidden"
                     style="display: none;">
                    <div class="w-full flex flex-col" @click.stop>
                        @include('layouts.partials.sidebar')
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-6 overflow-x-hidden">
                @hasSection('page-header')
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900">@yield('page-header')</h2>
                            @hasSection('page-desc')
                                <p class="text-sm text-slate-500 mt-1">@yield('page-desc')</p>
                            @endif
                        </div>
                        @hasSection('page-actions')
                            <div class="flex flex-wrap items-center gap-3 shrink-0">
                                @yield('page-actions')
                            </div>
                        @endif
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        // SweetAlert2 toast config
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'rounded-xl shadow-xl border border-slate-200 font-medium text-sm text-slate-800'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success')),
                iconColor: '#16a34a',
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error')),
                iconColor: '#ef4444',
            });
        @endif

        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: @json($errors->first()),
                iconColor: '#ef4444',
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
