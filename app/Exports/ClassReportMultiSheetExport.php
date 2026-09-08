<?php

namespace App\Exports;

use App\Models\PointsLog;
use App\Models\RuleThreshold;
use App\Models\SchoolClass;
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

class ClassReportMultiSheetExport
{
    protected SchoolClass $schoolClass;
    protected array $stats;
    protected int $schoolId;

    private const HEADER_BG = 'FF2563EB';
    private const HEADER_FONT = 'FFFFFFFF';
    private const SUBHEADER_BG = 'FFF1F5F9';
    private const BORDER_COLOR = 'FFE2E8F0';

    public function __construct(SchoolClass $schoolClass, array $stats)
    {
        $this->schoolClass = $schoolClass->load(['homeroomTeacher', 'academicYear']);
        $this->stats = $stats;
        $this->schoolId = $schoolClass->school_id;
    }

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(website_name())
            ->setTitle('Laporan Kelas - ' . $this->schoolClass->name);

        $this->buildRingkasanSheet($spreadsheet);
        $this->buildDaftarSiswaSheet($spreadsheet);
        $this->buildSiswaBeriskoSheet($spreadsheet);

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

    // ── Sheet 1: Ringkasan Kelas ────────────────────────────

    private function buildRingkasanSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Kelas');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'LAPORAN KELAS: ' . $this->schoolClass->name);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':E' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, website_name());
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        $row++;
        $sheet->setCellValue('A' . $row, 'Diekspor pada: ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(9)->setItalic(true)->setColor(new Color('FF94A3B8'));

        // ── Informasi Kelas ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'INFORMASI KELAS');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $classInfo = [
            ['Nama Kelas', $this->schoolClass->name],
            ['Wali Kelas', $this->schoolClass->homeroomTeacher?->name ?? 'Belum ditentukan'],
            ['Tahun Ajaran', $this->schoolClass->academicYear?->displayLabel() ?? '-'],
            ['Jumlah Siswa', $this->stats['total_students']],
        ];

        foreach ($classInfo as $i => $data) {
            $row++;
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('B' . $row)->getFont()->setSize(10);
            $this->applyDataRow($sheet, 'A' . $row . ':B' . $row, $i % 2 === 0);
        }

        // ── Statistik Kelas ──
        $totalViolationPts = $this->stats['total_violation_points'];
        $totalStudentsWithViolations = $this->stats['students_with_violations'];
        $avgViolation = $this->stats['total_students'] > 0
            ? round($totalViolationPts / $this->stats['total_students'], 1) : 0;

        $totalAchievementPts = 0;
        foreach ($this->stats['students'] as $s) {
            $totalAchievementPts += $s['total_achievement_points'];
        }

        $row += 2;
        $sheet->setCellValue('A' . $row, 'STATISTIK KELAS');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Metrik');
        $sheet->setCellValue('B' . $row, 'Nilai');
        $this->applyHeaderRow($sheet, 'A' . $row . ':B' . $row);

        $statsData = [
            ['Total Poin Pelanggaran Kelas', $totalViolationPts],
            ['Total Poin Prestasi Kelas', $totalAchievementPts],
            ['Siswa Terlibat Pelanggaran', $totalStudentsWithViolations . ' dari ' . $this->stats['total_students']],
            ['Rata-rata Poin Pelanggaran / Siswa', $avgViolation],
            ['Nett Poin Kelas', $totalAchievementPts - $totalViolationPts],
        ];

        foreach ($statsData as $i => $data) {
            $row++;
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $this->applyDataRow($sheet, 'A' . $row . ':B' . $row, $i % 2 === 0);
        }

        // ── Top 5 Pelanggaran Terbanyak ──
        $studentsSorted = collect($this->stats['students'])
            ->sortByDesc('total_violation_points')
            ->filter(fn($s) => $s['total_violation_points'] > 0)
            ->take(5)
            ->values();

