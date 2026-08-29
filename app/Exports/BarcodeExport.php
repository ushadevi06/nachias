<?php

namespace App\Exports;

use App\Models\StockEntryItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BarcodeExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        return StockEntryItem::with(['color'])
            ->whereHas('stockEntry', function($q) use ($filters) {
                $q->where('entry_type', 'Finished Goods');

                if (!empty($filters['date'])) {
                    try {
                        $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $filters['date'])->format('Y-m-d');
                        $q->whereDate('stock_date', $stockDate);
                    } catch (\Exception $e) {}
                }
                if (!empty($filters['from_date'])) {
                    try {
                        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', $filters['from_date'])->format('Y-m-d');
                        $q->where('stock_date', '>=', $fromDate);
                    } catch (\Exception $e) {}
                }
                if (!empty($filters['to_date'])) {
                    try {
                        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $filters['to_date'])->format('Y-m-d');
                        $q->where('stock_date', '<=', $toDate);
                    } catch (\Exception $e) {}
                }
            })->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Size',
            'Color',
            'Fit',
            'Barcode*',
            'Quantity',
            'Product Combination Location',
            'Product Variation Location',
            'Status (Active / Inactive)',
        ];
    }

    public function map($item): array
    {
        $sleeveShort = $item->sleeve_type === 'Full' ? 'F/S' : ($item->sleeve_type === 'Half' ? 'H/S' : $item->sleeve_type);
        
        $colorName = $item->color->color_name ?? null;
        if (empty($colorName) || $colorName === '-') {
            $artNo = trim($item->art_no ?? '');
            if (!empty($artNo)) {
                if (str_contains($artNo, '-')) {
                    $parts = explode('-', $artNo);
                    $colorName = trim(end($parts));
                } else {
                    $colorName = 'A';
                }
            } else {
                $colorName = 'A';
            }
        }

        return [
            $item->art_no, 
            $item->size ?? '-', 
            $colorName, 
            $sleeveShort, 
            $item->sku ?? $item->barcode ?? '-', 
            number_format($item->qty_in, 0), 
            '', 
            '', 
            'Active', 
        ];
    }

    public function title(): string
    {
        return 'Barcode Export';
    }
}
