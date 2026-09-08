<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Siswa - {{ $student->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        
        .page-header { text-align: center; padding: 24px 0 16px; border-bottom: 3px solid #2563eb; margin-bottom: 24px; }
        .page-header h1 { font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: 0.5px; margin-bottom: 4px; }
        .page-header .school-name { font-size: 13px; color: #2563eb; font-weight: 600; }
        .page-header .export-date { font-size: 9px; color: #94a3b8; margin-top: 6px; }

        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.8px; padding: 6px 12px; background: #eff6ff; border-left: 4px solid #2563eb; margin-bottom: 10px; }

        .info-grid { width: 100%; margin-bottom: 16px; }
        .info-grid td { padding: 5px 10px; vertical-align: top; }
        .info-grid .label { font-weight: 600; color: #475569; width: 160px; white-space: nowrap; }
        .info-grid .value { color: #0f172a; }
        .info-grid tr:nth-child(even) { background: #f8fafc; }

        .stats-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .stats-grid td { padding: 10px 14px; text-align: center; border: 1px solid #e2e8f0; }
        .stats-grid .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .stats-grid .stat-value { font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .stats-grid .stat-value.danger { color: #dc2626; }
        .stats-grid .stat-value.success { color: #16a34a; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 10px; font-weight: 700; }
        .status-safe { background: #dcfce7; color: #15803d; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-danger { background: #fee2e2; color: #991b1b; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data-table thead th { background: #2563eb; color: #ffffff; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; }
        table.data-table tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.data-table tbody tr:hover { background: #f1f5f9; }

        .text-danger { color: #dc2626; font-weight: 600; }
        .text-success { color: #16a34a; font-weight: 600; }
        .text-muted { color: #94a3b8; font-style: italic; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .category-table { width: 100%; border-collapse: collapse; }
        .category-table th { background: #f1f5f9; color: #475569; padding: 6px 10px; text-align: left; font-size: 10px; border-bottom: 2px solid #e2e8f0; }
        .category-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .category-table tr:nth-child(even) { background: #f8fafc; }

        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 8px; color: #94a3b8; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>LAPORAN POIN SISWA</h1>
        <div class="school-name">{{ $student->school->name ?? website_name() }}</div>
        <div class="export-date">Diekspor pada {{ now()->format('d M Y, H:i') }}</div>
    </div>

    {{-- Identity Section --}}
    <div class="section">
        <div class="section-title">Identitas Siswa</div>
        <table class="info-grid">
            <tr><td class="label">Nama Lengkap</td><td class="value">{{ $student->name }}</td></tr>
            <tr><td class="label">NISN</td><td class="value">{{ $student->nisn }}</td></tr>
            <tr><td class="label">Kelas</td><td class="value">{{ $student->currentClass->name ?? '-' }}</td></tr>
            <tr><td class="label">Wali Kelas</td><td class="value">{{ $student->currentClass?->homeroomTeacher?->name ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td class="value">{{ $student->birth_date ? $student->birth_date->format('d M Y') : '-' }}</td></tr>
        </table>
    </div>

    {{-- Stats Summary --}}
    @php
        $violations = $logs->filter(fn($l) => $l->rule->type->value === 'violation');
        $achievements = $logs->filter(fn($l) => $l->rule->type->value === 'achievement');
        $violationPts = (int) $violations->sum('points');
        $achievementPts = (int) $achievements->sum('points');
    @endphp

    <div class="section">
        <div class="section-title">Ringkasan Poin</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-label">Total Pelanggaran</div>
                    <div class="stat-value danger">{{ $violations->count() }}</div>
                </td>
                <td>
                    <div class="stat-label">Poin Pelanggaran</div>
                    <div class="stat-value danger">{{ number_format($violationPts) }}</div>
                </td>
                <td>
                    <div class="stat-label">Total Prestasi</div>
                    <div class="stat-value success">{{ $achievements->count() }}</div>
                </td>
                <td>
                    <div class="stat-label">Poin Prestasi</div>
                    <div class="stat-value success">{{ number_format($achievementPts) }}</div>
                </td>
                <td>
                    <div class="stat-label">Nett Poin</div>
                    <div class="stat-value" style="color: {{ ($achievementPts - $violationPts) >= 0 ? '#16a34a' : '#dc2626' }}">{{ $achievementPts - $violationPts >= 0 ? '+' : '' }}{{ number_format($achievementPts - $violationPts) }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Status --}}
    <div class="section">
        <div class="section-title">Status Kedisiplinan</div>
        @php
            $thresholds = \App\Models\RuleThreshold::withoutGlobalScopes()
                ->where('school_id', $student->school_id)
                ->orderByDesc('min_points')
                ->get();
            $currentAction = null;
            $statusClass = 'status-safe';
            foreach ($thresholds as $t) {
                if ($violationPts >= $t->min_points) {
                    $currentAction = $t->action;
                    $statusClass = $violationPts >= ($thresholds->first()->min_points ?? 0) ? 'status-danger' : 'status-warning';
                    break;
                }
            }
        @endphp
        <p style="margin: 8px 0;">
            Status: <span class="status-badge {{ $statusClass }}">{{ $currentAction ?? 'Aman — Tidak melewati threshold' }}</span>
        </p>
    </div>

    {{-- Category Breakdown --}}
    <div class="section">
        <div class="section-title">Breakdown per Kategori</div>
        <table class="category-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Total Poin</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $byCategory = $violations->groupBy(fn($l) => $l->rule->category?->label() ?? 'Lainnya');
                @endphp
                @foreach(['Ringan', 'Sedang', 'Berat'] as $cat)
                    @php $catLogs = $byCategory->get($cat, collect()); @endphp
                    <tr>
                        <td>{{ $cat }}</td>
                        <td class="text-center">{{ $catLogs->count() }}</td>
                        <td class="text-center">{{ (int) $catLogs->sum('points') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Log History --}}
    <div class="section">
        <div class="section-title">Riwayat Poin Lengkap</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Nama Aturan</th>
                    <th class="text-center">Poin</th>
                    <th>Dilaporkan Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $log->occurred_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->rule->type->label() }}</td>
                        <td>{{ $log->rule->category?->label() ?? '-' }}</td>
                        <td>{{ $log->rule->name ?? '-' }}</td>
                        <td class="text-center {{ $log->rule->type->value === 'violation' ? 'text-danger' : 'text-success' }}">
                            {{ $log->rule->type->value === 'violation' ? '-' : '+' }}{{ $log->points }}
                        </td>
                        <td>{{ $log->reporter->name ?? 'Sistem' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 20px;">Belum ada riwayat poin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        {{ website_name() }} — Laporan ini digenerate secara otomatis pada {{ now()->format('d M Y, H:i') }}. Dokumen ini bersifat rahasia.
    </div>
</body>
</html>
