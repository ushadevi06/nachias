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
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalesInvoiceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    protected $count = 0;
    protected $lastInvoiceId = null;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SalesInvoice::with(['customer', 'brand', 'items', 'items.item'])
            ->where(function($q) {
                $q->whereNotNull('irn')
                  ->orWhere(function($q2) {
                      $q2->whereNull('irn')
                         ->whereHas('customer', function($q3) {
                             $q3->where('name', 'like', '%CASH%');
                         });
                  });
            })->orderBy('id', 'asc');

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
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('inv_date', [$startDate, $endDate]);
            } elseif (count($dates) == 1) {
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
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
            'S.NO',
            'BILL.NO',
            'DATE',
            'PARTY',
            'BUYER GSTIN',
            '',
            'Pin Code',
            'Bill To',
            'Ship To',
            'IRN',
            'Ack.No',
            'Ack.Date',
            'BILL VALUE',
            'NAME OF ITEM',
            'PRODUCT',
            'SIZE',
            'SLEEVE',
            'HSN CODE',
            'RATE',
            'QUANTITY',
            'SALES VALUE',
            'SALES DISCOUNT',
            'SALES DISCOUNT (WITHOUT BOX)',
            'SUB_TOTAL',
            'GST %',
            'OUTPUT CGST 2.5%',
            'OUTPUT SGST 2.5%',
            'OUTPUT CGST 6%',
            'OUTPUT SGST 6%',
            'OUTPUT IGST 5%',
            'OUTPUT IGST 12%',
            'TOTAL',
            'COURIER CHARGES',
            'ROUND OFF',
            'GROSS TOTAL',
        ];
    }

    public function map($item): array
    {
        $this->count++;
        $inv = $item->invoice;
        
        $customer = $inv->customer;
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
        
        $sleeveType = str_ireplace(['Fs', 'Hs', 'F/s', 'H/s', 'Full', 'Half'], ['F/S', 'H/S', 'F/S', 'H/S', 'F/S', 'H/S'], $item->sleeve_type);

        $nameOfItem = trim($item->art_no . ' ' . $mappedSize . ' ' . $sleeveType);
        
        $gstPercent = ($inv->cgst_percent + $inv->sgst_percent) ?: ($inv->igst_percent ?? 0);

        $cgst2_5 = ''; $sgst2_5 = '';
        $cgst6 = ''; $sgst6 = '';
        if ($inv->cgst_percent == 2.5) { $cgst2_5 = $inv->cgst; }
        if ($inv->sgst_percent == 2.5) { $sgst2_5 = $inv->sgst; }
        if ($inv->cgst_percent == 6) { $cgst6 = $inv->cgst; }
        if ($inv->sgst_percent == 6) { $sgst6 = $inv->sgst; }

        $igst5 = ''; $igst12 = '';
        if ($inv->igst_percent == 5) { $igst5 = $inv->igst; }
        if ($inv->igst_percent == 12) { $igst12 = $inv->igst; }

        $isFirstRow = ($this->lastInvoiceId !== $inv->id);
        $this->lastInvoiceId = $inv->id;

        return [
            $this->count,
            $inv->inv_no,
            $inv->inv_date ? $inv->inv_date->format('d-m-Y') : '',
            $customer ? $customer->name : '',
            $customer ? $customer->gst_no : '',
            '',
            $customer ? $customer->zip_code : '',
            $customer ? $customer->name : '',
            $customer ? $customer->name : '',
            $inv->irn,
            $inv->ack_no,
            $inv->ack_date ? $inv->ack_date->format('d-m-Y') : '',
            $inv->grand_total, // BILL VALUE
            $nameOfItem,
            $brandName,
            $mappedSize,
            $sleeveType,
            $inv->hsn_sac,
            $item->rate,
            $item->quantity,
            $item->amount, // SALES VALUE
            $isFirstRow ? $inv->discount : '', // SALES DISCOUNT
            $isFirstRow ? $inv->box_discount_amount : '', // SALES DISCOUNT (WITHOUT BOX)
            $inv->sub_total, 
            $gstPercent,
            $isFirstRow ? $cgst2_5 : '',
            $isFirstRow ? $sgst2_5 : '',
            $isFirstRow ? $cgst6 : '',
            $isFirstRow ? $sgst6 : '',
            $isFirstRow ? $igst5 : '',
            $isFirstRow ? $igst12 : '',
            $inv->total,
            $inv->other_charges,
            $inv->round_off,
            $inv->grand_total,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF0000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }
}
