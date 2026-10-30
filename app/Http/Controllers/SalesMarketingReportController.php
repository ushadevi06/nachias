<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\SalesAgent;
use App\Models\Zone;
use App\Models\Brand;
use App\Models\CustomerOpeningBalance;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalesMarketingReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-marketing-report')) {
            return unauthorizedRedirect();
        }

        $customers = Customer::where('status', 'Active')->select('id', 'name', 'code')->orderBy('name', 'asc')->get();
        $executives = SalesAgent::where('status', 'Active')->select('id', 'name', 'code')->orderBy('name', 'asc')->get();
        $brands = Brand::where('status', 'Active')->select('id', 'brand_name', 'code')->orderBy('brand_name', 'asc')->get();

        $orders = collect();
        $incentiveReport = [];
        $comparisonReport = [];
        $outstandingReport = [];
        $creditNotes = collect();

        return view('reports/sales_marketing_report', compact('orders', 'customers', 'executives', 'brands', 'incentiveReport', 'comparisonReport', 'outstandingReport', 'creditNotes'));
    }

    public function ajaxReportData(Request $request, $type)
    {
        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLen = intval($request->length);
        $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        try {
            $parseDate = function($dateStr) {
                if (empty($dateStr) || $dateStr === 'DD-MM-YYYY' || trim($dateStr) === '') {
                    return null;
                }
                try {
                    return \Carbon\Carbon::createFromFormat('d-m-Y', trim($dateStr))->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        $ts = strtotime(trim($dateStr));
                        return ($ts !== false && $ts > 0) ? date('Y-m-d', $ts) : null;
                    } catch (\Exception $e2) {
                        return null;
                    }
                }
            };

            $fromDate = $parseDate($request->from_date);
            $toDate = $parseDate($request->to_date);
            $customerId = $request->customer_id;
            $agentId = $request->agent_id;
            $brandId = $request->brand_id;

            switch ($type) {
                case 'order-report':
                    $countQuery = SalesOrder::whereNull('deleted_at')
                        ->where(function($q) {
                            $q->whereNull('status')
                              ->orWhereRaw('LOWER(status) != ?', ['cancelled']);
                        });
                    if ($fromDate) $countQuery->where('so_date', '>=', $fromDate);
                    if ($toDate) $countQuery->where('so_date', '<=', $toDate);
                    if ($customerId) $countQuery->where('customer_id', $customerId);
                    if ($agentId) $countQuery->where('agent_id', $agentId);
                    if ($search) {
                        $countQuery->where(function($q) use ($search) {
                            $q->where('so_no', 'like', "%{$search}%")
                              ->orWhereHas('customer', function($c) use ($search) {
                                  $c->where('name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $totalRecords = $countQuery->count();
                    $totalQtySum = (float)(clone $countQuery)->sum('total_qty');

                    $dataQuery = clone $countQuery;
                    $ordersQuery = $dataQuery->with(['customer', 'items.stockEntryItem', 'salesInvoices.items'])->orderBy('id', 'desc');
                    if ($length !== -1) {
                        $ordersQuery->offset($start)->limit($length);
                    }
                    $orders = $ordersQuery->get();

                    $data = [];
                    foreach ($orders as $order) {
                        $validInvoices = $order->salesInvoices ? $order->salesInvoices->filter(function($inv) {
                            return empty($inv->einvoice_status) || strtolower((string)$inv->einvoice_status) !== 'cancelled';
                        }) : collect();
                        $delivered_qty = $validInvoices->sum(function($inv) { return $inv->items ? $inv->items->sum('quantity') : 0; });
                        $total_qty = (float)($order->total_qty ?? 0);
                        $fulfillmentStatus = ($delivered_qty >= $total_qty && $total_qty > 0) ? 'Completed' : ($delivered_qty > 0 ? 'Partial Delivery' : 'Planned');

                        $itemsHtml = '-';
                        if ($order->items && $order->items->count() > 0) {
                            $firstItem = $order->items->first();
                            $sleeve = is_array($firstItem->sleeve) ? ($firstItem->sleeve[0] ?? '') : $firstItem->sleeve;
                            $sleeveDisplay = ($sleeve == 'Full' || $sleeve == 'Full Sleeve') ? 'F/S' : (($sleeve == 'Half' || $sleeve == 'Half Sleeve') ? 'H/S' : ($sleeve ?? '-'));
                            $sizeDisplay = $firstItem->size_id ?? '-';
                            $variantInfo = trim($sizeDisplay . ' ' . $sleeveDisplay);

                            $itemsData = $order->items->map(function($item) {
                                $s = is_array($item->sleeve) ? ($item->sleeve[0] ?? "") : $item->sleeve;
                                $sd = ($s == "Full" || $s == "Full Sleeve") ? "F/S" : (($s == "Half" || $s == "Half Sleeve") ? "H/S" : $s);
                                return [
                                    "name" => (string)($item->item_name ?? '-'),
                                    "size" => (string)($item->size_id ?? "-"),
                                    "sleeve" => (string)($sd ?? '-'),
                                    "qty" => number_format((float)($item->qty ?? 0), 0)
                                ];
                            })->values()->all();

                            $itemsHtml = '<span>' . htmlspecialchars((string)($firstItem->item_name ?? '-')) . ' (' . htmlspecialchars((string)$variantInfo) . ')</span>';
                            if ($order->items->count() > 1) {
                                $itemsHtml .= ' <span class="badge bg-label-primary rounded-pill cursor-pointer view-order-items" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#orderItemsModal" data-order-no="' . htmlspecialchars((string)($order->so_no ?? '')) . '" data-items=\'' . htmlspecialchars(json_encode($itemsData), ENT_QUOTES, 'UTF-8') . '\'>+' . ($order->items->count() - 1) . ' more</span>';
                            }
                        }

                        $badgeClass = 'bg-label-info';
                        if ($fulfillmentStatus == 'Completed') $badgeClass = 'bg-label-success';
                        if ($fulfillmentStatus == 'Planned') $badgeClass = 'bg-label-warning';

                        $statusHtml = '<span class="badge ' . $badgeClass . ' rounded-pill">' . $fulfillmentStatus . '</span>';
                        $soNoRaw = (string)($order->so_no ?? '-');
                        $custRaw = (string)(optional($order->customer)->name ?? '-');
                        $soNoStr = htmlspecialchars($soNoRaw);
                        $custStr = htmlspecialchars($custRaw);
                        $soDateStr = $order->so_date ? date('d-M-Y', strtotime((string)$order->so_date)) : '-';
                        $itemsJson = htmlspecialchars(json_encode($itemsData), ENT_QUOTES, 'UTF-8');

                        $actionBtn = '<button type="button" class="btn btn-sm btn-label-primary rounded-pill view-order-modal-btn" data-so-no="' . $soNoStr . '" data-customer="' . $custStr . '" data-date="' . $soDateStr . '" data-status=\'' . htmlspecialchars($statusHtml, ENT_QUOTES, 'UTF-8') . '\' data-items=\'' . $itemsJson . '\'><i class="ri ri-eye-line me-1"></i>View Items</button>';

                        $data[] = [
                            'order_id' => $order->id,
                            'so_no' => '<span class="text-primary fw-bold">' . $soNoStr . ' <i class="ri ri-arrow-right-s-line ms-1"></i></span>',
                            'so_date' => $soDateStr,
                            'customer' => $custStr,
                            'qty' => number_format($total_qty, 0),
                            'status' => $statusHtml,
                            'so_no_raw' => $soNoRaw,
                            'customer_raw' => $custRaw,
                            'so_date_raw' => $soDateStr,
                            'status_html' => $statusHtml,
                            'items_data' => $itemsData
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data,
                        'totals' => [
                            'qty' => number_format($totalQtySum, 0)
                        ]
                    ]);

                case 'order-product-wise':
                    $soQuery = DB::table('sales_orders')
                        ->whereNull('sales_orders.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('sales_orders.status')
                              ->orWhereRaw('LOWER(sales_orders.status) != ?', ['cancelled']);
                        });

                    if ($fromDate) $soQuery->where('sales_orders.so_date', '>=', $fromDate);
                    if ($toDate) $soQuery->where('sales_orders.so_date', '<=', $toDate);
                    if ($customerId) $soQuery->where('sales_orders.customer_id', $customerId);
                    if ($agentId) $soQuery->where('sales_orders.agent_id', $agentId);

                    $soIds = $soQuery->pluck('sales_orders.id')->toArray();

                    if (empty($soIds)) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => [
                                'size_36' => '0', 'size_38' => '0', 'size_40' => '0', 'size_42' => '0',
                                'size_44' => '0', 'size_46' => '0', 'size_48' => '0',
                                'size_50' => '0', 'total_qty' => '0'
                            ]
                        ]);
                    }

                    $pwBaseQuery = DB::table('sales_order_items')->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')->leftJoin('stock_entry_items as sei', 'sales_order_items.stock_entry_item_id', '=', 'sei.id')->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')->leftJoin('items as it', 'sei.item_id', '=', 'it.id')->whereIn('sales_order_items.sale_order_id', $soIds)->whereNull('sales_order_items.deleted_at');

                    if ($search) {
                        $pwBaseQuery->where(function($q) use ($search) {
                            $q->where('sales_order_items.item_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.category_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('colors.color_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.api_color', 'like', "%{$search}%");
                        });
                    }

                    $summaryData = (clone $pwBaseQuery)->select(
                        DB::raw('(CASE 
                            WHEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, ""))) != "" 
                                THEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, "")))
                            WHEN it.name IS NOT NULL AND TRIM(it.name) != "" 
                                THEN it.name
                            WHEN sales_order_items.category_name IS NOT NULL AND TRIM(sales_order_items.category_name) != "" AND sales_order_items.category_name NOT REGEXP "^BC[0-9]"
                                THEN sales_order_items.category_name
                            WHEN sales_order_items.item_name IS NOT NULL AND TRIM(sales_order_items.item_name) != "" AND sales_order_items.item_name NOT REGEXP "^BC[0-9]"
                                THEN sales_order_items.item_name
                            WHEN sales_order_items.art_no IS NOT NULL AND TRIM(sales_order_items.art_no) != "" AND sales_order_items.art_no NOT REGEXP "^BC[0-9]"
                                THEN sales_order_items.art_no
                            ELSE sales_order_items.item_name
                        END) as display_item_name'),
                        'sales_order_items.art_no',
                        DB::raw('COALESCE(colors.color_name, sales_order_items.api_color, "-") as color'),
                        'sales_order_items.sleeve',
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "36" THEN sales_order_items.qty ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "38" THEN sales_order_items.qty ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "40" THEN sales_order_items.qty ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "42" THEN sales_order_items.qty ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "44" THEN sales_order_items.qty ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "46" THEN sales_order_items.qty ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "48" THEN sales_order_items.qty ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "50" THEN sales_order_items.qty ELSE 0 END) as size_50'),
                        DB::raw('SUM(sales_order_items.qty) as total_qty')
                    )->groupBy('display_item_name', 'sales_order_items.art_no', 'color', 'sales_order_items.sleeve')
                     ->orderBy('display_item_name', 'asc')
                     ->get();

                    $recordsTotal = count($summaryData);
                    $tot36 = floatval($summaryData->sum('size_36'));
                    $tot38 = floatval($summaryData->sum('size_38'));
                    $tot40 = floatval($summaryData->sum('size_40'));
                    $tot42 = floatval($summaryData->sum('size_42'));
                    $tot44 = floatval($summaryData->sum('size_44'));
                    $tot46 = floatval($summaryData->sum('size_46'));
                    $tot48 = floatval($summaryData->sum('size_48'));
                    $tot50 = floatval($summaryData->sum('size_50'));
                    $totAllQty = floatval($summaryData->sum('total_qty'));

                    $pagedPw = ($length !== -1) ? $summaryData->slice($start, $length)->values() : $summaryData;

                    $pwData = [];
                    $sno = $start + 1;
                    foreach ($pagedPw as $row) {
                        $rawProdName = $row->display_item_name ?: '-';
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $pwData[] = [
                            'sno' => $sno++,
                            'product_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsTotal,
                        'data' => $pwData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'order-party-wise':
                    $soQuery = DB::table('sales_orders')
                        ->whereNull('sales_orders.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('sales_orders.status')
                              ->orWhereRaw('LOWER(sales_orders.status) != ?', ['cancelled']);
                        });

                    if ($fromDate) $soQuery->where('sales_orders.so_date', '>=', $fromDate);
                    if ($toDate) $soQuery->where('sales_orders.so_date', '<=', $toDate);
                    if ($customerId) $soQuery->where('sales_orders.customer_id', $customerId);
                    if ($agentId) $soQuery->where('sales_orders.agent_id', $agentId);

                    $soIds = $soQuery->pluck('sales_orders.id')->toArray();

                    if (empty($soIds)) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => [
                                'size_36' => '0', 'size_38' => '0', 'size_40' => '0',
                                'size_42' => '0', 'size_44' => '0', 'size_46' => '0',
                                'size_48' => '0', 'size_50' => '0', 'total_qty' => '0'
                            ]
                        ]);
                    }

                    $partyBaseQuery = DB::table('sales_order_items')->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')->leftJoin('places', 'customers.place_id', '=', 'places.id')->leftJoin('cities', 'customers.city_id', '=', 'cities.id')->leftJoin('sales_agents', 'sales_orders.agent_id', '=', 'sales_agents.id')->leftJoin('zones', 'sales_orders.zone_id', '=', 'zones.id')->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')->leftJoin('stock_entry_items as sei', 'sales_order_items.stock_entry_item_id', '=', 'sei.id')->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')->leftJoin('items as it', 'sei.item_id', '=', 'it.id')->whereIn('sales_order_items.sale_order_id', $soIds)->whereNull('sales_order_items.deleted_at');

                    if ($search) {
                        $partyBaseQuery->where(function($q) use ($search) {
                            $q->where('sales_orders.so_no', 'like', "%{$search}%")
                              ->orWhere('sales_orders.order_no', 'like', "%{$search}%")
                              ->orWhere('sales_orders.orderaxe_ref_id', 'like', "%{$search}%")
                              ->orWhere('customers.name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.item_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.category_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('colors.color_name', 'like', "%{$search}%");
                        });
                    }

                    $partyGrouped = (clone $partyBaseQuery)->select(
                        'sales_orders.id as so_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.order_no',
                        'sales_orders.orderaxe_id',
                        'sales_orders.orderaxe_ref_id',
                        'customers.name as customer_name',
                        DB::raw('COALESCE(places.place_name, cities.city_name, "-") as place'),
                        DB::raw('COALESCE(sales_agents.name, "-") as agent_name'),
                        DB::raw('COALESCE(zones.zone_name, "-") as zone_name'),
                        'sales_order_items.item_name',
                        'sales_order_items.category_name',
                        'sales_order_items.art_no',
                        DB::raw('COALESCE(colors.color_name, sales_order_items.api_color, "-") as color'),
                        'sales_order_items.sleeve',
                        DB::raw("TRIM(CONCAT(COALESCE(b.brand_name, ''), ' ', COALESCE(st.style_name, ''))) as stock_prod_name"),
                        'it.name as item_tbl_name',
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "36" THEN sales_order_items.qty ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "38" THEN sales_order_items.qty ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "40" THEN sales_order_items.qty ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "42" THEN sales_order_items.qty ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "44" THEN sales_order_items.qty ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "46" THEN sales_order_items.qty ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "48" THEN sales_order_items.qty ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "50" THEN sales_order_items.qty ELSE 0 END) as size_50'),
                        DB::raw('SUM(sales_order_items.qty) as total_qty')
                    )->groupBy(
                        'sales_orders.id', 'sales_orders.so_no', 'sales_orders.so_date', 'sales_orders.order_no', 'sales_orders.orderaxe_id', 'sales_orders.orderaxe_ref_id',
                        'customers.name', 'place', 'agent_name', 'zone_name',
                        'sales_order_items.item_name', 'sales_order_items.category_name', 'sales_order_items.art_no', 'color', 'sales_order_items.sleeve',
                        'stock_prod_name', 'item_tbl_name'
                    )->orderBy('sales_orders.id', 'desc');

                    if ($search) {
                        $summaryData = $partyGrouped->get();

                        $partyRecordsTotal = count($summaryData);
                        $tot36 = floatval($summaryData->sum('size_36'));
                        $tot38 = floatval($summaryData->sum('size_38'));
                        $tot40 = floatval($summaryData->sum('size_40'));
                        $tot42 = floatval($summaryData->sum('size_42'));
                        $tot44 = floatval($summaryData->sum('size_44'));
                        $tot46 = floatval($summaryData->sum('size_46'));
                        $tot48 = floatval($summaryData->sum('size_48'));
                        $tot50 = floatval($summaryData->sum('size_50'));
                        $totAllQty = floatval($summaryData->sum('total_qty'));

                        $pagedParty = ($length !== -1) ? $summaryData->slice($start, $length)->values() : $summaryData;
                    } else {
                        $sizeTotals = DB::table('sales_order_items')
                            ->whereIn('sale_order_id', $soIds)
                            ->whereNull('deleted_at')
                            ->select(
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "36" THEN qty ELSE 0 END) as tot_36'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "38" THEN qty ELSE 0 END) as tot_38'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "40" THEN qty ELSE 0 END) as tot_40'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "42" THEN qty ELSE 0 END) as tot_42'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "44" THEN qty ELSE 0 END) as tot_44'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "46" THEN qty ELSE 0 END) as tot_46'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "48" THEN qty ELSE 0 END) as tot_48'),
                                DB::raw('SUM(CASE WHEN TRIM(size_id) = "50" THEN qty ELSE 0 END) as tot_50'),
                                DB::raw('SUM(qty) as tot_all')
                            )->first();

                        $partyRecordsTotal = DB::table('sales_order_items')
                            ->whereIn('sale_order_id', $soIds)
                            ->whereNull('deleted_at')
                            ->count(DB::raw('DISTINCT CONCAT(sale_order_id, "_", COALESCE(item_name, ""), "_", COALESCE(art_no, ""), "_", COALESCE(color_id, ""), "_", COALESCE(sleeve, ""))'));

                        $tot36 = floatval($sizeTotals->tot_36 ?? 0);
                        $tot38 = floatval($sizeTotals->tot_38 ?? 0);
                        $tot40 = floatval($sizeTotals->tot_40 ?? 0);
                        $tot42 = floatval($sizeTotals->tot_42 ?? 0);
                        $tot44 = floatval($sizeTotals->tot_44 ?? 0);
                        $tot46 = floatval($sizeTotals->tot_46 ?? 0);
                        $tot48 = floatval($sizeTotals->tot_48 ?? 0);
                        $tot50 = floatval($sizeTotals->tot_50 ?? 0);
                        $totAllQty = floatval($sizeTotals->tot_all ?? 0);

                        $pagedParty = ($length !== -1) ? $partyGrouped->offset($start)->limit($length)->get() : $partyGrouped->get();
                    }

                    $partyData = [];
                    $sno = $start + 1;
                    foreach ($pagedParty as $row) {
                        $orderNo = !empty($row->order_no) ? $row->order_no : (!empty($row->orderaxe_ref_id) ? $row->orderaxe_ref_id : $row->so_no);
                        $isOrderaxe = !empty($row->orderaxe_id) || !empty($row->orderaxe_ref_id);
                        if ($isOrderaxe) {
                            $stockName = !empty($row->stock_prod_name) ? trim($row->stock_prod_name) : (!empty($row->item_tbl_name) ? trim($row->item_tbl_name) : '');
                            if ($stockName !== '') {
                                $rawProdName = $stockName;
                            } elseif (!empty($row->category_name)) {
                                $rawProdName = $row->category_name;
                            } else {
                                $rawProdName = $row->item_name ?: '-';
                            }
                        } else {
                            $rawProdName = $row->item_name ?: '-';
                        }
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $partyData[] = [
                            'sno' => $sno++,
                            'order_no' => '<a href="' . url('sales_orders/view/' . $row->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($orderNo) . '</a>',
                            'order_date' => !empty($row->so_date) ? date('d-m-Y', strtotime($row->so_date)) : '-',
                            'customer_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($row->customer_name ?: '-') . '</span>',
                            'place' => htmlspecialchars($row->place ?: '-'),
                            'sales_executive' => htmlspecialchars($row->agent_name ?: '-'),
                            'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars($row->zone_name ?: '-') . '</span>',
                            'product_name' => '<span class="text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $partyRecordsTotal,
                        'recordsFiltered' => $partyRecordsTotal,
                        'data' => $partyData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'pending-report':
                case 'pending-product-wise':
                    $soQuery = DB::table('sales_orders')
                        ->whereNull('sales_orders.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('sales_orders.status')
                              ->orWhereRaw('LOWER(sales_orders.status) != ?', ['cancelled']);
                        });

                    if ($fromDate) $soQuery->where('sales_orders.so_date', '>=', $fromDate);
                    if ($toDate) $soQuery->where('sales_orders.so_date', '<=', $toDate);
                    if ($customerId) $soQuery->where('sales_orders.customer_id', $customerId);
                    if ($agentId) $soQuery->where('sales_orders.agent_id', $agentId);

                    $pendingSoIds = $soQuery->pluck('sales_orders.id')->toArray();

                    if (empty($pendingSoIds)) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => [
                                'size_36' => '0', 'size_38' => '0', 'size_40' => '0', 'size_42' => '0',
                                'size_44' => '0', 'size_46' => '0', 'size_48' => '0',
                                'size_50' => '0', 'total_qty' => '0'
                            ]
                        ]);
                    }

                    $pwBaseQuery = DB::table('sales_order_items')->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')->leftJoin('stock_entry_items as sei', 'sales_order_items.stock_entry_item_id', '=', 'sei.id')->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')->leftJoin('items as it', 'sei.item_id', '=', 'it.id')->whereIn('sales_order_items.sale_order_id', $pendingSoIds)->whereNull('sales_order_items.deleted_at');

                    if ($brandId) {
                        $pwBaseQuery->where(function($q) use ($brandId) {
                            $q->where('sei.brand_id', $brandId)->orWhere('b.id', $brandId);
                        });
                    }

                    if ($search) {
                        $pwBaseQuery->where(function($q) use ($search) {
                            $q->where('sales_order_items.item_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.category_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('colors.color_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.api_color', 'like', "%{$search}%");
                        });
                    }

                    $summaryData = (clone $pwBaseQuery)->select(
                        DB::raw('(CASE 
                            WHEN (sales_orders.orderaxe_id IS NOT NULL OR sales_orders.orderaxe_ref_id IS NOT NULL) THEN
                                CASE 
                                    WHEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, ""))) != "" THEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, "")))
                                    WHEN it.name IS NOT NULL AND it.name != "" THEN it.name
                                    WHEN sales_order_items.category_name IS NOT NULL AND sales_order_items.category_name != "" THEN sales_order_items.category_name
                                    ELSE sales_order_items.item_name
                                END
                            ELSE sales_order_items.item_name
                        END) as display_item_name'),
                        'sales_order_items.art_no',
                        DB::raw('COALESCE(colors.color_name, sales_order_items.api_color, "-") as color'),
                        'sales_order_items.sleeve',
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "36" THEN sales_order_items.qty ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "38" THEN sales_order_items.qty ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "40" THEN sales_order_items.qty ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "42" THEN sales_order_items.qty ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "44" THEN sales_order_items.qty ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "46" THEN sales_order_items.qty ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "48" THEN sales_order_items.qty ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "50" THEN sales_order_items.qty ELSE 0 END) as size_50'),
                        DB::raw('SUM(sales_order_items.qty) as total_qty')
                    )->groupBy('display_item_name', 'sales_order_items.art_no', 'color', 'sales_order_items.sleeve')
                     ->orderBy('display_item_name', 'asc')
                     ->get();

                    $dispBaseQuery = DB::table('sales_invoice_items as sii')
                        ->join('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
                        ->leftJoin('colors as col', 'sii.color_id', '=', 'col.id')
                        ->leftJoin('stock_entry_items as sei', 'sii.stock_entry_item_id', '=', 'sei.id')
                        ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
                        ->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')
                        ->leftJoin('items as it', 'sei.item_id', '=', 'it.id')
                        ->whereNull('sii.deleted_at')
                        ->whereNull('si.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        });

                    if ($fromDate) $dispBaseQuery->where('si.inv_date', '>=', $fromDate);
                    if ($toDate) $dispBaseQuery->where('si.inv_date', '<=', $toDate);
                    if ($customerId) $dispBaseQuery->where('si.customer_id', $customerId);
                    if ($agentId) $dispBaseQuery->where('si.agent_id', $agentId);
                    if ($brandId) {
                        $dispBaseQuery->where(function($q) use ($brandId) {
                            $q->where('sei.brand_id', $brandId)->orWhere('b.id', $brandId);
                        });
                    }
                    if ($search) {
                        $dispBaseQuery->where(function($q) use ($search) {
                            $q->where('sii.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('col.color_name', 'like', "%{$search}%")
                              ->orWhere('sii.api_color', 'like', "%{$search}%");
                        });
                    }

                    $dispTotals = (clone $dispBaseQuery)->select(
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "36" THEN sii.quantity ELSE 0 END) as tot_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "38" THEN sii.quantity ELSE 0 END) as tot_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "40" THEN sii.quantity ELSE 0 END) as tot_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "42" THEN sii.quantity ELSE 0 END) as tot_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "44" THEN sii.quantity ELSE 0 END) as tot_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "46" THEN sii.quantity ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "48" THEN sii.quantity ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "50" THEN sii.quantity ELSE 0 END) as size_50')
                    )->first();

                    $sizes = ['36', '38', '40', '42', '44', '46', '48', '50'];
                    $globalPending = [];
                    $totAllQty = 0;

                    foreach ($sizes as $sz) {
                        $so = floatval($summaryData->sum('size_' . $sz));
                        $dispKey = in_array($sz, ['46', '48', '50']) ? 'size_' . $sz : 'tot_' . $sz;
                        $disp = floatval($dispTotals->{$dispKey} ?? 0);
                        $rem = max(0, $so - $disp);
                        $globalPending[$sz] = $rem;
                        $totAllQty += $rem;
                    }

                    $tot36 = $globalPending['36'];
                    $tot38 = $globalPending['38'];
                    $tot40 = $globalPending['40'];
                    $tot42 = $globalPending['42'];
                    $tot44 = $globalPending['44'];
                    $tot46 = $globalPending['46'];
                    $tot48 = $globalPending['48'];
                    $tot50 = $globalPending['50'];

                    // Distribute global pending across order rows proportionally by ordered qty
                    $pendingPool = $globalPending;
                    $pendingRows = collect();

                    $sortedSummary = $summaryData->sortByDesc('total_qty')->values();

                    foreach ($sortedSummary as $row) {
                        $rowPending = [];
                        $rowTot = 0;
                        foreach ($sizes as $sz) {
                            $ord = floatval($row->{'size_' . $sz});
                            $needed = $pendingPool[$sz];
                            $take = min($ord, $needed);
                            $rowPending['size_' . $sz] = $take;
                            $pendingPool[$sz] -= $take;
                            $rowTot += $take;
                        }

                        if ($rowTot > 0) {
                            $newRow = clone $row;
                            foreach ($sizes as $sz) {
                                $newRow->{'size_' . $sz} = $rowPending['size_' . $sz];
                            }
                            $newRow->total_qty = $rowTot;
                            $pendingRows->push($newRow);
                        }
                    }

                    // Any remaining in pool add to first row
                    foreach ($sizes as $sz) {
                        if ($pendingPool[$sz] > 0 && $pendingRows->count() > 0) {
                            $pendingRows[0]->{'size_' . $sz} += $pendingPool[$sz];
                            $pendingRows[0]->total_qty += $pendingPool[$sz];
                        }
                    }

                    $recordsTotal = $pendingRows->count();
                    $pagedPw = ($length !== -1) ? $pendingRows->slice($start, $length)->values() : $pendingRows;

                    $pwData = [];
                    $sno = $start + 1;
                    foreach ($pagedPw as $row) {
                        $rawProdName = $row->display_item_name ?: '-';
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $pwData[] = [
                            'sno' => $sno++,
                            'product_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsTotal,
                        'data' => $pwData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'pending-party-wise':
                    $soQuery = DB::table('sales_orders')
                        ->whereNull('sales_orders.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('sales_orders.status')
                              ->orWhereRaw('LOWER(sales_orders.status) != ?', ['cancelled']);
                        });

                    if ($fromDate) $soQuery->where('sales_orders.so_date', '>=', $fromDate);
                    if ($toDate) $soQuery->where('sales_orders.so_date', '<=', $toDate);
                    if ($customerId) $soQuery->where('sales_orders.customer_id', $customerId);
                    if ($agentId) $soQuery->where('sales_orders.agent_id', $agentId);

                    $pendingSoIds = $soQuery->pluck('sales_orders.id')->toArray();

                    if (empty($pendingSoIds)) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => [
                                'size_36' => '0', 'size_38' => '0', 'size_40' => '0',
                                'size_42' => '0', 'size_44' => '0', 'size_46' => '0',
                                'size_48' => '0', 'size_50' => '0', 'total_qty' => '0'
                            ]
                        ]);
                    }

                    $partyBaseQuery = DB::table('sales_order_items')->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')->leftJoin('places', 'customers.place_id', '=', 'places.id')->leftJoin('cities', 'customers.city_id', '=', 'cities.id')->leftJoin('sales_agents', 'sales_orders.agent_id', '=', 'sales_agents.id')->leftJoin('zones', 'sales_orders.zone_id', '=', 'zones.id')->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')->leftJoin('stock_entry_items as sei', 'sales_order_items.stock_entry_item_id', '=', 'sei.id')->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')->leftJoin('items as it', 'sei.item_id', '=', 'it.id')->whereIn('sales_order_items.sale_order_id', $pendingSoIds)->whereNull('sales_order_items.deleted_at');

                    if ($brandId) {
                        $partyBaseQuery->where(function($q) use ($brandId) {
                            $q->where('sei.brand_id', $brandId)->orWhere('b.id', $brandId);
                        });
                    }

                    if ($search) {
                        $partyBaseQuery->where(function($q) use ($search) {
                            $q->where('sales_orders.so_no', 'like', "%{$search}%")
                              ->orWhere('sales_orders.order_no', 'like', "%{$search}%")
                              ->orWhere('sales_orders.orderaxe_ref_id', 'like', "%{$search}%")
                              ->orWhere('customers.name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.item_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.category_name', 'like', "%{$search}%")
                              ->orWhere('sales_order_items.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('colors.color_name', 'like', "%{$search}%");
                        });
                    }

                    $partyGrouped = (clone $partyBaseQuery)->select(
                        'sales_orders.id as so_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.order_no',
                        'sales_orders.orderaxe_id',
                        'sales_orders.orderaxe_ref_id',
                        'customers.name as customer_name',
                        DB::raw('COALESCE(places.place_name, cities.city_name, "-") as place'),
                        DB::raw('COALESCE(sales_agents.name, "-") as agent_name'),
                        DB::raw('COALESCE(zones.zone_name, "-") as zone_name'),
                        'sales_order_items.item_name',
                        'sales_order_items.category_name',
                        'sales_order_items.art_no',
                        DB::raw('COALESCE(colors.color_name, sales_order_items.api_color, "-") as color'),
                        'sales_order_items.sleeve',
                        DB::raw("TRIM(CONCAT(COALESCE(b.brand_name, ''), ' ', COALESCE(st.style_name, ''))) as stock_prod_name"),
                        'it.name as item_tbl_name',
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "36" THEN sales_order_items.qty ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "38" THEN sales_order_items.qty ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "40" THEN sales_order_items.qty ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "42" THEN sales_order_items.qty ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "44" THEN sales_order_items.qty ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "46" THEN sales_order_items.qty ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "48" THEN sales_order_items.qty ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sales_order_items.size_id) = "50" THEN sales_order_items.qty ELSE 0 END) as size_50'),
                        DB::raw('SUM(sales_order_items.qty) as total_qty')
                    )->groupBy(
                        'sales_orders.id', 'sales_orders.so_no', 'sales_orders.so_date', 'sales_orders.order_no', 'sales_orders.orderaxe_id', 'sales_orders.orderaxe_ref_id',
                        'customers.name', 'place', 'agent_name', 'zone_name',
                        'sales_order_items.item_name', 'sales_order_items.category_name', 'sales_order_items.art_no', 'color', 'sales_order_items.sleeve',
                        'stock_prod_name', 'item_tbl_name'
                    )->orderBy('sales_orders.id', 'desc');

                    $delPartyQuery = DB::table('sales_invoice_items as sii')
                        ->join('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
                        ->leftJoin('colors as col', 'sii.color_id', '=', 'col.id')
                        ->whereIn('si.so_id', $pendingSoIds)
                        ->whereNull('si.deleted_at')
                        ->whereNull('sii.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        })
                        ->select(
                            'si.so_id',
                            'sii.art_no',
                            DB::raw('COALESCE(col.color_name, sii.api_color, "-") as color'),
                            'sii.sleeve_type as sleeve',
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "36" THEN sii.quantity ELSE 0 END) as size_36'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "38" THEN sii.quantity ELSE 0 END) as size_38'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "40" THEN sii.quantity ELSE 0 END) as size_40'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "42" THEN sii.quantity ELSE 0 END) as size_42'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "44" THEN sii.quantity ELSE 0 END) as size_44'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "46" THEN sii.quantity ELSE 0 END) as size_46'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "48" THEN sii.quantity ELSE 0 END) as size_48'),
                            DB::raw('SUM(CASE WHEN TRIM(sii.size) = "50" THEN sii.quantity ELSE 0 END) as size_50'),
                            DB::raw('SUM(sii.quantity) as total_qty')
                        )->groupBy('si.so_id', 'sii.art_no', 'color', 'sii.sleeve_type')->get();

                    $normPartySleeveFunc = function($s) {
                        if (!empty($s) && str_starts_with(trim($s), '[')) {
                            $dec = json_decode($s, true);
                            $s = is_array($dec) ? ($dec[0] ?? '') : $s;
                        }
                        $u = strtoupper(trim((string)$s));
                        if ($u === 'FULL' || $u === 'FULL SLEEVE' || $u === 'FS' || $u === 'F/S') return 'FS';
                        if ($u === 'HALF' || $u === 'HALF SLEEVE' || $u === 'HS' || $u === 'H/S') return 'HS';
                        return $u ?: '-';
                    };

                    $delPartyMap = [];
                    foreach ($delPartyQuery as $d) {
                        $k = $d->so_id . '|' . trim($d->art_no) . '|' . trim($d->color) . '|' . $normPartySleeveFunc($d->sleeve);
                        $delPartyMap[$k] = [
                            '36' => floatval($d->size_36),
                            '38' => floatval($d->size_38),
                            '40' => floatval($d->size_40),
                            '42' => floatval($d->size_42),
                            '44' => floatval($d->size_44),
                            '46' => floatval($d->size_46),
                            '48' => floatval($d->size_48),
                            '50' => floatval($d->size_50),
                            'total' => floatval($d->total_qty),
                        ];
                    }

                    $allPartyRows = $partyGrouped->get();
                    $pendingPartyRows = collect();
                    $tot36 = $tot38 = $tot40 = $tot42 = $tot44 = $tot46 = $tot48 = $tot50 = $totAllQty = 0;
                    $sizes = ['36', '38', '40', '42', '44', '46', '48', '50'];

                    foreach ($allPartyRows as $row) {
                        $k = $row->so_id . '|' . trim($row->art_no) . '|' . trim($row->color) . '|' . $normPartySleeveFunc($row->sleeve);
                        $d = $delPartyMap[$k] ?? null;

                        $remSizes = [];
                        $rowTot = 0;
                        foreach ($sizes as $sz) {
                            $ord = floatval($row->{'size_' . $sz});
                            $dsp = $d ? floatval($d[$sz] ?? 0) : 0;
                            $rem = max(0, $ord - $dsp);
                            $remSizes['size_' . $sz] = $rem;
                            $rowTot += $rem;
                        }

                        if ($rowTot > 0) {
                            $newRow = clone $row;
                            foreach ($sizes as $sz) {
                                $newRow->{'size_' . $sz} = $remSizes['size_' . $sz];
                            }
                            $newRow->total_qty = $rowTot;

                            $tot36 += $remSizes['size_36'];
                            $tot38 += $remSizes['size_38'];
                            $tot40 += $remSizes['size_40'];
                            $tot42 += $remSizes['size_42'];
                            $tot44 += $remSizes['size_44'];
                            $tot46 += $remSizes['size_46'];
                            $tot48 += $remSizes['size_48'];
                            $tot50 += $remSizes['size_50'];
                            $totAllQty += $rowTot;

                            $pendingPartyRows->push($newRow);
                        }
                    }

                    $partyRecordsTotal = $pendingPartyRows->count();
                    $pagedParty = ($length !== -1) ? $pendingPartyRows->slice($start, $length)->values() : $pendingPartyRows;

                    $partyData = [];
                    $sno = $start + 1;
                    foreach ($pagedParty as $row) {
                        $orderNo = !empty($row->order_no) ? $row->order_no : (!empty($row->orderaxe_ref_id) ? $row->orderaxe_ref_id : $row->so_no);
                        $isOrderaxe = !empty($row->orderaxe_id) || !empty($row->orderaxe_ref_id);
                        if ($isOrderaxe) {
                            $stockName = !empty($row->stock_prod_name) ? trim($row->stock_prod_name) : (!empty($row->item_tbl_name) ? trim($row->item_tbl_name) : '');
                            if (!empty($stockName)) {
                                $rawProdName = $stockName;
                            } elseif (!empty($row->category_name)) {
                                $rawProdName = $row->category_name;
                            } else {
                                $rawProdName = $row->item_name ?: '-';
                            }
                        } else {
                            $rawProdName = $row->item_name ?: '-';
                        }
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $partyData[] = [
                            'sno' => $sno++,
                            'order_no' => '<a href="' . url('sales_orders/view/' . $row->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($orderNo) . '</a>',
                            'order_date' => !empty($row->so_date) ? date('d-m-Y', strtotime($row->so_date)) : '-',
                            'customer_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($row->customer_name ?: '-') . '</span>',
                            'place' => htmlspecialchars($row->place ?: '-'),
                            'sales_executive' => htmlspecialchars($row->agent_name ?: '-'),
                            'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars($row->zone_name ?: '-') . '</span>',
                            'product_name' => '<span class="text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $partyRecordsTotal,
                        'recordsFiltered' => $partyRecordsTotal,
                        'data' => $partyData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'dispatch-report':
                case 'dispatch-product-wise':
                    $baseInvQuery = DB::table('sales_invoice_items as sii')
                        ->join('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
                        ->leftJoin('colors as col', 'sii.color_id', '=', 'col.id')
                        ->leftJoin('stock_entry_items as sei', 'sii.stock_entry_item_id', '=', 'sei.id')
                        ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
                        ->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')
                        ->leftJoin('items as it', 'sei.item_id', '=', 'it.id')
                        ->whereNull('sii.deleted_at')
                        ->whereNull('si.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')
                              ->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        })
                        ->where(function($q) {
                            $q->where('sei.stock_type', 'finished_goods')
                              ->orWhere(function($sub) {
                                  $sub->whereNull('sei.stock_type')
                                      ->whereNotNull('sii.art_no')
                                      ->where('sii.art_no', '!=', '');
                              });
                        })
                        ->whereNotNull('sii.art_no')
                        ->where('sii.art_no', '!=', '');

                    if ($fromDate) $baseInvQuery->where('si.inv_date', '>=', $fromDate);
                    if ($toDate) $baseInvQuery->where('si.inv_date', '<=', $toDate);
                    if ($customerId) $baseInvQuery->where('si.customer_id', $customerId);
                    if ($agentId) $baseInvQuery->where('si.agent_id', $agentId);
                    if ($brandId) {
                        $baseInvQuery->where(function($q) use ($brandId) {
                            $q->where('sei.brand_id', $brandId)
                              ->orWhere('b.id', $brandId);
                        });
                    }

                    if ($search) {
                        $baseInvQuery->where(function($q) use ($search) {
                            $q->where('sii.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('col.color_name', 'like', "%{$search}%")
                              ->orWhere('sii.api_color', 'like', "%{$search}%");
                        });
                    }

                    $pwGrouped = (clone $baseInvQuery)->select(
                        DB::raw('(CASE 
                            WHEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, ""))) != "" 
                                THEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, "")))
                            WHEN it.name IS NOT NULL AND TRIM(it.name) != "" 
                                THEN it.name
                            WHEN sii.art_no IS NOT NULL AND TRIM(sii.art_no) != "" AND sii.art_no NOT REGEXP "^BC[0-9]"
                                THEN sii.art_no
                            ELSE sii.art_no
                        END) as display_item_name'),
                        'sii.art_no',
                        DB::raw('COALESCE(col.color_name, sii.api_color, "-") as color'),
                        'sii.sleeve_type as sleeve',
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "36" THEN sii.quantity ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "38" THEN sii.quantity ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "40" THEN sii.quantity ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "42" THEN sii.quantity ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "44" THEN sii.quantity ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "46" THEN sii.quantity ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "48" THEN sii.quantity ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "50" THEN sii.quantity ELSE 0 END) as size_50'),
                        DB::raw('SUM(sii.quantity) as total_qty')
                    )->groupBy('display_item_name', 'sii.art_no', 'color', 'sii.sleeve_type')
                     ->orderBy('display_item_name', 'asc');

                    $summaryData = $pwGrouped->get();
                    $recordsTotal = count($summaryData);
                    $tot36 = floatval($summaryData->sum('size_36'));
                    $tot38 = floatval($summaryData->sum('size_38'));
                    $tot40 = floatval($summaryData->sum('size_40'));
                    $tot42 = floatval($summaryData->sum('size_42'));
                    $tot44 = floatval($summaryData->sum('size_44'));
                    $tot46 = floatval($summaryData->sum('size_46'));
                    $tot48 = floatval($summaryData->sum('size_48'));
                    $tot50 = floatval($summaryData->sum('size_50'));
                    $totAllQty = floatval($summaryData->sum('total_qty'));

                    $pagedPw = ($length !== -1) ? $summaryData->slice($start, $length)->values() : $summaryData;

                    $pwData = [];
                    $sno = $start + 1;
                    foreach ($pagedPw as $row) {
                        $rawProdName = $row->display_item_name ?: '-';
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $pwData[] = [
                            'sno' => $sno++,
                            'product_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsTotal,
                        'data' => $pwData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'dispatch-party-wise':
                    $basePartyQuery = DB::table('sales_invoice_items as sii')
                        ->join('sales_invoices as si', 'sii.sales_invoice_id', '=', 'si.id')
                        ->leftJoin('sales_orders as so', 'si.so_id', '=', 'so.id')
                        ->leftJoin('customers as c', 'si.customer_id', '=', 'c.id')
                        ->leftJoin('places as p', 'c.place_id', '=', 'p.id')
                        ->leftJoin('cities as ci', 'c.city_id', '=', 'ci.id')
                        ->leftJoin('sales_agents as sa', 'si.agent_id', '=', 'sa.id')
                        ->leftJoin('zones as z', 'c.zone_id', '=', 'z.id')
                        ->leftJoin('colors as col', 'sii.color_id', '=', 'col.id')
                        ->leftJoin('stock_entry_items as sei', 'sii.stock_entry_item_id', '=', 'sei.id')
                        ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
                        ->leftJoin('styles as st', 'sei.style_id', '=', 'st.id')
                        ->leftJoin('items as it', 'sei.item_id', '=', 'it.id')
                        ->whereNull('sii.deleted_at')
                        ->whereNull('si.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')
                              ->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        })
                        ->where(function($q) {
                            $q->where('sei.stock_type', 'finished_goods')
                              ->orWhere(function($sub) {
                                  $sub->whereNull('sei.stock_type')
                                      ->whereNotNull('sii.art_no')
                                      ->where('sii.art_no', '!=', '');
                              });
                        })
                        ->whereNotNull('sii.art_no')
                        ->where('sii.art_no', '!=', '');

                    if ($fromDate) $basePartyQuery->where('si.inv_date', '>=', $fromDate);
                    if ($toDate) $basePartyQuery->where('si.inv_date', '<=', $toDate);
                    if ($customerId) $basePartyQuery->where('si.customer_id', $customerId);
                    if ($agentId) $basePartyQuery->where('si.agent_id', $agentId);
                    if ($brandId) {
                        $basePartyQuery->where(function($q) use ($brandId) {
                            $q->where('sei.brand_id', $brandId)
                              ->orWhere('b.id', $brandId);
                        });
                    }

                    if ($search) {
                        $basePartyQuery->where(function($q) use ($search) {
                            $q->where('si.inv_no', 'like', "%{$search}%")
                              ->orWhere('so.so_no', 'like', "%{$search}%")
                              ->orWhere('so.order_no', 'like', "%{$search}%")
                              ->orWhere('so.orderaxe_ref_id', 'like', "%{$search}%")
                              ->orWhere('c.name', 'like', "%{$search}%")
                              ->orWhere('sii.art_no', 'like', "%{$search}%")
                              ->orWhere('b.brand_name', 'like', "%{$search}%")
                              ->orWhere('st.style_name', 'like', "%{$search}%")
                              ->orWhere('it.name', 'like', "%{$search}%")
                              ->orWhere('col.color_name', 'like', "%{$search}%");
                        });
                    }

                    $partyGrouped = (clone $basePartyQuery)->select(
                        'si.id as invoice_id',
                        'si.inv_no',
                        'si.inv_date',
                        'so.id as so_id',
                        'so.so_no',
                        'so.so_date',
                        'so.order_no',
                        'c.name as customer_name',
                        DB::raw('COALESCE(p.place_name, ci.city_name, "-") as place'),
                        DB::raw('COALESCE(sa.name, "-") as agent_name'),
                        DB::raw('COALESCE(z.zone_name, "-") as zone_name'),
                        DB::raw('(CASE 
                            WHEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, ""))) != "" 
                                THEN TRIM(CONCAT(COALESCE(b.brand_name, ""), " ", COALESCE(st.style_name, "")))
                            WHEN it.name IS NOT NULL AND TRIM(it.name) != "" 
                                THEN it.name
                            WHEN sii.art_no IS NOT NULL AND TRIM(sii.art_no) != "" AND sii.art_no NOT REGEXP "^BC[0-9]"
                                THEN sii.art_no
                            ELSE sii.art_no
                        END) as display_item_name'),
                        'sii.art_no',
                        DB::raw('COALESCE(col.color_name, sii.api_color, "-") as color'),
                        'sii.sleeve_type as sleeve',
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "36" THEN sii.quantity ELSE 0 END) as size_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "38" THEN sii.quantity ELSE 0 END) as size_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "40" THEN sii.quantity ELSE 0 END) as size_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "42" THEN sii.quantity ELSE 0 END) as size_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "44" THEN sii.quantity ELSE 0 END) as size_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "46" THEN sii.quantity ELSE 0 END) as size_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "48" THEN sii.quantity ELSE 0 END) as size_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "50" THEN sii.quantity ELSE 0 END) as size_50'),
                        DB::raw('SUM(sii.quantity) as total_qty')
                    )->groupBy(
                        'si.id', 'si.inv_no', 'si.inv_date', 'so.id', 'so.so_no', 'so.so_date', 'so.order_no',
                        'customer_name', 'place', 'agent_name', 'zone_name',
                        'display_item_name', 'sii.art_no', 'color', 'sii.sleeve_type'
                    )->orderBy('si.id', 'desc');

                    $sizeTotals = (clone $basePartyQuery)->select(
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "36" THEN sii.quantity ELSE 0 END) as tot_36'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "38" THEN sii.quantity ELSE 0 END) as tot_38'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "40" THEN sii.quantity ELSE 0 END) as tot_40'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "42" THEN sii.quantity ELSE 0 END) as tot_42'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "44" THEN sii.quantity ELSE 0 END) as tot_44'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "46" THEN sii.quantity ELSE 0 END) as tot_46'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "48" THEN sii.quantity ELSE 0 END) as tot_48'),
                        DB::raw('SUM(CASE WHEN TRIM(sii.size) = "50" THEN sii.quantity ELSE 0 END) as tot_50'),
                        DB::raw('SUM(sii.quantity) as tot_all')
                    )->first();

                    $tot36 = floatval($sizeTotals->tot_36 ?? 0);
                    $tot38 = floatval($sizeTotals->tot_38 ?? 0);
                    $tot40 = floatval($sizeTotals->tot_40 ?? 0);
                    $tot42 = floatval($sizeTotals->tot_42 ?? 0);
                    $tot44 = floatval($sizeTotals->tot_44 ?? 0);
                    $tot46 = floatval($sizeTotals->tot_46 ?? 0);
                    $tot48 = floatval($sizeTotals->tot_48 ?? 0);
                    $tot50 = floatval($sizeTotals->tot_50 ?? 0);
                    $totAllQty = floatval($sizeTotals->tot_all ?? 0);

                    $countQuery = DB::table(DB::raw("({$partyGrouped->toSql()}) as sub"))->mergeBindings($partyGrouped);
                    $partyRecordsTotal = $countQuery->count();

                    $pagedParty = ($length !== -1) ? $partyGrouped->offset($start)->limit($length)->get() : $partyGrouped->get();

                    $partyData = [];
                    $sno = $start + 1;
                    foreach ($pagedParty as $row) {
                        $invNo = !empty($row->inv_no) ? $row->inv_no : '-';
                        $invLink = !empty($row->invoice_id) ? url('sales_invoices/view/' . $row->invoice_id) : null;
                        $invNoHtml = ($invLink && $invNo !== '-') 
                            ? '<a href="' . $invLink . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($invNo) . '</a>' 
                            : htmlspecialchars($invNo);
                        $formattedInvDate = !empty($row->inv_date) ? date('d-m-Y', strtotime($row->inv_date)) : '-';

                        $rawProdName = $row->display_item_name ?: '-';
                        $cleanProdName = preg_replace('/F\/S/i', 'FS', $rawProdName);
                        $cleanProdName = preg_replace('/H\/S/i', 'HS', $cleanProdName);

                        $rawSleeve = $row->sleeve;
                        if (!empty($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                            $dec = json_decode($rawSleeve, true);
                            $rawSleeve = is_array($dec) ? ($dec[0] ?? '') : $rawSleeve;
                        }
                        $slUpper = strtoupper(trim((string)$rawSleeve));
                        $sleeveDisplay = ($slUpper === 'FULL' || $slUpper === 'FULL SLEEVE' || $slUpper === 'FS' || $slUpper === 'F/S') ? 'FS' : (($slUpper === 'HALF' || $slUpper === 'HALF SLEEVE' || $slUpper === 'HS' || $slUpper === 'H/S') ? 'HS' : ($rawSleeve ?: '-'));

                        $partyData[] = [
                            'sno' => $sno++,
                            'invoice_no' => $invNoHtml,
                            'invoice_date' => $formattedInvDate,
                            'order_no' => $invNoHtml,
                            'order_date' => $formattedInvDate,
                            'customer_name' => '<span class="fw-semibold text-dark">' . htmlspecialchars($row->customer_name ?: '-') . '</span>',
                            'place' => htmlspecialchars($row->place ?: '-'),
                            'sales_executive' => htmlspecialchars($row->agent_name ?: '-'),
                            'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars($row->zone_name ?: '-') . '</span>',
                            'product_name' => '<span class="text-dark">' . htmlspecialchars($cleanProdName) . '</span>',
                            'art_no' => '<span class="fw-bold text-primary">' . htmlspecialchars($row->art_no ?: '-') . '</span>',
                            'color' => htmlspecialchars($row->color ?: '-'),
                            'sleeve' => '<span class="badge bg-label-info">' . htmlspecialchars($sleeveDisplay) . '</span>',
                            'size_36' => floatval($row->size_36) > 0 ? number_format($row->size_36, 0) : '-',
                            'size_38' => floatval($row->size_38) > 0 ? number_format($row->size_38, 0) : '-',
                            'size_40' => floatval($row->size_40) > 0 ? number_format($row->size_40, 0) : '-',
                            'size_42' => floatval($row->size_42) > 0 ? number_format($row->size_42, 0) : '-',
                            'size_44' => floatval($row->size_44) > 0 ? number_format($row->size_44, 0) : '-',
                            'size_46' => floatval($row->size_46) > 0 ? number_format($row->size_46, 0) : '-',
                            'size_48' => floatval($row->size_48) > 0 ? number_format($row->size_48, 0) : '-',
                            'size_50' => floatval($row->size_50) > 0 ? number_format($row->size_50, 0) : '-',
                            'total_qty' => '<span class="fw-bold text-danger">' . number_format($row->total_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $partyRecordsTotal,
                        'recordsFiltered' => $partyRecordsTotal,
                        'data' => $partyData,
                        'totals' => [
                            'size_36' => number_format($tot36, 0),
                            'size_38' => number_format($tot38, 0),
                            'size_40' => number_format($tot40, 0),
                            'size_42' => number_format($tot42, 0),
                            'size_44' => number_format($tot44, 0),
                            'size_46' => number_format($tot46, 0),
                            'size_48' => number_format($tot48, 0),
                            'size_50' => number_format($tot50, 0),
                            'total_qty' => number_format($totAllQty, 0),
                        ]
                    ]);

                case 'order-items':
                    $orderId = $request->order_id;
                    $soNo = $request->so_no;

                    $order = null;
                    if (!empty($orderId) && is_numeric($orderId)) {
                        $order = SalesOrder::find($orderId);
                    }
                    if (!$order && !empty($soNo)) {
                        $order = SalesOrder::where('so_no', $soNo)->first();
                    }

                    if (!$order) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => []
                        ]);
                    }

                    $itemsBaseQuery = SalesOrderItem::where('sale_order_id', $order->id)->whereNull('deleted_at');
                    $totalRecords = (clone $itemsBaseQuery)->count();
                    $totalItemQty = (float)(clone $itemsBaseQuery)->sum('qty');

                    $filteredQuery = clone $itemsBaseQuery;
                    if ($search) {
                        $filteredQuery->where(function($q) use ($search) {
                            $q->where('item_name', 'like', "%{$search}%")
                              ->orWhere('size_id', 'like', "%{$search}%")
                              ->orWhere('sleeve', 'like', "%{$search}%")
                              ->orWhere('qty', 'like', "%{$search}%");
                        });
                    }
                    $filteredRecords = (clone $filteredQuery)->count();

                    $orderColIdx = $request->input('order.0.column');
                    $orderDir = $request->input('order.0.dir', 'asc');
                    $columnsMap = [
                        1 => 'item_name',
                        2 => 'size_id',
                        3 => 'sleeve',
                        4 => 'qty'
                    ];

                    if (isset($columnsMap[$orderColIdx])) {
                        $filteredQuery->orderBy($columnsMap[$orderColIdx], $orderDir);
                    } else {
                        $filteredQuery->orderBy('id', 'asc');
                    }

                    if ($length !== -1) {
                        $filteredQuery->offset($start)->limit($length);
                    }

                    $items = $filteredQuery->get();

                    $data = [];
                    foreach ($items as $item) {
                        $s = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;
                        $sd = ($s == 'Full' || $s == 'Full Sleeve') ? 'F/S' : (($s == 'Half' || $s == 'Half Sleeve') ? 'H/S' : ($s ?? '-'));

                        $data[] = [
                            'name' => (string)($item->item_name ?? '-'),
                            'size' => (string)($item->size_id ?? '-'),
                            'sleeve' => (string)$sd,
                            'qty' => number_format((float)($item->qty ?? 0), 0)
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $filteredRecords,
                        'data' => $data,
                        'totals' => [
                            'qty' => number_format($totalItemQty, 0)
                        ]
                    ]);

                case 'comparison-report':
                    $currentYear = (int)($request->comparison_year ?? date('Y'));
                    $prevYear = $currentYear - 1;

                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];

                    $query = DB::table('sales_invoice_items')->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')->leftJoin('brands', DB::raw('COALESCE(stock_entry_items.brand_id, sales_invoices.brand_id)'), '=', 'brands.id')->whereNull('sales_invoices.deleted_at')->whereNull('sales_invoice_items.deleted_at')->where('sales_invoices.invoice_status', '!=', 'Cancelled')->whereIn(DB::raw('YEAR(sales_invoices.inv_date)'), [$prevYear, $currentYear]);

                    if ($request->filled('customer_id')) {
                        $query->where('sales_invoices.customer_id', $request->customer_id);
                    }
                    if ($request->filled('agent_id')) {
                        $query->where('sales_invoices.agent_id', $request->agent_id);
                    }
                    if ($request->filled('brand_id')) {
                        $query->where(DB::raw('COALESCE(stock_entry_items.brand_id, sales_invoices.brand_id)'), $request->brand_id);
                    }

                    $rawSales = $query->selectRaw("
                        YEAR(sales_invoices.inv_date) as yr,
                        MONTH(sales_invoices.inv_date) as mo,
                        COALESCE(brands.id, 0) as brand_id,
                        COALESCE(brands.brand_name, 'Other') as brand_name,
                        SUM(sales_invoice_items.quantity) as total_qty
                    ")->groupBy('yr', 'mo', 'brand_id', 'brand_name')->get();

                    $brandMap = [];
                    foreach ($rawSales as $row) {
                        $bId = (int)$row->brand_id;
                        if (!isset($brandMap[$bId])) {
                            $brandMap[$bId] = $row->brand_name;
                        }
                    }

                    if ($request->filled('brand_id')) {
                        $selBrand = Brand::find($request->brand_id);
                        if ($selBrand && !isset($brandMap[$selBrand->id])) {
                            $brandMap[$selBrand->id] = $selBrand->brand_name;
                        }
                    }

                    if (empty($brandMap)) {
                        $allBrands = Brand::orderBy('brand_name')->limit(10)->get();
                        foreach ($allBrands as $b) {
                            $brandMap[$b->id] = $b->brand_name;
                        }
                    }

                    asort($brandMap);

                    $brandsList = [];
                    foreach ($brandMap as $bId => $bName) {
                        $brandsList[] = [
                            'id' => $bId,
                            'name' => $bName
                        ];
                    }

                    $matrix = [];
                    foreach ($rawSales as $row) {
                        $yr = (int)$row->yr;
                        $mo = (int)$row->mo;
                        $bId = (int)$row->brand_id;
                        $qty = (float)$row->total_qty;

                        if (!isset($matrix[$mo])) {
                            $matrix[$mo] = [];
                        }
                        if (!isset($matrix[$mo][$bId])) {
                            $matrix[$mo][$bId] = [];
                        }
                        $matrix[$mo][$bId][$yr] = ($matrix[$mo][$bId][$yr] ?? 0) + $qty;
                    }

                    $monthRows = [];
                    $brandAnnualTotals = [];
                    foreach ($brandsList as $b) {
                        $brandAnnualTotals[$b['id']] = ['prev' => 0, 'curr' => 0];
                    }
                    $grandAnnualPrev = 0;
                    $grandAnnualCurr = 0;

                    foreach ($months as $mNum => $mName) {
                        $rowBrandData = [];
                        $monthTotalPrev = 0;
                        $monthTotalCurr = 0;

                        foreach ($brandsList as $b) {
                            $bId = $b['id'];
                            $pQty = (float)($matrix[$mNum][$bId][$prevYear] ?? 0);
                            $cQty = (float)($matrix[$mNum][$bId][$currentYear] ?? 0);

                            $rowBrandData[$bId] = [
                                'prev_qty' => $pQty,
                                'curr_qty' => $cQty,
                            ];

                            $monthTotalPrev += $pQty;
                            $monthTotalCurr += $cQty;

                            $brandAnnualTotals[$bId]['prev'] += $pQty;
                            $brandAnnualTotals[$bId]['curr'] += $cQty;
                        }

                        $monthGrowth = $monthTotalPrev > 0
                            ? (($monthTotalCurr - $monthTotalPrev) / $monthTotalPrev) * 100
                            : ($monthTotalCurr > 0 ? 100 : 0);

                        $grandAnnualPrev += $monthTotalPrev;
                        $grandAnnualCurr += $monthTotalCurr;

                        $monthRows[] = [
                            'month_num' => $mNum,
                            'month_name' => $mName,
                            'brands' => $rowBrandData,
                            'total_prev' => $monthTotalPrev,
                            'total_curr' => $monthTotalCurr,
                            'growth' => round($monthGrowth, 1)
                        ];
                    }

                    $grandGrowth = $grandAnnualPrev > 0
                        ? (($grandAnnualCurr - $grandAnnualPrev) / $grandAnnualPrev) * 100
                        : ($grandAnnualCurr > 0 ? 100 : 0);

                    return response()->json([
                        'prev_year' => $prevYear,
                        'curr_year' => $currentYear,
                        'brands' => $brandsList,
                        'months' => $monthRows,
                        'brand_totals' => $brandAnnualTotals,
                        'grand_total' => [
                            'prev' => $grandAnnualPrev,
                            'curr' => $grandAnnualCurr,
                            'growth' => round($grandGrowth, 1)
                        ]
                    ]);

                case 'sales-gst-report':
                case 'sales-report':
                    $countQuery = SalesInvoice::whereNull('deleted_at');
                    if ($fromDate) $countQuery->where('inv_date', '>=', $fromDate);
                    if ($toDate) $countQuery->where('inv_date', '<=', $toDate);
                    if ($customerId) $countQuery->where('customer_id', $customerId);
                    if ($agentId) $countQuery->where('agent_id', $agentId);
                    if ($request->filled('invoice_status')) {
                        $invStatus = trim((string)$request->invoice_status);
                        if ($invStatus === 'Cancelled') {
                            $countQuery->where(function($q) {
                                $q->where('invoice_status', 'Cancelled')
                                  ->orWhereRaw('LOWER(einvoice_status) = ?', ['cancelled']);
                            });
                        } else {
                            $countQuery->where('invoice_status', $invStatus);
                        }
                    }
                    $einvoiceStatus = strtolower(trim((string)$request->einvoice_status));
                    if ($einvoiceStatus === 'generated') {
                        $countQuery->whereRaw('LOWER(einvoice_status) = ?', ['generated']);
                    } elseif ($einvoiceStatus === 'not_generated') {
                        $countQuery->where(function($q) {
                            $q->whereNull('einvoice_status')
                              ->orWhereRaw('LOWER(einvoice_status) != ?', ['generated']);
                        });
                    }

                    if ($request->filled('gst_filing_status')) {
                        $gstStatus = strtolower(trim((string)$request->gst_filing_status));
                        if ($gstStatus === 'filed') {
                            $countQuery->where('is_gst_filed', 1);
                        } elseif ($gstStatus === 'pending') {
                            $countQuery->where(function($q) {
                                $q->whereNull('is_gst_filed')->orWhere('is_gst_filed', 0);
                            });
                        }
                    }
                    if ($search) {
                        $countQuery->where(function($q) use ($search) {
                            $q->where('inv_no', 'like', "%{$search}%");

                            if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                                $sNum = (int)$sm[1];
                                if ($sNum > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                                    $dbNum = ($sNum >= 316) ? ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                                    $q->orWhere('inv_no', 'like', "%CDW/{$dbNum}%");
                                }
                            } elseif (is_numeric(trim($search))) {
                                $numVal = (int)trim($search);
                                if ($numVal > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                                    $cdwNum = ($numVal >= 316) ? ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                                    $q->orWhere('inv_no', 'like', "%CDW/{$cdwNum}/%");
                                }
                            } elseif (stripos($search, 'CD') !== false && stripos($search, 'CDW') === false) {
                                $q->orWhere('inv_no', 'like', "%CDW/%");
                            }

                            $q->orWhereHas('customer', function($c) use ($search) {
                                  $c->where('name', 'like', "%{$search}%")
                                    ->orWhere('gst_no', 'like', "%{$search}%")
                                    ->orWhereHas('place', function($p) use ($search) {
                                        $p->where('place_name', 'like', "%{$search}%");
                                    })
                                    ->orWhereHas('city', function($ct) use ($search) {
                                        $ct->where('city_name', 'like', "%{$search}%");
                                    });
                              });
                        });
                    }

                    $unfilteredRecords = SalesInvoice::whereNull('deleted_at')->count();
                    $filteredRecords = $countQuery->count();
                    $pendingGstCount = (clone $countQuery)->where(function($q) {
                        $q->whereNull('is_gst_filed')->orWhere('is_gst_filed', 0);
                    })->count();
                    $totalCgst = (float)(clone $countQuery)->sum('cgst');
                    $totalSgst = (float)(clone $countQuery)->sum('sgst');
                    $totalIgst = (float)(clone $countQuery)->sum('igst');
                    $totalSubTotal = (float)(clone $countQuery)->sum('sub_total');
                    $totalDiscount = (float)(clone $countQuery)->sum('discount');
                    $totalGrandTotal = (float)(clone $countQuery)->sum('grand_total');
                    $totalTaxableValue = $totalSubTotal - $totalDiscount;
                    $totalQty = (float)\App\Models\SalesInvoiceItem::whereIn('sales_invoice_id', (clone $countQuery)->select('id'))->whereNull('deleted_at')->sum('quantity');
                    $totalRoundOff = (float)(clone $countQuery)->selectRaw("SUM(CASE WHEN LOWER(COALESCE(round_off_type, '')) IN ('less', 'subtract', '-') THEN -ABS(COALESCE(round_off, 0)) ELSE ABS(COALESCE(round_off, 0)) END) as total_round_off")->value('total_round_off');

                    $orderColIdx = $request->order[0]['column'] ?? null;
                    $orderDir = $request->order[0]['dir'] ?? 'desc';
                    $columnMap = [
                        1 => 'inv_no',
                        2 => 'inv_date',
                        7 => 'sub_total',
                        8 => 'discount',
                        10 => 'cgst_percent',
                        11 => 'cgst',
                        12 => 'sgst_percent',
                        13 => 'sgst',
                        14 => 'igst_percent',
                        15 => 'igst',
                        16 => 'round_off',
                        17 => 'grand_total',
                        18 => 'einvoice_status',
                    ];

                    $invoicesQuery = $countQuery->with(['customer.place', 'customer.city', 'items', 'gstFiledBy']);
                    if ($orderColIdx !== null && isset($columnMap[$orderColIdx])) {
                        $invoicesQuery->orderBy($columnMap[$orderColIdx], $orderDir);
                    } else {
                        $invoicesQuery->orderBy('inv_date', 'desc')->orderBy('id', 'desc');
                    }

                    if ($length !== -1) {
                        $invoicesQuery->offset($start)->limit($length);
                    }
                    $invoices = $invoicesQuery->get();

                    $data = [];
                    $sno = $start + 1;
                    foreach ($invoices as $invoice) {
                        $isGenerated = (!empty($invoice->einvoice_status) && strtolower((string)$invoice->einvoice_status) === 'generated');
                        $einvoiceStatusBadge = $isGenerated 
                            ? '<span class="badge bg-label-success rounded-pill px-3 py-1"><i class="ri ri-checkbox-circle-line me-1"></i>Generated</span>' 
                            : '<span class="badge bg-label-danger rounded-pill px-3 py-1"><i class="ri ri-close-circle-line me-1"></i>Not Generated</span>';
                        $einvoiceStatusRaw = $isGenerated ? 'Generated' : 'Not Generated';

                        $isGstFiled = (bool)($invoice->is_gst_filed ?? false);
                        $filedByName = $invoice->gstFiledBy ? htmlspecialchars((string)$invoice->gstFiledBy->name) : 'Staff';
                        $filedDateShort = $invoice->gst_filed_at ? date('d-m-Y', strtotime((string)$invoice->gst_filed_at)) : '';
                        $filedDateFull = $invoice->gst_filed_at ? date('d-M-Y h:i A', strtotime((string)$invoice->gst_filed_at)) : '';

                        $invNoStr = htmlspecialchars((string)($invoice->inv_no ?? '-'));

                        if ($isGstFiled) {
                            $gstStatusBadge = '
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge bg-label-success rounded-pill px-2 py-1 view-gst-info-btn cursor-pointer" 
                                          style="cursor: pointer;" 
                                          title="Click to view details"
                                          data-inv="' . $invNoStr . '"
                                          data-user="' . $filedByName . '"
                                          data-date="' . $filedDateFull . '">
                                        <i class="ri ri-checkbox-circle-line me-1"></i>Filed <i class="ri ri-information-line ms-1" style="font-size: 11px;"></i>
                                    </span>
                                    <small class="text-muted mt-1" style="font-size: 10px; line-height: 1.2;">
                                        <span class="fw-semibold text-dark">' . $filedByName . '</span><br>' . $filedDateShort . '
                                    </small>
                                </div>';
                        } else {
                            $gstStatusBadge = '<span class="badge bg-label-warning text-dark rounded-pill px-2 py-1"><i class="ri ri-time-line me-1"></i>Pending</span>';
                        }

                        $checkboxHtml = $isGstFiled 
                            ? '<span class="text-success" title="Already Filed"><i class="ri ri-checkbox-circle-fill"></i></span>' 
                            : '<input type="checkbox" class="form-check-input gst-invoice-cb" value="' . $invoice->id . '">';
                        $invViewUrl = url('sales_invoices/view/' . $invoice->id);
                        $invNoHtml = '<a href="' . $invViewUrl . '" target="_blank" class="fw-bold text-primary">' . $invNoStr . '</a>';

                        $isCancelled = ($invoice->invoice_status === 'Cancelled' || strtolower((string)$invoice->einvoice_status) === 'cancelled');
                        if ($isCancelled) {
                            $invNoHtml .= '<br><span class="badge bg-danger rounded-pill px-2 py-1 mt-1" style="font-size: 10px;"><i class="ri ri-close-circle-line me-1"></i>Cancelled</span>';
                        }

                        $custName = htmlspecialchars((string)(optional($invoice->customer)->name ?? '-'));
                        $gstNo = htmlspecialchars((string)(optional($invoice->customer)->gst_no ?: '-'));
                        $place = htmlspecialchars((string)(optional(optional($invoice->customer)->place)->place_name ?? (optional(optional($invoice->customer)->city)->city_name ?? '-')));


                        $invDateStr = $invoice->inv_date ? date('d-m-Y', strtotime((string)$invoice->inv_date)) : '-';

                        $cgstPercent = (float)($invoice->cgst_percent ?? 0);
                        $cgstAmount = (float)($invoice->cgst ?? 0);
                        $sgstPercent = (float)($invoice->sgst_percent ?? 0);
                        $sgstAmount = (float)($invoice->sgst ?? 0);
                        $igstPercent = (float)($invoice->igst_percent ?? 0);
                        $igstAmount = (float)($invoice->igst ?? 0);

                        $igstPercentDisplay = ($igstPercent > 0) ? number_format($igstPercent, 2) . '%' : '-';
                        $igstAmountDisplay = ($igstAmount > 0) ? '₹' . number_format($igstAmount, 2) : '-';

                        $subTotal = (float)($invoice->sub_total ?? 0);
                        $discountAmt = (float)($invoice->discount ?? 0);
                        $taxableValue = $subTotal - $discountAmt;
                        $grandTotal = (float)($invoice->grand_total ?? 0);
                        $itemQty = (float)($invoice->items ? $invoice->items->sum('quantity') : 0);

                        $discountPercentVal = (float)($invoice->sales_discount ?? 0);
                        if ($discountPercentVal <= 0 && !empty($invoice->discount_percent)) {
                            $discountPercentVal = (float)$invoice->discount_percent;
                        }
                        if ($discountPercentVal <= 0 && $discountAmt > 0 && $subTotal > 0) {
                            $discountPercentVal = ($discountAmt / $subTotal) * 100;
                        }
                        $discountPercentDisplay = ($discountPercentVal > 0) ? (number_format($discountPercentVal, 2) . '%') : '-';

                        $roundOffVal = (float)($invoice->round_off ?? 0);
                        if (in_array(strtolower((string)$invoice->round_off_type), ['less', 'subtract', '-'])) {
                            $roundOffVal = -$roundOffVal;
                        }
                        $roundOffDisplay = ($roundOffVal != 0) ? (($roundOffVal < 0 ? '-' : '') . '₹' . number_format(abs($roundOffVal), 2)) : '-';

                        $data[] = [
                            'checkbox' => $checkboxHtml,
                            'sno' => $sno++,
                            'inv_no' => $invNoHtml,
                            'inv_no_raw' => $invNoStr,
                            'inv_date' => $invDateStr,
                            'customer_name' => $custName,
                            'gst_no' => $gstNo,
                            'place' => $place,
                            'qty' => number_format($itemQty),
                            'sub_total' => '₹' . number_format($subTotal, 2),
                            'discount_percent' => $discountPercentDisplay,
                            'discount' => ($discountAmt > 0) ? '₹' . number_format($discountAmt, 2) : '-',
                            'taxable_value' => '₹' . number_format($taxableValue, 2),
                            'cgst_percent' => number_format($cgstPercent, 2) . '%',
                            'cgst_amount' => '₹' . number_format($cgstAmount, 2),
                            'sgst_percent' => number_format($sgstPercent, 2) . '%',
                            'sgst_amount' => '₹' . number_format($sgstAmount, 2),
                            'igst_percent' => $igstPercentDisplay,
                            'igst_amount' => $igstAmountDisplay,
                            'round_off' => $roundOffDisplay,
                            'total_amount' => '₹' . number_format($grandTotal, 2),
                            'einvoice_status' => $einvoiceStatusBadge,
                            'einvoice_status_raw' => $einvoiceStatusRaw,
                            'gst_filing_status' => $gstStatusBadge,
                            'is_gst_filed' => $isGstFiled ? 1 : 0,
                        ];
                    }

                    $totalRoundOffDisplay = ($totalRoundOff != 0) ? (($totalRoundOff < 0 ? '-' : '') . '₹' . number_format(abs($totalRoundOff), 2)) : '₹0.00';

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $unfilteredRecords,
                        'recordsFiltered' => $filteredRecords,
                        'pending_gst_count' => $pendingGstCount,
                        'data' => $data,
                        'totals' => [
                            'qty' => number_format($totalQty),
                            'sub_total' => '₹' . number_format($totalSubTotal, 2),
                            'discount' => ($totalDiscount > 0) ? '₹' . number_format($totalDiscount, 2) : '-',
                            'taxable_value' => '₹' . number_format($totalTaxableValue, 2),
                            'cgst_amount' => '₹' . number_format($totalCgst, 2),
                            'sgst_amount' => '₹' . number_format($totalSgst, 2),
                            'igst_amount' => ($totalIgst > 0) ? '₹' . number_format($totalIgst, 2) : '-',
                            'round_off' => $totalRoundOffDisplay,
                            'total_amount' => '₹' . number_format($totalGrandTotal, 2),
                            'cgst_raw' => $totalCgst,
                            'sgst_raw' => $totalSgst,
                            'igst_raw' => $totalIgst,
                        ]
                    ]);

                case 'invoice-report':
                    $countQuery = SalesInvoice::whereNull('deleted_at');
                    if ($fromDate) $countQuery->where('inv_date', '>=', $fromDate);
                    if ($toDate) $countQuery->where('inv_date', '<=', $toDate);
                    if ($customerId) $countQuery->where('customer_id', $customerId);
                    if ($agentId) $countQuery->where('agent_id', $agentId);
                    $einvoiceStatus = strtolower(trim((string)$request->einvoice_status));
                    if ($einvoiceStatus === 'generated') {
                        $countQuery->whereRaw('LOWER(einvoice_status) = ?', ['generated']);
                    } elseif ($einvoiceStatus === 'not_generated') {
                        $countQuery->where(function($q) {
                            $q->whereNull('einvoice_status')
                              ->orWhereRaw('LOWER(einvoice_status) != ?', ['generated']);
                        });
                    }

                    if ($request->filled('gst_filing_status')) {
                        $gstStatus = strtolower(trim((string)$request->gst_filing_status));
                        if ($gstStatus === 'filed') {
                            $countQuery->where('is_gst_filed', 1);
                        } elseif ($gstStatus === 'pending') {
                            $countQuery->where(function($q) {
                                $q->whereNull('is_gst_filed')->orWhere('is_gst_filed', 0);
                            });
                        }
                    }
                    if ($search) {
                        $countQuery->where(function($q) use ($search) {
                            $q->where('inv_no', 'like', "%{$search}%");

                            if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                                $sNum = (int)$sm[1];
                                if ($sNum > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                                    $dbNum = ($sNum >= 316) ? ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                                    $q->orWhere('inv_no', 'like', "%CDW/{$dbNum}%");
                                }
                            } elseif (is_numeric(trim($search))) {
                                $numVal = (int)trim($search);
                                if ($numVal > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                                    $cdwNum = ($numVal >= 316) ? ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                                    $q->orWhere('inv_no', 'like', "%CDW/{$cdwNum}/%");
                                }
                            } elseif (stripos($search, 'CD') !== false && stripos($search, 'CDW') === false) {
                                $q->orWhere('inv_no', 'like', "%CDW/%");
                            }

                            $q->orWhereHas('customer', function($c) use ($search) {
                                  $c->where('name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $totalRecords = $countQuery->count();

                    $relations = ['customer', 'salesOrder', 'items', 'checkedByUser', 'accountsSentByUser'];
                    $hasTbClass = class_exists(\App\Models\TransportBooking::class) && \Illuminate\Support\Facades\Schema::hasTable('transport_bookings');
                    if ($hasTbClass) {
                        $relations[] = 'transportBookings';
                    }

                    $invoicesQuery = $countQuery->with($relations)->orderBy('id', 'desc');
                    if ($length !== -1) {
                        $invoicesQuery->offset($start)->limit($length);
                    }
                    $invoices = $invoicesQuery->get();

                    $data = [];
                    foreach ($invoices as $invoice) {
                        $einvoice_generated = (!empty($invoice->einvoice_status) && strtolower((string)$invoice->einvoice_status) == 'generated') ? true : false;
                        $ewaybill_generated = (!empty($invoice->eway_bill_no)) ? true : false;
                        
                        $soDispatched = $invoice->salesOrder && strtolower((string)$invoice->salesOrder->status) == 'dispatched';
                        $dispatched = (!empty($invoice->delivery_status) && strtolower((string)$invoice->delivery_status) == 'dispatched') || $soDispatched ? true : false;
                        
                        // 1. Picking calculation based on scanned_qty vs quantity
                        $totQty = $invoice->items->sum('quantity');
                        $totScanned = $invoice->items->sum('scanned_qty');
                        $pickingCompleted = ($totQty > 0 && $totScanned >= $totQty);
                        $pickingInProgress = ($totScanned > 0 && $totScanned < $totQty);

                        // 2. Checking calculation
                        $checkingCompleted = ($invoice->checking_status === 'completed');

                        // 3. Kachathu calculation
                        $kachathuCompleted = ($invoice->kachathu_status === 'completed');

                        $tickHtml = '<span class="text-success fw-bold fs-4" title="✔"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        $crossHtml = '<span class="text-danger fw-bold fs-4" title="✘"><i class="ri ri-close-line"></i><span class="export-symbol" style="display:none;">✘</span></span>';
                        
                        // Picking HTML
                        if ($pickingCompleted) {
                            $pickingHtml = '<span class="text-success fw-bold fs-4" title="Picking Completed (' . $totScanned . '/' . $totQty . ')"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        } elseif ($pickingInProgress) {
                            $pickingHtml = '<span class="badge bg-warning text-dark px-2 py-1" title="Scanned ' . $totScanned . ' of ' . $totQty . '"><i class="ri-loader-4-line me-1"></i>' . $totScanned . '/' . $totQty . '</span>';
                        } else {
                            $pickingHtml = $crossHtml;
                        }

                        // Checking HTML (Read-only status)
                        if ($checkingCompleted) {
                            $checkerName = optional($invoice->checkedByUser)->name ?? 'User';
                            $checkTime = $invoice->checked_at ? date('d-m-Y H:i', strtotime((string)$invoice->checked_at)) : '';
                            $checkingHtml = '<span class="text-success fw-bold fs-4" title="Checked by ' . htmlspecialchars($checkerName) . ' (' . $checkTime . ')"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        } else {
                            $checkingHtml = $crossHtml;
                        }

                        // Kachathu HTML
                        $kachathuHtml = $kachathuCompleted ? $tickHtml : $crossHtml;

                        // 4. Ready for Dispatch (Triggered ONLY when Transport Sticker or Courier Sticker is printed)
                        $readyForDispatch = !empty($invoice->transport_sticker_printed);

                        if ($readyForDispatch) {
                            $readyTime = $invoice->transport_sticker_printed_at ? date('d-m-Y H:i', strtotime((string)$invoice->transport_sticker_printed_at)) : ($invoice->ready_for_dispatch_at ? date('d-m-Y H:i', strtotime((string)$invoice->ready_for_dispatch_at)) : '');
                            $readyHtml = '<span class="text-success fw-bold fs-4" title="Transport Sticker Printed' . ($readyTime ? ' (' . $readyTime . ')' : '') . '"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        } else {
                            $readyHtml = $crossHtml;
                        }

                        $hasInvoiceLr = !empty($invoice->lr_no) || !empty($invoice->tran_doc_no);
                        $hasBookingLr = false;
                        $bookingLrNo = null;

                        if ($hasTbClass && $invoice->relationLoaded('transportBookings') && $invoice->transportBookings) {
                            $hasBookingLr = $invoice->transportBookings->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->isNotEmpty();
                            $bookingLrNo = optional($invoice->transportBookings->whereNotNull('lr_no')->first())->lr_no;
                        }

                        $lrUpdated = ($hasInvoiceLr || $hasBookingLr);
                        $lrNumberVal = $invoice->lr_no ?: ($invoice->tran_doc_no ?: $bookingLrNo);
                        if ($lrUpdated) {
                            $lrHtml = '<span class="text-success fw-bold fs-4" title="LR No: ' . htmlspecialchars($lrNumberVal ?? '') . '"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        } else {
                            $lrHtml = $crossHtml;
                        }

                        $invNoStr = htmlspecialchars((string)($invoice->inv_no ?? '-'));
                        $invDateStr = $invoice->inv_date ? date('d-m-Y', strtotime((string)$invoice->inv_date)) : '-';
                        $custStr = htmlspecialchars((string)(optional($invoice->customer)->name ?? '-'));
                        
                        $isCancelled = ($invoice->invoice_status === 'Cancelled' || strtolower((string)$invoice->einvoice_status) === 'cancelled');
                        $invViewUrl = url('sales_invoices/view/' . $invoice->id);
                        $invNoHtml = '<a href="' . $invViewUrl . '" target="_blank" class="fw-bold text-primary">' . $invNoStr . '</a>';
                        if ($isCancelled) {
                            $invNoHtml .= '<br><span class="badge bg-danger rounded-pill px-2 py-1 mt-1" style="font-size: 10px;"><i class="ri ri-close-circle-line me-1"></i>Cancelled</span>';
                        }

                        $sentToAccounts = ($invoice->sent_to_accounts || in_array($invoice->invoice_status, ['Paid', 'Partially Paid']));
                        $sentTime = $invoice->sent_to_accounts_at ? date('d-m-Y H:i', strtotime((string)$invoice->sent_to_accounts_at)) : '';
                        $sentUser = optional($invoice->accountsSentByUser)->name ?? '';
                        $sentTooltip = $sentToAccounts ? ('Sent to Accounts' . ($sentUser ? ' by ' . htmlspecialchars($sentUser) : '') . ($sentTime ? ' (' . $sentTime . ')' : '')) : 'Pending';
                        $sentAccountsHtml = $sentToAccounts ? '<span class="text-success fw-bold fs-4" title="' . $sentTooltip . '"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>' : $crossHtml;

                        $hasGst = !empty(optional($invoice->customer)->gst_no);
                        $einvoiceSatisfied = $hasGst ? $einvoice_generated : true;
                        $ewaybillSatisfied = $ewaybill_generated; 

                        $allStepsCompleted = $pickingCompleted 
                            && $checkingCompleted 
                            && $einvoice_generated 
                            && $ewaybill_generated 
                            && $kachathuCompleted 
                            && $readyForDispatch 
                            && $lrUpdated 
                            && $sentToAccounts;

                        if ($allStepsCompleted) {
                            $statusBadge = '<span class="badge bg-success rounded-pill px-3 py-1">Completed</span>';
                        } elseif (!$einvoice_generated && ($pickingCompleted || $checkingCompleted)) {
                            $statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-3 py-1">Awaiting E-Invoice</span>';
                        } elseif ($einvoice_generated && !$ewaybill_generated && ($pickingCompleted || $checkingCompleted)) {
                            $statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-3 py-1">Awaiting E-Way Bill</span>';
                        } elseif ($readyForDispatch && !$lrUpdated) {
                            $statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-3 py-1">Awaiting LR</span>';
                        } elseif ($lrUpdated && !$sentToAccounts) {
                            $statusBadge = '<span class="badge bg-info text-white rounded-pill px-3 py-1">Awaiting Accounts</span>';
                        } elseif ($totScanned > 0 || $checkingCompleted || $kachathuCompleted) {
                            $statusBadge = '<span class="badge bg-primary rounded-pill px-3 py-1">In Progress</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-secondary rounded-pill px-3 py-1">Not Started</span>';
                        }

                        $data[] = [
                            'inv_date' => $invDateStr,
                            'inv_no' => $invNoHtml,
                            'customer' => $custStr,
                            'qty' => number_format($totQty),
                            'picking' => $pickingHtml,
                            'checking' => $checkingHtml,
                            'einvoice' => $einvoice_generated ? $tickHtml : $crossHtml,
                            'ewaybill' => $ewaybill_generated ? $tickHtml : $crossHtml,
                            'kachathu' => $kachathuHtml,
                            'ready_for_dispatch' => $readyHtml,
                            'lr_updated' => $lrHtml,
                            'sent_to_accounts' => $sentAccountsHtml,
                            'status' => $statusBadge,
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data
                    ]);

                case 'incentive-report':
                    $agents = SalesAgent::whereIn('status', ['active', 'Active'])->get();
                    $zones = Zone::whereIn('status', ['active', 'Active'])->get();
                    $cityIdToZone = [];
                    foreach ($zones as $zone) {
                        $ids = explode(',', (string)($zone->city_ids ?? ''));
                        foreach ($ids as $id) {
                            if ($id) $cityIdToZone[trim($id)] = $zone->zone_name;
                        }
                    }

                    $currMonth = (int)date('n');
                    $currYear = (int)date('Y');
                    $defaultFy = ($currMonth >= 4) ? ($currYear . '-' . ($currYear + 1)) : (($currYear - 1) . '-' . $currYear);

                    $selectedFy = $request->filled('incentive_fy') ? trim((string)$request->incentive_fy) : $defaultFy;
                    $fyParts = explode('-', $selectedFy);
                    $fyStartYear = (int)($fyParts[0] ?? $currYear);
                    $fyEndYear = (int)($fyParts[1] ?? ($fyStartYear + 1));

                    $selectedQuarter = $request->filled('incentive_quarter') ? strtoupper(trim((string)$request->incentive_quarter)) : 'ALL';

                    $multiplier = 12;
                    switch ($selectedQuarter) {
                        case 'Q1':
                            $incentiveFromDate = "{$fyStartYear}-04-01";
                            $incentiveToDate = "{$fyStartYear}-06-30";
                            $multiplier = 3;
                            break;
                        case 'Q2':
                            $incentiveFromDate = "{$fyStartYear}-07-01";
                            $incentiveToDate = "{$fyStartYear}-09-30";
                            $multiplier = 3;
                            break;
                        case 'Q3':
                            $incentiveFromDate = "{$fyStartYear}-10-01";
                            $incentiveToDate = "{$fyStartYear}-12-31";
                            $multiplier = 3;
                            break;
                        case 'Q4':
                            $incentiveFromDate = "{$fyEndYear}-01-01";
                            $incentiveToDate = "{$fyEndYear}-03-31";
                            $multiplier = 3;
                            break;
                        case 'ALL':
                        default:
                            $incentiveFromDate = "{$fyStartYear}-04-01";
                            $incentiveToDate = "{$fyEndYear}-03-31";
                            $multiplier = 12;
                            break;
                    }

                    $quarterLabels = [
                        'ALL' => 'Full Year (Apr - Mar)',
                        'Q1' => 'Q1 (Apr - Jun)',
                        'Q2' => 'Q2 (Jul - Sep)',
                        'Q3' => 'Q3 (Oct - Dec)',
                        'Q4' => 'Q4 (Jan - Mar)',
                    ];
                    $periodLabel = ($quarterLabels[$selectedQuarter] ?? $selectedQuarter) . " (FY {$selectedFy})";

                    $salesOrdersData = DB::table('sales_order_items as soi')
                        ->join('sales_orders as so', 'soi.sale_order_id', '=', 'so.id')
                        ->whereNull('so.deleted_at')
                        ->whereNull('soi.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('so.status')
                              ->orWhereRaw('LOWER(so.status) != ?', ['cancelled']);
                        })
                        ->where('so.so_date', '>=', $incentiveFromDate)
                        ->where('so.so_date', '<=', $incentiveToDate)
                        ->when($customerId, fn($q) => $q->where('so.customer_id', $customerId))
                        ->when($agentId, fn($q) => $q->where('so.agent_id', $agentId))
                        ->groupBy('so.agent_id')
                        ->select(
                            'so.agent_id',
                            DB::raw("SUM(CASE WHEN LOWER(COALESCE(so.order_type, '')) NOT LIKE '%discount%' AND COALESCE(so.sales_discount_percent, 0) <= 10 THEN soi.qty ELSE 0 END) as regular_qty"),
                            DB::raw("SUM(CASE WHEN LOWER(COALESCE(so.order_type, '')) LIKE '%discount%' OR COALESCE(so.sales_discount_percent, 0) > 10 THEN soi.qty ELSE 0 END) as discount_qty")
                        )
                        ->get()
                        ->keyBy('agent_id');

                    $returnQuantities = DB::table('credit_note_items as cni')
                        ->join('credit_notes as cn', 'cni.credit_note_id', '=', 'cn.id')
                        ->whereNull('cn.deleted_at')
                        ->whereNull('cni.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('cn.status')
                              ->orWhereRaw('LOWER(cn.status) != ?', ['cancelled']);
                        })
                        ->whereIn('cn.reason', ['Unorder PCS', 'Customer Non Moving'])
                        ->where('cn.note_date', '>=', $incentiveFromDate)
                        ->where('cn.note_date', '<=', $incentiveToDate)
                        ->when($customerId, fn($q) => $q->where('cn.customer_id', $customerId))
                        ->when($agentId, fn($q) => $q->where('cn.agent_id', $agentId))
                        ->groupBy('cn.agent_id')
                        ->select('cn.agent_id', DB::raw('SUM(cni.quantity) as return_qty'))
                        ->pluck('return_qty', 'cn.agent_id');

                    $incentiveReport = [];
                    $setting = \App\Models\Setting::first();
                    $refinishingRate = floatval($setting->refinishing_amount ?? 0);

                    $sumTotalTarget = 0;
                    $sumTotalAchieved = 0;
                    $sumTotalDiscount = 0;
                    $sumTotalReturn = 0;
                    $sumTotalIncentiveQty = 0;
                    $sumTotalGrossIncentiveAmt = 0;
                    $sumTotalRefinishing = 0;
                    $sumTotalNetIncentiveAmt = 0;

                    foreach ($agents as $agent) {
                        if ($agentId && $agent->id != $agentId) continue;

                        $zoneName = '-';
                        if (!empty($agent->zone_id)) {
                            $z = $zones->firstWhere('id', $agent->zone_id);
                            if ($z) {
                                $zoneName = $z->zone_name;
                            }
                        }
                        if ($zoneName === '-' && !empty($agent->city_id)) {
                            $zoneName = $cityIdToZone[$agent->city_id] ?? 'Unassigned';
                        }

                        $monthlyTarget = floatval($agent->sales_target ?? 0);
                        $target = $monthlyTarget * $multiplier;
                        $achieved = floatval($salesOrdersData[$agent->id]->regular_qty ?? 0);
                        $discountQty = floatval($salesOrdersData[$agent->id]->discount_qty ?? 0);
                        $returnQty = floatval($returnQuantities[$agent->id] ?? 0);
                        $ratePerPcs = floatval($agent->commission_value ?? 0);

                        $netExcess = ($achieved - $target) - $returnQty;
                        $incentiveQty = max(0, $netExcess);
                        $grossIncentiveAmt = $incentiveQty * $ratePerPcs;

                        $refinishingAmt = $returnQty * $refinishingRate;

                        $netIncentiveAmt = max(0, $grossIncentiveAmt - $refinishingAmt);

                        if ($target > 0 || $achieved > 0 || $discountQty > 0 || $returnQty > 0 || $agentId) {
                            if ($search) {
                                if (stripos((string)$zoneName, $search) === false && stripos((string)$agent->name, $search) === false && stripos((string)$agent->code, $search) === false) {
                                    continue;
                                }
                            }

                            $sumTotalTarget += $target;
                            $sumTotalAchieved += $achieved;
                            $sumTotalDiscount += $discountQty;
                            $sumTotalReturn += $returnQty;
                            $sumTotalIncentiveQty += $incentiveQty;
                            $sumTotalGrossIncentiveAmt += $grossIncentiveAmt;
                            $sumTotalRefinishing += $refinishingAmt;
                            $sumTotalNetIncentiveAmt += $netIncentiveAmt;

                            $incentiveReport[] = [
                                'agent_id' => $agent->id,
                                'agent_raw_name' => (string)$agent->name . ' (' . (string)$agent->code . ')',
                                'zone' => '<span class="fw-bold text-primary">' . htmlspecialchars((string)$zoneName) . '</span>',
                                'agent' => htmlspecialchars((string)$agent->name) . ' <small class="text-muted">(' . htmlspecialchars((string)$agent->code) . ')</small>',
                                'target' => '<span class="fw-semibold">' . number_format($target, 0) . '</span>',
                                'achieved' => '<span class="fw-bold text-dark">' . number_format($achieved, 0) . '</span>',
                                'discount_qty' => '<span class="text-muted">' . number_format($discountQty, 0) . '</span>',
                                'return_qty' => $returnQty > 0 
                                    ? '<span class="text-danger fw-semibold">-' . number_format($returnQty, 0) . '</span>' 
                                    : '<span class="text-muted">0</span>',
                                'incentive_qty' => $incentiveQty > 0 
                                    ? '<span class="badge bg-label-info fs-7">' . number_format($incentiveQty, 0) . '</span>' 
                                    : '<span class="text-muted">0</span>',
                                'rate_per_pcs' => '₹' . number_format($ratePerPcs, 2),
                                'incentive_amt' => '<span class="fw-semibold text-dark">' . ($grossIncentiveAmt > 0 ? '₹' . number_format($grossIncentiveAmt, 2) : '₹0.00') . '</span>',
                                'refinishing_amt' => $refinishingAmt > 0 
                                    ? '<span class="text-danger fw-semibold">-₹' . number_format($refinishingAmt, 2) . '</span>' . ($refinishingRate > 0 ? '<small class="text-muted d-block" style="font-size:11px;">(@ ₹' . number_format($refinishingRate, 2) . '/pc)</small>' : '')
                                    : '<span class="text-muted">₹0.00</span>',
                                'net_incentive_amt' => '<span class="text-success fw-bold">' . ($netIncentiveAmt > 0 ? '₹' . number_format($netIncentiveAmt, 2) : '₹0.00') . '</span>',
                            ];
                        }
                    }

                    $totalRecords = count($incentiveReport);
                    $pagedIncentives = ($length > 0 && $length !== -1) ? array_slice($incentiveReport, $start, $length) : $incentiveReport;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $pagedIncentives,
                        'period' => $periodLabel,
                        'totals' => [
                            'total_target' => number_format($sumTotalTarget, 0),
                            'total_achieved' => number_format($sumTotalAchieved, 0),
                            'total_discount' => number_format($sumTotalDiscount, 0),
                            'total_return' => number_format($sumTotalReturn, 0),
                            'total_incentive_qty' => number_format($sumTotalIncentiveQty, 0),
                            'gross_incentive_amt' => '₹' . number_format($sumTotalGrossIncentiveAmt, 2),
                            'total_refinishing' => ($sumTotalRefinishing > 0 ? '-₹' . number_format($sumTotalRefinishing, 2) : '₹0.00'),
                            'net_incentive_amt' => '₹' . number_format($sumTotalNetIncentiveAmt, 2),
                            'incentive_amt' => '₹' . number_format($sumTotalNetIncentiveAmt, 2)
                        ]
                    ]);

                case 'incentive-brand-drilldown':
                    $targetAgentId = $request->agent_id ?: $request->drilldown_agent_id;
                    if (!$targetAgentId) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => []
                        ]);
                    }

                    $agent = SalesAgent::find($targetAgentId);
                    if (!$agent) {
                        return response()->json([
                            'draw' => $draw,
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'totals' => []
                        ]);
                    }

                    $currMonth = (int)date('n');
                    $currYear = (int)date('Y');
                    $defaultFy = ($currMonth >= 4) ? ($currYear . '-' . ($currYear + 1)) : (($currYear - 1) . '-' . $currYear);

                    $selectedFy = $request->filled('incentive_fy') ? trim((string)$request->incentive_fy) : $defaultFy;
                    $fyParts = explode('-', $selectedFy);
                    $fyStartYear = (int)($fyParts[0] ?? $currYear);
                    $fyEndYear = (int)($fyParts[1] ?? ($fyStartYear + 1));

                    $selectedQuarter = $request->filled('incentive_quarter') ? strtoupper(trim((string)$request->incentive_quarter)) : 'ALL';

                    $multiplier = 12;
                    switch ($selectedQuarter) {
                        case 'Q1':
                            $incentiveFromDate = "{$fyStartYear}-04-01";
                            $incentiveToDate = "{$fyStartYear}-06-30";
                            $multiplier = 3;
                            break;
                        case 'Q2':
                            $incentiveFromDate = "{$fyStartYear}-07-01";
                            $incentiveToDate = "{$fyStartYear}-09-30";
                            $multiplier = 3;
                            break;
                        case 'Q3':
                            $incentiveFromDate = "{$fyStartYear}-10-01";
                            $incentiveToDate = "{$fyStartYear}-12-31";
                            $multiplier = 3;
                            break;
                        case 'Q4':
                            $incentiveFromDate = "{$fyEndYear}-01-01";
                            $incentiveToDate = "{$fyEndYear}-03-31";
                            $multiplier = 3;
                            break;
                        case 'ALL':
                        default:
                            $incentiveFromDate = "{$fyStartYear}-04-01";
                            $incentiveToDate = "{$fyEndYear}-03-31";
                            $multiplier = 12;
                            break;
                    }

                    $allBrands = \App\Models\Brand::all();
                    $setting = \App\Models\Setting::first();
                    $refinishingRate = floatval($setting->refinishing_amount ?? 0);

                    $soItems = DB::table('sales_order_items as soi')
                        ->join('sales_orders as so', 'soi.sale_order_id', '=', 'so.id')
                        ->leftJoin('stock_entry_items as sei', 'soi.stock_entry_item_id', '=', 'sei.id')
                        ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
                        ->whereNull('so.deleted_at')
                        ->whereNull('soi.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('so.status')
                              ->orWhereRaw('LOWER(so.status) != ?', ['cancelled']);
                        })
                        ->where('so.agent_id', $targetAgentId)
                        ->where('so.so_date', '>=', $incentiveFromDate)
                        ->where('so.so_date', '<=', $incentiveToDate)
                        ->when($customerId, fn($q) => $q->where('so.customer_id', $customerId))
                        ->select(
                            'soi.id',
                            'soi.qty',
                            'soi.rate',
                            'soi.amount',
                            'soi.art_no',
                            'soi.sku',
                            'soi.item_name',
                            'soi.category_name',
                            'soi.categories_path_val',
                            'so.order_type',
                            'so.sales_discount_percent',
                            'sei.brand_id as stock_brand_id',
                            'b.brand_name as stock_brand_name'
                        )
                        ->get();

                    $cnItems = DB::table('credit_note_items as cni')
                        ->join('credit_notes as cn', 'cni.credit_note_id', '=', 'cn.id')
                        ->leftJoin('stock_entry_items as sei1', 'cni.stock_entry_item_id', '=', 'sei1.id')
                        ->leftJoin('brands as b1', 'sei1.brand_id', '=', 'b1.id')
                        ->leftJoin('sales_invoice_items as sii', 'cni.sales_invoice_item_id', '=', 'sii.id')
                        ->leftJoin('stock_entry_items as sei2', 'sii.stock_entry_item_id', '=', 'sei2.id')
                        ->leftJoin('brands as b2', 'sei2.brand_id', '=', 'b2.id')
                        ->whereNull('cn.deleted_at')
                        ->whereNull('cni.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('cn.status')
                              ->orWhereRaw('LOWER(cn.status) != ?', ['cancelled']);
                        })
                        ->whereIn('cn.reason', ['Unorder PCS', 'Customer Non Moving'])
                        ->where('cn.agent_id', $targetAgentId)
                        ->where('cn.note_date', '>=', $incentiveFromDate)
                        ->where('cn.note_date', '<=', $incentiveToDate)
                        ->when($customerId, fn($q) => $q->where('cn.customer_id', $customerId))
                        ->select(
                            'cni.id',
                            'cni.quantity',
                            'cni.art_no',
                            'cni.sku',
                            'sii.art_no as sii_art_no',
                            'sii.sku as sii_sku',
                            'b1.brand_name as brand1',
                            'b2.brand_name as brand2'
                        )
                        ->get();

                    $brandData = [];

                    foreach ($soItems as $item) {
                        $brandName = null;
                        if (!empty($item->stock_brand_name)) {
                            $brandName = trim($item->stock_brand_name);
                        } else {
                            $resolved = $this->resolveBrandFromOrderItem($item, $allBrands);
                            $brandName = $resolved ? trim($resolved['name']) : 'Uncategorized';
                        }

                        if (!isset($brandData[$brandName])) {
                            $brandData[$brandName] = [
                                'brand_name' => $brandName,
                                'achieved' => 0,
                                'discount_qty' => 0,
                                'return_qty' => 0,
                                'revenue_amt' => 0,
                            ];
                        }

                        $orderType = strtolower((string)($item->order_type ?? ''));
                        $discPercent = floatval($item->sales_discount_percent ?? 0);
                        $qty = floatval($item->qty ?? 0);
                        $amt = floatval($item->amount ?: ($qty * floatval($item->rate ?? 0)));

                        if (str_contains($orderType, 'discount') || $discPercent > 10) {
                            $brandData[$brandName]['discount_qty'] += $qty;
                        } else {
                            $brandData[$brandName]['achieved'] += $qty;
                        }
                        $brandData[$brandName]['revenue_amt'] += $amt;
                    }

                    foreach ($cnItems as $item) {
                        $brandName = null;
                        if (!empty($item->brand1)) {
                            $brandName = trim($item->brand1);
                        } elseif (!empty($item->brand2)) {
                            $brandName = trim($item->brand2);
                        } else {
                            $tempItem = (object)[
                                'art_no' => $item->art_no ?: $item->sii_art_no,
                                'sku' => $item->sku ?: $item->sii_sku,
                                'item_name' => '',
                                'category_name' => '',
                                'categories_path_val' => '',
                                'stock_brand_name' => '',
                                'cat_brand_name' => ''
                            ];
                            $resolved = $this->resolveBrandFromOrderItem($tempItem, $allBrands);
                            $brandName = $resolved ? trim($resolved['name']) : 'Uncategorized';
                        }

                        if (!isset($brandData[$brandName])) {
                            $brandData[$brandName] = [
                                'brand_name' => $brandName,
                                'achieved' => 0,
                                'discount_qty' => 0,
                                'return_qty' => 0,
                                'revenue_amt' => 0,
                            ];
                        }

                        $brandData[$brandName]['return_qty'] += floatval($item->quantity ?? 0);
                    }

                    ksort($brandData);

                    $monthlyTarget = floatval($agent->sales_target ?? 0);
                    $totalExecutiveTarget = $monthlyTarget * $multiplier;
                    $ratePerPcs = floatval($agent->commission_value ?? 0);

                    $totalExecutiveAchieved = 0;
                    $totalExecutiveDiscount = 0;
                    $totalExecutiveReturn = 0;
                    $totalExecutiveRevenue = 0;

                    foreach ($brandData as $b) {
                        $totalExecutiveAchieved += $b['achieved'];
                        $totalExecutiveDiscount += $b['discount_qty'];
                        $totalExecutiveReturn += $b['return_qty'];
                        $totalExecutiveRevenue += $b['revenue_amt'];
                    }

                    $netExcess = ($totalExecutiveAchieved - $totalExecutiveTarget) - $totalExecutiveReturn;
                    $totalExecutiveIncentiveQty = max(0, $netExcess);

                    $rows = [];
                    $allocatedIncentiveQty = 0;
                    $sumGrossIncentiveAmt = 0;
                    $sumRefinishingAmt = 0;
                    $sumNetIncentiveAmt = 0;

                    // First pass: find the last brand with activity for rounding adjustment
                    $lastBrandKey = null;
                    foreach ($brandData as $bName => $b) {
                        if ($b['achieved'] > 0 || $b['discount_qty'] > 0 || $b['return_qty'] > 0 || $b['revenue_amt'] > 0) {
                            $lastBrandKey = $bName;
                        }
                    }

                    foreach ($brandData as $bName => $b) {
                        if ($search && stripos($bName, $search) === false) {
                            continue;
                        }

                        $achieved = $b['achieved'];
                        $discountQty = $b['discount_qty'];
                        $returnQty = $b['return_qty'];
                        $revenue = $b['revenue_amt'];

                        if ($achieved == 0 && $discountQty == 0 && $returnQty == 0 && $revenue == 0) {
                            continue;
                        }

                        $brandIncentiveQty = 0;
                        if ($totalExecutiveIncentiveQty > 0 && $totalExecutiveAchieved > 0 && $achieved > 0) {
                            if ($bName === $lastBrandKey) {
                                $brandIncentiveQty = max(0, $totalExecutiveIncentiveQty - $allocatedIncentiveQty);
                            } else {
                                $brandIncentiveQty = round($totalExecutiveIncentiveQty * ($achieved / $totalExecutiveAchieved));
                                $allocatedIncentiveQty += $brandIncentiveQty;
                            }
                        }

                        $brandGross = $brandIncentiveQty * $ratePerPcs;
                        $brandRefinishing = $returnQty * $refinishingRate;
                        $brandNet = max(0, $brandGross - $brandRefinishing);

                        $sumGrossIncentiveAmt += $brandGross;
                        $sumRefinishingAmt += $brandRefinishing;
                        $sumNetIncentiveAmt += $brandNet;

                        $rows[] = [
                            'brand' => '<span class="fw-bold text-dark">' . htmlspecialchars($bName) . '</span>',
                            'achieved' => '<span class="fw-bold text-dark">' . number_format($achieved, 0) . '</span>',
                            'discount_qty' => '<span class="text-muted">' . number_format($discountQty, 0) . '</span>',
                            'return_qty' => $returnQty > 0 
                                ? '<span class="text-danger fw-semibold">-' . number_format($returnQty, 0) . '</span>' 
                                : '<span class="text-muted">0</span>',
                            'incentive_qty' => $brandIncentiveQty > 0 
                                ? '<span class="badge bg-label-info fs-7">' . number_format($brandIncentiveQty, 0) . '</span>' 
                                : '<span class="text-muted">0</span>',
                            'rate_per_pcs' => '₹' . number_format($ratePerPcs, 2),
                            'incentive_amt' => '<span class="fw-semibold text-dark">' . ($brandGross > 0 ? '₹' . number_format($brandGross, 2) : '₹0.00') . '</span>',
                            'refinishing_amt' => $brandRefinishing > 0 
                                ? '<span class="text-danger fw-semibold">-₹' . number_format($brandRefinishing, 2) . '</span>' 
                                : '<span class="text-muted">₹0.00</span>',
                            'net_incentive_amt' => '<span class="text-success fw-bold">' . ($brandNet > 0 ? '₹' . number_format($brandNet, 2) : '₹0.00') . '</span>',
                        ];
                    }

                    $totalRecords = count($rows);
                    $pagedRows = ($length > 0 && $length !== -1) ? array_slice($rows, $start, $length) : $rows;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $pagedRows,
                        'totals' => [
                            'total_target' => number_format($totalExecutiveTarget, 0),
                            'total_achieved' => number_format($totalExecutiveAchieved, 0),
                            'total_discount' => number_format($totalExecutiveDiscount, 0),
                            'total_return' => number_format($totalExecutiveReturn, 0),
                            'total_incentive_qty' => number_format($totalExecutiveIncentiveQty, 0),
                            'gross_incentive_amt' => '₹' . number_format($sumGrossIncentiveAmt, 2),
                            'total_refinishing' => ($sumRefinishingAmt > 0 ? '-₹' . number_format($sumRefinishingAmt, 2) : '₹0.00'),
                            'net_incentive_amt' => '₹' . number_format($sumNetIncentiveAmt, 2),
                            'total_revenue' => '₹' . number_format($totalExecutiveRevenue, 2),
                        ]
                    ]);

                case 'credit-note-report':
                    $countQuery = \App\Models\CreditNote::whereNull('deleted_at');
                    if ($fromDate) $countQuery->where('note_date', '>=', $fromDate);
                    if ($toDate) $countQuery->where('note_date', '<=', $toDate);
                    if ($customerId) $countQuery->where('customer_id', $customerId);
                    if ($agentId) $countQuery->where('agent_id', $agentId);
                    if ($search) {
                        $countQuery->where(function($q) use ($search) {
                            $q->where('note_no', 'like', "%{$search}%")
                              ->orWhere('reference_no', 'like', "%{$search}%")
                              ->orWhereHas('customer', function($c) use ($search) {
                                  $c->where('name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $totalRecords = $countQuery->count();
                    $totalQty = (float)\App\Models\CreditNoteItem::whereIn('credit_note_id', (clone $countQuery)->select('id'))->whereNull('deleted_at')->sum('quantity');
                    $totalSubTotal = (float)(clone $countQuery)->sum('sub_total');
                    $totalDiscount = (float)(clone $countQuery)->sum('discount');
                    $totalTaxAmount = (float)(clone $countQuery)->sum('tax_amount');
                    $totalOtherCharges = (float)(clone $countQuery)->sum('other_charges');
                    $totalGrandTotal = (float)(clone $countQuery)->sum('grand_total');

                    $dataQuery = clone $countQuery;
                    $notesQuery = $dataQuery->with(['customer', 'salesAgent', 'zone'])->withSum('items', 'quantity')->orderBy('id', 'desc');
                    if ($length !== -1) {
                        $notesQuery->offset($start)->limit($length);
                    }
                    $notes = $notesQuery->get();

                    $data = [];
                    foreach ($notes as $note) {
                        $badgeClass = 'bg-label-info';
                        if ($note->status == 'Approved') $badgeClass = 'bg-label-success';
                        if ($note->status == 'Cancelled') $badgeClass = 'bg-label-danger';

                        $data[] = [
                            'note_no' => '<strong>' . htmlspecialchars((string)($note->note_no ?? '-')) . '</strong>',
                            'reference_no' => htmlspecialchars((string)($note->reference_no ?? '-')),
                            'note_date' => $note->note_date ? date('d-M-Y', strtotime((string)$note->note_date)) : '-',
                            'customer' => htmlspecialchars((string)(optional($note->customer)->name ?? '-')),
                            'zone' => '<span class="badge bg-label-info rounded-pill">' . htmlspecialchars((string)(optional($note->zone)->zone_name ?? '-')) . '</span>',
                            'agent' => htmlspecialchars((string)(optional($note->salesAgent)->name ?? '-')),
                            'reason' => htmlspecialchars((string)($note->reason ?? '-')),
                            'total_qty' => number_format((float)($note->items_sum_quantity ?? 0), 0),
                            'sub_total' => '₹' . number_format((float)($note->sub_total ?? 0), 2),
                            'discount' => '<span class="text-danger">-₹' . number_format((float)($note->discount ?? 0), 2) . '</span>',
                            'tax_amount' => '₹' . number_format((float)($note->tax_amount ?? 0), 2),
                            'other_charges' => '₹' . number_format((float)($note->other_charges ?? 0), 2),
                            'grand_total' => '<span class="fw-bold text-success">₹' . number_format((float)($note->grand_total ?? 0), 2) . '</span>',
                            'status' => '<span class="badge ' . $badgeClass . ' rounded-pill">' . htmlspecialchars((string)($note->status ?? '-')) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data,
                        'totals' => [
                            'total_qty' => number_format($totalQty, 0),
                            'sub_total' => '₹' . number_format($totalSubTotal, 2),
                            'discount' => ($totalDiscount > 0 ? '-₹' . number_format($totalDiscount, 2) : '₹0.00'),
                            'tax_amount' => '₹' . number_format($totalTaxAmount, 2),
                            'other_charges' => '₹' . number_format($totalOtherCharges, 2),
                            'grand_total' => '₹' . number_format($totalGrandTotal, 2)
                        ]
                    ]);

                case 'despatch-report':
                    $countQuery = SalesOrder::whereNull('deleted_at');
                    if ($fromDate) $countQuery->where('so_date', '>=', $fromDate);
                    if ($toDate) $countQuery->where('so_date', '<=', $toDate);
                    if ($customerId) $countQuery->where('customer_id', $customerId);
                    if ($agentId) $countQuery->where('agent_id', $agentId);
                    if ($search) {
                        $countQuery->where(function($q) use ($search) {
                            $q->where('so_no', 'like', "%{$search}%")
                              ->orWhereHas('customer', function($c) use ($search) {
                                  $c->where('name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $totalRecords = $countQuery->count();
                    $totOrderQty = (float)(clone $countQuery)->sum('total_qty');

                    $deliveredQtySum = (float)DB::table('sales_invoices as si')
                        ->join('sales_invoice_items as sii', 'sii.sales_invoice_id', '=', 'si.id')
                        ->whereIn('si.so_id', (clone $countQuery)->select('sales_orders.id'))
                        ->whereNull('si.deleted_at')
                        ->whereNull('sii.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')
                              ->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        })
                        ->sum('sii.quantity');

                    $totDeliveredQty = $deliveredQtySum;
                    $totPendingQty = max(0, $totOrderQty - $totDeliveredQty);

                    $orderItemSums = DB::table('sales_order_items as soi')
                        ->leftJoin('items as i', 'i.id', '=', 'soi.item_id')
                        ->leftJoin('brands as b', 'b.id', '=', 'i.brand_id')
                        ->whereIn('soi.sale_order_id', (clone $countQuery)->select('sales_orders.id'))
                        ->whereNull('soi.deleted_at')
                        ->selectRaw("
                            SUM(CASE WHEN UPPER(COALESCE(b.brand_name, '')) LIKE '%DHOTI%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%DHOTI%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%DHOTI%' THEN soi.qty ELSE 0 END) as dhoti_qty,
                            SUM(CASE WHEN UPPER(COALESCE(b.brand_name, '')) LIKE '%WHITE%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%WHITE%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%WHITE%' THEN soi.qty ELSE 0 END) as white_qty,
                            SUM(CASE WHEN UPPER(COALESCE(b.brand_name, '')) LIKE '%CORE%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%CORE%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%CORE%' THEN soi.qty ELSE 0 END) as core_qty,
                            SUM(CASE WHEN UPPER(COALESCE(b.brand_name, '')) LIKE '%BRAVO%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%BRAVO%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%BRAVO%' THEN soi.qty ELSE 0 END) as bravo_qty,
                            SUM(CASE WHEN UPPER(COALESCE(b.brand_name, '')) LIKE '%DEAL%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%DEAL%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%DEAL%' THEN soi.qty ELSE 0 END) as deal_qty,
                            SUM(CASE WHEN (UPPER(COALESCE(b.brand_name, '')) LIKE '%FORMAL%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%FORMAL%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%FORMAL%') AND NOT (UPPER(COALESCE(b.brand_name, '')) LIKE '%CORE%' OR UPPER(COALESCE(soi.categories_path_val, '')) LIKE '%CORE%' OR UPPER(COALESCE(soi.category_name, '')) LIKE '%CORE%') THEN soi.qty ELSE 0 END) as formal_qty
                        ")
                        ->first();

                    $totDhoti = (float)($orderItemSums->dhoti_qty ?? 0);
                    $totWhite = (float)($orderItemSums->white_qty ?? 0);
                    $totCore = (float)($orderItemSums->core_qty ?? 0);
                    $totBravo = (float)($orderItemSums->bravo_qty ?? 0);
                    $totDeal = (float)($orderItemSums->deal_qty ?? 0);
                    $totFormal = (float)($orderItemSums->formal_qty ?? 0);

                    $dataQuery = clone $countQuery;
                    $ordersQuery = $dataQuery->with(['customer.city', 'items.item.brand', 'salesAgent', 'zone', 'salesInvoices.items'])->orderBy('id', 'desc');
                    if ($length !== -1) {
                        $ordersQuery->offset($start)->limit($length);
                    }
                    $orders = $ordersQuery->get();

                    $data = [];
                    $sno = $start + 1;
                    $totPendingArtsCount = 0;
                    foreach ($orders as $order) {
                        $validInvoices = $order->salesInvoices ? $order->salesInvoices->filter(function($inv) {
                            return empty($inv->einvoice_status) || strtolower((string)$inv->einvoice_status) !== 'cancelled';
                        })->sortBy('inv_date') : collect();
                        
                        $delivered_qty = 0;
                        foreach ($validInvoices as $inv) {
                            $delivered_qty += $inv->items ? $inv->items->sum('quantity') : 0;
                        }

                        $dhoti_qty = 0; $white_qty = 0; $core_qty = 0; $bravo_qty = 0; $deal_qty = 0; $formal_qty = 0;
                        if ($order->items) {
                            foreach ($order->items as $item) {
                                $brandStr = '';
                                if ($item->item && $item->item->brand && !empty($item->item->brand->brand_name)) {
                                    $brandStr .= strtoupper((string)$item->item->brand->brand_name) . ' ';
                                }
                                $brandStr .= strtoupper((string)($item->categories_path_val ?? '')) . ' ';
                                $brandStr .= strtoupper((string)($item->category_name ?? ''));

                                $itemQty = (float)($item->qty ?? 0);
                                if (strpos($brandStr, 'DHOTI') !== false) $dhoti_qty += $itemQty;
                                if (strpos($brandStr, 'WHITE') !== false) $white_qty += $itemQty;
                                if (strpos($brandStr, 'CORE') !== false) $core_qty += $itemQty;
                                if (strpos($brandStr, 'BRAVO') !== false) $bravo_qty += $itemQty;
                                if (strpos($brandStr, 'DEAL') !== false) $deal_qty += $itemQty;
                                if (strpos($brandStr, 'FORMAL') !== false && strpos($brandStr, 'CORE') === false) {
                                    $formal_qty += $itemQty;
                                }
                            }
                        }

                        $total_qty = (float)($order->total_qty ?? 0);
                        $pending_qty = max(0, $total_qty - $delivered_qty);
                        $fulfillmentStatus = ($delivered_qty >= $total_qty && $total_qty > 0) ? 'Delivered' : ($delivered_qty > 0 ? 'Partial Delivery' : 'Planned');

                        $partial_d_date = '-';
                        $despatch_complete_date = '-';
                        if ($validInvoices->count() > 0) {
                            $latestInvoice = $validInvoices->sortByDesc(function($inv) {
                                return $inv->dispatch_completed_at ?? $inv->inv_date;
                            })->first();

                            $latestDate = null;
                            if (!empty($latestInvoice->dispatch_completed_at)) {
                                $latestDate = date('d-m-Y', strtotime((string)$latestInvoice->dispatch_completed_at));
                            } elseif (!empty($latestInvoice->inv_date)) {
                                $latestDate = date('d-m-Y', strtotime((string)$latestInvoice->inv_date));
                            }
                            
                            $partial_d_date = $latestDate ?? '-';
                            if ($delivered_qty >= $total_qty && $total_qty > 0) {
                                $despatch_complete_date = $latestDate ?? '-';
                            }
                        }

                        $statusBadge = $fulfillmentStatus == 'Delivered' ? '<span class="badge bg-label-success">Delivered</span>' : ($fulfillmentStatus == 'Partial Delivery' ? '<span class="badge bg-label-warning">Partial Delivery</span>' : '<span class="badge bg-label-secondary">Planned</span>');

                        $pendingArts = $this->calculateOrderPendingArts($order, $validInvoices);
                        $pending_art_count = $pendingArts['count'];
                        $pending_art_qty = $pendingArts['qty'];
                        $totPendingArtsCount += $pending_art_count;

                        $data[] = [
                            'sno' => $sno++,
                            'so_no' => '<span class="fw-bold text-primary">' . htmlspecialchars((string)($order->so_no ?? '-')) . '</span>',
                            'order_no' => htmlspecialchars((string)($order->order_no ?? '-')),
                            'order_type' => htmlspecialchars((string)($order->order_type ?? 'Regular')),
                            'so_date' => $order->so_date ? date('d-m-Y', strtotime((string)$order->so_date)) : '-',
                            'agent' => htmlspecialchars((string)(optional($order->salesAgent)->name ?? '-')),
                            'customer' => '<div class="fw-bold text-dark">' . htmlspecialchars((string)(optional($order->customer)->name ?? '-')) . '</div><small class="text-muted">' . htmlspecialchars((string)(optional($order->customer)->code ?? '')) . '</small>',
                            'place' => htmlspecialchars((string)(optional(optional($order->customer)->city)->city_name ?? '-')),
                            'zone' => htmlspecialchars((string)(optional($order->zone)->zone_name ?? '-')),
                            'dhoti_qty' => $dhoti_qty > 0 ? number_format($dhoti_qty, 0) : '',
                            'white_qty' => $white_qty > 0 ? number_format($white_qty, 0) : '',
                            'core_qty' => $core_qty > 0 ? number_format($core_qty, 0) : '',
                            'bravo_qty' => $bravo_qty > 0 ? number_format($bravo_qty, 0) : '',
                            'deal_qty' => $deal_qty > 0 ? number_format($deal_qty, 0) : '',
                            'formal_qty' => $formal_qty > 0 ? number_format($formal_qty, 0) : '',
                            'total_qty' => number_format($total_qty, 0),
                            'delivery_date' => $order->delivery_date ? date('d-m-Y', strtotime((string)$order->delivery_date)) : ($order->request_date ? date('d-m-Y', strtotime((string)$order->request_date)) : '-'),
                            'status' => $statusBadge,
                            'delivered_qty' => '<span class="text-success fw-bold">' . number_format($delivered_qty, 0) . '</span>',
                            'pending_qty' => ($pending_qty > 0)
                                ? '<span class="text-danger fw-bold">' . number_format($pending_qty, 0) . '</span>'
                                : '<span class="text-muted fw-semibold">0</span>',
                            'pending_arts_qty' => ($pending_art_count > 0)
                                ? '<a href="javascript:void(0)" class="text-primary fw-bold text-decoration-underline" onclick="showDespatchPendingItems(' . $order->id . ', \'' . htmlspecialchars($order->so_no ?? '', ENT_QUOTES) . '\')" title="Click to view ' . $pending_art_count . ' pending art numbers (' . number_format($pending_art_qty, 0) . ' pcs)">' . $pending_art_count . ' <i class="ri-list-check-2 small ms-1"></i></a>'
                                : '<span class="text-muted fw-semibold">0</span>',
                            'partial_d_date' => $partial_d_date,
                            'despatch_complete_date' => $despatch_complete_date,
                            'reason' => htmlspecialchars((string)($order->reason_for_delay ?? '-')),
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data,
                        'totals' => [
                            'dhoti_qty' => number_format($totDhoti, 0),
                            'white_qty' => number_format($totWhite, 0),
                            'core_qty' => number_format($totCore, 0),
                            'bravo_qty' => number_format($totBravo, 0),
                            'deal_qty' => number_format($totDeal, 0),
                            'formal_qty' => number_format($totFormal, 0),
                            'total_qty' => number_format($totOrderQty, 0),
                            'delivered_qty' => number_format($totDeliveredQty, 0),
                            'pending_qty' => number_format($totPendingQty, 0),
                            'pending_arts_qty' => number_format($totPendingArtsCount, 0),
                        ]
                    ]);

                case 'outstanding-report':
                    $query = Customer::where('status', 'Active');
                    if ($customerId) $query->where('id', $customerId);
                    if ($search) {
                        $query->where(function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('code', 'like', "%{$search}%")
                              ->orWhereHas('zone', function($zq) use ($search) {
                                  $zq->where('zone_name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $hasOpenTable = Schema::hasTable('customer_opening_balances') && class_exists(CustomerOpeningBalance::class);
                    $withRelations = [
                        'zone',
                        'salesInvoices' => function($invQ) use ($fromDate, $toDate) {
                            $invQ->whereNull('deleted_at');
                            if ($fromDate) $invQ->where('inv_date', '>=', $fromDate);
                            if ($toDate) $invQ->where('inv_date', '<=', $toDate);
                        }
                    ];
                    if ($hasOpenTable) {
                        $withRelations[] = 'openingBalance';
                    }

                    $allCustomers = $query->with($withRelations)->get();

                    $outstandingReport = [];
                    $sumBills = 0;
                    $sumOpenAmount = 0;
                    $sumSales = 0;
                    $sumReceived = 0;
                    $sumOutstanding = 0;

                    foreach ($allCustomers as $cust) {
                        $openBal = $hasOpenTable ? $cust->openingBalance : null;
                        $openAmount = $openBal ? (float)$openBal->open_amount : 0;
                        $openQty = $openBal ? (float)$openBal->open_qty : 0;

                        $invoices = $cust->salesInvoices;
                        $billsCount = $invoices ? $invoices->count() : 0;
                        $totalSales = $invoices ? (float)$invoices->sum('grand_total') : 0;
                        $received = $invoices ? (float)$invoices->sum('received_amount') : 0;
                        $netOutstanding = max(0, $openAmount + $totalSales - $received);

                        if ($openAmount > 0 || $totalSales > 0 || $netOutstanding > 0 || $billsCount > 0) {
                            $sumBills += $billsCount;
                            $sumOpenAmount += $openAmount;
                            $sumSales += $totalSales;
                            $sumReceived += $received;
                            $sumOutstanding += $netOutstanding;

                            $openDisplay = '<span class="text-muted">₹0.00</span>';
                            if ($openAmount > 0 || $openQty > 0) {
                                $openDisplay = '<div class="fw-semibold text-dark">₹' . number_format($openAmount, 2) . '</div>';
                                if ($openQty > 0) {
                                    $openDisplay .= '<small class="text-primary fw-medium font-monospace d-block">(' . number_format($openQty, 0) . ' pcs)</small>';
                                }
                            }

                            $outstandingReport[] = [
                                'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars((string)($cust->zone->zone_name ?? '-')) . '</span>',
                                'customer' => '<div class="fw-bold text-dark">' . htmlspecialchars((string)$cust->name) . '</div><small class="text-muted">' . htmlspecialchars((string)$cust->code) . '</small>',
                                'bills_count' => '<span class="badge rounded-pill bg-label-info">' . $billsCount . '</span>',
                                'open_outstanding' => $openDisplay,
                                'total_sales' => '₹' . number_format($totalSales, 2),
                                'received' => '<span class="text-success">₹' . number_format($received, 2) . '</span>',
                                'outstanding' => '<span class="fw-bold text-danger fs-6">₹' . number_format($netOutstanding, 2) . '</span>',
                            ];
                        }
                    }

                    $totalRecords = count($outstandingReport);
                    $pagedData = ($length > 0 && $length !== -1) ? array_slice($outstandingReport, $start, $length) : $outstandingReport;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $pagedData,
                        'totals' => [
                            'bills_count' => number_format($sumBills),
                            'open_outstanding' => '₹' . number_format($sumOpenAmount, 2),
                            'total_sales' => '₹' . number_format($sumSales, 2),
                            'received' => '₹' . number_format($sumReceived, 2),
                            'outstanding' => '₹' . number_format($sumOutstanding, 2),
                        ]
                    ]);

                case 'regular-discount':
                case 'discount':
                    $query = SalesInvoice::query()
                        ->select(
                            DB::raw("DATE_FORMAT(inv_date, '%b %Y') as period"),
                            DB::raw("YEAR(inv_date) as yr"),
                            DB::raw("MONTH(inv_date) as mn"),
                            DB::raw("SUM(sub_total) as total_sales"),
                            DB::raw("SUM(CASE WHEN discount = 0 THEN sub_total ELSE 0 END) as regular_sales"),
                            DB::raw("SUM(CASE WHEN discount > 0 THEN sub_total ELSE 0 END) as discount_sales_raw"),
                            DB::raw("SUM(discount) as discount_amount"),
                            DB::raw("SUM(sub_total - discount) as net_revenue"),
                            DB::raw("COUNT(DISTINCT CASE WHEN discount > 0 THEN id ELSE NULL END) as discount_inv_count"),
                            DB::raw("COUNT(DISTINCT CASE WHEN discount > 0 THEN customer_id ELSE NULL END) as discount_cust_count")
                        )
                        ->whereNull('deleted_at');

                    if ($fromDate) {
                        $query->where('inv_date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $query->where('inv_date', '<=', $toDate);
                    }
                    if ($customerId) {
                        $query->where('customer_id', $customerId);
                    }
                    if ($request->brand_id) {
                        $brandId = $request->brand_id;
                        $query->whereExists(function ($q) use ($brandId) {
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
                                ->where('brands.id', $brandId);
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
                        $discount_pc = $item->discount_sales_raw > 0 ? ($item->discount_amount / $item->discount_sales_raw) * 100 : 0;
                        $discount_share_pc = ($item->total_sales > 0 && $item->discount_sales_raw > 0) ? ($item->discount_sales_raw / $item->total_sales) * 100 : 0;

                        $data[] = [
                            'period' => '<strong>' . $item->period . '</strong>',
                            'total_sales' => '₹' . number_format($item->total_sales ?? 0, 2),
                            'regular_sales' => '₹' . number_format($item->regular_sales ?? 0, 2),
                            'discount_sales' => '₹' . number_format($item->discount_sales_raw ?? 0, 2),
                            'discount_amount' => '₹' . number_format($item->discount_amount ?? 0, 2),
                            'discount_pc' => '<span class="text-danger">' . number_format($discount_pc, 1) . '%</span>',
                            'discount_invoices' => '<span class="fw-bold text-dark">' . number_format($item->discount_inv_count ?? 0) . '</span> <span class="text-muted fw-normal">(' . number_format($item->discount_cust_count ?? 0) . ')</span>',
                            'discount_share_pc' => '<span class="fw-semibold text-primary">' . number_format($discount_share_pc, 1) . '%</span>',
                            'net_revenue' => '<span class="text-success fw-bold">₹' . number_format($item->net_revenue ?? 0, 2) . '</span>'
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $data,
                    ]);

                case 'brandwise-lost-sales':
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
                            'sales_order_items.amount',
                            'brands.id as stock_brand_id',
                            'brands.brand_name as stock_brand_name',
                            'brand_categories.name as cat_brand_name'
                        );

                    if ($fromDate && $toDate) {
                        $orderQuery->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
                    } elseif ($fromDate) {
                        $orderQuery->where('sales_orders.so_date', '>=', $fromDate);
                    } elseif ($toDate) {
                        $orderQuery->where('sales_orders.so_date', '<=', $toDate);
                    }
                    if ($customerId) {
                        $orderQuery->where('sales_orders.customer_id', $customerId);
                    }
                    if ($request->brand_id) {
                        $selBrand = $allBrands->firstWhere('id', $request->brand_id);
                        if ($selBrand) {
                            $bCode = trim($selBrand->code ?? '');
                            $bName = trim($selBrand->brand_name ?? '');
                            $orderQuery->where(function($q) use ($selBrand, $bCode, $bName) {
                                $q->where('stock_entry_items.brand_id', $selBrand->id)
                                  ->orWhere('brand_categories.name', $bName);
                                if (!empty($bCode)) {
                                    $q->orWhere('sales_order_items.art_no', 'like', $bCode . '%')
                                      ->orWhere('sales_order_items.sku', 'like', $bCode . '%')
                                      ->orWhere('sales_order_items.item_name', 'like', $bCode . '%');
                                }
                                if (!empty($bName)) {
                                    $q->orWhere('sales_order_items.item_name', 'like', '%' . $bName . '%')
                                      ->orWhere('sales_order_items.art_no', 'like', '%' . $bName . '%');
                                }
                            });
                        }
                    }

                    $orderItems = $orderQuery->get();

                    $brandMap = [];
                    $brandResolveCache = [];
                    $allBrandSoIds = [];

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
                            $cKey = $artNo . '|' . $sku . '|' . $itemName;

                            if (isset($brandResolveCache[$cKey])) {
                                $brandName = $brandResolveCache[$cKey]['name'];
                                $brandId = $brandResolveCache[$cKey]['id'];
                            } else {
                                foreach ($allBrands as $b) {
                                    $bCode = trim($b->code ?? '');
                                    $bNameCandidate = trim($b->brand_name ?? '');
                                    $isMatched = false;

                                    if ($bCode) {
                                        if (($artNo && stripos($artNo, $bCode) === 0) ||
                                            ($sku && stripos($sku, $bCode) === 0) ||
                                            ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') !== false || stripos($itemName, $bCode . ' ') !== false || stripos($itemName, $bCode . '_') !== false))) {
                                            $isMatched = true;
                                        }
                                    }
                                    if (!$isMatched && $bNameCandidate) {
                                        if (($artNo && stripos($artNo, $bNameCandidate) !== false) ||
                                            ($sku && stripos($sku, $bNameCandidate) !== false) ||
                                            ($itemName && stripos($itemName, $bNameCandidate) !== false)) {
                                            $isMatched = true;
                                        }
                                    }

                                    if ($isMatched) {
                                        $brandName = $b->brand_name;
                                        $brandId = $b->id;
                                        break;
                                    }
                                }
                                $brandResolveCache[$cKey] = ['name' => $brandName, 'id' => $brandId];
                            }
                        }

                        if (!$brandName || $brandName === 'Unassigned Brand') {
                            continue;
                        }

                        if ($request->brand_id && $brandId != $request->brand_id) {
                            continue;
                        }

                        if (!isset($brandMap[$brandName])) {
                            $brandMap[$brandName] = [
                                'brand_id' => $brandId,
                                'brand_name' => $brandName,
                                'so_ids' => [],
                                'items' => []
                            ];
                        }

                        $soId = $item->sale_order_id;
                        $brandMap[$brandName]['so_ids'][$soId] = true;
                        $brandMap[$brandName]['items'][] = $item;
                        $allBrandSoIds[$soId] = true;
                    }

                    $allSoIdsList = array_keys($allBrandSoIds);
                    $invMap = [];
                    if (!empty($allSoIdsList)) {
                        $invoices = DB::table('sales_invoice_items')
                            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                            ->whereIn('sales_invoices.so_id', $allSoIdsList)
                            ->whereNull('sales_invoices.deleted_at')
                            ->whereNull('sales_invoice_items.deleted_at')
                            ->select(
                                'sales_invoices.so_id',
                                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                            )
                            ->groupBy('sales_invoices.so_id', 'art_no')
                            ->get();

                        foreach ($invoices as $inv) {
                            $normArt = strtoupper(preg_replace('/\s+/', '', trim($inv->art_no)));
                            $invMap[$inv->so_id][$normArt] = ($invMap[$inv->so_id][$normArt] ?? 0) + floatval($inv->inv_qty ?? 0);
                        }
                    }

                    $allData = [];
                    $totMissedOrders = 0;
                    $totLostRevenue = 0;

                    foreach ($brandMap as $bName => $bData) {
                        $missedOrdersSet = [];
                        $totalLostValue = 0;

                        foreach ($bData['items'] as $item) {
                            $normArt = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
                            $ordQty = floatval($item->qty ?? 0);
                            $ordAmt = floatval($item->amount ?? 0);
                            $rate = $ordQty > 0 ? ($ordAmt / $ordQty) : 0;
                            $invQty = floatval($invMap[$item->sale_order_id][$normArt] ?? 0);

                            $itemPending = max(0, $ordQty - $invQty);
                            if ($itemPending > 0) {
                                $missedOrdersSet[$item->sale_order_id] = true;
                                $totalLostValue += ($itemPending * $rate);
                            }
                        }

                        $missedOrdersCount = count($missedOrdersSet);
                        if ($missedOrdersCount <= 0) {
                            continue;
                        }

                        if ($search && stripos($bName, $search) === false) {
                            continue;
                        }

                        $totMissedOrders += $missedOrdersCount;
                        $totLostRevenue += $totalLostValue;

                        $brandLink = '<a href="javascript:void(0)" class="fw-bold text-primary" onclick="drillDownToLostSalesBrand(\'' . htmlspecialchars($bName, ENT_QUOTES) . '\')">' . htmlspecialchars($bName) . '</a>';

                        $allData[] = [
                            'brand' => $brandLink,
                            'missed_orders' => '<span class="badge bg-danger fw-bold px-2 py-1">' . number_format($missedOrdersCount, 0) . '</span>',
                            'lost_value' => '<span class="text-danger fw-bold">₹' . number_format($totalLostValue, 2) . '</span>',
                        ];
                    }

                    $recordsTotal = count($allData);
                    $recordsFiltered = $recordsTotal;

                    $pagedData = ($length != -1) ? array_slice($allData, $start, $length) : $allData;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pagedData,
                        'totals' => [
                            'missed_orders' => $totMissedOrders,
                            'lost_revenue' => $totLostRevenue,
                        ]
                    ]);

                default:
                    return response()->json(['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("ajaxReportData error ({$type}): " . $e->getMessage());
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getBrandwiseLostSalesOrders(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length);
        $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $search = $request->search['value'] ?? '';

        $brandName = $request->brand_name;
        $customerId = $request->customer_id;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date))) : null;
        $toDate = $request->to_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date))) : null;

        $allBrands = Brand::where('status', 'Active')->orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

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
                'sales_order_items.rate',
                'sales_order_items.amount',
                'sales_order_items.size_id',
                'sales_order_items.sleeve',
                'stock_entry_items.size as stock_size',
                'stock_entry_items.sleeve_type as stock_sleeve',
                'stock_entry_items.barcode',
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
        if ($customerId) {
            $orderQuery->where('sales_orders.customer_id', $customerId);
        }
        if ($search) {
            $orderQuery->where(function($q) use ($search) {
                $q->where('sales_orders.so_no', 'like', "%{$search}%")
                  ->orWhere('sales_orders.order_no', 'like', "%{$search}%")
                  ->orWhere('sales_orders.orderaxe_ref_id', 'like', "%{$search}%")
                  ->orWhere('customers.name', 'like', "%{$search}%")
                  ->orWhere('sales_order_items.art_no', 'like', "%{$search}%");
            });
        }

        if ($brandName) {
            $targetBrand = $allBrands->first(function($b) use ($brandName) {
                return strtolower(trim($b->brand_name)) === strtolower(trim($brandName));
            });
            $bCode = $targetBrand ? trim($targetBrand->code) : '';

            if ($targetBrand) {
                $orderQuery->where(function($q) use ($targetBrand, $bCode, $brandName) {
                    $q->where('stock_entry_items.brand_id', $targetBrand->id)
                      ->orWhere('brand_categories.name', $brandName);
                    if (!empty($bCode)) {
                        $q->orWhere('sales_order_items.art_no', 'like', $bCode . '%')
                          ->orWhere('sales_order_items.sku', 'like', $bCode . '%')
                          ->orWhere('sales_order_items.item_name', 'like', $bCode . '%')
                          ->orWhere('sales_order_items.category_name', 'like', $bCode . '%')
                          ->orWhere('sales_order_items.categories_path_val', 'like', $bCode . '%');
                    }
                    $q->orWhere('sales_order_items.item_name', 'like', '%' . $brandName . '%')
                      ->orWhere('sales_order_items.art_no', 'like', '%' . $brandName . '%');
                });
            }
        }

        $orderItems = $orderQuery->get();

        $brandMap = $this->buildArtNoBrandMap($orderItems, $allBrands);

        $soMap = [];
        $matchedSoIds = [];

        foreach ($orderItems as $item) {
            $resolved = $brandMap[spl_object_id($item)] ?? null;

            if (!$resolved || empty($resolved['name']) || strtolower($resolved['name']) === 'uncategorized') {
                continue;
            }

            $resolvedBrand = $resolved['name'];

            if ($brandName && strtolower($resolvedBrand) !== strtolower($brandName)) {
                continue;
            }

            $soId    = $item->so_id;
            $artNo   = trim($item->art_no ?? '');
            $ordQ    = floatval($item->qty ?? 0);
            $ordAmt  = floatval($item->amount ?? 0);
            $normArtNo = strtoupper(preg_replace('/\s+/', '', $artNo));

            if (!isset($soMap[$soId])) {
                $matchedSoIds[] = $soId;
                $reasonText = !empty($item->reason_for_delay) ? trim($item->reason_for_delay) : '-';
                $orderaxeOrderNo = !empty($item->order_no) ? trim($item->order_no) : (!empty($item->orderaxe_ref_id) ? trim($item->orderaxe_ref_id) : '-');
                $soMap[$soId] = [
                    'so_id'             => $soId,
                    'so_no'             => $item->so_no,
                    'orderaxe_order_no' => $orderaxeOrderNo,
                    'orderaxe_ref_id'   => $item->orderaxe_ref_id ?? $item->order_no,
                    'is_orderaxe'       => !empty($item->orderaxe_id) || !empty($item->orderaxe_ref_id) || !empty($item->order_no),
                    'customer'          => $item->customer_name ?? '-',
                    'so_date'           => date('d M Y', strtotime($item->so_date)),
                    'reason'            => $reasonText,
                    'ordered_qty'       => 0,
                    'ordered_value'     => 0,
                    'so_art_nos'        => [],
                    'art_rates'         => [],
                    'items'             => [],
                ];
            }

            $itemRate = floatval($item->rate ?? 0);
            if ($itemRate <= 0 && $ordQ > 0 && $ordAmt > 0) {
                $itemRate = $ordAmt / $ordQ;
            }
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
            $itemDesc = $formattedCategory ?: ($item->item_name ?: ($item->sku ?: '-'));

            $soMap[$soId]['items'][] = [
                'art_no'        => $artNo,
                'norm_art_no'   => $normArtNo,
                'qty'           => $ordQ,
                'rate'          => $itemRate,
                'amount'        => $ordAmt,
                'description'   => $itemDesc,
                'barcode'       => !empty($item->barcode) ? $item->barcode : (!empty($item->sku) ? $item->sku : '-'),
                'size'          => !empty($item->size_id) ? $item->size_id : (!empty($item->stock_size) ? $item->stock_size : '-'),
                'sleeve'        => !empty($item->sleeve) ? $item->sleeve : (!empty($item->stock_sleeve) ? $item->stock_sleeve : '-'),
            ];

            $soMap[$soId]['ordered_qty']   += $ordQ;
            $soMap[$soId]['ordered_value'] += $ordAmt;

            if (!empty($normArtNo)) {
                $soMap[$soId]['so_art_nos'][$normArtNo] = ($soMap[$soId]['so_art_nos'][$normArtNo] ?? 0) + $ordQ;
                $soMap[$soId]['art_amounts'][$normArtNo] = ($soMap[$soId]['art_amounts'][$normArtNo] ?? 0) + $ordAmt;
                if (!isset($soMap[$soId]['art_display_names'][$normArtNo]) || empty($soMap[$soId]['art_display_names'][$normArtNo])) {
                    $soMap[$soId]['art_display_names'][$normArtNo] = $artNo;
                }
                if ($ordQ > 0) {
                    $soMap[$soId]['art_rates'][$normArtNo] = ($soMap[$soId]['art_amounts'][$normArtNo] / $soMap[$soId]['so_art_nos'][$normArtNo]);
                }
            }
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
                $normalizedArtNo = strtoupper(preg_replace('/\s+/', '', trim($inv->art_no)));
                $invoicedArtNosMap[$inv->so_id][$normalizedArtNo] = ($invoicedArtNosMap[$inv->so_id][$normalizedArtNo] ?? 0) + floatval($inv->invoiced_qty ?? 0);
            }
        }

        $allOrders = [];
        $orderSno = 1;
        $totLostQtyAll = 0;
        $totLostValAll = 0;

        foreach ($soMap as $soId => $data) {
            $soArtNos   = $data['so_art_nos'];
            $artAmounts = $data['art_amounts'] ?? [];
            $artDisplay = $data['art_display_names'] ?? [];

            foreach ($soArtNos as $normArt => $ordQ) {
                $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                $lostQty = max(0, $ordQ - $invQ);

                if ($lostQty <= 0) {
                    continue;
                }

                $ordAmt = floatval($artAmounts[$normArt] ?? 0);
                $rate = $ordQ > 0 ? ($ordAmt / $ordQ) : 0;
                $lostRev = $lostQty * $rate;

                $displayArtNo = !empty($artDisplay[$normArt]) ? $artDisplay[$normArt] : $normArt;

                $soNoHtml = '<a href="' . url('sales_orders/view/' . $soId) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($data['so_no']) . '</a>';
                if ($data['is_orderaxe']) {
                    $orderaxeText = ($data['orderaxe_order_no'] !== '-') ? htmlspecialchars($data['orderaxe_order_no']) : '';
                    if ($orderaxeText) {
                        $soNoHtml .= '<div class="d-flex align-items-center mt-1"><span class="text-secondary fw-semibold small">' . $orderaxeText . '</span></div>';
                    }
                }

                $reasonHtml = $data['reason'] !== '-' ? htmlspecialchars($data['reason']) : '<span class="text-muted">-</span>';

                $allOrders[] = [
                    'sno'               => $orderSno++,
                    'so_id'             => $soId,
                    'so_no_plain'       => $data['so_no'],
                    'so_no'             => $soNoHtml,
                    'art_no'            => '<span class="fw-bold text-primary">' . htmlspecialchars($displayArtNo) . '</span>',
                    'art_no_raw'        => $displayArtNo,
                    'customer'          => htmlspecialchars($data['customer']),
                    'customer_plain'    => $data['customer'],
                    'so_date'           => $data['so_date'],
                    'lost_qty'          => '<a href="javascript:void(0);" class="text-danger fw-bold lost-qty-drill-link" data-so-id="' . $soId . '" data-so-no="' . htmlspecialchars($data['so_no']) . '" data-customer="' . htmlspecialchars($data['customer']) . '" data-so-date="' . htmlspecialchars($data['so_date']) . '" data-art-no="' . htmlspecialchars($displayArtNo) . '" title="View Lost Items Detail">' . number_format($lostQty, 0) . ' <i class="ri ri-arrow-right-s-line" style="font-size:13px;"></i></a>',
                    'lost_qty_raw'      => $lostQty,
                    'selling_price'     => '₹' . number_format($rate, 2),
                    'selling_price_raw' => $rate,
                    'lost_revenue'      => '<span class="text-danger fw-bold">₹' . number_format($lostRev, 2) . '</span>',
                    'lost_revenue_raw'  => $lostRev,
                    'reason'            => $reasonHtml,
                    'reason_raw'        => $data['reason'],
                ];

                $totLostQtyAll += $lostQty;
                $totLostValAll += $lostRev;
            }
        }

        $viewType = $request->view_type ?? 'orders';

        if ($viewType === 'party') {
            $partyMap = [];
            foreach ($soMap as $soId => $data) {
                $soInvQty  = 0;
                $soArtNos  = $data['so_art_nos'];

                foreach ($soArtNos as $normArt => $ordQ) {
                    $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                    $effectiveInvQ = min($ordQ, $invQ);
                    $soInvQty += $effectiveInvQ;
                }
                $pendingQty = max(0, $data['ordered_qty'] - $soInvQty);
                if ($pendingQty <= 0) continue;

                foreach ($data['items'] as $it) {
                    $normArt = $it['norm_art_no'];
                    $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                    $remQ = max(0, $it['qty'] - $invQ);
                    if ($remQ <= 0) continue;

                    $artNo = $it['art_no'];
                    $cust = $data['customer'];
                    $rate = $it['rate'];
                    $reason = $data['reason'];
                    $key = strtoupper($artNo) . '|' . strtoupper($cust) . '|' . number_format($rate, 2) . '|' . $reason;

                    if (!isset($partyMap[$key])) {
                        $partyMap[$key] = [
                            'art_no'        => $artNo,
                            'customer_name' => $cust,
                            'order_qty'     => 0,
                            'rate'          => $rate,
                            'reason'        => $reason,
                        ];
                    }
                    $partyMap[$key]['order_qty'] += $it['qty'];
                }
            }

            $partyRows = [];
            $partySno = 1;
            $totPartyOrd = 0;
            $totPartyRev = 0;

            foreach ($partyMap as $p) {
                $ordQ = $p['order_qty'];
                $rate = $p['rate'];
                $rev = $ordQ * $rate;
                $totPartyOrd += $ordQ;
                $totPartyRev += $rev;

                $partyRows[] = [
                    'sno'           => $partySno++,
                    'art_no'        => '<span class="fw-bold text-primary">' . htmlspecialchars($p['art_no']) . '</span>',
                    'art_no_raw'    => $p['art_no'],
                    'customer_name' => htmlspecialchars($p['customer_name']),
                    'order_qty'     => '<span class="fw-bold text-dark">' . number_format($ordQ, 0) . '</span>',
                    'order_qty_raw' => $ordQ,
                    'selling_price' => '₹' . number_format($rate, 2),
                    'selling_price_raw' => $rate,
                    'revenue'       => '<span class="fw-bold text-danger">₹' . number_format($rev, 2) . '</span>',
                    'revenue_raw'   => $rev,
                    'reason'        => $p['reason'] !== '-' ? htmlspecialchars($p['reason']) : '<span class="text-muted">-</span>',
                    'reason_raw'    => $p['reason'],
                ];
            }

            $filteredParty = $partyRows;
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $filteredParty = array_values(array_filter($partyRows, function($it) use ($searchLower) {
                    return (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                        || (str_contains(strtolower($it['customer_name'] ?? ''), $searchLower))
                        || (str_contains(strtolower((string)$it['order_qty_raw']), $searchLower))
                        || (str_contains(strtolower((string)$it['selling_price_raw']), $searchLower))
                        || (str_contains(strtolower((string)$it['revenue_raw']), $searchLower))
                        || (str_contains(strtolower($it['reason_raw'] ?? ''), $searchLower));
                }));
            }

            $pagedParty = $length != -1 ? array_slice($filteredParty, $start, $length) : $filteredParty;

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => count($partyRows),
                'recordsFiltered' => count($filteredParty),
                'data'            => $pagedParty,
                'totals'          => [
                    'order_qty' => $totPartyOrd,
                    'revenue'   => $totPartyRev,
                ],
            ]);
        }

        if ($viewType === 'product') {
            $prodMap = [];
            foreach ($soMap as $soId => $data) {
                $soInvQty  = 0;
                $soArtNos  = $data['so_art_nos'];

                foreach ($soArtNos as $normArt => $ordQ) {
                    $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                    $effectiveInvQ = min($ordQ, $invQ);
                    $soInvQty += $effectiveInvQ;
                }
                $pendingQty = max(0, $data['ordered_qty'] - $soInvQty);
                if ($pendingQty <= 0) continue;

                foreach ($data['items'] as $it) {
                    $normArt = $it['norm_art_no'];
                    $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                    $remQ = max(0, $it['qty'] - $invQ);
                    if ($remQ <= 0) continue;

                    $artNo = $it['art_no'];
                    $desc = $it['description'];
                    $barcode = $it['barcode'];
                    $size = $it['size'];
                    $sleeve = $it['sleeve'];
                    $key = strtoupper($artNo) . '|' . strtoupper($barcode) . '|' . $size . '|' . $sleeve;

                    if (!isset($prodMap[$key])) {
                        $prodMap[$key] = [
                            'art_no'      => $artNo,
                            'description' => $desc,
                            'barcode'     => $barcode,
                            'size'        => $size,
                            'sleeve'      => $sleeve,
                            'lost_qty'    => 0,
                        ];
                    }
                    $prodMap[$key]['lost_qty'] += $remQ;
                }
            }

            $prodRows = [];
            $prodSno = 1;
            $totLostQ = 0;

            foreach ($prodMap as $p) {
                $lQ = $p['lost_qty'];
                $totLostQ += $lQ;

                $rawSleeve = $p['sleeve'];
                if (!empty($rawSleeve) && is_string($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                    $decoded = json_decode($rawSleeve, true);
                    $rawSleeve = is_array($decoded) && !empty($decoded) ? $decoded[0] : $rawSleeve;
                }
                $sleeveUpper = strtoupper(trim((string)$rawSleeve));
                if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL' || $sleeveUpper === 'FULL SLEEVE') {
                    $sleeveFormatted = 'Full';
                } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF' || $sleeveUpper === 'HALF SLEEVE') {
                    $sleeveFormatted = 'Half';
                } else {
                    $sleeveFormatted = $rawSleeve ?: '-';
                }

                $prodRows[] = [
                    'sno'         => $prodSno++,
                    'art_no'      => '<span class="fw-bold text-primary">' . htmlspecialchars($p['art_no']) . '</span>',
                    'art_no_raw'  => $p['art_no'],
                    'description' => htmlspecialchars($p['description']),
                    'barcode'     => '<span class="badge bg-label-secondary font-monospace">' . htmlspecialchars($p['barcode']) . '</span>',
                    'barcode_raw' => $p['barcode'],
                    'size'        => '<span class="badge bg-label-info">' . htmlspecialchars($p['size']) . '</span>',
                    'size_raw'    => $p['size'],
                    'sleeve'      => '<span class="badge bg-label-secondary">' . htmlspecialchars($sleeveFormatted) . '</span>',
                    'qty'         => '<span class="text-danger fw-bold">' . number_format($lQ, 0) . '</span>',
                    'lost_qty'    => '<span class="text-danger fw-bold">' . number_format($lQ, 0) . '</span>',
                    'qty_raw'     => $lQ,
                ];
            }

            $filteredProd = $prodRows;
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $filteredProd = array_values(array_filter($prodRows, function($it) use ($searchLower) {
                    return (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                        || (str_contains(strtolower($it['description'] ?? ''), $searchLower))
                        || (str_contains(strtolower($it['barcode_raw'] ?? ''), $searchLower))
                        || (str_contains(strtolower($it['size_raw'] ?? ''), $searchLower))
                        || (str_contains(strtolower((string)$it['qty_raw']), $searchLower));
                }));
            }

            $pagedProd = $length != -1 ? array_slice($filteredProd, $start, $length) : $filteredProd;

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => count($prodRows),
                'recordsFiltered' => count($filteredProd),
                'data'            => $pagedProd,
                'totals'          => [
                    'lost_qty' => $totLostQ,
                ],
            ]);
        }

        $filteredOrders = $allOrders;
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $filteredOrders = array_values(array_filter($allOrders, function($it) use ($searchLower) {
                return (str_contains(strtolower($it['so_no_plain'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['customer_plain'] ?? ''), $searchLower))
                    || (str_contains(strtolower((string)$it['lost_qty_raw']), $searchLower))
                    || (str_contains(strtolower((string)$it['selling_price_raw']), $searchLower))
                    || (str_contains(strtolower((string)$it['lost_revenue_raw']), $searchLower))
                    || (str_contains(strtolower($it['reason_raw'] ?? ''), $searchLower));
            }));
        }

        $orderColIdx = $request->input('order.0.column');
        $orderDir = strtolower($request->input('order.0.dir', 'asc'));
        $sortColMap = [
            0 => 'so_no_plain',
            1 => 'art_no_raw',
            2 => 'customer_plain',
            3 => 'lost_qty_raw',
            4 => 'selling_price_raw',
            5 => 'lost_revenue_raw',
            6 => 'reason_raw',
        ];

        if (isset($sortColMap[$orderColIdx])) {
            $colKey = $sortColMap[$orderColIdx];
            usort($filteredOrders, function($a, $b) use ($colKey, $orderDir) {
                if ($colKey === 'so_no_plain') {
                    $cmp = ((int)($a['so_id'] ?? 0) <=> (int)($b['so_id'] ?? 0));
                } else {
                    $valA = $a[$colKey] ?? '';
                    $valB = $b[$colKey] ?? '';
                    if (is_numeric($valA) && is_numeric($valB)) {
                        $cmp = ($valA <=> $valB);
                    } else {
                        $cmp = strcasecmp((string)$valA, (string)$valB);
                    }
                }
                return $orderDir === 'desc' ? -$cmp : $cmp;
            });
        }

        $recordsTotal = count($allOrders);
        $recordsFiltered = count($filteredOrders);

        $pagedOrders = $length != -1 ? array_slice($filteredOrders, $start, $length) : $filteredOrders;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedOrders,
            'totals' => [
                'lost_qty'       => $totLostQtyAll,
                'lost_value'     => $totLostValAll,
            ],
        ]);
    }

    public function getLostItemsDetail(Request $request, $so_id = null)
    {
        $soId = $so_id ?: $request->so_id;
        $so = DB::table('sales_orders')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->where('sales_orders.id', $soId)
            ->whereNull('sales_orders.deleted_at')
            ->first([
                'sales_orders.id',
                'sales_orders.so_no',
                'sales_orders.so_date',
                'sales_orders.total_qty',
                'sales_orders.reason_for_delay',
                'sales_orders.internal_remarks',
                'customers.name as customer_name'
            ]);

        if (!$so) {
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'status' => false,
                'message' => 'Sales Order not found'
            ], 404);
        }

        $brandName = $request->brand_name;
        $allBrands = Brand::where('status', 'Active')->orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

        $soItems = DB::table('sales_order_items')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
            ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
            ->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')
            ->where('sales_order_items.sale_order_id', $soId)
            ->whereNull('sales_order_items.deleted_at')
            ->select(
                'sales_order_items.art_no',
                'sales_order_items.item_name',
                'sales_order_items.sku',
                'sales_order_items.sleeve',
                'sales_order_items.qty',
                'sales_order_items.rate',
                'sales_order_items.amount',
                'sales_order_items.api_color',
                'sales_order_items.size_id',
                'sales_order_items.category_name',
                'sales_order_items.categories_path_val',
                'colors.color_name',
                'stock_entry_items.size as stock_size',
                'stock_entry_items.sleeve_type as stock_sleeve',
                'stock_entry_items.barcode',
                'b_stock.brand_name as stock_brand_name',
                'brand_categories.name as cat_brand_name'
            )
            ->get();

        if (!empty($brandName)) {
            $brandMap = $this->buildArtNoBrandMap($soItems, $allBrands);
            $soItems = $soItems->filter(function($item) use ($brandMap, $brandName) {
                $resolved = $brandMap[spl_object_id($item)] ?? null;
                return $resolved && strtolower(trim($resolved['name'])) === strtolower(trim($brandName));
            })->values();
        }

        $soArtNos = [];
        foreach ($soItems as $item) {
            $norm = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
            if (!empty($norm)) {
                $soArtNos[$norm] = ($soArtNos[$norm] ?? 0) + floatval($item->qty);
            }
        }

        $invRows = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.so_id', $soId)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select(
                'sales_invoice_items.sku',
                'sales_invoice_items.art_no',
                'sales_invoice_items.quantity'
            )
            ->get();

        $invSkuPool = [];
        $invArtNoTotals = [];

        foreach ($invRows as $row) {
            $sku = trim($row->sku ?? '');
            $q = floatval($row->quantity ?? 0);
            if (!empty($sku)) {
                $invSkuPool[$sku] = ($invSkuPool[$sku] ?? 0) + $q;
            }
            $norm = strtoupper(preg_replace('/\s+/', '', trim($row->art_no ?? '')));
            if (!empty($norm)) {
                $invArtNoTotals[$norm] = ($invArtNoTotals[$norm] ?? 0) + $q;
            }
        }

        $soRemMap = [];
        $consumedFromArt = [];

        foreach ($soItems as $idx => $item) {
            $sku = trim($item->barcode ?: ($item->sku ?: ''));
            $norm = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
            $soQ = floatval($item->qty ?? 0);

            $usedSku = 0;
            if (!empty($sku) && isset($invSkuPool[$sku]) && $invSkuPool[$sku] > 0) {
                $usedSku = min($soQ, $invSkuPool[$sku]);
                $invSkuPool[$sku] -= $usedSku;
                if (!empty($norm)) {
                    $consumedFromArt[$norm] = ($consumedFromArt[$norm] ?? 0) + $usedSku;
                }
            }

            $soRemMap[$idx] = [
                'item' => $item,
                'norm' => $norm,
                'remQ' => $soQ - $usedSku,
            ];
        }

        $unallocatedArtPool = [];
        foreach ($invArtNoTotals as $norm => $totInv) {
            $matched = $consumedFromArt[$norm] ?? 0;
            $unallocatedArtPool[$norm] = max(0, $totInv - $matched);
        }

        $lostItems = [];
        $itemSno = 1;
        $totalLostQty = 0;

        foreach ($soRemMap as $idx => $entry) {
            $remQ = $entry['remQ'];
            $norm = $entry['norm'];
            $item = $entry['item'];

            if ($remQ > 0 && !empty($norm) && isset($unallocatedArtPool[$norm]) && $unallocatedArtPool[$norm] > 0) {
                $usedArt = min($remQ, $unallocatedArtPool[$norm]);
                $unallocatedArtPool[$norm] -= $usedArt;
                $remQ -= $usedArt;
            }

            if ($remQ > 0) {
                $row = $this->buildLostItemRow($item, $remQ, $itemSno++);
                $lostItems[] = $row;
                $totalLostQty += $remQ;
            }
        }

        $matchedInvQty = 0;
        foreach ($soArtNos as $norm => $ordQ) {
            $invQ = $invArtNoTotals[$norm] ?? 0;
            $matchedInvQty += min($ordQ, $invQ);
        }

        $soTotalQty = floatval($so->total_qty ?? $soItems->sum('qty'));
        $invTotalQty = $matchedInvQty;
        $netBalance = max(0, $soTotalQty - $matchedInvQty);
        $extraDelivered = max(0, $totalLostQty - $netBalance);

        if ($netBalance <= 0) {
            $lostItems = [];
            $totalLostQty = 0;
        } elseif ($totalLostQty > $netBalance) {
            $excess = $totalLostQty - $netBalance;
            for ($i = count($lostItems) - 1; $i >= 0 && $excess > 0; $i--) {
                $itemQty = floatval($lostItems[$i]['qty_raw'] ?? 0);
                if ($itemQty <= 0) continue;
                $reduction = min($excess, $itemQty);
                $newQty = $itemQty - $reduction;
                $excess -= $reduction;
                $lostItems[$i]['qty_raw'] = $newQty;
                $lostItems[$i]['qty'] = '<span class="text-danger fw-bold">' . number_format($newQty, 0) . '</span>';
            }
            $lostItems = array_values(array_filter($lostItems, function($it) {
                return (floatval($it['qty_raw'] ?? 0)) > 0;
            }));
            foreach ($lostItems as $idx => &$it) {
                $it['sno'] = $idx + 1;
            }
            unset($it);
            $totalLostQty = $netBalance;
        }

        if (!empty($request->art_no)) {
            $filterArtNorm = strtoupper(preg_replace('/\s+/', '', trim($request->art_no)));
            $lostItems = array_values(array_filter($lostItems, function($it) use ($filterArtNorm) {
                $norm = strtoupper(preg_replace('/\s+/', '', trim($it['art_no_raw'] ?? '')));
                return $norm === $filterArtNorm;
            }));
            $itemSno = 1;
            $totalLostQty = 0;
            foreach ($lostItems as &$li) {
                $li['sno'] = $itemSno++;
                $totalLostQty += floatval($li['qty_raw'] ?? 0);
            }
            unset($li);
        }

        $reasonText = !empty($so->reason_for_delay) ? trim($so->reason_for_delay) : '-';
        $customerName = $so->customer_name ?: '-';

        $partyMap = [];
        foreach ($soRemMap as $idx => $entry) {
            $remQ = $entry['remQ'];
            if ($remQ <= 0) continue;
            $item = $entry['item'];
            $artNo = trim($item->art_no ?? '-');
            $rate = floatval($item->rate ?? 0);
            if ($rate <= 0 && floatval($item->qty ?? 0) > 0 && floatval($item->amount ?? 0) > 0) {
                $rate = floatval($item->amount) / floatval($item->qty);
            }
            $ordQ = floatval($item->qty ?? 0);
            $key = strtoupper($artNo) . '|' . number_format($rate, 2);

            if (!isset($partyMap[$key])) {
                $partyMap[$key] = [
                    'art_no'        => $artNo,
                    'customer_name' => $customerName,
                    'order_qty'     => 0,
                    'rate'          => $rate,
                    'reason'        => $reasonText,
                ];
            }
            $partyMap[$key]['order_qty'] += $ordQ;
        }

        $partyRows = [];
        $partySno = 1;
        $totPartyOrdQty = 0;
        $totPartyRevenue = 0;

        foreach ($partyMap as $p) {
            $ordQ = $p['order_qty'];
            $rate = $p['rate'];
            $rev = $ordQ * $rate;
            $totPartyOrdQty += $ordQ;
            $totPartyRevenue += $rev;

            $partyRows[] = [
                'sno'           => $partySno++,
                'art_no'        => '<span class="fw-bold text-primary">' . htmlspecialchars($p['art_no']) . '</span>',
                'art_no_raw'    => $p['art_no'],
                'customer_name' => htmlspecialchars($p['customer_name']),
                'order_qty'     => '<span class="fw-bold text-dark">' . number_format($ordQ, 0) . '</span>',
                'order_qty_raw' => $ordQ,
                'selling_price' => '₹' . number_format($rate, 2),
                'selling_price_raw' => $rate,
                'revenue'       => '<span class="fw-bold text-danger">₹' . number_format($rev, 2) . '</span>',
                'revenue_raw'   => $rev,
                'reason'        => $p['reason'] !== '-' ? htmlspecialchars($p['reason']) : '<span class="text-muted">-</span>',
                'reason_raw'    => $p['reason'],
            ];
        }

        if ($request->view_type === 'party') {
            $search = $request->search['value'] ?? '';
            $filteredParty = $partyRows;
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $filteredParty = array_values(array_filter($partyRows, function($it) use ($searchLower) {
                    return (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                        || (str_contains(strtolower($it['customer_name'] ?? ''), $searchLower))
                        || (str_contains(strtolower((string)$it['order_qty_raw']), $searchLower))
                        || (str_contains(strtolower((string)$it['selling_price_raw']), $searchLower))
                        || (str_contains(strtolower((string)$it['revenue_raw']), $searchLower))
                        || (str_contains(strtolower($it['reason_raw'] ?? ''), $searchLower));
                }));
            }

            $draw = intval($request->draw ?? 1);
            $start = intval($request->start ?? 0);
            $rawLen = intval($request->length ?? 10);
            $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
            $pagedParty = $length != -1 ? array_slice($filteredParty, $start, $length) : $filteredParty;

            return response()->json([
                'draw'             => $draw,
                'recordsTotal'     => count($partyRows),
                'recordsFiltered'  => count($filteredParty),
                'data'             => $pagedParty,
                'total_order_qty'  => number_format($totPartyOrdQty, 0),
                'total_revenue'    => number_format($totPartyRevenue, 2),
                'totals'           => [
                    'order_qty' => $totPartyOrdQty,
                    'revenue'   => $totPartyRevenue,
                ],
                'so_no'            => $so->so_no,
                'customer'         => $so->customer_name ?? '-',
                'so_date'          => $so->so_date ? date('d-m-Y', strtotime($so->so_date)) : '-',
                'reason'           => $reasonText,
                'status'           => true,
            ]);
        }

        $allArtNos = [];
        foreach ($lostItems as $li) {
            $rawArt = trim($li['art_no_raw'] ?? '');
            if (!empty($rawArt) && $rawArt !== '-') {
                $allArtNos[] = $rawArt;
            }
        }
        $allArtNos = array_values(array_unique($allArtNos));

        $fgStockMap = [];
        $cuttingMap = [];

        if (!empty($allArtNos)) {
            $stockRows = DB::table('stock_entry_items')
                ->where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->where(function($q) use ($allArtNos) {
                    $q->whereIn('art_no', $allArtNos)
                      ->orWhereIn('finished_item_code', $allArtNos);
                })
                ->select(
                    'art_no',
                    'finished_item_code',
                    'size',
                    'sleeve_type',
                    DB::raw('SUM(qty_in - qty_out) as balance')
                )
                ->groupBy('art_no', 'finished_item_code', 'size', 'sleeve_type')
                ->get();

            foreach ($stockRows as $sr) {
                $artKey = strtoupper(preg_replace('/\s+/', '', $sr->art_no ?: $sr->finished_item_code));
                $sizeKey = strtoupper(trim((string)$sr->size));
                $slvKey = strtoupper(trim((string)$sr->sleeve_type));
                if (str_starts_with($slvKey, 'FULL') || $slvKey === 'FS' || $slvKey === 'F/S') $slvKey = 'FULL';
                elseif (str_starts_with($slvKey, 'HALF') || $slvKey === 'HS' || $slvKey === 'H/S') $slvKey = 'HALF';

                $balVal = floatval($sr->balance);
                $fgStockMap[$artKey][$sizeKey][$slvKey] = ($fgStockMap[$artKey][$sizeKey][$slvKey] ?? 0) + $balVal;
                $fgStockMap[$artKey][$sizeKey]['ALL'] = ($fgStockMap[$artKey][$sizeKey]['ALL'] ?? 0) + $balVal;
                $fgStockMap[$artKey]['TOTAL'] = ($fgStockMap[$artKey]['TOTAL'] ?? 0) + $balVal;
            }

            // 2. Active Cuttings from job_card_entries & job_card_fabric_details
            $activeJcs = DB::table('job_card_entries as jce')
                ->join('job_card_fabric_details as jcfd', 'jce.id', '=', 'jcfd.job_card_entry_id')
                ->leftJoin('service_providers as sp', 'jce.service_provider_id', '=', 'sp.id')
                ->where(function($q) use ($allArtNos) {
                    $q->whereIn('jcfd.art_no', $allArtNos)
                      ->orWhereIn('jcfd.fg_art_no', $allArtNos);
                })
                ->whereNull('jce.deleted_at')
                ->whereNull('jcfd.deleted_at')
                ->whereNotIn('jce.status', ['Completed', 'Cancel', 'Cancelled'])
                ->select(
                    'jce.id',
                    'jce.job_card_no',
                    'jce.status as jc_status',
                    'jcfd.art_no',
                    'jcfd.fg_art_no',
                    'sp.name as unit_name',
                    'sp.code as unit_code'
                )
                ->distinct()
                ->get();

            $jcIds = $activeJcs->pluck('id')->unique()->toArray();

            $schedulesByJc = DB::table('process_schedules')->whereIn('job_card_entry_id', $jcIds)->whereNull('deleted_at')->orderBy('operation_stage_id', 'asc')->get()->groupBy('job_card_entry_id');

            $tasksByJc = DB::table('tasks')->whereIn('job_card_entry_id', $jcIds)->whereNull('deleted_at')->get()->groupBy('job_card_entry_id');

            foreach ($activeJcs as $jc) {
                $matchedArts = array_filter([$jc->art_no, $jc->fg_art_no]);
                $schedules = $schedulesByJc->get($jc->id, collect());
                $currentStage = 'CUTTING';

                if ($schedules->isNotEmpty()) {
                    $tasks = $tasksByJc->get($jc->id, collect());
                    $isAllDone = true;
                    foreach ($schedules as $sched) {
                        $schedTasks = $tasks->where('stage_id', $sched->id);
                        $isDone = false;
                        if (strtolower(trim($sched->status ?? '')) === 'completed') {
                            $isDone = true;
                        } elseif ($schedTasks->isNotEmpty() && $schedTasks->every(function($t) {
                            return strtolower(trim($t->status ?? '')) === 'completed';
                        })) {
                            $isDone = true;
                        }
                        if (!$isDone) {
                            $currentStage = strtoupper(trim($sched->stage ?? ''));
                            $isAllDone = false;
                            break;
                        }
                    }
                    if ($isAllDone) {
                        $currentStage = 'COMPLETED';
                    }
                }

                // Skip completed or cancelled cuttings — only active in-progress pipeline should be tracked here
                if ($currentStage === 'COMPLETED' || in_array(strtolower(trim($jc->jc_status ?? '')), ['completed', 'production completed', 'cancel', 'cancelled'])) {
                    continue;
                }

                $unitDisplay = !empty($jc->unit_name) ? $jc->unit_name : (!empty($jc->unit_code) ? $jc->unit_code : '-');

                foreach ($matchedArts as $aNo) {
                    $normArt = strtoupper(preg_replace('/\s+/', '', $aNo));
                    if (!isset($cuttingMap[$normArt])) {
                        $cuttingMap[$normArt] = [];
                    }
                    $exists = false;
                    foreach ($cuttingMap[$normArt] as $existing) {
                        if ($existing['id'] == $jc->id) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $cuttingMap[$normArt][] = [
                            'id'          => $jc->id,
                            'job_card_no' => $jc->job_card_no,
                            'unit'        => $unitDisplay,
                            'stage'       => $currentStage,
                        ];
                    }
                }
            }
        }

        foreach ($lostItems as &$li) {
            $rawArt = trim($li['art_no_raw'] ?? '');
            $normArt = strtoupper(preg_replace('/\s+/', '', $rawArt));
            $rawSize = strtoupper(trim($li['size_raw'] ?? ''));
            $rawSlv = strtoupper(trim(strip_tags($li['sleeve'] ?? '')));
            if (str_starts_with($rawSlv, 'FULL') || $rawSlv === 'FS' || $rawSlv === 'F/S') $rawSlv = 'FULL';
            elseif (str_starts_with($rawSlv, 'HALF') || $rawSlv === 'HS' || $rawSlv === 'H/S') $rawSlv = 'HALF';

            $bal = $fgStockMap[$normArt][$rawSize][$rawSlv] 
                ?? $fgStockMap[$normArt][$rawSize]['ALL'] 
                ?? 0;

            if ($bal > 0) {
                $li['fg_stock_html'] = '<span class="badge bg-label-success fw-bold">' . number_format($bal, 0) . ' pcs</span>';
                $li['fg_stock'] = $bal;
            } else {
                $li['fg_stock_html'] = '<span class="text-muted fw-bold">0</span>';
                $li['fg_stock'] = 0;
            }

            $cuttings = $cuttingMap[$normArt] ?? [];
            if (!empty($cuttings)) {
                $cutLines = [];
                $unitLines = [];
                $stageLines = [];

                $maxShow = 3;
                $sliceCuttings = array_slice($cuttings, 0, $maxShow);
                $remainingCount = count($cuttings) - $maxShow;
                $rowUniqueId = 'cut_' . ($li['sno'] ?? rand(100, 999)) . '_' . substr(md5($normArt . $rawSize . $rawSlv), 0, 6);

                foreach ($sliceCuttings as $c) {
                    $cutLines[] = '<div class="text-nowrap fw-bold text-primary mb-1">' . htmlspecialchars($c['job_card_no']) . '</div>';
                    $unitLines[] = '<div class="text-nowrap fw-semibold text-dark mb-1">' . htmlspecialchars($c['unit']) . '</div>';

                    $stg = $c['stage'];
                    $badgeClass = 'bg-label-primary';
                    if (str_contains($stg, 'STITCHING')) {
                        $badgeClass = 'bg-label-warning text-dark';
                    } elseif (str_contains($stg, 'KAJA') || str_contains($stg, 'BUTTON')) {
                        $badgeClass = 'bg-label-info';
                    } elseif (str_contains($stg, 'TRIMMING') || str_contains($stg, 'CHECKING')) {
                        $badgeClass = 'bg-label-dark';
                    } elseif (str_contains($stg, 'IRONING') || str_contains($stg, 'PACKING') || str_contains($stg, 'COMPLETED')) {
                        $badgeClass = 'bg-label-success';
                    }

                    $stageLines[] = '<div class="text-nowrap mb-1"><span class="badge ' . $badgeClass . '">' . htmlspecialchars($stg) . '</span></div>';
                }

                if ($remainingCount > 0) {
                    $extraCutLines = [];
                    $extraUnitLines = [];
                    $extraStageLines = [];

                    foreach (array_slice($cuttings, $maxShow) as $remC) {
                        $extraCutLines[] = '<div class="text-nowrap fw-bold text-primary mb-1">' . htmlspecialchars($remC['job_card_no']) . '</div>';
                        $extraUnitLines[] = '<div class="text-nowrap fw-semibold text-dark mb-1">' . htmlspecialchars($remC['unit']) . '</div>';

                        $remStg = $remC['stage'];
                        $remBadgeClass = 'bg-label-primary';
                        if (str_contains($remStg, 'STITCHING')) {
                            $remBadgeClass = 'bg-label-warning text-dark';
                        } elseif (str_contains($remStg, 'KAJA') || str_contains($remStg, 'BUTTON')) {
                            $remBadgeClass = 'bg-label-info';
                        } elseif (str_contains($remStg, 'TRIMMING') || str_contains($remStg, 'CHECKING')) {
                            $remBadgeClass = 'bg-label-dark';
                        } elseif (str_contains($remStg, 'IRONING') || str_contains($remStg, 'PACKING') || str_contains($remStg, 'COMPLETED')) {
                            $remBadgeClass = 'bg-label-success';
                        }

                        $extraStageLines[] = '<div class="text-nowrap mb-1"><span class="badge ' . $remBadgeClass . '">' . htmlspecialchars($remStg) . '</span></div>';
                    }

                    $cutLines[] = '<div class="extra-cuttings extra-cut-' . $rowUniqueId . '" style="display: none;">' . implode('', $extraCutLines) . '</div>';
                    $unitLines[] = '<div class="extra-cuttings extra-cut-' . $rowUniqueId . '" style="display: none;">' . implode('', $extraUnitLines) . '</div>';
                    $stageLines[] = '<div class="extra-cuttings extra-cut-' . $rowUniqueId . '" style="display: none;">' . implode('', $extraStageLines) . '</div>';

                    $cutLines[] = '<a href="javascript:void(0)" class="toggle-cut-btn badge bg-label-info text-primary fw-bold text-decoration-none d-inline-block mt-1" onclick="toggleCuttingExpansion(\'' . $rowUniqueId . '\', this, ' . $remainingCount . ')">+ ' . $remainingCount . ' more <i class="ri-arrow-down-s-line"></i></a>';
                    $unitLines[] = '<div class="toggle-cut-spacer-' . $rowUniqueId . '" style="height: 22px;">&nbsp;</div>';
                    $stageLines[] = '<div class="toggle-cut-spacer-' . $rowUniqueId . '" style="height: 22px;">&nbsp;</div>';
                }

                $li['cutting_no_html'] = implode('', $cutLines);
                $li['unit_html'] = implode('', $unitLines);
                $li['stage_status_html'] = implode('', $stageLines);
                $li['has_cutting'] = true;
            } else {
                $li['cutting_no_html'] = '<span class="text-muted">-</span>';
                $li['unit_html'] = '<span class="text-muted">-</span>';
                $li['stage_status_html'] = '<span class="badge bg-label-secondary">Not in Production</span>';
                $li['has_cutting'] = false;
            }
        }
        unset($li);

        $search = $request->search['value'] ?? '';
        $filteredItems = $lostItems;
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $filteredItems = array_values(array_filter($lostItems, function($it) use ($searchLower) {
                return (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['description'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['barcode_raw'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['color'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['size'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['sleeve'] ?? ''), $searchLower));
            }));
        }

        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLen = intval($request->length ?? 10);
        $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $pagedItems = $length != -1 ? array_slice($filteredItems, $start, $length) : $filteredItems;

        return response()->json([
            'draw'             => $draw,
            'recordsTotal'     => count($lostItems),
            'recordsFiltered'  => count($filteredItems),
            'data'             => $pagedItems,
            'total_lost_qty'   => number_format($totalLostQty, 0),
            'total_order_qty'  => number_format($totPartyOrdQty, 0),
            'total_revenue'    => number_format($totPartyRevenue, 2),
            'order_qty'        => number_format($soTotalQty, 0),
            'delivered_qty'    => number_format($invTotalQty, 0),
            'net_balance_qty'  => number_format($netBalance, 0),
            'extra_billed_qty' => number_format($extraDelivered, 0),
            'so_no'            => $so->so_no,
            'customer'         => $so->customer_name ?? '-',
            'so_date'          => $so->so_date ? date('d-m-Y', strtotime($so->so_date)) : '-',
            'reason'           => $reasonText,
            'status'           => true,
            'items'            => $lostItems,
            'party_items'      => $partyRows,
        ]);
    }

    protected function calculateOrderPendingArts($order, $validInvoices = null)
    {
        if (!$order) return ['qty' => 0, 'count' => 0];
        $totalQty = (float)($order->total_qty ?? 0);

        if ($validInvoices === null) {
            $validInvoices = $order->salesInvoices ? $order->salesInvoices->filter(function($inv) {
                return empty($inv->einvoice_status) || strtolower((string)$inv->einvoice_status) !== 'cancelled';
            }) : collect();
        }

        $deliveredQty = 0;
        foreach ($validInvoices as $inv) {
            $deliveredQty += $inv->items ? $inv->items->sum('quantity') : 0;
        }

        if ($deliveredQty >= $totalQty && $totalQty > 0) {
            return ['qty' => 0, 'count' => 0];
        }

        $invSkuPool = [];
        $invArtNoTotals = [];
        foreach ($validInvoices as $inv) {
            if (!$inv->items) continue;
            foreach ($inv->items as $sii) {
                $sku = trim($sii->sku ?? '');
                $q = floatval($sii->quantity ?? 0);
                if (!empty($sku)) {
                    $invSkuPool[$sku] = ($invSkuPool[$sku] ?? 0) + $q;
                }
                $norm = strtoupper(preg_replace('/\s+/', '', trim($sii->art_no ?? '')));
                if (!empty($norm)) {
                    $invArtNoTotals[$norm] = ($invArtNoTotals[$norm] ?? 0) + $q;
                }
            }
        }

        $soRemMap = [];
        $consumedFromArt = [];
        $soItems = $order->items ?? collect();
        foreach ($soItems as $idx => $item) {
            $sku = trim($item->barcode ?: ($item->sku ?: ''));
            $norm = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
            $soQ = floatval($item->qty ?? 0);
            $usedSku = 0;
            if (!empty($sku) && isset($invSkuPool[$sku]) && $invSkuPool[$sku] > 0) {
                $usedSku = min($soQ, $invSkuPool[$sku]);
                $invSkuPool[$sku] -= $usedSku;
                if (!empty($norm)) {
                    $consumedFromArt[$norm] = ($consumedFromArt[$norm] ?? 0) + $usedSku;
                }
            }
            $soRemMap[$idx] = [
                'norm' => $norm,
                'remQ' => $soQ - $usedSku,
            ];
        }

        $unallocatedArtPool = [];
        foreach ($invArtNoTotals as $norm => $totInv) {
            $matched = $consumedFromArt[$norm] ?? 0;
            $unallocatedArtPool[$norm] = max(0, $totInv - $matched);
        }

        $pendingArtQty = 0;
        $pendingArtsMap = [];
        foreach ($soRemMap as $idx => $entry) {
            $remQ = $entry['remQ'];
            $norm = $entry['norm'];
            if ($remQ > 0 && !empty($norm) && isset($unallocatedArtPool[$norm]) && $unallocatedArtPool[$norm] > 0) {
                $usedArt = min($remQ, $unallocatedArtPool[$norm]);
                $unallocatedArtPool[$norm] -= $usedArt;
                $remQ -= $usedArt;
            }
            if ($remQ > 0) {
                $pendingArtQty += $remQ;
                if (!empty($norm)) {
                    $pendingArtsMap[$norm] = true;
                }
            }
        }

        $netPendingQty = max(0, $totalQty - $deliveredQty);
        $finalPendingArtQty = min($netPendingQty, $pendingArtQty);

        return [
            'qty' => $finalPendingArtQty,
            'count' => count($pendingArtsMap),
        ];
    }

    protected function calculateOrderPendingArtQty($order, $validInvoices = null)
    {
        $res = $this->calculateOrderPendingArts($order, $validInvoices);
        return is_array($res) ? ($res['qty'] ?? 0) : $res;
    }

    protected function buildLostItemRow($item, $qty, $sno = 1)
    {
        $rawSleeve = $item->sleeve ?: ($item->stock_sleeve ?? '');
        if (!empty($rawSleeve) && is_string($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
            $decoded = json_decode($rawSleeve, true);
            $rawSleeve = is_array($decoded) && !empty($decoded) ? $decoded[0] : $rawSleeve;
        }
        $sleeveUpper = strtoupper(trim((string)$rawSleeve));
        if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL' || $sleeveUpper === 'FULL SLEEVE') {
            $sleeveFormatted = 'Full';
        } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF' || $sleeveUpper === 'HALF SLEEVE') {
            $sleeveFormatted = 'Half';
        } else {
            $sleeveFormatted = $rawSleeve ?: '-';
        }

        $color = !empty($item->color_name) ? $item->color_name : (!empty($item->api_color) ? $item->api_color : '-');
        $size = !empty($item->size_id) ? $item->size_id : (!empty($item->stock_size) ? $item->stock_size : '-');
        $barcode = !empty($item->barcode) ? $item->barcode : (!empty($item->sku) ? $item->sku : '-');
        $artNo = !empty($item->art_no) ? $item->art_no : '-';

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
        $desc = $formattedCategory ?: ($item->item_name ?: ($item->sku ?: '-'));

        return [
            'sno'         => $sno,
            'art_no'      => '<span class="fw-bold text-primary">' . htmlspecialchars($artNo) . '</span>',
            'art_no_raw'  => $artNo,
            'description' => htmlspecialchars($desc),
            'barcode'     => '<span class="badge bg-label-secondary font-monospace">' . htmlspecialchars($barcode) . '</span>',
            'barcode_raw' => $barcode,
            'color'       => htmlspecialchars($color),
            'size'        => '<span class="badge bg-label-info">' . htmlspecialchars($size) . '</span>',
            'size_raw'    => $size,
            'sleeve'      => '<span class="badge bg-label-secondary">' . htmlspecialchars($sleeveFormatted) . '</span>',
            'qty'         => '<span class="text-danger fw-bold">' . number_format($qty, 0) . '</span>',
            'qty_raw'     => $qty,
        ];
    }

    protected function resolveJobCardCurrentStage($jobCardId, $schedulesByJc, $tasksByJc, $opsByJc)
    {
        if (!$jobCardId) return '-';

        $schedules = $schedulesByJc->get($jobCardId, collect());
        if ($schedules->isNotEmpty()) {
            $tasks = $tasksByJc->get($jobCardId, collect());

            foreach ($schedules as $schedule) {
                $scheduleTasks = $tasks->where('stage_id', $schedule->id);

                $isCompleted = false;
                if (strtolower(trim($schedule->status ?? '')) === 'completed') {
                    $isCompleted = true;
                } elseif ($scheduleTasks->isNotEmpty() && $scheduleTasks->every(function($t) {
                    return strtolower(trim($t->status ?? '')) === 'completed';
                })) {
                    $isCompleted = true;
                }

                if (!$isCompleted) {
                    return strtoupper(trim($schedule->stage ?? ''));
                }
            }

            return 'COMPLETED';
        }

        $operations = $opsByJc->get($jobCardId, collect());
        if ($operations->isNotEmpty()) {
            $tasks = $tasksByJc->get($jobCardId, collect());

            foreach ($operations as $op) {
                $stageName = strtoupper(trim($op->operation_stage_name ?? ''));
                $osId = $op->operation_stage_id;

                $opTasks = $tasks->filter(function($t) use ($osId) {
                    return $t->stage_id == $osId;
                });

                $isCompleted = false;
                if ($opTasks->isNotEmpty() && $opTasks->every(function($t) {
                    return strtolower(trim($t->status ?? '')) === 'completed';
                })) {
                    $isCompleted = true;
                }

                if (!$isCompleted) {
                    return $stageName ?: 'CUTTING';
                }
            }

            return 'COMPLETED';
        }

        return 'CUTTING';
    }

    protected function resolveBrandFromOrderItem($item, $allBrands)
    {
        if (!empty($item->stock_brand_name)) {
            return ['name' => trim($item->stock_brand_name), 'id' => $item->stock_brand_id ?? null];
        }
        if (!empty($item->cat_brand_name)) {
            return ['name' => trim($item->cat_brand_name), 'id' => null];
        }

        $artNo = trim($item->art_no ?? '');
        $sku = trim($item->sku ?? '');
        $itemName = trim($item->item_name ?? '');
        $catName = trim($item->category_name ?? '');
        $catPath = trim($item->categories_path_val ?? '');

        if (stripos($artNo, 'BC') === 0 || stripos($sku, 'BC') === 0 || stripos($itemName, 'BC-') === 0 || stripos($catName, 'BC-') === 0) {
            $cbc = $allBrands->firstWhere('code', 'CBC');
            if ($cbc) {
                return ['name' => $cbc->brand_name, 'id' => $cbc->id];
            }
        }

        foreach ($allBrands as $b) {
            $bCode = trim($b->code ?? '');
            $bName = trim($b->brand_name ?? '');

            if (empty($bCode) && empty($bName)) {
                continue;
            }

            if ($bCode) {
                if (($artNo && (stripos($artNo, $bCode) === 0 || stripos($artNo, $bCode . '-') === 0)) ||
                    ($sku && (stripos($sku, $bCode) === 0 || stripos($sku, $bCode . '-') === 0)) ||
                    ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') === 0 || stripos($itemName, $bCode . ' ') === 0)) ||
                    ($catName && (stripos($catName, $bCode) === 0 || stripos($catName, $bCode . '-') === 0 || stripos($catName, $bCode . ' ') === 0)) ||
                    ($catPath && (stripos($catPath, $bCode) === 0 || stripos($catPath, $bCode . '-') === 0))) {
                    return ['name' => $b->brand_name, 'id' => $b->id];
                }
            }

            if ($bName) {
                if (($artNo && stripos($artNo, $bName) !== false) ||
                    ($sku && stripos($sku, $bName) !== false) ||
                    ($itemName && stripos($itemName, $bName) !== false) ||
                    ($catName && stripos($catName, $bName) !== false) ||
                    ($catPath && stripos($catPath, $bName) !== false)) {
                    return ['name' => $b->brand_name, 'id' => $b->id];
                }
            }
        }

        return null;
    }

    protected function buildArtNoBrandMap($orderItems, $allBrands)
    {
        $map = []; 
        $artNoMap = [];

        foreach ($orderItems as $item) {
            if (!empty($item->stock_brand_name)) {
                $artNoMap[spl_object_id($item)] = ['name' => trim($item->stock_brand_name), 'id' => $item->stock_brand_id ?? null];
                continue;
            }
            if (!empty($item->cat_brand_name)) {
                $artNoMap[spl_object_id($item)] = ['name' => trim($item->cat_brand_name), 'id' => null];
                continue;
            }

            $cacheKey = ($item->art_no ?? '') . '|||' . ($item->sku ?? '') . '|||' . ($item->item_name ?? '') . '|||' . ($item->category_name ?? '');
            if (!array_key_exists($cacheKey, $map)) {
                $map[$cacheKey] = $this->resolveBrandFromOrderItem($item, $allBrands);
            }
            $artNoMap[spl_object_id($item)] = $map[$cacheKey];
        }

        return $artNoMap;
    }

    public function importTallyOutstanding(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx,csv,txt|max:10240',
        ], [
            'import_file.required' => 'Please select a Tally Excel or CSV file to import.',
            'import_file.mimes'    => 'Only .xlsx, .xls, and .csv files are supported.',
            'import_file.max'      => 'File size cannot exceed 10MB.',
        ]);

        $file = $request->file('import_file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                return response()->json([
                    'status'  => false,
                    'message' => 'The uploaded file is empty or contains no data rows.'
                ], 422);
            }

            $firstRow = reset($rows);
            $colCode = null;
            $colName = null;
            $colQty = null;
            $colAmount = null;
            $colDate = null;
            $colRemarks = null;

            foreach ($firstRow as $colKey => $colHeader) {
                $h = strtolower(trim((string)$colHeader));
                if (empty($h)) continue;

                if (!$colCode && (str_contains($h, 'code') || $h === 'cust_code' || $h === 'customer code' || $h === 'party code')) {
                    $colCode = $colKey;
                } elseif (!$colName && (str_contains($h, 'name') || str_contains($h, 'party') || str_contains($h, 'customer') || str_contains($h, 'ledger'))) {
                    $colName = $colKey;
                } elseif (!$colQty && (str_contains($h, 'qty') || str_contains($h, 'quantity') || str_contains($h, 'pieces') || str_contains($h, 'nos'))) {
                    $colQty = $colKey;
                } elseif (!$colAmount && (str_contains($h, 'amount') || str_contains($h, 'outstanding') || str_contains($h, 'balance') || str_contains($h, 'bal') || str_contains($h, 'open'))) {
                    $colAmount = $colKey;
                } elseif (!$colDate && str_contains($h, 'date')) {
                    $colDate = $colKey;
                } elseif (!$colRemarks && (str_contains($h, 'remark') || str_contains($h, 'note'))) {
                    $colRemarks = $colKey;
                }
            }

            if (!$colCode) $colCode = 'A';
            if (!$colName) $colName = 'B';
            if (!$colQty) $colQty = 'C';
            if (!$colAmount) $colAmount = 'D';
            if (!$colDate) $colDate = 'E';
            if (!$colRemarks) $colRemarks = 'F';

            $defaultDate = $request->filled('as_on_date') ? date('Y-m-d', strtotime((string)$request->as_on_date)) : now()->format('Y-m-d');
            $defaultFinYear = $request->filled('financial_year') ? trim((string)$request->financial_year) : (date('n') >= 4 ? date('Y') . '-' . (date('Y') + 1) : (date('Y') - 1) . '-' . date('Y'));

            $customersByCode = Customer::all()->keyBy(function($c) { return strtoupper(trim((string)$c->code)); });
            $customersByName = Customer::all()->keyBy(function($c) { return strtolower(trim((string)$c->name)); });

            $imported = 0;
            $isFirst = true;

            foreach ($rows as $row) {
                if ($isFirst) {
                    $isFirst = false;
                    continue; 
                }

                $codeVal = trim((string)($row[$colCode] ?? ''));
                $nameVal = trim((string)($row[$colName] ?? ''));
                $rawQty = trim((string)($row[$colQty] ?? '0'));
                $rawAmt = trim((string)($row[$colAmount] ?? '0'));

                if (empty($codeVal) && empty($nameVal)) {
                    continue; 
                }

                $cleanAmt = preg_replace('/[^0-9.-]/', '', $rawAmt);
                $amtVal = is_numeric($cleanAmt) ? (float)$cleanAmt : 0;

                $cleanQty = preg_replace('/[^0-9.-]/', '', $rawQty);
                $qtyVal = is_numeric($cleanQty) ? (float)$cleanQty : 0;

                $customer = null;
                if (!empty($codeVal) && isset($customersByCode[strtoupper($codeVal)])) {
                    $customer = $customersByCode[strtoupper($codeVal)];
                } elseif (!empty($nameVal) && isset($customersByName[strtolower($nameVal)])) {
                    $customer = $customersByName[strtolower($nameVal)];
                }

                $finalCode = !empty($codeVal) ? $codeVal : ($customer ? $customer->code : 'UNKNOWN');
                $finalName = !empty($nameVal) ? $nameVal : ($customer ? $customer->name : '-');

                $asOnDate = $defaultDate;
                if ($colDate && !empty($row[$colDate])) {
                    $parsed = strtotime(trim((string)$row[$colDate]));
                    if ($parsed !== false && $parsed > 0) {
                        $asOnDate = date('Y-m-d', $parsed);
                    }
                }

                $remVal = $colRemarks && !empty($row[$colRemarks]) ? trim((string)$row[$colRemarks]) : 'Tally Import';

                CustomerOpeningBalance::updateOrCreate(
                    ['customer_code' => $finalCode],
                    [
                        'customer_id'    => $customer ? $customer->id : null,
                        'customer_name'  => $finalName,
                        'open_qty'       => $qtyVal,
                        'open_amount'    => $amtVal,
                        'as_on_date'     => $asOnDate,
                        'financial_year' => $defaultFinYear,
                        'remarks'        => $remVal,
                        'created_by'     => auth()->id(),
                    ]
                );

                $imported++;
            }

            return response()->json([
                'status'   => true,
                'message'  => "Successfully imported {$imported} customer opening balance(s) from Tally!",
                'imported' => $imported,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTallyTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tally_opening_outstanding_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Customer Code',
                'Customer Name',
                'Open Outstanding Qty',
                'Open Outstanding Amount'
            ]);

            $sampleCusts = Customer::where('status', 'Active')->take(2)->get();
            if ($sampleCusts->count() > 0) {
                foreach ($sampleCusts as $idx => $c) {
                    fputcsv($handle, [
                        $c->code ?: ('CUST' . (1001 + $idx)),
                        $c->name ?: 'Sample Textiles',
                        $idx == 0 ? '120' : '0',
                        $idx == 0 ? '75000.00' : '42500.00'
                    ]);
                }
            } else {
                fputcsv($handle, ['CUST001', 'Sample Textiles', '100', '50000.00']);
                fputcsv($handle, ['CUST002', 'Modern Fashion Mart', '0', '25000.00']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateGstFiling(Request $request)
    {
        $userName = auth()->user() ? auth()->user()->name : 'User';
        $userId = auth()->id();
        $isSelectAllMatching = $request->boolean('select_all_matching');

        if ($isSelectAllMatching) {
            $parseDate = function($dateStr) {
                if (empty($dateStr) || $dateStr === 'DD-MM-YYYY' || trim($dateStr) === '') {
                    return null;
                }
                try {
                    return \Carbon\Carbon::createFromFormat('d-m-Y', trim($dateStr))->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        $ts = strtotime(trim($dateStr));
                        return ($ts !== false && $ts > 0) ? date('Y-m-d', $ts) : null;
                    } catch (\Exception $e2) {
                        return null;
                    }
                }
            };

            $query = SalesInvoice::whereNull('deleted_at')
                ->where(function($q) {
                    $q->whereNull('is_gst_filed')->orWhere('is_gst_filed', 0);
                });

            $fromDate = $parseDate($request->from_date);
            $toDate = $parseDate($request->to_date);

            if ($fromDate) $query->where('inv_date', '>=', $fromDate);
            if ($toDate) $query->where('inv_date', '<=', $toDate);
            if ($request->filled('customer_id')) $query->where('customer_id', $request->customer_id);
            if ($request->filled('agent_id')) $query->where('agent_id', $request->agent_id);

            if ($request->filled('invoice_status')) {
                $invStatus = trim((string)$request->invoice_status);
                if ($invStatus === 'Cancelled') {
                    $query->where(function($q) {
                        $q->where('invoice_status', 'Cancelled')
                          ->orWhereRaw('LOWER(einvoice_status) = ?', ['cancelled']);
                    });
                } else {
                    $query->where('invoice_status', $invStatus);
                }
            }

            if ($request->filled('einvoice_status')) {
                $einvoiceStatus = strtolower(trim((string)$request->einvoice_status));
                if ($einvoiceStatus === 'generated') {
                    $query->whereRaw('LOWER(einvoice_status) = ?', ['generated']);
                } elseif ($einvoiceStatus === 'not_generated') {
                    $query->where(function($q) {
                        $q->whereNull('einvoice_status')
                          ->orWhereRaw('LOWER(einvoice_status) != ?', ['generated']);
                    });
                }
            }

            if ($request->filled('search')) {
                $search = trim((string)$request->search);
                $query->where(function($q) use ($search) {
                    $q->where('inv_no', 'like', "%{$search}%");

                    if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                        $sNum = (int)$sm[1];
                        if ($sNum > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                            $dbNum = ($sNum >= 316) ? ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                            $q->orWhere('inv_no', 'like', "%CDW/{$dbNum}%");
                        }
                    } elseif (is_numeric(trim($search))) {
                        $numVal = (int)trim($search);
                        if ($numVal > \App\Models\SalesInvoice::CDW_CD_OFFSET) {
                            $cdwNum = ($numVal >= 316) ? ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - \App\Models\SalesInvoice::CDW_CD_OFFSET);
                            $q->orWhere('inv_no', 'like', "%CDW/{$cdwNum}/%");
                        }
                    } elseif (stripos($search, 'CD') !== false && stripos($search, 'CDW') === false) {
                        $q->orWhere('inv_no', 'like', "%CDW/%");
                    }

                    $q->orWhereHas('customer', function($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%")
                          ->orWhere('gst_no', 'like', "%{$search}%")
                          ->orWhereHas('place', function($pl) use ($search) {
                              $pl->where('place_name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('city', function($ct) use ($search) {
                              $ct->where('city_name', 'like', "%{$search}%");
                          });
                    });
                });
            }

            $invoices = $query->get();
        } else {
            $request->validate([
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'exists:sales_invoices,id',
            ]);
            $invoices = SalesInvoice::whereIn('id', $request->invoice_ids)->get();
        }

        $count = 0;
        foreach ($invoices as $inv) {
            if ($inv->is_gst_filed) {
                continue;
            }
            $oldData = $inv->only(['is_gst_filed', 'gst_filed_at', 'gst_filed_by']);
            $inv->update([
                'is_gst_filed' => 1,
                'gst_filed_at' => now(),
                'gst_filed_by' => $userId,
            ]);
            $newData = $inv->fresh()->only(['is_gst_filed', 'gst_filed_at', 'gst_filed_by']);
            if (function_exists('addLog')) {
                addLog('gst_filed', 'Sales GST Report', 'sales_invoices', $inv->id, $oldData, $newData, "Invoice {$inv->inv_no} marked as GST Filed by {$userName}");
            }
            $count++;
        }

        $msg = "{$count} Invoice(s) marked as GST Filed successfully.";

        return response()->json([
            'status' => 'success',
            'message' => $msg
        ]);
    }
}

