<?php

namespace App\Exports;

use App\Models\PointsLog;
use App\Models\RuleThreshold;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardMultiSheetExport
{
    protected int $schoolId;
    protected string $from;
    protected string $to;

    // Styling constants
    private const HEADER_BG = 'FF2563EB'; // Blue-600
    private const HEADER_FONT = 'FFFFFFFF';
    private const SUBHEADER_BG = 'FFF1F5F9'; // Slate-100
    private const BORDER_COLOR = 'FFE2E8F0'; // Slate-200
    private const WARNING_BG = 'FFFFF7ED'; // Orange-50
    private const DANGER_BG = 'FFFEF2F2'; // Red-50

    public function __construct(int $schoolId, string $from, string $to)
    {
        $this->schoolId = $schoolId;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Build and download the multi-sheet spreadsheet.
     */
    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(website_name())
            ->setTitle('Laporan Dashboard - ' . website_name())
            ->setDescription('Laporan lengkap poin siswa dari ' . $this->from . ' hingga ' . $this->to);

        // Build all 4 sheets
        $this->buildRingkasanSheet($spreadsheet);
        $this->buildTopSiswaSheet($spreadsheet);
        $this->buildSiswaPerluDihandleSheet($spreadsheet);
        $this->buildPerKelasSheet($spreadsheet);

        // Set first sheet active
        $spreadsheet->setActiveSheetIndex(0);

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    // ── Sheet 1: Ringkasan Statistik ────────────────────────

    private function buildRingkasanSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Statistik');

        $fromDate = Carbon::parse($this->from);
        $toDate = Carbon::parse($this->to);

        // Title
        $row = 1;
        $sheet->setCellValue('A' . $row, 'RINGKASAN STATISTIK POIN SISWA');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, website_name() . ' — Periode: ' . $fromDate->format('d M Y') . ' s/d ' . $toDate->format('d M Y'));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        $row++;
        $sheet->setCellValue('A' . $row, 'Diekspor pada: ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(9)->setItalic(true)->setColor(new Color('FF94A3B8'));

        // ── Overall Stats ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'STATISTIK KESELURUHAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':F' . $row);

        $baseQuery = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereBetween('occurred_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59']);

        $totalViolations = (clone $baseQuery)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->count();
        $totalAchievements = (clone $baseQuery)->whereHas('rule', fn($q) => $q->where('type', 'achievement'))->count();
        $totalViolationPts = (int)(clone $baseQuery)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->sum('points');
        $totalAchievementPts = (int)(clone $baseQuery)->whereHas('rule', fn($q) => $q->where('type', 'achievement'))->sum('points');
        $studentsWithViolations = (clone $baseQuery)->whereHas('rule', fn($q) => $q->where('type', 'violation'))->distinct('student_id')->count('student_id');

        $row++;
        $statsData = [
            ['Metrik', 'Nilai'],
            ['Total Pelanggaran', $totalViolations],
            ['Total Prestasi', $totalAchievements],
            ['Total Poin Pelanggaran', $totalViolationPts],
            ['Total Poin Prestasi', $totalAchievementPts],
            ['Siswa Terlibat Pelanggaran', $studentsWithViolations],
        ];

        foreach ($statsData as $i => $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            if ($i === 0) {
                $this->applyHeaderRow($sheet, 'A' . $row . ':B' . $row);
            } else {
                $this->applyDataRow($sheet, 'A' . $row . ':B' . $row, $i % 2 === 0);
            }
            $row++;
        }

        // ── Daily Breakdown ──
        $row += 1;
        $sheet->setCellValue('A' . $row, 'REKAP HARIAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $dailyHeaders = ['Tanggal', 'Jumlah Pelanggaran', 'Poin Pelanggaran', 'Jumlah Prestasi', 'Poin Prestasi', 'Total Kejadian'];
        foreach ($dailyHeaders as $ci => $header) {
            $col = chr(65 + $ci); // A-F
            $sheet->setCellValue($col . $row, $header);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        // Fetch all logs in one query
        $allLogs = PointsLog::withoutGlobalScopes()
            ->with('rule:id,type')
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereBetween('occurred_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            ->get();

        $dailyData = $allLogs->groupBy(fn($log) => $log->occurred_at->format('Y-m-d'))
            ->sortKeys();

        $rowIdx = 0;
        foreach ($dailyData as $date => $logs) {
            $row++;
            $violations = $logs->filter(fn($l) => $l->rule->type->value === 'violation');
            $achievements = $logs->filter(fn($l) => $l->rule->type->value === 'achievement');

            $sheet->setCellValue('A' . $row, Carbon::parse($date)->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $violations->count());
            $sheet->setCellValue('C' . $row, (int) $violations->sum('points'));
            $sheet->setCellValue('D' . $row, $achievements->count());
            $sheet->setCellValue('E' . $row, (int) $achievements->sum('points'));
            $sheet->setCellValue('F' . $row, $logs->count());
            $this->applyDataRow($sheet, 'A' . $row . ':F' . $row, $rowIdx % 2 === 0);
            $rowIdx++;
        }

        // ── Monthly Breakdown ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'REKAP BULANAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        foreach ($dailyHeaders as $ci => $header) {
            $col = chr(65 + $ci);
            $headerText = $ci === 0 ? 'Bulan' : $header;
            $sheet->setCellValue($col . $row, $headerText);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        $monthlyData = $allLogs->groupBy(fn($log) => $log->occurred_at->format('Y-m'))
            ->sortKeys();

        $rowIdx = 0;
        foreach ($monthlyData as $month => $logs) {
            $row++;
            $violations = $logs->filter(fn($l) => $l->rule->type->value === 'violation');
            $achievements = $logs->filter(fn($l) => $l->rule->type->value === 'achievement');

            $sheet->setCellValue('A' . $row, Carbon::parse($month . '-01')->format('F Y'));
            $sheet->setCellValue('B' . $row, $violations->count());
            $sheet->setCellValue('C' . $row, (int) $violations->sum('points'));
            $sheet->setCellValue('D' . $row, $achievements->count());
            $sheet->setCellValue('E' . $row, (int) $achievements->sum('points'));
            $sheet->setCellValue('F' . $row, $logs->count());
            $this->applyDataRow($sheet, 'A' . $row . ':F' . $row, $rowIdx % 2 === 0);
            $rowIdx++;
        }

        // ── Weekly Breakdown ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'REKAP MINGGUAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $weeklyHeaders = ['Minggu Ke', 'Jumlah Pelanggaran', 'Poin Pelanggaran', 'Jumlah Prestasi', 'Poin Prestasi', 'Total Kejadian'];
        foreach ($weeklyHeaders as $ci => $header) {
            $col = chr(65 + $ci);
            $sheet->setCellValue($col . $row, $header);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        $weeklyData = $allLogs->groupBy(fn($log) => $log->occurred_at->format('Y-W'))
            ->sortKeys();

        $rowIdx = 0;
        foreach ($weeklyData as $week => $logs) {
            $row++;
            $violations = $logs->filter(fn($l) => $l->rule->type->value === 'violation');
            $achievements = $logs->filter(fn($l) => $l->rule->type->value === 'achievement');

            $parts = explode('-', $week);
            $weekLabel = 'Minggu ' . (int)$parts[1] . ', ' . $parts[0];

            $sheet->setCellValue('A' . $row, $weekLabel);
            $sheet->setCellValue('B' . $row, $violations->count());
            $sheet->setCellValue('C' . $row, (int) $violations->sum('points'));
            $sheet->setCellValue('D' . $row, $achievements->count());
            $sheet->setCellValue('E' . $row, (int) $achievements->sum('points'));
            $sheet->setCellValue('F' . $row, $logs->count());
            $this->applyDataRow($sheet, 'A' . $row . ':F' . $row, $rowIdx % 2 === 0);
            $rowIdx++;
        }

        // Freeze top rows and auto-size
        $sheet->freezePane('A5');
        $this->autoSizeColumns($sheet, 'A', 'F');
    }

    // ── Sheet 2: Top Siswa ──────────────────────────────────

    private function buildTopSiswaSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Top Siswa');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'RANKING SISWA TERBAIK');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Siswa dengan poin pelanggaran paling sedikit dan poin prestasi tertinggi');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        // Fetch all students with their points
        $students = Student::withoutGlobalScopes()
            ->with(['currentClass.homeroomTeacher'])
            ->where('school_id', $this->schoolId)
            ->get();

        // Batch-fetch violation and achievement points
        $violationPts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->select('student_id', DB::raw('SUM(points) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $achievementPts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'achievement'))
            ->select('student_id', DB::raw('SUM(points) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $violationCounts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->select('student_id', DB::raw('COUNT(*) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        // Sort: fewest violations first, then most achievements
        $ranked = $students->map(function ($s) use ($violationPts, $achievementPts, $violationCounts) {
            return [
                'student' => $s,
                'violation_points' => (int) ($violationPts[$s->id] ?? 0),
                'achievement_points' => (int) ($achievementPts[$s->id] ?? 0),
                'violation_count' => (int) ($violationCounts[$s->id] ?? 0),
            ];
        })->sortBy('violation_points')->sortByDesc('achievement_points')->values();

        $row += 2;
        $headers = ['Ranking', 'Nama Siswa', 'Kelas', 'Wali Kelas', 'Poin Pelanggaran', 'Poin Prestasi'];
        foreach ($headers as $ci => $header) {
            $col = chr(65 + $ci);
            $sheet->setCellValue($col . $row, $header);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        foreach ($ranked as $i => $data) {
            $row++;
            $s = $data['student'];
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $s->name);
            $sheet->setCellValue('C' . $row, $s->currentClass->name ?? '-');
            $sheet->setCellValue('D' . $row, $s->currentClass?->homeroomTeacher?->name ?? '-');
            $sheet->setCellValue('E' . $row, $data['violation_points']);
            $sheet->setCellValue('F' . $row, $data['achievement_points']);
            $this->applyDataRow($sheet, 'A' . $row . ':F' . $row, $i % 2 === 0);

            // Highlight top 3
            if ($i < 3) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFECFDF5'); // Emerald-50
            }
        }

        $sheet->freezePane('A5');
        $this->autoSizeColumns($sheet, 'A', 'F');
    }

    // ── Sheet 3: Siswa Perlu Di-handle ──────────────────────

    private function buildSiswaPerluDihandleSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Siswa Perlu Di-handle');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'SISWA YANG PERLU DITANGANI');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':H' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Siswa yang melewati threshold poin berdasarkan konfigurasi admin');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        // Get thresholds from settings
        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->orderByDesc('min_points')
            ->get();

        if ($thresholds->isEmpty()) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Tidak ada threshold yang dikonfigurasi. Silakan atur threshold di halaman Settings.');
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FFEF4444'));
            $this->autoSizeColumns($sheet, 'A', 'H');
            return;
        }

        $minThreshold = $thresholds->last()->min_points;

        // Get students exceeding thresholds
        $students = Student::withoutGlobalScopes()
            ->with(['currentClass.homeroomTeacher'])
            ->where('school_id', $this->schoolId)
            ->selectRaw('students.*, COALESCE((
                SELECT SUM(points_log.points) 
                FROM points_log 
                INNER JOIN rules ON points_log.rule_id = rules.id
                WHERE points_log.student_id = students.id 
                AND points_log.status = "approved" 
                AND rules.type = "violation"
            ), 0) as total_violation_points')
            ->orderByDesc('total_violation_points')
            ->get()
            ->filter(fn($s) => $s->total_violation_points >= $minThreshold);

        // Get last violation for each student
        $lastViolations = PointsLog::withoutGlobalScopes()
            ->with('rule:id,name,type,category')
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->whereIn('student_id', $students->pluck('id'))
            ->orderByDesc('occurred_at')
            ->get()
            ->groupBy('student_id')
            ->map(fn($logs) => $logs->first());

        // Violation counts
        $violationCounts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->whereIn('student_id', $students->pluck('id'))
            ->select('student_id', DB::raw('COUNT(*) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        // Show thresholds info
        $row += 2;
        $sheet->setCellValue('A' . $row, 'KONFIGURASI THRESHOLD');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':C' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Min. Poin');
        $sheet->setCellValue('B' . $row, 'Tindakan');
        $sheet->setCellValue('C' . $row, 'Keterangan');
        $this->applyHeaderRow($sheet, 'A' . $row . ':C' . $row);

        foreach ($thresholds as $ti => $t) {
            $row++;
            $sheet->setCellValue('A' . $row, $t->min_points);
            $sheet->setCellValue('B' . $row, $t->action);
            $sheet->setCellValue('C' . $row, $t->description ?? '-');
            $this->applyDataRow($sheet, 'A' . $row . ':C' . $row, $ti % 2 === 0);
        }

        // Students list
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DAFTAR SISWA');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':H' . $row);

        $row++;
        $headers = ['No', 'Nama Siswa', 'Kelas', 'Wali Kelas', 'Jml. Pelanggaran', 'Total Poin', 'Pelanggaran Terakhir', 'Tanggal Terakhir'];
        foreach ($headers as $ci => $header) {
            $col = chr(65 + $ci);
            $sheet->setCellValue($col . $row, $header);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':H' . $row);
        $headerRow = $row;

        $idx = 0;
        foreach ($students as $s) {
            $row++;
            $idx++;
            $lastViolation = $lastViolations[$s->id] ?? null;

            // Determine threshold action
            $action = '';
            foreach ($thresholds as $t) {
                if ($s->total_violation_points >= $t->min_points) {
                    $action = $t->action;
                    break;
                }
            }

            $sheet->setCellValue('A' . $row, $idx);
            $sheet->setCellValue('B' . $row, $s->name);
            $sheet->setCellValue('C' . $row, $s->currentClass->name ?? '-');
            $sheet->setCellValue('D' . $row, $s->currentClass?->homeroomTeacher?->name ?? '-');
            $sheet->setCellValue('E' . $row, (int) ($violationCounts[$s->id] ?? 0));
            $sheet->setCellValue('F' . $row, $s->total_violation_points);
            $sheet->setCellValue('G' . $row, $lastViolation?->rule?->name ?? '-');
            $sheet->setCellValue('H' . $row, $lastViolation?->occurred_at?->format('d/m/Y H:i') ?? '-');

            // Color-code by severity
            $bgColor = null;
            if ($thresholds->isNotEmpty() && $s->total_violation_points >= $thresholds->first()->min_points) {
                $bgColor = self::DANGER_BG;
            } else {
                $bgColor = self::WARNING_BG;
            }
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($bgColor);
            $this->applyBordersToRange($sheet, 'A' . $row . ':H' . $row);
        }

        if ($idx === 0) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Tidak ada siswa yang melewati threshold.');
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF22C55E'));
        }

        $sheet->freezePane('A' . ($headerRow + 1));
        $this->autoSizeColumns($sheet, 'A', 'H');
    }

    // ── Sheet 4: Per Kelas ──────────────────────────────────

    private function buildPerKelasSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Per Kelas');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'LAPORAN PER KELAS');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':G' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Rekap poin siswa dikelompokkan per kelas');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        // Load all classes with students and teacher
        $classes = SchoolClass::withoutGlobalScopes()
            ->with(['students', 'homeroomTeacher'])
            ->where('school_id', $this->schoolId)
            ->orderBy('name')
            ->get();

        // Batch-fetch all violation/achievement points
        $allStudentIds = $classes->flatMap(fn($c) => $c->students->pluck('id'));

        $violationPts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->whereIn('student_id', $allStudentIds)
            ->select('student_id', DB::raw('SUM(points) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $achievementPts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'achievement'))
            ->whereIn('student_id', $allStudentIds)
            ->select('student_id', DB::raw('SUM(points) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $violationCounts = PointsLog::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->where('status', 'approved')
            ->whereHas('rule', fn($q) => $q->where('type', 'violation'))
            ->whereIn('student_id', $allStudentIds)
            ->select('student_id', DB::raw('COUNT(*) as total'))
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        foreach ($classes as $class) {
            $row += 2;

            // Class header
            $classViolationTotal = $class->students->sum(fn($s) => (int) ($violationPts[$s->id] ?? 0));
            $classAchievementTotal = $class->students->sum(fn($s) => (int) ($achievementPts[$s->id] ?? 0));

            $sheet->setCellValue('A' . $row, 'KELAS: ' . $class->name);
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $this->applySectionHeaderStyle($sheet, 'A' . $row . ':G' . $row);

            $row++;
            $waliKelas = $class->homeroomTeacher?->name ?? 'Belum ditentukan';
            $sheet->setCellValue('A' . $row, 'Wali Kelas: ' . $waliKelas);
            $sheet->setCellValue('C' . $row, 'Total Pelanggaran: ' . $classViolationTotal . ' poin');
            $sheet->setCellValue('E' . $row, 'Total Prestasi: ' . $classAchievementTotal . ' poin');
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setSize(9)->setItalic(true);

            $row++;
            $headers = ['No', 'NISN', 'Nama Siswa', 'Jml. Pelanggaran', 'Poin Pelanggaran', 'Poin Prestasi', 'Nett Poin'];
            foreach ($headers as $ci => $header) {
                $col = chr(65 + $ci);
                $sheet->setCellValue($col . $row, $header);
            }
            $this->applyHeaderRow($sheet, 'A' . $row . ':G' . $row);

            $sortedStudents = $class->students->sortBy('name')->values();

            if ($sortedStudents->isEmpty()) {
                $row++;
                $sheet->setCellValue('A' . $row, 'Tidak ada siswa di kelas ini');
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF94A3B8'));
                continue;
            }

            foreach ($sortedStudents as $si => $student) {
                $row++;
                $vPts = (int) ($violationPts[$student->id] ?? 0);
                $aPts = (int) ($achievementPts[$student->id] ?? 0);
                $vCount = (int) ($violationCounts[$student->id] ?? 0);

                $sheet->setCellValue('A' . $row, $si + 1);
                $sheet->setCellValue('B' . $row, $student->nisn);
                $sheet->setCellValue('C' . $row, $student->name);
                $sheet->setCellValue('D' . $row, $vCount);
                $sheet->setCellValue('E' . $row, $vPts);
                $sheet->setCellValue('F' . $row, $aPts);
                $sheet->setCellValue('G' . $row, $aPts - $vPts);
                $this->applyDataRow($sheet, 'A' . $row . ':G' . $row, $si % 2 === 0);

                // Highlight high violators in red
                if ($vPts >= 50) {
                    $sheet->getStyle('E' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
                }
            }

            // Summary row
            $row++;
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('C' . $row, 'TOTAL KELAS');
            $sheet->setCellValue('D' . $row, $class->students->sum(fn($s) => (int) ($violationCounts[$s->id] ?? 0)));
            $sheet->setCellValue('E' . $row, $classViolationTotal);
            $sheet->setCellValue('F' . $row, $classAchievementTotal);
            $sheet->setCellValue('G' . $row, $classAchievementTotal - $classViolationTotal);
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB(self::SUBHEADER_BG);
            $this->applyBordersToRange($sheet, 'A' . $row . ':G' . $row);
        }

        $sheet->freezePane('A4');
        $this->autoSizeColumns($sheet, 'A', 'G');
    }

    // ── Styling Helpers ─────────────────────────────────────

    private function applyTitleStyle(Worksheet $sheet, string $range): void
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF1E293B'));
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function applySectionHeaderStyle(Worksheet $sheet, string $range): void
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF1E40AF'));
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFF6FF'); // Blue-50
        $style->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->setColor(new Color(self::HEADER_BG));
    }

    private function applyHeaderRow(Worksheet $sheet, string $range): void
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(10)->setColor(new Color(self::HEADER_FONT));
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::HEADER_BG);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF1D4ED8'));
    }

    private function applyDataRow(Worksheet $sheet, string $range, bool $isEven): void
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setSize(10);
        if ($isEven) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC'); // Slate-50
        }
        $this->applyBordersToRange($sheet, $range);
    }

    private function applyBordersToRange(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color(self::BORDER_COLOR));
    }

    private function autoSizeColumns(Worksheet $sheet, string $fromCol, string $toCol): void
    {
        $from = ord($fromCol);
        $to = ord($toCol);
        for ($i = $from; $i <= $to; $i++) {
            $sheet->getColumnDimension(chr($i))->setAutoSize(true);
        }
    }
}