        if ($studentsSorted->isNotEmpty()) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'TOP 5 SISWA POIN PELANGGARAN TERTINGGI');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

            $row++;
            $topHeaders = ['Ranking', 'NISN', 'Nama', 'Poin Pelanggaran', 'Status'];
            foreach ($topHeaders as $ci => $h) {
                $sheet->setCellValue(chr(65 + $ci) . $row, $h);
            }
            $this->applyHeaderRow($sheet, 'A' . $row . ':E' . $row);

            foreach ($studentsSorted as $i => $s) {
                $row++;
                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $s['nisn']);
                $sheet->setCellValue('C' . $row, $s['name']);
                $sheet->setCellValue('D' . $row, $s['total_violation_points']);
                $sheet->setCellValue('E' . $row, $s['action_required'] ?? 'Aman');
                $this->applyDataRow($sheet, 'A' . $row . ':E' . $row, $i % 2 === 0);

                if ($s['action_required']) {
                    $sheet->getStyle('E' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
                }
            }
        }

        $sheet->freezePane('A5');
        $this->autoSizeColumns($sheet, 'A', 'E');
    }

    // ── Sheet 2: Daftar Siswa Lengkap ───────────────────────

    private function buildDaftarSiswaSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Daftar Siswa');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'DAFTAR SISWA — KELAS ' . $this->schoolClass->name);
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':G' . $row);

        $row += 2;
        $headers = ['No', 'NISN', 'Nama Siswa', 'Poin Pelanggaran', 'Poin Prestasi', 'Nett Poin', 'Keterangan / Tindakan'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . $row, $h);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':G' . $row);

        $students = collect($this->stats['students'])->sortBy('name')->values();

        foreach ($students as $i => $s) {
            $row++;
            $nett = $s['total_achievement_points'] - $s['total_violation_points'];

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $s['nisn']);
            $sheet->setCellValue('C' . $row, $s['name']);
            $sheet->setCellValue('D' . $row, $s['total_violation_points']);
            $sheet->setCellValue('E' . $row, $s['total_achievement_points']);
            $sheet->setCellValue('F' . $row, $nett);
            $sheet->setCellValue('G' . $row, $s['action_required'] ?? 'Aman');
            $this->applyDataRow($sheet, 'A' . $row . ':G' . $row, $i % 2 === 0);

            if ($s['action_required']) {
                $sheet->getStyle('G' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
            } else {
                $sheet->getStyle('G' . $row)->getFont()->setColor(new Color('FF16A34A'));
            }

            // Highlight high violation points
            if ($s['total_violation_points'] >= 50) {
                $sheet->getStyle('D' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
            }
        }

        // Total row
        $row++;
        $totalVP = collect($students)->sum('total_violation_points');
        $totalAP = collect($students)->sum('total_achievement_points');
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('C' . $row, 'TOTAL');
        $sheet->setCellValue('D' . $row, $totalVP);
        $sheet->setCellValue('E' . $row, $totalAP);
        $sheet->setCellValue('F' . $row, $totalAP - $totalVP);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_BG);
        $this->applyBordersToRange($sheet, 'A' . $row . ':G' . $row);

        if ($students->isEmpty()) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Tidak ada siswa di kelas ini.');
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF94A3B8'));
        }

        $sheet->freezePane('A4');
        $this->autoSizeColumns($sheet, 'A', 'G');
    }

    // ── Sheet 3: Siswa Berisiko ─────────────────────────────

    private function buildSiswaBeriskoSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Siswa Berisiko');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'SISWA BERISIKO — KELAS ' . $this->schoolClass->name);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Siswa yang memerlukan tindakan berdasarkan threshold poin');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF64748B'));

        $atRisk = collect($this->stats['students'])->filter(fn($s) => $s['action_required'] !== null)
            ->sortByDesc('total_violation_points')->values();

        // Threshold info
        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->orderByDesc('min_points')
            ->get();

        if ($thresholds->isNotEmpty()) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'THRESHOLD YANG BERLAKU');
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
        }

        // At-risk students
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DAFTAR SISWA BERISIKO');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':F' . $row);

        $row++;
        $headers = ['No', 'NISN', 'Nama Siswa', 'Poin Pelanggaran', 'Poin Prestasi', 'Tindakan yang Diperlukan'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . $row, $h);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        if ($atRisk->isEmpty()) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Tidak ada siswa berisiko di kelas ini. Semua siswa dalam kondisi aman.');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF16A34A'));
        } else {
            foreach ($atRisk as $i => $s) {
                $row++;
                $sheet->setCellValue('A' . $row, $i + 1);
                $sheet->setCellValue('B' . $row, $s['nisn']);
                $sheet->setCellValue('C' . $row, $s['name']);
                $sheet->setCellValue('D' . $row, $s['total_violation_points']);
                $sheet->setCellValue('E' . $row, $s['total_achievement_points']);
                $sheet->setCellValue('F' . $row, $s['action_required']);

                // Red/orange background by severity
                $bgColor = ($thresholds->isNotEmpty() && $s['total_violation_points'] >= $thresholds->first()->min_points)
                    ? 'FFFEF2F2' : 'FFFFF7ED';
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
                $this->applyBordersToRange($sheet, 'A' . $row . ':F' . $row);
                $sheet->getStyle('F' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
            }
        }

        $sheet->freezePane('A4');
        $this->autoSizeColumns($sheet, 'A', 'F');
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
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFF6FF');
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
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
        }
        $this->applyBordersToRange($sheet, $range);
    }

    private function applyBordersToRange(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color(self::BORDER_COLOR));
    }

    private function autoSizeColumns(Worksheet $sheet, string $fromCol, string $toCol): void
    {
        for ($i = ord($fromCol); $i <= ord($toCol); $i++) {
            $sheet->getColumnDimension(chr($i))->setAutoSize(true);
        }
    }
}
