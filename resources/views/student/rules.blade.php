@extends('layouts.public')
@section('title', 'Peraturan Sekolah')
@section('nav-links')
    <a href="{{ route('student.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-200">Dashboard</a>
    <a href="{{ route('student.appeals.index') }}" class="text-sm text-gray-400 hover:text-gray-200">Banding</a>
@endsection

@section('content')
<h1 class="text-xl font-bold text-gray-100 mb-6">Peraturan Sekolah</h1>

<div class="space-y-4">
    @php $grouped = $rules->groupBy(fn($r) => $r->type->label()); @endphp
    @foreach($grouped as $type => $group)
        <div>
            <h2 class="text-sm font-semibold text-gray-400 uppercase mb-3">{{ $type }}</h2>
            <div class="bg-surface-light rounded-xl border border-gray-700/50 overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($group as $rule)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-200">{{ $rule->name }}</td>
                                <td class="px-5 py-3 text-xs text-gray-400">{{ $rule->category?->label() ?? '' }}</td>
                                <td class="px-5 py-3 text-sm text-right font-medium {{ $rule->points < 0 ? 'text-red-400' : 'text-green-400' }}">{{ $rule->points > 0 ? '+' : '' }}{{ $rule->points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
