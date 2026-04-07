<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCardEntry;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\ServiceProvider;
use App\Models\Task;
use App\Models\TaskAssignEmployee;
use Illuminate\Support\Facades\DB;

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
        $unitId = $request->unit_id;

        $wipQuery = JobCardEntry::with([
            'processGroup', 
            'serviceProvider', 
            'operations.operationStage', 
            'tasks.assignments'
        ])->where('grand_total_qty', '>', 0);

        if ($unitId) {
            $wipQuery->where('service_provider_id', $unitId);
        }

        if ($toDate) {
            $wipQuery->where('job_card_date', '<=', $toDate);
        }

        $jobCards = $wipQuery->get();
        $rows = [];

        foreach ($jobCards as $jc) {
            $stages = $jc->operations->pluck('operationStage')->unique('id')->filter();
            
            $openingDate = $fromDate ?: '1970-01-01';
            $closingDate = $toDate ?: date('Y-m-d');

            if ($stages->isEmpty()) {
                $processName = $jc->processGroup ? $jc->processGroup->name : 'N/A';
                
                $totalProduced = DB::table('production_receipt_items')
                    ->join('production_receipts', 'production_receipt_items.production_receipt_id', '=', 'production_receipts.id')
                    ->where('production_receipts.job_card_id', $jc->id)
                    ->where('production_receipts.status', 'Posted')
                    ->select(
                        DB::raw("SUM(CASE WHEN production_receipts.receipt_date < '$openingDate' THEN qty_to_receive ELSE 0 END) as opening_outward"),
                        DB::raw("SUM(CASE WHEN production_receipts.receipt_date >= '$openingDate' AND production_receipts.receipt_date <= '$closingDate' THEN qty_to_receive ELSE 0 END) as period_outward")
                    )->first();

                $openingInward = ($fromDate && $jc->job_card_date < $openingDate) ? $jc->grand_total_qty : 0;
                $periodInward = ($jc->job_card_date >= $openingDate && $jc->job_card_date <= $closingDate) ? $jc->grand_total_qty : 0;
                if (!$fromDate && $jc->job_card_date <= $closingDate) {
                    $periodInward = $jc->grand_total_qty;
                    $openingInward = 0;
                }

                $openingOutward = $totalProduced->opening_outward ?? 0;
                $periodOutward = $totalProduced->period_outward ?? 0;

                $openingWip = max(0, $openingInward - $openingOutward);
                $currentWip = $openingWip + $periodInward - $periodOutward;

                if ($openingWip + $periodInward + $openingOutward + $periodOutward > 0) {
                    $rows[] = [
                        'job_card_no' => $jc->job_card_no,
                        'process' => $processName,
                        'opening' => $openingWip,
                        'inward' => $periodInward,
                        'outward' => $periodOutward,
                        'current_wip' => $currentWip,
                    ];
                }
                continue;
            }

            $prevStageOpeningOutward = ($fromDate && $jc->job_card_date < $openingDate) ? $jc->grand_total_qty : 0;
            $prevStagePeriodOutward = ($jc->job_card_date >= $openingDate && $jc->job_card_date <= $closingDate) ? $jc->grand_total_qty : 0;
            
            if (!$fromDate && $jc->job_card_date <= $closingDate) {
                $prevStagePeriodOutward = $jc->grand_total_qty;
                $prevStageOpeningOutward = 0;
            }

            foreach ($stages as $stage) {
                $stageId = $stage->id;
                
                $stageTasks = $jc->tasks->where('stage_id', $stageId);
                $assignments = $stageTasks->flatMap->assignments;

                $serviceCompletions = $assignments->groupBy('service_id')->map(function($group) use ($openingDate, $closingDate) {
                    return [
                        'opening' => $group->where('updated_at', '<', $openingDate)->sum('completed_qty'),
                        'period' => $group->where('updated_at', '>=', $openingDate . ' 00:00:00')->where('updated_at', '<=', $closingDate . ' 23:59:59')->sum('completed_qty'),
                    ];
                });

                if ($serviceCompletions->isEmpty()) {
                    $openingOutward = 0;
                    $periodOutward = 0;
                } else {
                    $openingOutward = $serviceCompletions->min('opening');
                    $periodOutward = $serviceCompletions->min('period');
                }

                $openingInward = $prevStageOpeningOutward;
                $periodInward = $prevStagePeriodOutward;

                $openingWip = max(0, $openingInward - $openingOutward);
                $currentWip = $openingWip + $periodInward - $periodOutward;

                if ($openingWip + $periodInward + $openingOutward + $periodOutward > 0) {
                    $rows[] = [
                        'job_card_no' => $jc->job_card_no,
                        'process' => $stage->operation_stage_name,
                        'opening' => $openingWip,
                        'inward' => $periodInward,
                        'outward' => $periodOutward,
                        'current_wip' => $currentWip,
                    ];
                }

                $prevStageOpeningOutward = $openingOutward;
                $prevStagePeriodOutward = $periodOutward;
            }
        }

        $productionWip = collect($rows);
        
        // Individual Performance Report Logic
        $perfQuery = TaskAssignEmployee::with([
            'task.jobCard', 
            'task.stage.operationStage', 
            'task.operationStage',
            'employee', 
            'service'
        ]);
        
        if ($fromDate) {
            $perfQuery->where('issue_date', '>=', $fromDate);
        }
        if ($toDate) {
            $perfQuery->where('issue_date', '<=', $toDate);
        }
        if ($unitId) {
            $perfQuery->whereHas('task.jobCard', function($q) use ($unitId) {
                $q->where('service_provider_id', $unitId);
            });
        }
        
        $performanceIndividual = $perfQuery->get()->map(function($assign) {
            $assigned = (float)$assign->issue_qty;
            $completed = (float)$assign->completed_qty;
            $efficiency = ($assigned > 0) ? round(($completed / $assigned) * 100, 2) : 0;
            
            $stageName = 'N/A';
            if ($assign->task) {
                if ($assign->task->stage && $assign->task->stage->operationStage) {
                    $stageName = $assign->task->stage->operationStage->operation_stage_name;
                } elseif ($assign->task->operationStage) {
                    $stageName = $assign->task->operationStage->operation_stage_name;
                }
            }
            
            return [
                'job_card_no' => $assign->task->job_card_no ?? ($assign->task->jobCard->job_card_no ?? 'N/A'),
                'service' => $assign->service->service_name ?? 'N/A',
                'employee' => $assign->employee->name ?? 'N/A',
                'stage' => $stageName,
                'assigned_qty' => $assigned,
                'completed_qty' => $completed,
                'pending_qty' => max(0, $assigned - ($completed + (float)$assign->wastage_qty)),
                'efficiency' => $efficiency
            ];
        });

        // Section Wise Production Report Logic
        $sectionQuery = TaskAssignEmployee::with(['task.jobCard', 'task.operationStage', 'task.stage.operationStage', 'service']);
        
        if ($unitId) {
            $sectionQuery->whereHas('task.jobCard', function($q) use ($unitId) {
                $q->where('service_provider_id', $unitId);
            });
        }
        if ($fromDate) {
            $sectionQuery->where('issue_date', '>=', $fromDate);
        }
        if ($toDate) {
            $sectionQuery->where('issue_date', '<=', $toDate);
        }
        
        $sectionWiseProduction = $sectionQuery->get()->groupBy(function($item) {
            return ($item->task->job_card_entry_id ?? 0) . '-' . ($item->task->stage_id ?? 0) . '-' . ($item->service_id ?? 0);
        })->map(function($group) {
            $first = $group->first();
            
            $stageName = 'N/A';
            if ($first->task) {
                if ($first->task->stage && $first->task->stage->operationStage) {
                    $stageName = $first->task->stage->operationStage->operation_stage_name;
                } elseif ($first->task->operationStage) {
                    $stageName = $first->task->operationStage->operation_stage_name;
                }
            }

            return [
                'job_card_no' => $first->task->job_card_no ?? ($first->task->jobCard->job_card_no ?? 'N/A'),
                'service_name' => $first->service->service_name ?? 'N/A',
                'process_name' => $stageName,
                'task_plan' => $group->sum('issue_qty'),
                'inprocess' => $group->sum('inprogress_qty'),
                'completed' => $group->sum('completed_qty'),
                'hold' => ($first->task && $first->task->status == 'Hold') ? $group->sum('issue_qty') : 0
            ];
        })->values();

        // Job Card Completion Report Logic
        $jobCardCompletion = $jobCards->map(function($jc) {
            $totalReceived = DB::table('production_receipt_items')
                ->join('production_receipts', 'production_receipt_items.production_receipt_id', '=', 'production_receipts.id')
                ->where('production_receipts.job_card_id', $jc->id)
                ->where('production_receipts.status', 'Posted')
                ->sum('qty_to_receive');

            $lastReceiptDate = DB::table('production_receipts')
                ->where('job_card_id', $jc->id)
                ->where('status', 'Posted')
                ->max('receipt_date');

            $isCompleted = ($jc->grand_total_qty > 0) && ($totalReceived >= $jc->grand_total_qty);
            
            $statusLabel = 'Pending';
            $statusClass = 'warning';
            
            if ($isCompleted && $lastReceiptDate) {
                if ($jc->delivery_date) {
                    $delivery = strtotime($jc->delivery_date);
                    $actual = strtotime($lastReceiptDate);
                    $diff = floor(($delivery - $actual) / (60 * 60 * 24));
                    
                    if ($diff > 0) {
                        $statusLabel = $diff . " Days Early";
                        $statusClass = "success";
                    } elseif ($diff < 0) {
                        $statusLabel = abs($diff) . " Days Late";
                        $statusClass = "danger";
                    } else {
                        $statusLabel = "On Time";
                        $statusClass = "primary";
                    }
                } else {
                    $statusLabel = "Completed";
                    $statusClass = "success";
                }
            } elseif ($totalReceived > 0) {
                $percentage = round(($totalReceived / ($jc->grand_total_qty ?: 1)) * 100);
                $statusLabel = "In Progress ({$percentage}%)";
                $statusClass = "info";
            }

            return [
                'job_card_no' => $jc->job_card_no,
                'unit' => $jc->serviceProvider->name ?? 'N/A',
                'quantity' => $jc->grand_total_qty,
                'target_date' => $jc->delivery_date ? date('d-M-Y', strtotime($jc->delivery_date)) : 'N/A',
                'completed_date' => ($isCompleted && $lastReceiptDate) ? date('d-M-Y', strtotime($lastReceiptDate)) : '-',
                'status_label' => $statusLabel,
                'status_class' => $statusClass
            ];
        });

        // Brand Wise Unit Production Report Logic
        $receiptItemsQuery = ProductionReceiptItem::with(['productionReceipt.jobCard.brand', 'productionReceipt.jobCard.item', 'productionReceipt.jobCard.serviceProvider'])
            ->whereHas('productionReceipt', function($q) use ($unitId, $fromDate, $toDate) {
                if ($unitId) {
                    $q->where('service_provider_id', $unitId);
                }
                if ($fromDate) {
                    $q->where('receipt_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->where('receipt_date', '<=', $toDate);
                }
                $q->where('status', 'Posted');
            });

        $brandWiseProduction = $receiptItemsQuery->get()->groupBy(function($item) {
            $jc = $item->productionReceipt->jobCard;
            return ($jc->brand_id ?? 0) . '-' . ($jc->item_id ?? 0) . '-' . ($jc->service_provider_id ?? 0);
        })->flatMap(function($group) {
            $first = $group->first();
            $jc = $first->productionReceipt->jobCard;
            
            $rows = [];
            
            // If Job Card has Full Sleeve quantity
            if ($jc->total_qty_fs > 0) {
                $rows[] = [
                    'brand' => $jc->brand->brand_name ?? 'N/A',
                    'style' => $jc->item->name ?? 'N/A',
                    'sleeve' => 'Full Sleeve',
                    'qty' => $group->where('productionReceipt.jobCard.total_qty_fs', '>', 0)->sum('qty_to_receive'),
                    'unit' => $jc->serviceProvider->name ?? 'N/A'
                ];
            }
            
            // If Job Card has Half Sleeve quantity
            if ($jc->total_qty_hs > 0) {
                $rows[] = [
                    'brand' => $jc->brand->brand_name ?? 'N/A',
                    'style' => $jc->item->name ?? 'N/A',
                    'sleeve' => 'Half Sleeve',
                    'qty' => $group->where('productionReceipt.jobCard.total_qty_hs', '>', 0)->sum('qty_to_receive'),
                    'unit' => $jc->serviceProvider->name ?? 'N/A'
                ];
            }
            
            return $rows;
        })->values();

        if ($request->ajax()) {
            return response()->json([
                'production-wip' => view('reports.production_report.production_wip', compact('productionWip'))->render(),
                'performance-report' => view('reports.production_report.performance_individual', compact('performanceIndividual'))->render(),
                'incentive-report' => view('reports.production_report.incentive_report')->render(),
                'process-wise' => view('reports.production_report.section_wise_production', compact('sectionWiseProduction'))->render(),
                'production-cost' => view('reports.production_report.production_cost')->render(),
                'alteration-report' => view('reports.production_report.alteration_quantity')->render(),
                'completion-report' => view('reports.production_report.job_card_completion', compact('jobCardCompletion'))->render(),
                'brand-production' => view('reports.production_report.brand_wise_production', compact('brandWiseProduction'))->render(),
            ]);
        }

        $units = ServiceProvider::where('status', 'Active')->get();
        return view('reports/production_report', compact('productionWip', 'units', 'performanceIndividual', 'sectionWiseProduction', 'jobCardCompletion', 'brandWiseProduction'));
    }
}
