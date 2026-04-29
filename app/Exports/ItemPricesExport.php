<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ItemPricesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('item_prices')
            ->leftJoin(DB::raw('(SELECT finished_item_code, MAX(item_id) as item_id FROM stock_entry_items WHERE deleted_at IS NULL GROUP BY finished_item_code) as sei'), 'item_prices.finished_item_code', '=', 'sei.finished_item_code')
            ->leftJoin('items', 'sei.item_id', '=', 'items.id')
            ->whereNull('item_prices.deleted_at')
            ->select(
                'item_prices.finished_item_code',
                'item_prices.art_no',
                'item_prices.unit_price',
                'item_prices.selling_price',
                'item_prices.effective_from'
            )
            ->orderBy('item_prices.id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ITEM CODE',
            'UOM',
            'ART NO',
            'UNIT PRICE',
            'RETAIL PRICE',
            'EFFECTIVE DATE',
        ];
    }

    public function map($row): array
    {
        return [
            $row->finished_item_code,
            'PCS',
            $row->art_no ?? '-',
            number_format($row->unit_price, 2, '.', ''),
            number_format($row->selling_price, 2, '.', ''),
            $row->effective_from ? Carbon::parse($row->effective_from)->format('d-m-Y') : '-',
        ];
    }
}
