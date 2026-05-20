<?php
require 'c:/xampp/htdocs/nachias/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/nachias/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Brand;
use App\Models\StoreLocation;
use App\Models\Item;
use App\Models\GrnEntry;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\StockEntryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== STARTING FULL QUERY VERIFICATION ===\n\n";

$request = new Request();
// Test without filters first
$brandId = Brand::where('status', 'Active')->value('id');
$storeId = StoreLocation::where('status', 'Active')->value('id');

echo "Active Brand ID for filtering test: " . ($brandId ?? 'None') . "\n";
echo "Active Store ID for filtering test: " . ($storeId ?? 'None') . "\n\n";

// --- 1. Brandwise Sales ---
try {
    $currentSalesQuery = SalesInvoiceItem::query()
        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
        ->leftJoin('brands', function($join) {
            $join->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                 ->whereRaw("NOT EXISTS (
                      SELECT 1 FROM brands b2 
                      WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                      AND LENGTH(b2.code) > LENGTH(brands.code)
                 )");
        })
        ->whereNull('sales_invoices.deleted_at')
        ->whereNull('sales_invoice_items.deleted_at');

    $brandwiseSales = $currentSalesQuery->select(
        'brands.brand_name as brand',
        DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
        DB::raw('SUM(sales_invoice_items.amount) as sales_value'),
        'brands.id as brand_id'
    )
        ->groupBy('brands.brand_name', 'brands.id')
        ->get();
    echo "[PASS] Brandwise Sales Query completed successfully. Count: " . $brandwiseSales->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Brandwise Sales Query: " . $e->getMessage() . "\n";
}

// --- 2. Brandwise Stock ---
try {
    $stockQuery = StockEntryItem::query()
        ->leftJoin('brands', function($join) {
            $join->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                 ->whereRaw("NOT EXISTS (
                      SELECT 1 FROM brands b2 
                      WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                      AND LENGTH(b2.code) > LENGTH(brands.code)
                 )");
        })
        ->leftJoin('store_categories', 'stock_entry_items.store_category_id', '=', 'store_categories.id')
        ->leftJoin('size_ratios', function($join) {
            $join->on(DB::raw('1'), '=', DB::raw('1'))
                 ->whereRaw("size_ratios.id = (SELECT MIN(id) FROM size_ratios WHERE status = 'Active')");
        })
        ->select(
            'brands.brand_name as brand',
            DB::raw("COALESCE(store_categories.category_name, 'Fabric') as category"),
            'stock_entry_items.art_no as article_no',
            'size_ratios.ratio',
            DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty')
        )
        ->where('stock_entry_items.stock_type', 'finished_goods')
        ->whereNull('stock_entry_items.deleted_at');

    $brandwiseStock = $stockQuery->groupBy('brands.brand_name', 'store_categories.category_name', 'stock_entry_items.art_no', 'size_ratios.ratio')->get();
    echo "[PASS] Brandwise Stock Query completed successfully. Count: " . $brandwiseStock->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Brandwise Stock Query: " . $e->getMessage() . "\n";
}

// --- 3. Assorted Stock ---
try {
    $assortedQuery = StockEntryItem::query()
        ->select(
            'store_locations.store_location as store',
            DB::raw("COALESCE(stock_entry_items.finished_item_code, stock_entry_items.art_no, '-') as item_name"),
            'stock_entry_items.size',
            DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as stock_qty')
        )
        ->join('store_locations', 'stock_entry_items.store_location_id', '=', 'store_locations.id')
        ->leftJoin('brands', function($join) {
            $join->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                 ->whereRaw("NOT EXISTS (
                      SELECT 1 FROM brands b2 
                      WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                      AND LENGTH(b2.code) > LENGTH(brands.code)
                 )");
        })
        ->where('stock_entry_items.stock_type', 'finished_goods')
        ->whereNull('stock_entry_items.deleted_at');

    // Run with brand filter to test the brand filter join
    if ($brandId) {
        $assortedQuery->where('brands.id', $brandId);
    }

    $assortedStock = $assortedQuery->groupBy(
        'store_locations.store_location',
        'stock_entry_items.finished_item_code',
        'stock_entry_items.art_no',
        'stock_entry_items.size'
    )->get();
    echo "[PASS] Assorted Stock Query completed successfully. Count: " . $assortedStock->count() . "\n";
    if ($assortedStock->count() > 0) {
        echo "   Sample row: " . json_encode($assortedStock->first()) . "\n";
    }
} catch (\Exception $e) {
    echo "[FAIL] Assorted Stock Query: " . $e->getMessage() . "\n";
}

