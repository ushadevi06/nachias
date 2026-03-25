<?php

namespace App\Http\Controllers;

use App\Models\StockEntryItem;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function print($id)
    {
        $item = StockEntryItem::findOrFail($id);
        
        $labelData = json_decode($item->qrcode, true);
        
        // Fallback if json_decode fails
        if (!$labelData) {
            $labelData = [
                'sku' => $item->sku,
                'name' => 'Finished Good',
                'design' => $item->art_no,
                'fabric' => $item->fabricType ? $item->fabricType->fabric_type : 'Polyester',
                'size' => $item->size,
                'color' => $item->color ? $item->color->color_name : '-',
                'sleeve' => $item->sleeve_type ?? 'Full',
                'price' => number_format($item->price, 2)
            ];
        }

        return view('labels.print_qrcode', compact('item', 'labelData'));
    }
}
