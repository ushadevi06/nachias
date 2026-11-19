<?php

namespace App\Exports;

use App\Models\OpenStockEntryLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OpenStockLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    protected $count = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        OpenStockEntryLog::ensureTableExists();

        $query = OpenStockEntryLog::with([
            'brand',
            'warehouse',
            'storeLocation',
            'style',
            'creator'
        ])->orderBy('id', 'desc');

        if (!empty($this->filters['brand_id'])) {
            $matchingEntryNos = OpenStockEntryLog::where('brand_id', $this->filters['brand_id'])->distinct()->pluck('entry_no')->all();
            $query->whereIn('entry_no', !empty($matchingEntryNos) ? $matchingEntryNos : ['__NONE__']);
        }

        if (!empty($this->filters['warehouse_id'])) {
            $query->where('warehouse_id', $this->filters['warehouse_id']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('entry_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('entry_date', '<=', $this->filters['to_date']);
        }

        if (!empty($this->filters['search'])) {
            $term = trim($this->filters['search']);
            $query->where(function ($q) use ($term) {
                $q->where('entry_no', 'like', "%{$term}%")
                  ->orWhere('art_no', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('product_name', 'like', "%{$term}%")
                  ->orWhere('size', 'like', "%{$term}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Date',
            'Entry No',
            'Brand',
            'Product Name',
            'Art No',
            'Style',
            'Size',
            'Sleeve',
            'Qty (Pcs)',
            'MRP (Rs)',
            'WSP (Rs)',
            'Store Location',
            'Warehouse',
            'SKU / Barcode',
            'Remarks',
            'Header Remarks',
            'Logged By',
            'Logged At',
        ];
    }

    public function map($row): array
    {
        $this->count++;
        return [
            $this->count,
            $row->entry_date ? $row->entry_date->format('d-m-Y') : '',
            $row->entry_no ?? '',
            $row->brand->brand_name ?? ($row->brand->name ?? '-'),
            $row->product_name ?? '',
            $row->art_no ?? '',
            $row->style->style_name ?? ($row->style->name ?? '-'),
            $row->size ?? '',
            $row->sleeve_type ?? '',
            (float) ($row->qty ?? 0),
            $row->mrp !== null && $row->mrp > 0 ? (float) $row->mrp : ($row->price ? round((float)$row->price * 1.5, 2) : ''),
            $row->price !== null ? (float) $row->price : '',
            $row->storeLocation->store_location_name ?? '-',
            $row->warehouse->warehouse_name ?? '-',
            $row->sku ?? '',
            $row->remarks ?? '',
            $row->header_remarks ?? '',
            $row->creator->name ?? ($row->creator->username ?? 'Admin'),
            $row->created_at ? $row->created_at->format('d-m-Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6F42C1'], // Nachias ERP purple
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

