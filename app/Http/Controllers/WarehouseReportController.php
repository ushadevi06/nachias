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
use App\Models\Warehouse;
use App\Models\WarehouseBrandCapacity;
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
        $stores = \App\Models\StoreType::where('status', 'Active')->get();
        $warehouses = Warehouse::where('status', 'Active')->orderBy('id', 'desc')->get();
        $defaultWarehouseId = $warehouses->first()?->id;

        if ($request->ajax()) {
            $activeTab = $request->get('active_tab');

            if ($activeTab == 'warehouse-summary') {
                $selectedWarehouseId = $request->get('warehouse_id', $defaultWarehouseId);
                
                $capacities = WarehouseBrandCapacity::where('warehouse_id', $selectedWarehouseId)->where('status', 'Active')->pluck('capacity_pcs', 'brand_id')->toArray();

                $activeBrands = Brand::where('status', 'Active')->orderBy('id','desc')->get();
                $reportData = [];

                $totalCapacity = 0;
                $totalSetwise = 0;
                $totalSingle = 0;
                $totalTotalStock = 0;
                $totalDamage = 0;

                foreach ($activeBrands as $brand) {
                    $capacityPcs = $capacities[$brand->id] ?? 0;

                    // Fetch all finished goods stock items for this brand and warehouse
                    $stockItems = StockEntryItem::where('brand_id', $brand->id)
                        ->where(function($q) use ($selectedWarehouseId) {
                            $q->where('warehouse_id', $selectedWarehouseId)
                              ->orWhereHas('stockEntry', function($se) use ($selectedWarehouseId) {
                                  $se->where('warehouse_id', $selectedWarehouseId);
                              });
                        })
                        ->with('stockEntry.productionReceipt')
                        ->get();

                    $setwiseStock = 0;
                    $singleStoreStock = 0;
                    $damageStock = 0;

                    foreach ($stockItems as $item) {
                        $netQty = (float)($item->qty_in - $item->qty_out);
                        $storeTypeId = $item->stockEntry?->productionReceipt?->store_type_id ?? $item->store_category_id;

                        if ($storeTypeId == 12) {
                            // Single Store Stock (Store Type ID 12)
                            $singleStoreStock += $netQty;
                        } elseif ($storeTypeId == 6) {
                            // Damage Store Stock (Store Type ID 6)
                            $damageStock += $netQty;
                        } else {
                            // Setwise Stock (Store Type ID 3 or Default Finished Goods)
                            $setwiseStock += $netQty;
                        }
                    }

                    $totalStock = $setwiseStock + $singleStoreStock;
                    $utilizationPct = $capacityPcs > 0 ? round(($totalStock / $capacityPcs) * 100, 2) : 0;

                    $totalCapacity += $capacityPcs;
                    $totalSetwise += $setwiseStock;
                    $totalSingle += $singleStoreStock;
                    $totalTotalStock += $totalStock;
                    $totalDamage += $damageStock;

                    $reportData[] = [
                        'brand_name' => $brand->brand_name,
                        'capacity_pcs' => $capacityPcs,
                        'setwise_stock' => $setwiseStock,
                        'single_store_stock' => $singleStoreStock,
                        'total_stock' => $totalStock,
                        'utilization_pct' => $utilizationPct,
                        'damage_stock' => $damageStock,
                    ];
                }

                $totalUtilizationPct = $totalCapacity > 0 ? round(($totalTotalStock / $totalCapacity) * 100, 2) : 0;

                $totals = [
                    'capacity_pcs' => $totalCapacity,
                    'setwise_stock' => $totalSetwise,
                    'single_store_stock' => $totalSingle,
                    'total_stock' => $totalTotalStock,
                    'utilization_pct' => $totalUtilizationPct,
                    'damage_stock' => $totalDamage,
                ];

                return response()->json([
                    'warehouse-summary' => view('reports.warehouse_report.warehouse_summary', compact('reportData', 'totals'))->render()
                ]);
            }

            $tabViews = [
                'brand-stock' => 'reports.warehouse_report.brandwise_stock',
                'brandwise-stock' => 'reports.warehouse_report.brandwise_stock',
                'brand-sales' => 'reports.warehouse_report.brandwise_sales',
                'brandwise-sales' => 'reports.warehouse_report.brandwise_sales',
                'assorted-stock' => 'reports.warehouse_report.assorted_stock',
                'order-dispatch' => 'reports.warehouse_report.order_vs_dispatch',
                'sales-return' => 'reports.warehouse_report.sales_return',
                'white-dhoti' => 'reports.warehouse_report.white_dhoti',
                'dispatch' => 'reports.warehouse_report.dispatch_report',
                'inward' => 'reports.warehouse_report.stock_inward',
                'discount' => 'reports.warehouse_report.regular_discount',
                'damage' => 'reports.warehouse_report.damage_sales_split',
                'urgent-orders' => 'reports.warehouse_report.urgent_orders',
                'brandwise-lost-sales' => 'reports.warehouse_report.brandwise_lost_sales',
                'stock-inward-sales' => 'reports.warehouse_report.stock_inward_sales',
            ];

            if ($activeTab && isset($tabViews[$activeTab])) {
                return response()->json([
                    $activeTab => view($tabViews[$activeTab])->render()
                ]);
            }

            $priorityStock = collect([]);
            if ($activeTab == 'priority' || !$activeTab) {
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
                    $priorityStockQuery->where('brand_id', $request->brand_id);
                }
                if ($request->store_id) {
                    $priorityStockQuery->where('store_location_id', $request->store_id);
                }

                $priorityStock = $priorityStockQuery->groupBy('art_no')->having('current_stock', '>', 0)
                    ->having('ageing_days', '>=', 90)
                    ->orderBy('ageing_days', 'desc')
                    ->get();

                if ($activeTab == 'priority') {
                    return response()->json([
                        'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render()
                    ]);
                }
            }

            return response()->json([
                'sales-return' => view('reports.warehouse_report.sales_return')->render(),
                'white-dhoti' => view('reports.warehouse_report.white_dhoti')->render(),
                'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render(),
                'damage' => view('reports.warehouse_report.damage_sales_split')->render(),
                'urgent-orders' => view('reports.warehouse_report.urgent_orders')->render(),
                'brandwise-lost-sales' => view('reports.warehouse_report.brandwise_lost_sales')->render(),
                'stock-inward-sales' => view('reports.warehouse_report.stock_inward_sales')->render(),
            ]);
        }

        $priorityStock = collect([]);

        return view('reports/warehouse_report', compact(
            'priorityStock',
            'brands',
            'stores',
            'warehouses',
            'defaultWarehouseId'
        ));
    }

    public function getBrandwiseStyles(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length > 0 ? $request->length : 10);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        
        $stockQuery = StockEntryItem::query()
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                DB::raw('COALESCE(styles.id, 0) as style_id'),
                DB::raw("COALESCE(styles.style_name, '(No Style)') as style_name"),
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId !== null && $brandId !== '' && $brandId != 0) {
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
        } elseif ($brandId === 0 || $brandId === '0') {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        if ($search) {
            $stockQuery->where('styles.style_name', 'like', "%{$search}%");
        }

        $stockQuery->groupBy(DB::raw('COALESCE(styles.id, 0)'), DB::raw("COALESCE(styles.style_name, '(No Style)')"));

        $recordsTotal = DB::query()->fromSub($stockQuery, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        if ($length != -1) {
            $stockQuery->skip($start)->take($length);
        }

        $styles = $stockQuery->get();

        if ($draw) {
            $data = [];
            $brandName = $request->brand_name ?? '';
            foreach ($styles as $item) {
                $sId = $item->style_id;
                $sName = addslashes($item->style_name);
                $bId = $brandId ?? 0;
                $bNameEsc = addslashes($brandName);

                $data[] = [
                    'DT_RowAttr' => [
                        'onclick' => "drillDownToArtNo({$bId}, '{$bNameEsc}', {$sId}, '{$sName}')",
                        'style' => 'cursor: pointer;'
                    ],
                    'brand' => '<strong class="text-uppercase">' . htmlspecialchars($item->style_name) . '</strong>',
                    'total_qty' => number_format($item->total_qty ?? 0, 0),
                    'low_stock' => '-',
                    'excess_stock' => '-',
                    'stock_days' => '-',
                    'stock_value' => '₹' . number_format($item->stock_value ?? 0, 2),
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        }

        return response()->json($styles);
    }

    public function getBrandwiseArtNos(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length > 0 ? $request->length : 10);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $styleId = $request->style_id;
        
        $stockQuery = StockEntryItem::query()
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
                DB::raw('SUM(IFNULL(fg_min_stocks.max_stock, 0)) as total_max_stock'),
                DB::raw('MIN(CASE WHEN (stock_entry_items.qty_in - stock_entry_items.qty_out) > 0 THEN stock_entry_items.created_at ELSE NULL END) as oldest_stock_date')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($styleId !== null && $styleId !== '') {
            $stockQuery->where('stock_entry_items.style_id', $styleId);
        }

        if ($brandId !== null && $brandId !== '' && $brandId != 0) {
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
        } elseif ($brandId === 0 || $brandId === '0') {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        if ($search) {
            $stockQuery->where('stock_entry_items.art_no', 'like', "%{$search}%");
        }

        $stockQuery->groupBy('stock_entry_items.art_no');

        $recordsTotal = DB::query()->fromSub($stockQuery, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        if ($length != -1) {
            $stockQuery->skip($start)->take($length);
        }

        $artNos = $stockQuery->get();

        if ($draw) {
            $data = [];
            foreach ($artNos as $artNo) {
                $displayQty = max(0, floatval($artNo->total_qty ?? 0));
                $totalMinStock = floatval($artNo->total_min_stock ?? 0);
                $totalMaxStock = floatval($artNo->total_max_stock ?? 0);

                $lowStock = ($totalMinStock > 0 && $displayQty < $totalMinStock) ? ($totalMinStock - $displayQty) : 0;
                $excessStock = ($totalMaxStock > 0 && $displayQty > $totalMaxStock) ? ($displayQty - $totalMaxStock) : 0;
                $stockValue = max(0, floatval($artNo->stock_value ?? 0));

                $stockDays = '-';
                if ($artNo->oldest_stock_date) {
                    $stockDays = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($artNo->oldest_stock_date));
                }

                $lowStockDisplay = $lowStock > 0 ? '<span class="text-danger fw-bold">' . number_format($lowStock, 0) . '</span>' : '-';
                $excessStockDisplay = $excessStock > 0 ? '<span class="text-success fw-bold">' . number_format($excessStock, 0) . '</span>' : '-';

                $data[] = [
                    'brand' => '<strong class="text-uppercase">' . htmlspecialchars($artNo->art_no ?? '-') . '</strong>',
                    'total_qty' => number_format($displayQty, 0),
                    'low_stock' => $lowStockDisplay,
                    'excess_stock' => $excessStockDisplay,
                    'stock_days' => $stockDays,
                    'stock_value' => '₹' . number_format($stockValue, 2),
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        }

        return response()->json($artNos);
    }


    public function ajaxReportData(Request $request, $type)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length ? $request->length : 10);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        switch ($type) {
            case 'stock-inward-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
                $brandIdFilter = $request->brand_id;
                $storeIdFilter = $request->store_id;

                $brandsQuery = Brand::where('status', 'Active');
                if ($brandIdFilter) {
                    $brandsQuery->where('id', $brandIdFilter);
                }
                if ($search) {
                    $brandsQuery->where('brand_name', 'like', "%{$search}%");
                }
                $brands = $brandsQuery->get();

                $inwardQuery = DB::table('stock_entry_items')
                    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
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
                    ->where('stock_entry_items.qty_in', '>', 0)
                    ->whereNull('stock_entry_items.deleted_at')
                    ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $inwardQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $inwardQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }

                $inwardPerBrand = $inwardQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
                )->groupBy('brands.id')->pluck('inward_qty', 'brand_id');

                // Sales Qty in Date Range
                $salesQuery = DB::table('sales_invoice_items')
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
                    ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $salesQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $salesQuery->where('sales_invoices.store_location_id', $storeIdFilter);
                }

                $salesPerBrand = $salesQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
                )->groupBy('brands.id')->pluck('sales_qty', 'brand_id');

                // Sales Return Qty in Date Range
                $returnQuery = DB::table('credit_note_items')
                    ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                    ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
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
                    ->whereNull('credit_notes.deleted_at')
                    ->whereNull('credit_note_items.deleted_at')
                    ->whereBetween('credit_notes.note_date', [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $returnQuery->where('brands.id', $brandIdFilter);
                }

                $returnPerBrand = $returnQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(credit_note_items.quantity) as return_qty')
                )->groupBy('brands.id')->pluck('return_qty', 'brand_id');

                // Current Live Stock per Brand
                $currentStockQuery = DB::table('stock_entry_items')
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
                    ->whereNull('stock_entry_items.deleted_at');

                if ($brandIdFilter) {
                    $currentStockQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $currentStockQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }

                $currentStockPerBrand = $currentStockQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty')
                )->groupBy('brands.id')->pluck('total_qty', 'brand_id');

                // Inward from fromDate till today
                $inwardFromDateTillNowQuery = DB::table('stock_entry_items')
                    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
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
                    ->where('stock_entry_items.qty_in', '>', 0)
                    ->whereNull('stock_entry_items.deleted_at')
                    ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate);

                if ($brandIdFilter) {
                    $inwardFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $inwardFromDateTillNowQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }
                $inwardTillNowPerBrand = $inwardFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
                )->groupBy('brands.id')->pluck('inward_qty', 'brand_id');

                // Sales from fromDate till today
                $salesFromDateTillNowQuery = DB::table('sales_invoice_items')
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
                    ->where('sales_invoices.inv_date', '>=', $fromDate);

                if ($brandIdFilter) {
                    $salesFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $salesFromDateTillNowQuery->where('sales_invoices.store_location_id', $storeIdFilter);
                }
                $salesTillNowPerBrand = $salesFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
                )->groupBy('brands.id')->pluck('sales_qty', 'brand_id');

                $returnsFromDateTillNowQuery = DB::table('credit_note_items')
                    ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                    ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
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
                    ->whereNull('credit_notes.deleted_at')
                    ->whereNull('credit_note_items.deleted_at')
                    ->where('credit_notes.note_date', '>=', $fromDate);

                if ($brandIdFilter) {
                    $returnsFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                $returnsTillNowPerBrand = $returnsFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(credit_note_items.quantity) as return_qty')
                )->groupBy('brands.id')->pluck('return_qty', 'brand_id');

                $data = [];
                $totOpStock = 0;
                $totInward = 0;
                $totSales = 0;
                $totClosing = 0;
                $totSalesReturn = 0;
                $totNetClosing = 0;

                foreach ($brands as $brand) {
                    $bId = $brand->id;

                    $currStock = floatval($currentStockPerBrand[$bId] ?? 0);
                    $inwardTillNow = floatval($inwardTillNowPerBrand[$bId] ?? 0);
                    $salesTillNow = floatval($salesTillNowPerBrand[$bId] ?? 0);
                    $returnsTillNow = floatval($returnsTillNowPerBrand[$bId] ?? 0);

                    $opStock = max(0, $currStock - $inwardTillNow + $salesTillNow - $returnsTillNow);

                    $inward = floatval($inwardPerBrand[$bId] ?? 0);
                    $sales = floatval($salesPerBrand[$bId] ?? 0);
                    $closing = $opStock + $inward - $sales;
                    $salesReturn = floatval($returnPerBrand[$bId] ?? 0);
                    $netClosing = $closing + $salesReturn;

                    if ($opStock == 0 && $inward == 0 && $sales == 0 && $closing == 0 && $salesReturn == 0 && $netClosing == 0) {
                        continue;
                    }

                    $totOpStock += $opStock;
                    $totInward += $inward;
                    $totSales += $sales;
                    $totClosing += $closing;
                    $totSalesReturn += $salesReturn;
                    $totNetClosing += $netClosing;

                    $bNameSafe = addslashes($brand->brand_name);
                    $data[] = [
                        'DT_RowAttr' => [
                            'onclick' => "drillDownToStockInwardSalesDetails({$bId}, '{$bNameSafe}')",
                            'style' => 'cursor: pointer;',
                            'data-brand-id' => $bId,
                            'data-brand-name' => $brand->brand_name
                        ],
                        'brand' => '<strong>' . htmlspecialchars($brand->brand_name) . '</strong>',
                        'op_stock' => number_format($opStock, 0),
                        'inward' => $inward > 0 ? number_format($inward, 0) : '',
                        'sales' => $sales > 0 ? number_format($sales, 0) : '',
                        'closing' => number_format($closing, 0),
                        'sales_return' => $salesReturn > 0 ? number_format($salesReturn, 0) : '',
                        'net_closing' => number_format($netClosing, 0),
                    ];
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
                    'totals' => [
                        'op_stock' => number_format($totOpStock, 0),
                        'inward' => number_format($totInward, 0),
                        'sales' => number_format($totSales, 0),
                        'closing' => number_format($totClosing, 0),
                        'sales_return' => number_format($totSalesReturn, 0),
                        'net_closing' => number_format($totNetClosing, 0),
                    ]
                ]);

            case 'brandwise-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date))) : date('Y-m-d', strtotime('-30 days'));
                $toDate = $request->to_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date))) : date('Y-m-d');
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
                if ($search) {
                    $query->where('brands.brand_name', 'like', "%{$search}%");
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
                )->havingRaw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) != 0');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $assortedStock = $query->get();
                $data = [];
                
                foreach ($assortedStock as $stock) {
                    $data[] = [
                        'store' => $stock->store,
                        'item_name' => '<strong>' . $stock->item_name . '</strong>',
                        'size' => $stock->size ?? '-',
                        'stock_qty' => '<span class="text-primary fw-bold">' . number_format($stock->stock_qty ?? 0, 0) . '</span>',
                    ];
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
                        'sales_orders.request_date',
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
                    $query->where(DB::raw('COALESCE(NULLIF(sales_orders.request_date, "0000-00-00"), sales_orders.so_date)'), '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where(DB::raw('COALESCE(NULLIF(sales_orders.request_date, "0000-00-00"), sales_orders.so_date)'), '<=', date('Y-m-d', strtotime($request->to_date)));
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
                    'sales_orders.request_date',
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
                    if (!empty($so->orderaxe_id) || !empty($so->orderaxe_ref_id) || !empty($so->order_no)) {
                        $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
                    }
                    if (!empty($so->order_no)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->order_no . '</span>';
                    } elseif (!empty($so->orderaxe_ref_id)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->orderaxe_ref_id . '</span>';
                    }

                    // Dates & Urgency
                    $soDateFormatted = !empty($so->so_date) ? date('d M Y', strtotime($so->so_date)) : '-';
                    $effectiveOrderDate = (!empty($so->request_date) && $so->request_date != '0000-00-00') ? $so->request_date : $so->so_date;
                    $orderDateFormatted = !empty($effectiveOrderDate) ? date('d M Y', strtotime($effectiveOrderDate)) : '-';
                    $daysPending = $today->diffInDays(\Carbon\Carbon::parse($effectiveOrderDate));
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
                        'so_date' => $soDateFormatted,
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
                        'sales_order_items.category_name',
                        'sales_order_items.categories_path_val',
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
                        $catName = trim($item->category_name ?? '');
                        $catPath = trim($item->categories_path_val ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code);
                            $bName = trim($b->brand_name);

                            if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                                ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                                ($bCode && $catName && stripos($catName, $bCode) === 0) ||
                                ($bCode && $catPath && stripos($catPath, $bCode) === 0) ||
                                ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                                ($bName && $sku && stripos($sku, $bName) !== false) ||
                                ($bName && $itemName && stripos($itemName, $bName) !== false) ||
                                ($bName && $catName && stripos($catName, $bName) !== false) ||
                                ($bName && $catPath && stripos($catPath, $bName) !== false)) {
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

           
            case 'order-processing-time':
            case 'order-processing-summary':
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
                if ($brandIdFilter) {
                    $query->whereExists(function ($q) use ($brandIdFilter) {
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
                            ->where('brands.id', $brandIdFilter);
                    });
                }
                if ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('sales_orders.so_no', 'LIKE', "%{$searchValue}%")
                          ->orWhere('customers.name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('places.place_name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('cities.city_name', 'LIKE', "%{$searchValue}%");
                    });
                }

                $recordsTotal = (clone $query)->count();
                $recordsFiltered = $recordsTotal;

                $salesOrders = $query->orderBy('sales_orders.so_date', 'desc')
                    ->orderBy('sales_orders.id', 'desc')
                    ->skip($start)
                    ->take($length)
                    ->get();

                $soIds = $salesOrders->pluck('so_id')->toArray();

                $soItemsList = [];
                if (!empty($soIds)) {
                    $soItemsList = DB::table('sales_order_items')
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
                }

                $soArtNoInvoicedMap = [];
                if (!empty($soIds)) {
                    $invoices = DB::table('sales_invoice_items')
                        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                        ->whereIn('sales_invoices.so_id', $soIds)
                        ->whereNull('sales_invoices.deleted_at')
                        ->whereNull('sales_invoice_items.deleted_at')
                        ->select(
                            'sales_invoices.so_id',
                            'sales_invoice_items.art_no',
                            DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                        )
                        ->groupBy('sales_invoices.so_id', 'sales_invoice_items.art_no')
                        ->get();

                    foreach ($invoices as $inv) {
                        $soId = $inv->so_id;
                        $qty = floatval($inv->inv_qty ?? 0);
                        if ($inv->art_no) {
                            $soArtNoInvoicedMap[$soId][$inv->art_no] = ($soArtNoInvoicedMap[$soId][$inv->art_no] ?? 0) + $qty;
                        }
                    }
                }

                $data = [];
                $currentDateGroup = null;
                $dateSnoCounter = 1;

                foreach ($salesOrders as $so) {
                    $soDateFormatted = date('d-m-Y', strtotime($so->so_date));
                    if ($currentDateGroup !== $soDateFormatted) {
                        $currentDateGroup = $soDateFormatted;
                        $dateSnoCounter = 1;
                    } else {
                        $dateSnoCounter++;
                    }

                    $itemsForSo = array_filter($soItemsList->toArray(), function($item) use ($so) {
                        return $item->sale_order_id == $so->so_id;
                    });

                    $brands = [];
                    $totalOrderedQty = 0;
                    $totalInvoicedQty = 0;
                    $pendingArtNos = [];

                    foreach ($itemsForSo as $itm) {
                        $bName = null;
                        if (!empty($itm->stock_brand_name)) {
                            $bName = $itm->stock_brand_name;
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
                                    break;
                                }
                            }
                        }
                        if (!$bName) {
                            $bName = 'Uncategorized';
                        }
                        $brands[$bName] = true;

                        $ordQ = floatval($itm->qty ?? 0);
                        $invQ = floatval($soArtNoInvoicedMap[$so->so_id][$itm->art_no] ?? 0);
                        $totalOrderedQty += $ordQ;
                        $totalInvoicedQty += min($ordQ, $invQ);

                        if ($ordQ > $invQ && !empty($itm->art_no)) {
                            $pendingArtNos[] = $itm->art_no;
                        }
                    }

                    $pendingQty = max(0, $totalOrderedQty - $totalInvoicedQty);
                    $completionPct = $totalOrderedQty > 0 ? min(100, round(($totalInvoicedQty / $totalOrderedQty) * 100, 2)) : 0;

                    // Brand Badges
                    $brandBadges = [];
                    foreach (array_keys($brands) as $bName) {
                        $brandBadges[] = '<span class="badge bg-label-primary mb-1 me-1" style="font-size:10px;">' . htmlspecialchars($bName) . '</span>';
                    }
                    $brandHtml = !empty($brandBadges) ? implode('', $brandBadges) : '<span class="text-muted">-</span>';

                    // Priority / Order Type Badge
                    $priority = strtoupper($so->order_type ?: 'REGULAR');
                    $priorityClass = ($priority == 'URGENT') ? 'bg-label-danger' : (($priority == 'SAMPLE') ? 'bg-label-info' : 'bg-label-secondary');
                    $priorityHtml = '<span class="badge ' . $priorityClass . ' font-monospace" style="font-size:11px;">' . $priority . '</span>';

                    // Order Completed %
                    $pcBadgeStyle = 'background-color: #fef9c3; color: #854d0e;'; // yellow/warning
                    if ($completionPct < 80) {
                        $pcBadgeStyle = 'background-color: #f8d7da; color: #842029;'; // red
                    } elseif ($completionPct >= 90) {
                        $pcBadgeStyle = 'background-color: #d1e7dd; color: #0f5132;'; // green
                    }
                    $completionHtml = '<span class="badge px-2 py-1 fw-bold" style="' . $pcBadgeStyle . ' font-size: 11px;">' . $completionPct . '%</span>';

                    // Status (based on sales invoice)
                    if ($completionPct >= 100) {
                        $statusHtml = '<span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size:10px;">COMPLETED</span>';
                    } elseif ($totalInvoicedQty > 0) {
                        $statusHtml = '<span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color:#e63946 !important; font-size:10px;">PARTIAL DELIVERY</span>';
                    } else {
                        $statusHtml = '<span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color:#e63946 !important; font-size:10px;">PENDING</span>';
                    }

                    // SO No Link
                    $soNoHtml = '<a href="' . url('sales_orders/view/' . $so->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($so->so_no) . '</a>';

                    // Awaiting Art Nos
                    $uniquePendingArtNos = array_unique($pendingArtNos);
                    if (!empty($uniquePendingArtNos)) {
                        $countArtNos = count($uniquePendingArtNos);
                        $awaitingHtml = '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap view-awaiting-art-nos" data-so-id="' . $so->so_id . '" data-so-no="' . htmlspecialchars($so->so_no) . '" data-count="' . $countArtNos . '" data-bs-toggle="tooltip" title="Click to view pending Art Nos hierarchy"><i class="ri-eye-line me-1"></i>View Art No (' . $countArtNos . ')</button>';
                    } else {
                        $awaitingHtml = '<span class="text-success small fw-bold"><i class="ri-check-double-line me-1"></i>None / Closed</span>';
                    }

                    // Place
                    $place = $so->place_name ?: ($so->city_name ?: '-');

                    $data[] = [
                        'date' => date('d-m-Y', strtotime($so->so_date)),
                        'sno' => $dateSnoCounter,
                        'order_date' => date('d-m-Y', strtotime($so->so_date)),
                        'so_no' => $soNoHtml,
                        'delivery_date' => !empty($so->delivery_date) ? date('d-m-Y', strtotime($so->delivery_date)) : '-',
                        'priority' => $priorityHtml,
                        'customer' => htmlspecialchars($so->customer_name ?? '-'),
                        'place' => htmlspecialchars($place),
                        'brand' => $brandHtml,
                        'ordered_qty' => number_format($totalOrderedQty, 0),
                        'invoiced_qty' => number_format($totalInvoicedQty, 0),
                        'pending_qty' => '<span class="' . ($pendingQty > 0 ? 'text-danger fw-bold' : 'text-success fw-bold') . '">' . number_format($pendingQty, 0) . '</span>',
                        'completion' => $completionHtml,
                        'status' => $statusHtml,
                        'awaiting_art_nos' => $awaitingHtml,
                        'wip' => '-',
                        'action_required' => htmlspecialchars($so->reason_for_delay ?: '-')
                    ];
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
                $storeId = $request->store_id;
                $brandIdFilter = $request->brand_id;

                $allBrands = DB::table('brands')->get();

                $orderQuery = DB::table('sales_order_items')
                    ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', 'stock_entry_items.brand_id', '=', 'brands.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_order_items.deleted_at')
                    ->select(
                        'sales_order_items.sale_order_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'sales_order_items.qty',
                        'brands.id as stock_brand_id',
                        'brands.brand_name as stock_brand_name',
                        'brand_categories.name as cat_brand_name',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.reason_for_delay'
                    );

                if ($fromDate && $toDate) {
                    $orderQuery->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
                }
                if ($storeId) {
                    $orderQuery->where('sales_orders.store_id', $storeId);
                }

                $orderItems = $orderQuery->get();

                $brandMap = [];

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
                            $bCode = trim($b->code ?? '');
                            $bName = trim($b->brand_name ?? '');

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

                    if (!$brandName || $brandName === 'Unassigned Brand' || empty($brandId)) {
                        continue;
                    }

                    if ($brandIdFilter && $brandId != $brandIdFilter) {
                        continue;
                    }

                    if (!isset($brandMap[$brandName])) {
                        $brandMap[$brandName] = [
                            'brand_id' => $brandId,
                            'brand_name' => $brandName,
                            'so_ids' => [],
                            'total_qty' => 0,
                            'items' => []
                        ];
                    }

                    $soId = $item->sale_order_id;
                    $brandMap[$brandName]['so_ids'][$soId] = true;
                    $brandMap[$brandName]['total_qty'] += floatval($item->qty ?? 0);
                    $brandMap[$brandName]['items'][] = $item;
                }

                $data = [];
                $snoCounter = 1;

                foreach ($brandMap as $bName => $bData) {
                    $soIds = array_keys($bData['so_ids']);
                    $ordersCount = count($soIds);
                    $totalQty = $bData['total_qty'];
                    $invoicedQty = 0;
                    $pendingArtNosList = [];
                    $reasonsList = [];

                    if (!empty($soIds)) {
                        $invoicedArtNos = DB::table('sales_invoice_items')
                            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                            ->whereIn('sales_invoices.so_id', $soIds)
                            ->whereNull('sales_invoices.deleted_at')
                            ->whereNull('sales_invoice_items.deleted_at')
                            ->select(
                                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                            )
                            ->groupBy('art_no')
                            ->pluck('inv_qty', 'art_no');

                        foreach ($bData['items'] as $item) {
                            $artNo = $item->art_no;
                            $itemOrdQty = floatval($item->qty ?? 0);
                            $itemInvQty = floatval($invoicedArtNos[$artNo] ?? 0);
                            $invoicedQty += min($itemOrdQty, $itemInvQty);

                            $itemPending = max(0, $itemOrdQty - $itemInvQty);
                            if ($itemPending > 0 && !empty($artNo)) {
                                $pendingArtNosList[] = $artNo;
                            }
                        }

                        $delays = DB::table('sales_orders')
                            ->whereIn('id', $soIds)
                            ->whereNotNull('reason_for_delay')
                            ->where('reason_for_delay', '!=', '')
                            ->pluck('reason_for_delay')
                            ->toArray();

                        $reasonsList = array_unique($delays);
                    }

                    $pendingQty = max(0, $totalQty - $invoicedQty);
                    $completionPct = $totalQty > 0 ? min(100, ($invoicedQty / $totalQty) * 100) : 0;

                    $pctBadgeClass = $completionPct >= 100 ? 'bg-success' : ($completionPct >= 50 ? 'bg-warning text-dark' : 'bg-danger');

                    $uniquePendingArtNos = array_unique($pendingArtNosList);
                    $awaitingStr = !empty($uniquePendingArtNos) ? implode(', ', array_slice($uniquePendingArtNos, 0, 5)) . (count($uniquePendingArtNos) > 5 ? '...' : '') : 'None / Closed';
                    $reasonsStr = !empty($reasonsList) ? implode('; ', array_slice($reasonsList, 0, 3)) : '-';

                    $brandLink = '<a href="javascript:void(0)" class="fw-bold text-primary view-brand-orders-link" data-brand-id="' . $bData['brand_id'] . '" data-brand-name="' . htmlspecialchars($bName) . '">' . htmlspecialchars($bName) . '</a>';

                    $data[] = [
                        'sno' => $snoCounter++,
                        'brand' => $brandLink,
                        'orders_count' => number_format($ordersCount, 0),
                        'total_qty' => number_format($totalQty, 0),
                        'invoiced_qty' => number_format($invoicedQty, 0),
                        'completion_pct' => '<span class="badge ' . $pctBadgeClass . ' fw-bold px-2 py-1">' . number_format($completionPct, 2) . '%</span>',
                        'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                        'awaiting_art_nos' => htmlspecialchars($awaitingStr),
                        'wip' => '-',
                        'action_required' => htmlspecialchars($reasonsStr)
                    ];
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

            default:
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
        }
    }

    public function getBrandwiseCompletionOrders(Request $request)
    {
        $brandId = $request->brand_id;
        $brandNameParam = $request->brand_name;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
        $storeId = $request->store_id;

        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $searchValue = $request->search['value'] ?? null;

        $allBrands = DB::table('brands')->get();

        $query = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands', 'stock_entry_items.brand_id', '=', 'brands.id')
            ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_order_items.deleted_at')
            ->select(
                'sales_orders.id as so_id',
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'brands.id as stock_brand_id',
                'brands.brand_name as stock_brand_name',
                'brand_categories.name as cat_brand_name'
            );

        if ($storeId) {
            $query->where('sales_orders.store_id', $storeId);
        }
        if ($fromDate && $toDate) {
            $query->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
        }

        $items = $query->get();

        $matchedSoIds = [];
        foreach ($items as $item) {
            $bName = null;
            $bId = null;

            if (!empty($item->stock_brand_name)) {
                $bName = $item->stock_brand_name;
                $bId = $item->stock_brand_id;
            } elseif (!empty($item->cat_brand_name)) {
                $bName = $item->cat_brand_name;
            } else {
                $artNo = trim($item->art_no ?? '');
                $sku = trim($item->sku ?? '');
                $itemName = trim($item->item_name ?? '');

                foreach ($allBrands as $b) {
                    $bCode = trim($b->code ?? '');
                    $bBrandName = trim($b->brand_name ?? '');

                    if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                        ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                        ($bBrandName && $artNo && stripos($artNo, $bBrandName) !== false) ||
                        ($bBrandName && $sku && stripos($sku, $bBrandName) !== false) ||
                        ($bBrandName && $itemName && stripos($itemName, $bBrandName) !== false)) {
                        $bName = $b->brand_name;
                        $bId = $b->id;
                        break;
                    }
                }
            }

            if (($brandId && $bId == $brandId) || ($brandNameParam && strcasecmp($bName, $brandNameParam) === 0)) {
                $matchedSoIds[$item->so_id] = true;
            }
        }

        $soIds = array_keys($matchedSoIds);

        $soQuery = DB::table('sales_orders')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->whereIn('sales_orders.id', $soIds)
            ->whereNull('sales_orders.deleted_at')
            ->select(
                'sales_orders.id as so_id',
                'sales_orders.so_no',
                'sales_orders.so_date',
                'sales_orders.delivery_date',
                'customers.name as customer_name',
                'sales_orders.reason_for_delay',
                'sales_orders.status'
            );

        if (!empty($searchValue)) {
            $soQuery->where(function($q) use ($searchValue) {
                $q->where('sales_orders.so_no', 'like', "%{$searchValue}%")
                  ->orWhere('customers.name', 'like', "%{$searchValue}%")
                  ->orWhere('sales_orders.reason_for_delay', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = count($soIds);
        $recordsFiltered = $soQuery->count();

        $soQuery->orderBy('sales_orders.so_date', 'desc');

        if ($length != -1) {
            $soQuery->skip($start)->take($length);
        }

        $orders = $soQuery->get();

        $data = [];
        $sno = $start + 1;
        foreach ($orders as $so) {
            $soItems = DB::table('sales_order_items')
                ->where('sale_order_id', $so->so_id)
                ->whereNull('deleted_at')
                ->get();

            $orderedQty = $soItems->sum('qty');

            $invoicedArtNos = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->where('sales_invoices.so_id', $so->so_id)
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->select(
                    DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                    DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                )
                ->groupBy('art_no')
                ->pluck('inv_qty', 'art_no');

            $invoicedQty = 0;
            $pendingArtNos = [];

            foreach ($soItems as $item) {
                $artNo = $item->art_no;
                $itemOrdQty = floatval($item->qty ?? 0);
                $itemInvQty = floatval($invoicedArtNos[$artNo] ?? 0);
                $invoicedQty += min($itemOrdQty, $itemInvQty);

                $itemPending = max(0, $itemOrdQty - $itemInvQty);
                if ($itemPending > 0 && !empty($artNo)) {
                    $pendingArtNos[] = $artNo;
                }
            }

            $pendingQty = max(0, $orderedQty - $invoicedQty);
            $uniquePendingArtNos = array_unique($pendingArtNos);
            $countArtNos = count($uniquePendingArtNos);

            if ($countArtNos > 0) {
                $awaitingHtml = '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap view-awaiting-art-nos" data-so-id="' . $so->so_id . '" data-so-no="' . htmlspecialchars($so->so_no) . '" data-count="' . $countArtNos . '"><i class="ri-eye-line me-1"></i>View Art No (' . $countArtNos . ')</button>';
            } else {
                $awaitingHtml = '<span class="text-success small fw-bold"><i class="ri-check-double-line me-1"></i>None / Closed</span>';
            }

            $statusHtml = ($pendingQty <= 0) 
                ? '<span class="badge bg-success">COMPLETED</span>' 
                : '<span class="badge bg-danger">PENDING</span>';

            $data[] = [
                'sno' => $sno++,
                'order_date' => date('d-m-Y', strtotime($so->so_date)),
                'so_no' => '<a href="' . url('sales_orders/view/' . $so->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($so->so_no) . '</a>',
                'customer' => htmlspecialchars($so->customer_name ?: '-'),
                'total_qty' => number_format($orderedQty, 0),
                'invoiced_qty' => number_format($invoicedQty, 0),
                'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                'status' => $statusHtml,
                'awaiting_art_nos' => $awaitingHtml,
                'action_required' => htmlspecialchars($so->reason_for_delay ?: '-')
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'status' => true,
            'data' => $data
        ]);
    }

    public function getOrderProcessingTimeArtNos(Request $request)
    {
        $soId = $request->so_id;

        $items = DB::table('sales_order_items')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->select(
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'sales_order_items.category_name',
                'sales_order_items.qty',
                'sales_order_items.size_id',
                'sales_order_items.sleeve',
                'stock_entry_items.size as stock_size',
                'stock_entry_items.sleeve_type as stock_sleeve'
            )
            ->where('sales_order_items.sale_order_id', $soId)
            ->whereNull('sales_order_items.deleted_at')
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
        $sno = 1;
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

            // Format Sleeve: parse JSON array if present, convert FS -> Full, HS -> Half
            $rawSleeve = $item->sleeve ?: $item->stock_sleeve;
            if (!empty($rawSleeve) && is_string($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                $decoded = json_decode($rawSleeve, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $rawSleeve = $decoded[0];
                }
            }
            $sleeveUpper = strtoupper(trim((string)$rawSleeve));
            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL' || $sleeveUpper === 'FULL SLEEVE') {
                $sleeveFormatted = 'Full';
            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF' || $sleeveUpper === 'HALF SLEEVE') {
                $sleeveFormatted = 'Half';
            } else {
                $sleeveFormatted = $rawSleeve ? htmlspecialchars($rawSleeve) : '-';
            }

            // Size: use sales_order_items.size_id directly
            $sizeVal = $item->size_id ?: ($item->stock_size ?: '-');

            // Format Category Name as Cw-White-F/S or Cw-White-H/S
            $formattedCategory = null;
            if (!empty($item->category_name)) {
                $catStr = str_replace([' ', '_'], '-', trim($item->category_name));
                $parts = array_filter(explode('-', $catStr));
                $formattedParts = array_map(function($p) {
                    $pUpper = strtoupper($p);
                    if ($pUpper === 'FS' || $pUpper === 'F/S') return 'F/S';
                    if ($pUpper === 'HS' || $pUpper === 'H/S') return 'H/S';
                    return ucfirst(strtolower($p));
                }, $parts);
                $formattedCategory = implode('-', $formattedParts);
            }

            $itemNameDisplay = $formattedCategory ? htmlspecialchars($formattedCategory) : htmlspecialchars($item->item_name ?: ($item->sku ?: '-'));

            $artNosData[] = [
                'sno' => $sno++,
                'art_no' => '<span class="fw-bold text-dark">' . htmlspecialchars($artNo ?: '-') . '</span>',
                'item_name' => $itemNameDisplay,
                'sleeve' => '<span class="badge bg-label-info">' . $sleeveFormatted . '</span>',
                'size' => '<span class="badge bg-label-secondary">' . htmlspecialchars($sizeVal) . '</span>',
                'ordered_qty' => number_format($ordQty, 0),
                'invoiced_qty' => number_format($invQty, 0),
                'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                'status' => $status
            ];
        }

        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $draw = intval($request->draw ?? 1);

        $recordsTotal = count($artNosData);

        // Search filter
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $searchValueLower = strtolower($searchValue);
            $artNosData = array_values(array_filter($artNosData, function($row) use ($searchValueLower) {
                return (
                    str_contains(strtolower(strip_tags($row['art_no'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['item_name'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['sleeve'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['size'])), $searchValueLower)
                );
            }));
        }

        $recordsFiltered = count($artNosData);

        // Slice array for pagination
        $pagedData = $length > 0 ? array_slice($artNosData, $start, $length) : $artNosData;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'status' => true,
            'data' => $pagedData,
            'items' => $pagedData
        ]);
    }

    public function getBrandwiseCompletionArtNos(Request $request)
    {
        return $this->getOrderProcessingTimeArtNos($request);
    }

    public function getBrandwiseLostSalesOrders(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length > 0 ? $request->length : 10);
        $search = $request->search['value'] ?? '';

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
                'sales_order_items.category_name',
                'sales_order_items.categories_path_val',
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
        if ($search) {
            $orderQuery->where(function($q) use ($search) {
                $q->where('sales_orders.so_no', 'like', "%{$search}%")
                  ->orWhere('customers.name', 'like', "%{$search}%")
                  ->orWhere('sales_order_items.art_no', 'like', "%{$search}%");
            });
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
                $catName = trim($item->category_name ?? '');
                $catPath = trim($item->categories_path_val ?? '');

                foreach ($allBrands as $b) {
                    $bCode = trim($b->code);
                    $bName = trim($b->brand_name);
                    if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                        ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                        ($bCode && $catName && stripos($catName, $bCode) === 0) ||
                        ($bCode && $catPath && stripos($catPath, $bCode) === 0) ||
                        ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                        ($bName && $sku && stripos($sku, $bName) !== false) ||
                        ($bName && $itemName && stripos($itemName, $bName) !== false) ||
                        ($bName && $catName && stripos($catName, $bName) !== false) ||
                        ($bName && $catPath && stripos($catPath, $bName) !== false)) {
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
                    'items' => []
                ];
            }

            $soMap[$soId]['ordered_qty'] += floatval($item->qty ?? 0);
            $soMap[$soId]['ordered_value'] += floatval($item->amount ?? 0);
            $soMap[$soId]['items'][] = $item;
        }

        $invoicedArtNosMap = [];
        if (!empty($matchedSoIds)) {
            $invoices = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->whereIn('sales_invoices.so_id', array_unique($matchedSoIds))
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->select(
                    'sales_invoices.so_id',
                    DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                    DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty')
                )
                ->groupBy('sales_invoices.so_id', 'art_no')
                ->get();

            foreach ($invoices as $inv) {
                $invoicedArtNosMap[$inv->so_id][$inv->art_no] = floatval($inv->invoiced_qty ?? 0);
            }
        }

        $allOrders = [];
        $orderSno = 1;
        foreach ($soMap as $soId => $data) {
            $soInvQty = 0;
            $pendingArtNosList = [];

            foreach ($data['items'] as $i) {
                $artNo = $i->art_no;
                $ordQ = floatval($i->qty ?? 0);
                $invQ = floatval($invoicedArtNosMap[$soId][$artNo] ?? 0);
                $soInvQty += min($ordQ, $invQ);

                $itemPending = max(0, $ordQ - $invQ);
                if ($itemPending > 0 && !empty($artNo)) {
                    $pendingArtNosList[] = $artNo;
                }
            }

            $totalOrdQty = $data['ordered_qty'];
            $pendingQty = max(0, $totalOrdQty - $soInvQty);

            $soNoHtml = '<a href="' . url('sales_orders/view/' . $soId) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($data['so_no']) . '</a>';
            if ($data['is_orderaxe']) {
                $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
            }

            $reasonHtml = $data['reason'] !== '-' ? htmlspecialchars($data['reason']) : '<span class="text-muted">-</span>';

            $uniquePendingArtNos = array_unique($pendingArtNosList);
            $pendingArtNosCount = count($uniquePendingArtNos);

            if ($pendingArtNosCount > 0 && $pendingQty > 0) {
                $btnAwaiting = '<button class="btn btn-xs btn-outline-danger" onclick="drillDownToBrandOrderArtNos(' . $soId . ', \'' . htmlspecialchars($data['so_no'], ENT_QUOTES) . '\')">VIEW ART NO (' . $pendingArtNosCount . ')</button>';
                $statusBadge = '<span class="badge bg-danger">PENDING</span>';
            } else {
                $btnAwaiting = '<span class="badge bg-label-success">None / Closed</span>';
                $statusBadge = '<span class="badge bg-success">COMPLETED</span>';
            }

            $allOrders[] = [
                'sno' => $orderSno++,
                'order_date' => $data['so_date'],
                'so_no' => $soNoHtml,
                'customer' => htmlspecialchars($data['customer']),
                'total_qty' => number_format($totalOrdQty, 0),
                'invoiced_qty' => '<span class="text-primary fw-bold">' . number_format($soInvQty, 0) . '</span>',
                'pending_qty' => '<span class="text-danger fw-bold">' . number_format($pendingQty, 0) . '</span>',
                'status' => $statusBadge,
                'awaiting_art_nos' => $btnAwaiting,
                'action_required' => $reasonHtml,
            ];
        }

        $recordsTotal = count($allOrders);
        $recordsFiltered = $recordsTotal;

        $pagedOrders = $length != -1 ? array_slice($allOrders, $start, $length) : $allOrders;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedOrders,
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
            'so_date' => date('d M Y', strtotime((!empty($so->request_date) && $so->request_date != '0000-00-00') ? $so->request_date : $so->so_date)),
            'delivery_date' => $so->delivery_date ? date('d M Y', strtotime($so->delivery_date)) : '-',
            'status_name' => $so->status,
            'items' => $items
        ]);
    }

    public function getStockInwardSalesDetails(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length > 0 ? $request->length : 10);
        $search = $request->search['value'] ?? '';

        $brandId = $request->brand_id;
        $brandName = $request->brand_name;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
        $storeId = $request->store_id;

        $makeKey = function($artNo, $itemCode, $sleeve, $size) {
            return strtolower(trim($artNo ?? '')) . '_' . strtolower(trim($itemCode ?? '')) . '_' . strtolower(trim($sleeve ?? '')) . '_' . strtolower(trim($size ?? ''));
        };

        $stockItemsQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
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
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.finished_item_code',
                'stock_entry_items.size',
                'stock_entry_items.sleeve_type',
                'styles.style_name',
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as current_stock')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId) {
            $stockItemsQuery->where('brands.id', $brandId);
        }
        if ($storeId) {
            $stockItemsQuery->where('stock_entry_items.store_location_id', $storeId);
        }
        if ($search) {
            $stockItemsQuery->where(function($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.finished_item_code', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.size', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sleeve_type', 'like', "%{$search}%")
                  ->orWhere('styles.style_name', 'like', "%{$search}%");
            });
        }

        $stockItems = $stockItemsQuery->groupBy(
            'stock_entry_items.art_no',
            'stock_entry_items.finished_item_code',
            'stock_entry_items.size',
            'stock_entry_items.sleeve_type',
            'styles.style_name'
        )->get();

        $inwardQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
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
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.size',
                DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->where('stock_entry_items.qty_in', '>', 0)
            ->whereNull('stock_entry_items.deleted_at')
            ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate]);

        if ($brandId) {
            $inwardQuery->where('brands.id', $brandId);
        }
        if ($storeId) {
            $inwardQuery->where('stock_entry_items.store_location_id', $storeId);
        }

        $inwardMap = $inwardQuery->groupBy('stock_entry_items.art_no', 'stock_entry_items.size')
            ->get()
            ->keyBy(function($item) {
                return $item->art_no . '_' . $item->size;
            });

        $inwardTillNowQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
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
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.size',
                DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->where('stock_entry_items.qty_in', '>', 0)
            ->whereNull('stock_entry_items.deleted_at')
            ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate);

        if ($brandId) {
            $inwardTillNowQuery->where('brands.id', $brandId);
        }
        if ($storeId) {
            $inwardTillNowQuery->where('stock_entry_items.store_location_id', $storeId);
        }

        $inwardTillNowMap = $inwardTillNowQuery->groupBy('stock_entry_items.art_no', 'stock_entry_items.size')
            ->get()
            ->keyBy(function($item) {
                return $item->art_no . '_' . $item->size;
            });

        $salesQuery = DB::table('sales_invoice_items')
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
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                'sales_invoice_items.art_no',
                DB::raw('COALESCE(stock_entry_items.finished_item_code, styles.style_name, "") as finished_item_code'),
                DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                'sales_invoice_items.size',
                DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
            )
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

        if ($brandId) {
            $salesQuery->where('brands.id', $brandId);
        }
        if ($storeId) {
            $salesQuery->where('sales_invoices.store_id', $storeId);
        }

        $salesMap = $salesQuery->groupBy(
            'sales_invoice_items.art_no',
            DB::raw('COALESCE(stock_entry_items.finished_item_code, styles.style_name, "")'),
            DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            'sales_invoice_items.size'
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->finished_item_code, $item->sleeve_type, $item->size);
        });

        $salesTillNowQuery = DB::table('sales_invoice_items')
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
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                'sales_invoice_items.art_no',
                DB::raw('COALESCE(stock_entry_items.finished_item_code, styles.style_name, "") as finished_item_code'),
                DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                'sales_invoice_items.size',
                DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
            )
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->where('sales_invoices.inv_date', '>=', $fromDate);

        if ($brandId) {
            $salesTillNowQuery->where('brands.id', $brandId);
        }
        if ($storeId) {
            $salesTillNowQuery->where('sales_invoices.store_id', $storeId);
        }

        $salesTillNowMap = $salesTillNowQuery->groupBy(
            'sales_invoice_items.art_no',
            DB::raw('COALESCE(stock_entry_items.finished_item_code, styles.style_name, "")'),
            DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            'sales_invoice_items.size'
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->finished_item_code, $item->sleeve_type, $item->size);
        });

        $allItems = [];
        foreach ($stockItems as $item) {
            $key = $item->art_no . '_' . $item->size;

            $currStock = floatval($item->current_stock ?? 0);
            $inwardTillNow = floatval($inwardTillNowMap[$key]->inward_qty ?? 0);
            $salesTillNow = floatval($salesTillNowMap[$key]->sales_qty ?? 0);

            $opStock = max(0, $currStock - $inwardTillNow + $salesTillNow);
            $inward = floatval($inwardMap[$key]->inward_qty ?? 0);
            $sales = floatval($salesMap[$key]->sales_qty ?? 0);
            $closing = $opStock + $inward - $sales;
            $salesReturn = 0;
            $netClosing = $closing + $salesReturn;

            if ($opStock == 0 && $inward == 0 && $sales == 0 && $closing == 0 && $netClosing == 0) {
                continue;
            }

            $allItems[] = [
                'art_no' => '<strong>' . htmlspecialchars($item->art_no ?: '-') . '</strong>',
                'item_name' => htmlspecialchars($item->finished_item_code ?: ($item->style_name ?: '-')),
                'sleeve_type' => htmlspecialchars($item->sleeve_type ?: '-'),
                'size' => htmlspecialchars($item->size ?: '-'),
                'op_stock' => $opStock > 0 ? number_format($opStock, 0) : '',
                'inward' => $inward > 0 ? number_format($inward, 0) : '',
                'sales' => $sales > 0 ? number_format($sales, 0) : '',
                'closing' => $closing > 0 ? number_format($closing, 0) : '',
                'sales_return' => $salesReturn > 0 ? number_format($salesReturn, 0) : '',
                'net_closing' => '<strong class="text-primary">' . number_format($netClosing, 0) . '</strong>',
            ];
        }

        $recordsTotal = count($allItems);
        $recordsFiltered = $recordsTotal;

        $pagedData = $length != -1 ? array_slice($allItems, $start, $length) : $allItems;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedData,
        ]);
    }
}
