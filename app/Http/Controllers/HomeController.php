<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\StockEntryItem;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\PurchaseInvoice;
use App\Models\JobCardIssueItem;
use App\Models\JobCardEntry;
use App\Models\ProductionService;
use App\Models\Ticket;
use App\Models\DocumentRepository;
use App\Models\ProcessSchedule;
use App\Models\JobCardOperation;
use App\Models\Attendance;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        /*  Sales & Order Dashboard */
        $sales_today = SalesInvoice::whereDate('inv_date', $today)->whereNull('deleted_at')->sum('grand_total');
        $sales_month = SalesInvoice::whereBetween('inv_date', [$monthStart, Carbon::now()])->whereNull('deleted_at')->sum('grand_total');
        $sales_year = SalesInvoice::whereBetween('inv_date', [$yearStart, Carbon::now()])->whereNull('deleted_at')->sum('grand_total');

        $sales_count_today = SalesInvoice::whereDate('inv_date', $today)->whereNull('deleted_at')->count();
        $sales_count_month = SalesInvoice::whereBetween('inv_date', [$monthStart, Carbon::now()])->whereNull('deleted_at')->count();
        $sales_count_year = SalesInvoice::whereBetween('inv_date', [$yearStart, Carbon::now()])->whereNull('deleted_at')->count();

        $orders_today = SalesOrder::whereDate('so_date', $today)->whereNull('deleted_at')->count();
        $orders_month = SalesOrder::whereBetween('so_date', [$monthStart, Carbon::now()])->whereNull('deleted_at')->count();
        $total_stock = StockEntryItem::where('stock_type', 'finished_goods')->whereNull('deleted_at')->sum(DB::raw('qty_in - COALESCE(qty_out, 0)'));
        $urgent_orders = SalesOrder::where('status', 'Pending')->whereNull('deleted_at')->count();

        /* Sales Report Matrix (Sales, Sales Return, Net Sales) */
        // Today Sales
        $today_sales_qty = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereDate('sales_invoices.inv_date', $today)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->sum('sales_invoice_items.quantity');

        $today_sales_wot = DB::table('sales_invoices')
            ->whereDate('inv_date', $today)
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        // Today Sales Return (Credit Notes)
        $today_return_qty = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
            ->whereDate('credit_notes.note_date', $today)
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->sum('credit_note_items.quantity');

        $today_return_wot = DB::table('credit_notes')
            ->whereDate('note_date', $today)
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        $today_net_qty = max(0, $today_sales_qty - $today_return_qty);
        $today_net_wot = max(0, $today_sales_wot - $today_return_wot);

        // Monthly Sales
        $month_sales_qty = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereBetween('sales_invoices.inv_date', [$monthStart, Carbon::now()])
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->sum('sales_invoice_items.quantity');

        $month_sales_wot = DB::table('sales_invoices')
            ->whereBetween('inv_date', [$monthStart, Carbon::now()])
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        // Monthly Sales Return
        $month_return_qty = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
            ->whereBetween('credit_notes.note_date', [$monthStart, Carbon::now()])
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->sum('credit_note_items.quantity');

        $month_return_wot = DB::table('credit_notes')
            ->whereBetween('note_date', [$monthStart, Carbon::now()])
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        $month_net_qty = max(0, $month_sales_qty - $month_return_qty);
        $month_net_wot = max(0, $month_sales_wot - $month_return_wot);

        // Yearly Sales
        $year_sales_qty = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereBetween('sales_invoices.inv_date', [$yearStart, Carbon::now()])
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->sum('sales_invoice_items.quantity');

        $year_sales_wot = DB::table('sales_invoices')
            ->whereBetween('inv_date', [$yearStart, Carbon::now()])
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        // Yearly Sales Return
        $year_return_qty = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
            ->whereBetween('credit_notes.note_date', [$yearStart, Carbon::now()])
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->sum('credit_note_items.quantity');

        $year_return_wot = DB::table('credit_notes')
            ->whereBetween('note_date', [$yearStart, Carbon::now()])
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(sub_total, grand_total)'));

        $year_net_qty = max(0, $year_sales_qty - $year_return_qty);
        $year_net_wot = max(0, $year_sales_wot - $year_return_wot);

        /* Missed Sales (Lost Sales Revenue) Computation */
        $soTotals = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_order_items.deleted_at')
            ->select(
                'sales_orders.id as so_id',
                'sales_orders.so_date',
                DB::raw('SUM(sales_order_items.qty) as ordered_qty'),
                DB::raw('SUM(sales_order_items.amount) as ordered_value')
            )
            ->groupBy('sales_orders.id', 'sales_orders.so_date')
            ->get();

        $soInvoiceTotals = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select(
                'sales_invoices.so_id',
                DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty'),
                DB::raw('SUM(sales_invoice_items.amount) as invoiced_value')
            )
            ->groupBy('sales_invoices.so_id')
            ->get();

        $soInvoiceQtyTotals = $soInvoiceTotals->pluck('invoiced_qty', 'so_id');
        $soInvoiceValTotals = $soInvoiceTotals->pluck('invoiced_value', 'so_id');

        $today_missed_qty = 0; $today_missed_value = 0;
        $month_missed_qty = 0; $month_missed_value = 0;
        $year_missed_qty = 0; $year_missed_value = 0;

        foreach ($soTotals as $so) {
            $invQty = floatval($soInvoiceQtyTotals[$so->so_id] ?? 0);
            $invVal = floatval($soInvoiceValTotals[$so->so_id] ?? 0);

            $ordQty = floatval($so->ordered_qty ?? 0);
            $ordVal = floatval($so->ordered_value ?? 0);

            $lostQty = max(0, $ordQty - $invQty);
            $lostVal = max(0, $ordVal - $invVal);

            if ($lostQty > 0 || $ordQty > $invQty) {
                $soDate = Carbon::parse($so->so_date);

                if ($soDate->isToday()) {
                    $today_missed_qty += $lostQty;
                    $today_missed_value += $lostVal;
                }
                if ($soDate->gte($monthStart)) {
                    $month_missed_qty += $lostQty;
                    $month_missed_value += $lostVal;
                }
                if ($soDate->gte($yearStart)) {
                    $year_missed_qty += $lostQty;
                    $year_missed_value += $lostVal;
                }
            }
        }

        /*  Employee Attendance Dashboard */
        $total_emp = User::where('status', 'Active')->where('id', '!=', 1)->count();
        $present_emp_today = Attendance::whereDate('date', $today)->where('status', 'Present')->count();
        $absent_emp_today = Attendance::whereDate('date', $today)->where('status', 'Absent')->count();
        $late_emp_today = Attendance::whereDate('date', $today)->where('status', 'Late')->count();
        $overtime_today = Attendance::whereDate('date', $today)->where('status', 'Overtime')->count();

        $device_stats = Attendance::whereDate('date', $today)
            ->select('device_serial_number', 'status', DB::raw('count(*) as count'))
            ->groupBy('device_serial_number', 'status')
            ->get();

        $attendance_chart_data = [];
        $attendance_chart_data['All'] = [
            'Present' => $present_emp_today,
            'Absent' => $absent_emp_today,
            'Late' => $late_emp_today,
            'Overtime' => $overtime_today
        ];

        $dbDevices = DB::table('devices')->select('device_name', 'serial_number')->get();
        $deviceMap = [];
        foreach ($dbDevices as $d) {
            $dName = $d->device_name ?: $d->serial_number;
            $deviceMap[$d->serial_number] = $dName;
            $attendance_chart_data[$dName] = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Overtime' => 0];
        }

        foreach ($device_stats as $stat) {
            $sn = $stat->device_serial_number;
            $status = $stat->status;
            if (isset($deviceMap[$sn])) {
                $dName = $deviceMap[$sn];
                if (isset($attendance_chart_data[$dName][$status])) {
                    $attendance_chart_data[$dName][$status] = $stat->count;
                }
            }
        }
        /* Accounts & Financial Dashboard */
        $total_sales_value = SalesInvoice::whereNull('deleted_at')->sum('grand_total');
        $sales_return = CreditNote::whereNull('deleted_at')->sum('grand_total');
        $bill_discount = SalesInvoice::whereNull('deleted_at')->sum('discount');
        $total_sub_total = SalesInvoice::whereNull('deleted_at')->sum('sub_total');
        $bill_discount_percent = $total_sub_total > 0 ? round(($bill_discount / $total_sub_total) * 100, 2) : 0;
        $cash_discount = SalesInvoice::whereNull('deleted_at')->where('payment_mode', 'Cash')->sum('discount');
        $cash_sub_total = SalesInvoice::whereNull('deleted_at')->where('payment_mode', 'Cash')->sum('sub_total');
        $cash_discount_percent = $cash_sub_total > 0 ? round(($cash_discount / $cash_sub_total) * 100, 2) : 0;

        $total_customer_collections = Payment::where('payment_type', 'Customer Collection')
            ->whereNull('deleted_at')
            ->sum('amount');
        $total_debtors = max(0, $total_sales_value - $total_customer_collections);

        $total_purchase = PurchaseInvoice::whereNull('deleted_at')->sum('grand_total');
        $purchase_return = DebitNote::whereNull('deleted_at')->sum('grand_total');

        $total_supplier_payments = Payment::where('payment_type', 'Supplier Payment')
            ->whereNull('deleted_at')
            ->sum('amount');
        $total_creditors = max(0, $total_purchase - $total_supplier_payments);
        
        /* Debtors Outstanding & Aging Report */
        $debtors_aging = DB::table('sales_invoices')
            ->join('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('zones', 'customers.zone_id', '=', 'zones.id')
            ->leftJoinSub(
                DB::table('payments')
                    ->whereNull('deleted_at')
                    ->where('payment_type', 'Customer Collection')
                    ->select('reference_id', DB::raw('SUM(amount) as paid_amount'))
                    ->groupBy('reference_id'),
                'p',
                'p.reference_id', '=', 'sales_invoices.id'
            )
            ->leftJoin('sales_orders as so_direct', 'so_direct.id', '=', 'sales_invoices.so_id')
            ->leftJoin('sales_orders as so_json', function($join) {
                $join->whereRaw('JSON_LENGTH(sales_invoices.so_ids) > 0')
                    ->whereRaw('so_json.id = JSON_UNQUOTE(JSON_EXTRACT(sales_invoices.so_ids, "$[0]"))');
            })
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('customers.deleted_at')
            ->whereIn('sales_invoices.invoice_status', ['Unpaid/Credit', 'Partially Paid'])
            ->whereRaw('(sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) > 0')
            ->where(function($query) {
                $query->whereNotNull('sales_invoices.so_id')
                    ->orWhereRaw('JSON_LENGTH(sales_invoices.so_ids) > 0');
            })
            ->select(
                DB::raw('COALESCE(zones.zone_name, "No Zone") as zone_name'),
                DB::raw('SUM(sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) as total_due'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) <= 30 
                            THEN (sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) 
                            ELSE 0 END) as bucket_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) BETWEEN 31 AND 60 
                            THEN (sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) 
                            ELSE 0 END) as bucket_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) BETWEEN 61 AND 90 
                            THEN (sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) 
                            ELSE 0 END) as bucket_90'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) > 90 
                            THEN (sales_invoices.grand_total - COALESCE(p.paid_amount, 0)) 
                            ELSE 0 END) as bucket_above_90')
            )
            ->groupBy('zones.id', 'zones.zone_name')
            ->get();

        /* Creditors Outstanding & Aging Report */
        $creditors_aging = DB::table('purchase_invoices')
            ->join('suppliers', 'purchase_invoices.supplier_id', '=', 'suppliers.id')
            ->leftJoinSub(
                DB::table('payments')
                    ->whereNull('deleted_at')
                    ->where('payment_type', 'Supplier Payment')
                    ->select('reference_id', DB::raw('SUM(amount) as paid_amount'))
                    ->groupBy('reference_id'),
                'p',
                'p.reference_id', '=', 'purchase_invoices.id'
            )
            ->whereNull('purchase_invoices.deleted_at')
            ->whereNull('suppliers.deleted_at')
            ->whereRaw('(purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) > 0')
            ->select(
                'suppliers.name as supplier_name',
                DB::raw('SUM(purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) as total_due'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) <= 30 THEN (purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as bucket_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) BETWEEN 31 AND 60 THEN (purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as bucket_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) BETWEEN 61 AND 90 THEN (purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as bucket_90'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) > 90 THEN (purchase_invoices.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as bucket_above_90')
            )->groupBy('suppliers.id', 'suppliers.name')->get();

        $collection_performance = $total_sales_value > 0 ? round((($total_sales_value - $total_debtors) / $total_sales_value) * 100) : 0;

        /* Item-wise Stock Value */
        $fabric_value = StockEntryItem::where('store_category_id', 1)->whereNull('deleted_at')->sum(DB::raw('(qty_in - COALESCE(qty_out, 0)) * price'));

        $accessories_value = StockEntryItem::where('store_category_id', 2)->whereNull('deleted_at')->sum(DB::raw('(qty_in - COALESCE(qty_out, 0)) * price'));

        $activeJobCardIds = JobCardEntry::whereNotIn('status', ['Production Completed', 'Closed'])->pluck('id');
        $wip_material_cost = JobCardIssueItem::whereIn('job_card_entry_id', $activeJobCardIds)->join('stock_entry_items', 'job_card_issue_items.stock_entry_item_id', '=', 'stock_entry_items.id')->whereNull('job_card_issue_items.deleted_at')->sum(DB::raw('job_card_issue_items.qty_issue * stock_entry_items.price'));
        $wip_process_cost = JobCardOperation::whereIn('job_card_entry_id', $activeJobCardIds)->sum('total_cost');
        $wip_value = $wip_material_cost + $wip_process_cost;

        $wip_cost_breakdown = JobCardIssueItem::whereIn('job_card_entry_id', $activeJobCardIds)
            ->join('job_card_entries', 'job_card_issue_items.job_card_entry_id', '=', 'job_card_entries.id')
            ->join('stock_entry_items', 'job_card_issue_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->whereNull('job_card_issue_items.deleted_at')
            ->select('job_card_entries.job_card_no', DB::raw('SUM(job_card_issue_items.qty_issue * stock_entry_items.price) as total_cost'))
            ->groupBy('job_card_entries.id', 'job_card_entries.job_card_no')
            ->get();

        $finished_goods_value = StockEntryItem::where('stock_type', 'finished_goods')->whereNull('deleted_at')->sum(DB::raw('(qty_in - COALESCE(qty_out, 0)) * price'));

        $maintenance_raised = Ticket::count();
        $maintenance_attended = Ticket::whereNotNull('resolved_date')->count();
        $maintenance_pending = Ticket::where('status', 'Active')->whereNull('resolved_date')->count();

        $expiring_documents = DocumentRepository::where('validity_date', '>=', Carbon::now())->where('validity_date', '<=', Carbon::now()->addDays(90))->whereNull('deleted_at')->get();

        $months_labels = [];
        $sales_chart_data = [];
        $collection_chart_data = [];
        $purchase_chart_data = [];
        $payment_chart_data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->startOfMonth()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            $months_labels[] = $date->format('M');

            $sales_chart_data[] = round(SalesInvoice::whereBetween('inv_date', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('grand_total'), 2);
            $collection_chart_data[] = round(SalesInvoice::whereBetween('inv_date', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('received_amount'), 2);

            $purchase_chart_data[] = round(PurchaseInvoice::whereBetween('invoice_date', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('grand_total'), 2);
            $payment_chart_data[] = round(PurchaseInvoice::whereBetween('invoice_date', [$monthStart, $monthEnd])->whereNull('deleted_at')->sum('received_amount'), 2);
        }

        $wipQuery = JobCardEntry::with([
            'processGroup',
            'tasks.assignee.serviceProvider'
        ])->where('grand_total_qty', '>', 0)
            ->whereNotIn('status', ['Production Completed', 'Closed']);

        $jobCards = $wipQuery->get();
        $groupedRows = [];
        
        $userSpId = auth()->user()->service_provider_id;
        $isAdmin = auth()->id() == 1 || auth()->user()->hasRole('Super Admin');
        
        $userUnitStageId = null;
        if (!$isAdmin && $userSpId && auth()->user()->serviceProvider) {
            $userUnitStageId = auth()->user()->serviceProvider->operation_stage_id;
        }

        $allMovements = DB::table('production_movements')
            ->select(
                'job_card_id',
                'operation_stage_id',
                DB::raw('SUM(inward_qty) as total_inward'),
                DB::raw('SUM(outward_qty) as total_outward')
            )
            ->groupBy('job_card_id', 'operation_stage_id')
            ->get();

        foreach ($jobCards as $jc) {
            $schedules = ProcessSchedule::with('operationStage')->where('job_card_entry_id', $jc->id)->orderBy('id', 'asc')->get();

            if ($schedules->isEmpty()) {
                $processName = $jc->processGroup ? $jc->processGroup->name : 'N/A';

                $totalProduced = DB::table('production_receipt_items')->join('production_receipts', 'production_receipt_items.production_receipt_id', '=', 'production_receipts.id')->where('production_receipts.job_card_id', $jc->id)->where('production_receipts.status', 'Posted')->sum('qty_to_receive');

                $periodInward = $jc->grand_total_qty;
                $periodOutward = $totalProduced ?? 0;
                $currentWip = max(0, $periodInward - $periodOutward);

                if ($currentWip > 0) {
                    $hasSpTask = false;
                    $unitName = 'Unassigned';
                    
                    if (!$isAdmin && $userSpId) {
                        $hasSpTask = $jc->tasks->filter(function($t) use ($userSpId) {
                            return $t->assignee && $t->assignee->service_provider_id == $userSpId;
                        })->isNotEmpty();
                        
                        $isProcessResponsible = ($userUnitStageId && $jc->process_group_id == $userUnitStageId); 
                        
                        if (!$hasSpTask && !$isProcessResponsible) continue;
                        $unitName = auth()->user()->serviceProvider->name ?? 'Unknown Unit';
                    } else {
                        $firstTask = $jc->tasks->first();
                        $unitName = ($firstTask && $firstTask->assignee && $firstTask->assignee->serviceProvider) ? $firstTask->assignee->serviceProvider->name : 'Unassigned';
                    }

                    $stageName = $processName;
                    $key = $stageName;

                    if (!isset($groupedRows[$key])) {
                        $groupedRows[$key] = (object) [
                            'operation_stage_name' => $stageName,
                            'opening' => 0,
                            'inward' => 0,
                            'outward' => 0,
                            'wip' => 0,
                        ];
                    }
                    $groupedRows[$key]->inward += $periodInward;
                    $groupedRows[$key]->outward += $periodOutward;
                    $groupedRows[$key]->wip += $currentWip;
                }
                continue;
            }

            foreach ($schedules as $index => $schedule) {
                $stageMovement = $allMovements->where('job_card_id', $jc->id)->where('operation_stage_id', $schedule->operation_stage_id)->first();

                $inward = $stageMovement ? $stageMovement->total_inward : 0;
                $outward = $stageMovement ? $stageMovement->total_outward : 0;

                if ($index === 0 && $inward == 0) {
                    $inward = $jc->grand_total_qty;
                }

                $wip = max(0, $inward - $outward);

                $hasSpTask = false;
                
                if (!$isAdmin && $userSpId) {
                    $hasSpTask = $jc->tasks->where('stage_id', $schedule->id)->filter(function($t) use ($userSpId) {
                        return $t->assignee && $t->assignee->service_provider_id == $userSpId;
                    })->isNotEmpty();

                    $isStageResponsible = ($userUnitStageId && $schedule->operation_stage_id == $userUnitStageId);

                    if (!$hasSpTask && !$isStageResponsible) continue;
                }

                $stageName = $schedule->operationStage->operation_stage_name ?? 'N/A';
                $key = $stageName;

                if (!isset($groupedRows[$key])) {
                    $groupedRows[$key] = (object) [
                        'operation_stage_name' => $stageName,
                        'opening' => 0,
                        'inward' => 0,
                        'outward' => 0,
                        'wip' => 0,
                    ];
                }
                $groupedRows[$key]->inward += $inward;
                $groupedRows[$key]->outward += $outward;
                $groupedRows[$key]->wip += $wip;
            }
        }

        /* Production WIP (Unit Wise) */
        $production_wip = collect(array_values($groupedRows))->filter(function($row) {
            return $row->inward > 0 || $row->outward > 0 || $row->wip > 0;
        })->values();

        $production_plan_qty = JobCardEntry::whereMonth('job_card_date', Carbon::now()->month)->whereYear('job_card_date', Carbon::now()->year)->whereNull('deleted_at')->sum('grand_total_qty');

        $production_achieved_qty = DB::table('production_receipt_items')->join('production_receipts', 'production_receipt_items.production_receipt_id', '=', 'production_receipts.id')->whereMonth('production_receipts.receipt_date', Carbon::now()->month)->whereYear('production_receipts.receipt_date', Carbon::now()->year)->sum('production_receipt_items.scan_qty');

        $production_efficiency = $production_plan_qty > 0 ? round(($production_achieved_qty / $production_plan_qty) * 100) : 0;

        $delivery_overdue = JobCardEntry::whereNotIn('status', ['Production Completed', 'Closed'])->whereNotNull('delivery_date')->where('delivery_date', '<', Carbon::today())->whereNull('deleted_at')->select('job_card_no', 'delivery_date', 'grand_total_qty', DB::raw('DATEDIFF(CURDATE(), delivery_date) as overdue_days'))->orderByDesc('overdue_days')->take(5)->get();

        $process_wise_status = DB::table('tasks')
            ->join('process_schedules', 'tasks.stage_id', '=', 'process_schedules.id')
            ->join('operation_stages', 'process_schedules.operation_stage_id', '=', 'operation_stages.id')
            ->whereNull('tasks.deleted_at')
            ->whereNull('process_schedules.deleted_at')
            ->select(
                'operation_stages.operation_stage_name',
                DB::raw("SUM(CASE WHEN tasks.status = 'Planned'    THEN 1 ELSE 0 END) as planned"),
                DB::raw("SUM(CASE WHEN tasks.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress"),
                DB::raw("SUM(CASE WHEN tasks.status = 'Completed'  THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN tasks.status = 'Hold'       THEN 1 ELSE 0 END) as hold")
            )
            ->groupBy('operation_stages.id', 'operation_stages.operation_stage_name')
            ->get();

        /* Fabric & Store Stock Dashboard */
        $minStocksByBrand = DB::table('stock_entry_items as sei')
            ->join('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
            ->where('sei.store_category_id', 1)
            ->whereNull('sei.deleted_at')
            ->select('sei.brand_id', 'rm.id as rm_id', DB::raw('COALESCE(rm.min_stock, 0) as min_stock'))
            ->distinct()
            ->get()
            ->groupBy('brand_id')
            ->map(function($items) {
                return $items->sum('min_stock');
            });

        $fabricStockRaw = DB::table('stock_entry_items as sei')
            ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
            ->where('sei.store_category_id', 1)
            ->whereNull('sei.deleted_at')
            ->select(
                'sei.brand_id',
                DB::raw('COALESCE(b.brand_name, "Unknown Brand") as brand_name'),
                DB::raw('SUM(sei.qty_in - COALESCE(sei.qty_out, 0)) as stock'),
                DB::raw('SUM((sei.qty_in - COALESCE(sei.qty_out, 0)) * COALESCE(sei.price, 0)) as stock_value'),
                DB::raw('ROUND(AVG(DATEDIFF(CURDATE(), sei.created_at))) as avg_days')
            )
            ->groupBy('sei.brand_id', 'b.brand_name')
            ->having('stock', '>', 0)
            ->orderByDesc('stock')
            ->get();

        $fabric_stock_summary = [];
        $total_fabric_stock_qty = 0;
        $total_fabric_stock_val = 0;
        $fabric_shortage_count = 0;
        $fabric_excess_count = 0;

        foreach ($fabricStockRaw as $row) {
            $brandId = $row->brand_id;
            $stock = floatval($row->stock);
            $stockValue = floatval($row->stock_value);
            $avgDays = intval($row->avg_days);
            $minStock = floatval($minStocksByBrand[$brandId] ?? 0);

            $shortage = max(0, $minStock - $stock);
            $excess = max(0, $stock - $minStock);

            if ($shortage > 0) $fabric_shortage_count++;
            if ($excess > 0) $fabric_excess_count++;

            $total_fabric_stock_qty += $stock;
            $total_fabric_stock_val += $stockValue;

            $fabric_stock_summary[] = [
                'brand_name' => $row->brand_name,
                'stock' => $stock,
                'stock_value' => $stockValue,
                'days_in_warehouse' => $avgDays,
                'min_stock' => $minStock,
                'shortage' => $shortage,
                'excess' => $excess,
            ];
        }

        /* Fabric Utilisation Dashboard */
        $fabricUtilData = $this->computeFabricUtilisationSummary();
        $fabric_utilisation_summary = $fabricUtilData['summary'];
        $total_util_issued = $fabricUtilData['total_issued'];
        $total_util_consumed = $fabricUtilData['total_consumed'];
        $total_util_wastage = $fabricUtilData['total_wastage'];
        $total_util_pct = $fabricUtilData['total_util_pct'];

        /* Core Material Planner Dashboard */
        $corePlannerTotals = $this->getCoreMaterialPlannerTotals();
        $core_total_stock = $corePlannerTotals['total_stock'];
        $core_total_wip = $corePlannerTotals['total_wip'];
        $core_total_fg = $corePlannerTotals['total_fg'];
        $core_total_pipeline = $corePlannerTotals['total_pipeline'];

        /* Supplier Performance Dashboard */
        $supplierPerfData = $this->computeSupplierPerformanceData();
        $supplier_perf_count = $supplierPerfData['kpis']['total_suppliers'];
        $supplier_perf_spend = $supplierPerfData['kpis']['total_spend'];
        $supplier_perf_ontime = $supplierPerfData['kpis']['overall_ontime'];
        $supplier_perf_returns = $supplierPerfData['kpis']['total_returns'];

        /* Executive Fulfillment & Warehouse Operations KPI Card */
        $kpiCardData = $this->computeOperationsKpiCard();

        return view('dashboard', compact(
            'sales_today', 'sales_month', 'sales_year', 'sales_count_today', 'sales_count_month', 'sales_count_year', 'orders_today', 'orders_month', 'total_stock', 'urgent_orders', 'total_sales_value', 'sales_return', 'bill_discount', 'bill_discount_percent', 'cash_discount', 'cash_discount_percent', 'total_debtors', 'total_purchase', 'purchase_return', 'total_creditors', 'debtors_aging', 'creditors_aging', 'collection_performance', 'fabric_value', 'accessories_value', 'wip_value', 'finished_goods_value', 'months_labels', 'sales_chart_data', 'collection_chart_data', 'purchase_chart_data', 'payment_chart_data', 'production_wip', 'production_plan_qty', 'production_achieved_qty', 'production_efficiency', 'delivery_overdue', 'process_wise_status', 'wip_cost_breakdown', 'maintenance_raised', 'maintenance_attended', 'maintenance_pending', 'expiring_documents', 'total_emp', 'present_emp_today', 'absent_emp_today', 'late_emp_today', 'overtime_today', 'dbDevices', 'attendance_chart_data',
            'today_sales_qty', 'today_sales_wot', 'today_return_qty', 'today_return_wot', 'today_net_qty', 'today_net_wot',
            'month_sales_qty', 'month_sales_wot', 'month_return_qty', 'month_return_wot', 'month_net_qty', 'month_net_wot',
            'year_sales_qty', 'year_sales_wot', 'year_return_qty', 'year_return_wot', 'year_net_qty', 'year_net_wot',
            'today_missed_qty', 'today_missed_value', 'month_missed_qty', 'month_missed_value', 'year_missed_qty', 'year_missed_value',
            'fabric_stock_summary', 'total_fabric_stock_qty', 'total_fabric_stock_val', 'fabric_shortage_count', 'fabric_excess_count',
            'fabric_utilisation_summary', 'total_util_issued', 'total_util_consumed', 'total_util_wastage', 'total_util_pct',
            'core_total_stock', 'core_total_wip', 'core_total_fg', 'core_total_pipeline',
            'supplier_perf_count', 'supplier_perf_spend', 'supplier_perf_ontime', 'supplier_perf_returns',
            'kpiCardData'
        ));
    }

    private function computeOperationsKpiCard()
    {
        // 1. Pending Orders
        $pendingQuery = DB::table('sales_orders')
            ->where('status', 'Pending')
            ->whereNull('deleted_at');

        $totalPending = (clone $pendingQuery)->count();
        $dueToday = (clone $pendingQuery)
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '!=', '0000-00-00')
            ->whereDate('delivery_date', '<=', date('Y-m-d'))
            ->count();

        $pendingStatus = 'green';
        if ($totalPending > 20 || $dueToday > 10) {
            $pendingStatus = 'red';
        } elseif ($totalPending >= 5 || $dueToday > 0) {
            $pendingStatus = 'yellow';
        }

        // 2. Average Lead Time (Rolling Past 30 Days)
        $leadTimeQuery = DB::table('sales_invoices')
            ->join('sales_orders', 'sales_invoices.so_id', '=', 'sales_orders.id')
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_orders.deleted_at')
            ->whereNotNull('sales_invoices.inv_date')
            ->whereNotNull('sales_orders.so_date')
            ->where('sales_invoices.inv_date', '>=', 'sales_orders.so_date');

        $leadTimeDays = (clone $leadTimeQuery)
            ->where('sales_invoices.inv_date', '>=', Carbon::now()->subDays(30))
            ->selectRaw('AVG(DATEDIFF(sales_invoices.inv_date, sales_orders.so_date)) as avg_days')
            ->value('avg_days');

        if ($leadTimeDays === null) {
            $leadTimeDays = $leadTimeQuery
                ->selectRaw('AVG(DATEDIFF(sales_invoices.inv_date, sales_orders.so_date)) as avg_days')
                ->value('avg_days');
        }

        $leadTimeDays = $leadTimeDays !== null ? round(floatval($leadTimeDays), 1) : 0.0;
        $leadStatus = 'green';
        if ($leadTimeDays > 5.0) {
            $leadStatus = 'red';
        } elseif ($leadTimeDays > 3.0) {
            $leadStatus = 'yellow';
        }

        // 3. Warehouse Utilisation (Overall Finished Goods)
        $whCapQuery = DB::table('warehouse_brand_capacities')->where('status', 'Active');
        $activeWhIds = (clone $whCapQuery)->distinct()->pluck('warehouse_id')->toArray();
        $totalCapacity = floatval($whCapQuery->sum('capacity_pcs') ?? 0);

        $stockQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->where(function($q) {
                $q->whereNull('stock_entry_items.store_category_id')
                  ->orWhere('stock_entry_items.store_category_id', '!=', 6);
            });

        if (!empty($activeWhIds)) {
            $stockQuery->where(function($q) use ($activeWhIds) {
                $q->whereIn('stock_entry_items.warehouse_id', $activeWhIds)
                  ->orWhereIn('stock_entries.warehouse_id', $activeWhIds);
            });
        }

        $totalFgStock = floatval($stockQuery->selectRaw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as net_stock')->value('net_stock') ?? 0);
        $totalFgStock = max(0, $totalFgStock);

        $utilizationPct = $totalCapacity > 0 ? round(($totalFgStock / $totalCapacity) * 100, 1) : 0.0;
        $utilStatus = 'green';
        if ($utilizationPct > 95) {
            $utilStatus = 'red';
        } elseif ($utilizationPct > 80 || ($utilizationPct < 40 && $totalCapacity > 0)) {
            $utilStatus = 'yellow';
        }

        // 4. Dispatch Accuracy
        $totalDispatches = DB::table('sales_invoices')->whereNull('deleted_at')->count();
        $dispatchErrors = DB::table('credit_notes')->whereNull('deleted_at')
            ->where(function($q) {
                $q->where('fault', 'Warehouse Fault')
                  ->orWhereIn('reason', ['Invoice Mistake', 'Delay Despatch']);
            })
            ->count();

        $accuracyPct = $totalDispatches > 0
            ? round((($totalDispatches - $dispatchErrors) / $totalDispatches) * 100, 2)
            : 100.0;

        $accuracyStatus = 'green';
        if ($accuracyPct < 95.0) {
            $accuracyStatus = 'red';
        } elseif ($accuracyPct < 99.0) {
            $accuracyStatus = 'yellow';
        }

        return [
            'pending_orders' => [
                'total' => $totalPending,
                'due_today' => $dueToday,
                'display' => number_format($totalPending) . ' Orders (' . number_format($dueToday) . ' Due Today)',
                'status' => $pendingStatus
            ],
            'average_lead_time' => [
                'days' => $leadTimeDays,
                'display' => $leadTimeDays . ' Days',
                'status' => $leadStatus
            ],
            'warehouse_utilisation' => [
                'capacity' => $totalCapacity,
                'fg_stock' => $totalFgStock,
                'pct' => $utilizationPct,
                'display' => $utilizationPct . '%',
                'status' => $utilStatus
            ],
            'dispatch_accuracy' => [
                'total' => $totalDispatches,
                'errors' => $dispatchErrors,
                'pct' => $accuracyPct,
                'display' => $accuracyPct . '% (' . number_format($dispatchErrors) . ' Errors)',
                'status' => $accuracyStatus
            ]
        ];
    }

    private function computeFabricUtilisationSummary()
    {
        $utilJobCards = JobCardEntry::with([
            'brand',
            'serviceProvider',
            'issueItems',
            'fabricDetails',
            'item.style'
        ])
        ->where('status', '!=', 'cancelled')
        ->whereNotNull('job_card_date')
        ->orderBy('job_card_date', 'desc')
        ->get();

        $utilJobCardIds = $utilJobCards->pluck('id')->toArray();
        $utilStyleMap = [];
        if (!empty($utilJobCardIds)) {
            $utilStyleRows = DB::table('job_card_fabric_details as jcfd')
                ->join('stock_entry_items as sei', function($join) {
                    $join->on('sei.stock_entry_id', '=', 'jcfd.stock_entry_id')
                         ->on('sei.art_no', '=', 'jcfd.art_no');
                })
                ->join('styles as s', 'sei.style_id', '=', 's.id')
                ->whereIn('jcfd.job_card_entry_id', $utilJobCardIds)
                ->whereNull('jcfd.deleted_at')
                ->whereNull('sei.deleted_at')
                ->select('jcfd.job_card_entry_id', 's.style_name')
                ->distinct()
                ->get();
            foreach ($utilStyleRows as $sr) {
                if (!isset($utilStyleMap[$sr->job_card_entry_id])) {
                    $utilStyleMap[$sr->job_card_entry_id] = [];
                }
                $utilStyleMap[$sr->job_card_entry_id][] = $sr->style_name;
            }
        }

        $fabric_utilisation_summary = [];
        $total_util_issued = 0;
        $total_util_consumed = 0;
        $total_util_wastage = 0;

        foreach ($utilJobCards as $jc) {
            $brandName = $jc->brand ? $jc->brand->brand_name : 'Unassigned Brand';
            $brandId = $jc->brand_id ?: 0;

            $issuedFromItems = $jc->issueItems->sum('qty_issue');
            $issuedFromDetails = $jc->fabricDetails->sum('total_qty');
            $calcIssued = $jc->grand_total_qty * ($jc->average ?: 0);

            $fabricIssued = $issuedFromItems > 0 ? $issuedFromItems : ($issuedFromDetails > 0 ? $issuedFromDetails : $calcIssued);

            $consumedFromItems = $jc->issueItems->sum('qty_used');
            $consumedFromDetails = $jc->fabricDetails->sum('used_qty');
            $fabricConsumed = $consumedFromItems > 0 ? $consumedFromItems : ($consumedFromDetails > 0 ? $consumedFromDetails : ($jc->status == 'Completed' ? $fabricIssued : 0));

            $wastage = $jc->issueItems->sum('qty_wastage');
            if ($wastage == 0 && $fabricIssued > $fabricConsumed && $jc->status == 'Completed') {
                $wastage = max(0, $fabricIssued - $fabricConsumed);
            }

            $utilisation = $fabricIssued > 0 ? round(($fabricConsumed / $fabricIssued) * 100, 1) : 0;
            if ($utilisation > 100) $utilisation = 100.0;

            $style = 'N/A';
            if (!empty($utilStyleMap[$jc->id])) {
                $style = implode(', ', array_unique($utilStyleMap[$jc->id]));
            } elseif ($jc->item && $jc->item->style) {
                $style = $jc->item->style->style_name;
            }

            $serviceProviderName = $jc->serviceProvider ? $jc->serviceProvider->name : 'In-House';

            $total_util_issued += $fabricIssued;
            $total_util_consumed += $fabricConsumed;
            $total_util_wastage += $wastage;

            $jobCardDetail = [
                'id' => $jc->id,
                'job_card_no' => $jc->job_card_no,
                'date' => $jc->job_card_date ? date('d-M-Y', strtotime($jc->job_card_date)) : '-',
                'service_provider' => $serviceProviderName,
                'style' => $style,
                'fabric_issued' => floatval($fabricIssued),
                'fabric_consumed' => floatval($fabricConsumed),
                'wastage' => floatval($wastage),
                'utilisation' => $utilisation,
                'remarks' => $jc->remarks ?: '-',
                'status' => $jc->status ?: 'Draft',
            ];

            $groupKey = $brandId . '-' . $style . '-' . $serviceProviderName;

            if (!isset($fabric_utilisation_summary[$groupKey])) {
                $fabric_utilisation_summary[$groupKey] = [
                    'brand_id' => $brandId,
                    'brand_name' => $brandName,
                    'style' => $style,
                    'service_provider' => $serviceProviderName,
                    'job_cards_count' => 0,
                    'fabric_issued' => 0,
                    'fabric_consumed' => 0,
                    'wastage' => 0,
                    'utilisation' => 0,
                    'job_cards' => []
                ];
            }

            $fabric_utilisation_summary[$groupKey]['job_cards_count']++;
            $fabric_utilisation_summary[$groupKey]['fabric_issued'] += $fabricIssued;
            $fabric_utilisation_summary[$groupKey]['fabric_consumed'] += $fabricConsumed;
            $fabric_utilisation_summary[$groupKey]['wastage'] += $wastage;
            $fabric_utilisation_summary[$groupKey]['job_cards'][] = $jobCardDetail;
        }

        foreach ($fabric_utilisation_summary as &$b) {
            $b['utilisation'] = $b['fabric_issued'] > 0 
                ? round(($b['fabric_consumed'] / $b['fabric_issued']) * 100, 1) 
                : 0;
            if ($b['utilisation'] > 100) $b['utilisation'] = 100.0;
        }
        unset($b);

        usort($fabric_utilisation_summary, function($a, $b) {
            return $b['fabric_issued'] <=> $a['fabric_issued'];
        });

        $total_util_pct = $total_util_issued > 0 ? round(($total_util_consumed / $total_util_issued) * 100, 1) : 0;

        return [
            'summary' => array_values($fabric_utilisation_summary),
            'total_issued' => $total_util_issued,
            'total_consumed' => $total_util_consumed,
            'total_wastage' => $total_util_wastage,
            'total_util_pct' => $total_util_pct,
        ];
    }

    public function getFabricUtilisationAjax(Request $request)
    {
        $allData = $this->computeFabricUtilisationSummary();
        $search = trim($request->get('search', ''));
        $page = max(1, intval($request->get('page', 1)));
        $perPage = max(1, intval($request->get('per_page', 10)));

        $summary = $allData['summary'];

        if (!empty($search)) {
            $summary = array_values(array_filter($summary, function($item) use ($search) {
                return (stripos($item['brand_name'] ?? '', $search) !== false)
                    || (stripos($item['style'] ?? '', $search) !== false)
                    || (stripos($item['service_provider'] ?? '', $search) !== false);
            }));
        }

        $totalRecords = count($summary);
        $lastPage = max(1, ceil($totalRecords / $perPage));
        if ($page > $lastPage) $page = $lastPage;

        $totIssued = collect($summary)->sum('fabric_issued');
        $totConsumed = collect($summary)->sum('fabric_consumed');
        $totWastage = collect($summary)->sum('wastage');
        $totJcs = collect($summary)->sum('job_cards_count');
        $totUtil = $totIssued > 0 ? round(($totConsumed / $totIssued) * 100, 1) : 0;

        $offset = ($page - 1) * $perPage;
        $pageData = array_slice($summary, $offset, $perPage);

        return response()->json([
            'data' => $pageData,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalRecords,
            'last_page' => $lastPage,
            'from' => $totalRecords > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $totalRecords),
            'totals' => [
                'total_jcs' => number_format($totJcs) . ' JCs',
                'total_issued' => number_format($totIssued, 2),
                'total_consumed' => number_format($totConsumed, 2),
                'total_wastage' => number_format($totWastage, 2),
                'total_utilisation' => $totUtil . '%'
            ]
        ]);
    }

    private function getCoreMaterialPlannerTotals()
    {
        $totalStock = DB::table('stock_entry_items')
            ->where('store_category_id', 1)
            ->whereNull('deleted_at')
            ->sum(DB::raw('qty_in - COALESCE(qty_out, 0)'));

        $totalWip = DB::table('job_card_fabric_details as jcfd')
            ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
            ->whereNull('jcfd.deleted_at')
            ->where('jce.status', '!=', 'cancelled')
            ->where('jce.status', '!=', 'Completed')
            ->sum('jcfd.total_qty');

        $totalFg = DB::table('stock_entry_items')
            ->whereNull('store_category_id')
            ->whereNull('deleted_at')
            ->sum(DB::raw('qty_in - COALESCE(qty_out, 0)'));

        return [
            'total_stock' => floatval($totalStock),
            'total_wip' => floatval($totalWip),
            'total_fg' => floatval($totalFg),
            'total_pipeline' => floatval($totalStock) + floatval($totalWip)
        ];
    }

    public function getCoreMaterialPlannerAjax(Request $request)
    {
        $search = trim($request->input('search.value', $request->get('search', '')));
        $start = max(0, intval($request->get('start', 0)));
        $length = intval($request->get('length', 10));
        if ($length <= 0) $length = 10;

        $artQuery = DB::table('stock_entry_items as sei')
            ->leftJoin('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
            ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
            ->where('sei.store_category_id', 1)
            ->whereNull('sei.deleted_at')
            ->whereNotNull('sei.art_no')
            ->where('sei.art_no', '!=', '')
            ->select(
                'sei.art_no',
                DB::raw('MAX(rm.name) as item_name'),
                DB::raw('MAX(COALESCE(b.brand_name, "General")) as brand_name'),
                DB::raw('SUM(sei.qty_in - COALESCE(sei.qty_out, 0)) as stock')
            )
            ->groupBy('sei.art_no')
            ->havingRaw('stock > 0');

        if (!empty($search)) {
            $artQuery->where(function($q) use ($search) {
                $q->where('sei.art_no', 'like', "%{$search}%")
                  ->orWhere('rm.name', 'like', "%{$search}%")
                  ->orWhere('b.brand_name', 'like', "%{$search}%");
            });
        }

        // Get total count of art numbers
        $totalRecords = DB::query()->fromSub($artQuery, 'sub')->count();

        // Fetch ONLY the target art numbers for this page
        $pageArtRows = $artQuery->orderByDesc('stock')
            ->offset($start)
            ->limit($length)
            ->get();

        $pageArtList = $pageArtRows->pluck('art_no')->toArray();

        $wipQuery = [];
        $fgDirect = [];
        $fgViaJc = [];

        if (!empty($pageArtList)) {
            // Fast WIP lookup for only current page items
            $wipQuery = DB::table('job_card_fabric_details as jcfd')
                ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
                ->whereIn('jcfd.art_no', $pageArtList)
                ->whereNull('jcfd.deleted_at')
                ->where('jce.status', '!=', 'cancelled')
                ->where('jce.status', '!=', 'Completed')
                ->select('jcfd.art_no', DB::raw('SUM(jcfd.total_qty) as wip_qty'))
                ->groupBy('jcfd.art_no')
                ->get()
                ->keyBy('art_no');

            // Fast FG direct lookup for only current page items
            $fgDirect = DB::table('stock_entry_items')
                ->whereNull('store_category_id')
                ->whereIn('art_no', $pageArtList)
                ->whereNull('deleted_at')
                ->select('art_no', DB::raw('SUM(qty_in - COALESCE(qty_out, 0)) as fg_qty'))
                ->groupBy('art_no')
                ->get()
                ->keyBy('art_no');

            // Fast FG via JC lookup for only current page items
            $fgViaJc = DB::table('job_card_fabric_details as jcfd')
                ->join('job_card_entries as jce', 'jcfd.job_card_entry_id', '=', 'jce.id')
                ->join('stock_entry_items as fg_sei', function($join) {
                    $join->on('fg_sei.art_no', '=', 'jce.job_card_no')
                         ->orOn('fg_sei.art_no', '=', 'jce.reference_no');
                })
                ->whereIn('jcfd.art_no', $pageArtList)
                ->whereNull('fg_sei.store_category_id')
                ->whereNull('fg_sei.deleted_at')
                ->select('jcfd.art_no', DB::raw('SUM(fg_sei.qty_in - COALESCE(fg_sei.qty_out, 0)) as fg_qty'))
                ->groupBy('jcfd.art_no')
                ->get()
                ->keyBy('art_no');
        }

        $pageData = [];
        foreach ($pageArtRows as $idx => $item) {
            $stock = max(0, floatval($item->stock));
            $wip = max(0, floatval($wipQuery[$item->art_no]->wip_qty ?? 0));
            $fg1 = max(0, floatval($fgDirect[$item->art_no]->fg_qty ?? 0));
            $fg2 = max(0, floatval($fgViaJc[$item->art_no]->fg_qty ?? 0));
            $fg = max($fg1, $fg2);
            $pipeline = $stock + $wip;

            $pageData[] = [
                'DT_RowIndex' => $start + $idx + 1,
                'art_no' => '<span class="fw-bold text-primary">' . e($item->art_no) . '</span>',
                'item_name' => '<span class="text-dark">' . e($item->item_name ?: 'FABRIC') . '</span>',
                'brand_name' => '<span class="badge bg-label-info fw-bold">' . e($item->brand_name ?: 'General') . '</span>',
                'stock' => '<span class="fw-bold text-dark">' . number_format($stock, 2) . '</span>',
                'wip' => '<span class="fw-bold text-warning">' . number_format($wip, 2) . '</span>',
                'fg' => '<span class="fw-bold text-success">' . number_format($fg) . ' pcs</span>',
                'pipeline' => '<span class="fw-bold text-info">' . number_format($pipeline, 2) . '</span>',
                'total_pipeline' => '<span class="fw-bold text-info">' . number_format($pipeline, 2) . '</span>'
            ];
        }

        $grandTotals = $this->getCoreMaterialPlannerTotals();

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $pageData,
            'totals' => [
                'total_stock' => number_format($grandTotals['total_stock'], 2),
                'total_wip' => number_format($grandTotals['total_wip'], 2),
                'total_fg' => number_format($grandTotals['total_fg']),
                'total_pipeline' => number_format($grandTotals['total_pipeline'], 2)
            ]
        ]);
    }

    private function computeSupplierPerformanceData()
    {
        $supplierPOs = DB::table('purchase_orders as po')
            ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
            ->leftJoin('purchase_invoices as pi', 'po.id', '=', 'pi.purchase_order_id')
            ->leftJoin('grn_entries as grn', 'pi.id', '=', 'grn.purchase_invoice_id')
            ->whereNull('po.deleted_at')
            ->select(
                's.id as supplier_id',
                's.name as supplier_name',
                'po.id as po_id',
                'po.total_amount',
                'po.due_date',
                'po.po_date',
                DB::raw('COALESCE(grn.grn_date, pi.invoice_date) as delivery_date')
            )
            ->get();

        $debitNotes = DB::table('debit_notes')
            ->whereNull('deleted_at')
            ->select('supplier_id', DB::raw('COUNT(*) as dn_count'), DB::raw('SUM(grand_total) as dn_total'))
            ->groupBy('supplier_id')
            ->get()
            ->keyBy('supplier_id');

        $grouped = $supplierPOs->groupBy('supplier_id');
        $supplierPerformance = [];

        $grandSpend = 0;
        $grandOnTimeCount = 0;
        $grandEvalCount = 0;
        $grandReturnsCount = 0;

        foreach ($grouped as $supId => $pos) {
            $supplierName = $pos->first()->supplier_name;
            $uniquePos = $pos->unique('po_id');
            $totalSpend = $uniquePos->sum('total_amount');
            $ordersCount = $uniquePos->count();

            $onTimeCount = 0;
            $evaluatedCount = 0;
            $totalDelayDays = 0;

            foreach ($uniquePos as $po) {
                $dueDate = $po->due_date;
                $deliveryDate = $po->delivery_date;

                if (!$dueDate) {
                    if ($deliveryDate) {
                        $onTimeCount++;
                        $evaluatedCount++;
                    }
                    continue;
                }

                $evaluatedCount++;

                if ($deliveryDate) {
                    if ($deliveryDate <= $dueDate) {
                        $onTimeCount++;
                    } else {
                        $delay = (new \DateTime($deliveryDate))->diff(new \DateTime($dueDate))->days;
                        $totalDelayDays += $delay;
                    }
                } else {
                    $today = date('Y-m-d');
                    if ($today > $dueDate) {
                        $delay = (new \DateTime($today))->diff(new \DateTime($dueDate))->days;
                        $totalDelayDays += $delay;
                    } else {
                        $onTimeCount++;
                    }
                }
            }

            $onTimePct = $evaluatedCount > 0 ? round(($onTimeCount / $evaluatedCount) * 100, 1) : 100;
            $avgDelay = $ordersCount > 0 ? round($totalDelayDays / $ordersCount, 1) : 0;

            $dn = $debitNotes[$supId] ?? null;
            $dnCount = $dn ? $dn->dn_count : 0;
            $dnTotal = $dn ? floatval($dn->dn_total) : 0;

            $grandSpend += $totalSpend;
            $grandOnTimeCount += $onTimeCount;
            $grandEvalCount += $evaluatedCount;
            $grandReturnsCount += $dnCount;

            // Star Rating (1 to 5 Stars)
            if ($ordersCount == 0) {
                $stars = 5;
            } elseif ($onTimePct >= 95 && $dnCount <= 1) {
                $stars = 5;
            } elseif ($onTimePct >= 85 && $dnCount <= 3) {
                $stars = 4;
            } elseif ($onTimePct >= 70) {
                $stars = 3;
            } elseif ($onTimePct >= 50) {
                $stars = 2;
            } else {
                $stars = 1;
            }

            $supplierPerformance[] = [
                'supplier_id' => $supId,
                'supplier_name' => $supplierName,
                'purchase_value' => $totalSpend,
                'orders_count' => $ordersCount,
                'on_time_pct' => $onTimePct,
                'avg_delay' => $avgDelay,
                'returns_count' => $dnCount,
                'returns_total' => $dnTotal,
                'stars' => $stars
            ];
        }

        usort($supplierPerformance, fn($a, $b) => $b['purchase_value'] <=> $a['purchase_value']);

        $overallOnTime = $grandEvalCount > 0 ? round(($grandOnTimeCount / $grandEvalCount) * 100, 1) : 100;

        return [
            'suppliers' => $supplierPerformance,
            'kpis' => [
                'total_suppliers' => count($supplierPerformance),
                'total_spend' => $grandSpend,
                'overall_ontime' => $overallOnTime,
                'total_returns' => $grandReturnsCount
            ]
        ];
    }

    public function getSupplierPerformanceAjax(Request $request)
    {
        $perfData = $this->computeSupplierPerformanceData();
        $items = $perfData['suppliers'];

        $search = trim($request->input('search.value', $request->get('search', '')));
        $start = max(0, intval($request->get('start', 0)));
        $length = intval($request->get('length', 10));
        if ($length <= 0) $length = 10;

        if (!empty($search)) {
            $items = array_values(array_filter($items, function($item) use ($search) {
                return stripos($item['supplier_name'] ?? '', $search) !== false;
            }));
        }

        $totalRecords = count($items);
        $pageRows = array_slice($items, $start, $length);

        $pageData = [];
        foreach ($pageRows as $idx => $s) {
            // Star HTML
            $starsHtml = '<div class="d-inline-flex gap-1">';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $s['stars']) {
                    $starsHtml .= '<i class="ri ri-star-fill text-warning fs-6"></i>';
                } else {
                    $starsHtml .= '<i class="ri ri-star-line text-muted fs-6"></i>';
                }
            }
            $starsHtml .= '</div>';

            // On-Time Badge
            if ($s['on_time_pct'] >= 90) {
                $onTimeBadge = '<span class="badge bg-success text-white px-2 py-1 fw-bold">' . $s['on_time_pct'] . '%</span>';
            } elseif ($s['on_time_pct'] >= 75) {
                $onTimeBadge = '<span class="badge bg-warning text-dark px-2 py-1 fw-bold">' . $s['on_time_pct'] . '%</span>';
            } else {
                $onTimeBadge = '<span class="badge bg-danger text-white px-2 py-1 fw-bold">' . $s['on_time_pct'] . '%</span>';
            }

            // Avg Delay Badge
            $delayHtml = $s['avg_delay'] > 0 
                ? '<span class="text-danger fw-bold">' . $s['avg_delay'] . ' Days</span>'
                : '<span class="text-success fw-bold">0 Days</span>';

            // Returns Badge
            $returnsHtml = $s['returns_count'] > 0
                ? '<span class="badge bg-label-danger fw-bold">' . $s['returns_count'] . ' (' . '₹' . number_format($s['returns_total']) . ')</span>'
                : '<span class="text-muted">0</span>';

            $pageData[] = [
                'DT_RowIndex' => $start + $idx + 1,
                'supplier' => '<span class="fw-bold text-dark">' . e($s['supplier_name']) . '</span>',
                'purchase_value' => '<span class="fw-bold text-primary">₹' . number_format($s['purchase_value'], 2) . '</span>',
                'orders' => '<span class="badge bg-label-info fw-bold">' . $s['orders_count'] . '</span>',
                'on_time_pct' => $onTimeBadge,
                'avg_delay' => $delayHtml,
                'returns' => $returnsHtml,
                'overall_rating' => $starsHtml
            ];
        }

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $pageData,
            'kpis' => $perfData['kpis']
        ]);
    }

    public function getServiceWipDetails(Request $request)
    {
        $jobCardNo = $request->job_card_no;
        $operationStageName = $request->operation_stage_name;

        $jc = JobCardEntry::with('tasks.assignments')->where('job_card_no', $jobCardNo)->first();
        if (!$jc)
            return response()->json([]);

        $schedule = ProcessSchedule::with('operationStage')
            ->where('job_card_entry_id', $jc->id)
            ->whereHas('operationStage', function ($q) use ($operationStageName) {
                $q->where('operation_stage_name', $operationStageName);
            })->first();

        if (!$schedule)
            return response()->json([]);

        $stageId = $schedule->operation_stage_id;

        $stageTasks = $jc->tasks->filter(function ($task) use ($schedule) {
            if ($task->stage_id == $schedule->id && !$task->stage)
                return true;
            if ($task->stage && $task->stage->id == $schedule->id)
                return true;
            return false;
        });

        $assignments = $stageTasks->flatMap->assignments;
        $requiredServices = ProductionService::where('operation_stage_id', $stageId)->active()->get();

        $servicesData = [];
        foreach ($requiredServices as $service) {
            $serviceAssignments = $assignments->where('service_id', $service->id);
            if ($serviceAssignments->isEmpty()) {
                $servicesData[] = [
                    'name' => $service->service_name,
                    'completed' => 0,
                    'in_progress' => 0,
                    'wastage' => 0
                ];
                continue;
            }

            $isSequential = $serviceAssignments->count() > 1 && $serviceAssignments->max('issue_qty') >= $serviceAssignments->sum('issue_qty') * 0.9;

            if ($isSequential) {
                $completed = $serviceAssignments->min('completed_qty');
                $inprogress = $serviceAssignments->max('inprogress_qty');
                $wastage = $serviceAssignments->sum('wastage_qty');
            } else {
                $completed = $serviceAssignments->sum('completed_qty');
                $inprogress = $serviceAssignments->sum('inprogress_qty');
                $wastage = $serviceAssignments->sum('wastage_qty');
            }

            $servicesData[] = [
                'name' => $service->service_name,
                'completed' => (float) $completed,
                'in_progress' => (float) $inprogress,
                'wastage' => (float) $wastage
            ];
        }

        return response()->json($servicesData);
    }
}
