<?php

namespace App\Exports;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FinishedGoodsStockExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithEvents
{
    protected $count = 0;
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        return StockEntryItem::with([
            'stockEntry.productionReceipt.jobCard.fabricType',
            'stockEntry.productionReceipt.jobCard.purchaseOrder',
            'stockEntry.productionReceipt.jobCard.fit',
            'fabricType',
            'color',
            'brand'
        ])->whereHas('stockEntry', function($q) use ($filters) {
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
            [
                'Product Variation Details',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Product Details',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Product Dimensions (Variation-wise)',
                '',
                '',
                '',
                '',
                'Package Dimensions (Variation-wise)',
                '',
                '',
                '',
                ''
            ],
            [
                'Size',
                'Color',
                'Fit',
                'Barcode*',
                'MRP*',
                'WSP',
                'Pcs in One Set',
                'Variation Description',

                'Product Name*',
                'SKU Code*',
                'Status (ACTIVE/INACTIVE)*',
                'Has Variation? (Yes/No)',
                'Private (Yes/No)',
                'Tags',
                'Category Tree',
                'Brand',
                'Label (Helps to Categorise) - Max. 4 Labels',
                'Short Description',

                'Title #1',
                'Description #1',
                'Title #2',
                'Description #2',
                'Title #3',
                'Description #3',
                'Title #4',
                'Description #4',
                'Title #5',
                'Description #5',

                'Internal Description (Private to Company)',

                'Length',
                'Breadth',
                'Height',
                'Volume',
                'Weight',

                'Length',
                'Breadth',
                'Height',
                'Volume',
                'Weight',
            ]
        ];
    }
    public function map($item): array
    {
        $this->count++;
        $sleeveShort = $item->sleeve_type === 'Full' ? 'F/S' : ($item->sleeve_type === 'Half' ? 'H/S' : $item->sleeve_type);
        $fit = $sleeveShort;

        $price = \App\Models\ItemPrice::where('art_no', $item->art_no)->where('status', 'Active')->where('effective_from', '<=', date('Y-m-d'))->orderBy('effective_from', 'desc')->first();
        $mrp = $price ? $price->selling_price : 0;
        $wsp = $price ? $price->unit_price : 0;

        $parts = explode('-', $item->finished_item_code);
        $brandName = '';
        $brandCode = $parts[0] ?? '';
        $styleName = '';
        $styleCode = $parts[1] ?? '';

        if ($item->brand_id && $item->brand) {
            $brandName = $item->brand->brand_name;
        } elseif ($brandCode) {
            $brand = \App\Models\Brand::where('code', $brandCode)->first();
            $brandName = $brand ? $brand->brand_name : $brandCode;
        }

        if ($styleCode) {
            $style = \App\Models\Style::where('code', $styleCode)->first();
            $styleName = $style ? $style->style_name : $styleCode;
        }

        $categoryTree = "{$brandName} > {$brandCode} - {$styleName} {$sleeveShort}";

        $label = "{$brandName} {$styleName}  Shirts";
        $jobCardFit = $item->stockEntry?->productionReceipt?->jobCard?->fit?->fit_name ?? '-';
        $shortDescription = "{$jobCardFit}";

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
            $item->size ?? '-',
            $colorName,
            $fit,
            $item->sku ?? $item->barcode ?? '-',
            $mrp,
            $wsp,
            number_format($item->qty_in, 0),
            '', 
            $item->art_no, 
            $item->art_no, 
            'ACTIVE',
            'Yes',
            'No',
            '', 
            $categoryTree,
            $brandName,
            $label,
            $shortDescription,
            '', 
            '', 
            '', 
            '', 
            '', 
            '', 
            '', 
            '', 
            '', 
        ];
    }

    public function title(): string
    {
        return 'Finished Goods Stock';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFFFF00']], // Yellow
                'alignment' => ['horizontal' => 'center'],
            ],
            2 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD9E1F2']], // Light Blue/Gray
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->mergeCells('A1:H1');
                $event->sheet->getDelegate()->mergeCells('I1:AA1');
                $event->sheet->getDelegate()->getStyle('A1:AA1')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}
