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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
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
            
            // Fetch all task statuses
            $allStatuses = TaskStatus::all();

            $boards = [];
            foreach ($allStatuses as $status) {
                $boards[] = ['id' => $status->name, 'title' => $status->name, 'item' => []];
            }

            foreach ($tasks as $t) {
                $statusName = $t->status ?: 'Planned';
                
                // Calculate target quantity by summing individual service quantities
                $targetQty = 0;
                $serviceTargets = []; // Store target per service
                
                if ($t->services && is_array($t->services) && $t->stage_id) {
                    foreach ($t->services as $serviceId) {
                        $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $t->stage_id)
                            ->where('service_id', $serviceId)
                            ->first();
                        if ($scheduleService) {
                            $qty = (float)($scheduleService->calculated_qty ?? 0);
                            $serviceTargets[$serviceId] = $qty;
                            $targetQty += $qty;
                        }
                    }
                }
                
                // If no services found, fallback to stage planned_qty
                if ($targetQty == 0 && $t->stage) {
                    $targetQty = (float)($t->stage->planned_qty ?? 0);
                }
                
                // Calculate total received per service
                $totalReceived = 0;
                foreach ($t->receives as $receive) {
                    if ($receive->received_services && is_array($receive->received_services)) {
                        foreach ($receive->received_services as $serviceId => $quantities) {
                            $goodQty = (float)($quantities['good_qty'] ?? 0);
                            $wastageQty = (float)($quantities['wastage_qty'] ?? 0);
                            $totalReceived += $goodQty + $wastageQty;  // Good + Wastage = Work completed
                        }
                    } else {
                        // Fallback for old records without per-service tracking
                        $totalReceived += (float)($receive->good_qty ?? 0);
                    }
                }
                
                // Apply adjustments
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

    /* Task Receive Methods */
    public function receive_index(Request $request)
    {
        if ($request->ajax()) {
            $data = TaskReceive::with(['task', 'receivedFrom'])->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('task_no', function($row){
                    return $row->task->task_no ?? 'N/A';
                })
                ->addColumn('received_from_name', function($row){
                    return $row->receivedFrom->name ?? 'N/A';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex gap-2">';
                    $btn .= '<a href="'.url('task_receives/add/'.$row->id).'" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>';
                    $btn .= '<a href="'.url('task_receives/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="ri-delete-bin-line"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('task_management/receive_index');
    }

    public function receive_add($id = null)
    {
        $taskReceive = null;
        if ($id) {
            $taskReceive = TaskReceive::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();
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
        return response.json(['success' => false]);
    }

    public function adjustment_add(Request $request, $id = null)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'adjustment_type' => 'required',
            'qty' => 'required|numeric',
            'reason' => 'required'
        ],[
            'required' => 'This field is required.'
        ]);

        $taskReceiveExists = TaskReceive::where('task_id', $request->task_id)->exists();
        if (!$taskReceiveExists) {
            return back()->with('error', 'Adjustments can only be recorded after a Task Receive has been previously submitted for this task.')->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            if (!$id) {
                if (!$request->adjustment_no) {
                    $count = TaskAdjustment::count();
                    $data['adjustment_no'] = 'ADJ-' . date('Y') . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                }
                $data['created_by'] = auth()->id();
                TaskAdjustment::create($data);
                $message = 'Adjustment created successfully';
            } else {
                $adj = TaskAdjustment::findOrFail($id);
                $data['updated_by'] = auth()->id();
                $adj->update($data);
                $message = 'Adjustment updated successfully';
            }
            
            $task = Task::find($request->task_id);
            if ($task) {
                $issueQty = (float)($task->issue_qty ?? 0);
                $serviceCount = is_array($task->services) ? count($task->services) : 1;
                $targetQty = $issueQty * $serviceCount;

                $totalReceived = (float)$task->receives()->sum('good_qty');
                foreach($task->adjustments as $adjRec) { 
                    if ($adjRec->adjustment_type == 'Loss' || $adjRec->adjustment_type == 'Excess') {
                        $totalReceived += (float)$adjRec->qty;
                    } elseif ($adjRec->adjustment_type == 'Rework') {
                        $totalReceived -= (float)$adjRec->qty;
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
            }
            DB::commit();
            return redirect('task_management')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function addCustomStatus(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:255|unique:task_status,name'
        ]);

        $status = \App\Models\TaskStatus::create([
            'name' => $request->status_name,
            'color' => 'secondary',
            'progress_percent' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom status added successfully',
            'status' => $status
        ]);
    }
}