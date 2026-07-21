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
        $prices = DB::table('item_prices')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $grouped = [];
        foreach ($prices as $price) {
            $key = $price->finished_item_code . '|' . ($price->art_no ?? '') . '|' . $price->effective_from;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'finished_item_code' => $price->finished_item_code,
                    'art_no' => $price->art_no,
                    'effective_from' => $price->effective_from,
                    'status' => $price->status,
                    'sizes' => []
                ];
            }
            $grouped[$key]['sizes'][$price->size] = [
                'unit_price' => $price->unit_price,
                'selling_price' => $price->selling_price
            ];
        }

        return collect(array_values($grouped));
    }

    public function headings(): array
    {
        return [
            'Finished Item Code',
            'Art No',
            'MRP 36',
            'Selling Price 36',
            'MRP 38',
            'Selling Price 38',
            'MRP 40',
            'Selling Price 40',
            'MRP 42',
            'Selling Price 42',
            'MRP 44',
            'Selling Price 44',
            'MRP 46',
            'Selling Price 46',
            'MRP 48',
            'Selling Price 48',
            'MRP 50',
            'Selling Price 50',
            'Effective From',
            'Status',
        ];
    }

    public function map($row): array
    {
        $sizes = ['36', '38', '40', '42', '44', '46', '48', '50'];
        
        $mapped = [
            $row['finished_item_code'],
            $row['art_no'] ?? '-',
        ];

        foreach ($sizes as $size) {
            if (isset($row['sizes'][$size])) {
                $mapped[] = number_format($row['sizes'][$size]['unit_price'], 2, '.', '');
                $mapped[] = number_format($row['sizes'][$size]['selling_price'], 2, '.', '');
            } else {
                $mapped[] = '';
                $mapped[] = '';
            }
        }

        $mapped[] = $row['effective_from'] ? Carbon::parse($row['effective_from'])->format('d-m-Y') : '-';
        $mapped[] = $row['status'] ?? 'Active';

        return $mapped;
    }
}
