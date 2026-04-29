<?php

namespace App\Exports;

use App\Models\StockEntryItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BarcodeExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return StockEntryItem::with(['color'])
            ->whereHas('stockEntry', function($q) {
                $q->where('entry_type', 'Finished Goods');
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
        
        return [
            $item->art_no, 
            $item->size ?? '-', 
            $item->color->color_name ?? '-', 
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
