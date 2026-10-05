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
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $grouped = [];
        foreach ($prices as $price) {
            $key = $price->finished_item_code . '|' . ($price->art_no ?? '');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'finished_item_code' => $price->finished_item_code,
                    'art_no' => $price->art_no,
                    'effective_from' => $price->effective_from,
                    'status' => $price->status,
                    'sizes' => []
                ];
            }

            $supportedSizes = ['36', '38', '40', '42', '44', '46', '48', '50'];

            if (!empty($price->size) && in_array((string)$price->size, $supportedSizes, true)) {
                if (!isset($grouped[$key]['sizes'][$price->size])) {
                    $grouped[$key]['sizes'][$price->size] = [
                        'unit_price' => $price->unit_price,
                        'selling_price' => $price->selling_price
                    ];
                }
            } else {
                // If size is null / empty / 'ALL', apply this common price to all standard sizes that are not yet set
                foreach ($supportedSizes as $sz) {
                    if (!isset($grouped[$key]['sizes'][$sz])) {
                        $grouped[$key]['sizes'][$sz] = [
                            'unit_price' => $price->unit_price,
                            'selling_price' => $price->selling_price
                        ];
                    }
                }
            }
        }

        // Filter out items that have no valid prices in any size
        $validGrouped = array_filter($grouped, function ($item) {
            foreach ($item['sizes'] as $sizeData) {
                if (floatval($sizeData['selling_price'] ?? 0) > 0 || floatval($sizeData['unit_price'] ?? 0) > 0) {
                    return true;
                }
            }
            return false;
        });

        return collect(array_values($validGrouped));
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
                $mrp = floatval($row['sizes'][$size]['selling_price']);
                $sp = floatval($row['sizes'][$size]['unit_price']);

                if ($mrp > 0 && $sp <= 0) {
                    $sp = round($mrp / 1.5, 2);
                }

                $mapped[] = $mrp > 0 ? number_format($mrp, 2, '.', '') : '';
                $mapped[] = $sp > 0 ? number_format($sp, 2, '.', '') : '';
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
