<?php

namespace App\Exports;

use App\Models\ProductionReceiptItem;
use App\Models\BarcodeMaster;
use App\Models\ItemPrice;
use App\Models\StockEntryItem;
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
            'SKU',
            'Barcode',
            'Color',
            'Fit',
            'Product Combination Location',
            'Size',
            'Product Variation Location',
            'MRP',
            'Pcs in one set',
            'Stock',
            'Current Stock',
            'Unfulfilled Orders',
            'Future Stock',
            'Total',
            'Custom Status',
            'Custom Status Last Updated At',
        ];
    }

    public function map($item): array
    {
        // 1. Product Name -> art_no
        $productName = $item->art_no;

        // 2. SKU -> art_no
        $sku = $item->art_no;

        // 3. Barcode -> calculated barcode / sku
        $sleeveTypeLong = 'Full';
        $sleevePart = null;
        if (str_contains($item->size_variant, ' - ')) {
            $parts = explode(' - ', $item->size_variant);
            $sleevePart = trim($parts[1]);
            $sleeveTypeLong = ($sleevePart == 'F/S') ? 'Full' : (($sleevePart == 'H/S') ? 'Half' : $sleevePart);
        }
        $sleeveTypeShort = ($sleeveTypeLong == 'Full') ? 'F/S' : (($sleeveTypeLong == 'Half') ? 'H/S' : $sleeveTypeLong);

        $barcodeMaster = BarcodeMaster::where('art_no', $item->art_no)
            ->where('size', $item->size)
            ->where('sleeve_type', $sleeveTypeShort)
            ->first();

        if ($barcodeMaster) {
            $barcode = $barcodeMaster->barcode_no;
        } else {
            preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $item->art_no, $matches);
            $numericBase = $matches[2] ?? '';
            $suffix = $matches[3] ?? '1';
            $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
            $formattedSize = str_pad(trim((string)$item->size), 2, '0', STR_PAD_LEFT);
            $sleeveCode = '00';
            if (isset($sleevePart)) {
                if ($sleevePart == 'F/S') $sleeveCode = '01';
                elseif ($sleevePart == 'H/S') $sleeveCode = '02';
            } else {
                if ($sleeveTypeLong == 'Full') $sleeveCode = '01';
                elseif ($sleeveTypeLong == 'Half') $sleeveCode = '02';
            }
            $barcode = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;
        }

        // 4. Color -> color->color_name ?? ''
        $color = $item->color->color_name ?? '';
        if ($color === '-') {
            $color = '';
        }

        // 5. Fit -> sleeve abbreviation
        $fit = '';
        if (str_contains($item->size_variant, ' - ')) {
            $parts = explode(' - ', $item->size_variant);
            $sleeveAbbr = trim($parts[1]);
            if (strtoupper($sleeveAbbr) === 'F/S') {
                $fit = 'F/s';
            } elseif (strtoupper($sleeveAbbr) === 'H/S') {
                $fit = 'H/s';
            } else {
                $fit = $sleeveAbbr;
            }
        }
        if ($fit === '-') {
            $fit = '';
        }

        // 6. Product Combination Location -> empty
        $productCombinationLocation = '';

        // 7. Size -> size
        $size = $item->size;

        // 8. Product Variation Location -> empty
        $productVariationLocation = '';

        // 9. MRP -> check exists in item price, show price otherwise production receipt price (unit_price)
        $itemPrice = ItemPrice::where('finished_item_code', $item->item_code)
            ->where('status', 'Active')
            ->where('effective_from', '<=', date('Y-m-d'))
            ->orderBy('effective_from', 'desc')
            ->first();
        if (!$itemPrice) {
            $itemPrice = ItemPrice::where('art_no', $item->art_no)
                ->where('status', 'Active')
                ->where('effective_from', '<=', date('Y-m-d'))
                ->orderBy('effective_from', 'desc')
                ->first();
        }
        $mrp = $itemPrice ? $itemPrice->selling_price : $item->unit_price;

        // 10. Pcs in one set -> qty_to_receive
        $pcsInOneSet = (int)$item->qty_to_receive;

        // 11. Stock -> empty (blank)
        $stock = '';

        // 12. Current Stock -> stock entry finished goods
        $currentStockVal = (int)StockEntryItem::where('sku', $barcode)
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->sum(\DB::raw('qty_in - qty_out'));
        $currentStock = ($currentStockVal == 0) ? '' : $currentStockVal;

        // 13. Unfulfilled Orders -> empty (blank)
        $unfulfilledOrders = '';

        // 14. Future Stock -> current stock (calculated using values)
        $futureStockVal = $currentStockVal;
        $futureStock = ($futureStockVal == 0) ? '' : $futureStockVal;

        // 15. Total -> MRP * Future Stock
        $totalVal = $mrp * $futureStockVal;
        $total = ($totalVal == 0) ? '' : $totalVal;

        // 16. Custom Status -> 'None'
        $customStatus = 'None';

        // 17. Custom Status Last Updated At -> empty
        $customStatusLastUpdatedAt = '';

        return [
            $productName,
            $sku,
            $barcode,
            $color,
            $fit,
            $productCombinationLocation,
            $size,
            $productVariationLocation,
            $mrp,
            $pcsInOneSet,
            $stock,
            $currentStock,
            $unfulfilledOrders,
            $futureStock,
            $total,
            $customStatus,
            $customStatusLastUpdatedAt,
        ];
    }
}
