<?php

namespace App\Exports;

use App\Http\Controllers\FgStockTakingController;
use App\Models\StockTakingSession;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FgStockTakingExport implements WithMultipleSheets
{
    protected StockTakingSession $session;
    protected array $state;

    public function __construct(StockTakingSession $session)
    {
        $this->session = $session->loadMissing([
            'brand',
            'storeType',
            'warehouse',
            'storeLocation',
            'starter',
            'completer',
            'items.systemStoreLocation',
            'items.storeLocation',
            'scanLogs.scanner',
            'scanLogs.storeLocation',
        ]);
        $this->state = FgStockTakingController::computeSessionItemsState($this->session);
    }

    public function sheets(): array
    {
        return [
            new FgStockTakingVarianceSheet($this->session, $this->state),
            new FgStockTakingScanLogSheet($this->session),
        ];
    }
}

class FgStockTakingVarianceSheet implements FromArray, WithTitle, WithStyles, WithEvents
{
    protected StockTakingSession $session;
    protected array $state;
    protected int $firstItemRow = 8;
    protected int $lastItemRow = 8;
    protected int $totalRow = 9;
    protected int $itemCount = 0;

    public function __construct(StockTakingSession $session, ?array $state = null)
    {
        $this->session = $session;
        $this->state = $state ?? FgStockTakingController::computeSessionItemsState($session);
        $this->itemCount = count($this->state['rows']);
        $this->lastItemRow = $this->itemCount > 0 ? ($this->firstItemRow + $this->itemCount - 1) : $this->firstItemRow;
        $this->totalRow = $this->lastItemRow + 1;
    }

    public function title(): string
    {
        return 'Variance Report';
    }

