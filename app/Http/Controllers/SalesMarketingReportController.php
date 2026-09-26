<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\SalesAgent;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;

class SalesMarketingReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-marketing-report')) {
            return unauthorizedRedirect();
        }

        $customers = Customer::where('status', 'Active')->select('id', 'name', 'code')->orderBy('name', 'asc')->get();
        $executives = SalesAgent::where('status', 'Active')->select('id', 'name', 'code')->orderBy('name', 'asc')->get();

        $orders = collect();
        $incentiveReport = [];
        $comparisonReport = [];
        $outstandingReport = [];
        $creditNotes = collect();

        return view('reports/sales_marketing_report', compact('orders', 'customers', 'executives', 'incentiveReport', 'comparisonReport', 'outstandingReport', 'creditNotes'));
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

            switch ($type) {
                case 'order-report':
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

                case 'pending-report':
                    $deliveredQtySubquery = DB::table('sales_invoices as si')
                        ->join('sales_invoice_items as sii', 'sii.sales_invoice_id', '=', 'si.id')
                        ->whereColumn('si.so_id', 'sales_orders.id')
                        ->whereNull('si.deleted_at')
                        ->whereNull('sii.deleted_at')
                        ->where(function($q) {
                            $q->whereNull('si.einvoice_status')
                              ->orWhereRaw('LOWER(si.einvoice_status) != ?', ['cancelled']);
                        })
                        ->selectRaw('COALESCE(SUM(sii.quantity), 0)');

                    $countQuery = SalesOrder::whereNull('sales_orders.deleted_at')
                        ->whereRaw('(sales_orders.total_qty - COALESCE((' . $deliveredQtySubquery->toSql() . '), 0)) > 0');
                    $countQuery->mergeBindings($deliveredQtySubquery);

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
                    $allOrdersForTotals = (clone $countQuery)->select('sales_orders.id', 'sales_orders.total_qty')->selectSub($deliveredQtySubquery, 'delivered_qty')->get();
                    $totalOrdQty = $allOrdersForTotals->sum(function($o) { return (float)($o->total_qty ?? 0); });
                    $totalBalQty = $allOrdersForTotals->sum(function($o) {
                        $del = (float)($o->delivered_qty ?? 0);
                        $tot = (float)($o->total_qty ?? 0);
                        return max(0, $tot - $del);
                    });

                    $dataQuery = clone $countQuery;
                    $ordersQuery = $dataQuery->with(['customer', 'items.stockEntryItem'])
                        ->select('sales_orders.*')
                        ->selectSub($deliveredQtySubquery, 'delivered_qty')
                        ->orderBy('id', 'desc');
                    if ($length !== -1) {
                        $ordersQuery->offset($start)->limit($length);
                    }
                    $orders = $ordersQuery->get();

                    $data = [];
                    foreach ($orders as $order) {
                        $delivered_qty = (float)($order->delivered_qty ?? 0);
                        $total_qty = (float)($order->total_qty ?? 0);
                        $pending_qty = max(0, $total_qty - $delivered_qty);

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

                        $soNoRaw = (string)($order->so_no ?? '-');
                        $custRaw = (string)(optional($order->customer)->name ?? '-') . ' (' . (string)(optional($order->customer)->code ?? '-') . ')';
                        $soNoStr = htmlspecialchars($soNoRaw);
                        $custName = htmlspecialchars($custRaw);

                        $data[] = [
                            'order_id' => $order->id,
                            'so_no' => '<span class="text-primary fw-bold">' . $soNoStr . ' <i class="ri ri-arrow-right-s-line ms-1"></i></span>',
                            'customer' => $custName,
                            'ord_qty' => number_format($total_qty, 0),
                            'bal_qty' => '<span class="text-danger fw-bold">' . number_format($pending_qty, 0) . '</span>',
                            'so_no_raw' => $soNoRaw,
                            'customer_raw' => $custRaw,
                            'ord_qty_raw' => number_format($total_qty, 0),
                            'bal_qty_raw' => number_format($pending_qty, 0),
                            'items_data' => $itemsData ?? []
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data,
                        'totals' => [
                            'ord_qty' => number_format($totalOrdQty, 0),
                            'bal_qty' => number_format($totalBalQty, 0)
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

                    // Ordering
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
                    $currentYear = (int)date('Y');
                    $prevYear = $currentYear - 1;

                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];

                    $currentYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')->whereYear('inv_date', $currentYear)->whereNull('deleted_at')->groupByRaw('month')->pluck('total', 'month')->toArray();

                    $prevYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')->whereYear('inv_date', $prevYear)->whereNull('deleted_at')->groupByRaw('month')->pluck('total', 'month')->toArray();

                    $sumPrevSales = array_sum($prevYearSales);
                    $sumCurrSales = array_sum($currentYearSales);
                    $totGrowth = $sumPrevSales > 0 ? (($sumCurrSales - $sumPrevSales) / $sumPrevSales) * 100 : ($sumCurrSales > 0 ? 100 : 0);
                    $totGrowthDisplay = ($totGrowth > 0 ? '+' : '') . number_format($totGrowth, 1) . '%';

                    $comparisonData = [];
                    foreach ($months as $num => $name) {
                        $curr = (float)($currentYearSales[$num] ?? 0);
                        $prev = (float)($prevYearSales[$num] ?? 0);
                        $growth = $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);

                        $growthHtml = '-';
                        if ($growth > 0) {
                            $growthHtml = '<span class="text-success fw-bold"><i class="ri ri-arrow-up"></i> +' . number_format($growth, 1) . '%</span>';
                        } elseif ($growth < 0) {
                            $growthHtml = '<span class="text-danger fw-bold"><i class="ri ri-arrow-down"></i> ' . number_format($growth, 1) . '%</span>';
                        } else {
                            $growthHtml = '<span class="text-muted small">0%</span>';
                        }

                        $comparisonData[] = [
                            'month_name' => '<strong>' . $name . '</strong>',
                            'prev_year_sales' => '₹' . number_format($prev, 2),
                            'curr_year_sales' => '<span class="fw-bold text-primary">₹' . number_format($curr, 2) . '</span>',
                            'growth_pc' => $growthHtml,
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => count($comparisonData),
                        'recordsFiltered' => count($comparisonData),
                        'data' => $comparisonData,
                        'totals' => [
                            'prev_year_sales' => '₹' . number_format($sumPrevSales, 2),
                            'curr_year_sales' => '₹' . number_format($sumCurrSales, 2),
                            'growth_pc' => $totGrowthDisplay
                        ]
                    ]);

                case 'sales-gst-report':
                case 'sales-report':
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

                    $invoicesQuery = $countQuery->with(['customer.place', 'customer.city', 'items']);
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

                        $invNoStr = htmlspecialchars((string)($invoice->inv_no ?? '-'));
                        $invViewUrl = url('sales_invoices/view/' . $invoice->id);
                        $invNoHtml = '<a href="' . $invViewUrl . '" target="_blank" class="fw-bold text-primary">' . $invNoStr . '</a>';

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

                        $roundOffVal = (float)($invoice->round_off ?? 0);
                        if (in_array(strtolower((string)$invoice->round_off_type), ['less', 'subtract', '-'])) {
                            $roundOffVal = -$roundOffVal;
                        }
                        $roundOffDisplay = ($roundOffVal != 0) ? (($roundOffVal < 0 ? '-' : '') . '₹' . number_format(abs($roundOffVal), 2)) : '-';

                        $data[] = [
                            'sno' => $sno++,
                            'inv_no' => $invNoHtml,
                            'inv_no_raw' => $invNoStr,
                            'inv_date' => $invDateStr,
                            'customer_name' => $custName,
                            'gst_no' => $gstNo,
                            'place' => $place,
                            'qty' => number_format($itemQty),
                            'sub_total' => '₹' . number_format($subTotal, 2),
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
                        ];
                    }

                    $totalRoundOffDisplay = ($totalRoundOff != 0) ? (($totalRoundOff < 0 ? '-' : '') . '₹' . number_format(abs($totalRoundOff), 2)) : '₹0.00';

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $unfilteredRecords,
                        'recordsFiltered' => $filteredRecords,
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
                    $invoicesQuery = $countQuery->with(['customer', 'salesOrder'])->orderBy('id', 'desc');
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
                        
                        $tickHtml = '<span class="text-success fw-bold fs-4" title="✔"><i class="ri ri-check-line"></i><span class="export-symbol" style="display:none;">✔</span></span>';
                        $crossHtml = '<span class="text-danger fw-bold fs-4" title="✘"><i class="ri ri-close-line"></i><span class="export-symbol" style="display:none;">✘</span></span>';
                        
                        $invNoStr = htmlspecialchars((string)($invoice->inv_no ?? '-'));
                        $custStr = htmlspecialchars((string)(optional($invoice->customer)->name ?? '-'));
                        $invDateStr = $invoice->inv_date ? date('d-M-Y', strtotime((string)$invoice->inv_date)) : '-';

                        $data[] = [
                            'inv_no' => '<span class="text-primary fw-bold">' . $invNoStr . '</span>',
                            'inv_date' => $invDateStr,
                            'customer' => $custStr,
                            'einvoice' => $einvoice_generated ? $tickHtml : $crossHtml,
                            'ewaybill' => $ewaybill_generated ? $tickHtml : $crossHtml,
                            'dispatch' => $dispatched ? $tickHtml : $crossHtml,
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

                    $ordersQuery = SalesOrder::where('status', 'Approved')->whereNull('deleted_at');
                    if ($fromDate) $ordersQuery->where('so_date', '>=', $fromDate);
                    if ($toDate) $ordersQuery->where('so_date', '<=', $toDate);
                    if ($agentId) $ordersQuery->where('agent_id', $agentId);

                    $orders = $ordersQuery->get();

                    $incentiveReport = [];
                    $sumTotalSales = 0;
                    $sumTotalCommission = 0;

                    foreach ($agents as $agent) {
                        if ($agentId && $agent->id != $agentId) continue;

                        $zoneName = $cityIdToZone[$agent->city_id ?? ''] ?? 'Unassigned';
                        $agentOrders = $orders->where('agent_id', $agent->id);
                        $totalSales = (float)$agentOrders->sum('total_amount');
                        $totalCommission = (float)$agentOrders->sum('commission_amount');

                        if ($totalSales > 0 || $totalCommission > 0) {
                            if ($search) {
                                if (stripos((string)$zoneName, $search) === false && stripos((string)$agent->name, $search) === false) {
                                    continue;
                                }
                            }
                            $sumTotalSales += $totalSales;
                            $sumTotalCommission += $totalCommission;

                            $incentiveReport[] = [
                                'zone' => '<span class="fw-bold text-primary">' . htmlspecialchars((string)$zoneName) . '</span>',
                                'agent' => htmlspecialchars((string)$agent->name) . ' <small class="text-muted">(' . htmlspecialchars((string)$agent->code) . ')</small>',
                                'total_sales' => '₹' . number_format($totalSales, 2),
                                'incentive_pc' => '<span class="badge bg-label-info rounded-pill">' . number_format($totalSales > 0 ? ($totalCommission / $totalSales) * 100 : 0, 2) . '%</span>',
                                'incentive_amt' => '<span class="text-success fw-bold">₹' . number_format($totalCommission, 2) . '</span>',
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
                        'totals' => [
                            'total_sales' => '₹' . number_format($sumTotalSales, 2),
                            'incentive_amt' => '₹' . number_format($sumTotalCommission, 2)
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
                            'pending_qty' => '<span class="text-danger fw-bold">' . number_format($pending_qty, 0) . '</span>',
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
                        ]
                    ]);

                case 'outstanding-report':
                    $query = \App\Models\Customer::where('status', 'Active');
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

                    $allCustomers = $query->with(['zone', 'salesInvoices' => function($invQ) use ($fromDate, $toDate) {
                        $invQ->whereNull('deleted_at');
                        if ($fromDate) $invQ->where('inv_date', '>=', $fromDate);
                        if ($toDate) $invQ->where('inv_date', '<=', $toDate);
                    }])->get();

                    $outstandingReport = [];
                    $sumBills = 0;
                    $sumSales = 0;
                    $sumReceived = 0;
                    $sumOutstanding = 0;

                    foreach ($allCustomers as $cust) {
                        $invoices = $cust->salesInvoices;
                        if ($invoices->count() > 0) {
                            $totalSales = (float)$invoices->sum('grand_total');
                            $received = (float)$invoices->sum('received_amount');
                            $outstanding = max(0, $totalSales - $received);

                            if ($outstanding > 0 || $totalSales > 0) {
                                $sumBills += $invoices->count();
                                $sumSales += $totalSales;
                                $sumReceived += $received;
                                $sumOutstanding += $outstanding;

                                $outstandingReport[] = [
                                    'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars((string)($cust->zone->zone_name ?? '-')) . '</span>',
                                    'customer' => '<div class="fw-bold text-dark">' . htmlspecialchars((string)$cust->name) . '</div><small class="text-muted">' . htmlspecialchars((string)$cust->code) . '</small>',
                                    'bills_count' => '<span class="badge rounded-pill bg-label-info">' . $invoices->count() . '</span>',
                                    'total_sales' => '₹' . number_format($totalSales, 2),
                                    'received' => '<span class="text-success">₹' . number_format($received, 2) . '</span>',
                                    'outstanding' => '<span class="fw-bold text-danger">₹' . number_format($outstanding, 2) . '</span>',
                                ];
                            }
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
                            'total_sales' => '₹' . number_format($sumSales, 2),
                            'received' => '₹' . number_format($sumReceived, 2),
                            'outstanding' => '₹' . number_format($sumOutstanding, 2),
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
}
