@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
    
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
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex items-center justify-between mb-4">
            <p class="font-semibold text-slate-900">Total Pelanggaran</p>
            <div class="text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
            <div class="text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
            <div class="text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
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
            <div class="w-6 h-6 rounded bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-bold text-slate-900">{{ number_format($pendingCount) }}</h3>
            <p class="text-xs text-slate-400 mt-2">laporan menunggu</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-slate-900">Tren Poin</h3>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-slate-900">Aktivitas Harian</h3>
            <button class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
            </button>
        </div>
        <div class="relative h-64 w-full flex items-center justify-center">
            @if(count($classDistributionData['data']) > 0)
                <canvas id="classDistributionChart"></canvas>
            @else
                <p class="text-sm text-slate-400">Belum ada data aktivitas.</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Violators --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-slate-900">Pelanggaran Terbanyak</h3>
            <button class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
            </button>
        </div>
        <div class="space-y-4">
            @forelse($topViolators as $i => $entry)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 rounded {{ $i === 0 ? 'bg-blue-500' : ($i === 1 ? 'bg-blue-400' : 'bg-slate-200') }}"></div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $entry['student']->name }}</p>
                            <p class="text-xs text-slate-500">{{ $entry['violation_count'] }} pelanggaran</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-rose-500 bg-rose-50 px-2 py-1 rounded">{{ $entry['total_points'] }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-4">Belum ada data pelanggaran.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Points Log --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-slate-900">Aktivitas Terbaru</h3>
            <button class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
            </button>
        </div>
        <div class="space-y-4">
            @forelse($recentLogs as $log)
                <div class="flex items-center justify-between pb-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $log->rule->type->value === 'violation' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                            {{ substr($log->student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $log->student->name }}</p>
                            <p class="text-xs text-slate-500">{{ Str::limit($log->rule->name, 35) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold {{ $log->rule->type->value === 'violation' ? 'text-rose-500' : 'text-emerald-500' }}">
                            {{ $log->rule->type->value === 'achievement' ? '+' : '-' }}{{ $log->points }}
                        </span>
                        <p class="text-[10px] text-slate-400">{{ $log->occurred_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas.</p>
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
        
        let gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
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

        // Aktivitas Harian - Bar Chart (like Most Day Active)
        @if(count($classDistributionData['data']) > 0)
        const classCtx = document.getElementById('classDistributionChart').getContext('2d');
        const classData = @json($classDistributionData);
        const barBaseColor = isDark ? '#1e293b' : '#e2e8f0'; // slate-800 vs slate-200
        
        new Chart(classCtx, {
            type: 'bar',
            data: {
                labels: classData.labels,
                datasets: [{
                    data: classData.data,
                    backgroundColor: barBaseColor,
                    hoverBackgroundColor: '#3b82f6', // blue-500 on hover
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 20
                }]
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
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: tickColor, font: {size: 11} },
                        border: { display: false }
                    },
                    y: {
                        display: false,
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Make the highest value blue
        const chart = Chart.getChart("classDistributionChart");
        const maxVal = Math.max(...chart.data.datasets[0].data);
        const bgColors = chart.data.datasets[0].data.map(val => val === maxVal ? '#3b82f6' : barBaseColor);
        chart.data.datasets[0].backgroundColor = bgColors;
        chart.update();
        @endif

        // Listen for theme changes to update charts dynamically
        window.addEventListener('theme-changed', (e) => {
            const isDarkNow = e.detail === 'dark';
            const newGridColor = isDarkNow ? '#1e293b' : '#f1f5f9';
            const newTooltipBg = isDarkNow ? '#1e293b' : '#ffffff';
            const newTooltipTitle = isDarkNow ? '#f8fafc' : '#0f172a';
            const newTooltipBody = isDarkNow ? '#cbd5e1' : '#475569';
            const newTooltipBorder = isDarkNow ? '#334155' : '#e2e8f0';
            const newBarBaseColor = isDarkNow ? '#1e293b' : '#e2e8f0';
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

            const barChart = Chart.getChart('classDistributionChart');
            if (barChart) {
                barChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                barChart.options.plugins.tooltip.titleColor = newTooltipTitle;
                barChart.options.plugins.tooltip.bodyColor = newTooltipBody;
                barChart.options.plugins.tooltip.borderColor = newTooltipBorder;
                
                const maxV = Math.max(...barChart.data.datasets[0].data);
                barChart.data.datasets[0].backgroundColor = barChart.data.datasets[0].data.map(val => val === maxV ? '#3b82f6' : newBarBaseColor);
                barChart.update();
            }
        });
    });
</script>
@endpush