    public function array(): array
    {
        $s = $this->session;
        $stats = $this->state['stats'];
        $brandName = $s->brand?->brand_name ?? '-';
        $storeName = $s->storeType?->store_type_name ?? 'All Stores';
        $warehouseName = $s->warehouse?->warehouse_name ?? 'All Warehouses';
        $startedAt = $s->started_at ? $s->started_at->format('d-m-Y h:i A') : '-';
        $countedBy = $s->starter?->name ?? '-';
        $statusLabelTop = $s->status === 'Completed'
            ? ('Finalized (' . ($s->completed_at ? $s->completed_at->format('d-m-Y h:i A') : 'Completed') . ')')
            : 'In Progress';

        $rows = [
            ['FG STOCK TAKING - VARIANCE REPORT', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['Brand: ' . $brandName, '', 'Store / Wh: ' . $storeName . ' / ' . $warehouseName, '', 'Ref No: ' . $s->session_no, '', 'Date: ' . $startedAt, '', '', 'Counted By: ' . $countedBy, '', '', 'Status: ' . $statusLabelTop, '', '', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['SUMMARY OVERVIEW', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['Total SKUs: ' . $stats['total_skus'], '', 'System Qty', '=SUM(J' . $this->firstItemRow . ':J' . $this->lastItemRow . ')', 'Scanned Qty', '=SUM(K' . $this->firstItemRow . ':K' . $this->lastItemRow . ')', 'Remaining Qty', '=SUM(L' . $this->firstItemRow . ':L' . $this->lastItemRow . ')', 'Net Variance', '=F6-D6', 'Variance Value', '=SUM(O' . $this->firstItemRow . ':O' . $this->lastItemRow . ')', 'Scan Progress %', '=IF(D6>0,(D6-H6)/D6,0)', '', ''],
            ['S.No', 'Style', 'Art No', 'SKU', 'Sleeve', 'Size', 'Unit Rate (Rs.)', 'Snapshot Qty', 'Movement Qty', 'Effective System Qty', 'Scanned Qty', 'Remaining Qty', 'Variance Qty', 'Stock Status', 'Variance Value (Rs.)', 'Store Location'],
        ];

        $computedRows = collect($this->state['rows'])->sortBy([
            fn($a, $b) => strcmp((string) $a['model']->style_name, (string) $b['model']->style_name),
            fn($a, $b) => strcmp((string) $a['model']->art_no, (string) $b['model']->art_no),
            fn($a, $b) => strnatcasecmp((string) $a['model']->size, (string) $b['model']->size),
        ])->values();

        $allItemIds = $computedRows->pluck('model.id')->filter()->all();
        $scannedLocMap = \App\Http\Controllers\FgStockTakingController::getItemScannedLocationsMap($this->session->id, $allItemIds);

        if ($computedRows->isEmpty()) {
            $rows[] = [1, '-', '-', '-', '-', '-', 0, 0, 0, '=MAX(0,H8+I8)', 0, '=MAX(0,J8-K8)', '=K8-J8', 'No Items', '=M8*G8', '-'];
        } else {
            foreach ($computedRows as $idx => $entry) {
                $item = $entry['model'];
                $calc = $entry['calc'];
                $r = $this->firstItemRow + $idx;

                $statusLabel = match ($calc['status']) {
                    'completed' => 'Completed',
                    'excess' => 'Excess',
                    'in_progress' => 'In Progress (Shortage)',
                    'pending' => 'Pending (Shortage)',
                    default => 'Pending',
                };

                $locationDisplay = '-';
                if (floatval($calc['scanned_qty']) > 0.0001) {
                    $locationDisplay = $scannedLocMap[$item->id] ?? ($item->storeLocation?->store_location ? ($item->storeLocation->store_location . ' - ' . (int)$calc['scanned_qty']) : '-');
                }

                $masterBarcode = \App\Http\Controllers\FgStockTakingController::resolveMasterBarcode($item->art_no, $item->size, $item->sleeve_type, (int) $this->session->brand_id) ?: $item->sku;

                $rows[] = [
                    $idx + 1,
                    $item->finished_item_code ?: ($item->style_name ?: '-'),
                    $item->art_no ?: '-',
                    $masterBarcode,
                    $item->sleeve_type ?: '-',
                    $item->size ?: '-',
                    floatval($item->rate),
                    $calc['snapshot_qty'],
                    $calc['movement_qty'],
                    '=MAX(0,H' . $r . '+I' . $r . ')',
                    $calc['scanned_qty'],
                    '=MAX(0,J' . $r . '-K' . $r . ')',
                    '=K' . $r . '-J' . $r,
                    $statusLabel,
                    '=M' . $r . '*G' . $r,
                    $locationDisplay,
                ];
            }
        }

        $rows[] = [
            'TOTAL',
            '',
            '',
            $computedRows->count() . ' SKUs',
            '',
            '',
            '',
            '=SUM(H' . $this->firstItemRow . ':H' . $this->lastItemRow . ')',
            '=SUM(I' . $this->firstItemRow . ':I' . $this->lastItemRow . ')',
            '=SUM(J' . $this->firstItemRow . ':J' . $this->lastItemRow . ')',
            '=SUM(K' . $this->firstItemRow . ':K' . $this->lastItemRow . ')',
            '=SUM(L' . $this->firstItemRow . ':L' . $this->lastItemRow . ')',
            '=SUM(M' . $this->firstItemRow . ':M' . $this->lastItemRow . ')',
            '',
            '=SUM(O' . $this->firstItemRow . ':O' . $this->lastItemRow . ')',
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']]],
            5 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['rgb' => '1F4E79']]],
            6 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11]],
            7 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']]],
            $this->totalRow => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                $sheet->mergeCells('A1:P1');
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('C2:D2');
                $sheet->mergeCells('E2:F2');
                $sheet->mergeCells('G2:I2');
                $sheet->mergeCells('J2:L2');
                $sheet->mergeCells('M2:P2');
                $sheet->mergeCells('A5:P5');
                $sheet->mergeCells('A6:B6');
                $sheet->mergeCells('N6:P6');

                $sheet->getStyle('A1:P1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(6);
                $sheet->getRowDimension(4)->setRowHeight(6);

                $sheet->getStyle('A2:P2')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F5F9']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9E1EC']]],
                ]);
                $sheet->getStyle('A5:P6')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9EFF7']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B8C6D9']]],
                ]);

                $sheet->getStyle('D6')->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F6')->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('H6')->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('J6')->getNumberFormat()->setFormatCode('+#,##0.00;-#,##0.00;0.00');
                $sheet->getStyle('L6')->getNumberFormat()->setFormatCode('+#,##0.00;-#,##0.00;0.00');
                $sheet->getStyle('N6')->getNumberFormat()->setFormatCode('0.0%');

                $sheet->getStyle('A7:P7')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']]],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(24);

                $sheet->getStyle("A{$this->firstItemRow}:P{$this->lastItemRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                ]);

                $sheet->getStyle("G{$this->firstItemRow}:L{$this->totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("I{$this->firstItemRow}:I{$this->totalRow}")->getNumberFormat()->setFormatCode('+#,##0.00;-#,##0.00;0.00');
                $sheet->getStyle("M{$this->firstItemRow}:M{$this->totalRow}")->getNumberFormat()->setFormatCode('+#,##0.00;-#,##0.00;0.00');
                $sheet->getStyle("O{$this->firstItemRow}:O{$this->totalRow}")->getNumberFormat()->setFormatCode('+#,##0.00;-#,##0.00;0.00');

                $sheet->getStyle("A{$this->firstItemRow}:A{$this->lastItemRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$this->firstItemRow}:F{$this->lastItemRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("N{$this->firstItemRow}:N{$this->lastItemRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("P{$this->firstItemRow}:P{$this->lastItemRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                for ($r = $this->firstItemRow; $r <= $this->lastItemRow; $r++) {
                    $statusVal = (string) $sheet->getCell("N{$r}")->getValue();
                    if ($statusVal === 'Completed') {
                        $sheet->getStyle("N{$r}")->getFont()->getColor()->setRGB('1E7E34');
                    } elseif ($statusVal === 'Excess') {
                        $sheet->getStyle("N{$r}")->getFont()->getColor()->setRGB('B45309');
                        $sheet->getStyle("A{$r}:P{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFBEB');
                    } elseif (str_contains($statusVal, 'Shortage') || str_contains($statusVal, 'Pending')) {
                        $sheet->getStyle("N{$r}")->getFont()->getColor()->setRGB('B91C1C');
                        $sheet->getStyle("A{$r}:P{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF2F2');
                    }
                }

                $sheet->mergeCells("A{$this->totalRow}:C{$this->totalRow}");
                $sheet->getStyle("A{$this->totalRow}:P{$this->totalRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);

                $widths = [
                    'A' => 8, 'B' => 20, 'C' => 16, 'D' => 22, 'E' => 12, 'F' => 10,
                    'G' => 15, 'H' => 14, 'I' => 14, 'J' => 18, 'K' => 14, 'L' => 14,
                    'M' => 14, 'N' => 22, 'O' => 18, 'P' => 22,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $sheet->freezePane('A8');
                $sheet->setAutoFilter("A7:P{$this->lastItemRow}");
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4);
            },
        ];
    }
}

class FgStockTakingScanLogSheet implements FromArray, WithTitle, WithStyles, WithEvents
{
    protected StockTakingSession $session;
    protected int $lastRow = 4;

    public function __construct(StockTakingSession $session)
    {
        $this->session = $session;
    }

    public function title(): string
    {
        return 'Scan Audit Log';
    }

    public function array(): array
    {
        $rows = [
            ['FG STOCK TAKING - SCAN AUDIT LOG (' . ($this->session->brand?->brand_name ?? '') . ' - ' . $this->session->session_no . ')', '', '', '', '', '', '', '', '', '', ''],
            ['S.No', 'Scan Time', 'Raw Code', 'Resolved SKU', 'Detected Brand', 'Art No / Size / Sleeve', 'Store Location', 'Qty', 'Status', 'Message', 'Scanned By'],
        ];

        $logs = $this->session->scanLogs->sortByDesc('id')->values();
        if ($logs->isEmpty()) {
            $rows[] = [1, '-', '-', '-', '-', '-', '-', 0, '-', 'No scans recorded', '-'];
        } else {
            foreach ($logs as $idx => $log) {
                $desc = trim(implode(' / ', array_filter([
                    $log->art_no ?: null,
                    $log->size ?: null,
                    $log->sleeve_type ?: null,
                ])), ' /');

                $rows[] = [
                    $idx + 1,
                    $log->created_at ? $log->created_at->format('d-m-Y h:i:s A') : '-',
                    $log->raw_code,
                    $log->resolved_sku ?: '-',
                    $log->detected_brand_name ?: '-',
                    $desc ?: '-',
                    $log->storeLocation?->store_location ?? '-',
                    floatval($log->qty),
                    strtoupper((string) $log->scan_status),
                    $log->message ?: '-',
                    $log->scanner?->name ?? '-',
                ];
            }
        }

        $this->lastRow = count($rows);
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']]],
            2 => ['font' => ['name' => 'Calibri', 'bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(26);

                $sheet->getStyle('A2:K2')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                $sheet->getStyle("A2:K{$this->lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                ]);
                $sheet->getStyle("H3:H{$this->lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                $widths = [
                    'A' => 8, 'B' => 22, 'C' => 28, 'D' => 20, 'E' => 20,
                    'F' => 24, 'G' => 16, 'H' => 10, 'I' => 16, 'J' => 38, 'K' => 18,
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
                $sheet->freezePane('A3');
            },
        ];
    }
}

