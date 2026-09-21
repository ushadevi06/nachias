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
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProductionService;
use App\Models\Brand;
use App\Models\Style;
use App\Models\ProcessSchedule;
use App\Models\OperationStageTarget;

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->route('type') ?? $request->get('report_type') ?? $request->get('type') ?? 'production-wip';
            return $this->ajaxReportData($request, $type);
        }

        $units = ServiceProvider::where('status', 'Active')->orderBy('id', 'desc')->get();
        $cuttingEmployees = $this->getCuttingEmployees();
        $cuttingPlants = $this->getCuttingPlants();
        $operationStages = OperationStage::whereNull('deleted_at')->orderBy('id')->get();
        $usedBrandIds = DB::table('job_card_entries')->whereNotNull('brand_id')->distinct()->pluck('brand_id')->toArray();
        $brands = Brand::whereNull('deleted_at')->whereIn('id', $usedBrandIds)->orderBy('brand_name')->get();
        $allStageServices = ProductionService::whereNull('deleted_at')->where('status', 'Active')->orderBy('sequence')->orderBy('id')->get(['id', 'operation_stage_id', 'service_name', 'service_code'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'operation_stage_id' => $s->operation_stage_id,
                    'service_name' => $s->service_name,
                    'service_code' => $s->service_code,
                    'name' => $s->service_name,
                    'code' => $s->service_code,
                ];
            })
            ->groupBy('operation_stage_id');

        return view('reports/production_report', compact('units', 'cuttingEmployees', 'cuttingPlants', 'operationStages', 'brands', 'allStageServices'));
    }

    public function getCuttingEmployees()
    {
        $cuttingRoleIds = DB::table('roles')->where('name', 'like', 'CUTTING%')->pluck('id')->toArray();
        $service16UserIds = TaskAssignEmployee::where('service_id', 16)->pluck('issued_to')->filter()->unique()->toArray();

        return \App\Models\User::where('status', 'Active')
            ->where(function ($q) use ($cuttingRoleIds, $service16UserIds) {
                $q->whereIn('role_id', $cuttingRoleIds)
                    ->orWhereIn('id', $service16UserIds)
                    ->orWhereJsonContains('operation_stage_id', 1)
                    ->orWhereJsonContains('operation_stage_id', '1');
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'emp_id']);
    }

    public function getCuttingPlants()
    {
        return ServiceProvider::where('status', 'Active')->where('is_plant', 1)->orderBy('name', 'asc')->get(['id', 'name', 'code']);
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
            $parseDate = function ($dateStr) {
                return $this->parseReportDate($dateStr);
            };

            $fromDate = $parseDate($request->from_date);
            $toDate = $parseDate($request->to_date);
            $unitId = $request->unit_id;
            $brandId = $request->brand_id;

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

                    if ($brandId) {
                        $wipQuery->where('brand_id', $brandId);
                    }

                    if ($fromDate) {
                        $wipQuery->where('job_card_date', '>=', $fromDate);
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

                            if ($currentWip != 0 || $periodInward > 0 || $periodOutward > 0) {
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

                            $serviceCompletions = $assignments->groupBy('service_id')->map(function ($group) use ($openingDate, $closingDate) {
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

                            if ($currentWip != 0 || $periodInward > 0 || $periodOutward > 0) {
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
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                        $perfQuery->whereHas('task.jobCard', function ($q) use ($unitId) {
                            $q->where('service_provider_id', $unitId);
                        });
                    }
                    if ($search !== '') {
                        $perfQuery->where(function ($q) use ($search) {
                            $q->whereHas('employee', function ($eq) use ($search) {
                                $eq->where('name', 'like', "%{$search}%");
                            })->orWhereHas('service', function ($sq) use ($search) {
                                $sq->where('service_name', 'like', "%{$search}%");
                            })->orWhereHas('task', function ($tq) use ($search) {
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
                        $assigned = (float) $assign->issue_qty;
                        $completed = (float) $assign->completed_qty;
                        $pending = max(0, $assigned - ($completed + (float) $assign->wastage_qty));
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
                        if ($efficiency >= 90)
                            $badgeClass = 'bg-label-success';
                        elseif ($efficiency >= 70)
                            $badgeClass = 'bg-label-warning';

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
                        $sectionQuery->whereHas('task.jobCard', function ($q) use ($unitId) {
                            $q->where('service_provider_id', $unitId);
                        });
                    }
                    if ($fromDate) {
                        $sectionQuery->where('issue_date', '>=', $fromDate);
                    }
                    if ($toDate) {
                        $sectionQuery->where('issue_date', '<=', $toDate);
                    }

                    $groups = $sectionQuery->get()->groupBy(function ($item) {
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
                            'service_name' => $servName,
                            'process_name' => $stageName,
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
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                        $compQuery->where(function ($q) use ($search) {
                            $q->where('job_card_no', 'like', "%{$search}%")
                                ->orWhereHas('serviceProvider', function ($sq) use ($search) {
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
                        ->whereHas('productionReceipt', function ($q) use ($unitId, $fromDate, $toDate) {
                            if ($unitId) {
                                $q->whereHas('jobCard', function ($jcQuery) use ($unitId) {
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

                    $groups = $receiptItemsQuery->get()->groupBy(function ($item) {
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
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                            ->whereHas('stage', function ($q) use ($stage) {
                                $q->where('operation_stage_id', $stage->id);
                            });

                        if ($fromDate) {
                            $tasksQuery->where(function ($q) use ($fromDate) {
                                $q->where('issue_date', '>=', $fromDate)
                                    ->orWhere(function ($sub) use ($fromDate) {
                                        $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                    });
                            });
                        }
                        if ($toDate) {
                            $tasksQuery->where(function ($q) use ($toDate) {
                                $q->where('issue_date', '<=', $toDate)
                                    ->orWhere(function ($sub) use ($toDate) {
                                        $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                    });
                            });
                        }
                        if ($unitId) {
                            $tasksQuery->whereHas('jobCard', function ($q) use ($unitId) {
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
                                    $taskActual = (float) $services->map(function ($grp) {
                                        return $grp->sum('completed_qty');
                                    })->min();
                                } else {
                                    $taskActual = (float) $task->assignments->sum('completed_qty');
                                }
                            }

                            $stagePlan += $taskPlan;
                            $stageActual += $taskActual;

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

                        $targetVal = $stage->target !== null ? (float) $stage->target : null;
                        if ($targetVal !== null) {
                            $totalTarget += $targetVal;
                        }
                        $totalPlan += $stagePlan;
                        $totalActual += $stageActual;

                        $stageEfficiency = ($stagePlan > 0) ? round(($stageActual / $stagePlan) * 100, 1) : 0;

                        $effBadgeClass = 'bg-label-danger';
                        if ($stageEfficiency >= 95)
                            $effBadgeClass = 'bg-label-success';
                        elseif ($stageEfficiency >= 75)
                            $effBadgeClass = 'bg-label-warning';

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

                        $stagePending = max(0, $stagePlan - $stageActual);

                        $rows[] = [
                            'stage_id' => $stage->id,
                            'operation' => $deptHtml,
                            'target' => $targetVal !== null ? number_format($targetVal) . ' Pcs' : '<span class="text-muted">-</span>',
                            'plan' => '<span class="text-primary fw-bold">' . number_format($stagePlan) . ' Pcs</span>',
                            'actual' => '<span class="text-success fw-bold">' . number_format($stageActual) . ' Pcs</span>',
                            'pending' => '<span class="text-danger fw-bold">' . number_format($stagePending) . ' Pcs</span>',
                            'efficiency' => '<span class="badge ' . $effBadgeClass . ' rounded-pill px-3 py-1 fs-6">' . $stageEfficiency . '%</span>',
                            'working_hours' => '<span class="fw-semibold">8</span>',
                            'delay_details' => $delayDetailsHtml,
                            '_search_text' => strtolower($stage->operation_stage_name . ' ' . $stageEfficiency . '%')
                        ];
                    }

                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : array_slice($filteredRows, $start, $length);

                    $overallEfficiency = ($totalPlan > 0) ? round(($totalActual / $totalPlan) * 100, 1) : 0;
                    $totalPending = max(0, $totalPlan - $totalActual);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'meta' => [
                            'total_target' => number_format($totalTarget) . ' Pcs',
                            'total_plan' => number_format($totalPlan) . ' Pcs',
                            'total_actual' => number_format($totalActual) . ' Pcs',
                            'total_pending' => number_format($totalPending) . ' Pcs',
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
                        ->whereHas('stage', function ($q) use ($stageId) {
                            $q->where('operation_stage_id', $stageId);
                        });

                    if ($fromDate) {
                        $tasksQuery->where(function ($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                                ->orWhere(function ($sub) use ($fromDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                });
                        });
                    }
                    if ($toDate) {
                        $tasksQuery->where(function ($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                                ->orWhere(function ($sub) use ($toDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                });
                        });
                    }
                    if ($unitId) {
                        $tasksQuery->whereHas('jobCard', function ($q) use ($unitId) {
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
                                $taskActual = (float) $services->map(function ($grp) {
                                    return $grp->sum('completed_qty');
                                })->min();
                            } else {
                                $taskActual = (float) $task->assignments->sum('completed_qty');
                            }
                        }

                        $efficiency = ($taskPlan > 0) ? round(($taskActual / $taskPlan) * 100, 1) : 0;
                        $totalPlan += $taskPlan;
                        $totalActual += $taskActual;

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
                        if ($task->status === 'Completed')
                            $statusBadge = 'success';
                        elseif ($task->status === 'In Progress')
                            $statusBadge = 'primary';
                        elseif ($task->status === 'Hold')
                            $statusBadge = 'warning';
                        elseif ($task->status === 'Planned')
                            $statusBadge = 'info';

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
                        $filteredRows = array_values(array_filter($tasksData, function ($r) use ($lowerSearch) {
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
                        'fabricType',
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
                        $jcNo = $jc->job_card_no ?? 'N/A';
                        $issueDateStr = $jc->job_card_date ? date('d-M-Y', strtotime($jc->job_card_date)) : '-';
                        $deliveryDateStr = $jc->delivery_date ? date('d-M-Y', strtotime($jc->delivery_date)) : '-';
                        $ageDays = ($jc->no_of_days !== null && $jc->no_of_days !== '') ? intval($jc->no_of_days) : ($jc->job_card_date ? Carbon::parse($jc->job_card_date)->diffInDays(Carbon::now()) : 0);
                        $brandName = $jc->brand->brand_name ?? 'N/A';
                        $seasonName = $jc->season->season_name ?? '-';
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
                        $fabricDisplay = ($jc->fabricType && $jc->fabricType->fabric_type) ? htmlspecialchars($jc->fabricType->fabric_type) : '-';
                        $issueMts = floatval($jc->issueItems->sum('qty_issue'));
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
                        $cutQty = floatval($jc->issueItems->sum('produced_qty'));
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

                        $baseCutOrEst = ($cutQty > 0) ? $cutQty : $estQty;
                        $balanceBundle = max(0, $baseCutOrEst - $bundledQty);
                        $fullQty = floatval($jc->total_qty_fs ?? $jc->fs_qty ?? 0);
                        $halfQty = floatval($jc->total_qty_hs ?? $jc->hs_qty ?? 0);
                        $unitAssigned = $jc->serviceProvider->name ?? 'Not Assigned';
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
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                        $query->where(function ($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                                ->orWhere(function ($sub) use ($fromDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                });
                        });
                    }
                    if ($toDate) {
                        $query->where(function ($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                                ->orWhere(function ($sub) use ($toDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                });
                        });
                    }
                    if ($unitId) {
                        $query->where(function ($q) use ($unitId) {
                            $q->whereHas('task.jobCard', function ($jcQ) use ($unitId) {
                                $jcQ->where('service_provider_id', $unitId);
                            })->orWhereHas('employee', function ($empQ) use ($unitId) {
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
                        if (!$emp)
                            continue;

                        $tasksDone = $assignments->map(function ($a) {
                            return $a->service ? $a->service->service_name : ($a->task && $a->task->task_no ? $a->task->task_no : null);
                        })->filter()->unique()->values();

                        $tasksStr = $tasksDone->implode(' + ');

                        $totalHours = (float) $assignments->sum(function ($a) {
                            return (float) ($a->total_hrs ?? 0);
                        });

                        $targetQty = (float) $assignments->sum(function ($a) {
                            return (float) ($a->issue_qty ?? 0);
                        });

                        $completedQty = (float) $assignments->sum(function ($a) {
                            return (float) ($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
                        });

                        $pendingQty = max(0, $targetQty - $completedQty);
                        $efficiency = ($targetQty > 0) ? round(($completedQty / $targetQty) * 100, 1) : 0;

                        $summaryTotalHours += $totalHours;
                        $summaryTotalTarget += $targetQty;
                        $summaryTotalCompleted += $completedQty;

                        $remarksList = $assignments->pluck('remarks')->filter(function ($r) {
                            return !empty(trim($r));
                        })->unique()->values();
                        $remarksStr = $remarksList->isNotEmpty() ? $remarksList->implode(', ') : '-';

                        $designation = $emp->operation_stages_names ?: '-';
                        if ($designation === '-') {
                            $stageNames = $assignments->map(function ($a) {
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
                        if ($efficiency >= 95)
                            $effBadge = 'bg-label-success';
                        elseif ($efficiency >= 75)
                            $effBadge = 'bg-label-warning';

                        $empCode = $emp->emp_id ?: 'EMP' . $emp->id;

                        $empNameHtml = '<a href="javascript:void(0)" class="fw-bold text-primary view-emp-tasks text-decoration-none" data-emp-id="' . $emp->id . '" data-emp-name="' . htmlspecialchars($emp->name) . '" data-emp-code="' . htmlspecialchars($empCode) . '" data-designation="' . htmlspecialchars($designation) . '" title="Click to view task-wise breakdown">' . htmlspecialchars($emp->name) . ' <i class="ri-external-link-line small opacity-75 ms-1"></i></a>';

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
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                        $taskQuery->where(function ($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                                ->orWhere(function ($sub) use ($fromDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                });
                        });
                    }
                    if ($toDate) {
                        $taskQuery->where(function ($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                                ->orWhere(function ($sub) use ($toDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                });
                        });
                    }
                    if ($unitId) {
                        $taskQuery->whereHas('task.jobCard', function ($jcQ) use ($unitId) {
                            $jcQ->where('service_provider_id', $unitId);
                        });
                    }

                    $assignments = $taskQuery->orderBy('id', 'desc')->get();

                    $taskRows = [];
                    $totalEmpHours = 0;
                    $totalEmpTarget = 0;
                    $totalEmpCompleted = 0;

                    foreach ($assignments as $a) {
                        $tHours = (float) ($a->total_hrs ?? 0);
                        $tTarget = (float) ($a->issue_qty ?? 0);
                        $tCompleted = (float) ($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
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
                        if ($statusVal === 'Completed')
                            $statusBadge = 'success';
                        elseif ($statusVal === 'In Progress')
                            $statusBadge = 'primary';
                        elseif ($statusVal === 'Hold')
                            $statusBadge = 'warning';
                        elseif ($statusVal === 'Open')
                            $statusBadge = 'info';

                        $effBadge = 'bg-label-danger';
                        if ($tEfficiency >= 95)
                            $effBadge = 'bg-label-success';
                        elseif ($tEfficiency >= 75)
                            $effBadge = 'bg-label-warning';

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
                        $filteredRows = array_values(array_filter($taskRows, function ($r) use ($lowerSearch) {
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
                        $stageNames = $assignments->map(function ($a) {
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
                        $jobQuery->where(function ($q) use ($fromDate) {
                            $q->where('issue_date', '>=', $fromDate)
                                ->orWhere(function ($sub) use ($fromDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate);
                                });
                        });
                    }
                    if ($toDate) {
                        $jobQuery->where(function ($q) use ($toDate) {
                            $q->where('issue_date', '<=', $toDate)
                                ->orWhere(function ($sub) use ($toDate) {
                                    $sub->whereNull('issue_date')->whereDate('created_at', '<=', $toDate);
                                });
                        });
                    }
                    if ($unitId) {
                        $jobQuery->whereHas('task.jobCard', function ($jcQ) use ($unitId) {
                            $jcQ->where('service_provider_id', $unitId);
                        });
                    }

                    $assignments = $jobQuery->orderBy('id', 'desc')->get();
                    $groupedJobs = $assignments->groupBy(function ($a) {
                        return $a->task ? ($a->task->job_card_entry_id ?: $a->task->job_card_no) : 'no_jc_' . $a->id;
                    });

                    $jobRows = [];
                    $totalEmpTarget = 0;
                    $totalEmpCompleted = 0;

                    foreach ($groupedJobs as $jcKey => $group) {
                        $first = $group->first();
                        $jcNo = $first->task ? ($first->task->job_card_no ?? ($first->task->jobCard ? $first->task->jobCard->job_card_no : 'N/A')) : 'N/A';
                        $unit = ($first->task && $first->task->jobCard && $first->task->jobCard->serviceProvider) ? $first->task->jobCard->serviceProvider->name : '-';

                        $tasksList = $group->map(function ($a) {
                            return $a->service ? $a->service->service_name : ($a->task ? $a->task->task_no : null);
                        })->filter()->unique()->values()->implode(', ');

                        $target = (float) $group->sum('issue_qty');
                        $completed = (float) $group->sum(function ($a) {
                            return (float) ($a->completed_qty > 0 ? $a->completed_qty : ($a->status === 'Completed' ? $a->issue_qty : 0));
                        });
                        $pending = max(0, $target - $completed);
                        $eff = ($target > 0) ? round(($completed / $target) * 100, 1) : 0;

                        $totalEmpTarget += $target;
                        $totalEmpCompleted += $completed;

                        $effBadge = 'bg-label-danger';
                        if ($eff >= 95)
                            $effBadge = 'bg-label-success';
                        elseif ($eff >= 75)
                            $effBadge = 'bg-label-warning';

                        $statusVal = $first->status ?: ($first->task ? $first->task->status : 'Planned');
                        $statusBadge = 'secondary';
                        if ($statusVal === 'Completed')
                            $statusBadge = 'success';
                        elseif ($statusVal === 'In Progress')
                            $statusBadge = 'primary';
                        elseif ($statusVal === 'Hold')
                            $statusBadge = 'warning';

                        $remarks = $group->pluck('remarks')->filter(function ($r) {
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
                        $filteredRows = array_values(array_filter($jobRows, function ($r) use ($lowerSearch) {
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
                        $stageNames = $assignments->map(function ($a) {
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

                case 'cutting-section-average':
                    $reportData = $this->getCuttingSectionAverageData($request);
                    $rows = $reportData['rows'];
                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
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
                        'data' => $pageData,
                        'meta' => $reportData['meta']
                    ]);

                case 'stage-wise-wip':
                    return $this->getStageWiseWipData($request);

                case 'final-finishing-average':
                    $reportData = $this->getFinalFinishingAverageData($request);
                    $rows = $reportData['rows'];
                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : ($length > 0 ? array_slice($filteredRows, $start, $length) : $filteredRows);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'meta' => $reportData['meta']
                    ]);

                case 'unit-line-average':
                    $reportData = $this->getUnitLineAverageData($request);
                    $rows = $reportData['rows'];
                    $totalRecords = count($rows);
                    if ($search !== '') {
                        $lowerSearch = strtolower($search);
                        $filteredRows = array_values(array_filter($rows, function ($r) use ($lowerSearch) {
                            return strpos($r['_search_text'], $lowerSearch) !== false;
                        }));
                    } else {
                        $filteredRows = $rows;
                    }
                    $recordsFiltered = count($filteredRows);
                    $pageData = $isExport ? $filteredRows : ($length > 0 ? array_slice($filteredRows, $start, $length) : $filteredRows);

                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $pageData,
                        'meta' => $reportData['meta']
                    ]);

                case 'production-planning':
                    return $this->getProductionPlanningData($request);

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

    public function getCuttingSectionAverageData(Request $request)
    {
        $fromDate = $this->parseReportDate($request->from_date);
        $toDate = $this->parseReportDate($request->to_date);
        $unitId = $request->unit_id;
        $unitName = null;
        if ($unitId) {
            $unitObj = ServiceProvider::find($unitId);
            if ($unitObj) {
                $unitName = $unitObj->name;
            }
        }

        $defaultDailyTarget = 2000;
        $otDailyTarget = 2250;

        if (!$fromDate || !$toDate) {
            $latestDate = Task::where(function ($q) {
                $q->where('stage_id', 1)
                    ->orWhereHas('stage', function ($sq) {
                        $sq->where('operation_stage_id', 1);
                    });
            })->whereNotNull('issue_date')->max('issue_date');

            if (!$latestDate) {
                $latestDate = JobCardEntry::max('job_card_date') ?: date('Y-m-d');
            }

            $latestCarbon = Carbon::parse($latestDate);
            $calcFromDate = $fromDate ?: $latestCarbon->copy()->startOfMonth()->format('Y-m-d');
            $calcToDate = $toDate ?: $latestCarbon->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $calcFromDate = $fromDate;
            $calcToDate = $toDate;
        }

        $cuttingTasksQuery = Task::where(function ($q) {
            $q->where('stage_id', 1)
                ->orWhereHas('stage', function ($sq) {
                    $sq->where('operation_stage_id', 1);
                });
        })
            ->whereBetween('issue_date', [$calcFromDate, $calcToDate])
            ->with(['jobCard.serviceProvider', 'assignments.service', 'assignments.employee']);

        if ($unitId) {
            $cuttingTasksQuery->whereHas('jobCard', function ($jq) use ($unitId) {
                $jq->where('service_provider_id', $unitId);
            });
        }
        $cuttingTasks = $cuttingTasksQuery->get();

        $assignQuery = TaskAssignEmployee::whereBetween('issue_date', [$calcFromDate, $calcToDate])
            ->with(['service', 'employee', 'task.jobCard.serviceProvider']);

        if ($unitId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($unitId) {
                $jq->where('service_provider_id', $unitId);
            });
        }
        $allAssignments = $assignQuery->get();

        $jcQuery = JobCardEntry::whereBetween('job_card_date', [$calcFromDate, $calcToDate])
            ->with('serviceProvider');
        if ($unitId) {
            $jcQuery->where('service_provider_id', $unitId);
        }
        $jobCards = $jcQuery->get();

        $attendanceRecords = Attendance::whereBetween('date', [$calcFromDate, $calcToDate])
            ->where(function ($q) {
                $q->where('status', 'Overtime')
                    ->orWhere('work_hours', '>', 9);
            })
            ->get();

        $otDatesMap = [];
        foreach ($attendanceRecords as $att) {
            $dStr = Carbon::parse($att->date)->format('Y-m-d');
            $otDatesMap[$dStr] = true;
        }

        $activeDates = collect();
        foreach ($cuttingTasks as $ct) {
            if ($ct->issue_date)
                $activeDates->push(Carbon::parse($ct->issue_date)->format('Y-m-d'));
        }
        foreach ($allAssignments as $as) {
            if ($as->issue_date)
                $activeDates->push(Carbon::parse($as->issue_date)->format('Y-m-d'));
        }
        foreach ($jobCards as $jc) {
            if ($jc->job_card_date)
                $activeDates->push(Carbon::parse($jc->job_card_date)->format('Y-m-d'));
        }
        foreach ($attendanceRecords as $att) {
            if ($att->date)
                $activeDates->push(Carbon::parse($att->date)->format('Y-m-d'));
        }
        $activeDates = $activeDates->unique()->sort()->values();

        if ($activeDates->isEmpty()) {
            $curr = Carbon::parse($calcFromDate);
            $end = Carbon::parse($calcToDate);
            while ($curr->lte($end)) {
                if (!$curr->isSunday()) {
                    $activeDates->push($curr->format('Y-m-d'));
                }
                $curr->addDay();
            }
        }

        $cuttingEmployees = $this->getCuttingEmployees();
        $cuttingPlants = $this->getCuttingPlants();

        $rows = [];
        $summaryColTotals = [
            'master_total' => 0,
            'cutting_bundleing_qty' => 0,
            'fusing_qty' => 0,
            'logo_qty' => 0,
            'issue_total' => 0,
            'target_per_day' => 0
        ];
        foreach ($cuttingEmployees as $emp) {
            $summaryColTotals['emp_' . $emp->id] = 0;
        }
        foreach ($cuttingPlants as $plant) {
            $summaryColTotals['plant_' . $plant->id] = 0;
        }

        $otDaysWorkedCount = 0;
        $regularDaysWorkedCount = 0;

        $tasksByDate = $cuttingTasks->groupBy(fn($t) => Carbon::parse($t->issue_date)->format('Y-m-d'));
        $assignsByDate = $allAssignments->groupBy(fn($a) => Carbon::parse($a->issue_date)->format('Y-m-d'));
        $jcsByDate = $jobCards->groupBy(fn($j) => Carbon::parse($j->job_card_date)->format('Y-m-d'));

        foreach ($activeDates as $dateStr) {
            $cDate = Carbon::parse($dateStr);
            $dFormatted = $cDate->format('d-m-Y');

            $dayAssigns = $assignsByDate->get($dateStr, collect());
            $dayTasks = $tasksByDate->get($dateStr, collect());
            $dayJcs = $jcsByDate->get($dateStr, collect());

            $empQuantities = [];
            $masterTotal = 0;

            foreach ($cuttingEmployees as $emp) {
                $eQty = 0;
                foreach ($dayAssigns as $as) {
                    if ($as->issued_to == $emp->id) {
                        $sName = strtoupper($as->service ? $as->service->service_name : '');
                        $isCutting = ($as->service_id == 16) || (strpos($sName, 'CUTTING') !== false);
                        if ($isCutting) {
                            $eQty += (float) $as->issue_qty;
                        }
                    }
                }
                $empQuantities[$emp->id] = $eQty;
                $masterTotal += $eQty;
                $summaryColTotals['emp_' . $emp->id] += $eQty;
            }
            $summaryColTotals['master_total'] += $masterTotal;

            $bundleingQty = 0;
            $fusingQty = 0;
            $logoQty = 0;

            foreach ($dayAssigns as $as) {
                $sName = strtoupper($as->service ? $as->service->service_name : '');
                if (strpos($sName, 'BUNDLE') !== false) {
                    $bundleingQty += (float) $as->issue_qty;
                }
                if (strpos($sName, 'FUSING') !== false) {
                    $fusingQty += (float) $as->issue_qty;
                }
                if (strpos($sName, 'LOGO') !== false) {
                    $logoQty += (float) $as->issue_qty;
                }
            }
            $summaryColTotals['cutting_bundleing_qty'] += $bundleingQty;
            $summaryColTotals['fusing_qty'] += $fusingQty;
            $summaryColTotals['logo_qty'] += $logoQty;

            $plantQuantities = [];
            $issueTotal = 0;

            foreach ($cuttingPlants as $plant) {
                $pQty = 0;
                foreach ($dayTasks as $t) {
                    if ($t->jobCard && $t->jobCard->service_provider_id == $plant->id) {
                        $tQty = (float) ($t->issue_qty > 0 ? $t->issue_qty : ($t->jobCard ? $t->jobCard->grand_total_qty : 0));
                        $pQty += $tQty;
                    }
                }
                $plantQuantities[$plant->id] = $pQty;
                $issueTotal += $pQty;
            }

            if ($issueTotal == 0 && $dayJcs->isNotEmpty()) {
                foreach ($cuttingPlants as $plant) {
                    $jcQty = 0;
                    foreach ($dayJcs as $jc) {
                        if ($jc->service_provider_id == $plant->id) {
                            $jcQty += (float) $jc->grand_total_qty;
                        }
                    }
                    $plantQuantities[$plant->id] = $jcQty;
                    $issueTotal += $jcQty;
                }
            }

            foreach ($cuttingPlants as $plant) {
                $summaryColTotals['plant_' . $plant->id] += $plantQuantities[$plant->id];
            }
            $summaryColTotals['issue_total'] += $issueTotal;

            $isOt = isset($otDatesMap[$dateStr]);
            if ($isOt) {
                $otDaysWorkedCount++;
            } else {
                $regularDaysWorkedCount++;
            }

            $targetPerDay = $defaultDailyTarget; 
            $efficiencyVal = ($targetPerDay > 0 && $issueTotal > 0) ? round(($issueTotal / $targetPerDay) * 100) : 0;
            $summaryColTotals['target_per_day'] += $targetPerDay;

            $effClass = 'text-danger';
            if ($efficiencyVal >= 100)
                $effClass = 'text-success fw-bold';
            elseif ($efficiencyVal >= 75)
                $effClass = 'text-primary fw-bold';
            elseif ($efficiencyVal >= 50)
                $effClass = 'text-warning fw-bold';

            $otBadge = $isOt
                ? '<span class="badge bg-label-success fw-bold px-2 py-1">Yes</span>'
                : '<span class="text-muted">No</span>';

            $formatNum = function ($v) {
                return $v > 0 ? number_format($v) : '-';
            };

            $row = [
                'date' => '<strong>' . htmlspecialchars($dFormatted) . '</strong>',
                'date_clean' => $dFormatted,
                'date_raw' => $dateStr,
            ];
            $searchTextParts = [$dFormatted];

            foreach ($cuttingEmployees as $emp) {
                $ev = $empQuantities[$emp->id];
                $row['emp_' . $emp->id] = $formatNum($ev);
                if ($ev > 0)
                    $searchTextParts[] = (string) $ev . ' ' . $emp->name;
            }
            $row['master_total'] = '<strong class="text-dark">' . $formatNum($masterTotal) . '</strong>';
            $row['master_total_clean'] = $formatNum($masterTotal);
            $searchTextParts[] = (string) $masterTotal;

            $row['cutting_bundleing_qty'] = $formatNum($bundleingQty);
            $row['fusing_qty'] = $formatNum($fusingQty);
            $row['logo_qty'] = $formatNum($logoQty);

            foreach ($cuttingPlants as $plant) {
                $pv = $plantQuantities[$plant->id];
                $row['plant_' . $plant->id] = $formatNum($pv);
                if ($pv > 0)
                    $searchTextParts[] = (string) $pv . ' ' . ($plant->code ?: $plant->name);
            }
            $row['issue_total'] = '<strong class="text-dark">' . $formatNum($issueTotal) . '</strong>';
            $row['issue_total_clean'] = $formatNum($issueTotal);
            $searchTextParts[] = (string) $issueTotal;

            $row['efficiency'] = '<span class="' . $effClass . '">' . ($efficiencyVal > 0 ? $efficiencyVal . '%' : '-') . '</span>';
            $row['efficiency_clean'] = $efficiencyVal > 0 ? $efficiencyVal . '%' : '-';
            $row['efficiency_val'] = $efficiencyVal;
            $row['ot'] = $otBadge;
            $row['ot_clean'] = $isOt ? 'Yes' : 'No';
            $row['ot_val'] = $isOt ? 'Yes' : 'No';
            $row['target_per_day'] = number_format($targetPerDay);
            $row['_search_text'] = strtolower(implode(' ', $searchTextParts) . ' ' . ($isOt ? 'Yes' : 'No'));

            $rows[] = $row;
        }

        $totalDaysWorked = count($rows);
        $targetForDaysWorked = ($regularDaysWorkedCount * $defaultDailyTarget) + ($otDaysWorkedCount * $otDailyTarget);
        $actualIssueTotal = $summaryColTotals['issue_total'];
        $loss = $actualIssueTotal - $targetForDaysWorked;
        $avgDailyActual = ($totalDaysWorked > 0) ? round($actualIssueTotal / $totalDaysWorked) : 0;
        $overallEfficiency = ($targetForDaysWorked > 0) ? round(($actualIssueTotal / $targetForDaysWorked) * 100) : 0;

        $dailyTargetAvg = ($totalDaysWorked > 0) ? ($targetForDaysWorked / $totalDaysWorked) : $defaultDailyTarget;
        $daysBackward = ($dailyTargetAvg > 0) ? round(abs($loss) / $dailyTargetAvg, 2) : 0;

        $monthCarbon = Carbon::parse($calcFromDate);
        $totalMonthDays = $monthCarbon->daysInMonth;
        $remainingDays = max(1, $totalMonthDays - $totalDaysWorked);
        $targetPerDayToAchieve = ($remainingDays > 0) ? round(abs($loss) / $remainingDays) : 0;

        $totalRow = [
            'date' => 'TOTAL',
        ];
        foreach ($cuttingEmployees as $emp) {
            $totalRow['emp_' . $emp->id] = number_format($summaryColTotals['emp_' . $emp->id]);
        }
        $totalRow['master_total'] = number_format($summaryColTotals['master_total']);
        $totalRow['cutting_bundleing_qty'] = number_format($summaryColTotals['cutting_bundleing_qty']);
        $totalRow['fusing_qty'] = number_format($summaryColTotals['fusing_qty']);
        $totalRow['logo_qty'] = number_format($summaryColTotals['logo_qty']);
        foreach ($cuttingPlants as $plant) {
            $totalRow['plant_' . $plant->id] = number_format($summaryColTotals['plant_' . $plant->id]);
        }
        $totalRow['issue_total'] = number_format($summaryColTotals['issue_total']);
        $totalRow['efficiency'] = $overallEfficiency . '%';
        $totalRow['ot'] = $otDaysWorkedCount > 0 ? $otDaysWorkedCount . ' Days' : 'No';
        $totalRow['target_per_day'] = number_format($summaryColTotals['target_per_day']);

        $daysCountForAvg = max(1, $totalDaysWorked);
        $avgRow = [
            'date' => 'AVERAGE',
        ];
        foreach ($cuttingEmployees as $emp) {
            $avgRow['emp_' . $emp->id] = number_format(round($summaryColTotals['emp_' . $emp->id] / $daysCountForAvg));
        }
        $avgRow['master_total'] = number_format(round($summaryColTotals['master_total'] / $daysCountForAvg));
        $avgRow['cutting_bundleing_qty'] = number_format(round($summaryColTotals['cutting_bundleing_qty'] / $daysCountForAvg));
        $avgRow['fusing_qty'] = number_format(round($summaryColTotals['fusing_qty'] / $daysCountForAvg));
        $avgRow['logo_qty'] = number_format(round($summaryColTotals['logo_qty'] / $daysCountForAvg));
        foreach ($cuttingPlants as $plant) {
            $avgRow['plant_' . $plant->id] = number_format(round($summaryColTotals['plant_' . $plant->id] / $daysCountForAvg));
        }
        $avgRow['issue_total'] = number_format($avgDailyActual);
        $avgRow['efficiency'] = $overallEfficiency . '%';
        $avgRow['ot'] = '-';
        $avgRow['target_per_day'] = number_format($defaultDailyTarget);

        $meta = [
            'total_row' => $totalRow,
            'average_row' => $avgRow,
            'cutting_employees' => $cuttingEmployees->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'emp_id' => $e->emp_id]),
            'cutting_plants' => $cuttingPlants->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'code' => $p->code ?: $p->name]),
            'report_title' => 'HO CUTTING SECTION AVERAGE REPORT ' . strtoupper(Carbon::parse($calcFromDate)->format('F Y')) . ' - (' . number_format($defaultDailyTarget) . ' PCS PER DAY)',
            'report_period' => Carbon::parse($calcFromDate)->format('M Y'),
            'monthly_target' => number_format($targetForDaysWorked),
            'timing_reg_days' => $regularDaysWorkedCount,
            'timing_ot_days' => $otDaysWorkedCount,
            'timing_total_days' => $totalDaysWorked,
            'target_reg_days' => number_format($regularDaysWorkedCount * $defaultDailyTarget),
            'target_ot_days' => number_format($otDaysWorkedCount * $otDailyTarget),
            'target_total_days' => number_format($targetForDaysWorked),
            'actual_issue' => number_format($actualIssueTotal),
            'loss' => ($loss < 0 ? '-' : '+') . number_format(abs($loss)),
            'loss_is_negative' => $loss < 0,
            'average' => number_format($avgDailyActual),
            'efficiency' => $overallEfficiency . '%',
            'days_backward' => $daysBackward,
            'target_per_day_to_achieve' => number_format($targetPerDayToAchieve)
        ];

        return [
            'calcFromDate' => $calcFromDate,
            'calcToDate' => $calcToDate,
            'unitId' => $unitId,
            'unitName' => $unitName,
            'cuttingEmployees' => $cuttingEmployees,
            'cuttingPlants' => $cuttingPlants,
            'rows' => $rows,
            'totalRow' => $totalRow,
            'avgRow' => $avgRow,
            'summaryColTotals' => $summaryColTotals,
            'meta' => $meta
        ];
    }

    public function getFinalFinishingAverageData(Request $request)
    {
        $fromDate = $this->parseReportDate($request->from_date);
        $toDate = $this->parseReportDate($request->to_date);
        $unitId = $request->unit_id;
        $unitName = null;
        if ($unitId) {
            $unitObj = ServiceProvider::find($unitId);
            if ($unitObj) {
                $unitName = $unitObj->name;
            }
        }

        if (!$fromDate || !$toDate) {
            $latestDate = Task::where(function ($q) {
                $q->whereIn('stage_id', [5, 6])
                    ->orWhereHas('stage', function ($sq) {
                        $sq->whereIn('operation_stage_id', [5, 6]);
                    });
            })->whereNotNull('issue_date')->max('issue_date');

            if (!$latestDate) {
                $latestDate = date('Y-m-d');
            }

            $latestCarbon = Carbon::parse($latestDate);
            $calcFromDate = $fromDate ?: $latestCarbon->copy()->startOfMonth()->format('Y-m-d');
            $calcToDate = $toDate ?: $latestCarbon->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $calcFromDate = $fromDate;
            $calcToDate = $toDate;
        }

        $finishingStage = OperationStage::where('operation_stage_name', 'like', '%IRON%')->orWhere('operation_stage_name', 'like', '%PACK%')->orWhere('operation_stage_name', 'like', '%FINISH%')->first();
        $stageId = $finishingStage ? $finishingStage->id : 6;

        $fromCarbon = Carbon::parse($calcFromDate);
        $toCarbon = Carbon::parse($calcToDate);
        if ($fromCarbon->format('Y-m') === $toCarbon->format('Y-m')) {
            $periodLabel = strtoupper($fromCarbon->format('F Y'));
            $periodShortLabel = $fromCarbon->format('M Y');
        } else {
            $periodLabel = strtoupper($fromCarbon->format('F Y')) . ' - ' . strtoupper($toCarbon->format('F Y'));
            $periodShortLabel = $fromCarbon->format('M Y') . ' - ' . $toCarbon->format('M Y');
        }

        if ($unitId) {
            $targetRecord = \App\Models\OperationStageTarget::where('operation_stage_id', $stageId)->where('service_provider_id', $unitId)->first();

            if ($targetRecord && $targetRecord->target_qty > 0) {
                $defaultDailyTarget = (float) $targetRecord->target_qty;
            } elseif ($finishingStage && $finishingStage->target > 0) {
                $defaultDailyTarget = (float) $finishingStage->target;
            } else {
                $defaultDailyTarget = 2105; 
            }

            $unitDisplayName = $unitName ? strtoupper($unitName) : 'UNIT #' . $unitId;
            $reportTitle = 'FINAL FINISHING AVERAGE REPORT ' . $periodLabel . ' - ' . $unitDisplayName . ' (' . number_format($defaultDailyTarget) . ' PCS)';
            $unitLabel = $unitDisplayName;
        } else {
            $sumTargets = (float) \App\Models\OperationStageTarget::where('operation_stage_id', $stageId)->sum('target_qty');

            if ($sumTargets > 0) {
                $defaultDailyTarget = $sumTargets;
            } elseif ($finishingStage && $finishingStage->target > 0) {
                $defaultDailyTarget = (float) $finishingStage->target;
            } else {
                $defaultDailyTarget = 2105; 
            }

            $reportTitle = 'FINAL FINISHING AVERAGE REPORT ' . $periodLabel . ' (ALL UNITS - ' . number_format($defaultDailyTarget) . ' PCS)';
            $unitLabel = 'ALL UNITS';
        }

        $otDailyTarget = (int) round($defaultDailyTarget + ($defaultDailyTarget / 8));

        $assignQuery = TaskAssignEmployee::whereBetween('issue_date', [$calcFromDate, $calcToDate])
            ->whereNull('deleted_at')
            ->with(['service', 'task.jobCard.serviceProvider']);

        if ($unitId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($unitId) {
                $jq->where('service_provider_id', $unitId);
            });
        }
        $allAssignments = $assignQuery->get();

        $receiptQuery = DB::table('production_receipts')
            ->join('production_receipt_items', 'production_receipts.id', '=', 'production_receipt_items.production_receipt_id')
            ->whereBetween('production_receipts.receipt_date', [$calcFromDate, $calcToDate])
            ->select('production_receipts.receipt_date', DB::raw('SUM(production_receipt_items.completed_qty) as total_delivery'))
            ->groupBy('production_receipts.receipt_date');

        if ($unitId) {
            $receiptQuery->join('job_card_entries', 'production_receipts.job_card_id', '=', 'job_card_entries.id')
                ->where('job_card_entries.service_provider_id', $unitId);
        }
        $dailyDeliveries = $receiptQuery->pluck('total_delivery', 'receipt_date')->toArray();

        $attendanceQuery = Attendance::whereBetween('date', [$calcFromDate, $calcToDate])
            ->where(function ($q) {
                $q->where('status', 'Overtime')->orWhere('work_hours', '>', 9);
            });

        if ($unitId) {
            $unitEmpCodes = DB::table('users')->where('service_provider_id', $unitId)->whereNotNull('emp_id')->whereNull('deleted_at')->pluck('emp_id')->filter()->toArray();

            if (!empty($unitEmpCodes)) {
                $attendanceQuery->whereIn('emp_code', $unitEmpCodes);
            }
        }

        $attendanceRecords = $attendanceQuery->get();
        $otDates = $attendanceRecords->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->flip()->toArray();

        $curr = Carbon::parse($calcFromDate);
        $end = Carbon::parse($calcToDate);
        $activeDates = collect();
        while ($curr->lte($end)) {
            if (!$curr->isSunday()) {
                $activeDates->push($curr->format('Y-m-d'));
            }
            $curr->addDay();
        }

        $assignsByDate = $allAssignments->groupBy(fn($a) => Carbon::parse($a->issue_date)->format('Y-m-d'));

        $rows = [];
        $summaryColTotals = [
            'trimming' => 0,
            'checking' => 0,
            'ironing' => 0,
            'despatch' => 0,
            'delivery' => 0,
            'target' => 0,
        ];

        $workedDaysCount = 0;
        $countSeq = 0;

        foreach ($activeDates as $dateStr) {
            $cDate = Carbon::parse($dateStr);
            $dFormatted = $cDate->format('d-m-Y');

            $dayAssigns = $assignsByDate->get($dateStr, collect());

            $trimmingQty = 0;
            $checkingQty = 0;
            $ironingQty = 0;
            $despatchQty = 0;

            foreach ($dayAssigns as $as) {
                $sName = strtoupper($as->service ? $as->service->service_name : '');
                $sCode = strtoupper($as->service ? $as->service->service_code : '');

                if (str_contains($sName, 'TRIM') || str_contains($sCode, 'TRIM')) {
                    $trimmingQty += (float) ($as->completed_qty > 0 ? $as->completed_qty : $as->issue_qty);
                }
                if (str_contains($sName, 'CHECK') || str_contains($sCode, 'CHECK')) {
                    $checkingQty += (float) ($as->completed_qty > 0 ? $as->completed_qty : $as->issue_qty);
                }
                if (str_contains($sName, 'IRON') || str_contains($sCode, 'IRON')) {
                    $ironingQty += (float) $as->issue_qty;
                    $despatchQty += (float) $as->completed_qty;
                }
            }

            $deliveryQty = (float) ($dailyDeliveries[$dateStr] ?? 0);

            $hasOt = isset($otDates[$dateStr]);
            $targetPerDay = $hasOt ? $otDailyTarget : $defaultDailyTarget;

            $effPercent = $targetPerDay > 0 ? round(($despatchQty / $targetPerDay) * 100, 1) : 0;
            if ($ironingQty > 0 || $despatchQty > 0) {
                $countSeq += 2;
                $displayCount = $countSeq;
                $workedDaysCount++;
            } else {
                $displayCount = '';
            }

            $summaryColTotals['trimming'] += $trimmingQty;
            $summaryColTotals['checking'] += $checkingQty;
            $summaryColTotals['ironing'] += $ironingQty;
            $summaryColTotals['despatch'] += $despatchQty;
            $summaryColTotals['delivery'] += $deliveryQty;
            $summaryColTotals['target'] += $targetPerDay;

            $rows[] = [
                'date' => $dFormatted,
                'trimming' => $trimmingQty > 0 ? number_format($trimmingQty, 0) : '-',
                'checking' => $checkingQty > 0 ? number_format($checkingQty, 0) : '-',
                'ironing' => $ironingQty > 0 ? number_format($ironingQty, 0) : '-',
                'despatch' => $despatchQty > 0 ? number_format($despatchQty, 0) : '-',
                'delivery' => $deliveryQty > 0 ? number_format($deliveryQty, 0) : '-',
                'efficiency' => $despatchQty > 0 ? ($effPercent . '%') : '-',
                'efficiency_val' => $effPercent,
                'target_per_day' => number_format($targetPerDay, 0),
                'count' => $displayCount !== '' ? $displayCount : '-',
                '_search_text' => strtolower($dFormatted . ' ' . $trimmingQty . ' ' . $checkingQty . ' ' . $ironingQty . ' ' . $despatchQty . ' ' . $deliveryQty)
            ];
        }

        $overallEfficiency = $summaryColTotals['target'] > 0  ? round(($summaryColTotals['despatch'] / $summaryColTotals['target']) * 100, 1) : 0;

        $totalRow = [
            'date' => 'TOTAL',
            'trimming' => $summaryColTotals['trimming'] > 0 ? number_format($summaryColTotals['trimming'], 0) : '-',
            'checking' => $summaryColTotals['checking'] > 0 ? number_format($summaryColTotals['checking'], 0) : '-',
            'ironing' => number_format($summaryColTotals['ironing'], 0),
            'despatch' => number_format($summaryColTotals['despatch'], 0),
            'delivery' => number_format($summaryColTotals['delivery'], 0),
            'efficiency' => $overallEfficiency . '%',
            'target_per_day' => '-',
            'count' => '-'
        ];

        $effectiveDays = max(1, $workedDaysCount);
        $avgRow = [
            'date' => 'AVERAGE',
            'trimming' => $summaryColTotals['trimming'] > 0 ? number_format(round($summaryColTotals['trimming'] / $effectiveDays), 0) : '-',
            'checking' => $summaryColTotals['checking'] > 0 ? number_format(round($summaryColTotals['checking'] / $effectiveDays), 0) : '-',
            'ironing' => number_format(round($summaryColTotals['ironing'] / $effectiveDays), 0),
            'despatch' => number_format(round($summaryColTotals['despatch'] / $effectiveDays), 0),
            'delivery' => number_format(round($summaryColTotals['delivery'] / $effectiveDays), 0),
            'efficiency' => '-',
            'target_per_day' => '-',
            'count' => '-'
        ];

        $meta = [
            'report_title' => $reportTitle,
            'report_period' => $periodShortLabel,
            'base_target' => $defaultDailyTarget,
            'unit_label' => $unitLabel,
            'worked_days' => $workedDaysCount,
            'total_row' => $totalRow,
            'average_row' => $avgRow,
        ];

        return [
            'calcFromDate' => $calcFromDate,
            'calcToDate' => $calcToDate,
            'unitId' => $unitId,
            'unitName' => $unitName,
            'rows' => $rows,
            'totalRow' => $totalRow,
            'avgRow' => $avgRow,
            'meta' => $meta
        ];
    }

    public function getUnitLineAverageData(Request $request)
    {
        $fromDate = $this->parseReportDate($request->from_date);
        $toDate = $this->parseReportDate($request->to_date);
        $unitId = $request->unit_id;
        $brandId = $request->brand_id;
        $unitName = null;
        if ($unitId) {
            $unitObj = ServiceProvider::find($unitId);
            if ($unitObj) {
                $unitName = $unitObj->name;
            }
        }

        if (!$fromDate || !$toDate) {
            $latestDate = TaskAssignEmployee::whereNotNull('issue_date')->max('issue_date');
            if (!$latestDate) {
                $latestDate = date('Y-m-d');
            }
            $latestCarbon = Carbon::parse($latestDate);
            $calcFromDate = $fromDate ?: $latestCarbon->copy()->startOfMonth()->format('Y-m-d');
            $calcToDate = $toDate ?: $latestCarbon->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $calcFromDate = $fromDate;
            $calcToDate = $toDate;
        }

        $fromCarbon = Carbon::parse($calcFromDate);
        $toCarbon = Carbon::parse($calcToDate);
        if ($fromCarbon->format('Y-m') === $toCarbon->format('Y-m')) {
            $periodLabel = strtoupper($fromCarbon->format('F Y'));
            $periodShortLabel = $fromCarbon->format('M Y');
        } else {
            $periodLabel = strtoupper($fromCarbon->format('F Y')) . ' - ' . strtoupper($toCarbon->format('F Y'));
            $periodShortLabel = $fromCarbon->format('M Y') . ' - ' . $toCarbon->format('M Y');
        }

        // Operation Stage for Stitching / Assemble
        $stitchingStage = OperationStage::where('operation_stage_name', 'like', '%ASSEMBLE%')
            ->orWhere('operation_stage_name', 'like', '%STITCH%')
            ->first();
        $stitchingStageId = $stitchingStage ? $stitchingStage->id : 3;

        // Target Resolution from Master (strictly required from master)
        if ($unitId) {
            $targetRecord = OperationStageTarget::where('service_provider_id', $unitId)
                ->where(function ($q) use ($stitchingStageId) {
                    $q->where('operation_stage_id', $stitchingStageId)
                      ->orWhereIn('operation_stage_id', [2, 3]);
                })->first();

            if (!$targetRecord) {
                $targetRecord = OperationStageTarget::where('service_provider_id', $unitId)->first();
            }

            if ($targetRecord && (float)$targetRecord->target_qty > 0) {
                $dailyTarget = (float)$targetRecord->target_qty;
            } elseif ($stitchingStage && (float)$stitchingStage->target > 0) {
                $dailyTarget = (float)$stitchingStage->target;
            } else {
                $dailyTarget = 900;
            }

            $unitDisplayName = $unitName ? strtoupper($unitName) : 'UNIT #' . $unitId;
            $reportTitle = $unitDisplayName . ' AVERAGE REPORT - ' . $periodLabel;
            $unitLabel = $unitDisplayName;
        } else {
            $sumTargets = (float) OperationStageTarget::whereIn('operation_stage_id', [2, 3])->sum('target_qty');
            if ($sumTargets > 0) {
                $dailyTarget = $sumTargets;
            } elseif ($stitchingStage && (float)$stitchingStage->target > 0) {
                $dailyTarget = (float)$stitchingStage->target;
            } else {
                $dailyTarget = 900;
            }

            $unitDisplayName = 'ALL UNITS';
            $reportTitle = 'ALL UNITS AVERAGE REPORT - ' . $periodLabel;
            $unitLabel = 'ALL UNITS';
        }

        // Assignments query
        $assignQuery = TaskAssignEmployee::whereBetween('issue_date', [$calcFromDate, $calcToDate])
            ->whereNull('deleted_at')
            ->with(['service', 'task.jobCard.serviceProvider']);

        if ($unitId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($unitId) {
                $jq->where('service_provider_id', $unitId);
            });
        }
        if ($brandId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($brandId) {
                $jq->where('brand_id', $brandId);
            });
        }
        $allAssignments = $assignQuery->get();

        // Deliveries to Head Office query
        $receiptQuery = DB::table('production_receipts')
            ->join('production_receipt_items', 'production_receipts.id', '=', 'production_receipt_items.production_receipt_id')
            ->whereBetween('production_receipts.receipt_date', [$calcFromDate, $calcToDate])
            ->select('production_receipts.receipt_date', DB::raw('SUM(production_receipt_items.completed_qty) as total_delivery'))
            ->groupBy('production_receipts.receipt_date');

        if ($unitId) {
            $receiptQuery->join('job_card_entries', 'production_receipts.job_card_id', '=', 'job_card_entries.id')
                ->where('job_card_entries.service_provider_id', $unitId);
            if ($brandId) {
                $receiptQuery->where('job_card_entries.brand_id', $brandId);
            }
        }
        $dailyDeliveries = $receiptQuery->pluck('total_delivery', 'receipt_date')->toArray();

        // Attendance OT query
        $attendanceQuery = Attendance::whereBetween('date', [$calcFromDate, $calcToDate])
            ->where(function ($q) {
                $q->where('status', 'Overtime')->orWhere('work_hours', '>', 9);
            });

        if ($unitId) {
            $unitEmpCodes = DB::table('users')->where('service_provider_id', $unitId)->whereNotNull('emp_id')->whereNull('deleted_at')->pluck('emp_id')->filter()->toArray();
            if (!empty($unitEmpCodes)) {
                $attendanceQuery->whereIn('emp_code', $unitEmpCodes);
            }
        }
        $otDates = $attendanceQuery->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->flip()->toArray();

        // Active non-Sunday dates
        $curr = Carbon::parse($calcFromDate);
        $end = Carbon::parse($calcToDate);
        $activeDates = collect();
        while ($curr->lte($end)) {
            if (!$curr->isSunday()) {
                $activeDates->push($curr->format('Y-m-d'));
            }
            $curr->addDay();
        }

        $totalWorkingDaysInMonth = $activeDates->count();
        $assignsByDate = $allAssignments->groupBy(fn($a) => Carbon::parse($a->issue_date)->format('Y-m-d'));

        $rows = [];
        $summaryColTotals = [
            'n_patti' => 0,
            'back_shoulder' => 0,
            'sleeve' => 0,
            'collar' => 0,
            'cuff' => 0,
            'assemble' => 0,
            'kaja' => 0,
            'button' => 0,
            'trimming' => 0,
            'checking' => 0,
            'ho_deliver' => 0,
        ];

        $workedDaysCount = 0;
        $cumulativeDeliveries = 0;
        $cumulativeTarget = 0;

        foreach ($activeDates as $dateStr) {
            $cDate = Carbon::parse($dateStr);
            $dFormatted = $cDate->format('d-m-Y');

            $dayAssigns = $assignsByDate->get($dateStr, collect());

            $colQuantities = [
                'n_patti' => 0,
                'back_shoulder' => 0,
                'sleeve' => 0,
                'collar' => 0,
                'cuff' => 0,
                'assemble' => 0,
                'kaja' => 0,
                'button' => 0,
                'trimming' => 0,
                'checking' => 0,
            ];

            foreach ($dayAssigns as $as) {
                $sName = strtoupper($as->service ? $as->service->service_name : '');
                $sCode = strtoupper($as->service ? $as->service->service_code : '');
                $qty = (float) ($as->completed_qty > 0 ? $as->completed_qty : $as->issue_qty);

                if (str_contains($sName, 'N.PATTI') || str_contains($sCode, 'N.PATTI') || str_contains($sName, 'BUTTON PATTI')) {
                    $colQuantities['n_patti'] += $qty;
                } elseif (str_contains($sName, 'BACK & SHOULDER') || str_contains($sCode, 'BACK & SHOULDER') || (str_contains($sName, 'SHOULDER') && !str_contains($sName, 'SLEEVE'))) {
                    $colQuantities['back_shoulder'] += $qty;
                } elseif ((str_contains($sName, 'SLEEVE') || str_contains($sCode, 'SLEEVE')) && !str_contains($sName, 'SIDE ATTACH')) {
                    $colQuantities['sleeve'] += $qty;
                } elseif (str_contains($sName, 'COLLAR') || str_contains($sCode, 'COLLAR')) {
                    $colQuantities['collar'] += $qty;
                } elseif (str_contains($sName, 'CUFF') || str_contains($sCode, 'CUFF')) {
                    $colQuantities['cuff'] += $qty;
                } elseif (str_contains($sName, 'ASSAMBLE') || str_contains($sName, 'ASSEMBLE') || str_contains($sName, 'FRONT ATTACH') || str_contains($sName, 'SIDE ATTACH')) {
                    $colQuantities['assemble'] += $qty;
                } elseif (str_contains($sName, 'KAJA') || str_contains($sCode, 'KAJA')) {
                    $colQuantities['kaja'] += $qty;
                } elseif (str_contains($sName, 'BUTTON') || str_contains($sCode, 'BUTTON')) {
                    $colQuantities['button'] += $qty;
                } elseif (str_contains($sName, 'TRIM') || str_contains($sCode, 'TRIM')) {
                    $colQuantities['trimming'] += $qty;
                } elseif (str_contains($sName, 'CHECK') || str_contains($sCode, 'CHECK')) {
                    $colQuantities['checking'] += $qty;
                }
            }

            $deliveryQty = (float) ($dailyDeliveries[$dateStr] ?? 0);

            // Cumulative MTD Efficiency calculation
            $workedDaysCount++;
            $cumulativeDeliveries += $deliveryQty;
            $cumulativeTarget += $dailyTarget;
            $effPercent = $cumulativeTarget > 0 ? round(($cumulativeDeliveries / $cumulativeTarget) * 100, 1) : 0;

            $hasOt = isset($otDates[$dateStr]);
            $otText = $hasOt ? 'YES' : 'NO';

            foreach ($colQuantities as $k => $v) {
                $summaryColTotals[$k] += $v;
            }
            $summaryColTotals['ho_deliver'] += $deliveryQty;

            $rows[] = [
                'date' => $dFormatted,
                'n_patti' => $colQuantities['n_patti'] > 0 ? number_format($colQuantities['n_patti'], 0) : '-',
                'back_shoulder' => $colQuantities['back_shoulder'] > 0 ? number_format($colQuantities['back_shoulder'], 0) : '-',
                'sleeve' => $colQuantities['sleeve'] > 0 ? number_format($colQuantities['sleeve'], 0) : '-',
                'collar' => $colQuantities['collar'] > 0 ? number_format($colQuantities['collar'], 0) : '-',
                'cuff' => $colQuantities['cuff'] > 0 ? number_format($colQuantities['cuff'], 0) : '-',
                'assemble' => $colQuantities['assemble'] > 0 ? number_format($colQuantities['assemble'], 0) : '-',
                'kaja' => $colQuantities['kaja'] > 0 ? number_format($colQuantities['kaja'], 0) : '-',
                'button' => $colQuantities['button'] > 0 ? number_format($colQuantities['button'], 0) : '-',
                'trimming' => $colQuantities['trimming'] > 0 ? number_format($colQuantities['trimming'], 0) : '-',
                'checking' => $colQuantities['checking'] > 0 ? number_format($colQuantities['checking'], 0) : '-',
                'ho_deliver' => $deliveryQty > 0 ? number_format($deliveryQty, 0) : '-',
                'efficiency' => $effPercent . '%',
                'efficiency_val' => $effPercent,
                'ot' => $otText,
                '_search_text' => strtolower($dFormatted . ' ' . implode(' ', $colQuantities) . ' ' . $deliveryQty . ' ' . $otText)
            ];
        }

        // Store Stock: Checked qty ready in unit store waiting for delivery
        $storeStockQty = max(0, $summaryColTotals['checking'] - $summaryColTotals['ho_deliver']);
        $storeStockEff = $cumulativeTarget > 0 ? round(($storeStockQty / $cumulativeTarget) * 100, 1) : 0;

        $storeStockRow = [
            'label' => 'Store Stock',
            'ho_deliver' => $storeStockQty > 0 ? number_format($storeStockQty, 0) : '-',
            'efficiency' => $storeStockQty > 0 ? ($storeStockEff . '%') : '-',
        ];

        // Overall month efficiency
        $overallEfficiency = $cumulativeTarget > 0 ? round(($summaryColTotals['ho_deliver'] / $cumulativeTarget) * 100, 1) : 0;

        $totalRow = [
            'date' => 'Total',
            'n_patti' => $summaryColTotals['n_patti'] > 0 ? number_format($summaryColTotals['n_patti'], 0) : '-',
            'back_shoulder' => $summaryColTotals['back_shoulder'] > 0 ? number_format($summaryColTotals['back_shoulder'], 0) : '-',
            'sleeve' => $summaryColTotals['sleeve'] > 0 ? number_format($summaryColTotals['sleeve'], 0) : '-',
            'collar' => $summaryColTotals['collar'] > 0 ? number_format($summaryColTotals['collar'], 0) : '-',
            'cuff' => $summaryColTotals['cuff'] > 0 ? number_format($summaryColTotals['cuff'], 0) : '-',
            'assemble' => $summaryColTotals['assemble'] > 0 ? number_format($summaryColTotals['assemble'], 0) : '-',
            'kaja' => $summaryColTotals['kaja'] > 0 ? number_format($summaryColTotals['kaja'], 0) : '-',
            'button' => $summaryColTotals['button'] > 0 ? number_format($summaryColTotals['button'], 0) : '-',
            'trimming' => $summaryColTotals['trimming'] > 0 ? number_format($summaryColTotals['trimming'], 0) : '-',
            'checking' => $summaryColTotals['checking'] > 0 ? number_format($summaryColTotals['checking'], 0) : '-',
            'ho_deliver' => $summaryColTotals['ho_deliver'] > 0 ? number_format($summaryColTotals['ho_deliver'], 0) : '-',
            'efficiency' => $overallEfficiency . '%',
            'ot' => '-'
        ];

        $divDays = $workedDaysCount > 0 ? $workedDaysCount : 1;
        $avgRow = [
            'date' => 'AVG',
            'n_patti' => number_format($summaryColTotals['n_patti'] / $divDays, 0),
            'back_shoulder' => number_format($summaryColTotals['back_shoulder'] / $divDays, 0),
            'sleeve' => number_format($summaryColTotals['sleeve'] / $divDays, 0),
            'collar' => number_format($summaryColTotals['collar'] / $divDays, 0),
            'cuff' => number_format($summaryColTotals['cuff'] / $divDays, 0),
            'assemble' => number_format($summaryColTotals['assemble'] / $divDays, 0),
            'kaja' => number_format($summaryColTotals['kaja'] / $divDays, 0),
            'button' => number_format($summaryColTotals['button'] / $divDays, 0),
            'trimming' => number_format($summaryColTotals['trimming'] / $divDays, 0),
            'checking' => number_format($summaryColTotals['checking'] / $divDays, 0),
            'ho_deliver' => number_format($summaryColTotals['ho_deliver'] / $divDays, 0),
            'efficiency' => '-',
            'ot' => '-'
        ];

        $meta = [
            'report_title' => $reportTitle,
            'report_period' => $periodShortLabel,
            'target_qty' => number_format($dailyTarget, 0),
            'unit_label' => $unitLabel,
            'working_days' => $totalWorkingDaysInMonth,
            'monthly_target' => number_format($dailyTarget * $totalWorkingDaysInMonth, 0),
            'store_stock_row' => $storeStockRow,
            'total_row' => $totalRow,
            'avg_row' => $avgRow,
        ];

        return [
            'calcFromDate' => $calcFromDate,
            'calcToDate' => $calcToDate,
            'unitId' => $unitId,
            'unitName' => $unitName,
            'rows' => $rows,
            'totalRow' => $totalRow,
            'avgRow' => $avgRow,
            'meta' => $meta
        ];
    }

    public function exportCuttingSectionAverageExcel(Request $request)
    {
        $reportData = $this->getCuttingSectionAverageData($request);
        $period = str_replace([' ', '/', '\\', ':', '*'], '_', $reportData['meta']['report_period'] ?? date('M_Y'));
        $fileName = 'HO_CUTTING_SECTION_AVERAGE_REPORT_' . $period . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new CuttingSectionAverageExport(
                $reportData['cuttingEmployees'],
                $reportData['cuttingPlants'],
                $reportData['rows'],
                $reportData['totalRow'],
                $reportData['avgRow'],
                $reportData['meta']
            ),
            $fileName
        );
    }

    public function exportCuttingSectionAveragePdf(Request $request)
    {
        $reportData = $this->getCuttingSectionAverageData($request);
        $period = str_replace([' ', '/', '\\', ':', '*'], '_', $reportData['meta']['report_period'] ?? date('M_Y'));
        $fileName = 'HO_CUTTING_SECTION_AVERAGE_REPORT_' . $period . '_' . date('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('reports.production_report._cutting_section_average_print', [
            'cuttingEmployees' => $reportData['cuttingEmployees'],
            'cuttingPlants' => $reportData['cuttingPlants'],
            'rows' => $reportData['rows'],
            'totalRow' => $reportData['totalRow'],
            'avgRow' => $reportData['avgRow'],
            'meta' => $reportData['meta'],
            'unitName' => $reportData['unitName'] ?? null,
            'isPrint' => false
        ]);
        $pdf->setPaper('A3', 'landscape');

        return $pdf->stream($fileName);
    }

    public function exportCuttingSectionAveragePrint(Request $request)
    {
        $reportData = $this->getCuttingSectionAverageData($request);

        return view('reports.production_report._cutting_section_average_print', [
            'cuttingEmployees' => $reportData['cuttingEmployees'],
            'cuttingPlants' => $reportData['cuttingPlants'],
            'rows' => $reportData['rows'],
            'totalRow' => $reportData['totalRow'],
            'avgRow' => $reportData['avgRow'],
            'meta' => $reportData['meta'],
            'unitName' => $reportData['unitName'] ?? null,
            'isPrint' => true
        ]);
    }

    private function parseReportDate($dateStr)
    {
        if (empty($dateStr) || $dateStr === 'DD-MM-YYYY' || trim($dateStr) === '') {
            return null;
        }
        try {
            return Carbon::createFromFormat('d-m-Y', trim($dateStr))->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                $ts = strtotime(trim($dateStr));
                return ($ts !== false && $ts > 0) ? date('Y-m-d', $ts) : null;
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    public function getStageWiseWipData(Request $request)
    {
        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLength = $request->get('length');
        $length = ($rawLength !== null && intval($rawLength) == -1) ? -1 : intval($rawLength > 0 ? $rawLength : 10);
        $isExport = ($request->get('export') == 1) || ($request->get('all') == 1) || ($length < 0);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');
        $search = trim($search);

        $stageId = $request->operation_stage_id ?: $request->stage_id;
        if (!$stageId) {
            $stageId = OperationStage::where('operation_stage_name', 'like', '%CUTTING%')->value('id') ?? 1;
        }
        $currentStage = OperationStage::find($stageId);

        $fromDate = $this->parseReportDate($request->from_date);
        $toDate = $this->parseReportDate($request->to_date);
        $brandId = $request->brand_id;
        $unitId = $request->unit_id;

        $services = ProductionService::where('operation_stage_id', $stageId)->whereNull('deleted_at')->where('status', 'Active')->orderBy('sequence')->orderBy('id')->get(['id', 'service_name', 'service_code']);

        $jcQuery = JobCardEntry::with([
            'brand',
            'fabricDetails.stockEntry.stockEntryItems.style',
            'tasks' => function ($q) use ($stageId) {
                $q->whereNull('deleted_at')
                  ->where(function ($sq) use ($stageId) {
                      $sq->where('stage_id', $stageId)
                         ->orWhereHas('stage', function ($psq) use ($stageId) {
                             $psq->where('operation_stage_id', $stageId);
                         });
                  });
            },
            'tasks.assignments' => function ($q) {
                $q->whereNull('deleted_at');
            }
        ])
        ->where('grand_total_qty', '>', 0)
        ->whereNull('deleted_at');

        $jcQuery->whereDoesntHave('tasks', function ($tq) use ($stageId) {
            $tq->whereNull('deleted_at')
               ->where(function ($sq) use ($stageId) {
                   $sq->where('stage_id', $stageId)
                      ->orWhereHas('stage', function ($psq) use ($stageId) {
                          $psq->where('operation_stage_id', $stageId);
                      });
               })
               ->where('status', 'Completed');
        });

        if ($fromDate) {
            $jcQuery->where('job_card_date', '>=', $fromDate);
        }
        if ($toDate) {
            $jcQuery->where('job_card_date', '<=', $toDate);
        }
        if ($brandId) {
            $jcQuery->where('brand_id', $brandId);
        }
        if ($unitId) {
            $jcQuery->where('service_provider_id', $unitId);
        }

        if ($search !== '') {
            $jcQuery->where(function ($q) use ($search) {
                $q->where('job_card_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('brand_name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fabricDetails', function ($fq) use ($search) {
                      $fq->where('art_no', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = (clone $jcQuery)->count();
        $recordsFiltered = $totalRecords;

        $pageQuery = (clone $jcQuery)->orderBy('job_card_date', 'desc')->orderBy('id', 'desc');
        if (!$isExport) {
            $pageQuery->skip($start)->take($length);
        }
        $pageJobCards = $pageQuery->with([
            'brand',
            'fabricDetails',
            'tasks' => function ($q) { $q->whereNull('deleted_at'); },
            'tasks.assignments' => function ($q) { $q->whereNull('deleted_at'); }
        ])->get();

        $pageJobCardIds = $pageJobCards->pluck('id')->toArray();
        $allJcIds = (clone $jcQuery)->pluck('id')->toArray();

        $pageSeIds = $pageJobCards->flatMap->fabricDetails->pluck('stock_entry_id')->filter()->unique()->toArray();
        $seStyles = [];
        if (!empty($pageSeIds)) {
            $seStyles = DB::table('stock_entry_items')->join('styles', 'stock_entry_items.style_id', '=', 'styles.id')->whereIn('stock_entry_items.stock_entry_id', $pageSeIds)->whereNull('stock_entry_items.deleted_at')->select('stock_entry_items.stock_entry_id', 'styles.code', 'styles.style_name', 'stock_entry_items.art_no')->distinct()->get()->groupBy('stock_entry_id');
        }

        $nextSchedules = DB::table('process_schedules')->whereIn('job_card_entry_id', $pageJobCardIds)->where('operation_stage_id', '>', $stageId)->whereNotNull('start_date')->where('start_date', '!=', '0000-00-00')->orderBy('operation_stage_id', 'asc')->get()->groupBy('job_card_entry_id');

        $receipts = DB::table('production_receipts')->join('production_receipt_items', 'production_receipts.id', '=', 'production_receipt_items.production_receipt_id')->whereIn('production_receipts.job_card_id', $pageJobCardIds)->select('production_receipts.job_card_id', DB::raw('SUM(production_receipt_items.completed_qty) as total_received'))->groupBy('production_receipts.job_card_id')->pluck('total_received', 'production_receipts.job_card_id')->toArray();

        $movements = DB::table('production_movements')->whereIn('job_card_id', $pageJobCardIds)->where('operation_stage_id', $stageId)->whereNull('deleted_at')->select('job_card_id', DB::raw('SUM(outward_qty) as total_outward'))->groupBy('job_card_id')->pluck('total_outward', 'job_card_id')->toArray();

        $totalCuttingSum = (float) (clone $jcQuery)->sum('grand_total_qty');
        $totalFsSum = (float) (clone $jcQuery)->sum('total_qty_fs');
        $totalHsSum = (float) (clone $jcQuery)->sum('total_qty_hs');
        $totalMtrsSum = (float) DB::table('job_card_fabric_details')->whereIn('job_card_entry_id', $allJcIds)->whereNull('deleted_at')->sum('mtr');

        $totalStoreStockSum = (float) DB::table('production_receipts')->join('production_receipt_items', 'production_receipts.id', '=', 'production_receipt_items.production_receipt_id')->whereIn('production_receipts.job_card_id', $allJcIds)->sum('production_receipt_items.completed_qty');

        $serviceWipSums = DB::table('task_assign_employees')->join('tasks', 'task_assign_employees.task_id', '=', 'tasks.id')->whereIn('tasks.job_card_entry_id', $allJcIds)->whereNull('task_assign_employees.deleted_at')->whereNull('tasks.deleted_at')->select('task_assign_employees.service_id', DB::raw('SUM(GREATEST(0, task_assign_employees.issue_qty - task_assign_employees.completed_qty)) as total_wip'))->groupBy('task_assign_employees.service_id')->pluck('total_wip', 'task_assign_employees.service_id')->toArray();

        $serviceTotals = [];
        foreach ($services as $svc) {
            $serviceTotals[$svc->id] = (float) ($serviceWipSums[$svc->id] ?? 0);
        }

        $usedBrandIds = DB::table('job_card_entries')->whereNotNull('brand_id')->distinct()->pluck('brand_id')->toArray();
        $allBrands = Brand::whereNull('deleted_at')->whereIn('id', $usedBrandIds)->orderBy('brand_name')->get(['id', 'brand_name', 'code']);

        $allStyles = Style::whereNull('deleted_at')->orderBy('id')->get(['id', 'style_name', 'code']);

        $styleKeys = [];
        foreach ($allStyles as $st) {
            $sKey = $st->code ?: $st->style_name;
            $styleKeys[$sKey] = $sKey;
        }

        $matrix = [];
        foreach ($allBrands as $br) {
            $bKey = $br->code ?: $br->brand_name;
            $matrix[$bKey] = array_fill_keys(array_keys($styleKeys), 0);
            $matrix[$bKey]['TOTAL'] = 0;
        }
        $matrix['TOTAL'] = array_fill_keys(array_keys($styleKeys), 0);
        $matrix['TOTAL']['TOTAL'] = 0;

        $matrixRaw = DB::table('job_card_entries as jc')->leftJoin('brands as b', 'jc.brand_id', '=', 'b.id')->whereIn('jc.id', $allJcIds)->select('jc.id', 'jc.grand_total_qty', 'b.code as brand_code', 'b.brand_name')->get();

        $allFdSeList = DB::table('job_card_fabric_details')->whereIn('job_card_entry_id', $allJcIds)->whereNull('deleted_at')->whereNotNull('stock_entry_id')->select('job_card_entry_id', 'stock_entry_id')->distinct()->get();

        $allSeIds = $allFdSeList->pluck('stock_entry_id')->unique()->toArray();
        $seToStyleMap = [];
        if (!empty($allSeIds)) {
            $seToStyleMap = DB::table('stock_entry_items')->join('styles', 'stock_entry_items.style_id', '=', 'styles.id')->whereIn('stock_entry_items.stock_entry_id', $allSeIds)->whereNull('stock_entry_items.deleted_at')->select('stock_entry_items.stock_entry_id', DB::raw('COALESCE(styles.code, styles.style_name) as style_code'))->distinct()->pluck('style_code', 'stock_entry_items.stock_entry_id')->toArray();
        }

        $jcPrimaryStyle = [];
        foreach ($allFdSeList as $fdSe) {
            if (!isset($jcPrimaryStyle[$fdSe->job_card_entry_id]) && isset($seToStyleMap[$fdSe->stock_entry_id])) {
                $jcPrimaryStyle[$fdSe->job_card_entry_id] = $seToStyleMap[$fdSe->stock_entry_id];
            }
        }

        foreach ($matrixRaw as $mRow) {
            $bKey = $mRow->brand_code ?: ($mRow->brand_name ?: 'OTHER');
            $stCode = $jcPrimaryStyle[$mRow->id] ?? 'PLN';
            if (!isset($styleKeys[$stCode])) {
                $stCode = 'PLN';
            }
            $qty = (float) $mRow->grand_total_qty;
            if (!isset($matrix[$bKey])) {
                $matrix[$bKey] = array_fill_keys(array_keys($styleKeys), 0);
                $matrix[$bKey]['TOTAL'] = 0;
            }
            $matrix[$bKey][$stCode] = ($matrix[$bKey][$stCode] ?? 0) + $qty;
            $matrix[$bKey]['TOTAL'] += $qty;
            $matrix['TOTAL'][$stCode] = ($matrix['TOTAL'][$stCode] ?? 0) + $qty;
            $matrix['TOTAL']['TOTAL'] += $qty;
        }

        foreach ($matrix as $bKey => $bData) {
            if ($bKey !== 'TOTAL' && empty($bData['TOTAL'])) {
                unset($matrix[$bKey]);
            }
        }

        $rows = [];
        $index = $start + 1;
        $moreThan5DaysCount = 0;

        foreach ($pageJobCards as $jc) {
            $dateStr = $jc->job_card_date ? date('d-m-Y', strtotime($jc->job_card_date)) : '-';
            $cutNo = $jc->job_card_no ?? $jc->reference_no ?? '-';
            $totalMtrs = (float) $jc->fabricDetails->sum('mtr');

            $jcStyles = [];
            $whiteArtNos = [];
            foreach ($jc->fabricDetails as $fd) {
                $fdArt = trim($fd->art_no ?? '');
                if ($fd->stock_entry_id && isset($seStyles[$fd->stock_entry_id])) {
                    foreach ($seStyles[$fd->stock_entry_id] as $st) {
                        $stCode = $st->code ?: $st->style_name;
                        $jcStyles[$stCode] = $stCode;
                        $isWhite = (strtoupper($stCode) === 'WHT' || strtoupper($st->style_name ?? '') === 'WHITE');
                        if ($isWhite) {
                            $art = $fdArt ?: trim($st->art_no ?? '');
                            if ($art !== '') {
                                $whiteArtNos[$art] = $art;
                            }
                        }
                    }
                }
            }

            $hasWhiteStyle = false;
            foreach ($jcStyles as $stCode) {
                if (strtoupper($stCode) === 'WHT' || strtoupper($stCode) === 'WHITE') {
                    $hasWhiteStyle = true;
                    break;
                }
            }
            if ($hasWhiteStyle && empty($whiteArtNos)) {
                foreach ($jc->fabricDetails as $fd) {
                    $art = trim($fd->art_no ?? '');
                    if ($art !== '') {
                        $whiteArtNos[$art] = $art;
                    }
                }
            }

            $styleParts = [];
            foreach ($jcStyles as $stCode) {
                $isWhite = (strtoupper($stCode) === 'WHT' || strtoupper($stCode) === 'WHITE');
                if ($isWhite && !empty($whiteArtNos)) {
                    $styleParts[] = $stCode . ' - ' . implode(', ', $whiteArtNos);
                } else {
                    $styleParts[] = $stCode;
                }
            }
            $styleDisplay = !empty($styleParts) ? implode(', ', $styleParts) : 'PLN';

            $fsQty = (float) ($jc->total_qty_fs ?: $jc->fs_qty ?: $jc->fabricDetails->sum('fs_qty'));
            $hsQty = (float) ($jc->total_qty_hs ?: $jc->hs_qty ?: $jc->fabricDetails->sum('hs_qty'));
            $totalCuttingQty = (float) ($jc->grand_total_qty ?: ($fsQty + $hsQty));
            $deliveryDate = $jc->delivery_date ? date('d-m-Y', strtotime($jc->delivery_date)) : '-';

            $daysInWip = $jc->job_card_date ? Carbon::parse($jc->job_card_date)->diffInDays(now()) : 0;
            if ($daysInWip > 5) {
                $moreThan5DaysCount++;
            }

            $nextSched = isset($nextSchedules[$jc->id]) ? $nextSchedules[$jc->id]->first() : null;
            $cuttingSentDate = ($nextSched && $nextSched->start_date) ? date('d-m-Y', strtotime($nextSched->start_date)) : '-';
            $daysTaken = ($nextSched && $nextSched->start_date && $jc->job_card_date) ? Carbon::parse($jc->job_card_date)->diffInDays(Carbon::parse($nextSched->start_date)) : '-';

            $stStock = isset($receipts[$jc->id]) ? (float) $receipts[$jc->id] : (isset($movements[$jc->id]) ? (float) $movements[$jc->id] : 0);

            $allAssignments = $jc->tasks->flatMap->assignments;
            $serviceCols = [];
            foreach ($services as $svc) {
                $svcAss = $allAssignments->where('service_id', $svc->id);
                $issued = $svcAss->sum('issue_qty');
                $completed = $svcAss->sum('completed_qty');
                $wip = max(0, $issued - $completed);

                if ($svcAss->isNotEmpty() && $wip == 0 && $completed > 0) {
                    $serviceCols['svc_' . $svc->id] = '<span class="badge bg-label-success" style="font-size: 0.72rem; padding: 2px 6px;">FINISH</span>';
                } elseif ($wip > 0) {
                    $serviceCols['svc_' . $svc->id] = '<span class="fw-bold text-dark">' . number_format($wip, 0) . '</span>';
                } elseif ($svcAss->isNotEmpty() && $issued == 0) {
                    $serviceCols['svc_' . $svc->id] = '<span class="badge bg-label-warning" style="font-size: 0.72rem; padding: 2px 6px;">PENDING</span>';
                } else {
                    $serviceCols['svc_' . $svc->id] = '-';
                }
            }

            $brandCode = $jc->brand ? ($jc->brand->code ?: $jc->brand->brand_name) : 'OTHER';

            $row = array_merge([
                's_no' => $index++,
                'date' => $dateStr,
                'cut_no' => '<strong>' . htmlspecialchars($cutNo) . '</strong>',
                'mtrs' => number_format($totalMtrs, 1),
                'style' => htmlspecialchars($styleDisplay),
                'fs_qty' => number_format($fsQty, 0),
                'hs_qty' => number_format($hsQty, 0),
                'total_cutting_qty' => '<strong>' . number_format($totalCuttingQty, 0) . '</strong>',
                'delivery_date' => $deliveryDate,
            ], $serviceCols, [
                'days_in_wip' => $daysInWip,
                'is_over_5_days' => $daysInWip > 5,
                'cutting_sent_date' => $cuttingSentDate,
                'days_taken' => $daysTaken,
                'store_stock' => $stStock > 0 ? number_format($stStock, 0) : '-',
                '_search_text' => strtolower($cutNo . ' ' . $styleDisplay . ' ' . $brandCode . ' ' . $dateStr)
            ]);

            $rows[] = $row;
        }

        $pageData = $rows;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pageData,
            'meta' => [
                'stage_id' => $stageId,
                'stage_name' => $currentStage->operation_stage_name ?? 'CUTTING',
                'services' => $services->map(fn($s) => ['id' => $s->id, 'name' => $s->service_name, 'code' => $s->service_code])->values(),
                'more_than_5_days' => $moreThan5DaysCount,
                'totals' => [
                    'mtrs' => number_format($totalMtrsSum, 1),
                    'fs_qty' => number_format($totalFsSum, 0),
                    'hs_qty' => number_format($totalHsSum, 0),
                    'total_cutting_qty' => number_format($totalCuttingSum, 0),
                    'services' => array_map(fn($v) => $v > 0 ? number_format($v, 0) : '-', $serviceTotals),
                    'store_stock' => number_format($totalStoreStockSum, 0),
                ],
                'abstract_matrix' => [
                    'columns' => array_values($styleKeys),
                    'rows' => $matrix,
                ]
            ]
        ]);
    }

    public function getProductionPlanningData(Request $request)
    {
        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLength = $request->get('length');
        $length = ($rawLength !== null && intval($rawLength) == -1) ? -1 : intval($rawLength > 0 ? $rawLength : 10);
        $isExport = ($request->get('export') == 1) || ($request->get('all') == 1) || ($length < 0);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');
        $search = trim($search);

        $fromDate = $this->parseReportDate($request->from_date);
        $toDate = $this->parseReportDate($request->to_date);

        if (!$fromDate && !$toDate) {
            $fromDate = Carbon::today()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        } elseif (!$fromDate) {
            $fromDate = $toDate;
        } elseif (!$toDate) {
            $toDate = $fromDate;
        }

        $stageId = intval($request->get('stage_id', 1));
        if ($stageId <= 0) {
            $stageId = 1;
        }

        $currentStage = OperationStage::find($stageId);
        $stageName = $currentStage ? strtoupper($currentStage->operation_stage_name) : 'CUTTING';

        $unitId = $request->unit_id;
        $brandId = $request->brand_id;

        $assignQuery = TaskAssignEmployee::with([
            'employee:id,name,emp_id',
            'service:id,service_name,service_code,operation_stage_id',
            'task.jobCard.brand',
            'task.stage',
        ])->where(function ($q) use ($stageId) {
            $q->whereHas('service', function ($sq) use ($stageId) {
                $sq->where('operation_stage_id', $stageId);
            })->orWhereHas('task.stage', function ($sq) use ($stageId) {
                $sq->where('operation_stage_id', $stageId);
            });
        });

        $assignQuery->where(function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('issue_date', [$fromDate, $toDate])
              ->orWhere(function ($sub) use ($fromDate, $toDate) {
                  $sub->whereNull('issue_date')->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
              });
        });

        if ($unitId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($unitId) {
                $jq->where('service_provider_id', $unitId);
            });
        }

        if ($brandId) {
            $assignQuery->whereHas('task.jobCard', function ($jq) use ($brandId) {
                $jq->where('brand_id', $brandId);
            });
        }

        $assignments = $assignQuery->orderBy('id', 'asc')->get();

        $empCodes = $assignments->map(fn($a) => $a->employee->emp_id ?? null)->filter()->unique()->toArray();
        $attendances = Attendance::whereIn('emp_code', $empCodes)->whereBetween('date', [$fromDate, $toDate])->get()->groupBy('emp_code');

        $rows = [];
        $sNo = 1;
        $totalHours = 0;
        $totalPlan = 0;
        $totalIssue = 0;
        $totalFinish = 0;
        $totalPending = 0;

        foreach ($assignments as $a) {
            $emp = $a->employee;
            $empName = $emp ? $emp->name : 'N/A';
            $empCode = $emp ? $emp->emp_id : null;

            $hrs = (float) ($a->total_hrs ?? 0);
            if ($hrs == 0 && $empCode && isset($attendances[$empCode])) {
                $hrs = (float) ($attendances[$empCode]->first()->work_hours ?? 0);
            }
            if ($hrs == 0) {
                $hrs = 8.0;
            }

            $workName = $a->service ? $a->service->service_name : 'N/A';
            $cutNo = $a->task ? ($a->task->job_card_no ?? ($a->task->jobCard ? $a->task->jobCard->job_card_no : '-')) : '-';

            $planQty = 0;
            if ($a->task && $a->task->stage && $a->task->stage->planned_qty > 0) {
                $planQty = (float) $a->task->stage->planned_qty;
            } elseif ($a->task && $a->task->issue_qty > 0) {
                $planQty = (float) $a->task->issue_qty;
            } elseif ($a->task && $a->task->jobCard && $a->task->jobCard->grand_total_qty > 0) {
                $planQty = (float) $a->task->jobCard->grand_total_qty;
            } else {
                $planQty = (float) $a->issue_qty;
            }

            $issueQty = (float) $a->issue_qty;
            $finishQty = (float) $a->completed_qty;
            $pendingQty = max(0, $issueQty - $finishQty - (float) ($a->wastage_qty ?? 0));

            $totalHours += $hrs;
            $totalPlan += $planQty;
            $totalIssue += $issueQty;
            $totalFinish += $finishQty;
            $totalPending += $pendingQty;

            $rows[] = [
                's_no' => $sNo++,
                'name' => htmlspecialchars($empName),
                'working_hours' => number_format($hrs, 1),
                'work' => htmlspecialchars($workName),
                'cut_no' => '<strong>' . htmlspecialchars($cutNo) . '</strong>',
                'plan_qty' => number_format($planQty, 0),
                'issue_qty' => number_format($issueQty, 0),
                'completed_qty' => number_format($finishQty, 0),
                'pending_qty' => number_format($pendingQty, 0),
                'remarks' => htmlspecialchars($a->remarks ?: '-'),
                '_search_text' => strtolower($empName . ' ' . $workName . ' ' . $cutNo . ' ' . ($a->remarks ?: ''))
            ];
        }

        $recordsTotal = count($rows);
        if (!empty($search)) {
            $rows = array_values(array_filter($rows, function ($r) use ($search) {
                return strpos($r['_search_text'] ?? '', strtolower($search)) !== false;
            }));
            // Re-number s_no
            foreach ($rows as $idx => &$rowRef) {
                $rowRef['s_no'] = $idx + 1;
            }
        }
        $recordsFiltered = count($rows);

        if ($isExport) {
            $pageData = $rows;
        } else {
            $pageData = array_slice($rows, $start, $length);
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pageData,
            'meta' => [
                'stage_id' => $stageId,
                'stage_name' => $stageName,
                'total_employees' => count(array_unique(array_column($rows, 'name'))),
                'total_hours' => number_format($totalHours, 1),
                'total_plan' => number_format($totalPlan, 0),
                'total_issue' => number_format($totalIssue, 0),
                'total_finish' => number_format($totalFinish, 0),
                'total_pending' => number_format($totalPending, 0),
                'from_date' => date('d-m-Y', strtotime($fromDate)),
                'to_date' => date('d-m-Y', strtotime($toDate)),
            ]
        ]);
    }
}
