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
        $query = SalesOrder::with(['customer.place', 'customer.city', 'items.stockEntryItem', 'items.item.brand', 'salesAgent', 'zone', 'salesInvoices.items'])->whereNull('deleted_at');

        if ($request->from_date) {
            $query->where('so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query->where('so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }


        $orders = $query->orderBy('id', 'desc')->get();

        foreach ($orders as $order) {
            $validInvoices = $order->salesInvoices->filter(function($inv) {
                return empty($inv->einvoice_status) || strtolower($inv->einvoice_status) !== 'cancelled';
            })->sortBy('inv_date');
            
            $delivered_qty = 0;
            foreach ($validInvoices as $inv) {
                $delivered_qty += $inv->items->sum('quantity');
            }

            // Calculate brand categories
            $dhoti_qty = 0; $white_qty = 0; $core_qty = 0; $bravo_qty = 0; $deal_qty = 0; $formal_qty = 0;
            foreach ($order->items as $item) {
                $brandStr = '';
                if ($item->item && $item->item->brand) {
                    $brandStr .= strtoupper($item->item->brand->brand_name) . ' ';
                }
                $brandStr .= strtoupper($item->categories_path_val ?? '') . ' ';
                $brandStr .= strtoupper($item->category_name ?? '');

                if (strpos($brandStr, 'DHOTI') !== false) $dhoti_qty += $item->qty;
                if (strpos($brandStr, 'WHITE') !== false) $white_qty += $item->qty;
                if (strpos($brandStr, 'CORE') !== false) $core_qty += $item->qty;
                if (strpos($brandStr, 'BRAVO') !== false) $bravo_qty += $item->qty;
                if (strpos($brandStr, 'DEAL') !== false) $deal_qty += $item->qty;
                if (strpos($brandStr, 'FORMAL') !== false && strpos($brandStr, 'CORE') === false) {
                    $formal_qty += $item->qty;
                }
            }

            $order->dhoti_qty = $dhoti_qty;
            $order->white_qty = $white_qty;
            $order->core_qty = $core_qty;
            $order->bravo_qty = $bravo_qty;
            $order->deal_qty = $deal_qty;
            $order->formal_qty = $formal_qty;

            $order->delivered_qty = $delivered_qty;
            $order->pending_qty = max(0, $order->total_qty - $delivered_qty);

            $order->partial_d_date = null;
            $order->despatch_complete_date = null;
            
            if ($validInvoices->count() > 0) {
                $latestInvoice = $validInvoices->sortByDesc(function($inv) {
                    return $inv->dispatch_completed_at ?? $inv->inv_date;
                })->first();

                $latestDate = $latestInvoice->dispatch_completed_at ? $latestInvoice->dispatch_completed_at->format('d-m-Y') : ($latestInvoice->inv_date ? $latestInvoice->inv_date->format('d-m-Y') : null);
                
                $order->partial_d_date = $latestDate;
                
                if ($order->delivered_qty >= $order->total_qty) {
                    $order->despatch_complete_date = $latestDate;
                }
            }

            if ($order->delivered_qty >= $order->total_qty) {
                $order->fulfillment_status = 'Delivered';
            }
            elseif ($order->delivered_qty > 0) {
                $order->fulfillment_status = 'Partial Delivery';
            }
            else {
                $order->fulfillment_status = 'Planned';
            }
            $order->delay_reason = $order->reason_for_delay;
        }

        $customers = Customer::where('status', 'Active')->get();
        $executives = SalesAgent::where('status', 'Active')->get();

        $zones = Zone::where('status', 'active')->get();
        $cityIdToZone = [];
        foreach ($zones as $zone) {
            $ids = explode(',', $zone->city_ids);
            foreach ($ids as $id) {
                if ($id)
                    $cityIdToZone[trim($id)] = $zone->zone_name;
            }
        }

        $incentiveReport = [];
        $agents = SalesAgent::where('status', 'Active')->get();

        foreach ($agents as $agent) {
            $zoneName = $cityIdToZone[$agent->city_id] ?? 'Unassigned';

            $agentOrders = $orders->where('agent_id', $agent->id)->where('status', 'Approved');
            $totalSales = $agentOrders->sum('total_amount');
            $totalCommission = $agentOrders->sum('commission_amount');

            if ($totalSales > 0 || $totalCommission > 0) {
                $incentiveReport[] = [
                    'zone' => $zoneName,
                    'agent_name' => $agent->name,
                    'agent_code' => $agent->code,
                    'total_sales' => $totalSales,
                    'incentive_pc' => $totalSales > 0 ? ($totalCommission / $totalSales) * 100 : 0,
                    'incentive_amt' => $totalCommission
                ];
            }
        }

        usort($incentiveReport, function ($a, $b) {
            if ($a['zone'] == $b['zone'])
                return strcmp($a['agent_name'], $b['agent_name']);
            return strcmp($a['zone'], $b['zone']);
        });

        // Sales Comparison (Month & Year) Logic
        $currentYear = 2026;
        $prevYear = 2025;

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $currentYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')
            ->whereYear('inv_date', $currentYear)
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $prevYearSales = SalesInvoice::selectRaw('MONTH(inv_date) as month, SUM(grand_total) as total')
            ->whereYear('inv_date', $prevYear)
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $comparisonReport = [];
        foreach ($months as $num => $name) {
            $curr = $currentYearSales[$num] ?? 0;
            $prev = $prevYearSales[$num] ?? 0;
            $growth = $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);

            $comparisonReport[] = [
                'month_name' => $name,
                'curr_year_sales' => $curr,
                'prev_year_sales' => $prev,
                'growth_pc' => $growth,
                'curr_year' => $currentYear,
                'prev_year' => $prevYear
            ];
        }

        // Zone Wise Outstanding Logic
        $outstandingReport = [];
        $customers_with_invoices = SalesInvoice::with('customer.zone')
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('customer_id');

        foreach ($customers_with_invoices as $custId => $invs) {
            $customer = $invs->first()->customer;
            if (!$customer) continue;

            $totalInvoiced = $invs->sum('grand_total');
            $totalReceived = $invs->sum('received_amount');
            $totalDue = $invs->sum('due_amount');

            if ($totalDue > 0) {
                $outstandingReport[] = [
                    'zone' => $customer->zone->zone_name ?? 'Unassigned',
                    'customer_name' => $customer->name,
                    'customer_code' => $customer->code,
                    'total_sales' => $totalInvoiced,
                    'received' => $totalReceived,
                    'outstanding' => $totalDue,
                    'bills_count' => $invs->where('due_amount', '>', 0)->count()
                ];
            }
        }

        usort($outstandingReport, function($a, $b) {
            if ($a['zone'] == $b['zone']) return strcmp($a['customer_name'], $b['customer_name']);
            return strcmp($a['zone'], $b['zone']);
        });

        // Credit Note Report Logic
        $creditNotesQuery = \App\Models\CreditNote::with(['customer', 'salesAgent', 'zone'])->whereNull('deleted_at');

        if ($request->from_date) {
            $creditNotesQuery->where('note_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $creditNotesQuery->where('note_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        if ($request->customer_id) {
            $creditNotesQuery->where('customer_id', $request->customer_id);
        }
        if ($request->agent_id) {
            $creditNotesQuery->where('agent_id', $request->agent_id);
        }

        $creditNotes = $creditNotesQuery->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return response()->json([
                'order-report' => view('reports.sales_marketing_reports._order_report', compact('orders'))->render(),
                'pending-report' => view('reports.sales_marketing_reports._pending_report', compact('orders'))->render(),
                'incentive-report' => view('reports.sales_marketing_reports._incentive_report', compact('incentiveReport'))->render(),
                'comparison-report' => view('reports.sales_marketing_reports._comparison_report', compact('comparisonReport'))->render(),
                'outstanding-report' => view('reports.sales_marketing_reports._outstanding_report', compact('outstandingReport'))->render(),
                'tracker-report' => view('reports.sales_marketing_reports._tracker_report')->render(),
                'location-report' => view('reports.sales_marketing_reports._location_report')->render(),
                'trip-report' => view('reports.sales_marketing_reports._trip_report')->render(),
                'expense-report' => view('reports.sales_marketing_reports._expense_report')->render(),
                'swatch-report' => view('reports.sales_marketing_reports._swatch_report')->render(),
                'complaint-report' => view('reports.sales_marketing_reports._complaint_report')->render(),
                'credit-note-report' => view('reports.sales_marketing_reports._credit_note_report', compact('creditNotes'))->render(),
                'despatch-report' => view('reports.sales_marketing_reports._despatch_tracking_report', compact('orders'))->render(),
            ]);
        }

        return view('reports/sales_marketing_report', compact('orders', 'customers', 'executives', 'incentiveReport', 'comparisonReport', 'outstandingReport', 'creditNotes'));
    }
}
