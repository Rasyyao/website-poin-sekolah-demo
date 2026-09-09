@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-desc', 'Ringkasan statistik poin, prestasi, dan pelanggaran kedisiplinan siswa.')


@section('page-header', 'Dashboard')
@section('page-actions')
    <div class="flex items-center gap-3">
        <div class="flex items-center bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 px-3 py-2 border-r border-slate-200 text-sm font-medium text-slate-700">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ \Carbon\Carbon::parse($from)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}</span>
            </div>
            <form method="GET" action="{{ route('admin.reports.dashboard') }}" class="relative">
                <select name="filter" onchange="this.form.submit()" class="appearance-none bg-transparent text-slate-700 py-2 pl-3 pr-8 text-sm font-medium focus:outline-none cursor-pointer">
                    <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>This day</option>
                    <option value="last_7_days" {{ $filter === 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="last_30_days" {{ $filter === 'last_30_days' ? 'selected' : '' }}>Last 30 days</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </form>
        </div>

        <a href="{{ route('admin.reports.dashboard.export', ['filter' => $filter]) }}" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </a>
    </div>
@endsection

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex items-center justify-between mb-4">
            <p class="font-semibold text-slate-900">Total Pelanggaran</p>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_violations']) }}</h3>
            <p class="text-xs text-slate-400 mt-2">{{ number_format($stats['total_violation_points']) }} poin terkumpul</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex items-center justify-between mb-4">
            <p class="font-semibold text-slate-900">Total Prestasi</p>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_achievements']) }}</h3>
            <p class="text-xs text-slate-400 mt-2">{{ number_format($stats['total_achievement_points']) }} poin didapat</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex items-center justify-between mb-4">
            <p class="font-semibold text-slate-900">Siswa Bermasalah</p>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2.25m0 4.5h.008v.008H12v-.008zM10.29 3.86l-8.16 14.14A2 2 0 004 21h16a2 2 0 001.87-3l-8.16-14.14a2 2 0 00-3.42 0z"/></svg>
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['students_with_violations']) }}</h3>
            <p class="text-xs text-slate-400 mt-2">siswa tercatat melanggar</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex items-center justify-between mb-4">
            <p class="font-semibold text-slate-900">Pending Approval</p>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-bold text-slate-900">{{ number_format($pendingCount) }}</h3>
            <p class="text-xs text-slate-400 mt-2">laporan menunggu</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-semibold text-slate-900">Tren Poin</h3>
        </div>
        <div class="relative w-full" style="height: 200px;">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-slate-900">Persebaran Siswa</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Jumlah siswa per kelas</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-blue-500/10 text-blue-500 border border-blue-500/20 rounded-full">
                    {{ $classDistributionData['total_classes'] ?? count($classDistributionData['labels']) }} Kelas
                </span>
            </div>

            <div class="relative w-full flex items-center justify-center my-1" style="height: 140px; max-width: 140px; margin-left: auto; margin-right: auto;">
                @if(!empty($classDistributionData['data']) && array_sum($classDistributionData['data']) > 0)
                    <canvas id="classDistributionChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xl font-bold text-slate-900 tracking-tight leading-none">{{ $classDistributionData['total_students'] ?? 0 }}</span>
                        <span class="text-[10px] font-medium text-slate-400 mt-0.5">Total Siswa</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-1 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-xs text-slate-400">Belum ada data siswa.</p>
                    </div>
                @endif
            </div>
        </div>

        @if(!empty($classDistributionData['labels']) && array_sum($classDistributionData['data']) > 0)
            <div class="mt-2 pt-2 border-t border-slate-100 flex flex-wrap items-center justify-center gap-1.5 max-h-20 overflow-y-auto">
                @php
                    $palette = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316', '#6366f1'];
                @endphp
                @foreach($classDistributionData['labels'] as $idx => $className)
                    @php
                        $color = $palette[$idx % count($palette)];
                        $count = $classDistributionData['data'][$idx] ?? 0;
                        $percent = $classDistributionData['total_students'] > 0 ? round(($count / $classDistributionData['total_students']) * 100) : 0;
                    @endphp
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100 text-[11px]">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $color }}"></span>
                        <span class="font-medium text-slate-700">{{ $className }}</span>
                        <span class="font-bold text-slate-900 ml-0.5">{{ $count }}</span>
                        <span class="text-[9px] text-slate-400">({{ $percent }}%)</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Violators --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-900">Pelanggaran Terbanyak</h3>
            <a href="{{ route('admin.students.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Lihat Semua
            </a>
        </div>
        <div class="space-y-5">
            @forelse($topViolators as $i => $entry)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-slate-400 w-4 text-center">{{ $i + 1 }}</span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $entry['student']->name }}</p>
                            <p class="text-xs text-slate-400">{{ $entry['violation_count'] }} pelanggaran</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-red-500 bg-red-500/10 border border-red-500/20 px-2.5 py-1 rounded-lg tabular-nums">{{ $entry['total_points'] }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada data pelanggaran.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Points Log --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-900">Aktivitas Terbaru</h3>
            <a href="{{ route('admin.points-log.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Lihat Semua
            </a>
        </div>
        <div class="space-y-4">
            @forelse($recentLogs as $log)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $log->rule->type->value === 'violation' ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' }}">
                            {{ substr($log->student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $log->student->name }}</p>
                            <p class="text-xs text-slate-400">{{ $log->rule->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold tabular-nums {{ $log->rule->type->value === 'violation' ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $log->rule->type->value === 'achievement' ? '+' : '-' }}{{ $log->points }}
                        </span>
                        <p class="text-[10px] text-slate-400">{{ $log->occurred_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tren Poin - Line Chart with Gradient (like Total Profit)
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        const chartData = @json($chartData);
        
        let gradientBlue = ctx.createLinearGradient(0, 0, 0, 200);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.2)'); // blue-500
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0)');

        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? '#1e293b' : '#f1f5f9'; // slate-800 vs slate-100
        const tickColor = isDark ? '#94a3b8' : '#94a3b8'; // slate-400
        const tooltipBg = isDark ? '#1e293b' : '#ffffff'; // slate-800 vs white
        const tooltipTitle = isDark ? '#f8fafc' : '#0f172a'; // slate-50 vs slate-900
        const tooltipBody = isDark ? '#cbd5e1' : '#475569'; // slate-300 vs slate-600
        const tooltipBorder = isDark ? '#334155' : '#e2e8f0'; // slate-700 vs slate-200

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Pelanggaran',
                        data: chartData.violations,
                        borderColor: '#3b82f6', // blue-500
                        backgroundColor: gradientBlue,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: isDark ? '#0f172a' : '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipTitle,
                        bodyColor: tooltipBody,
                        borderColor: tooltipBorder,
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: gridColor,
                            drawBorder: false,
                        },
                        ticks: { color: tickColor, stepSize: 10, padding: 10 },
                        border: { display: false }
                    },
                    x: {
                        grid: { 
                            display: false,
                            drawBorder: false
                        },
                        ticks: { color: tickColor, padding: 10 },
                        border: { display: false }
                    }
                }
            }
        });

        // Persebaran Siswa per Kelas - Doughnut Chart
        @if(!empty($classDistributionData['data']) && array_sum($classDistributionData['data']) > 0)
        const classCtx = document.getElementById('classDistributionChart').getContext('2d');
        const classData = @json($classDistributionData);
        const palette = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316', '#6366f1'];
        const sliceColors = classData.labels.map((_, i) => palette[i % palette.length]);

        new Chart(classCtx, {
            type: 'doughnut',
            data: {
                labels: classData.labels,
                datasets: [{
                    data: classData.data,
                    backgroundColor: sliceColors,
                    hoverBackgroundColor: sliceColors,
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipTitle,
                        bodyColor: tooltipBody,
                        borderColor: tooltipBorder,
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed || 0;
                                const total = classData.total_students || 1;
                                const pct = ((val / total) * 100).toFixed(1);
                                return ` ${val} Siswa (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Listen for theme changes to update charts dynamically
        window.addEventListener('theme-changed', (e) => {
            const isDarkNow = e.detail === 'dark';
            const newGridColor = isDarkNow ? '#1e293b' : '#f1f5f9';
            const newTooltipBg = isDarkNow ? '#1e293b' : '#ffffff';
            const newTooltipTitle = isDarkNow ? '#f8fafc' : '#0f172a';
            const newTooltipBody = isDarkNow ? '#cbd5e1' : '#475569';
            const newTooltipBorder = isDarkNow ? '#334155' : '#e2e8f0';
            const newPointBg = isDarkNow ? '#0f172a' : '#ffffff';

            const lineChart = Chart.getChart('dashboardChart');
            if (lineChart) {
                lineChart.options.scales.y.grid.color = newGridColor;
                lineChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                lineChart.options.plugins.tooltip.titleColor = newTooltipTitle;
                lineChart.options.plugins.tooltip.bodyColor = newTooltipBody;
                lineChart.options.plugins.tooltip.borderColor = newTooltipBorder;
                lineChart.data.datasets[0].pointBackgroundColor = newPointBg;
                lineChart.update();
            }

            const doughnutChart = Chart.getChart('classDistributionChart');
            if (doughnutChart) {
                doughnutChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                doughnutChart.options.plugins.tooltip.titleColor = newTooltipTitle;
                doughnutChart.options.plugins.tooltip.bodyColor = newTooltipBody;
                doughnutChart.options.plugins.tooltip.borderColor = newTooltipBorder;
                doughnutChart.data.datasets[0].borderColor = isDarkNow ? '#1e293b' : '#ffffff';
                doughnutChart.update();
            }
        });
    });
</script>
@endpush
