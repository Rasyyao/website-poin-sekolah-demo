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
                $hasSearch = in_array($role, ['super_admin', 'admin', 'kesiswaan', 'teacher', 'homeroom', 'counselor']);
                    
                $navLinks = [];
                $iconClass = 'w-4 h-4';
                $svgStart = '<svg class="'.$iconClass.'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">';
                $svgEnd = '</svg>';

                // Icons reused across entries
                $icoHome    = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'.$svgEnd;
                $icoRanking = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>'.$svgEnd;
                $icoStaff   = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>'.$svgEnd;
                $icoStudent = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'.$svgEnd;
                $icoClass   = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>'.$svgEnd;
                $icoCalendar= $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>'.$svgEnd;
                $icoRules   = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'.$svgEnd;
                $icoWarning = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>'.$svgEnd;
                $icoLog     = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>'.$svgEnd;
                $icoAppeal  = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>'.$svgEnd;
                $icoCert    = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>'.$svgEnd;
                $icoMigrate = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>'.$svgEnd;
                $icoSettings= $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'.$svgEnd;
                $icoPlus    = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>'.$svgEnd;
                $icoEye     = $svgStart.'<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'.$svgEnd;

                if (in_array($role, ['super_admin', 'admin', 'kesiswaan'])) {
                    $navLinks[] = ['name' => 'Dashboard', 'category' => 'Menu Utama', 'keywords' => 'home beranda dashboard statistik ringkasan', 'url' => route('admin.reports.dashboard'), 'icon' => $icoHome];
                    $navLinks[] = ['name' => 'Siswa Melampaui Batas', 'category' => 'Laporan', 'keywords' => 'ranking peringkat poin paling banyak pelanggaran batas', 'url' => route('admin.reports.ranking'), 'icon' => $icoRanking];
                    $navLinks[] = ['name' => 'Daftar Siswa', 'category' => 'Siswa', 'keywords' => 'siswa murid tambah cari kelola list data', 'url' => route('admin.students.index'), 'icon' => $icoStudent];
                    $navLinks[] = ['name' => 'Pindah Kelas', 'category' => 'Siswa', 'keywords' => 'pindah kelas mutasi migrasi naik tingkat', 'url' => route('admin.students.migration'), 'icon' => $icoMigrate];
                    $navLinks[] = ['name' => 'Daftar Kelas', 'category' => 'Kelas', 'keywords' => 'kelas rombel kelompok belajar tambah kelas baru', 'url' => route('admin.classes.index'), 'icon' => $icoClass];
                    $navLinks[] = ['name' => 'Log Poin', 'category' => 'Operasional', 'keywords' => 'log riwayat histori poin catatan masuk keluar', 'url' => route('admin.points-log.index'), 'icon' => $icoLog];
                    $navLinks[] = ['name' => 'Banding', 'category' => 'Operasional', 'keywords' => 'banding keberatan komplain keluhan sanggah', 'url' => route('admin.appeals.index'), 'icon' => $icoAppeal];
                    $navLinks[] = ['name' => 'Sertifikat', 'category' => 'Operasional', 'keywords' => 'sertifikat penghargaan prestasi cetak print', 'url' => route('admin.certificates.index'), 'icon' => $icoCert];
                    $navLinks[] = ['name' => 'Peraturan', 'category' => 'Peraturan', 'keywords' => 'peraturan aturan tata tertib tambah ubah hapus', 'url' => route('admin.rules.index'), 'icon' => $icoRules];
                    if (in_array($role, ['super_admin', 'admin'])) {
                        $navLinks[] = ['name' => 'Guru & Staf', 'category' => 'Pengguna', 'keywords' => 'guru staf pegawai tambah hapus kelola akun pengguna', 'url' => route('admin.staff.index'), 'icon' => $icoStaff];
                        $navLinks[] = ['name' => 'Ambang Batas Poin', 'category' => 'Peraturan', 'keywords' => 'ambang batas threshold limit maksimum poin sanksi', 'url' => route('admin.rule-thresholds.index'), 'icon' => $icoWarning];
                        $navLinks[] = ['name' => 'Konfigurasi Sistem', 'category' => 'Pengaturan', 'keywords' => 'pengaturan setting konfigurasi sistem nama sekolah tahun pelajaran semester tanda tangan sertifikat', 'url' => route('admin.academic-years.index'), 'icon' => $icoSettings];
                    }
                }
                if (in_array($role, ['admin', 'kesiswaan', 'teacher', 'homeroom', 'counselor'])) {
                    $navLinks[] = ['name' => 'Input Poin', 'category' => 'Poin', 'keywords' => 'input tambah poin catat pelanggaran prestasi baru', 'url' => route('teacher.points.index'), 'icon' => $icoPlus];
                }
                if (in_array($role, ['teacher', 'homeroom', 'counselor'])) {
                    $navLinks[] = ['name' => 'Siswa Saya', 'category' => 'Siswa', 'keywords' => 'siswa saya wali kelas pantau monitor murid', 'url' => route('teacher.my-students'), 'icon' => $icoEye];
                    $navLinks[] = ['name' => 'Banding', 'category' => 'Operasional', 'keywords' => 'banding keberatan komplain sanggah', 'url' => route('admin.appeals.index'), 'icon' => $icoAppeal];
                }
            @endphp
            <header x-data="{ 
                        isSearchOpen: false, 
                        isMobileMenuOpen: false,
                        searchQuery: '',
                        activeIndex: -1,
                        navLinks: {{ json_encode($navLinks) }},
                        get filteredNavLinks() {
                            const q = this.searchQuery.trim().toLowerCase();
                            if (q.length === 0) return this.navLinks.slice(0, 6);
                            return this.navLinks.filter(link => {
                                const haystack = (link.name + ' ' + (link.keywords || '') + ' ' + (link.category || '')).toLowerCase();
                                return haystack.includes(q);
                            }).slice(0, 8);
                        },
                        get groupedLinks() {
                            const groups = {};
                            this.filteredNavLinks.forEach(link => {
                                const cat = link.category || 'Lainnya';
                                if (!groups[cat]) groups[cat] = [];
                                groups[cat].push(link);
                            });
                            return Object.entries(groups);
                        },
                        openSearch() {
                            this.isSearchOpen = true;
                            this.activeIndex = -1;
                        },
                        closeSearch() {
                            this.isSearchOpen = false;
                            this.activeIndex = -1;
                        },
                        handleKeydown(e) {
                            const links = this.filteredNavLinks;
                            if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                this.activeIndex = Math.min(this.activeIndex + 1, links.length - 1);
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                this.activeIndex = Math.max(this.activeIndex - 1, -1);
                            } else if (e.key === 'Enter' && this.activeIndex >= 0) {
                                e.preventDefault();
                                window.location.href = links[this.activeIndex].url;
                            } else if (e.key === 'Escape') {
                                this.closeSearch();
                            }
                        }
                    }" 
                    :class="(isSearchOpen || isMobileMenuOpen) ? 'z-[100]' : 'z-20'"
                    class="h-16 border-b border-slate-200 bg-white flex items-center justify-between px-4 lg:px-6 sticky top-0 shadow-sm transition-all duration-200">
                
                <!-- Overlay backdrop covering entire page for search -->
                <div x-show="isSearchOpen" 
                     x-transition.opacity 
                     class="fixed inset-0 bg-black/20 z-[90]" 
                     style="display: none;" @click="closeSearch()"></div>

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
                    @if($hasSearch)
                    <div @keydown.window.prevent.ctrl.enter="$refs.searchInput.focus(); openSearch()"
                         @click.away="closeSearch()"
                         @keydown="handleKeydown($event)"
                         class="hidden md:block relative z-[100] w-full max-w-xl">

                        <div class="relative items-center bg-slate-100 rounded-lg focus-within:ring-2 focus-within:ring-blue-500/30 transition-all flex w-full">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text"
                                   x-ref="searchInput"
                                   x-model="searchQuery"
                                   @focus="openSearch()"
                                   @input="openSearch(); activeIndex = -1"
                                   autocomplete="off"
                                   placeholder="Cari menu atau halaman..."
                                   class="pl-9 pr-24 py-2 bg-transparent border-none outline-none focus:outline-none rounded-lg text-sm text-slate-900 focus:ring-0 w-full placeholder-slate-400">
                            <div class="absolute right-2 flex items-center gap-1 pointer-events-none">
                                <kbd class="inline-flex items-center px-1.5 py-0.5 rounded border border-slate-200 bg-white text-[10px] text-slate-400 font-medium font-sans leading-none">Ctrl</kbd>
                                <kbd class="inline-flex items-center px-1.5 py-0.5 rounded border border-slate-200 bg-white text-[10px] text-slate-400 font-medium font-sans leading-none">↵</kbd>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div x-show="isSearchOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden max-h-[70vh] overflow-y-auto"
                             style="display: none;">

                            <!-- Empty state hint -->
                            <template x-if="searchQuery.trim().length === 0">
                                <div class="px-4 pt-3 pb-1">
                                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Akses Cepat</p>
                                </div>
                            </template>

                            <!-- No results -->
                            <template x-if="filteredNavLinks.length === 0 && searchQuery.trim().length > 0">
                                <div class="px-4 py-6 text-center">
                                    <svg class="w-8 h-8 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <p class="text-sm text-slate-400">Tidak ada halaman ditemukan</p>
                                    <p class="text-xs text-slate-300 mt-0.5" x-text="'untuk pencarian &quot;' + searchQuery + '&quot;'"></p>
                                </div>
                            </template>

                            <!-- Grouped results -->
                            <template x-if="filteredNavLinks.length > 0">
                                <div class="py-2">
                                    <template x-for="[category, links] in groupedLinks" :key="category">
                                        <div class="mb-1">
                                            <div class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="category"></div>
                                            <template x-for="(link, idx) in links" :key="link.url">
                                                <a :href="link.url"
                                                   class="flex items-center gap-3 mx-2 px-3 py-2 rounded-lg text-sm transition-colors"
                                                   :class="filteredNavLinks.indexOf(link) === activeIndex
                                                       ? 'bg-blue-50 text-blue-700 font-semibold'
                                                       : 'text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-medium'">
                                                    <div class="text-slate-400 shrink-0" x-html="link.icon"></div>
                                                    <span x-text="link.name" class="flex-1"></span>
                                                    <template x-if="filteredNavLinks.indexOf(link) === activeIndex">
                                                        <kbd class="text-[9px] px-1 py-0.5 bg-blue-100 text-blue-500 rounded font-mono">↵</kbd>
                                                    </template>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Footer hint -->
                            <div class="border-t border-slate-100 px-4 py-2 flex items-center gap-3 text-[10px] text-slate-400">
                                <span class="flex items-center gap-1"><kbd class="px-1 py-0.5 bg-slate-100 rounded text-[9px] font-mono">↑↓</kbd> navigasi</span>
                                <span class="flex items-center gap-1"><kbd class="px-1 py-0.5 bg-slate-100 rounded text-[9px] font-mono">↵</kbd> buka</span>
                                <span class="flex items-center gap-1"><kbd class="px-1 py-0.5 bg-slate-100 rounded text-[9px] font-mono">Esc</kbd> tutup</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-2 lg:gap-4">
                    {{-- Theme Toggle (visible on all screen sizes) --}}
                    <div class="flex items-center">
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
                    </div>

                    {{-- User Name + Avatar --}}
                    <div class="flex items-center gap-2">
                        <div class="hidden lg:block text-right">
                            <p class="text-sm font-semibold text-slate-800 truncate max-w-[140px] leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate capitalize leading-tight">{{ str_replace('_', ' ', auth()->user()->role?->value ?? '-') }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold border border-blue-500/30 shadow-sm text-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                        </div>
                    </div>
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
