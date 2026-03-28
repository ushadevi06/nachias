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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        $sales_today = SalesInvoice::whereDate('inv_date', $today)->sum('grand_total');
        $sales_month = SalesInvoice::whereBetween('inv_date', [$monthStart, Carbon::now()])->sum('grand_total');
        $sales_year = SalesInvoice::whereBetween('inv_date', [$yearStart, Carbon::now()])->sum('grand_total');

        $sales_count_today = SalesInvoice::whereDate('inv_date', $today)->count();
        $sales_count_month = SalesInvoice::whereBetween('inv_date', [$monthStart, Carbon::now()])->count();
        $sales_count_year = SalesInvoice::whereBetween('inv_date', [$yearStart, Carbon::now()])->count();

        $orders_today = SalesOrder::whereDate('so_date', $today)->count();
        $orders_month = SalesOrder::whereBetween('so_date', [$monthStart, Carbon::now()])->count();
        $total_stock = StockEntryItem::sum('qty_in') - StockEntryItem::sum('qty_out');
        $urgent_orders = SalesOrder::where('status', 'Pending')->count();

        $total_sales_value = SalesInvoice::whereNull('deleted_at')->sum('grand_total');
        $sales_return = CreditNote::whereNull('deleted_at')->sum('grand_total');
        $bill_discount = SalesInvoice::whereNull('deleted_at')->sum('discount');
        $total_sub_total = SalesInvoice::whereNull('deleted_at')->sum('sub_total');
        $bill_discount_percent = $total_sub_total > 0 ? round(($bill_discount / $total_sub_total) * 100, 2) : 0;
        $cash_discount = 0;
        $cash_discount_percent = 0;

        $total_debtors = SalesInvoice::whereNull('deleted_at')->sum('due_amount');
        $total_purchase = PurchaseInvoice::whereNull('deleted_at')->sum('grand_total');
        $purchase_return = DebitNote::whereNull('deleted_at')->sum('grand_total');
        $total_creditors = PurchaseInvoice::whereNull('deleted_at')->sum('due_amount');

        $debtors_aging = DB::table('sales_invoices')
            ->join('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->join('zones', 'customers.zone_id', '=', 'zones.id')
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('customers.deleted_at')
            ->whereNull('zones.deleted_at')
            ->where('sales_invoices.due_amount', '>', 0)
            ->select(
                'zones.zone_name',
                DB::raw('SUM(sales_invoices.due_amount) as total_due'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) <= 30 THEN sales_invoices.due_amount ELSE 0 END) as bucket_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) BETWEEN 31 AND 60 THEN sales_invoices.due_amount ELSE 0 END) as bucket_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) BETWEEN 61 AND 90 THEN sales_invoices.due_amount ELSE 0 END) as bucket_90'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), sales_invoices.inv_date) > 90 THEN sales_invoices.due_amount ELSE 0 END) as bucket_above_90')
            )->groupBy('zones.id', 'zones.zone_name')->get();

        $creditors_aging = DB::table('purchase_invoices')
            ->join('suppliers', 'purchase_invoices.supplier_id', '=', 'suppliers.id')
            ->whereNull('purchase_invoices.deleted_at')
            ->whereNull('suppliers.deleted_at')
            ->where('purchase_invoices.due_amount', '>', 0)
            ->select(
                'suppliers.name as supplier_name',
                DB::raw('SUM(purchase_invoices.due_amount) as total_due'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) <= 30 THEN purchase_invoices.due_amount ELSE 0 END) as bucket_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) BETWEEN 31 AND 60 THEN purchase_invoices.due_amount ELSE 0 END) as bucket_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) BETWEEN 61 AND 90 THEN purchase_invoices.due_amount ELSE 0 END) as bucket_90'),
                DB::raw('SUM(CASE WHEN DATEDIFF(CURDATE(), purchase_invoices.invoice_date) > 90 THEN purchase_invoices.due_amount ELSE 0 END) as bucket_above_90')
            )->groupBy('suppliers.id', 'suppliers.name')->get();

        $collection_performance = $total_sales_value > 0 ? round((($total_sales_value - $total_debtors) / $total_sales_value) * 100) : 0;

        $fabric_value = StockEntryItem::where('store_category_id', 1)->whereNull('deleted_at')->sum(DB::raw('(qty_in - COALESCE(qty_out, 0)) * price'));

        $accessories_value = StockEntryItem::where('store_category_id', 2)->whereNull('deleted_at')->sum(DB::raw('(qty_in - COALESCE(qty_out, 0)) * price'));

        $wip_value = JobCardIssueItem::join('job_card_entries', 'job_card_issue_items.job_card_entry_id', '=', 'job_card_entries.id')->whereNotIn('job_card_entries.status', ['Production Completed', 'Closed'])->whereNull('job_card_issue_items.deleted_at')->sum('job_card_issue_items.total_cost');

        $wip_cost_breakdown = JobCardIssueItem::join('job_card_entries', 'job_card_issue_items.job_card_entry_id', '=', 'job_card_entries.id')->whereNotIn('job_card_entries.status', ['Production Completed', 'Closed'])->whereNull('job_card_issue_items.deleted_at')->select('job_card_entries.job_card_no', DB::raw('SUM(job_card_issue_items.total_cost) as total_cost'))->groupBy('job_card_entries.id', 'job_card_entries.job_card_no')->get();

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
            'tasks'
        ])->where('grand_total_qty', '>', 0)
            ->whereNotIn('status', ['Production Completed', 'Closed']);

        $jobCards = $wipQuery->get();
        $rows = [];

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
                    $rows[] = (object) [
                        'job_card_no' => $jc->job_card_no,
                        'operation_stage_name' => $processName,
                        'opening' => 0,
                        'inward' => $periodInward,
                        'outward' => $periodOutward,
                        'wip' => $currentWip,
                    ];
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

                $hasActiveTask = $jc->tasks->where('stage_id', $schedule->id)->where('status', '!=', 'Completed')->isNotEmpty();

                if ($wip > 0 || $hasActiveTask) {
                    $rows[] = (object) [
                        'job_card_no' => $jc->job_card_no,
                        'operation_stage_name' => $schedule->operationStage->operation_stage_name ?? 'N/A',
                        'opening' => 0,
                        'inward' => $inward,
                        'outward' => $outward,
                        'wip' => $wip,
                    ];
                }
            }
        }

        $production_wip = collect($rows);

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

        return view('dashboard', compact('sales_today', 'sales_month', 'sales_year', 'sales_count_today', 'sales_count_month', 'sales_count_year', 'orders_today', 'orders_month', 'total_stock', 'urgent_orders', 'total_sales_value', 'sales_return', 'bill_discount', 'bill_discount_percent', 'cash_discount', 'cash_discount_percent', 'total_debtors', 'total_purchase', 'purchase_return', 'total_creditors', 'debtors_aging', 'creditors_aging', 'collection_performance', 'fabric_value', 'accessories_value', 'wip_value', 'finished_goods_value', 'months_labels', 'sales_chart_data', 'collection_chart_data', 'purchase_chart_data', 'payment_chart_data', 'production_wip', 'production_plan_qty', 'production_achieved_qty', 'production_efficiency', 'delivery_overdue', 'process_wise_status', 'wip_cost_breakdown', 'maintenance_raised', 'maintenance_attended', 'maintenance_pending', 'expiring_documents'));
    }

    public function getServiceWipDetails(\Illuminate\Http\Request $request)
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
