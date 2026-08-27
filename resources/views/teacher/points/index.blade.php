@extends('layouts.app')
@section('title', 'Riwayat Input Poin')
@section('page-title', 'Riwayat Input Poin')
@section('page-header', 'Log Input Poin')
@section('page-desc', 'Daftar poin yang telah Anda inputkan ke siswa.')

@section('page-actions')
    <button x-data @click="$dispatch('open-modal')" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">+ Input Poin</button>
@endsection
@section('content')
<div x-data="{ showModal: {{ $errors->any() ? 'true' : 'false' }} }" @open-modal.window="showModal = true">

    <div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-700/50">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Tanggal</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Peraturan</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Poin</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-700/30">
                @forelse($logs as $log)
                    <tr class="hover:bg-surface-lighter/50">
                        <td class="px-5 py-3 text-sm text-gray-300">{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-200 font-medium">{{ $log->student->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-300">{{ $log->rule->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm text-center font-medium {{ $log->points < 0 ? 'text-red-400' : 'text-green-400' }}">{{ $log->points }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $log->status->value === 'approved' ? 'bg-green-500/10 text-green-400' : ($log->status->value === 'pending' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') }}">{{ $log->status->label() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">Anda belum pernah menginput poin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->withQueryString()->links() }}</div>

    {{-- Create Modal --}}
    <template x-teleport="body">
    <div>
    {{-- Backdrop --}}
    <div x-show="showModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="showModal = false"
         class="fixed inset-0 bg-black/75"
         style="z-index:40;"></div>

    {{-- Modal Box --}}
    <div x-show="showModal"
         class="fixed inset-0 flex items-center justify-center p-4"
         style="z-index:50;">
        <div class="bg-surface rounded-xl shadow-2xl border border-gray-700/50 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6" @click.stop>
            
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-100" id="modal-title">Input Poin Siswa</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-200 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('teacher.points.store') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Siswa <span class="text-red-400">*</span></label>
                            <select name="student_id" id="student-select" required class="w-full" placeholder="Cari Siswa...">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }} ({{ $student->nisn }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Peraturan <span class="text-red-400">*</span></label>
                            <select name="rule_id" id="rule-select" required class="w-full" placeholder="Cari Peraturan...">
                                <option value="">-- Pilih Peraturan --</option>
                                @foreach($rules as $rule)
                                    <option value="{{ $rule->id }}" {{ old('rule_id') == $rule->id ? 'selected' : '' }}>
                                        {{ $rule->name }} ({{ $rule->points > 0 ? '+' : '' }}{{ $rule->points }})
                                        @if($rule->category) [{{ $rule->category->label() }}] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-medium text-gray-300">Tanggal Kejadian</label>
                                <button type="button" onclick="const tzoffset = (new Date()).getTimezoneOffset() * 60000; document.getElementById('occurred_at').value = (new Date(Date.now() - tzoffset)).toISOString().slice(0,19);" class="text-xs text-primary-400 hover:text-primary-300 transition-colors cursor-pointer">Set Waktu Saat Ini</button>
                            </div>
                            <input type="datetime-local" step="1" name="occurred_at" id="occurred_at" value="{{ old('occurred_at') }}" class="w-full px-4 py-2.5 rounded-lg bg-surface-light border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" style="color-scheme: dark;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Catatan</label>
                            <textarea name="note" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Catatan opsional...">{{ old('note') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">URL Bukti (opsional)</label>
                            <input type="text" name="evidence_url" value="{{ old('evidence_url') }}" class="w-full px-4 py-2.5 rounded-lg bg-surface border border-gray-600 text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="https://...">
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-700/50 mt-6">
                            <button type="button" @click="showModal = false" class="px-6 py-2.5 rounded-lg bg-surface-lighter text-gray-300 text-sm hover:bg-gray-600 transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition-colors cursor-pointer">Catat Poin</button>
                        </div>
                    </form>
                
        </div>
    </div>
    </div>
</template>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof TomSelect !== 'undefined') {
            new TomSelect("#student-select", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                maxOptions: 50
            });
            new TomSelect("#rule-select", {
                create: false,
                sortField: { field: "text", direction: "asc" },
                maxOptions: 50
            });
        }
    });
</script>
@endsection
