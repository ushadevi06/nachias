<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
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
        $length = intval($request->length > 0 ? $request->length : 10);
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

                    $dataQuery = clone $countQuery;
                    $orders = $dataQuery->with(['customer', 'items', 'salesInvoices.items'])
                        ->orderBy('id', 'desc')
                        ->offset($start)
                        ->limit($length)
                        ->get();

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

                        $data[] = [
                            'so_no' => '<strong>' . htmlspecialchars((string)($order->so_no ?? '-')) . '</strong>',
                            'so_date' => $order->so_date ? date('d-M-Y', strtotime((string)$order->so_date)) : '-',
                            'customer' => htmlspecialchars((string)(optional($order->customer)->name ?? '-')),
                            'item' => $itemsHtml,
                            'qty' => number_format($total_qty, 0),
                            'status' => '<span class="badge ' . $badgeClass . ' rounded-pill">' . $fulfillmentStatus . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data
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

                    $dataQuery = clone $countQuery;
                    $orders = $dataQuery->with(['customer', 'items'])
                        ->select('sales_orders.*')
                        ->selectSub($deliveredQtySubquery, 'delivered_qty')
                        ->orderBy('id', 'desc')
                        ->offset($start)
                        ->limit($length)
                        ->get();

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

                        $custName = htmlspecialchars((string)(optional($order->customer)->name ?? '-')) . ' (' . htmlspecialchars((string)(optional($order->customer)->code ?? '-')) . ')';

                        $data[] = [
                            'customer' => $custName,
                            'item' => $itemsHtml,
                            'ord_qty' => number_format($total_qty, 0),
                            'bal_qty' => '<span class="text-danger fw-bold">' . number_format($pending_qty, 0) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data
                    ]);

                case 'comparison-report':
                    $currentYear = (int)date('Y');
                    $prevYear = $currentYear - 1;

                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];

                    $currentYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')
                        ->whereYear('inv_date', $currentYear)
                        ->whereNull('deleted_at')
                        ->groupByRaw('month')
                        ->pluck('total', 'month')
                        ->toArray();

                    $prevYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')
                        ->whereYear('inv_date', $prevYear)
                        ->whereNull('deleted_at')
                        ->groupByRaw('month')
                        ->pluck('total', 'month')
                        ->toArray();

                    $comparisonData = [];
                    foreach ($months as $num => $name) {
                        $curr = (float)($currentYearSales[$num] ?? 0);
                        $prev = (float)($prevYearSales[$num] ?? 0);
                        $growth = $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);

                        $growthHtml = '-';
                        if ($growth > 0) {
                            $growthHtml = '<span class="text-success fw-bold"><i class="ti ti-arrow-up"></i> +' . number_format($growth, 1) . '%</span>';
                        } elseif ($growth < 0) {
                            $growthHtml = '<span class="text-danger fw-bold"><i class="ti ti-arrow-down"></i> ' . number_format($growth, 1) . '%</span>';
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
                        'data' => $comparisonData
                    ]);

                case 'outstanding-report':
                    $customers_with_invoices = SalesInvoice::with('customer.zone')
                        ->whereNull('deleted_at')
                        ->select('customer_id', DB::raw('SUM(grand_total) as total_invoiced'), DB::raw('SUM(received_amount) as total_received'), DB::raw('SUM(due_amount) as total_due'), DB::raw('COUNT(id) as bills_count'))
                        ->groupBy('customer_id')
                        ->having('total_due', '>', 0)
                        ->get();

                    $outstandingData = [];
                    foreach ($customers_with_invoices as $inv) {
                        $customer = $inv->customer;
                        if (!$customer) continue;

                        $outstandingData[] = [
                            'zone' => '<span class="badge bg-label-secondary">' . htmlspecialchars((string)(optional($customer->zone)->zone_name ?? 'Unassigned')) . '</span>',
                            'customer' => '<div class="fw-bold text-dark">' . htmlspecialchars((string)$customer->name) . '</div><small class="text-muted">' . htmlspecialchars((string)$customer->code) . '</small>',
                            'bills_count' => '<span class="badge rounded-pill bg-label-info">' . (int)$inv->bills_count . '</span>',
                            'total_sales' => '₹' . number_format((float)$inv->total_invoiced, 2),
                            'received' => '<span class="text-success">₹' . number_format((float)$inv->total_received, 2) . '</span>',
                            'outstanding' => '<span class="fw-bold text-danger">₹' . number_format((float)$inv->total_due, 2) . '</span>',
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => count($outstandingData),
                        'recordsFiltered' => count($outstandingData),
                        'data' => $outstandingData
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
                    $pagedIncentives = $length > 0 ? array_slice($incentiveReport, $start, $length) : $incentiveReport;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $pagedIncentives
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

                    $dataQuery = clone $countQuery;
                    $notes = $dataQuery->with(['customer', 'salesAgent', 'zone'])
                        ->orderBy('id', 'desc')
                        ->offset($start)
                        ->limit($length)
                        ->get();

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
                        'data' => $data
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

                    $dataQuery = clone $countQuery;
                    $orders = $dataQuery->with(['customer.city', 'items.item.brand', 'salesAgent', 'zone', 'salesInvoices.items'])
                        ->orderBy('id', 'desc')
                        ->offset($start)
                        ->limit($length)
                        ->get();

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
                        'data' => $data
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
