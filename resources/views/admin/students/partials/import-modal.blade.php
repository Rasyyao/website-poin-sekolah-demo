{{-- Import Siswa dari Excel/CSV --}}
<template x-teleport="body">
<div x-data="studentImportModal()" @open-import-modal.window="open()">
    {{-- Backdrop --}}
    <div x-show="show" x-cloak
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/75" style="z-index:40;"></div>

    {{-- Modal --}}
    <div x-show="show" x-cloak class="fixed inset-0 flex items-center justify-center p-4" style="z-index:50;">
        <div class="bg-slate-100 rounded-xl shadow-2xl border border-slate-200 w-full max-w-5xl max-h-[90vh] overflow-y-auto p-6" @click.stop>

            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-semibold text-slate-800">Import Siswa dari Excel/CSV</h3>
                <button @click="close()" class="text-slate-500 hover:text-slate-900 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Step: Upload --}}
            <div x-show="step === 'upload'">
                <p class="text-sm text-slate-500 mb-4">Unggah file Excel (.xlsx, .xls) atau CSV sesuai format template. Data akan ditampilkan untuk diperiksa sebelum benar-benar disimpan.</p>

                <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-slate-300 rounded-xl p-10 cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition-colors"
                       @dragover.prevent @drop.prevent="onDrop($event)">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span class="text-sm text-slate-600" x-text="fileName || 'Klik untuk pilih file atau drag & drop ke sini'"></span>
                    <span class="text-xs text-slate-400">Format: .xlsx, .xls, .csv (maks 10MB)</span>
                    <input type="file" class="hidden" accept=".xlsx,.xls,.csv" @change="onFileChange($event)">
                </label>

                <p x-show="error" x-text="error" class="mt-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"></p>

                <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-200">
                    <a href="{{ route('admin.students.import.template') }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Template
                    </a>
                    <button type="button" @click="parseFile()" :disabled="!file || loading"
                            class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="loading ? 'Memproses...' : 'Tampilkan Preview'"></span>
                    </button>
                </div>
            </div>

            {{-- Step: Preview --}}
            <div x-show="step === 'preview'">

                {{-- Summary --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
                        <div class="text-2xl font-black text-slate-800" x-text="rows.length"></div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-1">Total Baris</div>
                    </div>
                    <div class="bg-white rounded-xl border border-green-200 p-4 text-center">
                        <div class="text-2xl font-black text-green-600" x-text="validCount()"></div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-1">Siap Diimport</div>
                    </div>
                    <div class="bg-white rounded-xl border border-red-200 p-4 text-center">
                        <div class="text-2xl font-black text-red-600" x-text="invalidCount()"></div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-1">Total Error</div>
                    </div>
                    <div class="bg-white rounded-xl border border-blue-200 p-4 text-center">
                        <div class="text-2xl font-black text-blue-600" x-text="selectedCount()"></div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-1">Terpilih Import</div>
                    </div>
                </div>

                {{-- Grouped by class --}}
                <div class="space-y-3 max-h-[45vh] overflow-y-auto pr-1">
                    <template x-for="group in groupedRows()" :key="group.name">
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                            <button type="button" @click="toggleGroup(group.name)"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-slate-50/70 hover:bg-slate-100 transition-colors cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500 transition-transform" :class="expanded[group.name] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="text-sm font-semibold text-slate-800" x-text="group.name"></span>
                                    <span class="text-xs text-slate-400" x-text="'(' + group.rows.length + ' siswa)'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700" x-text="group.validCount + ' valid'"></span>
                                    <span x-show="group.invalidCount > 0" class="text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-700" x-text="group.invalidCount + ' error'"></span>
                                </div>
                            </button>

                            <div x-show="expanded[group.name]" x-cloak class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-white border-b border-slate-100">
                                            <th class="px-3 py-2 w-10 text-center text-[11px] font-semibold text-slate-400 uppercase">Import</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">NISN</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">Nama</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">Kelas</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">Tgl Lahir</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">Kontak Ortu</th>
                                            <th class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="row in group.rows" :key="row.id">
                                            <tr :class="!row.is_valid ? 'bg-red-50/50' : ''">
                                                <td class="px-3 py-2 text-center">
                                                    <input type="checkbox" x-model="row.import" :disabled="!row.is_valid"
                                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer disabled:opacity-40">
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <input type="text" x-model="row.nisn" @input="validateRow(row)"
                                                           class="w-28 px-2 py-1.5 rounded-md border text-sm font-mono focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                           :class="row.errors.includes('NISN kosong') || row.errors.includes('NISN sudah terdaftar') || row.errors.includes('NISN duplikat di file') ? 'border-red-300 bg-red-50' : 'border-slate-200'">
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <input type="text" x-model="row.name" @input="validateRow(row)"
                                                           class="w-40 px-2 py-1.5 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                           :class="row.errors.includes('Nama kosong') ? 'border-red-300 bg-red-50' : 'border-slate-200'">
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <select x-model="row.class_id" @change="onClassChange(row, $event)"
                                                            class="w-32 px-2 py-1.5 rounded-md border text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                            :class="row.class_name_raw && !row.class_id ? 'border-red-300 bg-red-50' : 'border-slate-200'">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <input type="date" x-model="row.birth_date"
                                                           class="w-36 px-2 py-1.5 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <input type="text" x-model="row.parent_contact"
                                                           class="w-32 px-2 py-1.5 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                                <td class="px-3 py-1.5">
                                                    <template x-if="row.is_valid">
                                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            Valid
                                                        </span>
                                                    </template>
                                                    <template x-if="!row.is_valid">
                                                        <div class="flex flex-wrap gap-1 max-w-[160px]">
                                                            <template x-for="err in row.errors" :key="err">
                                                                <span class="text-[11px] font-medium px-1.5 py-0.5 rounded bg-red-100 text-red-600" x-text="err"></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-200">
                    <button type="button" @click="step = 'upload'" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors cursor-pointer">
                        &larr; Ganti File
                    </button>
                    <button type="button" @click="submitImport()" :disabled="selectedCount() === 0 || importing"
                            class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
                        <svg x-show="importing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="importing ? 'Menyimpan...' : ('Import ' + selectedCount() + ' Siswa')"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
</template>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('studentImportModal', () => ({
        show: false,
        step: 'upload',
        file: null,
        fileName: '',
        loading: false,
        importing: false,
        error: null,
        rows: [],
        expanded: {},
        classNames: @json($classes->pluck('name', 'id')),

        open() {
            this.reset();
            this.show = true;
        },
        close() {
            this.show = false;
        },
        reset() {
            this.step = 'upload';
            this.file = null;
            this.fileName = '';
            this.loading = false;
            this.importing = false;
            this.error = null;
            this.rows = [];
            this.expanded = {};
        },
        onFileChange(e) {
            const f = e.target.files[0];
            if (f) { this.file = f; this.fileName = f.name; this.error = null; }
        },
        onDrop(e) {
            const f = e.dataTransfer.files[0];
            if (f) { this.file = f; this.fileName = f.name; this.error = null; }
        },
        async parseFile() {
            if (!this.file) return;
            this.loading = true;
            this.error = null;
            const formData = new FormData();
            formData.append('file', this.file);

            try {
                const res = await fetch('{{ route('admin.students.import.parse') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.error || 'Gagal memproses file.';
                    return;
                }
                if (!data.rows || data.rows.length === 0) {
                    this.error = 'Tidak ada data siswa yang ditemukan pada file.';
                    return;
                }
                this.rows = data.rows;
                this.expanded = {};
                this.groupedRows().forEach(g => { this.expanded[g.name] = true; });
                this.step = 'preview';
            } catch (err) {
                this.error = 'Terjadi kesalahan saat mengunggah file.';
            } finally {
                this.loading = false;
            }
        },
        groupKeyOf(row) {
            if (row.class_id) return row.matched_class_name;
            if (row.class_name_raw) return row.class_name_raw + ' (Tidak Ditemukan)';
            return 'Tanpa Kelas';
        },
        groupedRows() {
            const groups = {};
            for (const row of this.rows) {
                const key = this.groupKeyOf(row);
                if (!groups[key]) groups[key] = [];
                groups[key].push(row);
            }
            return Object.keys(groups).sort().map(name => ({
                name,
                rows: groups[name],
                validCount: groups[name].filter(r => r.is_valid).length,
                invalidCount: groups[name].filter(r => !r.is_valid).length,
            }));
        },
        toggleGroup(name) {
            this.expanded[name] = !this.expanded[name];
        },
        onClassChange(row, e) {
            const id = e.target.value;
            row.class_id = id || null;
            row.matched_class_name = id ? this.classNames[id] : '-';
            this.validateRow(row);
        },
        validateRow(row) {
            const errors = [];
            if (!row.nisn) errors.push('NISN kosong');
            if (!row.name) errors.push('Nama kosong');
            if (row.class_name_raw && !row.class_id) errors.push("Kelas '" + row.class_name_raw + "' tidak ditemukan");

            if (row.nisn) {
                const dupCount = this.rows.filter(r => r.nisn === row.nisn).length;
                if (dupCount > 1) errors.push('NISN duplikat di file');
            }

            row.errors = errors;
            row.is_valid = errors.length === 0;
            row.import = row.is_valid;
        },
        validCount() {
            return this.rows.filter(r => r.is_valid).length;
        },
        invalidCount() {
            return this.rows.filter(r => !r.is_valid).length;
        },
        selectedCount() {
            return this.rows.filter(r => r.import).length;
        },
        async submitImport() {
            const students = this.rows.filter(r => r.import);
            if (students.length === 0) return;

            this.importing = true;
            try {
                const res = await fetch('{{ route('admin.students.import.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ students }),
                });
                const data = await res.json();
                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.error || 'Terjadi kesalahan.' });
                    return;
                }
                this.close();
                window.location.href = data.redirect;
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan data.' });
            } finally {
                this.importing = false;
            }
        },
    }));
});
</script>
