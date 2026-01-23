<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Yajra\DataTables\DataTables;

class TaskTrackingMonitoringController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $tasks = Task::with(['assignee', 'jobCard', 'stage.operationStage', 'receives', 'adjustments', 'createdBy'])
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($tasks)
                ->addIndexColumn()
                ->addColumn('task_id', function($row) {
                    return $row->task_no ?? 'N/A';
                })
                ->addColumn('job_card_no', function($row) {
                    return $row->jobCard->job_card_no ?? 'N/A';
                })
                ->addColumn('task_title', function($row) {
                    $stageName = $row->stage->operationStage->operation_stage_name ?? ($row->stage->stage ?? 'N/A');
                    return $stageName . ' - ' . ($row->jobCard->job_card_no ?? 'N/A');
                })
                ->addColumn('assigned_by', function($row) {
                    return $row->createdBy->name ?? 'N/A';
                })
                ->addColumn('assigned_to', function($row) {
                    $assignee = $row->assignee;
                    if ($assignee) {
                        return $assignee->name . ' <span class="mini-title">(EMP' . str_pad($assignee->id, 3, '0', STR_PAD_LEFT) . ')</span>';
                    }
                    return 'N/A';
                })
                ->addColumn('task_status', function($row) {
                    $status = $row->status ?? 'Planned';
                    $badgeClass = 'secondary';
                    
                    switch($status) {
                        case 'Completed':
                            $badgeClass = 'success';
                            break;
                        case 'In Progress':
                            $badgeClass = 'warning';
                            break;
                        case 'Hold':
                            $badgeClass = 'danger';
                            break;
                        case 'Planned':
                        default:
                            $badgeClass = 'secondary';
                            break;
                    }
                    
                    return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
                })
                ->addColumn('qty_ordered', function($row) {
                    // Calculate target quantity by summing individual service quantities
                    $targetQty = 0;
                    if ($row->services && is_array($row->services) && $row->stage_id) {
                        foreach ($row->services as $serviceId) {
                            $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $row->stage_id)
                                ->where('service_id', $serviceId)
                                ->first();
                            if ($scheduleService) {
                                $targetQty += (float)($scheduleService->calculated_qty ?? 0);
                            }
                        }
                    }
                    
                    // If no services found, fallback to stage planned_qty
                    if ($targetQty == 0 && $row->stage) {
                        $targetQty = (float)($row->stage->planned_qty ?? 0);
                    }
                    
                    return $targetQty;
                })
                ->addColumn('qty_completed', function($row) {
                    $totalReceived = 0;
                    
                    // Sum per-service received quantities (good + wastage)
                    foreach ($row->receives as $receive) {
                        if ($receive->received_services && is_array($receive->received_services)) {
                            foreach ($receive->received_services as $serviceId => $quantities) {
                                $goodQty = (float)($quantities['good_qty'] ?? 0);
                                $wastageQty = (float)($quantities['wastage_qty'] ?? 0);
                                $totalReceived += $goodQty + $wastageQty;
                            }
                        } else {
                            // Fallback for old records
                            $totalReceived += (float)($receive->good_qty ?? 0);
                        }
                    }
                    
                    foreach($row->adjustments as $adj) {
                        if ($adj->adjustment_type == 'Loss' || $adj->adjustment_type == 'Excess') {
                            $totalReceived += (float)$adj->qty;
                        } elseif ($adj->adjustment_type == 'Rework') {
                            $totalReceived -= (float)$adj->qty;
                        }
                    }
                    
                    return max(0, $totalReceived);
                })
                ->addColumn('qty_pending', function($row) {
                    // Calculate target quantity by summing individual service quantities
                    $targetQty = 0;
                    if ($row->services && is_array($row->services) && $row->stage_id) {
                        foreach ($row->services as $serviceId) {
                            $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $row->stage_id)
                                ->where('service_id', $serviceId)
                                ->first();
                            if ($scheduleService) {
                                $targetQty += (float)($scheduleService->calculated_qty ?? 0);
                            }
                        }
                    }
                    
                    // If no services found, fallback to stage planned_qty
                    if ($targetQty == 0 && $row->stage) {
                        $targetQty = (float)($row->stage->planned_qty ?? 0);
                    }
                    
                    // Calculate total received per service (good + wastage)
                    $totalReceived = 0;
                    foreach ($row->receives as $receive) {
                        if ($receive->received_services && is_array($receive->received_services)) {
                            foreach ($receive->received_services as $serviceId => $quantities) {
                                $goodQty = (float)($quantities['good_qty'] ?? 0);
                                $wastageQty = (float)($quantities['wastage_qty'] ?? 0);
                                $totalReceived += $goodQty + $wastageQty;
                            }
                        } else {
                            // Fallback for old records
                            $totalReceived += (float)($receive->good_qty ?? 0);
                        }
                    }
                    
                    foreach($row->adjustments as $adj) {
                        if ($adj->adjustment_type == 'Loss' || $adj->adjustment_type == 'Excess') {
                            $totalReceived += (float)$adj->qty;
                        } elseif ($adj->adjustment_type == 'Rework') {
                            $totalReceived -= (float)$adj->qty;
                        }
                    }
                    
                    return max(0, $targetQty - $totalReceived);
                })
                ->addColumn('deadline_date', function($row) {
                    return $row->due_date ? date('d-m-Y', strtotime($row->due_date)) : 'N/A';
                })
                ->addColumn('alerts', function($row) {
                    $isOverdue = $row->due_date && strtotime($row->due_date) < time() && $row->status != 'Completed';
                    $checked = $isOverdue ? 'checked' : '';
                    return '<div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" value="1" name="alerts_' . $row->id . '" ' . $checked . ' />
                    </div>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="button-box">';
                    $btn .= '<a href="' . url('view_task_tracking_monitoring/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                    $btn .= '<a href="' . url('task_management/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['assigned_to', 'task_status', 'alerts', 'action'])
                ->make(true);
        }
        
        return view('task_tracking_monitoring/view');
    }
    
    public function add(){
        return view('task_tracking_monitoring/add');
    }
    
    public function view($id){
        $task = Task::with(['assignee', 'jobCard', 'stage.operationStage', 'receives', 'adjustments'])->findOrFail($id);
        return view('task_tracking_monitoring/view_details', compact('task'));
    }
}
