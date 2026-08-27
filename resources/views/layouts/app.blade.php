<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Poin Sekolah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-gray-100 min-h-screen antialiased">
    <div class="flex min-h-screen relative">
        {{-- Sidebar Backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-[5] hidden lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <div id="sidebar" class="fixed inset-y-0 left-0 z-[10] w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            @include('layouts.partials.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col w-full lg:ml-64 min-w-0">
            {{-- Top Bar --}}
            <header class="h-16 border-b border-gray-700/50 bg-surface/80 backdrop-blur-xl flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-4">
                    <button class="lg:hidden p-1.5 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-700/50 transition-colors cursor-pointer" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-base lg:text-xl font-bold text-gray-100 tracking-tight truncate max-w-[150px] sm:max-w-md">@yield('page-title', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center gap-3 lg:gap-5">
                    {{-- User Profile --}}
                    <div class="flex items-center gap-3 group">
                        <div class="hidden sm:block text-right">
                            <span class="block text-sm font-semibold text-gray-100 leading-tight group-hover:text-primary-300 transition-colors">{{ auth()->user()->name ?? 'Guest' }}</span>
                            <span class="block text-xs font-medium text-primary-400 mt-0.5">{{ auth()->user()->role?->label() ?? '' }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500/20 to-primary-600/30 flex items-center justify-center text-primary-400 font-bold border border-primary-500/30 shadow-inner">
                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="hidden sm:block w-px h-8 bg-gray-700/50"></div>

                    {{-- Logout Button --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" title="Logout" class="inline-flex items-center justify-center gap-2 text-sm font-medium px-3.5 py-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 hover:border-red-500 transition-all duration-200 cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-6 overflow-x-hidden">
                @hasSection('page-header')
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-100">@yield('page-header')</h2>
                            @hasSection('page-desc')
                                <p class="text-sm text-gray-400 mt-1">@yield('page-desc')</p>
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
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // SweetAlert2 dark toast config
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#1e293b',
            color: '#f1f5f9',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success')),
                iconColor: '#22c55e',
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error')),
                iconColor: '#ef4444',
            });
        @endif

        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: @json(session('warning')),
                iconColor: '#f59e0b',
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul style="text-align:left;padding-left:1.2rem;margin:0">' +
                    @foreach($errors->all() as $error)
                        '<li>{{ addslashes($error) }}</li>' +
                    @endforeach
                    '</ul>',
                background: '#ffffff',
                color: '#1f2937',
                confirmButtonColor: '#2563eb',
                iconColor: '#ef4444',
            });
        @endif
    </script>
    @stack('scripts')

    {{-- School Settings Modal --}}
    @if(auth()->user()?->school && in_array(auth()->user()->role?->value, ['super_admin', 'admin']))
    <div x-data="{ showSchoolSettings: false }" @open-school-settings.window="showSchoolSettings = true">
        <div x-show="showSchoolSettings" style="display: none;"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showSchoolSettings = false"
             class="fixed inset-0 bg-black/75 z-[40]"></div>

        <div x-show="showSchoolSettings" style="display: none;"
             class="fixed inset-0 flex items-center justify-center p-4 z-[50]">
            <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-lg p-6" @click.stop>
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-medium text-gray-100">Pengaturan Sekolah</h3>
                    <button @click="showSchoolSettings = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.school.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Sekolah</label>
                        <input type="text" name="name" value="{{ auth()->user()->school->name }}" required class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-700/50 mt-6">
                        <button type="button" @click="showSchoolSettings = false" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</body>
</html>
