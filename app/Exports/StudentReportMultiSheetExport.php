<?php

namespace App\Exports;

use App\Models\PointsLog;
use App\Models\RuleThreshold;
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

class StudentReportMultiSheetExport
{
    protected Student $student;
    protected int $schoolId;

    private const HEADER_BG = 'FF2563EB';
    private const HEADER_FONT = 'FFFFFFFF';
    private const SUBHEADER_BG = 'FFF1F5F9';
    private const BORDER_COLOR = 'FFE2E8F0';

    public function __construct(Student $student)
    {
        $this->student = $student->load(['currentClass.homeroomTeacher', 'school']);
        $this->schoolId = $student->school_id;
    }

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator(website_name())
            ->setTitle('Laporan Siswa - ' . $this->student->name);

        $this->buildProfilSheet($spreadsheet);
        $this->buildRiwayatSheet($spreadsheet);
        $this->buildRekapSheet($spreadsheet);

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

    // ── Sheet 1: Profil & Ringkasan ─────────────────────────

    private function buildProfilSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Profil & Ringkasan');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'LAPORAN POIN SISWA');
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

        // ── Identitas Siswa ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'IDENTITAS SISWA');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $profileData = [
            ['Nama Lengkap', $this->student->name],
            ['NISN', $this->student->nisn],
            ['Kelas', $this->student->currentClass->name ?? '-'],
            ['Wali Kelas', $this->student->currentClass?->homeroomTeacher?->name ?? '-'],
            ['Tanggal Lahir', $this->student->birth_date ? $this->student->birth_date->format('d M Y') : '-'],
        ];

        foreach ($profileData as $i => $data) {
            $row++;
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('B' . $row)->getFont()->setSize(10);
            $this->applyDataRow($sheet, 'A' . $row . ':B' . $row, $i % 2 === 0);
        }

        // ── Ringkasan Poin ──
        $allLogs = PointsLog::withoutGlobalScopes()
            ->with('rule:id,name,type,category')
            ->where('student_id', $this->student->id)
            ->where('status', 'approved')
            ->orderByDesc('occurred_at')
            ->get();

        $violations = $allLogs->filter(fn($l) => $l->rule->type->value === 'violation');
        $achievements = $allLogs->filter(fn($l) => $l->rule->type->value === 'achievement');

        $row += 2;
        $sheet->setCellValue('A' . $row, 'RINGKASAN POIN');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Metrik');
        $sheet->setCellValue('B' . $row, 'Nilai');
        $this->applyHeaderRow($sheet, 'A' . $row . ':B' . $row);

        $summaryData = [
            ['Total Pelanggaran', $violations->count()],
            ['Total Poin Pelanggaran', (int) $violations->sum('points')],
            ['Total Prestasi', $achievements->count()],
            ['Total Poin Prestasi', (int) $achievements->sum('points')],
            ['Nett Poin', (int) $achievements->sum('points') - (int) $violations->sum('points')],
        ];

        foreach ($summaryData as $i => $data) {
            $row++;
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $this->applyDataRow($sheet, 'A' . $row . ':B' . $row, $i % 2 === 0);
        }

        // ── Status Threshold ──
        $thresholds = RuleThreshold::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->orderByDesc('min_points')
            ->get();

        $totalViolationPts = (int) $violations->sum('points');
        $currentAction = 'Aman (tidak melewati threshold)';
        foreach ($thresholds as $t) {
            if ($totalViolationPts >= $t->min_points) {
                $currentAction = $t->action . ' (threshold: ' . $t->min_points . ' poin)';
                break;
            }
        }

        $row += 2;
        $sheet->setCellValue('A' . $row, 'STATUS KEDISIPLINAN');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $row++;
        $sheet->setCellValue('A' . $row, 'Status Saat Ini');
        $sheet->setCellValue('B' . $row, $currentAction);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        if ($totalViolationPts > 0 && $thresholds->isNotEmpty() && $totalViolationPts >= $thresholds->last()->min_points) {
            $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setColor(new Color('FFDC2626'));
        } else {
            $sheet->getStyle('B' . $row)->getFont()->setBold(true)->setColor(new Color('FF16A34A'));
        }

        // ── Breakdown per Kategori ──
        $row += 2;
        $sheet->setCellValue('A' . $row, 'BREAKDOWN PER KATEGORI');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $this->applySectionHeaderStyle($sheet, 'A' . $row . ':E' . $row);

        $row++;
        $headers = ['Kategori', 'Jumlah Kejadian', 'Total Poin'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . $row, $h);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':C' . $row);

        $byCategory = $violations->groupBy(fn($l) => $l->rule->category?->label() ?? 'Lainnya');
        $catIdx = 0;
        foreach (['Ringan', 'Sedang', 'Berat'] as $cat) {
            $catLogs = $byCategory->get($cat, collect());
            $row++;
            $sheet->setCellValue('A' . $row, $cat);
            $sheet->setCellValue('B' . $row, $catLogs->count());
            $sheet->setCellValue('C' . $row, (int) $catLogs->sum('points'));
            $this->applyDataRow($sheet, 'A' . $row . ':C' . $row, $catIdx % 2 === 0);
            $catIdx++;
        }

        $sheet->freezePane('A5');
        $this->autoSizeColumns($sheet, 'A', 'E');
    }

    // ── Sheet 2: Riwayat Lengkap ────────────────────────────

    private function buildRiwayatSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Riwayat Lengkap');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'RIWAYAT POIN LENGKAP — ' . $this->student->name);
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':H' . $row);

        $row += 2;
        $headers = ['No', 'Tanggal & Waktu', 'Jenis', 'Kategori', 'Nama Aturan', 'Poin', 'Dilaporkan Oleh', 'Catatan'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . $row, $h);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':H' . $row);

        $logs = PointsLog::withoutGlobalScopes()
            ->with(['rule:id,name,type,category', 'reporter:id,name'])
            ->where('student_id', $this->student->id)
            ->where('status', 'approved')
            ->orderByDesc('occurred_at')
            ->get();

        foreach ($logs as $i => $log) {
            $row++;
            $isViolation = $log->rule->type->value === 'violation';

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $log->occurred_at?->format('d/m/Y H:i') ?? '-');
            $sheet->setCellValue('C' . $row, $log->rule->type->label());
            $sheet->setCellValue('D' . $row, $log->rule->category?->label() ?? '-');
            $sheet->setCellValue('E' . $row, $log->rule->name ?? '-');
            $sheet->setCellValue('F' . $row, ($isViolation ? '-' : '+') . $log->points);
            $sheet->setCellValue('G' . $row, $log->reporter->name ?? 'Sistem');
            $sheet->setCellValue('H' . $row, $log->note ?? '-');
            $this->applyDataRow($sheet, 'A' . $row . ':H' . $row, $i % 2 === 0);

            // Color-code violations vs achievements
            if ($isViolation) {
                $sheet->getStyle('F' . $row)->getFont()->setColor(new Color('FFDC2626'));
            } else {
                $sheet->getStyle('F' . $row)->getFont()->setColor(new Color('FF16A34A'));
            }
        }

        if ($logs->isEmpty()) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Belum ada riwayat poin.');
            $sheet->mergeCells('A' . $row . ':H' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF94A3B8'));
        }

        $sheet->freezePane('A4');
        $this->autoSizeColumns($sheet, 'A', 'H');
    }

    // ── Sheet 3: Rekap Bulanan ──────────────────────────────

    private function buildRekapSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Rekap Bulanan');

        $row = 1;
        $sheet->setCellValue('A' . $row, 'REKAP BULANAN — ' . $this->student->name);
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $this->applyTitleStyle($sheet, 'A' . $row . ':F' . $row);

        $logs = PointsLog::withoutGlobalScopes()
            ->with('rule:id,type')
            ->where('student_id', $this->student->id)
            ->where('status', 'approved')
            ->orderBy('occurred_at')
            ->get();

        $row += 2;
        $headers = ['Bulan', 'Jml. Pelanggaran', 'Poin Pelanggaran', 'Jml. Prestasi', 'Poin Prestasi', 'Nett'];
        foreach ($headers as $ci => $h) {
            $sheet->setCellValue(chr(65 + $ci) . $row, $h);
        }
        $this->applyHeaderRow($sheet, 'A' . $row . ':F' . $row);

        $monthly = $logs->groupBy(fn($l) => $l->occurred_at->format('Y-m'))->sortKeys();

        $grandV = 0; $grandVP = 0; $grandA = 0; $grandAP = 0;
        $idx = 0;
        foreach ($monthly as $month => $mLogs) {
            $row++;
            $violations = $mLogs->filter(fn($l) => $l->rule->type->value === 'violation');
            $achievements = $mLogs->filter(fn($l) => $l->rule->type->value === 'achievement');
            $vCount = $violations->count();
            $vPts = (int) $violations->sum('points');
            $aCount = $achievements->count();
            $aPts = (int) $achievements->sum('points');

            $sheet->setCellValue('A' . $row, Carbon::parse($month . '-01')->format('F Y'));
            $sheet->setCellValue('B' . $row, $vCount);
            $sheet->setCellValue('C' . $row, $vPts);
            $sheet->setCellValue('D' . $row, $aCount);
            $sheet->setCellValue('E' . $row, $aPts);
            $sheet->setCellValue('F' . $row, $aPts - $vPts);
            $this->applyDataRow($sheet, 'A' . $row . ':F' . $row, $idx % 2 === 0);

            $grandV += $vCount; $grandVP += $vPts; $grandA += $aCount; $grandAP += $aPts;
            $idx++;
        }

        // Grand total row
        $row++;
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, $grandV);
        $sheet->setCellValue('C' . $row, $grandVP);
        $sheet->setCellValue('D' . $row, $grandA);
        $sheet->setCellValue('E' . $row, $grandAP);
        $sheet->setCellValue('F' . $row, $grandAP - $grandVP);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_BG);
        $this->applyBordersToRange($sheet, 'A' . $row . ':F' . $row);

        if ($logs->isEmpty()) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Belum ada data.');
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setColor(new Color('FF94A3B8'));
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
