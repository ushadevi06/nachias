<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\StockEntryItem;
use App\Models\StoreLocation;
use Illuminate\Support\Facades\DB;

class WarehouseReportController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::where('status', 'Active')->get();
        $stores = StoreLocation::where('status', 'Active')->get();


        // --- 1. Brandwise Sales with Trend ---
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-01');
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');

        // Current Period Sales
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
            ->whereNull('sales_invoice_items.deleted_at')
            ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

        if ($request->brand_id) {
            $currentSalesQuery->where('brands.id', $request->brand_id);
        }
        if ($request->store_id) {
            $currentSalesQuery->where('sales_invoices.store_location_id', $request->store_id);
        }


        $brandwiseSales = $currentSalesQuery->select(
            'brands.brand_name as brand',
            // 'brand_categories.name as category',
            DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
            DB::raw('SUM(sales_invoice_items.amount) as sales_value'),
            'brands.id as brand_id'
        )
            ->groupBy('brands.brand_name', 'brands.id')
            ->get();

        // Trend Calculation
        $daysDiff = (strtotime($toDate) - strtotime($fromDate)) / (60 * 60 * 24);
        $prevToDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $prevFromDate = date('Y-m-d', strtotime($prevToDate . " -$daysDiff days"));

        $prevSales = SalesInvoiceItem::query()
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
            ->whereNull('sales_invoice_items.deleted_at')
            ->whereBetween('sales_invoices.inv_date', [$prevFromDate, $prevToDate]);

        if ($request->brand_id) {
            $prevSales->where('brands.id', $request->brand_id);
        }
        if ($request->store_id) {
            $prevSales->where('sales_invoices.store_location_id', $request->store_id);
        }


        $prevSalesPlucked = $prevSales->select('brands.id as brand_id', DB::raw('SUM(sales_invoice_items.amount) as prev_sales_value'))
            ->groupBy('brands.id')
            ->pluck('prev_sales_value', 'brand_id');

        foreach ($brandwiseSales as $sale) {
            $prevValue = $prevSalesPlucked[$sale->brand_id] ?? 0;
            if ($prevValue > 0) {
                $sale->trend = (($sale->sales_value - $prevValue) / $prevValue) * 100;
            }
            else {
                $sale->trend = $sale->sales_value > 0 ? 100 : 0;
            }
        }

        // --- 2. Brandwise Stock ---
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

        if ($request->brand_id) {
            $stockQuery->where('brands.id', $request->brand_id);
        }
        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }


        $brandwiseStockRaw = $stockQuery->groupBy('brands.brand_name', 'store_categories.category_name', 'stock_entry_items.art_no', 'size_ratios.ratio')->get();

        $brandwiseStock = $brandwiseStockRaw->map(function ($data) {
            $ratios = explode(',', $data->ratio);
            $itemsPerSet = array_sum($ratios);
            $data->items_per_set = $itemsPerSet;
            $data->sets_available = $itemsPerSet > 0 ? floor($data->total_qty / $itemsPerSet) : 0;
            return $data;
        });

        // --- 3. Assorted Stock ---
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

        if ($request->brand_id) {
            $assortedQuery->where('brands.id', $request->brand_id);
        }
        if ($request->store_id) {
            $assortedQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }


        $assortedStock = $assortedQuery->groupBy(
            'store_locations.store_location',
            'stock_entry_items.finished_item_code',
            'stock_entry_items.art_no',
            'stock_entry_items.size'
        )->get();

        // --- 4. Order vs Dispatch ---
        $orderVsDispatchQuery = SalesOrder::with(['customer'])
            ->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
            ->leftJoin('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->select(
            'sales_orders.id',
            'sales_orders.customer_id',
            'sales_orders.so_no',
            'sales_orders.so_date',
            'sales_orders.total_qty as ordered_qty',
            DB::raw('COALESCE(SUM(sales_invoice_items.quantity), 0) as dispatched_qty')
        )
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at');

        if ($request->from_date) {
            $orderVsDispatchQuery->where('sales_orders.so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $orderVsDispatchQuery->where('sales_orders.so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->brand_id) {
            $orderVsDispatchQuery->whereExists(function ($query) use ($request) {
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
                    ->where('brands.id', $request->brand_id);
            });
        }


        $orderVsDispatch = $orderVsDispatchQuery->groupBy('sales_orders.id', 'sales_orders.customer_id', 'sales_orders.so_no', 'sales_orders.so_date', 'sales_orders.total_qty')
            ->get();

        foreach ($orderVsDispatch as $data) {
            $data->pending_qty = max(0, $data->ordered_qty - $data->dispatched_qty);
            $data->fulfillment_pc = $data->ordered_qty > 0 ? min(100, ($data->dispatched_qty / $data->ordered_qty) * 100) : 0;
        }

        // --- 5. Dispatch Report ---
        $dispatchReportQuery = SalesOrder::with(['customer.city', 'salesInvoices'])
            ->where('status', 'Dispatched')
            ->whereNull('deleted_at');

        if ($request->from_date) {
            $dispatchReportQuery->where('so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $dispatchReportQuery->where('so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->brand_id) {
            $dispatchReportQuery->whereExists(function ($query) use ($request) {
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
                    ->where('brands.id', $request->brand_id);
            });
        }

        $dispatchReport = $dispatchReportQuery->orderBy('id', 'desc')->get();
        
        // --- 6. Stock Inward (GRN) ---
        $stockInwardQuery = GrnEntry::with(['supplier'])
            ->leftJoin('grn_entry_items', 'grn_entries.id', '=', 'grn_entry_items.grn_entry_id')
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
                'grn_entries.grn_date',
                'grn_entries.status',
                'grn_entries.supplier_id',
                DB::raw('SUM(grn_entry_items.qty_received) as total_qty')
            )
            ->whereNull('grn_entries.deleted_at')
            ->whereNull('grn_entry_items.deleted_at');

        if ($request->from_date) {
            $stockInwardQuery->where('grn_entries.grn_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $stockInwardQuery->where('grn_entries.grn_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->brand_id) {
            $stockInwardQuery->where('brands.id', $request->brand_id);
        }


        $stockInward = $stockInwardQuery->groupBy('grn_entries.id', 'grn_entries.grn_number', 'grn_entries.grn_date', 'grn_entries.status', 'grn_entries.supplier_id')
            ->orderBy('grn_entries.grn_date', 'desc')
            ->get();

        // --- 7. Regular vs Discount Sales ---
        $regularDiscountQuery = SalesInvoice::query()
            ->select(
                DB::raw("DATE_FORMAT(inv_date, '%b %Y') as period"),
                DB::raw("SUM(CASE WHEN discount = 0 THEN sub_total ELSE 0 END) as regular_sales"),
                DB::raw("SUM(CASE WHEN discount > 0 THEN sub_total ELSE 0 END) as discount_sales_raw"),
                DB::raw("SUM(discount) as total_discount"),
                DB::raw("SUM(sub_total - discount) as net_revenue")
            )
            ->whereNull('deleted_at');

        if ($request->from_date) {
            $regularDiscountQuery->where('inv_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $regularDiscountQuery->where('inv_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->store_id) {
            $regularDiscountQuery->where('store_location_id', $request->store_id);
        }
        if ($request->brand_id) {
            $regularDiscountQuery->whereExists(function ($query) use ($request) {
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
                    ->where('brands.id', $request->brand_id);
            });
        }

        $regularDiscount = $regularDiscountQuery->groupBy(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"), DB::raw("YEAR(inv_date)"), DB::raw("MONTH(inv_date)"))
            ->orderBy(DB::raw("YEAR(inv_date)"), 'desc')
            ->orderBy(DB::raw("MONTH(inv_date)"), 'desc')
            ->get();

        foreach ($regularDiscount as $data) {
            $data->discount_sales = $data->discount_sales_raw - $data->total_discount;
            $data->discount_pc = $data->discount_sales_raw > 0 ? ($data->total_discount / $data->discount_sales_raw) * 100 : 0;
        }

        // --- 8. Priority Stock (Ageing > 90 Days) ---
        $priorityStockQuery = StockEntryItem::query()
            ->select(
                'art_no',
                DB::raw('SUM(qty_in - qty_out) as current_stock'),
                DB::raw('DATEDIFF(NOW(), MIN(created_at)) as ageing_days'),
                DB::raw('MAX(price) as latest_price'),
                DB::raw('SUM((qty_in - qty_out) * price) as stock_value')
            )
            ->whereNull('deleted_at');

        if ($request->brand_id) {
            $priorityStockQuery->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('brands')
                    ->whereColumn('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                    ->whereRaw("NOT EXISTS (
                         SELECT 1 FROM brands b2 
                         WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                         AND LENGTH(b2.code) > LENGTH(brands.code)
                    )")
                    ->where('brands.id', $request->brand_id);
            });
        }
        if ($request->store_id) {
            $priorityStockQuery->where('store_location_id', $request->store_id);
        }

        $priorityStock = $priorityStockQuery->groupBy('art_no')
            ->having('current_stock', '>', 0)
            ->having('ageing_days', '>=', 90)
            ->orderBy('ageing_days', 'desc')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'brand-sales' => view('reports.warehouse_report.brandwise_sales', compact('brandwiseSales'))->render(),
                'brand-stock' => view('reports.warehouse_report.brandwise_stock', compact('brandwiseStock'))->render(),
                'assorted-stock' => view('reports.warehouse_report.assorted_stock', compact('assortedStock'))->render(),
                'order-dispatch' => view('reports.warehouse_report.order_vs_dispatch', compact('orderVsDispatch'))->render(),
                'sales-return' => view('reports.warehouse_report.sales_return')->render(),
                'white-dhoti' => view('reports.warehouse_report.white_dhoti')->render(),
                'dispatch' => view('reports.warehouse_report.dispatch_report', compact('dispatchReport'))->render(),
                'inward' => view('reports.warehouse_report.stock_inward', compact('stockInward'))->render(),
                'discount' => view('reports.warehouse_report.regular_discount', compact('regularDiscount'))->render(),
                'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render(),
                'damage' => view('reports.warehouse_report.damage_sales_split')->render(),
            ]);
        }

        return view('reports/warehouse_report', compact(
            'brandwiseSales',
            'brandwiseStock',
            'assortedStock',
            'orderVsDispatch',
            'dispatchReport',
            'stockInward',
            'regularDiscount',
            'priorityStock',
            'brands',
            'stores'
        ));
    }
}
