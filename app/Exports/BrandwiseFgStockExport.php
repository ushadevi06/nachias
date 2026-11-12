<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exports Brandwise Finished Goods stock in a Tally-style 3-level hierarchy
 * (Brand -> Style -> Art No + Sleeve + Size matrix + Closing Balance).
 */
class BrandwiseFgStockExport implements FromArray, WithTitle, WithStyles, WithEvents
{
    protected array $tree;
    protected array $meta;
    protected array $sizeKeys;
    protected array $sheetRows = [];
    protected array $rowMeta = [];
    protected array $decimalCells = [];
    protected int $lastDataRow = 6;

    protected const HEADER_ROW_COUNT = 6;

    public function __construct(array $tree, array $meta, bool $hasOtherSize = false)
    {
        $this->tree = $tree;
        $this->meta = $meta;
        $this->sizeKeys = ['36', '38', '40', '42', '44', '46', '48', '50'];
        if ($hasOtherSize) {
            $this->sizeKeys[] = 'other';
        }

        $this->buildSheetRows();
    }

    protected function colLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }

    protected function sumFormulaFromRows(string $col, array $rows): string
    {
        if (empty($rows)) {
            return '0';
        }
        $terms = array_map(fn(int $r) => $col . $r, $rows);
        return '=' . implode('+', $terms);
    }

    protected function rateFormula(string $qtyCol, string $valCol, int $row): string
    {
        return "=IF({$qtyCol}{$row}>0,{$valCol}{$row}/{$qtyCol}{$row},0)";
    }

    protected function emptyTotalsBucket(): array
    {
        $bucket = ['sizes' => [], 'qty' => 0.0];
        foreach ($this->sizeKeys as $sz) {
            $bucket['sizes'][$sz] = 0.0;
        }
        return $bucket;
    }

    protected function accumulateTotals(array &$target, array $source): void
    {
        foreach ($this->sizeKeys as $sz) {
            $target['sizes'][$sz] += floatval($source['sizes'][$sz] ?? 0);
        }
        $target['qty'] += floatval($source['qty'] ?? 0);
    }

    protected function trackDecimalCells(int $rowIdx, array $bucket, int $qtyColIdx): void
    {
        foreach ($this->sizeKeys as $idx => $sz) {
            $val = floatval($bucket['sizes'][$sz] ?? 0);
            if (abs($val - round($val)) > 0.0001) {
                $this->decimalCells[] = $this->colLetter(3 + $idx) . $rowIdx;
            }
        }
        $qty = floatval($bucket['qty'] ?? 0);
        if (abs($qty - round($qty)) > 0.0001) {
            $this->decimalCells[] = $this->colLetter($qtyColIdx) . $rowIdx;
        }
    }

    protected function buildSheetRows(): void
    {
        $sizeCount = count($this->sizeKeys);
        $totalCols = 2 + $sizeCount + 3; // Particulars + Sleeve + Sizes + Qty + Rate + Value
        $emptyRow = array_fill(0, $totalCols, '');

        $lastSizeCol = $this->colLetter(2 + $sizeCount);
        $qtyColIdx = 2 + $sizeCount + 1;
        $rateColIdx = $qtyColIdx + 1;
        $valColIdx = $qtyColIdx + 2;

        $qtyCol = $this->colLetter($qtyColIdx);
        $valCol = $this->colLetter($valColIdx);

        // Banner rows (1..4)
        $r1 = $emptyRow;
        $r1[0] = $this->meta['report_title'] ?? 'BRANDWISE FG STOCKS';

        $r2 = $emptyRow;
        $r2[0] = $this->meta['company_name'] ?? 'NACHIAS FASHION PRIVATE LIMITED';

        $r3 = $emptyRow;
        $r3[0] = $this->meta['filter_summary'] ?? '';

        $r4 = $emptyRow;

        // Table header row 1 (Row 5)
        $r5 = $emptyRow;
        $r5[0] = 'Particulars';
        $r5[1] = 'Sleeve';
        $r5[2] = 'Size';
        $r5[$qtyColIdx - 1] = 'Closing Balance';

        // Table header row 2 (Row 6)
        $r6 = $emptyRow;
        foreach ($this->sizeKeys as $idx => $sz) {
            $r6[2 + $idx] = $sz === 'other' ? 'Other' : $sz;
        }
        $r6[$qtyColIdx - 1] = 'Quantity';
        $r6[$rateColIdx - 1] = 'Rate';
        $r6[$valColIdx - 1] = 'Value';

        $this->sheetRows = [$r1, $r2, $r3, $r4, $r5, $r6];
        $currentRow = self::HEADER_ROW_COUNT;

        if (empty($this->tree)) {
            $currentRow++;
            $noDataRow = $emptyRow;
            $noDataRow[0] = 'No finished goods stock found matching the selected filters.';
            $this->sheetRows[] = $noDataRow;
            $this->rowMeta[$currentRow] = ['type' => 'empty'];
            $this->lastDataRow = $currentRow;
            return;
        }

        $brandRowIndices = [];
        $grandBucket = $this->emptyTotalsBucket();

        foreach ($this->tree as $brandName => $styles) {
            $currentRow++;
            $brandRowIdx = $currentRow;
            $brandRowIndices[] = $brandRowIdx;

            $this->sheetRows[] = $emptyRow;
            $this->rowMeta[$brandRowIdx] = ['type' => 'brand'];
            $styleRowIndices = [];
            $brandBucket = $this->emptyTotalsBucket();

            foreach ($styles as $styleName => $artRows) {
                $currentRow++;
                $styleRowIdx = $currentRow;
                $styleRowIndices[] = $styleRowIdx;

                $this->sheetRows[] = $emptyRow;
                $this->rowMeta[$styleRowIdx] = ['type' => 'style'];
                $styleBucket = $this->emptyTotalsBucket();

                $artStartRow = $currentRow + 1;
                foreach ($artRows as $art) {
                    $currentRow++;
                    $artBucket = $this->emptyTotalsBucket();
                    $row = [
                        $art['art_no'],
                        $art['sleeve'],
                    ];
                    foreach ($this->sizeKeys as $sz) {
                        $szQty = floatval($art['sizes'][$sz] ?? 0);
                        $artBucket['sizes'][$sz] = $szQty;
                        $artBucket['qty'] += $szQty;
                        $row[] = $szQty;
                    }
                    $row[] = "=SUM(C{$currentRow}:{$lastSizeCol}{$currentRow})";
                    $row[] = $this->rateFormula($qtyCol, $valCol, $currentRow);
                    $row[] = floatval($art['stock_value'] ?? 0);

                    $this->sheetRows[] = $row;
                    $this->rowMeta[$currentRow] = ['type' => 'art'];
                    $this->trackDecimalCells($currentRow, $artBucket, $qtyColIdx);
                    $this->accumulateTotals($styleBucket, $artBucket);
                }
                $artEndRow = $currentRow;

                $styleRow = [$styleName, ''];
                for ($c = 3; $c <= $qtyColIdx; $c++) {
                    $col = $this->colLetter($c);
                    $styleRow[] = "=SUM({$col}{$artStartRow}:{$col}{$artEndRow})";
                }
                $styleRow[] = $this->rateFormula($qtyCol, $valCol, $styleRowIdx);
                $styleRow[] = "=SUM({$valCol}{$artStartRow}:{$valCol}{$artEndRow})";

                $this->sheetRows[$styleRowIdx - 1] = $styleRow;
                $this->trackDecimalCells($styleRowIdx, $styleBucket, $qtyColIdx);
                $this->accumulateTotals($brandBucket, $styleBucket);
            }

            $brandRow = [$brandName, ''];
            for ($c = 3; $c <= $qtyColIdx; $c++) {
                $col = $this->colLetter($c);
                $brandRow[] = $this->sumFormulaFromRows($col, $styleRowIndices);
            }
            $brandRow[] = $this->rateFormula($qtyCol, $valCol, $brandRowIdx);
            $brandRow[] = $this->sumFormulaFromRows($valCol, $styleRowIndices);

            $this->sheetRows[$brandRowIdx - 1] = $brandRow;
            $this->trackDecimalCells($brandRowIdx, $brandBucket, $qtyColIdx);
            $this->accumulateTotals($grandBucket, $brandBucket);
        }

        // Grand Total row
        $currentRow++;
        $grandRowIdx = $currentRow;
        $grandRow = ['GRAND TOTAL', ''];
        for ($c = 3; $c <= $qtyColIdx; $c++) {
            $col = $this->colLetter($c);
            $grandRow[] = $this->sumFormulaFromRows($col, $brandRowIndices);
        }
        $grandRow[] = $this->rateFormula($qtyCol, $valCol, $grandRowIdx);
        $grandRow[] = $this->sumFormulaFromRows($valCol, $brandRowIndices);

        $this->sheetRows[] = $grandRow;
        $this->rowMeta[$grandRowIdx] = ['type' => 'grand_total'];
        $this->trackDecimalCells($grandRowIdx, $grandBucket, $qtyColIdx);
        $this->lastDataRow = $grandRowIdx;
    }

    public function array(): array
    {
        return $this->sheetRows;
    }

    public function title(): string
    {
        return 'Brandwise FG Stocks';
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sizeCount = count($this->sizeKeys);
                $lastSizeColIdx = 2 + $sizeCount;
                $qtyColIdx = $lastSizeColIdx + 1;
                $rateColIdx = $qtyColIdx + 1;
                $valColIdx = $qtyColIdx + 2;

                $lastSizeCol = $this->colLetter($lastSizeColIdx);
                $qtyCol = $this->colLetter($qtyColIdx);
                $rateCol = $this->colLetter($rateColIdx);
                $valCol = $this->colLetter($valColIdx);
                $lastRow = $this->lastDataRow;

                // Show outline expand/collapse (+/-) on parent Brand and Style rows above children
                $sheet->setShowSummaryBelow(false);
                $sheet->freezePane('A7');
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // Banner merges and styling
                $sheet->mergeCells("A1:{$valCol}1");
                $sheet->mergeCells("A2:{$valCol}2");
                $sheet->mergeCells("A3:{$valCol}3");

                $sheet->getStyle("A1:{$valCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A2:{$valCol}2")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A3:{$valCol}3")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(8);

                // Table header merges (Rows 5..6)
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells("C5:{$lastSizeCol}5");
                $sheet->mergeCells("{$qtyCol}5:{$valCol}5");

                $sheet->getStyle("A5:{$valCol}6")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF64748B']],
                    ],
                ]);
                $sheet->getStyle('A5:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getRowDimension(5)->setRowHeight(21);
                $sheet->getRowDimension(6)->setRowHeight(21);

                if (isset($this->rowMeta[7]) && $this->rowMeta[7]['type'] === 'empty') {
                    $sheet->mergeCells("A7:{$valCol}7");
                    $sheet->getStyle("A7:{$valCol}7")->applyFromArray([
                        'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension(7)->setRowHeight(24);
                    $sheet->getColumnDimension('A')->setWidth(32);
                } else {
                    $sheet->getDefaultRowDimension()->setRowHeight(20);

                    // Uniform Calibri 11pt base style across all body rows (matches standard Warehouse Report Excel font)
                    $sheet->getStyle("A7:{$valCol}{$lastRow}")->applyFromArray([
                        'font' => ['size' => 11, 'color' => ['argb' => 'FF0F172A']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                        ],
                    ]);
                    $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setIndent(2);

                    // Clean integer format by default (renders 0 as "-"); explicit .00 only where fractional qty exists
                    $sheet->getStyle("C7:{$qtyCol}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                    foreach ($this->decimalCells as $cellCoord) {
                        $sheet->getStyle($cellCoord)->getNumberFormat()->setFormatCode('#,##0.00;-#,##0.00;"-"');
                    }
                    $sheet->getStyle("{$rateCol}7:{$valCol}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $sheet->getStyle("C7:{$qtyCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("{$rateCol}7:{$valCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("B7:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $maxParticularLen = 24;
                    foreach ($this->rowMeta as $rowNum => $info) {
                        $type = $info['type'];
                        $particularVal = (string) ($this->sheetRows[$rowNum - 1][0] ?? '');

                        if ($type === 'art') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 12);
                            $sheet->getRowDimension($rowNum)->setOutlineLevel(2);
                        } elseif ($type === 'style') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 8);
                            $sheet->getStyle("A{$rowNum}:{$valCol}{$rowNum}")->applyFromArray([
                                'font' => ['bold' => true, 'italic' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF4F7FC']],
                            ]);
                            $sheet->getStyle("A{$rowNum}")->getAlignment()->setIndent(1);
                            $sheet->getRowDimension($rowNum)->setOutlineLevel(1)->setRowHeight(21);
                        } elseif ($type === 'brand') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 5);
                            $sheet->getStyle("A{$rowNum}:{$valCol}{$rowNum}")->applyFromArray([
                                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8EEF7']],
                                'borders' => [
                                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']],
                                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']],
                                ],
                            ]);
                            $sheet->getStyle("A{$rowNum}")->getAlignment()->setIndent(0);
                            $sheet->getRowDimension($rowNum)->setRowHeight(22);
                        } elseif ($type === 'grand_total') {
                            $sheet->getStyle("A{$rowNum}:{$valCol}{$rowNum}")->applyFromArray([
                                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                                'borders' => [
                                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF475569']],
                                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF475569']],
                                ],
                            ]);
                            $sheet->getStyle("A{$rowNum}")->getAlignment()->setIndent(0);
                            $sheet->getRowDimension($rowNum)->setRowHeight(23);
                        }
                    }

                    $sheet->getColumnDimension('A')->setWidth(min(50, max(30, $maxParticularLen)));
                }

                $sheet->getColumnDimension('B')->setWidth(10);
                for ($c = 3; $c <= $lastSizeColIdx; $c++) {
                    $sheet->getColumnDimension($this->colLetter($c))->setWidth(10);
                }
                $sheet->getColumnDimension($qtyCol)->setWidth(14);
                $sheet->getColumnDimension($rateCol)->setWidth(13);
                $sheet->getColumnDimension($valCol)->setWidth(18);

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
            },
        ];
    }
}
