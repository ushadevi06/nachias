<?php

namespace App\Exports;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RawMaterialStockExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $count = 0;
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        return StockEntryItem::with([
            'stockEntry.grnEntry',
            'rawMaterial.storeCategory',
            'storeCategory',
            'storeLocation',
            'uom'
        ])->whereHas('stockEntry', function($q) use ($filters) {
            $q->where('entry_type', 'Raw Material');

            if (!empty($filters['date'])) {
                try {
                    $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $filters['date'])->format('Y-m-d');
                    $q->whereDate('stock_date', $stockDate);
                } catch (\Exception $e) {}
            }
        })->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Stock Entry No',
            'Stock Date',
            'GRN No',
            'Store Category',
            'Raw Material',
            'Art No',
            'UOM',
            'Qty In',
            'Qty Out',
            'Balance Qty',
            'Store Location',
        ];
    }

    public function map($item): array
    {
        $this->count++;
        $entry = $item->stockEntry;

        return [
            $this->count,
            $entry->stock_entry_no ?? '-',
            $entry->stock_date ? $entry->stock_date->format('d-m-Y') : '-',
            $entry->grnEntry->grn_number ?? '-',
            $item->storeCategory->category_name ?? '-',
            $item->rawMaterial->name ?? '-',
            $item->art_no ?? '-',
            $item->uom->uom_code ?? '-',
            number_format($item->qty_in, 2),
            number_format($item->qty_out ?? 0, 2),
            number_format($item->qty_in - ($item->qty_out ?? 0), 2),
            $item->storeLocation->store_location ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Raw Material Stock';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD9E1F2']],
            ],
        ];
    }
}
