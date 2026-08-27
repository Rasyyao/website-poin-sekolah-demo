@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-header', 'Dashboard')
@section('page-desc', 'Ikhtisar statistik dan aktivitas sekolah Anda')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Total Pelanggaran</p>
                <p class="text-2xl font-bold text-red-400 mt-1">{{ $stats['total_violations'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ $stats['total_violation_points'] }} poin</p>
    </div>

    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Total Prestasi</p>
                <p class="text-2xl font-bold text-green-400 mt-1">{{ $stats['total_achievements'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ $stats['total_achievement_points'] }} poin</p>
    </div>

    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Siswa Bermasalah</p>
                <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $stats['students_with_violations'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">siswa dengan pelanggaran</p>
    </div>

    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Pending Approval</p>
                <p class="text-2xl font-bold text-orange-400 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">menunggu persetujuan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4">Tren Poin (30 Hari Terakhir)</h3>
        <div class="relative h-64 w-full">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>
    
    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4">Sebaran Pelanggaran per Kelas (30 Hari)</h3>
        <div class="relative h-64 w-full flex items-center justify-center">
            @if(count($classDistributionData['data']) > 0)
                <canvas id="classDistributionChart"></canvas>
            @else
                <p class="text-sm text-gray-500">Belum ada data pelanggaran.</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Violators --}}
    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4">Siswa dengan Pelanggaran Terbanyak</h3>
        <div class="space-y-3">
            @forelse($topViolators as $i => $entry)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-700/30' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-surface flex items-center justify-center text-xs font-medium {{ $i < 3 ? 'text-red-400' : 'text-gray-500' }}">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm text-gray-200">{{ $entry['student']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $entry['violation_count'] }} pelanggaran</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-red-400">{{ $entry['total_points'] }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada data pelanggaran.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Points Log --}}
    <div class="bg-surface-light rounded-xl border border-gray-700/50 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4">Aktivitas Terbaru</h3>
        <div class="space-y-3">
            @forelse($recentLogs as $log)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-700/30' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $log->rule->type->value === 'violation' ? 'bg-red-400' : 'bg-green-400' }}"></div>
                        <div>
                            <p class="text-sm text-gray-200">{{ $log->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $log->rule->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-medium {{ $log->rule->type->value === 'violation' ? 'text-red-400' : 'text-green-400' }}">{{ $log->rule->type->value === 'achievement' ? '+' : '' }}{{ $log->points }}</span>
                        <p class="text-xs text-gray-500">{{ $log->occurred_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Pelanggaran',
                        data: chartData.violations,
                        backgroundColor: 'rgba(248, 113, 113, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Prestasi',
                        data: chartData.achievements,
                        backgroundColor: 'rgba(74, 222, 128, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#64748b' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e2e8f0' },
                        ticks: { color: '#64748b', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });

        @if(count($classDistributionData['data']) > 0)
        const classCtx = document.getElementById('classDistributionChart').getContext('2d');
        const classData = @json($classDistributionData);
        
        new Chart(classCtx, {
            type: 'doughnut',
            data: {
                labels: classData.labels,
                datasets: [{
                    data: classData.data,
                    backgroundColor: [
                        'rgba(248, 113, 113, 0.8)', // red
                        'rgba(251, 146, 60, 0.8)',  // orange
                        'rgba(250, 204, 21, 0.8)',  // yellow
                        'rgba(96, 165, 250, 0.8)',  // blue
                        'rgba(192, 132, 252, 0.8)'  // purple
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#64748b', font: { size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
        @endif
    });
</script>
@endpush
