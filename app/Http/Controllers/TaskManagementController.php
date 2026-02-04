<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\JobCardEntry;
use App\Models\User;
use App\Models\ProcessSchedule;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskReceive;
use App\Models\Shift;
use App\Models\TaskAdjustment;
use App\Models\ProductionStageConsumable;
use App\Models\StockConsumableIssue;
use App\Models\StockConsumableIssueItem;
use App\Models\StockConsumableStockDetail;
use App\Models\StockEntryItem;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TaskManagementController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $defaults = [
                ['name' => 'Planned', 'color' => 'secondary', 'progress' => 0],
                ['name' => 'In Progress', 'color' => 'secondary', 'progress' => 50],
                ['name' => 'Completed', 'color' => 'success', 'progress' => 100],
                ['name' => 'Hold', 'color' => 'warning', 'progress' => 25],
            ];
            
            foreach($defaults as $d) {
                TaskStatus::firstOrCreate(['name' => $d['name']], [
                    'color' => $d['color'],
                    'progress_percent' => $d['progress']
                ]);
            }

            $tasks = Task::with(['receives', 'adjustments'])->get();
            $allStatuses = TaskStatus::all();

            $boards = [];
            foreach ($allStatuses as $status) {
                $boards[] = ['id' => $status->name, 'title' => $status->name, 'item' => []];
            }

            foreach ($tasks as $t) {
                $statusName = $t->status ?: 'Planned';
                $targetQty = 0;
                $serviceTargets = [];
                
                if ($t->services && is_array($t->services) && $t->stage_id) {
                    foreach ($t->services as $serviceId) {
                        $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $t->stage_id)->where('service_id', $serviceId)->first();
                        if ($scheduleService) {
                            $qty = (float)($scheduleService->calculated_qty ?? 0);
                            $serviceTargets[$serviceId] = $qty;
                            $targetQty += $qty;
                        }
                    }
                }
                if ($targetQty == 0 && $t->stage) {
                    $targetQty = (float)($t->stage->planned_qty ?? 0);
                }
                
                $totalReceived = 0;
                foreach ($t->receives as $receive) {
                    if ($receive->received_services && is_array($receive->received_services)) {
                        foreach ($receive->received_services as $serviceId => $quantities) {
                            $goodQty = (float)($quantities['good_qty'] ?? 0);
                            $wastageQty = (float)($quantities['wastage_qty'] ?? 0);
                            $totalReceived += $goodQty + $wastageQty;
                        }
                    } else {
                        $totalReceived += (float)($receive->good_qty ?? 0);
                    }
                }
                
                foreach($t->adjustments as $adj) {
                    if ($adj->adjustment_type == 'Loss') {
                        $totalReceived += (float)$adj->qty; 
                    } elseif ($adj->adjustment_type == 'Excess') {
                        $totalReceived += (float)$adj->qty;
                    } elseif ($adj->adjustment_type == 'Rework') {
                        $totalReceived -= (float)$adj->qty;
                    }
                }
                
                if ($statusName == 'Completed') {
                    $progress = 100;
                } elseif ($targetQty > 0) {
                    $progress = min(100, max(0, round(($totalReceived / $targetQty) * 100)));
                } else {
                    $progress = 0;
                }

                $boardIndex = array_search($statusName, array_column($boards, 'id'));
                if ($boardIndex !== false) {
                    $boards[$boardIndex]['item'][] = [
                        'id' => $t->id,
                        'eid' => $t->id,
                        'task_no' => $t->task_no,
                        'title' => ($t->job_card_no ?? 'No JC') . ' - ' . (int)$targetQty . ' PCS',
                        'badge-text' => $statusName,
                        'start-date' => $t->issue_date ? Carbon::parse($t->issue_date)->format('d-m-Y') : 'N/A',
                        'due-date' => $t->due_date ? Carbon::parse($t->due_date)->format('d-m-Y') : 'N/A',
                        'progress' => $progress,
                        'working_level' => (float)max(0, $totalReceived) . ' / ' . (float)$targetQty . ' PCS',
                        'total_received' => (float)max(0, $totalReceived),
                        'target_qty' => (float)$targetQty
                    ];
                }
            }
            return response()->json($boards);
        }

        if (auth()->id() != 1 && !auth()->user()->can('view task')) {
            return unauthorizedRedirect();
        }
            $allStatuses = TaskStatus::all();
            return view('task_management/view', compact('allStatuses')); 
    }
    
    public function add($id = null)
    {
        $productionId = request()->production_id;
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit task')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create task')) {
                return unauthorizedRedirect();
            }
        }

        $task = null;
        if ($id) {
            $task = Task::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();
            
            if ($request->issue_date) {
                $request->merge(['issue_date' => Carbon::createFromFormat('d-m-Y', $request->issue_date)->format('Y-m-d')]);
            }
            if ($request->due_date) {
                $request->merge(['due_date' => Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d')]);
            }

            $request->validate([
                'stage_id' => 'required',
                'issued_to' => 'required',
                'service_ids' => 'required',
                'issue_date' => 'required|date',
                'due_date' => 'nullable|date',
                'status' => 'required|string|max:255'
            ],[
                'required' => 'This field is required',
            ]);

            DB::beginTransaction();
            try {
                $data = $request->except(['_token', 'service_ids']);
                $data['services'] = $request->service_ids;
                TaskStatus::firstOrCreate(['name' => $data['status']], ['color' => 'info', 'progress_percent' => 10]);

                if (!$id) {
                    $taskCount = Task::count();
                    $data['task_no'] = 'TASK-' . str_pad($taskCount + 1, 3, '0', STR_PAD_LEFT);
                    $data['created_by'] = auth()->id();
                    $task = Task::create($data);
                } else {
                    $data['updated_by'] = auth()->id();
                    $task->update($data);
                }

                DB::commit();
                return redirect('task_management')->with('success', 'Task saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()])->with('active_tab', 'issue');
            }
        }

        $production = null;
        $jobCard = null;
        $stages = collect([]);
        $users = [];
        $nextTaskNo = $id ? $task->task_no : 'TASK-' . str_pad(Task::count() + 1, 3, '0', STR_PAD_LEFT); 

        if (!$id && request()->has('production_id')) {
            try {
                $prodId = \Illuminate\Support\Facades\Crypt::decrypt(request()->production_id);
                $production = Production::with([
                    'jobCard',
                    'processSchedules.operationStage',
                    'processSchedules.serviceProvider',
                    'processSchedules.services.productionService'
                ])->find($prodId);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $production = null;
            } catch (\Exception $e) {
                $production = null;
            }
            
            if ($production) {
                $jobCard = $production->jobCard;
                $stages = $production->processSchedules;
            }
        } elseif ($task) {
            $production = Production::with([
                'jobCard',
                'processSchedules.operationStage',
                'processSchedules.serviceProvider',
                'processSchedules.services.productionService'
            ])->find($task->production_id);
            if ($production) {
                $jobCard = $production->jobCard;
                $stages = $production->processSchedules;
            }
        }
        $users = User::where('id', '!=', 1)->where('status', 'Active')->get();
        $stores = \App\Models\StoreType::where('status', 'Active')->get();
        
        $allStatuses = TaskStatus::pluck('name')->toArray();
        if (empty($allStatuses)) {
            $allStatuses = ['Planned', 'In Progress', 'Completed', 'Hold'];
        }

        $shifts = \App\Models\Shift::where('status', 'Active')->get();

        $nextTRNo = 'REC-' . date('Y') . '-' . str_pad(TaskReceive::count() + 1, 3, '0', STR_PAD_LEFT);
        $nextAdjNo = 'ADJ-' . date('Y') . '-' . str_pad(TaskAdjustment::count() + 1, 3, '0', STR_PAD_LEFT);
        
        $relatedTasks = Task::where('job_card_entry_id', ($jobCard->id ?? 0))->get();
    
        $taskReceive = null;
        $taskAdjustment = null;
        if ($task) {
            $taskReceive = TaskReceive::where('task_id', $task->id)->latest()->first();
            if ($taskReceive) {
                $nextTRNo = $taskReceive->task_receive_no;
            }

            $taskAdjustment = TaskAdjustment::where('task_id', $task->id)->latest()->first();
            if ($taskAdjustment) {
                $nextAdjNo = $taskAdjustment->adjustment_no;
            }
        }

        return view('task_management/add', compact('task', 'production', 'jobCard', 'stages', 'users', 'stores', 'nextTaskNo', 'allStatuses', 'nextTRNo', 'nextAdjNo', 'relatedTasks', 'taskReceive', 'taskAdjustment', 'shifts'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view task')) {
            return unauthorizedRedirect();
        }
        $task = Task::findOrFail($id);
        return view('task_management/view_details', compact('task'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $task = Task::findOrFail($request->task_id);
            $task->status = $request->status;
            TaskStatus::firstOrCreate(['name' => $request->status], ['color' => 'info', 'progress_percent' => 10]);
            $task->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete task')) {
            return unauthorizedRedirect();
        }
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect('task_management')->with('success', 'Task deleted successfully');
    }

    public function receive_add($id = null)
    {
        $taskReceive = null;
        if ($id) {
            $taskReceive = TaskReceive::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();
            if ($request->received_date) {
                $request->merge(['received_date' => Carbon::createFromFormat('d-m-Y', $request->received_date)->format('Y-m-d')]);
            }

            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'received_date' => 'required|date',
                'received_store' => 'required',
                'shift_id' => 'nullable|exists:shifts,id',
                'received_services' => 'required|array'
            ],[
                'required' => 'This field is required.'
            ]);

            $task = Task::findOrFail($request->task_id);

            DB::beginTransaction();
            try {
                $data = $request->all();
                $task = Task::findOrFail($request->task_id);
                $data['received_from'] = $task->issued_to; 
                
                $totalGood = 0;
                $totalRework = 0;
                $totalWastage = 0;
                
                foreach ($request->received_services as $svc) {
                    $totalGood += (float)($svc['good_qty'] ?? 0);
                    $totalRework += (float)($svc['rework_qty'] ?? 0);
                    $totalWastage += (float)($svc['wastage_qty'] ?? 0);
                }

                $data['good_qty'] = $totalGood;
                $data['rework_qty'] = $totalRework;
                $data['wastage_qty'] = $totalWastage;

                if (!$id) {
                    $trCount = TaskReceive::count();
                    $data['task_receive_no'] = $request->task_receive_no ?: ('REC-' . date('Y') . '-' . str_pad($trCount + 1, 3, '0', STR_PAD_LEFT));
                    $data['created_by'] = auth()->id();
                    TaskReceive::create($data);
                } else {
                    $data['updated_by'] = auth()->id();
                    $data['updated_by'] = auth()->id();
                    $taskReceive->update($data);
                }

                $task->refresh(); 
                $issueQty = (float)($task->issue_qty ?? 0);
                $serviceCount = is_array($task->services) ? count($task->services) : 1;
                $targetQty = $issueQty * $serviceCount;

                $totalReceived = (float)$task->receives()->sum('good_qty');
                foreach($task->adjustments as $adj) {
                    if ($adj->adjustment_type == 'Loss' || $adj->adjustment_type == 'Excess') {
                        $totalReceived += (float)$adj->qty;
                    } elseif ($adj->adjustment_type == 'Rework') {
                        $totalReceived -= (float)$adj->qty;
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
                    $task->status = $newStatus;
                    $task->save();
                    \App\Models\TaskStatus::firstOrCreate(['name' => $newStatus], ['color' => 'secondary']);
                }

                /* 
                if ($newStatus == 'Completed') {
                    $task->load('stage'); 
                    if ($task->stage && $task->stage->consumables_issued_at === null) {
                        $totalGoodQty = $task->receives()->sum('good_qty');
                        
                        if ($totalGoodQty > 0) {
                            $this->issueConsumables($task, $totalGoodQty);
                            $task->stage->update(['consumables_issued_at' => now()]);
                        }
                    }
                }
                */

                DB::commit();
                return redirect('task_management')->with('success', 'Task received successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage());
            }
        }

        $tasks = Task::all(); 
        $stores = \App\Models\StoreType::where('status', 'Active')->get();
        $nextTRNo = 'TR-' . str_pad(TaskReceive::count() + 1, 3, '0', STR_PAD_LEFT);
        
        return view('task_management/receive_add', compact('taskReceive', 'tasks', 'nextTRNo', 'stores'));
    }

    public function getTaskDetails($id)
    {
        $task = Task::with(['assignee', 'stage.operationStage'])->find($id);
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
                'plant' => $task->stage->scheduled_to ?? 'N/A',
                'services' => $serviceData
            ]);
        }
        return response()->json(['success' => false]);
    }

    public function getStageConsumables($id)
    {
        $schedule = \App\Models\ProcessSchedule::with(['operationStage', 'production.jobCard.fabricDetails'])->find($id);
        if (!$schedule) return response()->json(['success' => false]);
        
        $stageName = $schedule->operationStage->operation_stage_name ?? ($schedule->stage ?? '');
        
        $consumableConfigs = \App\Models\ProductionStageConsumable::where('stage', $stageName)->where('status', 'Active')->pluck('raw_material_id')->toArray();

        $jobCard = $schedule->production->jobCard ?? null;
        $rmIdsFromArt = [];
        $rmIdsFromIssues = [];
        if ($jobCard) {
            $artNumbers = $jobCard->fabricDetails->pluck('art_no')->filter()->toArray();
            $rmIdsFromArt = RawMaterial::where(function($q) use ($artNumbers) {
                $q->whereIn('code', $artNumbers)->orWhereIn('name', $artNumbers);
            })->pluck('id')->toArray();
            
            $rmIdsFromIssues = \App\Models\StockEntryItem::whereIn('id', function($q) use ($jobCard) {
                $q->select('stock_entry_item_id')
                ->from('job_card_issue_items')
                ->where('job_card_entry_id', $jobCard->id)
                ->whereNotNull('stock_entry_item_id');
            })->pluck('raw_material_id')->toArray();
        }

        $allRelatedRmIds = array_unique(array_merge($rmIdsFromArt, $rmIdsFromIssues, $consumableConfigs));
        $materials = collect([]);
        if (!empty($allRelatedRmIds)) {
            $materials = RawMaterial::whereIn('id', $allRelatedRmIds)
                ->where('status', 'Active')
                ->get();
        }

        $formatted = $materials->map(function($m) {
            return [
                'id' => $m->id,
                'text' => $m->name . ($m->code ? " ({$m->code})" : "")
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
            /*
            if ($id) {
                $existing = TaskAdjustment::find($id);
                if ($existing && $existing->status == 'Posted') {
                    throw new \Exception("Posted adjustments cannot be edited. Please create a new adjustment for corrections.");
                }
            }
            */
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
                'service_id' => $request->service_id,
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

            foreach ($request->items as $itemData) {
                $currentStock = \App\Models\StockEntryItem::where('raw_material_id', $itemData['raw_material_id'])->sum(DB::raw('qty_in - qty_out'));
                
                $diff = (float)$itemData['qty'];
                $newStock = in_array($itemData['adjustment_type'], $decreaseTypes) ? ($currentStock - $diff) : ($currentStock + $diff);
                $item = $adjustment->items()->create([
                    'raw_material_id' => $itemData['raw_material_id'],
                    'adjustment_type' => $itemData['adjustment_type'],
                    'qty' => $diff,
                    'remarks' => $itemData['remarks'] ?? '',
                    'previous_stock' => $currentStock,
                    'new_stock' => $newStock
                ]);

                /*
                if (in_array($itemData['adjustment_type'], $decreaseTypes)) {
                    $this->deductSingleMaterialStock(
                        $itemData['raw_material_id'], 
                        $diff, 
                        'Task Adj (' . $itemData['adjustment_type'] . ') - ' . $nextAdjNo,
                        $stageName,
                        $itemData['adjustment_type']
                    );
                } else {
                    $this->addSingleMaterialStock(
                        $itemData['raw_material_id'], 
                        $diff, 
                        'Task Adj (' . $itemData['adjustment_type'] . ') - ' . $nextAdjNo,
                        $stageName
                    );
                }
                */
            }

            $issueQty = (float)($task->issue_qty ?? 0);
            $serviceCount = is_array($task->services) ? count($task->services) : 1;
            $targetQty = $issueQty * $serviceCount;

            $totalReceived = (float)$task->receives()->sum('good_qty');
            
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
                $task->status = $newStatus;
                $task->save();
            }

            DB::commit();
            return redirect('task_management')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function issueConsumables($task, $qtyProduced)
    {
        $stageName = $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? 'N/A');
        $consumables = ProductionStageConsumable::where('stage', $stageName)->where('status', 'Active')->get();
        if ($consumables->isEmpty()) return;

        $issueCount = StockConsumableIssue::count();
        $issueNo = 'ISSUE-AUTO-' . date('Ymd') . '-' . str_pad($issueCount + 1, 4, '0', STR_PAD_LEFT);
        
        $issue = StockConsumableIssue::create([
            'issue_no' => $issueNo,
            'issue_date' => date('Y-m-d'),
            'issue_type' => 'Consumable Issue',
            'production_stage' => $stageName,
            'remarks' => 'Auto-issued for Task: ' . $task->task_no . ' (Qty: ' . $qtyProduced . ')',
            'status' => 'Posted',
            'created_by' => auth()->id(),
        ]);

        foreach ($consumables as $config) {
            $requiredQty = $qtyProduced * $config->quantity_per_unit;
            
            if ($requiredQty <= 0) continue;

            $tempStockUsage = [];
            $weightedCost = 0;
            $remainingToDeduct = $requiredQty;

            $stockCandidates = StockEntryItem::where('raw_material_id', $config->raw_material_id)
                ->whereRaw('(qty_in - qty_out) > 0')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($stockCandidates as $stockItem) {
                if ($remainingToDeduct <= 0) break;

                $available = $stockItem->qty_in - $stockItem->qty_out;
                if ($available <= 0) continue;

                $take = min($available, $remainingToDeduct);
                
                $stockItem->qty_out += $take;
                $stockItem->save();
                
                $tempStockUsage[] = [
                    'stock_entry_item_id' => $stockItem->id,
                    'qty' => $take
                ];
                
                $weightedCost += ($take * $stockItem->price);
                $remainingToDeduct -= $take;
            }

            $unitPrice = ($requiredQty > 0) ? ($weightedCost / $requiredQty) : 0;
            $totalValue = $requiredQty * $unitPrice;

            $issueItem = StockConsumableIssueItem::create([
                'stock_consumable_issue_id' => $issue->id,
                'raw_material_id' => $config->raw_material_id,
                'stock_entry_item_id' => $tempStockUsage[0]['stock_entry_item_id'] ?? null,
                'qty_issued' => $requiredQty,
                'qty_returned' => 0,
                'net_consumption' => $requiredQty,
                'uom_id' => $config->uom_id,
                'unit_price' => $unitPrice,
                'total_value' => $totalValue,
                'created_by' => auth()->id(),
            ]);

            foreach ($tempStockUsage as $usage) {
                StockConsumableStockDetail::create([
                    'stock_consumable_issue_item_id' => $issueItem->id,
                    'stock_entry_item_id' => $usage['stock_entry_item_id'],
                    'qty' => $usage['qty']
                ]);
            }
        }
    }

    private function deductSingleMaterialStock($materialId, $qty, $remarks, $stageName = null, $type = 'Stock Adjustment')
    {
        if ($qty <= 0) return;
        $issueCount = \App\Models\StockConsumableIssue::count();
        $issueNo = 'ISSUE-ADJ-' . date('Ymd') . '-' . str_pad($issueCount + 1, 4, '0', STR_PAD_LEFT);
        
        $issueType = 'Stock Adjustment';
        if ($type == 'Rework') $issueType = 'Rework';

        $issue = \App\Models\StockConsumableIssue::create([
            'issue_no' => $issueNo,
            'issue_date' => date('Y-m-d'),
            'issue_type' => $issueType,
            'production_stage' => $stageName,
            'remarks' => $remarks,
            'status' => 'Posted',
            'created_by' => auth()->id(),
        ]);

        $tempStockUsage = [];
        $weightedCost = 0;
        $remainingToDeduct = $qty;

        $mat = \App\Models\RawMaterial::find($materialId);
        $matName = $mat ? $mat->name : "Unknown Item";

        $stockCandidates = \App\Models\StockEntryItem::where('raw_material_id', $materialId)->whereRaw('(qty_in - qty_out) > 0')->orderBy('id', 'asc')->get();

        $totalAvailable = $stockCandidates->sum(function($item) { return $item->qty_in - $item->qty_out; });
        if ($totalAvailable < $qty) {
            throw new \Exception("Insufficient stock for '{$matName}'. Available: {$totalAvailable}, Required: {$qty}");
        }

        foreach ($stockCandidates as $stockItem) {
            if ($remainingToDeduct <= 0) break;
            $available = $stockItem->qty_in - $stockItem->qty_out;
            if ($available <= 0) continue;
            $take = min($available, $remainingToDeduct);
            $stockItem->qty_out += $take;
            $stockItem->save();
            $tempStockUsage[] = ['stock_entry_item_id' => $stockItem->id, 'qty' => $take];
            $weightedCost += ($take * $stockItem->price);
            $remainingToDeduct -= $take;
        }

        $unitPrice = ($qty > 0) ? ($weightedCost / $qty) : 0;
        $totalValue = $qty * $unitPrice;

        $mat = \App\Models\RawMaterial::find($materialId);
        $issueItem = \App\Models\StockConsumableIssueItem::create([
            'stock_consumable_issue_id' => $issue->id,
            'raw_material_id' => $materialId,
            'stock_entry_item_id' => $tempStockUsage[0]['stock_entry_item_id'] ?? null,
            'qty_issued' => $qty,
            'qty_returned' => 0,
            'net_consumption' => $qty,
            'uom_id' => $mat->uom_id ?? null,
            'unit_price' => $unitPrice,
            'total_value' => $totalValue,
            'created_by' => auth()->id(),
        ]);

        foreach ($tempStockUsage as $usage) {
            \App\Models\StockConsumableStockDetail::create([
                'stock_consumable_issue_item_id' => $issueItem->id,
                'stock_entry_item_id' => $usage['stock_entry_item_id'],
                'qty' => $usage['qty']
            ]);

            $stockItemRec = \App\Models\StockEntryItem::find($usage['stock_entry_item_id']);
            if ($stockItemRec) {
                $stockItemRec->qty_in += $usage['qty'];
                $stockItemRec->save();
            }
        }
    }

    private function addSingleMaterialStock($materialId, $qty, $remarks, $stageName = null)
    {
        if ($qty <= 0) return;
        $stockEntryItem = \App\Models\StockEntryItem::where('raw_material_id', $materialId)->orderBy('id', 'desc')->first();
        if ($stockEntryItem) {
            $stockEntryItem->qty_in += $qty;
            $stockEntryItem->save();
        }
    }
}
