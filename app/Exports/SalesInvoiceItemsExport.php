<?php

namespace App\Exports;

use App\Models\SalesInvoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalesInvoiceItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    protected $count = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SalesInvoice::with(['brand', 'items', 'items.item'])->orderBy('id', 'asc');

        if (!empty($this->filters['customer_id'])) {
            $query->where('customer_id', $this->filters['customer_id']);
        }
        if (!empty($this->filters['brand_id'])) {
            $query->where('brand_id', $this->filters['brand_id']);
        }
        if (!empty($this->filters['inv_no'])) {
            $query->where('inv_no', $this->filters['inv_no']);
        }
        if (!empty($this->filters['inv_date_range'])) {
            $dates = explode(' to ', $this->filters['inv_date_range']);
            if (count($dates) == 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('inv_date', [$startDate, $endDate]);
            } elseif (count($dates) == 1) {
                $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $query->whereDate('inv_date', $startDate);
            }
        }

        $invoices = $query->get();
        $flatData = collect([]);

        foreach ($invoices as $inv) {
            foreach ($inv->items as $item) {
                $item->invoice = $inv;
                $flatData->push($item);
            }
        }

        return $flatData;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Item',
            'Barcode',
            'UOM',
            'HS Code',
            'GST %',
            'Class2',
            'Class3',
            'Class1',
            'Art',
            'Sub Group3',
        ];
    }

    public function map($item): array
    {
        $this->count++;
        $inv = $item->invoice;
        
        $brandName = $inv->brand ? $inv->brand->brand_name : '';
        
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

        $rawSize = $item->size;
        $sizeChar = $sizeMappings[$rawSize] ?? '';
        $mappedSize = $rawSize . ($sizeChar ? ' ' . $sizeChar : '');
        
        $sleeveType = str_ireplace(['Fs', 'Hs', 'F/s', 'H/s'], ['F/S', 'H/S', 'F/S', 'H/S'], $item->sleeve_type);

        $nameOfItem = trim($item->art_no . ' ' . $mappedSize . ' ' . $sleeveType);

        $gstPercent = ($inv->cgst_percent + $inv->sgst_percent) ?: ($inv->igst_percent ?? 0);

        return [
            $this->count,
            $nameOfItem,
            '', // Barcode
            'PCS', // UOM
            $inv->hsn_sac, // HS Code
            $gstPercent,
            $sleeveType, // Class2
            'FINISHED GOODS', // Class3
            $brandName . ' (FG)', // Class1
            $item->art_no, // Art
            $item->art_no . ' ' . $sleeveType, // Sub Group3
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
            ]
        ]);
        
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
