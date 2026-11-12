<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\ItemPrice;
use App\Models\StockEntryItem;
use App\Models\Style;
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
    protected array $priceMap = [];
    protected array $brandCodeMap = [];
    protected array $styleCodeMap = [];
    protected bool $lookupsLoaded = false;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filters = $this->filters;

        $items = StockEntryItem::with([
            'stockEntry.productionReceipt.jobCard.fit',
            'color',
            'brand',
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
                    $q->whereDate('stock_date', '>=', $fromDate);
                } catch (\Exception $e) {}
            }
            if (!empty($filters['to_date'])) {
                try {
                    $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $filters['to_date'])->format('Y-m-d');
                    $q->whereDate('stock_date', '<=', $toDate);
                } catch (\Exception $e) {}
            }
        })->orderBy('id', 'desc')->get();

        $this->preloadLookups($items);

        return $items;
    }

    /**
     * Bulk-preload active ItemPrices, Brands, and Styles to eliminate per-row N+1 queries during map().
     */
    protected function preloadLookups($items): void
    {
        $this->priceMap = [];
        $this->brandCodeMap = [];
        $this->styleCodeMap = [];

        $artNos = $items->pluck('art_no')
            ->filter(fn($val) => trim((string) $val) !== '')
            ->unique()
            ->values()
            ->all();

        $today = date('Y-m-d');
        foreach (array_chunk($artNos, 1000) as $chunk) {
            $prices = ItemPrice::whereIn('art_no', $chunk)
                ->where('status', 'Active')
                ->where('effective_from', '<=', $today)
                ->orderBy('effective_from', 'desc')
                ->get(['art_no', 'selling_price', 'unit_price']);

            foreach ($prices as $price) {
                $key = strtolower(trim((string) $price->art_no));
                if ($key !== '' && !isset($this->priceMap[$key])) {
                    $this->priceMap[$key] = [
                        'selling_price' => $price->selling_price,
                        'unit_price' => $price->unit_price,
                    ];
                }
            }
        }

        foreach (Brand::get(['code', 'brand_name']) as $brand) {
            $key = strtolower(trim((string) $brand->code));
            if ($key !== '' && !isset($this->brandCodeMap[$key])) {
                $this->brandCodeMap[$key] = $brand->brand_name;
            }
        }

        foreach (Style::get(['code', 'style_name']) as $style) {
            $key = strtolower(trim((string) $style->code));
            if ($key !== '' && !isset($this->styleCodeMap[$key])) {
                $this->styleCodeMap[$key] = $style->style_name;
            }
        }

        $this->lookupsLoaded = true;
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

        $artKey = strtolower(trim((string) ($item->art_no ?? '')));
        if ($this->lookupsLoaded) {
            $price = $this->priceMap[$artKey] ?? null;
            $mrp = $price ? $price['selling_price'] : 0;
            $wsp = $price ? $price['unit_price'] : 0;
        } else {
            $price = ItemPrice::where('art_no', $item->art_no)->where('status', 'Active')->where('effective_from', '<=', date('Y-m-d'))->orderBy('effective_from', 'desc')->first();
            $mrp = $price ? $price->selling_price : 0;
            $wsp = $price ? $price->unit_price : 0;
        }

        $parts = explode('-', $item->finished_item_code);
        $brandName = '';
        $brandCode = $parts[0] ?? '';
        $styleName = '';
        $styleCode = $parts[1] ?? '';

        if ($item->brand_id && $item->brand) {
            $brandName = $item->brand->brand_name;
        } elseif ($brandCode) {
            $brandKey = strtolower(trim((string) $brandCode));
            if ($this->lookupsLoaded) {
                $brandName = $this->brandCodeMap[$brandKey] ?? $brandCode;
            } else {
                $brand = Brand::where('code', $brandCode)->first();
                $brandName = $brand ? $brand->brand_name : $brandCode;
            }
        }

        if ($styleCode) {
            $styleKey = strtolower(trim((string) $styleCode));
            if ($this->lookupsLoaded) {
                $styleName = $this->styleCodeMap[$styleKey] ?? $styleCode;
            } else {
                $style = Style::where('code', $styleCode)->first();
                $styleName = $style ? $style->style_name : $styleCode;
            }
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
