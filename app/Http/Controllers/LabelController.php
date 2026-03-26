<?php

namespace App\Http\Controllers;

use App\Models\StockEntryItem;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function print($id)
    {
        $item = StockEntryItem::with([
            'item.brand',
            'item.style',
            'color',
            'fabricType',
            'stockEntry.productionReceipt.jobCard'
        ])->findOrFail($id);

        $labelData = json_decode($item->qrcode, true);

        // Always ensure we have the latest/rich data for the new design
        $fabricType = $item->fabricType ? $item->fabricType->fabric_type : null;
        if (!$fabricType && $item->stockEntry->productionReceipt && $item->stockEntry->productionReceipt->jobCard) {
            $fabricType = $item->stockEntry->productionReceipt->jobCard->fabricType->fabric_type ?? null;
        }

        $labelData = [
            'sku' => $item->sku,
            'company_name' => 'NACHIAS FASHION PRIVATE LIMITED',
            'company_address' => '272/2, SOMU NAGAR, SIRINGERI NAGAR, SARATHAMBAL KOVIL BACKSIDE BYE PASS ROAD, MADURAI - 625016',
            'company_gstin' => '33AADCN9342A1ZU',
            'company_contact' => 'TOLL-FREE: 8489938071, 8489938073',
            'company_email' => 'sales@nachias.com',
            'product_name' => $item->item->style->style_name ?? ($item->item->name ?? 'SHIRTS'),
            'brand_name' => $item->item->brand->brand_name ?? 'CASINO FORMAL',
            'design' => $item->art_no ?? ($item->item->design_art_no ?? '-'),
            'color' => $item->color ? $item->color->color_name : '-',
            'fabric' => $fabricType ?? 'COTTON POLYESTER',
            'size' => $item->size,
            'sleeve' => $item->sleeve_type ?? 'FULL',
            'price' => number_format($item->price, 2),
            'mfg_date' => now()->format('F Y'),
            'lot_no' => ($item->stockEntry->productionReceipt && $item->stockEntry->productionReceipt->jobCard)
            ? $item->stockEntry->productionReceipt->jobCard->job_card_no
            : '-',
            'quantity' => number_format($item->qty_in, 2)
        ];

        return view('labels.print_qrcode', compact('item', 'labelData'));
    }
}
