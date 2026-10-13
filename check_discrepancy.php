<?php
/**
 * Investigate CASINO BRAVO stock discrepancy
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$brand = DB::table('brands')->where('brand_name', 'like', '%CASINO BRAVO%')->where('brand_name', 'not like', '%CORE%')->first();
if (!$brand) { echo "Brand not found.\n"; exit; }

$brandCode = $brand->code;
echo "Brand: {$brand->brand_name} (ID: {$brand->id}, Code: {$brandCode})\n";
echo str_repeat('=', 80) . "\n\n";

$fromDate = date('Y-m-d');
$toDate = date('Y-m-d');
echo "Date Range: {$fromDate} to {$toDate}\n\n";

// 1. Warehouse Summary: SUM(qty_in - qty_out) - all stock
$allStock = DB::table('stock_entry_items')
    ->leftJoin('brands', function($join) {
        $join->on(function($query) {
            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                ->orOn(function($sub) {
                    $sub->whereNull('stock_entry_items.brand_id')
                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                        ->whereRaw("NOT EXISTS (
                            SELECT 1 FROM brands b2 
                            WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                            AND LENGTH(b2.code) > LENGTH(brands.code)
                        )");
                });
        });
    })
    ->where('stock_entry_items.stock_type', 'finished_goods')
    ->whereNull('stock_entry_items.deleted_at')
    ->where('brands.id', $brand->id)
    ->select(DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'))
    ->first();

echo "1. WAREHOUSE SUMMARY (all stock): " . number_format($allStock->total_qty ?? 0) . "\n\n";

// 2. Stock I&S Level-1: currentStockPerBrand (same query but with direct brand filter)
$currentStock = DB::table('stock_entry_items')
    ->where('stock_entry_items.stock_type', 'finished_goods')
    ->whereNull('stock_entry_items.deleted_at')
    ->where(function($q) use ($brand, $brandCode) {
        $q->where('stock_entry_items.brand_id', $brand->id);
        if ($brandCode) {
            $q->orWhere(function($sub) use ($brandCode) {
                $sub->whereNull('stock_entry_items.brand_id')
                    ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
            });
        }
    })
    ->select(DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'))
    ->first();

echo "2. STOCK I&S currentStockPerBrand: " . number_format($currentStock->total_qty ?? 0) . "\n\n";

// 3. Check if there's overlap: items matching CASINO BRAVO code but also matching another brand with longer code
echo "3. Checking brand code overlaps...\n";
$allBrands = DB::table('brands')->where('status', 'Active')->get();
$overlapping = [];
foreach ($allBrands as $b) {
    if ($b->id == $brand->id) continue;
    if (strpos($b->code, $brandCode) === 0 || strpos($brandCode, $b->code) === 0) {
        $overlapping[] = $b;
    }
}

if (count($overlapping) > 0) {
    echo "   OVERLAPPING brand codes found:\n";
    foreach ($overlapping as $o) {
        echo "   - {$o->brand_name} (Code: {$o->code})\n";
    }
    
    // Check items that match both
    foreach ($overlapping as $o) {
        $count = DB::table('stock_entry_items')
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->whereNull('brand_id')
            ->where('art_no', 'like', $brandCode . '%')
            ->where('art_no', 'like', $o->code . '%')
            ->select(DB::raw('COUNT(*) as cnt, SUM(qty_in - qty_out) as stock'))
            ->first();
        echo "   Items matching BOTH '{$brandCode}' and '{$o->code}': count={$count->cnt}, stock={$count->stock}\n";
    }
} else {
    echo "   No overlapping brand codes.\n";
}

// 4. Compare: items with brand_id set vs items matched by art_no
$withBrandId = DB::table('stock_entry_items')
    ->where('stock_type', 'finished_goods')
    ->whereNull('deleted_at')
    ->where('brand_id', $brand->id)
    ->select(DB::raw('COUNT(*) as cnt, SUM(qty_in - qty_out) as stock'))
    ->first();

$withoutBrandId = DB::table('stock_entry_items')
    ->where('stock_type', 'finished_goods')
    ->whereNull('deleted_at')
    ->whereNull('brand_id')
    ->where('art_no', 'like', $brandCode . '%')
    ->select(DB::raw('COUNT(*) as cnt, SUM(qty_in - qty_out) as stock'))
    ->first();

echo "\n4. BRAND MATCHING BREAKDOWN:\n";
echo "   Items with brand_id={$brand->id}: count={$withBrandId->cnt}, stock=" . number_format($withBrandId->stock ?? 0) . "\n";
echo "   Items with art_no LIKE '{$brandCode}%' (no brand_id): count={$withoutBrandId->cnt}, stock=" . number_format($withoutBrandId->stock ?? 0) . "\n\n";

// 5. Now calculate Opening Stock both ways
$makeKey = function($artNo, $sleeve, $size) {
    return strtolower(trim($artNo ?? '')) . '_' . strtolower(trim($sleeve ?? '')) . '_' . strtolower(trim($size ?? ''));
};

// Stock items grouped
$stockItems = DB::table('stock_entry_items')
    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
    ->select(
        'stock_entry_items.art_no',
        'stock_entry_items.size',
        'stock_entry_items.sleeve_type',
        DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as current_stock')
    )
    ->where('stock_entry_items.stock_type', 'finished_goods')
    ->whereNull('stock_entry_items.deleted_at')
    ->where(function($q) use ($brand, $brandCode) {
        $q->where('stock_entry_items.brand_id', $brand->id);
        if ($brandCode) {
            $q->orWhere(function($sub) use ($brandCode) {
                $sub->whereNull('stock_entry_items.brand_id')
                    ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
            });
        }
    })
    ->groupBy('stock_entry_items.art_no', 'stock_entry_items.size', 'stock_entry_items.sleeve_type')
    ->get();

// Inward till now
$inwardTillNow = DB::table('stock_entry_items')
    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
    ->select('stock_entry_items.art_no', 'stock_entry_items.sleeve_type', 'stock_entry_items.size',
        DB::raw('SUM(stock_entry_items.qty_in) as inward_qty'))
    ->where('stock_entry_items.stock_type', 'finished_goods')
    ->where('stock_entry_items.qty_in', '>', 0)
    ->whereNull('stock_entry_items.deleted_at')
    ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate)
    ->where(function($q) use ($brand, $brandCode) {
        $q->where('stock_entry_items.brand_id', $brand->id);
        if ($brandCode) {
            $q->orWhere(function($sub) use ($brandCode) {
                $sub->whereNull('stock_entry_items.brand_id')
                    ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
            });
        }
    })
    ->groupBy('stock_entry_items.art_no', 'stock_entry_items.sleeve_type', 'stock_entry_items.size')
    ->get()->keyBy(function($item) use ($makeKey) { return $makeKey($item->art_no, $item->sleeve_type, $item->size); });

// Sales till now
$salesTillNow = DB::table('sales_invoice_items')
    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
    ->select('sales_invoice_items.art_no',
        DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
        'sales_invoice_items.size',
        DB::raw('SUM(sales_invoice_items.quantity) as sales_qty'))
    ->whereNull('sales_invoices.deleted_at')
    ->whereNull('sales_invoice_items.deleted_at')
    ->where('sales_invoices.inv_date', '>=', $fromDate)
    ->where(function($q) use ($brand, $brandCode) {
        $q->where('stock_entry_items.brand_id', $brand->id);
        if ($brandCode) {
            $q->orWhere(function($sub) use ($brandCode) {
                $sub->whereNull('stock_entry_items.brand_id')
                    ->where('sales_invoice_items.art_no', 'like', $brandCode . '%');
            });
        }
    })
    ->groupBy('sales_invoice_items.art_no',
        DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
        'sales_invoice_items.size')
    ->get()->keyBy(function($item) use ($makeKey) { return $makeKey($item->art_no, $item->sleeve_type, $item->size); });

// Returns till now
$returnsTillNow = DB::table('credit_note_items')
    ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
    ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
    ->leftJoin('stock_entry_items', function($join) {
        $join->on('credit_note_items.stock_entry_item_id', '=', 'stock_entry_items.id')
             ->orOn('sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id');
    })
    ->select(
        DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no) as art_no'),
        DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
        DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size) as size'),
        DB::raw('SUM(credit_note_items.quantity) as return_qty'))
    ->whereNull('credit_notes.deleted_at')
    ->whereNull('credit_note_items.deleted_at')
    ->where('credit_notes.note_date', '>=', $fromDate)
    ->where(function($q) use ($brand, $brandCode) {
        $q->where('stock_entry_items.brand_id', $brand->id);
        if ($brandCode) {
            $q->orWhere(function($sub) use ($brandCode) {
                $sub->whereNull('stock_entry_items.brand_id')
                    ->where(DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'), 'like', $brandCode . '%');
            });
        }
    })
    ->groupBy(
        DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'),
        DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
        DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size)'))
    ->get()->keyBy(function($item) use ($makeKey) { return $makeKey($item->art_no, $item->sleeve_type, $item->size); });

$totOpClamped = 0;
$totOpUnclamped = 0;
$totInward = 0;
$totSales = 0;
$totReturns = 0;
$clampedItems = [];

foreach ($stockItems as $item) {
    $key = $makeKey($item->art_no, $item->sleeve_type, $item->size);
    
    $curr = floatval($item->current_stock ?? 0);
    $inTN = floatval($inwardTillNow[$key]->inward_qty ?? 0);
    $sTN = floatval($salesTillNow[$key]->sales_qty ?? 0);
    $rTN = floatval($returnsTillNow[$key]->return_qty ?? 0);
    
    $rawOp = $curr - $inTN + $sTN - $rTN;
    $clampedOp = max(0, $rawOp);
    
    $totOpUnclamped += $rawOp;
    $totOpClamped += $clampedOp;
    
    if ($rawOp < 0) {
        $clampedItems[] = [
            'art_no' => $item->art_no,
            'size' => $item->size,
            'sleeve' => $item->sleeve_type,
            'curr' => $curr,
            'inTN' => $inTN,
            'sTN' => $sTN,
            'rTN' => $rTN,
            'rawOp' => $rawOp,
        ];
    }
    
    $totInward += floatval($inwardTillNow[$key]->inward_qty ?? 0);
    $totSales += floatval($salesTillNow[$key]->sales_qty ?? 0);
    $totReturns += floatval($returnsTillNow[$key]->return_qty ?? 0);
}

// Level-1 brand-level calculations
$brandInward = DB::table('stock_entry_items')
    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
    ->leftJoin('brands', function($join) {
        $join->on(function($query) {
            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                ->orOn(function($sub) {
                    $sub->whereNull('stock_entry_items.brand_id')
                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                        ->whereRaw("NOT EXISTS (SELECT 1 FROM brands b2 WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') AND LENGTH(b2.code) > LENGTH(brands.code))");
                });
        });
    })
    ->where('stock_entry_items.stock_type', 'finished_goods')
    ->where('stock_entry_items.qty_in', '>', 0)
    ->whereNull('stock_entry_items.deleted_at')
    ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate])
    ->where('brands.id', $brand->id)
    ->select(DB::raw('SUM(stock_entry_items.qty_in) as inward_qty'))
    ->first();

$brandSales = DB::table('sales_invoice_items')
    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
    ->leftJoin('brands', function($join) {
        $join->on(function($query) {
            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                ->orOn(function($sub) {
                    $sub->whereNull('stock_entry_items.brand_id')
                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                        ->whereRaw("NOT EXISTS (SELECT 1 FROM brands b2 WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') AND LENGTH(b2.code) > LENGTH(brands.code))");
                });
        });
    })
    ->whereNull('sales_invoices.deleted_at')
    ->whereNull('sales_invoice_items.deleted_at')
    ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate])
    ->where('brands.id', $brand->id)
    ->select(DB::raw('SUM(sales_invoice_items.quantity) as sales_qty'))
    ->first();

echo "5. LEVEL-1 BRAND SUMMARY CALCULATION:\n";
echo "   Current Stock: " . number_format($allStock->total_qty ?? 0) . "\n";
echo "   Inward (brand-level, today): " . number_format($brandInward->inward_qty ?? 0) . "\n";
echo "   Sales (brand-level, today): " . number_format($brandSales->sales_qty ?? 0) . "\n\n";

echo "6. LEVEL-2 ITEM-LEVEL CALCULATION:\n";
echo "   SUM current_stock: " . number_format($stockItems->sum('current_stock')) . "\n";
echo "   Total Inward (item-level): " . number_format($totInward) . "\n";
echo "   Total Sales (item-level): " . number_format($totSales) . "\n";
echo "   Total Returns (item-level): " . number_format($totReturns) . "\n";
echo "   Opening Stock (clamped): " . number_format($totOpClamped) . "\n";
echo "   Opening Stock (unclamped): " . number_format($totOpUnclamped) . "\n";
echo "   Clamping inflation: " . ($totOpClamped - $totOpUnclamped) . "\n";
echo "   Closing (clamped): " . number_format($totOpClamped + $totInward - $totSales) . "\n";
echo "   Closing (unclamped): " . number_format($totOpUnclamped + $totInward - $totSales) . "\n";
echo "   Net Closing (clamped): " . number_format($totOpClamped + $totInward - $totSales + $totReturns) . "\n\n";

echo "   Items with negative raw opening stock (clamped to 0): " . count($clampedItems) . "\n";
if (count($clampedItems) > 0) {
    echo "   Top 10 clamped items:\n";
    usort($clampedItems, function($a, $b) { return $a['rawOp'] - $b['rawOp']; });
    foreach (array_slice($clampedItems, 0, 10) as $ci) {
        echo "     Art={$ci['art_no']} Size={$ci['size']} Sleeve={$ci['sleeve']} | curr={$ci['curr']} inTN={$ci['inTN']} sTN={$ci['sTN']} rTN={$ci['rTN']} rawOp={$ci['rawOp']}\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "ANALYSIS:\n";
$warehouseTotal = $allStock->total_qty ?? 0;
$closingClamped = $totOpClamped + $totInward - $totSales;
$closingUnclamped = $totOpUnclamped + $totInward - $totSales;
echo "   Warehouse Summary stock: " . number_format($warehouseTotal) . "\n";
echo "   Stock I&S Closing (with clamping): " . number_format($closingClamped) . "\n";
echo "   Stock I&S Closing (without clamping): " . number_format($closingUnclamped) . "\n";
echo "   Difference (clamped - actual): " . ($closingClamped - $warehouseTotal) . "\n";
echo "   Difference (unclamped - actual): " . ($closingUnclamped - $warehouseTotal) . "\n";
