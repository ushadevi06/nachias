<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exports Purchase Fabric Store stock in a Tally-style 3-level hierarchy
 * (Brand -> Width + Brand + Style -> Art No + Supplier) with Closing Balance (Qty, Rate, Value).
 */
class FabricRawStockExport implements FromArray, WithTitle, WithStyles, WithEvents
{
    protected array $tree;
    protected array $meta;
    protected array $sheetRows = [];
    protected array $rowMeta = [];
    protected int $lastDataRow = 6;

    protected const HEADER_ROW_COUNT = 6;

    public function __construct(array $tree, array $meta)
    {
        $this->tree = $tree;
        $this->meta = $meta;
        $this->buildSheetRows();
    }

    protected function sumFormulaFromRows(string $col, array $rows): string
    {
        if (empty($rows)) {
            return '0';
        }
        $terms = array_map(fn(int $r) => $col . $r, $rows);
        return '=' . implode('+', $terms);
    }

    protected function rateFormula(int $row): string
    {
        return "=IF(B{$row}>0,D{$row}/B{$row},0)";
    }

    protected function buildSheetRows(): void
    {
        $emptyRow = ['', '', '', ''];

        // Banner rows (1..4)
        $r1 = [$this->meta['report_title'] ?? 'RAW FABRIC', '', '', ''];
        $r2 = [$this->meta['company_name'] ?? 'NACHIAS FASHION PRIVATE LIMITED', '', '', ''];
        $r3 = [$this->meta['filter_summary'] ?? '', '', '', ''];
        $r4 = $emptyRow;

        // Table header rows (5..6)
        $r5 = ['Particulars', 'Closing Balance', '', ''];
        $r6 = ['', 'Quantity', 'Rate', 'Value'];

        $this->sheetRows = [$r1, $r2, $r3, $r4, $r5, $r6];
        $currentRow = self::HEADER_ROW_COUNT;

        if (empty($this->tree)) {
            $currentRow++;
            $this->sheetRows[] = ['No fabric stock found matching the selected filters.', '', '', ''];
            $this->rowMeta[$currentRow] = ['type' => 'empty'];
            $this->lastDataRow = $currentRow;
            return;
        }

        $brandRowIndices = [];

        foreach ($this->tree as $brandName => $subGroups) {
            $currentRow++;
            $brandRowIdx = $currentRow;
            $brandRowIndices[] = $brandRowIdx;

            $this->sheetRows[] = $emptyRow;
            $this->rowMeta[$brandRowIdx] = ['type' => 'brand'];
            $subGroupRowIndices = [];

            foreach ($subGroups as $subGroupName => $items) {
                $currentRow++;
                $subRowIdx = $currentRow;
                $subGroupRowIndices[] = $subRowIdx;

                $this->sheetRows[] = $emptyRow;
                $this->rowMeta[$subRowIdx] = ['type' => 'subgroup'];

                $artStartRow = $currentRow + 1;
                foreach ($items as $item) {
                    $currentRow++;
                    $this->sheetRows[] = [
                        $item['particulars'],
                        floatval($item['qty'] ?? 0),
                        $this->rateFormula($currentRow),
                        floatval($item['value'] ?? 0),
                    ];
                    $this->rowMeta[$currentRow] = ['type' => 'art'];
                }
                $artEndRow = $currentRow;

                $this->sheetRows[$subRowIdx - 1] = [
                    $subGroupName,
                    "=SUM(B{$artStartRow}:B{$artEndRow})",
                    $this->rateFormula($subRowIdx),
                    "=SUM(D{$artStartRow}:D{$artEndRow})",
                ];
            }

            $this->sheetRows[$brandRowIdx - 1] = [
                $brandName,
                $this->sumFormulaFromRows('B', $subGroupRowIndices),
                $this->rateFormula($brandRowIdx),
                $this->sumFormulaFromRows('D', $subGroupRowIndices),
            ];
        }

        // Grand Total row
        $currentRow++;
        $grandRowIdx = $currentRow;
        $this->sheetRows[] = [
            'GRAND TOTAL',
            $this->sumFormulaFromRows('B', $brandRowIndices),
            $this->rateFormula($grandRowIdx),
            $this->sumFormulaFromRows('D', $brandRowIndices),
        ];
        $this->rowMeta[$grandRowIdx] = ['type' => 'grand_total'];
        $this->lastDataRow = $grandRowIdx;
    }

    public function array(): array
    {
        return $this->sheetRows;
    }

    public function title(): string
    {
        return 'RAW FABRIC';
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
                $lastRow = $this->lastDataRow;

                $sheet->setShowSummaryBelow(false);
                $sheet->freezePane('A7');
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // Banner merges & styling
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');

                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:D2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(8);

                // Table header merges (Rows 5..6)
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:D5');

                $sheet->getStyle('A5:D6')->applyFromArray([
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
                    $sheet->mergeCells('A7:D7');
                    $sheet->getStyle('A7:D7')->applyFromArray([
                        'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FF64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension(7)->setRowHeight(24);
                    $sheet->getColumnDimension('A')->setWidth(42);
                } else {
                    $sheet->getDefaultRowDimension()->setRowHeight(20);

                    // Uniform Calibri 11pt base style across all body rows
                    $sheet->getStyle("A7:D{$lastRow}")->applyFromArray([
                        'font' => ['size' => 11, 'color' => ['argb' => 'FF0F172A']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                        ],
                    ]);
                    $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setIndent(2);

                    $sheet->getStyle("B7:B{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00 "mtr"');
                    $sheet->getStyle("C7:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("B7:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $maxParticularLen = 36;
                    foreach ($this->rowMeta as $rowNum => $info) {
                        $type = $info['type'];
                        $particularVal = (string) ($this->sheetRows[$rowNum - 1][0] ?? '');

                        if ($type === 'art') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 8);
                            $sheet->getRowDimension($rowNum)->setOutlineLevel(2);
                        } elseif ($type === 'subgroup') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 6);
                            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
                                'font' => ['bold' => true, 'italic' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF4F7FC']],
                            ]);
                            $sheet->getStyle("A{$rowNum}")->getAlignment()->setIndent(1);
                            $sheet->getRowDimension($rowNum)->setOutlineLevel(1)->setRowHeight(21);
                        } elseif ($type === 'brand') {
                            $maxParticularLen = max($maxParticularLen, strlen($particularVal) + 4);
                            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
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
                            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
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

                    $sheet->getColumnDimension('A')->setWidth(min(65, max(38, $maxParticularLen)));
                }

                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(20);

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
            },
        ];
    }
}
