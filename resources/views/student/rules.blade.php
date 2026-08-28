@extends('layouts.public')
@section('title', 'Peraturan Sekolah')
@section('page-header', 'Peraturan Sekolah')
@section('page-desc', 'Daftar peraturan, kategori, dan bobot poin yang berlaku')

@section('content')
<div class="space-y-6">
    @php $grouped = $rules->groupBy(fn($r) => $r->type->label()); @endphp
    @forelse($grouped as $type => $group)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center gap-2">
                @if($type === 'Pelanggaran')
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @else
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                @endif
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">{{ $type }}</h2>
                <span class="ml-auto text-xs text-slate-400">{{ $group->count() }} peraturan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($group as $rule)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 text-sm text-slate-800 font-medium">{{ $rule->name }}</td>
                                <td class="px-6 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ $rule->category?->label() ?? '' }}</td>
                                <td class="px-6 py-3.5 text-sm text-right whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2.5 py-1 rounded-full text-sm font-bold border {{ $rule->type->value === 'violation' ? 'border-red-200 bg-red-50 text-red-500' : 'border-green-200 bg-green-50 text-green-500' }}">
                                        {{ $rule->type->value === 'achievement' ? '+' : '' }}{{ $rule->points }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
            <p class="text-slate-500">Belum ada peraturan yang terdaftar.</p>
        </div>
    @endforelse
</div>
@endsection
