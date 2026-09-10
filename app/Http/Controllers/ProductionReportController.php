<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCardEntry;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\ServiceProvider;
use App\Models\Task;
use App\Models\TaskAssignEmployee;
use App\Models\StockEntryItem;
use App\Models\OperationStage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->route('type') ?? $request->get('report_type') ?? $request->get('type') ?? 'production-wip';
            return $this->ajaxReportData($request, $type);
        }

        $units = ServiceProvider::where('status', 'Active')->orderBy('name', 'asc')->get();
        return view('reports/production_report', compact('units'));
    }

    public function ajaxReportData(Request $request, $type)
    {
        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLength = $request->get('length');
        $length = ($rawLength !== null && intval($rawLength) == -1) ? -1 : intval($rawLength > 0 ? $rawLength : 10);
        $isExport = ($request->get('export') == 1) || ($request->get('all') == 1) || ($length < 0);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');
        $search = trim($search);

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
            $unitId = $request->unit_id;

            switch ($type) {
                case 'production-wip':
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

                    $openingDate = $fromDate ?: '1970-01-01';
                    $closingDate = $toDate ?: date('Y-m-d');

                    foreach ($jobCards as $jc) {
                        $stages = $jc->operations->pluck('operationStage')->unique('id')->filter();

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
                                    'job_card_no' => '<strong>' . htmlspecialchars($jc->job_card_no ?? '') . '</strong>',
                                    'process' => htmlspecialchars($processName),
                                    'opening' => number_format($openingWip),
                                    'inward' => '<span class="text-success">' . number_format($periodInward) . '</span>',
                                    'outward' => '<span class="text-primary">' . number_format($periodOutward) . '</span>',
                                    'current_wip' => '<span class="fw-bold">' . number_format($currentWip) . '</span>',
                                    '_search_text' => strtolower(($jc->job_card_no ?? '') . ' ' . $processName)
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
                                    'job_card_no' => '<strong>' . htmlspecialchars($jc->job_card_no ?? '') . '</strong>',
                                    'process' => htmlspecialchars($stage->operation_stage_name ?? 'N/A'),
                                    'opening' => number_format($openingWip),
                                    'inward' => '<span class="text-success">' . number_format($periodInward) . '</span>',
                                    'outward' => '<span class="text-primary">' . number_format($periodOutward) . '</span>',
                                    'current_wip' => '<span class="fw-bold">' . number_format($currentWip) . '</span>',
                                    '_search_text' => strtolower(($jc->job_card_no ?? '') . ' ' . ($stage->operation_stage_name ?? ''))
                                ];
                            }

                            $prevStageOpeningOutward = $openingOutward;
                            $prevStagePeriodOutward = $periodOutward;
                        }
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData
                    ]);

                case 'performance-report':
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
                    if ($search !== '') {
                        $perfQuery->where(function($q) use ($search) {
                            $q->whereHas('employee', function($eq) use ($search) {
                                $eq->where('name', 'like', "%{$search}%");
                            })->orWhereHas('service', function($sq) use ($search) {
                                $sq->where('service_name', 'like', "%{$search}%");
                            })->orWhereHas('task', function($tq) use ($search) {
                                $tq->where('job_card_no', 'like', "%{$search}%");
                            });
                        });
                    }

                    $totalRecords = $perfQuery->count();
                    if ($isExport) {
                        $items = $perfQuery->orderBy('id', 'desc')->get();
                    } else {
                        $items = $perfQuery->orderBy('id', 'desc')->offset($start)->limit($length)->get();
                    }

                    $data = [];
                    foreach ($items as $assign) {
                        $assigned = (float)$assign->issue_qty;
                        $completed = (float)$assign->completed_qty;
                        $pending = max(0, $assigned - ($completed + (float)$assign->wastage_qty));
                        $efficiency = ($assigned > 0) ? round(($completed / $assigned) * 100, 2) : 0;
                        
                        $stageName = 'N/A';
                        if ($assign->task) {
                            if ($assign->task->stage && $assign->task->stage->operationStage) {
                                $stageName = $assign->task->stage->operationStage->operation_stage_name;
                            } elseif ($assign->task->operationStage) {
                                $stageName = $assign->task->operationStage->operation_stage_name;
                            }
                        }
                        
                        $badgeClass = 'bg-label-danger';
                        if ($efficiency >= 90) $badgeClass = 'bg-label-success';
                        elseif ($efficiency >= 70) $badgeClass = 'bg-label-warning';

                        $data[] = [
                            'job_card_no' => '<strong>' . htmlspecialchars($assign->task->job_card_no ?? ($assign->task->jobCard->job_card_no ?? 'N/A')) . '</strong>',
                            'service' => htmlspecialchars($assign->service->service_name ?? 'N/A'),
                            'employee' => htmlspecialchars($assign->employee->name ?? 'N/A'),
                            'stage' => htmlspecialchars($stageName),
                            'assigned_qty' => number_format($assigned),
                            'completed_qty' => '<span class="text-success">' . number_format($completed) . '</span>',
                            'pending_qty' => '<span class="text-danger">' . number_format($pending) . '</span>',
                            'efficiency' => '<span class="badge ' . $badgeClass . ' rounded-pill">' . $efficiency . '%</span>'
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data
                    ]);

                case 'process-wise':
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
                    
                    $groups = $sectionQuery->get()->groupBy(function($item) {
                        return ($item->task->job_card_entry_id ?? 0) . '-' . ($item->task->stage_id ?? 0) . '-' . ($item->service_id ?? 0);
                    });

                    $rows = [];
                    foreach ($groups as $group) {
                        $first = $group->first();
                        
                        $stageName = 'N/A';
                        if ($first->task) {
                            if ($first->task->stage && $first->task->stage->operationStage) {
                                $stageName = $first->task->stage->operationStage->operation_stage_name;
                            } elseif ($first->task->operationStage) {
                                $stageName = $first->task->operationStage->operation_stage_name;
                            }
                        }

                        $jcNo = $first->task->job_card_no ?? ($first->task->jobCard->job_card_no ?? 'N/A');
                        $servName = $first->service->service_name ?? 'N/A';

                        $rows[] = [
                            'job_card_no' => '<strong>' . htmlspecialchars($jcNo) . '</strong>',
                            'service_name' => htmlspecialchars($servName),
                            'process_name' => htmlspecialchars($stageName),
                            'task_plan' => '<span class="text-primary">' . number_format($group->sum('issue_qty')) . '</span>',
                            'inprocess' => '<span class="text-warning">' . number_format($group->sum('inprogress_qty')) . '</span>',
                            'completed' => '<span class="text-success">' . number_format($group->sum('completed_qty')) . '</span>',
                            'hold' => '<span class="text-danger">' . number_format(($first->task && $first->task->status == 'Hold') ? $group->sum('issue_qty') : 0) . '</span>',
                            '_search_text' => strtolower($jcNo . ' ' . $servName . ' ' . $stageName)
                        ];
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData
                    ]);

                case 'completion-report':
                    $compQuery = JobCardEntry::with(['serviceProvider'])->where('grand_total_qty', '>', 0);
                    if ($unitId) {
                        $compQuery->where('service_provider_id', $unitId);
                    }
                    if ($fromDate) {
                        $compQuery->where('job_card_date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $compQuery->where('job_card_date', '<=', $toDate);
                    }
                    if ($search !== '') {
                        $compQuery->where(function($q) use ($search) {
                            $q->where('job_card_no', 'like', "%{$search}%")
                              ->orWhereHas('serviceProvider', function($sq) use ($search) {
                                  $sq->where('name', 'like', "%{$search}%");
                              });
                        });
                    }

                    $totalRecords = $compQuery->count();
                    if ($isExport) {
                        $jobCards = $compQuery->orderBy('id', 'desc')->get();
                    } else {
                        $jobCards = $compQuery->orderBy('id', 'desc')->offset($start)->limit($length)->get();
                    }

                    $data = [];
                    foreach ($jobCards as $jc) {
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

                        $data[] = [
                            'job_card_no' => '<strong>' . htmlspecialchars($jc->job_card_no ?? '') . '</strong>',
                            'unit' => htmlspecialchars($jc->serviceProvider->name ?? 'N/A'),
                            'quantity' => number_format($jc->grand_total_qty ?? 0),
                            'target_date' => $jc->delivery_date ? date('d-M-Y', strtotime($jc->delivery_date)) : 'N/A',
                            'completed_date' => ($isCompleted && $lastReceiptDate) ? date('d-M-Y', strtotime($lastReceiptDate)) : '-',
                            'days_taken' => '<span class="badge bg-label-' . $statusClass . ' rounded-pill">' . $statusLabel . '</span>'
                        ];
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $data
                    ]);

                case 'brand-production':
                    $receiptItemsQuery = ProductionReceiptItem::with([
                        'productionReceipt.jobCard.brand', 
                        'productionReceipt.jobCard.item.style', 
                        'productionReceipt.jobCard.serviceProvider', 
                        'productionReceipt.jobCard.fabricDetails'
                    ])
                    ->whereHas('productionReceipt', function($q) use ($unitId, $fromDate, $toDate) {
                        if ($unitId) {
                            $q->whereHas('jobCard', function($jcQuery) use ($unitId) {
                                $jcQuery->where('service_provider_id', $unitId);
                            });
                        }
                        if ($fromDate) {
                            $q->where('receipt_date', '>=', $fromDate);
                        }
                        if ($toDate) {
                            $q->where('receipt_date', '<=', $toDate);
                        }
                        $q->where('status', 'Posted');
                    });

                    $groups = $receiptItemsQuery->get()->groupBy(function($item) {
                        $jc = $item->productionReceipt->jobCard;
                        return ($jc->brand_id ?? 0) . '-' . ($jc->item_id ?? 0) . '-' . ($jc->service_provider_id ?? 0);
                    });

                    $rows = [];
                    foreach ($groups as $group) {
                        $first = $group->first();
                        $jc = $first->productionReceipt->jobCard;
                        
                        $styleName = 'N/A';
                        if ($jc && $jc->item && $jc->item->style) {
                            $styleName = $jc->item->style->style_name ?? $jc->item->name;
                        } elseif ($jc && $jc->fabricDetails && $jc->fabricDetails->first()) {
                            $firstFabric = $jc->fabricDetails->first();
                            if ($firstFabric && $firstFabric->art_no) {
                                $stockItem = StockEntryItem::with('style')
                                    ->where('art_no', $firstFabric->art_no)
                                    ->whereNotNull('style_id')
                                    ->first();
                                if ($stockItem && $stockItem->style) {
                                    $styleName = $stockItem->style->style_name;
                                } else {
                                    $styleName = $firstFabric->art_no;
                                }
                            }
                        }

                        $brandName = $jc->brand->brand_name ?? 'N/A';
                        $unitName = $jc->serviceProvider->name ?? 'N/A';

                        if ($jc && $jc->total_qty_fs > 0) {
                            $qtyFs = $group->where('productionReceipt.jobCard.total_qty_fs', '>', 0)->sum('qty_to_receive');
                            $rows[] = [
                                'brand' => '<strong>' . htmlspecialchars($brandName) . '</strong>',
                                'style' => htmlspecialchars($styleName),
                                'sleeve' => 'Full Sleeve',
                                'qty' => number_format($qtyFs),
                                'unit' => htmlspecialchars($unitName),
                                '_search_text' => strtolower($brandName . ' ' . $styleName . ' Full Sleeve ' . $unitName)
                            ];
                        }

                        if ($jc && $jc->total_qty_hs > 0) {
                            $qtyHs = $group->where('productionReceipt.jobCard.total_qty_hs', '>', 0)->sum('qty_to_receive');
                            $rows[] = [
                                'brand' => '<strong>' . htmlspecialchars($brandName) . '</strong>',
                                'style' => htmlspecialchars($styleName),
                                'sleeve' => 'Half Sleeve',
                                'qty' => number_format($qtyHs),
                                'unit' => htmlspecialchars($unitName),
                                '_search_text' => strtolower($brandName . ' ' . $styleName . ' Half Sleeve ' . $unitName)
                            ];
                        }
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData
                    ]);

                case 'incentive-report':
                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => 1,
                        'recordsFiltered' => 1,
                        'data' => [
                            [
                                'employee' => '<strong>Karthick</strong>',
                                'total_production' => '1,200',
                                'incentive_rate' => '₹2.50',
                                'total_incentive' => '<span class="text-primary fw-bold">₹3,000</span>'
                            ]
                        ]
                    ]);

                case 'production-cost':
                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => 1,
                        'recordsFiltered' => 1,
                        'data' => [
                            [
                                'unit' => 'Unit II (Stitching)',
                                'process' => 'Cuff Attachment',
                                'material_cost' => '₹45,000',
                                'labor_cost' => '₹12,000',
                                'overheads' => '₹5,000',
                                'total_cost' => '<span class="text-danger fw-bold">₹62,000</span>'
                            ]
                        ]
                    ]);

                case 'alteration-report':
                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => 1,
                        'recordsFiltered' => 1,
                        'data' => [
                            [
                                'job_card_no' => '<strong>JC/2026/005</strong>',
                                'unit' => 'Unit II',
                                'total_produced' => '500',
                                'alteration_qty' => '<span class="text-danger">15</span>',
                                'alteration_pc' => '3%'
                            ]
                        ]
                    ]);

                case 'department-efficiency':
                    $stages = OperationStage::active()->orderBy('id', 'asc')->get();
                    $rows = [];
                    $totalTarget = 0;
                    $totalPlan = 0;
                    $totalActual = 0;

                    foreach ($stages as $stage) {
                        $tasksQuery = Task::with(['jobCard.serviceProvider', 'assignments', 'stage'])
                            ->whereHas('stage', function($q) use ($stage) {
                                $q->where('operation_stage_id', $stage->id);
                            });

                        if ($fromDate) {
                            $tasksQuery->where(function($q) use ($fromDate) {
                                $q->where('issue_date', '>=', $fromDate)
                                  ->orWhere(function($sub) use ($fromDate) {
                                      $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                  });
                            });
                        }
                        if ($toDate) {
                            $tasksQuery->where(function($q) use ($toDate) {
                                $q->where('issue_date', '<=', $toDate)
                                  ->orWhere(function($sub) use ($toDate) {
                                      $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                  });
                            });
                        }
                        if ($unitId) {
                            $tasksQuery->whereHas('jobCard', function($q) use ($unitId) {
                                $q->where('service_provider_id', $unitId);
                            });
                        }

                        $tasks = $tasksQuery->get();

                        $stagePlan = 0;
                        $stageActual = 0;
                        $delayedCount = 0;

                        foreach ($tasks as $task) {
                            $taskPlan = (float) ($task->issue_qty ?? 0);
                            
                            $taskActual = 0;
                            if ($task->status === 'Completed') {
                                $taskActual = $taskPlan;
                            } elseif ($task->assignments->isNotEmpty()) {
                                $services = $task->assignments->groupBy('service_id');
                                if ($services->isNotEmpty()) {
                                    $taskActual = (float) $services->map(function($grp) {
                                        return $grp->sum('completed_qty');
                                    })->min();
                                } else {
                                    $taskActual = (float) $task->assignments->sum('completed_qty');
                                }
                            }

                            $stagePlan += $taskPlan;
                            $stageActual += $taskActual;

                            // Check if delayed
                            $isDelayed = false;
                            if ($task->due_date) {
                                $dueDate = Carbon::parse($task->due_date)->startOfDay();
                                $now = Carbon::now()->startOfDay();
                                if ($task->status === 'Completed') {
                                    $completedDate = Carbon::parse($task->updated_at)->startOfDay();
                                    if ($completedDate->gt($dueDate)) {
                                        $isDelayed = true;
                                    }
                                } else {
                                    if ($now->gt($dueDate)) {
                                        $isDelayed = true;
                                    }
                                }
                            }
                            if ($isDelayed || $task->status === 'Hold') {
                                $delayedCount++;
                            }
                        }

                        $targetVal = $stage->target !== null ? (float)$stage->target : null;
                        if ($targetVal !== null) {
                            $totalTarget += $targetVal;
                        }
                        $totalPlan += $stagePlan;
                        $totalActual += $stageActual;

                        $stageEfficiency = ($stagePlan > 0) ? round(($stageActual / $stagePlan) * 100, 1) : 0;
                        
                        $effBadgeClass = 'bg-label-danger';
                        if ($stageEfficiency >= 95) $effBadgeClass = 'bg-label-success';
                        elseif ($stageEfficiency >= 75) $effBadgeClass = 'bg-label-warning';

                        $tasksCount = $tasks->count();
                        if ($tasksCount === 0) {
                            $delayDetailsHtml = '<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 view-dept-tasks" data-stage-id="' . $stage->id . '" data-stage-name="' . htmlspecialchars($stage->operation_stage_name) . '"><i class="ri-file-list-line me-1"></i> No Tasks</button>';
                        } else {
                            if ($delayedCount > 0) {
                                $delayDetailsHtml = '<button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 view-dept-tasks" data-stage-id="' . $stage->id . '" data-stage-name="' . htmlspecialchars($stage->operation_stage_name) . '" title="Click to view ' . $delayedCount . ' delayed tasks"><i class="ri-alarm-warning-line me-1"></i> ' . $delayedCount . ' Delayed / View (' . $tasksCount . ')</button>';
                            } else {
                                $delayDetailsHtml = '<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 view-dept-tasks" data-stage-id="' . $stage->id . '" data-stage-name="' . htmlspecialchars($stage->operation_stage_name) . '" title="Click to view tasks & delay status"><i class="ri-eye-line me-1"></i> View Tasks (' . $tasksCount . ')</button>';
                            }
                        }

                        $deptHtml = '<a href="javascript:void(0)" class="fw-bold text-primary view-dept-tasks text-decoration-none" data-stage-id="' . $stage->id . '" data-stage-name="' . htmlspecialchars($stage->operation_stage_name) . '">' . htmlspecialchars($stage->operation_stage_name) . ' <i class="ri-external-link-line small opacity-75 ms-1"></i></a>';

                        $rows[] = [
                            'stage_id' => $stage->id,
                            'operation' => $deptHtml,
                            'target' => $targetVal !== null ? number_format($targetVal) . ' Pcs' : '<span class="text-muted">-</span>',
                            'plan' => '<span class="text-primary fw-bold">' . number_format($stagePlan) . ' Pcs</span>',
                            'actual' => '<span class="text-success fw-bold">' . number_format($stageActual) . ' Pcs</span>',
                            'efficiency' => '<span class="badge ' . $effBadgeClass . ' rounded-pill px-3 py-1 fs-6">' . $stageEfficiency . '%</span>',
                            'working_hours' => '<span class="fw-semibold">8</span>',
                            'delay_details' => $delayDetailsHtml,
                            '_search_text' => strtolower($stage->operation_stage_name . ' ' . $stageEfficiency . '%')
                        ];
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    $overallEfficiency = ($totalPlan > 0) ? round(($totalActual / $totalPlan) * 100, 1) : 0;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'meta' => [
                            'total_target' => number_format($totalTarget) . ' Pcs',
                            'total_plan' => number_format($totalPlan) . ' Pcs',
                            'total_actual' => number_format($totalActual) . ' Pcs',
                            'overall_efficiency' => $overallEfficiency . '%',
                            'efficiency_val' => $overallEfficiency
                        ]
                    ]);

                case 'department-tasks':
                    $stageId = intval($request->stage_id ?? 0);
                    $stage = OperationStage::find($stageId);
                    if (!$stage) {
                        return response()->json(['success' => false, 'message' => 'Operation stage not found'], 404);
                    }

                    $tasksQuery = Task::with(['jobCard.serviceProvider', 'assignments', 'stage'])
                        ->whereHas('stage', function($q) use ($stageId) {
                            $q->where('operation_stage_id', $stageId);
                        });

                    if ($fromDate) {
                        $tasksQuery->where(function($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                              ->orWhere(function($sub) use ($fromDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                              });
                        });
                    }
                    if ($toDate) {
                        $tasksQuery->where(function($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                              ->orWhere(function($sub) use ($toDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                              });
                        });
                    }
                    if ($unitId) {
                        $tasksQuery->whereHas('jobCard', function($q) use ($unitId) {
                            $q->where('service_provider_id', $unitId);
                        });
                    }

                    $tasks = $tasksQuery->orderBy('id', 'desc')->get();

                    $tasksData = [];
                    $totalPlan = 0;
                    $totalActual = 0;

                    foreach ($tasks as $task) {
                        $taskPlan = (float) ($task->issue_qty ?? 0);
                        
                        $taskActual = 0;
                        if ($task->status === 'Completed') {
                            $taskActual = $taskPlan;
                        } elseif ($task->assignments->isNotEmpty()) {
                            $services = $task->assignments->groupBy('service_id');
                            if ($services->isNotEmpty()) {
                                $taskActual = (float) $services->map(function($grp) {
                                    return $grp->sum('completed_qty');
                                })->min();
                            } else {
                                $taskActual = (float) $task->assignments->sum('completed_qty');
                            }
                        }

                        $efficiency = ($taskPlan > 0) ? round(($taskActual / $taskPlan) * 100, 1) : 0;
                        $totalPlan += $taskPlan;
                        $totalActual += $taskActual;

                        // Reason / Delay Calculation
                        $delayReason = '-';
                        $delayBadge = 'secondary';
                        
                        if (!empty($task->remarks)) {
                            $delayReason = $task->remarks;
                            $delayBadge = 'info';
                        } elseif ($task->jobCard && !empty($task->jobCard->remarks)) {
                            $delayReason = $task->jobCard->remarks;
                            $delayBadge = 'info';
                        } elseif ($task->status === 'Hold') {
                            $delayReason = 'On Hold';
                            $delayBadge = 'warning';
                        } elseif ($task->due_date) {
                            $dueDate = Carbon::parse($task->due_date)->startOfDay();
                            $now = Carbon::now()->startOfDay();
                            if ($task->status === 'Completed') {
                                $completedDate = Carbon::parse($task->updated_at)->startOfDay();
                                if ($completedDate->gt($dueDate)) {
                                    $diff = $completedDate->diffInDays($dueDate);
                                    $delayReason = "Delayed by {$diff} day" . ($diff > 1 ? 's' : '');
                                    $delayBadge = 'danger';
                                } else {
                                    $delayReason = "Completed on time";
                                    $delayBadge = 'success';
                                }
                            } else {
                                if ($now->gt($dueDate)) {
                                    $diff = $now->diffInDays($dueDate);
                                    $delayReason = "Overdue by {$diff} day" . ($diff > 1 ? 's' : '');
                                    $delayBadge = 'danger';
                                } else {
                                    $delayReason = "On Track";
                                    $delayBadge = 'primary';
                                }
                            }
                        } elseif ($task->status === 'Completed') {
                            $delayReason = 'Completed';
                            $delayBadge = 'success';
                        }

                        $statusBadge = 'secondary';
                        if ($task->status === 'Completed') $statusBadge = 'success';
                        elseif ($task->status === 'In Progress') $statusBadge = 'primary';
                        elseif ($task->status === 'Hold') $statusBadge = 'warning';
                        elseif ($task->status === 'Planned') $statusBadge = 'info';

                        $jcNo = $task->job_card_no ?? ($task->jobCard->job_card_no ?? 'N/A');
                        $unitName = $task->jobCard && $task->jobCard->serviceProvider ? $task->jobCard->serviceProvider->name : 'N/A';
                        $taskNo = $task->task_no ?? 'N/A';
                        $issueDateFormatted = $task->issue_date ? Carbon::parse($task->issue_date)->format('d-m-Y') : '-';
                        $dueDateFormatted = $task->due_date ? Carbon::parse($task->due_date)->format('d-m-Y') : '-';

                        $tasksData[] = [
                            'id' => $task->id,
                            'task_no' => $taskNo,
                            'job_card_no' => $jcNo,
                            'unit' => $unitName,
                            'issue_date' => $issueDateFormatted,
                            'due_date' => $dueDateFormatted,
                            'plan' => number_format($taskPlan) . ' Pcs',
                            'actual' => number_format($taskActual) . ' Pcs',
                            'efficiency' => $efficiency . '%',
                            'status' => $task->status ?: 'Planned',
                            'status_badge' => $statusBadge,
                            'delay_reason' => $delayReason,
                            'delay_badge' => $delayBadge,
                            'view_url' => url('task_management/view_details/' . $task->id),
                            '_search_text' => strtolower($taskNo . ' ' . $jcNo . ' ' . $unitName . ' ' . $issueDateFormatted . ' ' . $dueDateFormatted . ' ' . ($task->status ?: 'Planned') . ' ' . $delayReason)
                        ];
                    }

                    $totalRecords = count($tasksData);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($tasksData, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $tasksData;
                    }
                    $recordsFiltered = count($filteredRows);

                    $isExport = ($request->get('export') == 1) || ($request->get('all') == 1) || ($length < 0);
                    if ($isExport) {
                        $pageData = $filteredRows;
                    } else {
                        $pageData = array_slice($filteredRows, $start, $length);
                    }

                    $deptEfficiency = ($totalPlan > 0) ? round(($totalActual / $totalPlan) * 100, 1) : 0;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'success' => true,
                        'stage_name' => $stage->operation_stage_name,
                        'stage_target' => $stage->target !== null ? number_format($stage->target) . ' Pcs' : '-',
                        'summary' => [
                            'total_tasks' => $totalRecords,
                            'total_plan' => number_format($totalPlan) . ' Pcs',
                            'total_actual' => number_format($totalActual) . ' Pcs',
                            'efficiency' => $deptEfficiency . '%'
                        ],
                        'tasks' => $filteredRows
                    ]);

                case 'casino-cutting-wip':
                    $cuttingQuery = JobCardEntry::with([
                        'brand',
                        'season',
                        'serviceProvider',
                        'issueItems',
                        'purchaseOrder.items.style',
                        'item.style',
                        'fabricDetails',
                        'tasks.assignments.service'
                    ]);

                    if ($unitId) {
                        $cuttingQuery->where('service_provider_id', $unitId);
                    }
                    if ($fromDate) {
                        $cuttingQuery->where('job_card_date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $cuttingQuery->where('job_card_date', '<=', $toDate);
                    }

                    $jobCards = $cuttingQuery->orderBy('id', 'desc')->get();
                    $rows = [];

                    // Preload styles for stockEntryItems if needed
                    $fabricArtNos = [];
                    foreach ($jobCards as $jc) {
                        if ((!$jc->purchaseOrder || $jc->purchaseOrder->items->isEmpty()) && (!$jc->item || !$jc->item->style)) {
                            foreach ($jc->fabricDetails as $fd) {
                                if ($fd->art_no) {
                                    $fabricArtNos[] = trim($fd->art_no);
                                }
                            }
                        }
                    }
                    $artStyleMap = [];
                    if (!empty($fabricArtNos)) {
                        $stockItems = StockEntryItem::with('style')
                            ->whereIn('art_no', array_unique($fabricArtNos))
                            ->whereNotNull('style_id')
                            ->get();
                        foreach ($stockItems as $si) {
                            if ($si->style) {
                                $artStyleMap[trim($si->art_no)] = $si->style;
                            }
                        }
                    }

                    foreach ($jobCards as $jc) {
                        // 1. Job Card No
                        $jcNo = $jc->job_card_no ?? 'N/A';

                        // 2. Issue Date
                        $issueDateStr = $jc->job_card_date ? date('d-M-Y', strtotime($jc->job_card_date)) : '-';

                        // 3. Delivery Date
                        $deliveryDateStr = $jc->delivery_date ? date('d-M-Y', strtotime($jc->delivery_date)) : '-';

                        // 4. Age (Day)
                        $ageDays = ($jc->no_of_days !== null && $jc->no_of_days !== '') ? intval($jc->no_of_days) : ($jc->job_card_date ? Carbon::parse($jc->job_card_date)->diffInDays(Carbon::now()) : 0);

                        // 5. Brand
                        $brandName = $jc->brand->brand_name ?? 'N/A';

                        // 6. Season
                        $seasonName = $jc->season->season_name ?? '-';

                        // 7. Pattern (from styles table during PO or fallback)
                        $patternNames = [];
                        $resolvedStyles = [];

                        if ($jc->purchaseOrder && $jc->purchaseOrder->items->isNotEmpty()) {
                            foreach ($jc->purchaseOrder->items as $poItem) {
                                if ($poItem->style) {
                                    $patternNames[] = $poItem->style->style_name;
                                    $resolvedStyles[] = $poItem->style;
                                }
                            }
                        }

                        if (empty($patternNames) && $jc->item && $jc->item->style) {
                            $patternNames[] = $jc->item->style->style_name;
                            $resolvedStyles[] = $jc->item->style;
                        }

                        if (empty($patternNames) && $jc->fabricDetails->isNotEmpty()) {
                            foreach ($jc->fabricDetails as $fd) {
                                $art = trim($fd->art_no ?? '');
                                if (isset($artStyleMap[$art])) {
                                    $patternNames[] = $artStyleMap[$art]->style_name;
                                    $resolvedStyles[] = $artStyleMap[$art];
                                }
                            }
                        }

                        $patternNames = array_unique(array_filter($patternNames));
                        $patternDisplay = !empty($patternNames) ? implode(', ', $patternNames) : '-';

                        // 8. Fabric
                        $fabricDisplay = '-';

                        // 9. Issue Mts (total sum of Qty To Issue)
                        $issueMts = floatval($jc->issueItems->sum('qty_issue'));

                        // 10. Estimate Qty (from style master average consumption / 1.5)
                        $styleAvgCons = 0;
                        if (!empty($resolvedStyles)) {
                            foreach ($resolvedStyles as $st) {
                                if (floatval($st->average_consumption ?? 0) > 0) {
                                    $styleAvgCons = floatval($st->average_consumption);
                                    break;
                                }
                            }
                        }
                        $estQty = ($styleAvgCons > 0) ? round($styleAvgCons / 1.5) : 0;

                        // 11. Cut Qty (sum of Produced Qty)
                        $cutQty = floatval($jc->issueItems->sum('produced_qty'));

                        // 12. Bundle (Task Management bundle service allocation qty)
                        $bundledQty = 0;
                        if ($jc->tasks->isNotEmpty()) {
                            foreach ($jc->tasks as $t) {
                                if ($t->assignments->isNotEmpty()) {
                                    foreach ($t->assignments as $asgn) {
                                        if ($asgn->service && stripos($asgn->service->service_name, 'bundle') !== false) {
                                            $bundledQty += floatval($asgn->issue_qty ?? 0);
                                        }
                                    }
                                }
                            }
                        }

                        // 13. Balance Bundle (pending bundle)
                        $baseCutOrEst = ($cutQty > 0) ? $cutQty : $estQty;
                        $balanceBundle = max(0, $baseCutOrEst - $bundledQty);

                        // 14. Full Sleeve
                        $fullQty = floatval($jc->total_qty_fs ?? $jc->fs_qty ?? 0);

                        // 15. Half Sleeve
                        $halfQty = floatval($jc->total_qty_hs ?? $jc->hs_qty ?? 0);

                        // 16. Unit Assigned
                        $unitAssigned = $jc->serviceProvider->name ?? 'Not Assigned';

                        // 17. Status
                        $rawStatus = trim($jc->status ?: 'Waiting');
                        $statusClass = 'secondary';
                        if (stripos($rawStatus, 'complete') !== false) {
                            $statusClass = 'success';
                        } elseif (stripos($rawStatus, 'progress') !== false || stripos($rawStatus, 'running') !== false) {
                            $statusClass = 'primary';
                        } elseif (stripos($rawStatus, 'pending') !== false || stripos($rawStatus, 'hold') !== false) {
                            $statusClass = 'warning';
                        } elseif (stripos($rawStatus, 'overdue') !== false) {
                            $statusClass = 'danger';
                        }
                        $statusBadge = '<span class="badge bg-label-' . $statusClass . ' rounded-pill px-2 py-1">' . htmlspecialchars($rawStatus) . '</span>';

                        // 18. Priority (compare issue and delivery date: overdue -> critical, near by delivery date -> high, otherwise -> normal)
                        $priorityText = 'Normal';
                        $priorityBadge = '<span class="badge bg-label-secondary rounded-pill px-2 py-1">Normal</span>';

                        $isJcCompleted = stripos($rawStatus, 'complete') !== false;
                        if ($isJcCompleted) {
                            $priorityText = 'Completed';
                            $priorityBadge = '<span class="text-success fw-bold"><i class="ri-check-line me-1"></i>Completed</span>';
                        } elseif ($jc->delivery_date) {
                            $today = Carbon::now()->startOfDay();
                            $delDate = Carbon::parse($jc->delivery_date)->startOfDay();
                            if ($today->gt($delDate)) {
                                $priorityText = 'Critical';
                                $priorityBadge = '<span class="badge bg-label-danger rounded-pill px-2 py-1"><i class="ri-record-circle-fill text-danger me-1"></i>Critical</span>';
                            } elseif ($today->diffInDays($delDate, false) <= 2) {
                                $priorityText = 'High';
                                $priorityBadge = '<span class="badge bg-label-warning rounded-pill px-2 py-1">High</span>';
                            }
                        }

                        // 19. Remarks
                        $remarksText = $jc->remarks ?: '-';

                        $rows[] = [
                            'job_card_no' => '<strong>' . htmlspecialchars($jcNo) . '</strong>',
                            'issue_date' => $issueDateStr,
                            'delivery_date' => $deliveryDateStr,
                            'age_days' => $ageDays,
                            'brand' => htmlspecialchars($brandName),
                            'season' => htmlspecialchars($seasonName),
                            'pattern' => htmlspecialchars($patternDisplay),
                            'fabric' => $fabricDisplay,
                            'issue_mts' => number_format($issueMts),
                            'estimate_qty' => number_format($estQty),
                            'cut_qty' => number_format($cutQty),
                            'bundle' => number_format($bundledQty),
                            'balance_bundle' => number_format($balanceBundle),
                            'full_sleeve' => number_format($fullQty),
                            'half_sleeve' => number_format($halfQty),
                            'unit_assigned' => htmlspecialchars($unitAssigned),
                            'status' => $statusBadge,
                            'priority' => $priorityBadge,
                            'remarks' => htmlspecialchars($remarksText),
                            '_search_text' => strtolower($jcNo . ' ' . $brandName . ' ' . $seasonName . ' ' . $patternDisplay . ' ' . $unitAssigned . ' ' . $rawStatus . ' ' . $priorityText . ' ' . $remarksText)
                        ];
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);

                    $isExport = ($request->get('export') == 1) || ($request->get('all') == 1) || ($length < 0);
                    if ($isExport) {
                        $pageData = $filteredRows;
                    } else {
                        $pageData = array_slice($filteredRows, $start, $length);
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData
                    ]);

                case 'employee-efficiency':
                    $query = TaskAssignEmployee::with([
                        'employee',
                        'service',
                        'task.jobCard.serviceProvider',
                        'task.stage.operationStage',
                        'task.operationStage'
                    ])->whereNotNull('issued_to');

                    if ($fromDate) {
                        $query->where(function($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                              ->orWhere(function($sub) use ($fromDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                              });
                        });
                    }
                    if ($toDate) {
                        $query->where(function($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                              ->orWhere(function($sub) use ($toDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                              });
                        });
                    }
                    if ($unitId) {
                        $query->where(function($q) use ($unitId) {
                            $q->whereHas('task.jobCard', function($jcQ) use ($unitId) {
                                $jcQ->where('service_provider_id', $unitId);
                            })->orWhereHas('employee', function($empQ) use ($unitId) {
                                $empQ->where('service_provider_id', $unitId);
                            });
                        });
                    }

                    $allAssignments = $query->get();
                    $grouped = $allAssignments->groupBy('issued_to');

                    $rows = [];
                    $summaryTotalHours = 0;
                    $summaryTotalTarget = 0;
                    $summaryTotalCompleted = 0;

                    foreach ($grouped as $empId => $assignments) {
                        $emp = $assignments->first()->employee;
                        if (!$emp) continue;

                        $tasksDone = $assignments->map(function($a) {
                            return $a->service ? $a->service->service_name : ($a->task && $a->task->task_no ? $a->task->task_no : null);
                        })->filter()->unique()->values();

                        $tasksStr = $tasksDone->implode(' + ');

                        $totalHours = (float) $assignments->sum(function($a) {
                            return (float)($a->total_hrs ?? 0);
                        });

                        $targetQty = (float) $assignments->sum(function($a) {
                            return (float)($a->issue_qty ?? 0);
                        });

                        $completedQty = (float) $assignments->sum(function($a) {
                            return (float)($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
                        });

                        $pendingQty = max(0, $targetQty - $completedQty);
                        $efficiency = ($targetQty > 0) ? round(($completedQty / $targetQty) * 100, 1) : 0;

                        $summaryTotalHours += $totalHours;
                        $summaryTotalTarget += $targetQty;
                        $summaryTotalCompleted += $completedQty;

                        $remarksList = $assignments->pluck('remarks')->filter(function($r) {
                            return !empty(trim($r));
                        })->unique()->values();
                        $remarksStr = $remarksList->isNotEmpty() ? $remarksList->implode(', ') : '-';

                        $designation = $emp->operation_stages_names ?: '-';
                        if ($designation === '-') {
                            $stageNames = $assignments->map(function($a) {
                                if ($a->task && $a->task->stage && $a->task->stage->operationStage) {
                                    return $a->task->stage->operationStage->operation_stage_name;
                                } elseif ($a->task && $a->task->operationStage) {
                                    return $a->task->operationStage->operation_stage_name;
                                }
                                return null;
                            })->filter()->unique()->values();
                            if ($stageNames->isNotEmpty()) {
                                $designation = $stageNames->implode(', ');
                            }
                        }

                        $effBadge = 'bg-label-danger';
                        if ($efficiency >= 95) $effBadge = 'bg-label-success';
                        elseif ($efficiency >= 75) $effBadge = 'bg-label-warning';

                        $empCode = $emp->emp_id ?: 'EMP' . $emp->id;

                        // Clickable Employee Name for Point 1 (Task Wise Report)
                        $empNameHtml = '<a href="javascript:void(0)" class="fw-bold text-primary view-emp-tasks text-decoration-none" data-emp-id="' . $emp->id . '" data-emp-name="' . htmlspecialchars($emp->name) . '" data-emp-code="' . htmlspecialchars($empCode) . '" data-designation="' . htmlspecialchars($designation) . '" title="Click to view task-wise breakdown">' . htmlspecialchars($emp->name) . ' <i class="ri-external-link-line small opacity-75 ms-1"></i></a>';

                        // Clickable Target Qty for Point 2 (Job Wise Summary)
                        $targetQtyHtml = '<a href="javascript:void(0)" class="fw-bold text-primary view-emp-jobs text-decoration-none" data-emp-id="' . $emp->id . '" data-emp-name="' . htmlspecialchars($emp->name) . '" data-emp-code="' . htmlspecialchars($empCode) . '" data-designation="' . htmlspecialchars($designation) . '" title="Click to view job card summary">' . number_format($targetQty) . ' Pcs <i class="ri-file-list-line small opacity-75 ms-1"></i></a>';

                        $rows[] = [
                            'emp_id_raw' => $empCode,
                            'emp_id' => '<span class="badge bg-label-secondary font-monospace">' . htmlspecialchars($empCode) . '</span>',
                            'employee_name' => $empNameHtml,
                            'designation' => htmlspecialchars($designation),
                            'task' => '<span class="fw-semibold text-dark">' . htmlspecialchars($tasksStr ?: '-') . '</span>',
                            'hours_working' => '<span class="fw-bold">' . (fmod($totalHours, 1) !== 0.0 ? number_format($totalHours, 1) : number_format($totalHours, 0)) . '</span>',
                            'target_qty' => $targetQtyHtml,
                            'completed' => '<span class="text-success fw-bold">' . number_format($completedQty) . ' Pcs</span>',
                            'pending' => '<span class="text-danger fw-semibold">' . number_format($pendingQty) . ' Pcs</span>',
                            'efficiency' => '<span class="badge ' . $effBadge . ' rounded-pill px-3 py-1 fs-6">' . $efficiency . '%</span>',
                            'remark' => htmlspecialchars($remarksStr),
                            '_search_text' => strtolower($empCode . ' ' . $emp->name . ' ' . $designation . ' ' . $tasksStr . ' ' . $remarksStr . ' ' . $efficiency . '%')
                        ];
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    $overallEfficiency = ($summaryTotalTarget > 0) ? round(($summaryTotalCompleted / $summaryTotalTarget) * 100, 1) : 0;

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'meta' => [
                            'total_employees' => $totalRecords,
                            'total_hours' => (fmod($summaryTotalHours, 1) !== 0.0 ? number_format($summaryTotalHours, 1) : number_format($summaryTotalHours, 0)) . ' Hrs',
                            'total_target' => number_format($summaryTotalTarget) . ' Pcs',
                            'total_completed' => number_format($summaryTotalCompleted) . ' Pcs',
                            'overall_efficiency' => $overallEfficiency . '%',
                            'efficiency_val' => $overallEfficiency
                        ]
                    ]);

                case 'employee-tasks':
                    $empId = intval($request->emp_id ?? 0);
                    $empUser = \App\Models\User::find($empId);
                    if (!$empUser) {
                        return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
                    }

                    $taskQuery = TaskAssignEmployee::with([
                        'employee',
                        'service',
                        'task.jobCard.serviceProvider',
                        'task.stage.operationStage',
                        'task.operationStage'
                    ])->where('issued_to', $empId);

                    if ($fromDate) {
                        $taskQuery->where(function($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                              ->orWhere(function($sub) use ($fromDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                              });
                        });
                    }
                    if ($toDate) {
                        $taskQuery->where(function($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                              ->orWhere(function($sub) use ($toDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                              });
                        });
                    }
                    if ($unitId) {
                        $taskQuery->whereHas('task.jobCard', function($jcQ) use ($unitId) {
                            $jcQ->where('service_provider_id', $unitId);
                        });
                    }

                    $assignments = $taskQuery->orderBy('id', 'desc')->get();

                    $taskRows = [];
                    $totalEmpHours = 0;
                    $totalEmpTarget = 0;
                    $totalEmpCompleted = 0;

                    foreach ($assignments as $a) {
                        $tHours = (float)($a->total_hrs ?? 0);
                        $tTarget = (float)($a->issue_qty ?? 0);
                        $tCompleted = (float)($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
                        $tPending = max(0, $tTarget - $tCompleted);
                        $tEfficiency = ($tTarget > 0) ? round(($tCompleted / $tTarget) * 100, 1) : 0;

                        $totalEmpHours += $tHours;
                        $totalEmpTarget += $tTarget;
                        $totalEmpCompleted += $tCompleted;

                        $taskNo = $a->task ? $a->task->task_no : 'N/A';
                        $jcNo = $a->task ? ($a->task->job_card_no ?? ($a->task->jobCard ? $a->task->jobCard->job_card_no : 'N/A')) : 'N/A';
                        $serviceName = $a->service ? $a->service->service_name : '-';
                        
                        $stageName = '-';
                        if ($a->task && $a->task->stage && $a->task->stage->operationStage) {
                            $stageName = $a->task->stage->operationStage->operation_stage_name;
                        } elseif ($a->task && $a->task->operationStage) {
                            $stageName = $a->task->operationStage->operation_stage_name;
                        }

                        $statusVal = $a->status ?: ($a->task ? $a->task->status : 'Planned');
                        $statusBadge = 'secondary';
                        if ($statusVal === 'Completed') $statusBadge = 'success';
                        elseif ($statusVal === 'In Progress') $statusBadge = 'primary';
                        elseif ($statusVal === 'Hold') $statusBadge = 'warning';
                        elseif ($statusVal === 'Open') $statusBadge = 'info';

                        $effBadge = 'bg-label-danger';
                        if ($tEfficiency >= 95) $effBadge = 'bg-label-success';
                        elseif ($tEfficiency >= 75) $effBadge = 'bg-label-warning';

                        $remark = $a->remarks ?: ($a->task && $a->task->remarks ? $a->task->remarks : '-');

                        $taskRows[] = [
                            'task_no' => '<strong>' . htmlspecialchars($taskNo) . '</strong>',
                            'job_card_no' => htmlspecialchars($jcNo),
                            'service' => '<span class="badge bg-label-primary">' . htmlspecialchars($serviceName) . '</span>',
                            'stage' => htmlspecialchars($stageName),
                            'hours_worked' => (fmod($tHours, 1) !== 0.0 ? number_format($tHours, 1) : number_format($tHours, 0)),
                            'target_qty' => number_format($tTarget) . ' Pcs',
                            'completed_qty' => '<span class="text-success fw-bold">' . number_format($tCompleted) . ' Pcs</span>',
                            'pending_qty' => '<span class="text-danger">' . number_format($tPending) . ' Pcs</span>',
                            'efficiency' => '<span class="badge ' . $effBadge . ' rounded-pill">' . $tEfficiency . '%</span>',
                            'status' => '<span class="badge bg-label-' . $statusBadge . ' rounded-pill">' . htmlspecialchars($statusVal) . '</span>',
                            'remarks' => htmlspecialchars($remark),
                            '_search_text' => strtolower($taskNo . ' ' . $jcNo . ' ' . $serviceName . ' ' . $stageName . ' ' . $statusVal . ' ' . $remark)
                        ];
                    }

                    $totalRecords = count($taskRows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($taskRows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $taskRows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    $overallEmpEff = ($totalEmpTarget > 0) ? round(($totalEmpCompleted / $totalEmpTarget) * 100, 1) : 0;
                    $designation = $empUser->operation_stages_names ?: '-';
                    if ($designation === '-') {
                        $stageNames = $assignments->map(function($a) {
                            if ($a->task && $a->task->stage && $a->task->stage->operationStage) {
                                return $a->task->stage->operationStage->operation_stage_name;
                            } elseif ($a->task && $a->task->operationStage) {
                                return $a->task->operationStage->operation_stage_name;
                            }
                            return null;
                        })->filter()->unique()->values();
                        if ($stageNames->isNotEmpty()) {
                            $designation = $stageNames->implode(', ');
                        }
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'success' => true,
                        'employee' => [
                            'id' => $empUser->id,
                            'name' => $empUser->name,
                            'emp_id' => $empUser->emp_id ?: 'EMP' . $empUser->id,
                            'designation' => $designation,
                        ],
                        'summary' => [
                            'total_tasks' => $totalRecords,
                            'total_hours' => (fmod($totalEmpHours, 1) !== 0.0 ? number_format($totalEmpHours, 1) : number_format($totalEmpHours, 0)) . ' Hrs',
                            'total_target' => number_format($totalEmpTarget) . ' Pcs',
                            'total_completed' => number_format($totalEmpCompleted) . ' Pcs',
                            'total_pending' => number_format(max(0, $totalEmpTarget - $totalEmpCompleted)) . ' Pcs',
                            'efficiency' => $overallEmpEff . '%'
                        ]
                    ]);

                case 'employee-jobs':
                    $empId = intval($request->emp_id ?? 0);
                    $empUser = \App\Models\User::find($empId);
                    if (!$empUser) {
                        return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
                    }

                    $jobQuery = TaskAssignEmployee::with([
                        'employee',
                        'service',
                        'task.jobCard.serviceProvider',
                        'task.stage.operationStage',
                        'task.operationStage'
                    ])->where('issued_to', $empId);

                    if ($fromDate) {
                        $jobQuery->where(function($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                              ->orWhere(function($sub) use ($fromDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                              });
                        });
                    }
                    if ($toDate) {
                        $jobQuery->where(function($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                              ->orWhere(function($sub) use ($toDate) {
                                  $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                              });
                        });
                    }
                    if ($unitId) {
                        $jobQuery->whereHas('task.jobCard', function($jcQ) use ($unitId) {
                            $jcQ->where('service_provider_id', $unitId);
                        });
                    }

                    $assignments = $jobQuery->orderBy('id', 'desc')->get();
                    $groupedJobs = $assignments->groupBy(function($a) {
                        return $a->task ? ($a->task->job_card_entry_id ?: $a->task->job_card_no) : 'no_jc_' . $a->id;
                    });

                    $jobRows = [];
                    $totalEmpTarget = 0;
                    $totalEmpCompleted = 0;

                    foreach ($groupedJobs as $jcKey => $group) {
                        $first = $group->first();
                        $jcNo = $first->task ? ($first->task->job_card_no ?? ($first->task->jobCard ? $first->task->jobCard->job_card_no : 'N/A')) : 'N/A';
                        $unit = ($first->task && $first->task->jobCard && $first->task->jobCard->serviceProvider) ? $first->task->jobCard->serviceProvider->name : '-';
                        
                        $tasksList = $group->map(function($a) {
                            return $a->service ? $a->service->service_name : ($a->task ? $a->task->task_no : null);
                        })->filter()->unique()->values()->implode(', ');

                        $target = (float) $group->sum('issue_qty');
                        $completed = (float) $group->sum(function($a) {
                            return (float)($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
                        });
                        $pending = max(0, $target - $completed);
                        $eff = ($target > 0) ? round(($completed / $target) * 100, 1) : 0;

                        $totalEmpTarget += $target;
                        $totalEmpCompleted += $completed;

                        $effBadge = 'bg-label-danger';
                        if ($eff >= 95) $effBadge = 'bg-label-success';
                        elseif ($eff >= 75) $effBadge = 'bg-label-warning';

                        $statusVal = $first->status ?: ($first->task ? $first->task->status : 'Planned');
                        $statusBadge = 'secondary';
                        if ($statusVal === 'Completed') $statusBadge = 'success';
                        elseif ($statusVal === 'In Progress') $statusBadge = 'primary';
                        elseif ($statusVal === 'Hold') $statusBadge = 'warning';

                        $remarks = $group->pluck('remarks')->filter(function($r) {
                            return !empty(trim($r));
                        })->unique()->implode(', ');
                        if (empty($remarks)) {
                            $remarks = $first->task && $first->task->remarks ? $first->task->remarks : '-';
                        }

                        $jobRows[] = [
                            'job_card_no' => '<strong>' . htmlspecialchars($jcNo) . '</strong>',
                            'unit' => htmlspecialchars($unit),
                            'tasks' => '<span class="fw-semibold">' . htmlspecialchars($tasksList ?: '-') . '</span>',
                            'target_qty' => number_format($target) . ' Pcs',
                            'completed_qty' => '<span class="text-success fw-bold">' . number_format($completed) . ' Pcs</span>',
                            'pending_qty' => '<span class="text-danger">' . number_format($pending) . ' Pcs</span>',
                            'efficiency' => '<span class="badge ' . $effBadge . ' rounded-pill">' . $eff . '%</span>',
                            'status' => '<span class="badge bg-label-' . $statusBadge . ' rounded-pill">' . htmlspecialchars($statusVal) . '</span>',
                            'remarks' => htmlspecialchars($remarks ?: '-'),
                            '_search_text' => strtolower($jcNo . ' ' . $unit . ' ' . $tasksList . ' ' . $statusVal . ' ' . $remarks)
                        ];
                    }

                    $totalRecords = count($jobRows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($jobRows, function($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $jobRows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    $overallEmpEff = ($totalEmpTarget > 0) ? round(($totalEmpCompleted / $totalEmpTarget) * 100, 1) : 0;
                    $designation = $empUser->operation_stages_names ?: '-';
                    if ($designation === '-') {
                        $stageNames = $assignments->map(function($a) {
                            if ($a->task && $a->task->stage && $a->task->stage->operationStage) {
                                return $a->task->stage->operationStage->operation_stage_name;
                            } elseif ($a->task && $a->task->operationStage) {
                                return $a->task->operationStage->operation_stage_name;
                            }
                            return null;
                        })->filter()->unique()->values();
                        if ($stageNames->isNotEmpty()) {
                            $designation = $stageNames->implode(', ');
                        }
                    }

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'success' => true,
                        'employee' => [
                            'id' => $empUser->id,
                            'name' => $empUser->name,
                            'emp_id' => $empUser->emp_id ?: 'EMP' . $empUser->id,
                            'designation' => $designation,
                        ],
                        'summary' => [
                            'total_jobs' => $totalRecords,
                            'total_target' => number_format($totalEmpTarget) . ' Pcs',
                            'total_completed' => number_format($totalEmpCompleted) . ' Pcs',
                            'total_pending' => number_format(max(0, $totalEmpTarget - $totalEmpCompleted)) . ' Pcs',
                            'efficiency' => $overallEmpEff . '%'
                        ]
                    ]);

                default:
                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => []
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
