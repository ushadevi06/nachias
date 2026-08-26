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
    public function brandwiseStockDetails($id)
    {
        $brand = Brand::find($id);
        return view('reports.warehouse_report.brandwise_stock_details', compact('id', 'brand'));
    }

    public function index(Request $request)
    {
        $brands = Brand::where('status', 'Active')->get();
        $stores = StoreLocation::where('status', 'Active')->get();


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

        $priorityStock = $priorityStockQuery->groupBy('art_no')->having('current_stock', '>', 0)->having('ageing_days', '>=', 90)->orderBy('ageing_days', 'desc')->get();

                if ($request->ajax()) {
            return response()->json([
                'sales-return' => view('reports.warehouse_report.sales_return')->render(),
                'white-dhoti' => view('reports.warehouse_report.white_dhoti')->render(),
                'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render(),
                'damage' => view('reports.warehouse_report.damage_sales_split')->render(),
                'urgent-orders' => view('reports.warehouse_report.urgent_orders')->render(),
                'brandwise-lost-sales' => view('reports.warehouse_report.brandwise_lost_sales')->render(),
            ]);
        }

        return view('reports/warehouse_report', compact(
            'priorityStock',
            'brands',
            'stores'
        ));
    }

    public function getBrandwiseStyles(Request $request)
    {
        $brandId = $request->brand_id;
        
        $stockQuery = \App\Models\StockEntryItem::query()
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->leftJoin('fg_min_stocks', function($join) {
                $join->on('stock_entry_items.id', '=', 'fg_min_stocks.stock_entry_item_id')
                    ->where('fg_min_stocks.status', '=', 'Active')
                    ->whereNull('fg_min_stocks.deleted_at');
            })
            ->select(
                DB::raw('COALESCE(styles.id, 0) as style_id'),
                DB::raw("COALESCE(styles.style_name, '(No Style)') as style_name"),
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                DB::raw('SUM(IFNULL(fg_min_stocks.min_stock, 0)) as total_min_stock')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId) {
            $stockQuery->where(function($q) use ($brandId) {
                $q->where('stock_entry_items.brand_id', $brandId)
                  ->orWhere(function($q2) use ($brandId) {
                      $q2->whereNull('stock_entry_items.brand_id')
                         ->whereExists(function($sub) use ($brandId) {
                             $sub->select(DB::raw(1))
                                 ->from('brands')
                                 ->where('brands.id', $brandId)
                                 ->whereRaw('stock_entry_items.art_no LIKE CONCAT(brands.code, \'%\')')
                                 ->whereRaw('NOT EXISTS (SELECT 1 FROM brands b2 WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, \'%\') AND LENGTH(b2.code) > LENGTH(brands.code))');
                         });
                  });
            });
        } else {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        $styles = $stockQuery->groupBy('styles.id', 'styles.style_name')->get();

        return response()->json($styles);
    }

    public function getBrandwiseArtNos(Request $request)
    {
        $brandId = $request->brand_id;
        $styleId = $request->style_id;
        
        $stockQuery = \App\Models\StockEntryItem::query()
            ->leftJoin('fg_min_stocks', function($join) {
                $join->on('stock_entry_items.id', '=', 'fg_min_stocks.stock_entry_item_id')
                     ->where('fg_min_stocks.status', '=', 'Active')
                     ->whereNull('fg_min_stocks.deleted_at');
            })
            ->select(
                'stock_entry_items.art_no',
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                DB::raw('SUM(IFNULL(fg_min_stocks.min_stock, 0)) as total_min_stock'),
                DB::raw('MIN(CASE WHEN (stock_entry_items.qty_in - stock_entry_items.qty_out) > 0 THEN stock_entry_items.created_at ELSE NULL END) as oldest_stock_date')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->where('stock_entry_items.style_id', $styleId);

        if ($brandId) {
            $stockQuery->where(function($q) use ($brandId) {
                $q->where('stock_entry_items.brand_id', $brandId)
                  ->orWhere(function($q2) use ($brandId) {
                      $q2->whereNull('stock_entry_items.brand_id')
                         ->whereExists(function($sub) use ($brandId) {
                             $sub->select(DB::raw(1))
                                 ->from('brands')
                                 ->where('brands.id', $brandId)
                                 ->whereRaw('stock_entry_items.art_no LIKE CONCAT(brands.code, \'%\')')
                                 ->whereRaw('NOT EXISTS (SELECT 1 FROM brands b2 WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, \'%\') AND LENGTH(b2.code) > LENGTH(brands.code))');
                         });
                  });
            });
        } else {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        $artNos = $stockQuery->groupBy('stock_entry_items.art_no')->get();

        foreach ($artNos as $artNo) {
            if ($artNo->oldest_stock_date) {
                $artNo->stock_days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($artNo->oldest_stock_date));
            } else {
                $artNo->stock_days = '-';
            }
        }

        return response()->json($artNos);
    }


    public function ajaxReportData(Request $request, $type)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length);
        $search = $request->search['value'] ?? '';

        switch ($type) {
            case 'brandwise-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d', strtotime('-30 days'));
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
                $query = SalesInvoiceItem::query()
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        ) ");
                                });
                        });
                    })
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at')
                    ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

                if ($request->brand_id) {
                    $query->where('brands.id', $request->brand_id);
                }
                if ($request->store_id) {
                    $query->where('sales_invoices.store_location_id', $request->store_id);
                }

                $query->select(
                    'brands.brand_name as brand',
                    DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
                    DB::raw('SUM(sales_invoice_items.amount) as sales_value'),
                    'brands.id as brand_id'
                )->groupBy('brands.brand_name', 'brands.id');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $brandwiseSales = $query->get();

                $daysDiff = (strtotime($toDate) - strtotime($fromDate)) / (60 * 60 * 24);
                $prevToDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
                $prevFromDate = date('Y-m-d', strtotime($prevToDate . " -$daysDiff days"));

                $prevSales = SalesInvoiceItem::query()
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
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

                $data = [];
                foreach ($brandwiseSales as $sale) {
                    $prevValue = $prevSalesPlucked[$sale->brand_id] ?? 0;
                    if ($prevValue > 0) {
                        $trend = (($sale->sales_value - $prevValue) / $prevValue) * 100;
                    } else {
                        $trend = $sale->sales_value > 0 ? 100 : 0;
                    }

                    $trendHtml = '';
                    if ($trend > 0) {
                        $trendHtml = '<span class="text-success fw-bold"><i class="ri-arrow-up-line align-middle"></i> ' . number_format($trend, 1) . '%</span>';
                    } elseif ($trend < 0) {
                        $trendHtml = '<span class="text-danger fw-bold"><i class="ri-arrow-down-line align-middle"></i> ' . number_format(abs($trend), 1) . '%</span>';
                    } else {
                        $trendHtml = '<span class="text-muted"><i class="ri-subtract-line align-middle"></i> 0.0%</span>';
                    }

                    $data[] = [
                        'brand' => '<strong>' . ($sale->brand ?? '-') . '</strong>',
                        'sold_qty' => number_format($sale->sold_qty ?? 0, 0),
                        'sales_value' => '₹' . number_format($sale->sales_value ?? 0, 2),
                        'trend' => $trendHtml,
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-stock':
                $query = StockEntryItem::query()
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
                    ->leftJoin('fg_min_stocks', function($join) {
                        $join->on('stock_entry_items.id', '=', 'fg_min_stocks.stock_entry_item_id')
                            ->where('fg_min_stocks.status', '=', 'Active')
                            ->whereNull('fg_min_stocks.deleted_at');
                    })
                    ->select(
                        'brands.id as brand_id',
                        'brands.brand_name as brand',
                        DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                        DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                        DB::raw('SUM(IFNULL(fg_min_stocks.min_stock, 0)) as total_min_stock')
                    )
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->whereNull('stock_entry_items.deleted_at');

                if ($request->brand_id) {
                    $query->where('brands.id', $request->brand_id);
                }
                if ($request->store_id) {
                    $query->where('stock_entry_items.store_location_id', $request->store_id);
                }
                
                if ($search) {
                    $query->where('brands.brand_name', 'like', "%{$search}%");
                }

                $query->groupBy('brands.id', 'brands.brand_name');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $brandwiseStock = $query->get();
                $data = [];
                
                foreach ($brandwiseStock as $stock) {
                    $low_stock = max(0, ($stock->total_min_stock ?? 0) - ($stock->total_qty ?? 0));
                    $excess_stock = max(0, ($stock->total_qty ?? 0) - (($stock->total_min_stock ?? 0) * 2));
                    
                    $brandId = $stock->brand_id ?? 0;
                    $brandName = addslashes($stock->brand ?? '-');

                    $data[] = [
                        'DT_RowAttr' => [
                            'onclick' => "drillDownToStyle({$brandId}, '{$brandName}')",
                            'style' => 'cursor: pointer;',
                            'data-brand-id' => $brandId,
                            'data-brand-name' => $stock->brand ?? '-'
                        ],
                        'brand' => '<strong class="text-uppercase">' . ($stock->brand ?? '-') . '</strong>',
                        'total_qty' => number_format($stock->total_qty ?? 0, 0),
                        'low_stock' => '-',
                        'excess_stock' => '-',
                        'stock_days' => '-',
                        'stock_value' => '₹' . number_format($stock->stock_value ?? 0, 2),
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'assorted-stock':
                $query = StockEntryItem::query()
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
                    $query->where('brands.id', $request->brand_id);
                }
                if ($request->store_id) {
                    $query->where('stock_entry_items.store_location_id', $request->store_id);
                }
                
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('store_locations.store_location', 'like', "%{$search}%")
                          ->orWhere('stock_entry_items.finished_item_code', 'like', "%{$search}%")
                          ->orWhere('stock_entry_items.art_no', 'like', "%{$search}%")
                          ->orWhere('stock_entry_items.size', 'like', "%{$search}%");
                    });
                }

                $query->groupBy(
                    'store_locations.store_location',
                    'stock_entry_items.finished_item_code',
                    'stock_entry_items.art_no',
                    'stock_entry_items.size'
                );

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $assortedStock = $query->get();
                $data = [];
                
                foreach ($assortedStock as $stock) {
                    if ($stock->stock_qty != 0) {
                        $data[] = [
                            'store' => $stock->store,
                            'item_name' => '<strong>' . $stock->item_name . '</strong>',
                            'size' => $stock->size ?? '-',
                            'stock_qty' => '<span class="text-primary fw-bold">' . number_format($stock->stock_qty ?? 0, 0) . '</span>',
                        ];
                    }
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'order-dispatch':
                $query = SalesOrder::with(['customer'])
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
                    $query->where('sales_orders.so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('sales_orders.so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
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
                
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('sales_orders.so_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                }

                $query->groupBy('sales_orders.id', 'sales_orders.customer_id', 'sales_orders.so_no', 'sales_orders.so_date', 'sales_orders.total_qty');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $orderVsDispatch = $query->get();
                $data = [];
                
                foreach ($orderVsDispatch as $so) {
                    $pending_qty = max(0, $so->ordered_qty - $so->dispatched_qty);
                    $fulfillment_pc = $so->ordered_qty > 0 ? min(100, ($so->dispatched_qty / $so->ordered_qty) * 100) : 0;
                    
                    $pcClass = 'bg-danger';
                    if($fulfillment_pc >= 90) $pcClass = 'bg-success';
                    elseif($fulfillment_pc >= 50) $pcClass = 'bg-warning';

                    $fulfillmentHtml = '
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="me-2 text-muted" style="font-size: 0.75rem;">' . number_format($fulfillment_pc, 1) . '%</span>
                            <div class="progress" style="height: 6px; width: 60px;">
                                <div class="progress-bar ' . $pcClass . '" role="progressbar" style="width: ' . $fulfillment_pc . '%" aria-valuenow="' . $fulfillment_pc . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>';

                    $data[] = [
                        'customer' => $so->customer->name ?? '-',
                        'so_no' => '<strong>' . $so->so_no . '</strong><br><span class="text-muted" style="font-size: 0.75rem;">' . date('d M Y', strtotime($so->so_date)) . '</span>',
                        'ordered_qty' => number_format($so->ordered_qty, 0),
                        'dispatched_qty' => '<span class="text-primary fw-bold">' . number_format($so->dispatched_qty, 0) . '</span>',
                        'pending_qty' => '<span class="' . ($pending_qty > 0 ? 'text-danger' : 'text-success') . ' fw-bold">' . number_format($pending_qty, 0) . '</span>',
                        'fulfillment' => $fulfillmentHtml
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'dispatch-report':
                $query = SalesOrder::with(['customer.city', 'salesInvoices'])
                    ->where('status', 'Dispatched')
                    ->whereNull('deleted_at');

                if ($request->from_date) {
                    $query->where('so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
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
                
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('so_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                }

                $recordsTotal = $query->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $dispatchReport = $query->orderBy('id', 'desc')->get();
                $data = [];
                
                foreach ($dispatchReport as $so) {
                    $invoiceIds = $so->salesInvoices->pluck('inv_no')->toArray();
                    $invoicesHtml = !empty($invoiceIds) ? '<span class="badge bg-label-info">' . implode(', ', $invoiceIds) . '</span>' : '-';

                    $data[] = [
                        'date' => date('d M Y', strtotime($so->so_date)),
                        'so_no' => '<strong>' . $so->so_no . '</strong>',
                        'customer' => $so->customer->name ?? '-',
                        'destination' => $so->customer->city->city_name ?? '-',
                        'qty' => number_format($so->total_qty, 0),
                        'invoices' => $invoicesHtml,
                        'status' => '<span class="badge bg-label-success"><i class="ri-check-double-line align-middle me-1"></i> Dispatched</span>'
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'stock-inward':
                $query = GrnEntry::with(['supplier'])
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
                    $query->where('grn_entries.grn_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('grn_entries.grn_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->brand_id) {
                    $query->where('brands.id', $request->brand_id);
                }
                
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('grn_entries.grn_number', 'like', "%{$search}%")
                          ->orWhereHas('supplier', function($q2) use ($search) {
                              $q2->where('supplier_name', 'like', "%{$search}%");
                          });
                    });
                }

                $query->groupBy('grn_entries.id', 'grn_entries.grn_number', 'grn_entries.grn_date', 'grn_entries.status', 'grn_entries.supplier_id');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $stockInward = $query->orderBy('grn_entries.grn_date', 'desc')->get();
                $data = [];
                
                foreach ($stockInward as $grn) {
                    $statusClass = 'bg-label-primary';
                    if($grn->status == 'Completed') $statusClass = 'bg-label-success';
                    elseif($grn->status == 'Pending') $statusClass = 'bg-label-warning';

                    $data[] = [
                        'date' => date('d M Y', strtotime($grn->grn_date)),
                        'grn_no' => '<strong>' . $grn->grn_number . '</strong>',
                        'supplier' => $grn->supplier->supplier_name ?? '-',
                        'qty' => number_format($grn->total_qty, 0),
                        'status' => '<span class="badge ' . $statusClass . '">' . $grn->status . '</span>'
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'regular-discount':
                $query = SalesInvoice::query()
                    ->select(
                        DB::raw("DATE_FORMAT(inv_date, '%b %Y') as period"),
                        DB::raw("YEAR(inv_date) as yr"),
                        DB::raw("MONTH(inv_date) as mn"),
                        DB::raw("SUM(CASE WHEN discount = 0 THEN sub_total ELSE 0 END) as regular_sales"),
                        DB::raw("SUM(CASE WHEN discount > 0 THEN sub_total ELSE 0 END) as discount_sales_raw"),
                        DB::raw("SUM(discount) as total_discount"),
                        DB::raw("SUM(sub_total - discount) as net_revenue")
                    )
                    ->whereNull('deleted_at');

                if ($request->from_date) {
                    $query->where('inv_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('inv_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->store_id) {
                    $query->where('store_location_id', $request->store_id);
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
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
                
                if ($search) {
                    $query->where(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"), 'like', "%{$search}%");
                }

                $query->groupBy(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"), DB::raw("YEAR(inv_date)"), DB::raw("MONTH(inv_date)"));

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $regularDiscount = $query->orderBy('yr', 'desc')->orderBy('mn', 'desc')->get();
                $data = [];
                
                foreach ($regularDiscount as $item) {
                    $discount_sales = $item->discount_sales_raw - $item->total_discount;
                    $discount_pc = $item->discount_sales_raw > 0 ? ($item->total_discount / $item->discount_sales_raw) * 100 : 0;
                    
                    $data[] = [
                        'period' => '<strong>' . $item->period . '</strong>',
                        'regular_sales' => '₹' . number_format($item->regular_sales ?? 0, 2),
                        'discount_sales' => '₹' . number_format($discount_sales ?? 0, 2),
                        'discount_pc' => '<span class="text-danger">' . number_format($discount_pc, 1) . '%</span>',
                        'net_revenue' => '<span class="text-success fw-bold">₹' . number_format($item->net_revenue ?? 0, 2) . '</span>'
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'urgent-orders':
                $query = SalesOrder::with(['customer'])
                    ->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                    ->leftJoin('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
                    ->select(
                        'sales_orders.id',
                        'sales_orders.customer_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.delivery_date',
                        'sales_orders.orderaxe_id',
                        'sales_orders.orderaxe_ref_id',
                        'sales_orders.order_no',
                        'sales_orders.status',
                        'sales_orders.total_qty as ordered_qty',
                        DB::raw('COALESCE(SUM(sales_invoice_items.quantity), 0) as dispatched_qty')
                    )
                    ->where('sales_orders.status', 'Pending')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at');

                if ($request->from_date) {
                    $query->where('sales_orders.so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('sales_orders.so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->store_id) {
                    $query->where('sales_orders.store_id', $request->store_id);
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
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

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('sales_orders.so_no', 'like', "%{$search}%")
                          ->orWhere('sales_orders.orderaxe_ref_id', 'like', "%{$search}%")
                          ->orWhere('sales_orders.order_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                }

                $query->groupBy(
                    'sales_orders.id',
                    'sales_orders.customer_id',
                    'sales_orders.so_no',
                    'sales_orders.so_date',
                    'sales_orders.delivery_date',
                    'sales_orders.orderaxe_id',
                    'sales_orders.orderaxe_ref_id',
                    'sales_orders.order_no',
                    'sales_orders.status',
                    'sales_orders.total_qty'
                );

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $urgentOrders = $query->orderBy('sales_orders.id', 'desc')->get();
                $soIds = $urgentOrders->pluck('id')->toArray();

                // Multi-layered Brand Resolution per SO
                $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                $soItemsList = DB::table('sales_order_items')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->select(
                        'sales_order_items.sale_order_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'b_stock.brand_name as stock_brand_name',
                        'brand_categories.name as cat_brand_name'
                    )
                    ->whereIn('sales_order_items.sale_order_id', $soIds)
                    ->whereNull('sales_order_items.deleted_at')
                    ->get();

                $itemsWithBrands = [];
                foreach ($soItemsList as $soItem) {
                    $foundBrand = null;
                    if (!empty($soItem->stock_brand_name)) {
                        $foundBrand = $soItem->stock_brand_name;
                    } elseif (!empty($soItem->cat_brand_name)) {
                        $foundBrand = $soItem->cat_brand_name;
                    } else {
                        $artNo = trim($soItem->art_no ?? '');
                        $sku = trim($soItem->sku ?? '');
                        $itemName = trim($soItem->item_name ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code);
                            $bName = trim($b->brand_name);

                            if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                                ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                                ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                                ($bName && $sku && stripos($sku, $bName) !== false) ||
                                ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                                $foundBrand = $b->brand_name;
                                break;
                            }
                        }
                    }

                    if ($foundBrand) {
                        $itemsWithBrands[$soItem->sale_order_id][$foundBrand] = true;
                    }
                }

                // Stock Map per Art No
                $stockMap = DB::table('stock_entry_items')
                    ->select('art_no', DB::raw('SUM(qty_in - qty_out) as current_stock'))
                    ->where('stock_type', 'finished_goods')
                    ->whereNull('deleted_at')
                    ->groupBy('art_no')
                    ->pluck('current_stock', 'art_no');

                // Fetch items for pending stock check
                $soItems = DB::table('sales_order_items')
                    ->select('sale_order_id', 'art_no', 'qty')
                    ->whereIn('sale_order_id', $soIds)
                    ->whereNull('deleted_at')
                    ->get()
                    ->groupBy('sale_order_id');

                $data = [];
                $today = \Carbon\Carbon::today();

                foreach ($urgentOrders as $so) {
                    $pending_qty = max(0, $so->ordered_qty - $so->dispatched_qty);

                    // Orderaxe Badge & Link
                    $soNoHtml = '<a href="' . url('sales_orders/view/' . $so->id) . '" target="_blank" class="fw-bold text-primary" data-bs-toggle="tooltip" title="Click to view full Sales Order">' . $so->so_no . '</a>';
                    if (!empty($so->orderaxe_id) || !empty($so->orderaxe_ref_id)) {
                        $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
                    }
                    if (!empty($so->orderaxe_ref_id)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->orderaxe_ref_id . '</span>';
                    } elseif (!empty($so->order_no)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->order_no . '</span>';
                    }

                    // Dates & Urgency
                    $orderDateFormatted = date('d M Y', strtotime($so->so_date));
                    $daysPending = $today->diffInDays(\Carbon\Carbon::parse($so->so_date));
                    $daysPendingHtml = '<span class="badge bg-label-warning text-dark fw-bold px-2 py-1" style="font-size: 11px;">' . $daysPending . ' ' . ($daysPending == 1 ? 'Day' : 'Days') . '</span>';

                    $deliveryHtml = '-';
                    if (!empty($so->delivery_date)) {
                        $delDate = \Carbon\Carbon::parse($so->delivery_date);
                        $diffDays = $today->diffInDays($delDate, false);
                        if ($diffDays < 0) {
                            $deliveryHtml = '<span class="text-danger fw-bold">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-danger" style="font-size:10px;">' . abs($diffDays) . ' Days Overdue</span>';
                        } elseif ($diffDays == 0) {
                            $deliveryHtml = '<span class="text-warning fw-bold">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-warning" style="font-size:10px;">Due Today</span>';
                        } else {
                            $deliveryHtml = '<span class="text-dark">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-success" style="font-size:10px;">Due in ' . $diffDays . ' Days</span>';
                        }
                    }

                    // Brands
                    $brandList = isset($itemsWithBrands[$so->id]) ? array_keys($itemsWithBrands[$so->id]) : [];
                    $brandBadges = [];
                    foreach ($brandList as $bName) {
                        $brandBadges[] = '<span class="badge bg-label-primary mb-1 me-1">' . htmlspecialchars($bName) . '</span>';
                    }
                    $brandHtml = !empty($brandBadges) ? implode('', $brandBadges) : '<span class="text-muted">-</span>';

                    $actionHtml = '<a href="' . url('sales_orders/view/' . $so->id) . '" target="_blank" class="btn btn-sm btn-primary rounded-pill"><i class="ri-eye-line me-1"></i>View Order</a>';

                    $data[] = [
                        'so_no' => $soNoHtml,
                        'customer' => $so->customer->name ?? '-',
                        'order_date' => $orderDateFormatted,
                        'days_pending' => $daysPendingHtml,
                        'delivery_date' => $deliveryHtml,
                        'brands' => $brandHtml,
                        'ordered_qty' => number_format($so->ordered_qty, 0),
                        'action' => $actionHtml
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-lost-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;

                $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                // 1. Fetch sales order items
                $orderQuery = DB::table('sales_order_items')
                    ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->select(
                        'sales_orders.id as so_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'sales_order_items.qty',
                        'sales_order_items.amount',
                        'b_stock.brand_name as stock_brand_name',
                        'b_stock.id as stock_brand_id',
                        'brand_categories.name as cat_brand_name'
                    )
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_order_items.deleted_at');

                if ($fromDate) {
                    $orderQuery->where('sales_orders.so_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $orderQuery->where('sales_orders.so_date', '<=', $toDate);
                }
                if ($request->store_id) {
                    $orderQuery->where('sales_orders.store_id', $request->store_id);
                }

                $orderItems = $orderQuery->get();

                // Group ordered items by brand_name and so_id
                $brandSoTotals = [];
                $soIdsList = [];

                foreach ($orderItems as $item) {
                    $brandName = null;
                    $brandId = null;

                    if (!empty($item->stock_brand_name)) {
                        $brandName = $item->stock_brand_name;
                        $brandId = $item->stock_brand_id;
                    } elseif (!empty($item->cat_brand_name)) {
                        $brandName = $item->cat_brand_name;
                    } else {
                        $artNo = trim($item->art_no ?? '');
                        $sku = trim($item->sku ?? '');
                        $itemName = trim($item->item_name ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code);
                            $bName = trim($b->brand_name);

                            if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                                ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                                ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                                ($bName && $sku && stripos($sku, $bName) !== false) ||
                                ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                                $brandName = $b->brand_name;
                                $brandId = $b->id;
                                break;
                            }
                        }
                    }

                    if (!$brandName) {
                        $brandName = 'Uncategorized';
                    }

                    if ($request->brand_id && $brandId != $request->brand_id) {
                        continue;
                    }

                    $soId = $item->so_id;
                    $soIdsList[] = $soId;

                    if (!isset($brandSoTotals[$brandName][$soId])) {
                        $brandSoTotals[$brandName][$soId] = ['ordered_qty' => 0, 'ordered_value' => 0];
                    }
                    $brandSoTotals[$brandName][$soId]['ordered_qty'] += floatval($item->qty ?? 0);
                    $brandSoTotals[$brandName][$soId]['ordered_value'] += floatval($item->amount ?? 0);
                }

                // 2. Fetch invoiced totals per SO
                $soInvoiceMap = [];
                if (!empty($soIdsList)) {
                    $invoices = DB::table('sales_invoice_items')
                        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                        ->whereIn('sales_invoices.so_id', array_unique($soIdsList))
                        ->whereNull('sales_invoices.deleted_at')
                        ->whereNull('sales_invoice_items.deleted_at')
                        ->select(
                            'sales_invoices.so_id',
                            DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty'),
                            DB::raw('SUM(sales_invoice_items.amount) as invoiced_value')
                        )
                        ->groupBy('sales_invoices.so_id')
                        ->get();

                    foreach ($invoices as $inv) {
                        $soInvoiceMap[$inv->so_id] = [
                            'invoiced_qty' => floatval($inv->invoiced_qty ?? 0),
                            'invoiced_value' => floatval($inv->invoiced_value ?? 0),
                        ];
                    }
                }

                // Build Summary Data array grouped by Brand
                $data = [];
                foreach ($brandSoTotals as $bName => $soList) {
                    if ($search && stripos($bName, $search) === false) {
                        continue;
                    }

                    $missedOrdersCount = 0;
                    $totalLostValue = 0;

                    foreach ($soList as $soId => $totals) {
                        $ordQty = $totals['ordered_qty'];
                        $ordVal = $totals['ordered_value'];
                        $invQty = floatval($soInvoiceMap[$soId]['invoiced_qty'] ?? 0);
                        $invVal = floatval($soInvoiceMap[$soId]['invoiced_value'] ?? 0);

                        $lostQty = max(0, $ordQty - $invQty);
                        $lostVal = max(0, $ordVal - $invVal);

                        if ($lostQty > 0 || $ordQty > $invQty) {
                            $missedOrdersCount++;
                            $totalLostValue += $lostVal;
                        }
                    }

                    if ($missedOrdersCount > 0 || $totalLostValue > 0) {
                        $data[] = [
                            'DT_RowAttr' => [
                                'onclick' => "drillDownToLostSalesBrand('" . addslashes($bName) . "')",
                                'style' => 'cursor: pointer;',
                                'class' => 'clickable-brand-row'
                            ],
                            'brand' => '<strong class="text-uppercase text-primary">' . htmlspecialchars($bName) . ' <i class="ri-arrow-right-s-line ms-1 small"></i></strong>',
                            'missed_orders' => '<span class="badge bg-label-danger px-3 py-1 fs-6 fw-bold">' . number_format($missedOrdersCount, 0) . '</span>',
                            'lost_value' => '<span class="text-danger fw-bold fs-6">₹' . number_format($totalLostValue, 2) . '</span>'
                        ];
                    }
                }

                $recordsTotal = count($data);
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $data = array_slice($data, $start, $length);
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-completion':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
                $brandIdFilter = $request->brand_id;
                $storeIdFilter = $request->store_id;
                $searchValue = $request->search['value'] ?? null;

                $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                $query = DB::table('sales_orders')
                    ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
                    ->leftJoin('places', 'customers.place_id', '=', 'places.id')
                    ->leftJoin('cities', 'customers.city_id', '=', 'cities.id')
                    ->select(
                        'sales_orders.id as so_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.delivery_date',
                        'sales_orders.order_type',
                        'sales_orders.order_no',
                        'sales_orders.reason_for_delay',
                        'sales_orders.internal_remarks',
                        'customers.name as customer_name',
                        'places.place_name as place_name',
                        'cities.city_name as city_name'
                    )
                    ->whereNull('sales_orders.deleted_at');

                if ($fromDate) {
                    $query->where('sales_orders.so_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->where('sales_orders.so_date', '<=', $toDate);
                }
                if ($storeIdFilter) {
                    $query->where('sales_orders.store_id', $storeIdFilter);
                }
                if ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('sales_orders.so_no', 'LIKE', "%{$searchValue}%")
                          ->orWhere('customers.name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('places.place_name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('cities.city_name', 'LIKE', "%{$searchValue}%");
                    });
                }

                $recordsTotal = DB::table('sales_orders')->whereNull('deleted_at')->count();
                $recordsFiltered = $query->count();

                $salesOrders = $query->orderBy('sales_orders.so_date', 'desc')
                    ->skip($start)
                    ->take($length > 0 ? $length : 10)
                    ->get();

                $soIds = $salesOrders->pluck('so_id')->toArray();

                $soItemsMap = [];
                if (!empty($soIds)) {
                    $items = DB::table('sales_order_items')
                        ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                        ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                        ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                        ->select(
                            'sales_order_items.sale_order_id',
                            'sales_order_items.art_no',
                            'sales_order_items.sku',
                            'sales_order_items.item_name',
                            'sales_order_items.qty',
                            'b_stock.brand_name as stock_brand_name',
                            'b_stock.id as stock_brand_id',
                            'brand_categories.name as cat_brand_name'
                        )
                        ->whereIn('sales_order_items.sale_order_id', $soIds)
                        ->whereNull('sales_order_items.deleted_at')
                        ->get();

                    foreach ($items as $itm) {
                        $bName = null;
                        $bId = null;
                        if (!empty($itm->stock_brand_name)) {
                            $bName = $itm->stock_brand_name;
                            $bId = $itm->stock_brand_id;
                        } elseif (!empty($itm->cat_brand_name)) {
                            $bName = $itm->cat_brand_name;
                        } else {
                            $artNo = trim($itm->art_no ?? '');
                            $sku = trim($itm->sku ?? '');
                            $itemName = trim($itm->item_name ?? '');
                            foreach ($allBrands as $b) {
                                $bCode = trim($b->code);
                                $bNameCandidate = trim($b->brand_name);
                                if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                                    ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                                    ($bNameCandidate && $artNo && stripos($artNo, $bNameCandidate) !== false) ||
                                    ($bNameCandidate && $sku && stripos($sku, $bNameCandidate) !== false) ||
                                    ($bNameCandidate && $itemName && stripos($itemName, $bNameCandidate) !== false)) {
                                    $bName = $b->brand_name;
                                    $bId = $b->id;
                                    break;
                                }
                            }
                        }

                        if (!$bName) {
                            $bName = 'Uncategorized';
                        }

                        if (!isset($soItemsMap[$itm->sale_order_id])) {
                            $soItemsMap[$itm->sale_order_id] = [
                                'ordered_qty' => 0,
                                'brands' => [],
                                'art_nos' => []
                            ];
                        }

                        $soItemsMap[$itm->sale_order_id]['ordered_qty'] += floatval($itm->qty ?? 0);
                        $soItemsMap[$itm->sale_order_id]['brands'][$bName] = true;
                        if (!empty($itm->art_no)) {
                            $soItemsMap[$itm->sale_order_id]['art_nos'][$itm->art_no] = ($soItemsMap[$itm->sale_order_id]['art_nos'][$itm->art_no] ?? 0) + floatval($itm->qty ?? 0);
                        }
                    }
                }

                $soInvoiceQtyMap = [];
                if (!empty($soIds)) {
                    $invoices = DB::table('sales_invoice_items')
                        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                        ->whereIn('sales_invoices.so_id', $soIds)
                        ->whereNull('sales_invoices.deleted_at')
                        ->whereNull('sales_invoice_items.deleted_at')
                        ->select(
                            'sales_invoices.so_id',
                            DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty')
                        )
                        ->groupBy('sales_invoices.so_id')
                        ->get();

                    foreach ($invoices as $inv) {
                        $soInvoiceQtyMap[$inv->so_id] = floatval($inv->invoiced_qty ?? 0);
                    }
                }

                $data = [];
                $sno = $start + 1;

                foreach ($salesOrders as $so) {
                    $orderedQty = isset($soItemsMap[$so->so_id]) ? $soItemsMap[$so->so_id]['ordered_qty'] : 0;
                    $invoicedQty = $soInvoiceQtyMap[$so->so_id] ?? 0;
                    $pendingQty = max(0, $orderedQty - $invoicedQty);
                    $compltdPercent = $orderedQty > 0 ? min(100, round(($invoicedQty / $orderedQty) * 100, 1)) : 0;

                    if ($invoicedQty >= $orderedQty && $orderedQty > 0) {
                        $statusHtml = '<span class="badge bg-success">COMPLETED</span>';
                    } elseif ($invoicedQty > 0) {
                        $statusHtml = '<span class="badge" style="background-color: #f8d7da; color: #842029;">PARTIAL DELIVERY</span>';
                    } else {
                        $statusHtml = '<span class="badge" style="background-color: #f8d7da; color: #842029;">PENDING</span>';
                    }

                    $brandNames = isset($soItemsMap[$so->so_id]) ? array_keys($soItemsMap[$so->so_id]['brands']) : [];
                    $brandStr = !empty($brandNames) ? implode(', ', $brandNames) : '-';

                    if ($pendingQty > 0) {
                        $artNoActionHtml = '<button class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill" style="font-size:11px;" onclick="drillDownToCompletionArtNos(' . $so->so_id . ', \'' . htmlspecialchars($so->so_no) . '\')"><i class="ri-list-check me-1"></i>View Art Nos</button>';
                    } else {
                        $artNoActionHtml = '<span class="text-muted">-</span>';
                    }

                    $remarks = $so->reason_for_delay ?: '-';

                    $data[] = [
                        'sno' => $sno++,
                        'order_date' => date('d-m-Y', strtotime($so->so_date)),
                        'order_no' => $so->so_no,
                        'delivery_date' => $so->delivery_date ? date('d-m-Y', strtotime($so->delivery_date)) : '-',
                        'priority' => strtoupper($so->order_type ?: 'REGULAR'),
                        'customer' => $so->customer_name ?? '-',
                        'place' => $so->place_name ?: ($so->city_name ?: '-'),
                        'brand' => $brandStr,
                        'order_qty' => number_format($orderedQty, 0),
                        'invoiced_qty' => number_format($invoicedQty, 0),
                        'pending_qty' => number_format($pendingQty, 0),
                        'compltd_percent' => '<span class="fw-bold ' . ($compltdPercent >= 100 ? 'text-success' : 'text-danger') . '">' . $compltdPercent . '%</span>',
                        'status' => $statusHtml,
                        'awaiting_art_no' => $artNoActionHtml,
                        'action_required' => htmlspecialchars($remarks)
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);
        }
    }

    public function getBrandwiseCompletionArtNos(Request $request)
    {
        $soId = $request->so_id;

        $items = DB::table('sales_order_items')
            ->select('art_no', 'sku', 'item_name', 'qty')
            ->where('sale_order_id', $soId)
            ->whereNull('deleted_at')
            ->get();

        $invoicedArtNos = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.so_id', $soId)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select(
                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
            )
            ->groupBy('art_no')
            ->pluck('inv_qty', 'art_no');

        $artNosData = [];
        foreach ($items as $item) {
            $artNo = $item->art_no;
            $ordQty = floatval($item->qty ?? 0);
            $invQty = floatval($invoicedArtNos[$artNo] ?? 0);
            $pendingQty = max(0, $ordQty - $invQty);

            if ($pendingQty <= 0) {
                continue;
            }

            if ($invQty > 0) {
                $status = '<span class="badge bg-warning text-dark">Partial</span>';
            } else {
                $status = '<span class="badge bg-danger">Pending</span>';
            }

            $artNosData[] = [
                'art_no' => $artNo ?: '-',
                'item_name' => $item->item_name ?: ($item->sku ?: '-'),
                'ordered_qty' => number_format($ordQty, 0),
                'invoiced_qty' => number_format($invQty, 0),
                'pending_qty' => number_format($pendingQty, 0),
                'status' => $status
            ];
        }

        return response()->json([
            'status' => true,
            'items' => $artNosData
        ]);
    }

    public function getBrandwiseLostSalesOrders(Request $request)
    {
        $brandName = $request->brand_name;
        $storeId = $request->store_id;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;

        $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

        $orderQuery = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
            ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
            ->select(
                'sales_orders.id as so_id',
                'sales_orders.so_no',
                'sales_orders.orderaxe_id',
                'sales_orders.orderaxe_ref_id',
                'sales_orders.order_no',
                'sales_orders.so_date',
                'sales_orders.reason_for_delay',
                'sales_orders.internal_remarks',
                'customers.name as customer_name',
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'sales_order_items.qty',
                'sales_order_items.amount',
                'b_stock.brand_name as stock_brand_name',
                'brand_categories.name as cat_brand_name'
            )
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_order_items.deleted_at');

        if ($fromDate) {
            $orderQuery->where('sales_orders.so_date', '>=', $fromDate);
        }
        if ($toDate) {
            $orderQuery->where('sales_orders.so_date', '<=', $toDate);
        }
        if ($storeId) {
            $orderQuery->where('sales_orders.store_id', $storeId);
        }

        $orderItems = $orderQuery->get();

        $soMap = [];
        $matchedSoIds = [];

        foreach ($orderItems as $item) {
            $resolvedBrand = null;
            if (!empty($item->stock_brand_name)) {
                $resolvedBrand = $item->stock_brand_name;
            } elseif (!empty($item->cat_brand_name)) {
                $resolvedBrand = $item->cat_brand_name;
            } else {
                $artNo = trim($item->art_no ?? '');
                $sku = trim($item->sku ?? '');
                $itemName = trim($item->item_name ?? '');

                foreach ($allBrands as $b) {
                    $bCode = trim($b->code);
                    $bName = trim($b->brand_name);
                    if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                        ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                        ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                        ($bName && $sku && stripos($sku, $bName) !== false) ||
                        ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                        $resolvedBrand = $b->brand_name;
                        break;
                    }
                }
            }

            if (!$resolvedBrand) {
                $resolvedBrand = 'Uncategorized';
            }

            if ($brandName && strtolower($resolvedBrand) !== strtolower($brandName)) {
                continue;
            }

            $soId = $item->so_id;
            $matchedSoIds[] = $soId;

            if (!isset($soMap[$soId])) {
                $reasonText = !empty($item->reason_for_delay) ? trim($item->reason_for_delay) : '-';

                $soMap[$soId] = [
                    'so_id' => $soId,
                    'so_no' => $item->so_no,
                    'orderaxe_ref_id' => $item->orderaxe_ref_id ?? $item->order_no,
                    'is_orderaxe' => !empty($item->orderaxe_id) || !empty($item->orderaxe_ref_id),
                    'customer' => $item->customer_name ?? '-',
                    'so_date' => date('d M Y', strtotime($item->so_date)),
                    'reason' => $reasonText,
                    'ordered_qty' => 0,
                    'ordered_value' => 0,
                ];
            }

            $soMap[$soId]['ordered_qty'] += floatval($item->qty ?? 0);
            $soMap[$soId]['ordered_value'] += floatval($item->amount ?? 0);
        }

        $invoiceItemMap = [];
        if (!empty($matchedSoIds)) {
            $invoices = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->whereIn('sales_invoices.so_id', array_unique($matchedSoIds))
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->select('sales_invoices.so_id', DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty'), DB::raw('SUM(sales_invoice_items.amount) as invoiced_value'))
                ->groupBy('sales_invoices.so_id')
                ->get();

            foreach ($invoices as $inv) {
                $invoiceItemMap[$inv->so_id] = [
                    'invoiced_qty' => floatval($inv->invoiced_qty ?? 0),
                    'invoiced_value' => floatval($inv->invoiced_value ?? 0),
                ];
            }
        }

        $result = [];
        foreach ($soMap as $soId => $data) {
            $invQty = floatval($invoiceItemMap[$soId]['invoiced_qty'] ?? 0);
            $invVal = floatval($invoiceItemMap[$soId]['invoiced_value'] ?? 0);

            $lostQty = max(0, $data['ordered_qty'] - $invQty);
            $lostVal = max(0, $data['ordered_value'] - $invVal);

            if ($lostQty > 0 || $data['ordered_qty'] > $invQty) {
                $soNoHtml = '<a href="' . url('sales_orders/view/' . $soId) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($data['so_no']) . '</a>';
                if ($data['is_orderaxe']) {
                    $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
                }

                $reasonHtml = $data['reason'] !== '-' ? htmlspecialchars($data['reason']) : '<span class="text-muted">-</span>';

                $result[] = [
                    'so_no' => $soNoHtml,
                    'customer' => htmlspecialchars($data['customer']),
                    'so_date' => $data['so_date'],
                    'ordered_qty' => number_format($data['ordered_qty'], 0),
                    'invoiced_qty' => '<span class="text-primary fw-bold">' . number_format($invQty, 0) . '</span>',
                    'lost_qty' => '<span class="text-danger fw-bold">' . number_format($lostQty, 0) . '</span>',
                    'lost_value' => '<span class="text-danger fw-bold">₹' . number_format($lostVal, 2) . '</span>',
                    'reason' => $reasonHtml,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'brand_name' => $brandName,
            'orders' => array_values($result)
        ]);
    }

    public function getUrgentOrderDetails($id)
    {
        $so = SalesOrder::with(['customer', 'items.stockEntryItem', 'items.size', 'items.color'])->find($id);

        if (!$so) {
            return response()->json(['status' => false, 'message' => 'Sales Order not found'], 404);
        }

        // Get dispatched qty per item for this SO
        $dispatchedItemMap = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.so_id', $id)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select('sales_invoice_items.art_no', DB::raw('SUM(sales_invoice_items.quantity) as total_dispatched'))
            ->groupBy('sales_invoice_items.art_no')
            ->pluck('total_dispatched', 'art_no');

        // Stock Map per art_no
        $stockMap = DB::table('stock_entry_items')
            ->select('art_no', DB::raw('SUM(qty_in - qty_out) as current_stock'))
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->groupBy('art_no')
            ->pluck('current_stock', 'art_no');

        $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

        $items = [];
        foreach ($so->items as $item) {
            $artNo = trim($item->art_no ?? '');
            $sku = trim($item->sku ?? '');
            $itemName = trim($item->item_name ?? '');
            $ordered = floatval($item->qty);
            $dispatched = floatval($dispatchedItemMap[$artNo] ?? 0);
            $pending = max(0, $ordered - $dispatched);
            $whStock = floatval($stockMap[$artNo] ?? 0);

            // Brand lookup for item
            $brandName = '-';
            if ($item->stockEntryItem && $item->stockEntryItem->brand) {
                $brandName = $item->stockEntryItem->brand->brand_name;
            } elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            } else {
                foreach ($allBrands as $b) {
                    $bCode = trim($b->code);
                    $bName = trim($b->brand_name);
                    if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                        ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                        ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                        ($bName && $sku && stripos($sku, $bName) !== false) ||
                        ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                        $brandName = $b->brand_name;
                        break;
                    }
                }
            }

            $items[] = [
                'art_no' => $artNo ?: '-',
                'item_name' => $item->item_name ?? '-',
                'brand_name' => $brandName,
                'size' => $item->size->size_name ?? ($item->size_id ?? '-'),
                'color' => $item->color->color_name ?? ($item->color_id ?? '-'),
                'ordered_qty' => number_format($ordered, 0),
                'dispatched_qty' => number_format($dispatched, 0),
                'pending_qty' => number_format($pending, 0),
                'wh_stock' => number_format($whStock, 0),
                'stock_status' => $whStock >= $pending ? '<span class="badge bg-label-success">Available</span>' : ($whStock > 0 ? '<span class="badge bg-label-warning">Partial</span>' : '<span class="badge bg-label-danger">Out of Stock</span>')
            ];
        }

        return response()->json([
            'status' => true,
            'so_no' => $so->so_no,
            'orderaxe_ref_id' => $so->orderaxe_ref_id ?? $so->order_no,
            'customer_name' => $so->customer->name ?? '-',
            'so_date' => date('d M Y', strtotime($so->so_date)),
            'delivery_date' => $so->delivery_date ? date('d M Y', strtotime($so->delivery_date)) : '-',
            'status_name' => $so->status,
            'items' => $items
        ]);
    }
}