// --- 4. Order vs Dispatch ---
try {
    $orderVsDispatchQuery = SalesOrder::query()
        ->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
        ->leftJoin('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
        ->select(
            'sales_orders.id',
            'sales_orders.so_no',
            'sales_orders.total_qty as ordered_qty',
            DB::raw('COALESCE(SUM(sales_invoice_items.quantity), 0) as dispatched_qty')
        )
        ->whereNull('sales_orders.deleted_at')
        ->whereNull('sales_invoices.deleted_at')
        ->whereNull('sales_invoice_items.deleted_at');

    if ($brandId) {
        $orderVsDispatchQuery->whereExists(function ($query) use ($brandId) {
            $query->select(DB::raw(1))
                ->from('sales_order_items')
                ->leftJoin('brands', function($join) {
                    $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                         ->whereRaw("NOT EXISTS (
                              SELECT 1 FROM brands b2 
                              WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                              AND LENGTH(b2.code) > LENGTH(brands.code)
                         )");
                })
                ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                ->where('brands.id', $brandId);
        });
    }

    $orderVsDispatch = $orderVsDispatchQuery->groupBy('sales_orders.id', 'sales_orders.so_no', 'sales_orders.total_qty')->get();
    echo "[PASS] Order vs Dispatch Query completed successfully. Count: " . $orderVsDispatch->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Order vs Dispatch Query: " . $e->getMessage() . "\n";
}

// --- 5. Dispatch Report ---
try {
    $dispatchReportQuery = SalesOrder::with(['customer.city', 'salesInvoices'])
        ->where('status', 'Dispatched')
        ->whereNull('deleted_at');

    if ($brandId) {
        $dispatchReportQuery->whereExists(function ($query) use ($brandId) {
            $query->select(DB::raw(1))
                ->from('sales_order_items')
                ->leftJoin('brands', function($join) {
                    $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                         ->whereRaw("NOT EXISTS (
                              SELECT 1 FROM brands b2 
                              WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                              AND LENGTH(b2.code) > LENGTH(brands.code)
                         )");
                })
                ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                ->where('brands.id', $brandId);
        });
    }

    $dispatchReport = $dispatchReportQuery->get();
    echo "[PASS] Dispatch Report Query completed successfully. Count: " . $dispatchReport->count() . "\n";
    if ($dispatchReport->count() > 0) {
        $firstDoc = $dispatchReport->first();
        $invoice = $firstDoc->salesInvoices->first();
        echo "   First Sales Order SO No: " . $firstDoc->so_no . "\n";
        echo "   Related Invoice details:\n";
        if ($invoice) {
            echo "     Invoice No: " . $invoice->inv_no . "\n";
            echo "     Transporter Name (from Invoice): " . ($invoice->transporter_name ?? 'N/A') . "\n";
            echo "     LR No (from Invoice): " . ($invoice->lr_no ?? 'N/A') . "\n";
        } else {
            echo "     No related invoices found. Fallback Transporter: " . ($firstDoc->transporter_name ?? 'N/A') . ", LR No: " . ($firstDoc->lr_no ?? 'N/A') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "[FAIL] Dispatch Report Query: " . $e->getMessage() . "\n";
}

// --- 6. Stock Inward (GRN) ---
try {
    $stockInwardQuery = GrnEntry::leftJoin('grn_entry_items', 'grn_entries.id', '=', 'grn_entry_items.grn_entry_id')
        ->leftJoin('brands', function($join) {
            $join->on('grn_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                 ->whereRaw("NOT EXISTS (
                      SELECT 1 FROM brands b2 
                      WHERE grn_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                      AND LENGTH(b2.code) > LENGTH(brands.code)
                 )");
        })
        ->select(
            'grn_entries.id',
            'grn_entries.grn_number',
            DB::raw('SUM(grn_entry_items.qty_received) as total_qty')
        )
        ->whereNull('grn_entries.deleted_at')
        ->whereNull('grn_entry_items.deleted_at');

    if ($brandId) {
        $stockInwardQuery->where('brands.id', $brandId);
    }

    $stockInward = $stockInwardQuery->groupBy('grn_entries.id', 'grn_entries.grn_number')->get();
    echo "[PASS] Stock Inward Query completed successfully. Count: " . $stockInward->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Stock Inward Query: " . $e->getMessage() . "\n";
}

// --- 7. Regular vs Discount Sales ---
try {
    $regularDiscountQuery = SalesInvoice::query()
        ->select(
            DB::raw("DATE_FORMAT(inv_date, '%b %Y') as period"),
            DB::raw("SUM(sub_total) as net_revenue")
        )
        ->whereNull('deleted_at');

    if ($brandId) {
        $regularDiscountQuery->whereExists(function ($query) use ($brandId) {
            $query->select(DB::raw(1))
                ->from('sales_invoice_items')
                ->leftJoin('brands', function($join) {
                    $join->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                         ->whereRaw("NOT EXISTS (
                              SELECT 1 FROM brands b2 
                              WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                              AND LENGTH(b2.code) > LENGTH(brands.code)
                         )");
                })
                ->whereColumn('sales_invoice_items.sales_invoice_id', 'sales_invoices.id')
                ->where('brands.id', $brandId);
        });
    }

    $regularDiscount = $regularDiscountQuery->groupBy(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"))->get();
    echo "[PASS] Regular vs Discount Sales Query completed successfully. Count: " . $regularDiscount->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Regular vs Discount Sales Query: " . $e->getMessage() . "\n";
}

// --- 8. Priority Stock ---
try {
    $priorityStockQuery = StockEntryItem::query()
        ->select(
            'art_no',
            DB::raw('SUM(qty_in - qty_out) as current_stock')
        )
        ->whereNull('deleted_at');

    if ($brandId) {
        $priorityStockQuery->whereExists(function ($query) use ($brandId) {
            $query->select(DB::raw(1))
                ->from('brands')
                ->whereColumn('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                ->whereRaw("NOT EXISTS (
                     SELECT 1 FROM brands b2 
                     WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                     AND LENGTH(b2.code) > LENGTH(brands.code)
                )")
                ->where('brands.id', $brandId);
        });
    }

    $priorityStock = $priorityStockQuery->groupBy('art_no')
        ->having('current_stock', '>', 0)
        ->get();
    echo "[PASS] Priority Stock Query completed successfully. Count: " . $priorityStock->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] Priority Stock Query: " . $e->getMessage() . "\n";
}

echo "\n=== QUERY VERIFICATION COMPLETED ===\n";
