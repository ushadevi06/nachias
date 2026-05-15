<?php

namespace App\Exports;

use App\Models\ProductionReceiptItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductionReceiptExport implements FromCollection, WithHeadings, WithMapping
{
    protected $count = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProductionReceiptItem::with(['color', 'productionReceipt'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Size',
            'Color',
            'Fit',
            'Barcode*',
            'Quantity (Stock In Production)*',
        ];
    }

    public function map($item): array
    {
        return [
            $item->item_name,
            $item->size,
            $item->color->color_name ?? '-',
            $item->art_no ?? '-',
            $item->item_code,
            $item->qty_to_receive,
        ];
    }
}
