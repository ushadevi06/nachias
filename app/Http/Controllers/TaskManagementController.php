<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobCardEntry;
use App\Models\User;
use App\Models\ProcessSchedule;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\OperationStage;
use App\Models\Shift;
use App\Models\TaskAdjustment;
use App\Models\ProductionStageConsumable;
use App\Models\StoreType;
use App\Models\StockEntryItem;
use App\Models\RawMaterial;
use App\Models\TaskAssignEmployee;
use App\Models\TaskLog;
use App\Models\ProductionService;
use App\Models\JobCardIssueItem;
use App\Models\GrnEntry;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TaskManagementController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view task-management')) {
            return unauthorizedRedirect();
        }
        $allStatuses = TaskStatus::all();
        return view('task_management/view', compact('allStatuses'));
    }

    public function fetch(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view task-management')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tasks = Task::with(['jobCard', 'stage.operationStage', 'operationStage', 'assignments'])->get();
        $allStatuses = TaskStatus::all();

        $boards = [];
        foreach ($allStatuses as $status) {
            $boards[$status->name] = ['id' => $status->name, 'title' => $status->name, 'item' => []];
        }

        foreach ($tasks as $t) {
            $statusName = $t->status ?: 'Planned';

            $stage = $t->stage;
            if (!$stage && $t->job_card_entry_id) {
                $osId = $t->operation_stage_id ?? $t->stage_id;
                $stage = ProcessSchedule::where('job_card_entry_id', $t->job_card_entry_id)->where('operation_stage_id', $osId)->first();
            }

            $jcStart = $stage && $stage->start_date ? Carbon::parse($stage->start_date)->format('d-m-Y') : ($t->jobCard && $t->jobCard->job_card_date ? Carbon::parse($t->jobCard->job_card_date)->format('d-m-Y') : 'N/A');
            $jcEnd = $stage && $stage->due_date ? Carbon::parse($stage->due_date)->format('d-m-Y') : ($t->jobCard && $t->jobCard->delivery_date ? Carbon::parse($t->jobCard->delivery_date)->format('d-m-Y') : 'N/A');

            $stageName = 'No Stage';
            if ($stage) {
                $stageName = $stage->operationStage ? $stage->operationStage->operation_stage_name : ($stage->stage ?: 'No Stage');
            } elseif ($t->operationStage) {
                $stageName = $t->operationStage->operation_stage_name ?: 'No Stage';
            }

            $targetQty = (float) ($t->jobCard->grand_total_qty ?? 0);
            if ($targetQty == 0 && $stage) {
                $targetQty = (float) ($stage->planned_qty ?? 0);
            }
            if ($targetQty == 0) {
                $targetQty = (float) ($t->issue_qty ?? 0);
            }

            $totalReceived = 0;
            foreach ($t->assignments as $assign) {
                $totalReceived += (float) $assign->completed_qty + (float) $assign->wastage_qty;
            }

            if (isset($boards[$statusName])) {
                $boards[$statusName]['item'][] = [
                    'id' => $t->id,
                    'eid' => $t->id,
                    'task_no' => $t->task_no,
                    'title' => ($t->job_card_no ?? 'No JC') . ' - ' . (int) $targetQty . ' PCS',
                    'stage_name' => $stageName,
                    'badge-text' => $statusName,
                    'start-date' => $t->issue_date ? Carbon::parse($t->issue_date)->format('d-m-Y') : 'N/A',
                    'due-date' => $t->due_date ? Carbon::parse($t->due_date)->format('d-m-Y') : 'N/A',
                    'jc-start' => $jcStart,
                    'jc-end' => $jcEnd,
                    'working_level' => (float) max(0, $totalReceived) . ' / ' . (float) $targetQty . ' PCS',
                    'total_received' => (float) max(0, $totalReceived),
                    'target_qty' => (float) $targetQty
                ];
            }
        }
        return response()->json(array_values($boards));
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit task-management') && !auth()->user()->can('assign-task job-card')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create task-management') && !auth()->user()->can('assign-task job-card')) {
                return unauthorizedRedirect();
            }
        }

        $task = null;
        $jobCard = null;
        $stages = collect([]);

        if ($id) {
            $task = Task::with([
                'jobCard',
                'stage.operationStage',
                'assignee',
                'assignments.assignee',
                'assignments.service'
            ])->findOrFail($id);

            if (!$jobCard && $task->job_card_entry_id) {
                $jobCard = JobCardEntry::find($task->job_card_entry_id);
            }

            if ($jobCard) {
                $stages = ProcessSchedule::with([
                    'operationStage',
                    'serviceProvider'
                ])->where('job_card_entry_id', $jobCard->id)->get();
            }

            if ($task->stage_id && (!$stages->where('id', $task->stage_id)->count() || !$task->stage)) {
                $currentStage = ProcessSchedule::with(['operationStage', 'serviceProvider'])->find($task->stage_id);
                if ($currentStage) {
                    $task->setRelation('stage', $currentStage);
                    if (!$stages->where('id', $task->stage_id)->count()) {
                        $stages->push($currentStage);
                    }
                }
            }
        } else {
            if (request()->has('job_card_id')) {
                $jobCard = JobCardEntry::find(request()->job_card_id);
                if ($jobCard) {
                    $jobCardId = $jobCard->id;
                    $stages = ProcessSchedule::with(['operationStage', 'serviceProvider'])->where('job_card_entry_id', $jobCardId)->get();
                }
            }
        }

        if ($stages->isNotEmpty() && request()->has('stage_id')) {
            $sId = request()->stage_id;
            $psStage = $stages->where('id', $sId)->first();
            
            if (!$psStage) {
                $psStage = $stages->where('operation_stage_id', $sId)->first();
                if ($psStage) {
                    request()->merge(['stage_id' => $psStage->id]);
                    $sId = $psStage->id;
                }
            }

            if (!$id && $psStage) {
                $jobCardId = $psStage->job_card_entry_id;
                
                $taskOpStageIds = Task::with('stage')
                    ->where('job_card_entry_id', $jobCardId)
                    ->get()
                    ->map(function ($task) {
                        return ($task->stage && $task->stage->operation_stage_id) ? $task->stage->operation_stage_id : $task->stage_id;
                    })
                    ->filter()
                    ->toArray();
                
                $canAssign = true;
                foreach ($stages as $stage) {
                    if ($stage->id == $psStage->id) {
                        break;
                    }
                    if (!in_array($stage->operation_stage_id, $taskOpStageIds)) {
                        $canAssign = false;
                        break;
                    }
                }
                
                if (!$canAssign) {
                    return redirect()->back()->with('danger', 'You cannot assign a task for a future stage before previous stages are assigned.');
                }
            }

            if ($psStage) {
                if (!request()->has('issue_date') && $psStage->start_date) {
                    request()->merge(['issue_date' => \Carbon\Carbon::parse($psStage->start_date)->format('d-m-Y')]);
                }
                if (!request()->has('due_date') && $psStage->due_date) {
                    request()->merge(['due_date' => \Carbon\Carbon::parse($psStage->due_date)->format('d-m-Y')]);
                }
                if (!request()->has('remarks') && $psStage->remarks) {
                    request()->merge(['remarks' => $psStage->remarks]);
                }
            }
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'assignments' => 'required|array|min:1',
                'assignments.*.service_id' => 'required',
                'assignments.*.issued_to' => 'required',
                'assignments.*.issue_date' => 'required',
                'assignments.*.issue_qty' => 'required|numeric|min:1',
                'issued_by' => 'required',
                'status' => 'required'
            ];
            
            $messages = [
                'assignments.*.service_id.required' => 'This field is required.',
                'assignments.*.issued_to.required' => 'This field is required.',
                'assignments.*.issue_date.required' => 'This field is required.',
                'assignments.*.issue_qty.required' => 'This field is required.',
                'assignments.*.issue_qty.min' => 'Issue Qty must be at least 1',
                'issued_by.required' => 'This field is required.',
            ];

            $request->validate($rules, $messages);

            $assignments = $request->input('assignments');
            if (!$assignments) {
                $assignments = [
                    [
                        'issued_to' => $request->issued_to,
                        'service_ids' => $request->service_ids,
                        'issue_date' => $request->issue_date,
                        'due_date' => $request->due_date,
                        'total_hrs' => $request->total_hrs,
                        'issue_qty' => $request->issue_qty,
                    ]
                ];
            }

            if ($request->status == 'Completed') {
                $totalIssue = 0;
                $totalCompleted = 0;
                $totalWastage = 0;

                if ($id) {
                    $task = Task::with('assignments')->find($id);
                    $existingAssignments = $task ? $task->assignments->keyBy('id') : collect();
                } else {
                    $existingAssignments = collect();
                }

                foreach ($assignments as $index => $assign) {
                    if (empty($assign['issued_to'])) continue;
                    
                    $issueQty = (float)($assign['issue_qty'] ?? 0);
                    $totalIssue += $issueQty;
                    
                    if ($id && isset($assign['id']) && $existingAssignments->has($assign['id'])) {
                        $existing = $existingAssignments->get($assign['id']);
                        $totalCompleted += (float) $existing->completed_qty;
                        $totalWastage += (float) $existing->wastage_qty;
                    }
                }
                
                if ($totalIssue <= 0 || round($totalCompleted + $totalWastage, 2) < round($totalIssue, 2)) {
                    return back()->withInput()->withErrors(['status' => 'This task cannot be marked as Completed because it is not fully finished. Please complete the remaining quantity before changing the status to Completed.'])->with('active_tab', 'issue');
                }
            }

            // Validate service quantity sums against stage planned quantity
            $stageId = $request->input('stage_id');
            $stage = ProcessSchedule::find($stageId);
            $stageMaxQty = $stage ? (float)$stage->planned_qty : 0;

            $jobCard = JobCardEntry::find($request->input('job_card_entry_id'));

            if ($stageMaxQty > 0) {
                $serviceQtySums = [];
                foreach ($assignments as $assign) {
                    if (empty($assign['issued_to'])) {
                        continue;
                    }
                    $serviceId = $assign['service_id'] ?? $assign['services'] ?? null;
                    if (is_array($serviceId)) {
                        $serviceId = $serviceId[0] ?? null;
                    }
                    if ($serviceId) {
                        $qty = (float)($assign['issue_qty'] ?? 0);
                        if (!isset($serviceQtySums[$serviceId])) {
                            $serviceQtySums[$serviceId] = 0;
                        }
                        $serviceQtySums[$serviceId] += $qty;
                    }
                }

                foreach ($serviceQtySums as $serviceId => $totalQty) {
                    $service = ProductionService::find($serviceId);
                    $serviceMaxQty = $stageMaxQty;
                    
                    if ($service && $jobCard) {
                        if ($service->base_quantity_source == 'FS Qty') {
                            $serviceMaxQty = $jobCard->total_qty_fs ?? $serviceMaxQty;
                        } elseif ($service->base_quantity_source == 'HS Qty') {
                            $serviceMaxQty = $jobCard->total_qty_hs ?? $serviceMaxQty;
                        } else {
                            $serviceMaxQty = $jobCard->grand_total_qty ?? $serviceMaxQty;
                        }
                    }

                    if ($totalQty > $serviceMaxQty) {
                        $serviceName = $service ? $service->service_name : 'Selected Service';
                        throw new \Exception("Total quantity for service '$serviceName' ($totalQty) exceeds the allowed quantity ($serviceMaxQty PCS).");
                    }
                }
            }

            if ($jobCard && $jobCard->process_group_id) {
                foreach ($assignments as $assign) {
                    if (empty($assign['issued_to'])) {
                        continue;
                    }
                    $serviceId = $assign['service_id'] ?? $assign['services'] ?? null;
                    if (is_array($serviceId)) {
                        $serviceId = $serviceId[0] ?? null;
                    }
                    if ($serviceId) {
                        $service = ProductionService::with('processGroups')->find($serviceId);
                        if ($service && !$service->processGroups->contains($jobCard->process_group_id)) {
                            $serviceName = $service->service_name;
                            throw new \Exception("The service '$serviceName' does not belong to the Job Card's Process Group.");
                        }
                    }
                }
            }

            $commonData = $request->only(['job_card_entry_id', 'job_card_no', 'stage_id', 'issued_by', 'remarks', 'status']);

            DB::beginTransaction();
            try {
                $taskData = $commonData;
                $taskData['updated_by'] = auth()->id();

                if ($id) {
                    $task = Task::findOrFail($id);
                    $updates = [];
                    if ($task->remarks != $taskData['remarks'])
                        $updates[] = "Remarks changed";
                    if ($task->stage_id != $taskData['stage_id'])
                        $updates[] = "Stage changed";
                    if ($task->status != $taskData['status'])
                        $updates[] = "Status changed to " . $taskData['status'];
                    $task->update($taskData);
                    if (!empty($updates)) {
                        $this->logActivity($task->id, 'Updated', 'Task updated: ' . implode(', ', $updates));
                    }
                } else {
                    $taskCount = Task::count() + 1;
                    $taskData['task_no'] = 'TASK-' . str_pad($taskCount, 3, '0', STR_PAD_LEFT);
                    $taskData['created_by'] = auth()->id();
                    $taskData['issued_to'] = $assignments[0]['issued_to'] ?? null;
                    if (!empty($assignments[0]['issue_date'])) {
                        $taskData['issue_date'] = $this->formatDate($assignments[0]['issue_date']);
                    }
                    if (!empty($assignments[0]['due_date'])) {
                        $taskData['due_date'] = $this->formatDate($assignments[0]['due_date']);
                    }
                    $task = Task::create($taskData);
                    $this->logActivity($task->id, 'Created', 'Task created with ticket number ' . $task->task_no);
                }

                if ($id) {
                    $oldAssigneeIds = array_unique($task->assignments->pluck('issued_to')->toArray());
                    $newAssigneeIds = array_unique(array_filter(array_column($assignments, 'issued_to')));

                    $addedIds = array_diff($newAssigneeIds, $oldAssigneeIds);
                    $removedIds = array_diff($oldAssigneeIds, $newAssigneeIds);

                    $task->assignments()->delete();

                    if (!empty($addedIds)) {
                        $addedNames = User::whereIn('id', $addedIds)->pluck('name')->toArray();
                        if (!empty($addedNames)) {
                            $this->logActivity($task->id, 'Assignment', 'Employee(s) assigned: ' . implode(', ', $addedNames));
                        }
                    }
                    if (!empty($removedIds)) {
                        $removedNames = User::whereIn('id', $removedIds)->pluck('name')->toArray();
                        if (!empty($removedNames)) {
                            $this->logActivity($task->id, 'Assignment', 'Employee(s) removed: ' . implode(', ', $removedNames));
                        }
                    }
                }

                $allServiceIds = [];
                $totalIssueQty = 0;
                
                $existingAssignmentsMap = ($id && $task) ? $task->assignments->keyBy('id') : collect();
                foreach ($assignments as $assign) {
                    $isAssigned = !empty($assign['issued_to']);
                    $status = $isAssigned ? ($assign['status'] ?? 'Open') : 'Pending Assignment';

                    $assignData = [
                        'task_id' => $task->id,
                        'issued_to' => $isAssigned ? $assign['issued_to'] : null,
                        'issue_qty' => $assign['issue_qty'] ?? 0,
                        'total_hrs' => $assign['total_hrs'] ?? 0,
                        'status' => $status,
                        'remarks' => $assign['remarks'] ?? null,
                        'created_by' => auth()->id(),
                    ];
                    
                    if (isset($assign['id']) && $existingAssignmentsMap->has($assign['id'])) {
                        $existing = $existingAssignmentsMap->get($assign['id']);
                        $assignData['completed_qty'] = $existing->completed_qty ?? 0;
                        $assignData['inprogress_qty'] = $existing->inprogress_qty ?? 0;
                        $assignData['wastage_qty'] = $existing->wastage_qty ?? 0;
                        $assignData['qc_checked_qty'] = $existing->qc_checked_qty ?? 0;
                        $assignData['qc_passed_qty'] = $existing->qc_passed_qty ?? 0;
                        $assignData['qc_rejected_qty'] = $existing->qc_rejected_qty ?? 0;
                        $assignData['qc_status'] = $existing->qc_status ?? 'Pending';
                        if (!empty($existing->status)) {
                        $assignData['status'] = $existing->status;
                        }
                    }

                    $totalIssueQty += (float) ($assign['issue_qty'] ?? 0);

                    $assignData['issue_date'] = $this->formatDate($assign['issue_date'] ?? null);
                    $assignData['due_date'] = $this->formatDate($assign['due_date'] ?? null);

                    $serviceId = $assign['service_id'] ?? $assign['services'] ?? null;
                    if (is_array($serviceId)) {
                        $serviceId = $serviceId[0] ?? null;
                    }
                    $assignData['service_id'] = $serviceId;

                    $unitRate = 0;
                    if ($serviceId) {
                        $allServiceIds[] = $serviceId;
                        $ps = \App\Models\ProductionService::find($serviceId);
                        if ($ps) {
                            $unitRate = $ps->cost ?? 0;
                        }
                    }
                    $assignData['unit_rate'] = $unitRate;
                    $assignData['total_cost'] = (float)($assign['issue_qty'] ?? 0) * $unitRate;

                    TaskAssignEmployee::create($assignData);
                }

                $jc = JobCardEntry::find($task->job_card_entry_id);
                $task->update([
                    'services' => array_values(array_unique($allServiceIds)),
                    'issue_qty' => $jc->grand_total_qty ?? $totalIssueQty
                ]);

                DB::commit();
                return redirect('task_management')->with('success', 'Task(s) saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()])->with('active_tab', 'issue');
            }
        }

        if ($stages->isEmpty() && (request()->has('job_card_id') || (isset($jobCard) && $jobCard))) {
            $jcId = request('job_card_id') ?: ($jobCard ? $jobCard->id : null);
            if ($jcId) {
                $stages = ProcessSchedule::with(['operationStage', 'serviceProvider'])->where('job_card_entry_id', $jcId)->get();
            }
        }


        $nextTaskNo = $id ? $task->task_no : 'TASK-' . str_pad(Task::count() + 1, 3, '0', STR_PAD_LEFT);
        $users = User::where('id', '!=', 1)->where('status', 'Active')->get();
        $supervisors = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['Production Supervisor', 'Supervisor', 'Unit Supervisor', 'Cutting Supervisor'])
            ->where('users.status', 'Active')
            ->select('users.*')
            ->get();
        $allStatuses = TaskStatus::pluck('name')->toArray();
        if (empty($allStatuses)) {
            $allStatuses = ['Planned', 'In Progress', 'Completed', 'Hold'];
        }

        $shifts = Shift::active()->get();

        $nextAdjNo = 'ADJ-' . date('Y') . '-' . str_pad(TaskAdjustment::count() + 1, 3, '0', STR_PAD_LEFT);

        $relatedTasks = Task::where('job_card_entry_id', ($jobCard->id ?? 0))->get();

        $taskAdjustment = null;
        $taskAdjustments = collect([]);
        if ($task) {
            $taskAdjustments = TaskAdjustment::with(['items.rawMaterial', 'items.uom', 'items.service'])->where('task_id', $task->id)->latest()->get();

            $taskAdjustment = $taskAdjustments->first();
            if ($taskAdjustment) {
                $nextAdjNo = $taskAdjustment->adjustment_no;
            }
        }


        $services = [];
        $finalStageId = request('stage_id') ?: ($task ? $task->stage_id : old('stage_id'));

        $selectedSchedule = null;
        if ($task && $task->stage && $task->stage_id == $finalStageId) {
            $selectedSchedule = $task->stage;
        } elseif ($finalStageId) {
            $selectedSchedule = ProcessSchedule::with(['operationStage'])->find($finalStageId);
        }

        if ($selectedSchedule) {
            $query = ProductionService::where('operation_stage_id', $selectedSchedule->operation_stage_id)->where('status', 'Active');
            if ($jobCard && $jobCard->process_group_id) {
                $query->whereHas('processGroups', function($q) use ($jobCard) {
                    $q->where('process_groups.id', $jobCard->process_group_id);
                });
            }
            $services = $query->orderBy('sequence', 'asc')->get()->map(function ($s) use ($selectedSchedule, $jobCard) {
                $qty = $selectedSchedule->planned_qty ?? 0;
                if ($jobCard) {
                    if ($s->base_quantity_source == 'FS Qty') {
                        $qty = $jobCard->total_qty_fs ?? $qty;
                    } elseif ($s->base_quantity_source == 'HS Qty') {
                        $qty = $jobCard->total_qty_hs ?? $qty;
                    } else {
                        $qty = $jobCard->grand_total_qty ?? $qty;
                    }
                }
                return [
                    'id' => $s->id,
                    'name' => ($s->service_name ?? ''),
                    'qty' => $qty,
                    'multiplier' => 1
                ];
            })->values()->all();
        }


        $jobCardGrnNo = '';
        if ($jobCard) {
            $jobCardGrnNo = StockEntryItem::whereIn('id', function ($q) use ($jobCard) {
                $q->select('stock_entry_item_id')
                    ->from('job_card_issue_items')
                    ->where('job_card_entry_id', $jobCard->id)
                    ->whereNotNull('stock_entry_item_id');
            })->with('grnEntryItem.grnEntry')->get()
                ->pluck('grnEntryItem.grnEntry.grn_number')
                ->unique()
                ->filter()
                ->first() ?? '';
        }

        return view('task_management/add', compact('task', 'jobCard', 'stages', 'users', 'supervisors', 'nextTaskNo', 'allStatuses', 'nextAdjNo', 'relatedTasks', 'taskAdjustment', 'shifts', 'taskAdjustments', 'services', 'jobCardGrnNo'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details task-management')) {
            return unauthorizedRedirect();
        }
        $task = Task::with([
            'stage.operationStage',
            'operationStage',
            'assignments.assignee',
            'assignments.service',
            'adjustments.items.rawMaterial',
            'adjustments.items.uom'
        ])->findOrFail($id);
        return view('task_management/view_details', compact('task'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $task = Task::with('assignments')->findOrFail($request->task_id);
            
            if ($request->status === 'Completed') {
                if ($task->assignments->isEmpty()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'This task cannot be marked as Completed because it has no assignments.'
                    ], 200);
                }

                $totalIssue = 0;
                $totalCompleted = 0;
                $totalWastage = 0;

                foreach ($task->assignments as $assignment) {
                    $totalIssue += (float) $assignment->issue_qty;
                    $totalCompleted += (float) $assignment->completed_qty;
                    $totalWastage += (float) $assignment->wastage_qty;
                }

                if ($totalIssue <= 0 || round($totalCompleted + $totalWastage, 2) < round($totalIssue, 2)) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'This task cannot be marked as Completed because it is not fully finished. Please complete the remaining quantity before changing the status to Completed.'
                    ], 200);
                }
            }

            if ($request->status === 'In Progress') {
                $hasProgress = $task->assignments->contains(function($as) {
                    return (float)$as->completed_qty > 0 || (float)$as->wastage_qty > 0 || $as->status === 'In Progress';
                });
                
                if (!$hasProgress) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Cannot move to In Progress. No progress has been recorded on any assignment yet.'
                    ], 200);
                }
            }

            $task->status = $request->status;
            TaskStatus::firstOrCreate(['name' => $request->status], ['color' => 'info', 'progress_percent' => 10]);
            $task->save();
            $this->logActivity($task->id, 'Status Change', 'Status changed to ' . $request->status);

            $this->syncProductionMovements($task);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function update_task_progress(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'assignments' => 'required|array',
            'assignments.*.id' => 'required|exists:task_assign_employees,id',
            'assignments.*.completed_qty' => 'nullable|numeric|min:0',
            'assignments.*.inprogress_qty' => 'nullable|numeric|min:0',
            'assignments.*.wastage_qty' => 'nullable|numeric|min:0',
            'assignments.*.qc_checked_qty' => 'nullable|numeric|min:0',
            'assignments.*.qc_passed_qty' => 'nullable|numeric|min:0',
            'assignments.*.qc_rejected_qty' => 'nullable|numeric|min:0',
            'assignments.*.status' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $task = Task::findOrFail($request->task_id);
            $totalCompleted = 0;
            $totalAssigned = 0;
            $allCompleted = true;
            $anyStarted = false;

            foreach ($request->assignments as $assignData) {
                $assignment = TaskAssignEmployee::findOrFail($assignData['id']);

                $completed = (float) ($assignData['completed_qty'] ?? 0);
                $wastage = (float) ($assignData['wastage_qty'] ?? 0);
                $assignedQty = (float) $assignment->issue_qty;

                $qc_checked = (float) ($assignData['qc_checked_qty'] ?? 0);
                $qc_passed = (float) ($assignData['qc_passed_qty'] ?? 0);
                $qc_rejected = (float) ($assignData['qc_rejected_qty'] ?? 0);

                if (($completed + $wastage) > $assignedQty) {
                    throw new \Exception("Completed ($completed) and Wastage ($wastage) quantity cannot exceed Assigned quantity ($assignedQty) for employee " . ($assignment->assignee->name ?? 'Unknown'));
                }
                if ($qc_checked > $completed) {
                    throw new \Exception("QC Checked quantity ($qc_checked) cannot exceed Completed quantity ($completed) for employee " . ($assignment->assignee->name ?? 'Unknown'));
                }

                if (round($qc_passed + $qc_rejected, 2) != round($qc_checked, 2)) {
                    throw new \Exception("QC quantities are invalid. Passed ($qc_passed) + Rejected ($qc_rejected) must equal Checked ($qc_checked) for employee " . ($assignment->assignee->name ?? 'Unknown'));
                }

                $inprogress = max(0, $assignedQty - ($completed + $wastage));

                if ($completed == 0 && $wastage == 0) {
                    $status = 'Open';
                } elseif (($completed + $wastage) < $assignedQty) {
                    $status = 'In Progress';
                } else {
                    $status = 'Completed';
                }

                $qc_status = 'Pending';
                if ($qc_checked == 0) {
                    $qc_status = 'Pending';
                } elseif ($qc_checked < $completed) {
                    $qc_status = 'In QC';
                } elseif ($qc_checked == $completed) {
                    $qc_status = 'QC Completed';
                }

                $originalCompleted = $assignment->completed_qty;
                $originalWastage = $assignment->wastage_qty;
                $originalQcChecked = $assignment->qc_checked_qty;

                $totalCost = $completed * (float)($assignment->unit_rate ?? 0);

                $assignment->update([
                    'completed_qty' => $completed,
                    'inprogress_qty' => $inprogress,
                    'wastage_qty' => $wastage,
                    'qc_checked_qty' => $qc_checked,
                    'qc_passed_qty' => $qc_passed,
                    'qc_rejected_qty' => $qc_rejected,
                    'qc_status' => $qc_status,
                    'status' => $status,
                    'total_cost' => $totalCost
                ]);

                $totalCompleted += $completed;
                $totalAssigned += $assignedQty;

                if ($status != 'Completed') {
                    $allCompleted = false;
                }
                if ($status == 'In Progress' || $completed > 0) {
                    $anyStarted = true;
                }

                $changes = [];
                if ((float) $completed != (float) $originalCompleted)
                    $changes[] = "Completed: " . (float) $originalCompleted . " -> " . (float) $completed;
                if ((float) $wastage != (float) $originalWastage)
                    $changes[] = "Wastage: " . (float) $originalWastage . " -> " . (float) $wastage;
                if ((float) $qc_checked != (float) $originalQcChecked)
                    $changes[] = "QC Checked: " . (float) $originalQcChecked . " -> " . (float) $qc_checked;

                if (!empty($changes)) {
                    $empName = $assignment->assignee->name ?? 'Unknown Employee';
                    $this->logActivity($task->id, 'Progress Update', "Updated progress for **$empName**: " . implode(', ', $changes));
                }
            }

            $newStatus = $task->status;
            if ($allCompleted || ($totalAssigned > 0 && $totalCompleted >= $totalAssigned)) {
                $newStatus = 'Completed';
            } elseif ($anyStarted || $totalCompleted > 0) {
                $newStatus = 'In Progress';
            }

            if ($newStatus != $task->status) {
                $task->status = $newStatus;
                TaskStatus::firstOrCreate(['name' => $newStatus], ['color' => 'secondary']);
                $task->save();
                if ($task->stage_id) {
                    $schedule = ProcessSchedule::find($task->stage_id);
                    if ($schedule) {
                        $schedule->update(['status' => $newStatus]);

                        if ($newStatus == 'Completed') {
                            $nextSchedule = ProcessSchedule::where('production_id', $schedule->production_id)
                                ->where('id', '>', $schedule->id)
                                ->orderBy('id', 'asc')
                                ->first();

                            if ($nextSchedule && $nextSchedule->status == 'Planned') {
                                $nextSchedule->update(['status' => 'Pending']);
                            }
                        }
                    }
                }
                $this->logActivity($task->id, 'Status Change', "Task status automatically updated to $newStatus");
            }

            $this->syncProductionMovements($task);

            DB::commit();
            return redirect('task_management')->with('success', 'Task progress updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete task-management')) {
            return unauthorizedRedirect();
        }
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect('task_management')->with('success', 'Task deleted successfully');
    }


    public function getTaskDetails($id)
    {
        $task = Task::with(['assignee', 'stage.operationStage', 'stage.serviceProvider'])->find($id);
        if ($task) {
            $stageName = $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? 'N/A');
            $serviceData = [];
            if ($task->services && is_array($task->services)) {
                $serviceData = ProductionService::whereIn('id', $task->services)
                    ->get(['id', 'service_name', 'service_code'])
                    ->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->service_name
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'issued_to' => $task->assignee->name ?? 'N/A',
                'issued_to_id' => $task->issued_to,
                'issue_qty' => $task->issue_qty,
                'issue_date' => $task->issue_date,
                'stage_name' => $stageName,
                'plant' => $task->stage->serviceProvider->name ?? 'N/A',
                'services' => $serviceData
            ]);
        }
        return response()->json(['success' => false]);
    }

    public function getStageConsumables(Request $request, $id)
    {
        $schedule = ProcessSchedule::with(['operationStage', 'jobCard.fabricDetails', 'jobCard.item'])->find($id);

        $jobCard = null;
        $stageName = '';

        if ($schedule) {
            $stageName = $schedule->operationStage->operation_stage_name ?? ($schedule->stage ?? '');
            $jobCard = $schedule->jobCard ?? null;
        }

        if (!$jobCard && $request->has('job_card_id')) {
            $jobCard = JobCardEntry::with(['fabricDetails', 'item'])->find($request->job_card_id);
        }

        if (!$jobCard)
            return response()->json(['success' => false, 'message' => 'Job Card not found']);

        $consumableConfigs = [];
        if ($stageName) {
            $consumableConfigs = ProductionStageConsumable::where('stage', $stageName)->where('status', 'Active')->pluck('raw_material_id')->toArray();
        }

        $rmIdsFromArt = [];
        $rmIdsFromIssues = [];
        $rmIdsFromItemMaster = [];

        if ($jobCard) {
            $artNumbers = $jobCard->fabricDetails->pluck('art_no')->filter()->map(function ($val) {
                return trim($val);
            })->toArray();

            $rmIdsDirect = RawMaterial::where(function ($q) use ($artNumbers) {
                $q->whereIn('code', $artNumbers)->orWhereIn('name', $artNumbers);
            })->pluck('id')->toArray();

            $rmIdsFromGrn = StockEntryItem::whereIn('grn_entry_item_id', function ($query) use ($artNumbers) {
                $query->select('id')->from('grn_entry_items')->whereIn('art_no', $artNumbers);
            })->pluck('raw_material_id')->toArray();

            $rmIdsFromIssues = JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->whereNotNull('stock_entry_item_id')->pluck('raw_material_id')->toArray();

            if ($jobCard->item && !empty($jobCard->item->related_materials)) {
                $rmIdsFromItemMaster = (array) $jobCard->item->related_materials;
            }

            $rmIdsFromArt = array_unique(array_merge($rmIdsDirect, $rmIdsFromGrn));
        }

        $allRelatedRmIds = array_unique(array_merge($rmIdsFromArt, $rmIdsFromIssues, $consumableConfigs, $rmIdsFromItemMaster));
        $materials = collect([]);
        if (!empty($allRelatedRmIds)) {
            $materials = RawMaterial::whereIn('id', $allRelatedRmIds)->where('status', 'Active')->get();
        }

        $grnIdsForJobCard = [];
        if ($jobCard && $jobCard->purchase_order_id) {
            $grnIdsForJobCard = GrnEntry::whereIn('purchase_invoice_id', function ($q) use ($jobCard) {
                $q->select('id')->from('purchase_invoices')->where('purchase_order_id', $jobCard->purchase_order_id);
            })->pluck('id')->toArray();
        }

        $formatted = $materials->map(function ($m) use ($jobCard, $grnIdsForJobCard) {
            $grnItem = null;

            $grnItem = StockEntryItem::where('raw_material_id', $m->id)
                ->whereIn('id', function ($q) use ($jobCard) {
                    $q->select('stock_entry_item_id')
                        ->from('job_card_issue_items')
                        ->where('job_card_entry_id', $jobCard->id);
                })->with('grnEntryItem.grnEntry')->first();

            if (!$grnItem && !empty($grnIdsForJobCard)) {
                $grnItem = StockEntryItem::where('raw_material_id', $m->id)
                    ->whereHas('grnEntryItem', function ($q) use ($grnIdsForJobCard) {
                        $q->whereIn('grn_entry_id', $grnIdsForJobCard);
                    })->with('grnEntryItem.grnEntry')->first();
            }

            if (!$grnItem) {
                $grnItem = StockEntryItem::where('raw_material_id', $m->id)
                    ->with('grnEntryItem.grnEntry')
                    ->latest()
                    ->first();
            }

            $grnNo = $grnItem?->grnEntryItem?->grnEntry?->grn_number ?? '';
            $artNo = $grnItem?->art_no ?? ($grnItem?->grnEntryItem?->art_no ?? '');

            if (empty($artNo) && $jobCard) {
                $fabric = $jobCard->fabricDetails->first(function ($f) use ($m) {
                    return trim($f->art_no) == trim($m->code) || trim($f->art_no) == trim($m->name);
                });
                if ($fabric)
                    $artNo = $fabric->art_no;
            }

            return [
                'id' => $m->id,
                'text' => $m->name . ($m->code ? " ({$m->code})" : ""),
                'art_no' => $artNo,
                'grn_no' => $grnNo
            ];
        });

        return response()->json([
            'success' => true,
            'materials' => $formatted
        ]);
    }

    public function adjustment_add(Request $request, $id = null)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'approved_by' => 'required',
            'overall_reason' => 'required',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:production_services,id',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.adjustment_type' => 'required',
            'items.*.qty' => 'required|numeric|min:0.01',
        ], [
            'required' => 'This field is required.',
            'items.*.qty.min' => 'Quantity must be greater than zero.',
            'items.required' => 'At least one material adjustment is required.'
        ]);

        DB::beginTransaction();
        try {
            $task = Task::findOrFail($request->task_id);
            $nextAdjNo = $request->adjustment_no;
            if (!$id && !$nextAdjNo) {
                $count = TaskAdjustment::count();
                $nextAdjNo = 'ADJ-' . date('Y') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }

            $headerData = [
                'adjustment_no' => $nextAdjNo,
                'task_id' => $task->id,
                'job_card_id' => $request->job_card_id,
                'affected_stage' => $request->affected_stage,
                'approved_by' => $request->approved_by,
                'overall_reason' => $request->overall_reason,
                'status' => 'Posted'
            ];

            if (!$id) {
                $headerData['created_by'] = auth()->id();
                $adjustment = TaskAdjustment::create($headerData);
                $message = 'Adjustment posted successfully';
            } else {
                $adjustment = TaskAdjustment::findOrFail($id);
                $headerData['updated_by'] = auth()->id();
                $adjustment->update($headerData);
                $adjustment->items()->delete();
                $message = 'Adjustment updated successfully';
            }

            $decreaseTypes = ['Loss', 'Rework', 'Damage'];
            $stageName = $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? 'N/A');

            $adjustedItems = [];
            foreach ($request->items as $itemData) {
                $currentStock = StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])->sum(DB::raw('qty_in - qty_out'));

                $diff = (float) $itemData['qty'];
                $newStock = in_array($itemData['adjustment_type'], $decreaseTypes) ? ($currentStock - $diff) : ($currentStock + $diff);

                $artNo = $itemData['art_no'] ?? null;
                $grnNo = $itemData['grn_no'] ?? null;

                if ((empty($artNo) || empty($grnNo))) {
                    $stockItemQuery = StockEntryItem::where('raw_material_id', $itemData['raw_material_id']);

                    if ($request->job_card_id) {
                        $stockItemQuery->whereIn('id', function ($q) use ($request) {
                            $q->select('stock_entry_item_id')
                                ->from('job_card_issue_items')
                                ->where('job_card_entry_id', $request->job_card_id);
                        });
                    }

                    $stockItem = $stockItemQuery->with('grnEntryItem.grnEntry')->first();

                    if (!$stockItem && $request->job_card_id) {
                        $jobCard = JobCardEntry::find($request->job_card_id);
                        if ($jobCard && $jobCard->purchase_order_id) {
                            $grnIds = GrnEntry::whereIn('purchase_invoice_id', function ($q) use ($jobCard) {
                                $q->select('id')->from('purchase_invoices')->where('purchase_order_id', $jobCard->purchase_order_id);
                            })->pluck('id')->toArray();

                            if (!empty($grnIds)) {
                                $stockItem = StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])
                                    ->whereHas('grnEntryItem', function ($q) use ($grnIds) {
                                        $q->whereIn('grn_entry_id', $grnIds);
                                    })->with('grnEntryItem.grnEntry')->first();
                            }
                        }
                    }

                    if (!$stockItem) {
                        $stockItem = StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])->with('grnEntryItem.grnEntry')->latest()->first();
                    }

                    if ($stockItem) {
                        $artNo = $artNo ?: ($stockItem->art_no ?? $stockItem->grnEntryItem?->art_no);
                        $grnNo = $grnNo ?: $stockItem->grnEntryItem?->grnEntry?->grn_number;
                    }
                }

                $item = $adjustment->items()->create([
                    'service_id' => !empty($itemData['service_id']) ? $itemData['service_id'] : null,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'grn_no' => $grnNo,
                    'art_no' => $artNo,
                    'adjustment_type' => $itemData['adjustment_type'],
                    'qty' => $diff,
                    'remarks' => $itemData['remarks'] ?? '',
                    'previous_stock' => $currentStock,
                    'new_stock' => $newStock
                ]);

                $materialName = RawMaterial::find($itemData['raw_material_id'])->name ?? 'Unknown Material';
                $adjustedItems[] = "$materialName (" . $itemData['adjustment_type'] . ": " . (float) $diff . ")";
            }

            $this->logActivity($task->id, 'Adjustment', "Adjustment **$nextAdjNo** posted for reason: " . $request->overall_reason . ". Items: " . implode(', ', $adjustedItems));

            $issueQty = (float) ($task->issue_qty ?? 0);
            $serviceCount = is_array($task->services) ? count($task->services) : 1;
            $targetQty = $issueQty * $serviceCount;

            $totalReceived = 0;

            $allAdjustmentItems = \App\Models\TaskAdjustmentItem::whereHas('adjustment', function ($q) use ($task) {
                $q->where('task_id', $task->id);
            })->get();

            foreach ($allAdjustmentItems as $adjItem) {
                if ($adjItem->adjustment_type == 'Loss' || $adjItem->adjustment_type == 'Excess') {
                    $totalReceived += (float) $adjItem->qty;
                } elseif ($adjItem->adjustment_type == 'Rework') {
                    $totalReceived -= (float) $adjItem->qty;
                }
            }

            $newStatus = $task->status;
            if ($targetQty > 0 && $totalReceived >= $targetQty) {
                $newStatus = 'Completed';
            } elseif ($totalReceived > 0 && $task->status == 'Planned') {
                $newStatus = 'In Progress';
            }

            if ($task->status == 'Completed' && $totalReceived < $targetQty) {
                $newStatus = 'In Progress';
            }

            if ($newStatus !== $task->status) {
                $oldStatus = $task->status;
                $task->status = $newStatus;
                $task->save();
                $this->logActivity($task->id, 'Status Change', "Task status automatically updated from $oldStatus to $newStatus due to adjustment");
            }

            DB::commit();
            return redirect('task_management')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function formatDate($dateStr)
    {
        if (empty($dateStr))
            return null;
        try {
            return Carbon::createFromFormat('d-m-Y', $dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($dateStr)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    private function logActivity($taskId, $action, $description)
    {
        try {
            TaskLog::create([
                'task_id' => $taskId,
                'user_id' => auth()->id() ?? 1,
                'action' => $action,
                'description' => $description
            ]);
        } catch (\Exception $e) {
        }
    }
    private function syncProductionMovements($task)
    {
        $jobCardId = $task->job_card_entry_id ?? ($task->jobCard->id ?? null);
        if (!$jobCardId)
            return;

        $jc = JobCardEntry::with(['tasks.assignments', 'tasks.stage'])->find($jobCardId);
        if (!$jc)
            return;

        $task->loadMissing('stage');
        $stageId = $task->stage->operation_stage_id ?? null;
        if (!$stageId)
            return;

        $processScheduleId = $task->stage_id;

        $stageTasks = $jc->tasks->filter(function ($t) use ($stageId) {
            return ($t->stage && $t->stage->operation_stage_id == $stageId);
        });

        $assignments = $stageTasks->flatMap->assignments;

        $actualOutward = 0;
        $actualWastage = 0;

        if ($assignments->isNotEmpty()) {
            $assignedServiceIds = $assignments->pluck('service_id')->unique()->filter()->toArray();
            $actualWastage = (float) $assignments->sum('wastage_qty');
            
            if (empty($assignedServiceIds)) {
                $actualOutward = (float) $assignments->sum('completed_qty');
            } else {
                $relevantServices = ProductionService::whereIn('id', $assignedServiceIds)->active()->get();
                
                if ($relevantServices->isNotEmpty()) {
                    $serviceCompletions = $relevantServices->map(function ($service) use ($assignments) {
                        $serviceAssignments = $assignments->where('service_id', $service->id);
                        if ($serviceAssignments->isEmpty()) return 0;

                        $isSequential = $serviceAssignments->count() > 1 && $serviceAssignments->max('issue_qty') >= $serviceAssignments->sum('issue_qty') * 0.9;
                        return $isSequential ? (float) $serviceAssignments->min('completed_qty') : (float) $serviceAssignments->sum('completed_qty');
                    });
                    $actualOutward = $serviceCompletions->min();
                } else {
                    $actualOutward = (float) $assignments->sum('completed_qty');
                }
            }
        }

        if ($task->status == 'Completed' && $actualOutward < (float)$task->issue_qty) {
            $actualOutward = (float)$task->issue_qty;
        }

        if ($actualOutward <= 0 && $actualWastage <= 0)
            return;

        $recordedOutward = \App\Models\ProductionMovement::where('job_card_id', $jobCardId)->where('process_schedule_id', $processScheduleId)->sum('outward_qty');
        $recordedWastage = \App\Models\ProductionMovement::where('job_card_id', $jobCardId)->where('process_schedule_id', $processScheduleId)->sum('wastage_qty');

        $outwardDelta = $actualOutward - $recordedOutward;
        $wastageDelta = $actualWastage - $recordedWastage;

        if ($outwardDelta > 0 || $wastageDelta > 0) {
            \App\Models\ProductionMovement::create([
                'job_card_id' => $jobCardId,
                'process_schedule_id' => $processScheduleId,
                'operation_stage_id' => $stageId,
                'production_service_id' => null,
                'task_id' => $task->id,
                'outward_qty' => max(0, $outwardDelta),
                'wastage_qty' => max(0, $wastageDelta),
                'remarks' => 'Automated progress sync',
                'created_by' => auth()->id()
            ]);

            if ($outwardDelta > 0) {
                $nextSchedule = ProcessSchedule::where('job_card_entry_id', $jobCardId)->where('id', '>', $processScheduleId)->orderBy('id', 'asc')->first();

                if ($nextSchedule) {
                    \App\Models\ProductionMovement::create([
                        'job_card_id' => $jobCardId,
                        'process_schedule_id' => $nextSchedule->id,
                        'operation_stage_id' => $nextSchedule->operation_stage_id,
                        'production_service_id' => null,
                        'task_id' => null,
                        'inward_qty' => $outwardDelta,
                        'remarks' => 'Automated inward from Previous Stage',
                        'created_by' => auth()->id()
                    ]);
                }
            }
        }
    }
}
