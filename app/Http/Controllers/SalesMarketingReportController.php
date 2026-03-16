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
        $query = SalesOrder::with(['customer', 'items.item'])->whereNull('deleted_at');

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
        if ($request->zone_id) {
            $query->where('zone_id', $request->zone_id);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        foreach ($orders as $order) {
            $delivered_qty = DB::table('sales_invoice_items')->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')->where('sales_invoices.so_id', $order->id)->whereNull('sales_invoices.deleted_at')->whereNull('sales_invoice_items.deleted_at')->sum('sales_invoice_items.quantity');

            $order->delivered_qty = $delivered_qty;
            $order->pending_qty = max(0, $order->total_qty - $delivered_qty);

            if ($delivered_qty <= 0) {
                $order->fulfillment_status = 'Planned';
            }
            elseif ($order->pending_qty > 0) {
                $order->fulfillment_status = 'Processing';
            }
            else {
                $order->fulfillment_status = 'Completed';
            }
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

        // Sort by Zone then Customer
        usort($outstandingReport, function($a, $b) {
            if ($a['zone'] == $b['zone']) return strcmp($a['customer_name'], $b['customer_name']);
            return strcmp($a['zone'], $b['zone']);
        });

        return view('reports/sales_marketing_report', compact('orders', 'customers', 'executives', 'incentiveReport', 'comparisonReport', 'outstandingReport', 'zones'));
    }
}
