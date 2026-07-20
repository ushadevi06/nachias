<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use App\Models\ProductionReceiptItem;
use Carbon\Carbon;

class ProductionReceiptReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $request;
    protected $count = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = ProductionReceiptItem::with('productionReceipt')->orderBy('id', 'desc');

        if (!empty($this->request->date_range)) {
            $dates = explode(' to ', $this->request->date_range);
            $startDate = isset($dates[0]) ? \Carbon\Carbon::parse($dates[0])->format('Y-m-d') : null;
            $endDate = isset($dates[1]) ? \Carbon\Carbon::parse($dates[1])->format('Y-m-d') : $startDate;

            if ($startDate && $endDate) {
                $query->whereHas('productionReceipt', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('receipt_date', [$startDate, $endDate]);
                });
            } elseif ($startDate) {
                $query->whereHas('productionReceipt', function ($q) use ($startDate) {
                    $q->whereDate('receipt_date', $startDate);
                });
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Date',
            'Item',
            'Quantity',
            'Price',
            'Amount'
        ];
    }

    public function map($item): array
    {
        $this->count++;
        
        $date = '';
        if ($item->productionReceipt && $item->productionReceipt->receipt_date) {
            $date = Carbon::parse($item->productionReceipt->receipt_date)->format('d-m-Y');
        }

        $sizeMappings = [
            '36' => 'S',
            '38' => 'M',
            '40' => 'L',
            '42' => 'XL',
            '44' => 'XXL',
            '46' => '3XL',
            '48' => '4XL',
            '50' => '5XL',
        ];

        $rawSize = $item->size ?? '';
        $sizeChar = $sizeMappings[$rawSize] ?? '';
        $mappedSize = $rawSize . ($sizeChar ? ' ' . $sizeChar : '');
        
        $sleeveType = '';
        if (preg_match('/(F\/S|H\/S|Fs|Hs)/i', $item->item_name, $matches)) {
            $sleeveType = str_ireplace(['Fs', 'Hs', 'F/s', 'H/s'], ['F/S', 'H/S', 'F/S', 'H/S'], $matches[1]);
        }

        $nameOfItem = trim(($item->art_no ?? '') . ' ' . $mappedSize . ' ' . $sleeveType);

        return [
            $this->count,
            $date,
            $nameOfItem,
            $item->qty_to_receive ?? 0,
            $item->unit_price ?? 0,
            $item->total_value ?? 0
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
            ]
        ]);
    }
}
