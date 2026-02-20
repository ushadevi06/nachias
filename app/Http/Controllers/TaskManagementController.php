<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\JobCardEntry;
use App\Models\User;
use App\Models\ProcessSchedule;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\OperationStage;
use App\Models\Shift;
use App\Models\TaskAdjustment;
use App\Models\ProductionStageConsumable;
use App\Models\StockEntryItem;
use App\Models\RawMaterial;
use App\Models\TaskAssignEmployee;
use App\Models\TaskLog;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TaskManagementController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $tasks = Task::with(['jobCard', 'stage.operationStage', 'operationStage', 'assignments'])->get();
            $allStatuses = TaskStatus::all();

            $boards = [];
            foreach ($allStatuses as $status) {
                // Initialize boards using status name as key for fast lookup
                $boards[$status->name] = ['id' => $status->name, 'title' => $status->name, 'item' => []];
            }

            foreach ($tasks as $t) {
                $statusName = $t->status ?: 'Planned';
                
                // --- Date Fallback Logic ---
                $stage = $t->stage;
                if (!$stage && $t->job_card_entry_id) {
                    // Try to find current schedule if orphaned
                    $osId = $t->operation_stage_id ?? $t->stage_id; 
                    $stage = \App\Models\ProcessSchedule::where('job_card_entry_id', $t->job_card_entry_id)
                        ->where('operation_stage_id', $osId)
                        ->first();
                }

                $jcStart = $stage && $stage->start_date ? Carbon::parse($stage->start_date)->format('d-m-Y') : ($t->jobCard && $t->jobCard->job_card_date ? Carbon::parse($t->jobCard->job_card_date)->format('d-m-Y') : 'N/A');
                $jcEnd = $stage && $stage->due_date ? Carbon::parse($stage->due_date)->format('d-m-Y') : ($t->jobCard && $t->jobCard->delivery_date ? Carbon::parse($t->jobCard->delivery_date)->format('d-m-Y') : 'N/A');

                $stageName = 'No Stage';
                if ($stage) {
                    $stageName = $stage->operationStage ? $stage->operationStage->operation_stage_name : ($stage->stage ?: 'No Stage');
                } elseif ($t->operationStage) {
                    $stageName = $t->operationStage->operation_stage_name ?: 'No Stage';
                }
                // ---------------------------

                $targetQty = (float)($t->jobCard->grand_total_qty ?? 0);
                if ($targetQty == 0 && $t->services && is_array($t->services) && $t->stage_id) {
                    foreach ($t->services as $serviceId) {
                        $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $t->stage_id)->where('service_id', $serviceId)->first();
                        if ($scheduleService) {
                            $targetQty += (float)($scheduleService->calculated_qty ?? 0);
                        }
                    }
                }
                if ($targetQty == 0 && $stage) {
                    $targetQty = (float)($stage->planned_qty ?? 0);
                }
                if ($targetQty == 0) {
                    $targetQty = (float)($t->issue_qty ?? 0);
                }
                
                $totalReceived = 0;
                foreach ($t->assignments as $assign) {
                    $totalReceived += (float)$assign->completed_qty + (float)$assign->wastage_qty;
                }


                if (isset($boards[$statusName])) {
                    $boards[$statusName]['item'][] = [
                        'id' => $t->id,
                        'eid' => $t->id,
                        'task_no' => $t->task_no,
                        'title' => ($t->job_card_no ?? 'No JC') . ' - ' . (int)$targetQty . ' PCS',
                        'stage_name' => $stageName,
                        'badge-text' => $statusName,
                        'start-date' => $t->issue_date ? Carbon::parse($t->issue_date)->format('d-m-Y') : 'N/A',
                        'due-date' => $t->due_date ? Carbon::parse($t->due_date)->format('d-m-Y') : 'N/A',
                        'jc-start' => $jcStart,
                        'jc-end' => $jcEnd,
                        'working_level' => (float)max(0, $totalReceived) . ' / ' . (float)$targetQty . ' PCS',
                        'total_received' => (float)max(0, $totalReceived),
                        'target_qty' => (float)$targetQty
                    ];
                }
            }
            return response()->json(array_values($boards));
        }

        if (auth()->id() != 1 && !auth()->user()->can('view task-management')) {
            return unauthorizedRedirect();
        }
        $allStatuses = TaskStatus::all();
        return view('task_management/view', compact('allStatuses')); 
    }
    
    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit task-management')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create task-management')) {
                return unauthorizedRedirect();
            }
        }
        
        $task = null;
        $jobCard = null;
        $production = null;
        $stages = collect([]);

        if ($id) {
            $task = Task::with([
                'production', 
                'jobCard', 
                'stage.operationStage',
                'stage.services.productionService',
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
                    'serviceProvider', 
                    'services.productionService'
                ])->where('job_card_entry_id', $jobCard->id)->get();
            }

            if ($task->stage_id && (!$stages->where('id', $task->stage_id)->count() || !$task->stage)) {
                $currentStage = ProcessSchedule::with(['operationStage', 'serviceProvider', 'services.productionService'])->find($task->stage_id);
                if ($currentStage) {
                    $task->setRelation('stage', $currentStage);
                    if (!$stages->where('id', $task->stage_id)->count()) {
                        $stages->push($currentStage);
                    }
                }
            }
        }

        if (request()->isMethod('post')) {
            $request = request();

            $request->validate([
                'assignments' => 'required|array|min:1',
                'assignments.*.service_id' => 'required',
                'assignments.*.issued_to' => 'required',
                'assignments.*.issue_date' => 'required',
                'assignments.*.due_date' => 'required',
                'issue_store' => 'required',
                'status' => 'required'
            ], [
                'assignments.*.service_id.required' => 'This field is required.',
                'assignments.*.issued_to.required' => 'This field is required.',
                'assignments.*.issue_date.required' => 'This field is required.',
                'assignments.*.due_date.required' => 'This field is required.',
                'assignments.*.issue_qty.required' => 'This field is required.',
                'assignments.*.issue_qty.min' => 'Issue Qty must be at least 1',
            ]);

            $assignments = $request->input('assignments');
            if (!$assignments) {
                $assignments = [[
                    'issued_to' => $request->issued_to,
                    'service_ids' => $request->service_ids,
                    'issue_date' => $request->issue_date,
                    'due_date' => $request->due_date,
                    'total_hrs' => $request->total_hrs,
                    'issue_qty' => $request->issue_qty,
                ]];
            }

            $commonData = $request->only(['job_card_entry_id', 'job_card_no', 'stage_id', 'issue_store', 'remarks', 'status']);

            DB::beginTransaction();
            try {
                $taskData = $commonData;
                $taskData['updated_by'] = auth()->id();
                
                if ($id) {
                    $task = Task::findOrFail($id);
                    $updates = [];
                    if ($task->remarks != $taskData['remarks']) $updates[] = "Remarks changed";
                    if ($task->stage_id != $taskData['stage_id']) $updates[] = "Stage changed";
                    if ($task->status != $taskData['status']) $updates[] = "Status changed to " . $taskData['status'];
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

                foreach ($assignments as $assign) {
                    if (empty($assign['issued_to'])) continue;

                    $assignData = [
                        'task_id' => $task->id,
                        'issued_to' => $assign['issued_to'],
                        'issue_qty' => $assign['issue_qty'] ?? 0,
                        'total_hrs' => $assign['total_hrs'] ?? 0,
                        'status' => $assign['status'] ?? 'Open',
                        'remarks' => $assign['remarks'] ?? null,
                        'created_by' => auth()->id(),
                    ];

                    $totalIssueQty += (float)($assign['issue_qty'] ?? 0);

                    $assignData['issue_date'] = $this->formatDate($assign['issue_date'] ?? null);
                    $assignData['due_date'] = $this->formatDate($assign['due_date'] ?? null);

                    $serviceId = $assign['service_id'] ?? $assign['services'] ?? null;
                    if (is_array($serviceId)) {
                        $serviceId = $serviceId[0] ?? null;
                    }
                    $assignData['service_id'] = $serviceId;

                    if ($serviceId) {
                        $allServiceIds[] = $serviceId;
                    }

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

        if (!$id) {
            if (request()->has('job_card_id')) {
                $jobCard = JobCardEntry::find(request()->job_card_id);
                if ($jobCard) {
                    $stages = ProcessSchedule::with([
                        'operationStage', 
                        'serviceProvider', 
                        'services.productionService'
                    ])->where('job_card_entry_id', $jobCard->id)->get();
                }
            }
        }

        if ($stages->isNotEmpty() && request()->has('stage_id')) {
            $sId = request()->stage_id;
            if (!$stages->contains('id', $sId)) {
                $psStage = $stages->where('operation_stage_id', $sId)->first();
                if ($psStage) {
                    request()->merge(['stage_id' => $psStage->id]);
                }
            }
        }
        $nextTaskNo = $id ? $task->task_no : 'TASK-' . str_pad(Task::count() + 1, 3, '0', STR_PAD_LEFT); 
        $users = User::where('id', '!=', 1)->where('status', 'Active')->get();
        $stores = \App\Models\StoreType::where('status', 'Active')->get();
        
        $allStatuses = TaskStatus::pluck('name')->toArray();
        if (empty($allStatuses)) {
            $allStatuses = ['Planned', 'In Progress', 'Completed', 'Hold'];
        }

        $shifts = \App\Models\Shift::active()->get();

        $nextAdjNo = 'ADJ-' . date('Y') . '-' . str_pad(TaskAdjustment::count() + 1, 3, '0', STR_PAD_LEFT);
        
        $relatedTasks = Task::where('job_card_entry_id', ($jobCard->id ?? 0))->get();
    
        $taskAdjustment = null;
        $taskAdjustments = collect([]);
        if ($task) {
            $taskAdjustments = TaskAdjustment::with(['items.rawMaterial', 'items.uom', 'items.service'])
                ->where('task_id', $task->id)
                ->latest()
                ->get();

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
            $selectedSchedule = ProcessSchedule::with(['services.productionService', 'operationStage'])->find($finalStageId);
        }
        
        if ($selectedSchedule) {
            $services = \App\Models\ProductionService::where('operation_stage_id', $selectedSchedule->operation_stage_id)
                ->where('status', 'Active')
                ->get()
                ->map(function($s) use ($selectedSchedule) {
                    return [
                        'id' => $s->id,
                        'name' => ($s->service_name ?? '') . ' - ' . ($s->service_code ?? ''),
                        'qty' => $selectedSchedule->planned_qty ?? 0
                    ];
                })->values()->all();
        }


        $jobCardGrnNo = '';
        if ($jobCard) {
            $jobCardGrnNo = \App\Models\StockEntryItem::whereIn('id', function($q) use ($jobCard) {
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

        return view('task_management/add', compact('task', 'production', 'jobCard', 'stages', 'users', 'stores', 'nextTaskNo', 'allStatuses',  'nextAdjNo', 'relatedTasks', 'taskAdjustment', 'shifts',  'taskAdjustments', 'services', 'jobCardGrnNo'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details task-management')) {
            return unauthorizedRedirect();
        }
        $task = Task::with(['adjustments.items.rawMaterial', 'adjustments.items.uom'])->findOrFail($id);
        return view('task_management/view_details', compact('task'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $task = Task::findOrFail($request->task_id);
            $task->status = $request->status;
            TaskStatus::firstOrCreate(['name' => $request->status], ['color' => 'info', 'progress_percent' => 10]);
            $task->save();
            $this->logActivity($task->id, 'Status Change', 'Status changed to ' . $request->status);
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
                
                $completed = (float)($assignData['completed_qty'] ?? 0);
                $wastage = (float)($assignData['wastage_qty'] ?? 0);
                $assignedQty = (float)$assignment->issue_qty;

                $qc_checked = (float)($assignData['qc_checked_qty'] ?? 0);
                $qc_passed = (float)($assignData['qc_passed_qty'] ?? 0);
                $qc_rejected = (float)($assignData['qc_rejected_qty'] ?? 0);

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

                $assignment->update([
                    'completed_qty' => $completed,
                    'inprogress_qty' => $inprogress,
                    'wastage_qty' => $wastage,
                    'qc_checked_qty' => $qc_checked,
                    'qc_passed_qty' => $qc_passed,
                    'qc_rejected_qty' => $qc_rejected,
                    'qc_status' => $qc_status,
                    'status' => $status
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
                if ((float)$completed != (float)$originalCompleted) $changes[] = "Completed: " . (float)$originalCompleted . " -> " . (float)$completed;
                if ((float)$wastage != (float)$originalWastage) $changes[] = "Wastage: " . (float)$originalWastage . " -> " . (float)$wastage;
                if ((float)$qc_checked != (float)$originalQcChecked) $changes[] = "QC Checked: " . (float)$originalQcChecked . " -> " . (float)$qc_checked;

                if (!empty($changes)) {
                    $empName = $assignment->employee->name ?? 'Unknown Employee';
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
                $serviceData = \App\Models\ProductionService::whereIn('id', $task->services)
                    ->get(['id', 'service_name', 'service_code'])
                    ->map(function($s) {
                        return [
                            'id' => $s->id,
                            'name' => $s->service_name . ' (' . $s->service_code . ')'
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
        $schedule = \App\Models\ProcessSchedule::with(['operationStage', 'production.jobCard.fabricDetails', 'jobCard.fabricDetails', 'jobCard.item'])->find($id);
        
        $jobCard = null;
        $stageName = '';

        if ($schedule) {
            $stageName = $schedule->operationStage->operation_stage_name ?? ($schedule->stage ?? '');
            $jobCard = $schedule->jobCard ?? ($schedule->production->jobCard ?? null);
        }

        if (!$jobCard && $request->has('job_card_id')) {
            $jobCard = \App\Models\JobCardEntry::with(['fabricDetails', 'item'])->find($request->job_card_id);
        }

        if (!$jobCard) return response()->json(['success' => false, 'message' => 'Job Card not found']);
        
        $consumableConfigs = [];
        if ($stageName) {
            $consumableConfigs = \App\Models\ProductionStageConsumable::where('stage', $stageName)->where('status', 'Active')->pluck('raw_material_id')->toArray();
        }

        $rmIdsFromArt = [];
        $rmIdsFromIssues = [];
        $rmIdsFromItemMaster = [];

        if ($jobCard) {
            $artNumbers = $jobCard->fabricDetails->pluck('art_no')->filter()->map(function($val) { return trim($val); })->toArray();
            
            $rmIdsDirect = RawMaterial::where(function($q) use ($artNumbers) {
                $q->whereIn('code', $artNumbers)->orWhereIn('name', $artNumbers);
            })->pluck('id')->toArray();
            
            $rmIdsFromGrn = \App\Models\StockEntryItem::whereIn('grn_entry_item_id', function($query) use ($artNumbers) {
                $query->select('id')->from('grn_entry_items')->whereIn('art_no', $artNumbers);
            })->pluck('raw_material_id')->toArray();

            $rmIdsFromIssues = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)
                ->whereNotNull('stock_entry_item_id')
                ->pluck('raw_material_id')
                ->toArray();

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
            $grnIdsForJobCard = \App\Models\GrnEntry::whereIn('purchase_invoice_id', function($q) use ($jobCard) {
                $q->select('id')->from('purchase_invoices')->where('purchase_order_id', $jobCard->purchase_order_id);
            })->pluck('id')->toArray();
        }

        $formatted = $materials->map(function($m) use ($jobCard, $grnIdsForJobCard) {
            $grnItem = null;
            
            $grnItem = \App\Models\StockEntryItem::where('raw_material_id', $m->id)
                ->whereIn('id', function($q) use ($jobCard) {
                    $q->select('stock_entry_item_id')
                    ->from('job_card_issue_items')
                    ->where('job_card_entry_id', $jobCard->id);
                })->with('grnEntryItem.grnEntry')->first();

            if (!$grnItem && !empty($grnIdsForJobCard)) {
                $grnItem = \App\Models\StockEntryItem::where('raw_material_id', $m->id)
                    ->whereHas('grnEntryItem', function($q) use ($grnIdsForJobCard) {
                        $q->whereIn('grn_entry_id', $grnIdsForJobCard);
                    })->with('grnEntryItem.grnEntry')->first();
            }

            if (!$grnItem) {
                $grnItem = \App\Models\StockEntryItem::where('raw_material_id', $m->id)
                    ->with('grnEntryItem.grnEntry')
                    ->latest()
                    ->first();
            }

            $grnNo = $grnItem?->grnEntryItem?->grnEntry?->grn_number ?? '';
            $artNo = $grnItem?->art_no ?? ($grnItem?->grnEntryItem?->art_no ?? '');

            if (empty($artNo) && $jobCard) {
                $fabric = $jobCard->fabricDetails->first(function($f) use ($m) {
                    return trim($f->art_no) == trim($m->code) || trim($f->art_no) == trim($m->name);
                });
                if ($fabric) $artNo = $fabric->art_no;
            }

            return [
                'id'     => $m->id,
                'text'   => $m->name . ($m->code ? " ({$m->code})" : ""),
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
                $currentStock = \App\Models\StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])->sum(DB::raw('qty_in - qty_out'));
                
                $diff = (float)$itemData['qty'];
                $newStock = in_array($itemData['adjustment_type'], $decreaseTypes) ? ($currentStock - $diff) : ($currentStock + $diff);

                $artNo = $itemData['art_no'] ?? null;
                $grnNo = $itemData['grn_no'] ?? null;

                if ((empty($artNo) || empty($grnNo))) {
                    $stockItemQuery = \App\Models\StockEntryItem::where('raw_material_id', $itemData['raw_material_id']);
                    
                    if ($request->job_card_id) {
                        $stockItemQuery->whereIn('id', function($q) use ($request) {
                            $q->select('stock_entry_item_id')
                            ->from('job_card_issue_items')
                            ->where('job_card_entry_id', $request->job_card_id);
                        });
                    }

                    $stockItem = $stockItemQuery->with('grnEntryItem.grnEntry')->first();

                    if (!$stockItem && $request->job_card_id) {
                        $jobCard = \App\Models\JobCardEntry::find($request->job_card_id);
                        if ($jobCard && $jobCard->purchase_order_id) {
                            $grnIds = \App\Models\GrnEntry::whereIn('purchase_invoice_id', function($q) use ($jobCard) {
                                $q->select('id')->from('purchase_invoices')->where('purchase_order_id', $jobCard->purchase_order_id);
                            })->pluck('id')->toArray();

                            if (!empty($grnIds)) {
                                $stockItem = \App\Models\StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])
                                    ->whereHas('grnEntryItem', function($q) use ($grnIds) {
                                        $q->whereIn('grn_entry_id', $grnIds);
                                    })->with('grnEntryItem.grnEntry')->first();
                            }
                        }
                    }

                    if (!$stockItem) {
                        $stockItem = \App\Models\StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])->with('grnEntryItem.grnEntry')->latest()->first();
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

                $materialName = \App\Models\RawMaterial::find($itemData['raw_material_id'])->name ?? 'Unknown Material';
                $adjustedItems[] = "$materialName (" . $itemData['adjustment_type'] . ": " . (float)$diff . ")";
            }

            $this->logActivity($task->id, 'Adjustment', "Adjustment **$nextAdjNo** posted for reason: " . $request->overall_reason . ". Items: " . implode(', ', $adjustedItems));

            $issueQty = (float)($task->issue_qty ?? 0);
            $serviceCount = is_array($task->services) ? count($task->services) : 1;
            $targetQty = $issueQty * $serviceCount;

            $totalReceived = 0;
            
            $allAdjustmentItems = \App\Models\TaskAdjustmentItem::whereHas('adjustment', function($q) use ($task) {
                $q->where('task_id', $task->id);
            })->get();

            foreach($allAdjustmentItems as $adjItem) { 
                if ($adjItem->adjustment_type == 'Loss' || $adjItem->adjustment_type == 'Excess') {
                    $totalReceived += (float)$adjItem->qty;
                } elseif ($adjItem->adjustment_type == 'Rework') {
                    $totalReceived -= (float)$adjItem->qty;
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

    private function formatDate($dateStr) {
        if (empty($dateStr)) return null;
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
}
