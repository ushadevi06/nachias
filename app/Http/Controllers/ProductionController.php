<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\ProcessSchedule;
use App\Models\ProcessScheduleService;
use App\Models\JobCardEntry;
use App\Models\ProductionService;
use App\Models\OperationStage;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $productions = Production::latest()->get();
            $data = [];
            $i = 1;

            foreach ($productions as $row) {
                $action = '<div class="d-inline-block text-nowrap">';
                if (auth()->id() == 1 || auth()->user()->can('edit production')) {
                    $action .= '<a href="' . url('productions/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                
                $action .= '<button class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base ri ri-more-2-fill"></i></button>';
                $action .= '<div class="dropdown-menu dropdown-menu-end m-0">';

                if (auth()->id() == 1 || auth()->user()->can('view production')) {
                    $action .= '<a href="' . url('view_production/' . $row->id) . '" class="dropdown-item">View</a>';
                }
                
                $action .= '<a href="' . url('task_management/create?production_id=' . urlencode(\Illuminate\Support\Facades\Crypt::encrypt($row->id))) . '" class="dropdown-item">Assign Task</a>';
                
                $action .= '</div></div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'production_id' => 'PROD-' . str_pad($row->id, 4, '0', STR_PAD_LEFT),
                    'production_date' => date('d-m-Y', strtotime($row->created_at)),
                    'job_card_no' => $row->job_card_no,
                    'planned_qty' => $row->total_planned_qty,
                    'start_date' => $row->planned_start_date ? date('d-m-Y', strtotime($row->planned_start_date)) : '-',
                    'end_date' => $row->planned_end_date ? date('d-m-Y', strtotime($row->planned_end_date)) : '-',
                    'status' => '<span class="badge bg-label-' . ($row->status == 'Confirmed' ? 'success' : ($row->status == 'Draft' ? 'warning' : 'secondary')) . '">' . $row->status . '</span>',
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }
        return view('productions/view');
    }
    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit production')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create production')) {
                return unauthorizedRedirect();
            }
        }
        $production = $id ? Production::with(['processSchedules.services'])->findOrFail($id) : null;

        $usedJobCardIds = Production::when($id, function ($query) use ($id) {
            return $query->where('id', '!=', $id);
        })->pluck('job_card_entry_id')->toArray();

        $jobCards = JobCardEntry::whereNotIn('id', $usedJobCardIds)->get();

        $plants = ServiceProvider::where('is_plant', 1)->where('status', 'Active')->get();
        $operationStages = OperationStage::active()->where('operation_stage_name', '!=', 'Cutting')->get();

        if (request()->isMethod('post')) {
            return $this->store(request(), $id);
        }

        return view('productions.add', compact('production', 'jobCards', 'plants', 'operationStages'));
    }

    public function getJobCardDetails($id)
    {
        $jobCard = JobCardEntry::with(['purchaseOrder', 'serviceProvider', 'processGroup'])->find($id);
        if (!$jobCard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'purchase_order_id' => $jobCard->purchase_order_id,
                'purchase_order_no' => $jobCard->purchaseOrder ? $jobCard->purchaseOrder->po_number : '-',
                'plant_id' => $jobCard->service_provider_id,
                'plant_name' => $jobCard->serviceProvider ? $jobCard->serviceProvider->name : '-',
                'process_group_id' => $jobCard->process_group_id,
                'process_group_name' => $jobCard->processGroup ? $jobCard->processGroup->name : '-',
                'fs_qty' => $jobCard->total_qty_fs ?? 0,
                'hs_qty' => $jobCard->total_qty_hs ?? 0,
                'total_qty' => $jobCard->grand_total_qty ?? 0,
            ]
        ]);
    }

    public function getServices($stage, $jobCardId)
    {
        $jobCard = JobCardEntry::find($jobCardId);
        if (!$jobCard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        $fsQty = $jobCard->total_qty_fs ?? 0;
        $hsQty = $jobCard->total_qty_hs ?? 0;
        $totalQty = $jobCard->grand_total_qty ?? 0;

        $operationStage = OperationStage::where('operation_stage_name', $stage)->where('operation_stage_name', '!=', 'Cutting')->first();
        if (!$operationStage) {
            return response()->json(['success' => true, 'services' => []]);
        }
        $services = ProductionService::where('operation_stage_id', $operationStage->id)->where('status', 'Active')->get();
        $data = $services->map(function ($service) use ($fsQty, $hsQty, $totalQty) {
            $qty = 0;
            if ($service->applies_to == 'ALL' || $service->applies_to == 'Both') {
                $qty = $totalQty;
            } elseif ($service->applies_to == 'Full Sleeve') {
                $qty = $fsQty;
            } elseif ($service->applies_to == 'Half Sleeve') {
                $qty = $hsQty;
            }
            $qty = (float)$qty * (float)($service->multiplier ?? 1);
            return [
                'id' => $service->id,
                'service_code' => $service->service_code,
                'service_name' => $service->service_name,
                'applies_to' => $service->applies_to,
                'qty' => $qty,
                'uom' => $service->uom
            ];
        });

        \Log::info("Returning services for stage $stage", ['services' => $data->pluck('id')->toArray()]);
        return response()->json(['success' => true, 'services' => $data]);
    }

    public function store(Request $request, $id = null)
    {
        $rules = [
            'job_card_entry_id' => 'required|exists:job_card_entries,id',
            'plant_id' => 'required',
            'planned_start_date' => 'required',
            'planned_end_date' => 'required',
            'expected_completion_date' => 'required',
            'status' => 'required',
            'schedules.*.planned_qty' => 'nullable|numeric|min:1',
            'schedules.*.start_date' => 'required_with:schedules.*.planned_qty',
            'schedules.*.end_date' => 'required_with:schedules.*.planned_qty',
            'schedules.*.scheduled_to' => 'required_with:schedules.*.planned_qty',
        ];

        $messages = [
            'required' => 'This field is required.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'job_card_entry_id', 'job_card_no', 'purchase_order_id', 'purchase_order_no',
                'plant_id', 'process_group_id', 'full_sleeve_qty', 'half_sleeve_qty',
                'total_planned_qty', 'planned_start_date', 'planned_end_date',
                'expected_completion_date', 'status', 'remarks'
            ]);

            $dateFields = ['planned_start_date', 'planned_end_date', 'expected_completion_date'];
            foreach ($dateFields as $field) {
                if ($request->filled($field)) {
                    $data[$field] = date('Y-m-d', strtotime($request->$field));
                }
            }

            if ($id) {
                $oldData = Production::find($id)->toArray();
                $data['updated_by'] = Auth::id();
                Production::where('id', $id)->update($data);
                $productionId = $id;
                $production = Production::find($id); 
                addLog('update', 'Production', 'productions', $id, $oldData, $production->toArray());
                ProcessSchedule::where('production_id', $productionId)->delete();
            } else {
                $data['created_by'] = Auth::id();
                $production = Production::create($data);
                $productionId = $production->id;
                addLog('create', 'Production', 'productions', $productionId, null, $production->toArray());         
            }

            if ($request->has('schedules')) {
                foreach ($request->input('schedules') as $stageId => $scheduleData) {
                    if (empty($scheduleData['planned_qty'])) continue;

                    $stageName = OperationStage::find($stageId)->operation_stage_name ?? 'Unknown';

                    $schedule = ProcessSchedule::create([
                        'production_id' => $productionId,
                        'stage' => $stageName, 
                        'planned_qty' => $scheduleData['planned_qty'] ?? 0,
                        'uom' => $scheduleData['uom'] ?? 'PCS',
                        'scheduled_to' => $scheduleData['scheduled_to'] ?? null,
                        'service_provider_type' => $scheduleData['service_provider_type'] ?? null,
                        'start_date' => isset($scheduleData['start_date']) ? date('Y-m-d', strtotime($scheduleData['start_date'])) : null,
                        'end_date' => isset($scheduleData['end_date']) ? date('Y-m-d', strtotime($scheduleData['end_date'])) : null,
                        'due_date' => isset($scheduleData['due_date']) ? date('Y-m-d', strtotime($scheduleData['due_date'])) : null,
                        'status' => 'Planned',
                        'created_by' => Auth::id()
                    ]);

                    if (isset($scheduleData['services'])) {
                        foreach ($scheduleData['services'] as $service) {
                            if (isset($service['selected']) && $service['selected'] == 1) {
                                \Log::info("Attempting to create ProcessScheduleService", [
                                    'process_schedule_id' => $schedule->id,
                                    'service_id' => $service['service_id'] ?? 'MISSING',
                                    'applies_to' => $service['applies_to'] ?? 'MISSING',
                                    'qty' => $service['qty'] ?? '0'
                                ]);

                                if (!\App\Models\ProductionService::where('id', $service['service_id'])->exists()) {
                                    \Log::error("Production Service ID does not exist: " . $service['service_id']);
                                    continue;
                                }

                                ProcessScheduleService::create([
                                    'process_schedule_id' => $schedule->id,
                                    'service_id' => $service['service_id'],
                                    'applies_to' => $service['applies_to'],
                                    'calculated_qty' => $service['qty'],
                                    'uom' => $service['uom']
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect('productions')->with('success', 'Production Planning saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }
        $production = Production::with(['processSchedules.services.productionService', 'processSchedules.serviceProvider', 'jobCard.purchaseOrder', 'plant', 'processGroup'])->findOrFail($id);
        return view('productions/view_details', compact('production'));
    }

    public function suspend($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit production')) {
            return unauthorizedRedirect();
        }
        $production = Production::findOrFail($id);
        $production->update(['status' => 'Suspended']);
        return redirect('productions')->with('success', 'Production suspended successfully');
    }
}
