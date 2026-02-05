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
use App\Models\ProductionStageConsumable;

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
                
                $action .= '<button class="btn dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base ri ri-more-2-fill"></i></button>';
                $action .= '<div class="dropdown-menu dropdown-menu-end m-0">';

                if (auth()->id() == 1 || auth()->user()->can('view production')) {
                    $action .= '<a href="' . url('view_production/' . $row->id) . '" class="dropdown-item"><i class="icon-base ri ri-eye-line me-2"></i>View</a>';
                }
                
                $action .= '<a href="' . url('task_management/create?production_id=' . urlencode(\Illuminate\Support\Facades\Crypt::encrypt($row->id))) . '" class="dropdown-item"><i class="icon-base ri ri-task-line me-2"></i>Assign Task</a>';
                
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

        $nextProductionId = '';
        if (!$id) {
            $latestProduction = Production::latest()->first();
            $nextId = $latestProduction ? $latestProduction->id + 1 : 1;
            $nextProductionId = 'PROD-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        } else {
            $nextProductionId = 'PROD-' . date('Y', strtotime($production->created_at)) . '-' . str_pad($production->id, 3, '0', STR_PAD_LEFT);
        }

        return view('productions.add', compact('production', 'jobCards', 'plants', 'operationStages', 'nextProductionId'));
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
            
            return [
                'id' => $service->id,
                'service_code' => $service->service_code,
                'service_name' => $service->service_name,
                'applies_to' => $service->applies_to,
                'qty' => $qty
            ];
        });

        return response()->json(['success' => true, 'services' => $data]);
    }

    public function store(Request $request, $id = null)
    {
        $rules = [
            'job_card_entry_id' => 'required|exists:job_card_entries,id',
            'plant_id' => 'required',
            'planned_start_date' => 'required|date_format:d-m-Y',
            'planned_end_date' => 'required|date_format:d-m-Y|after_or_equal:planned_start_date',
            'expected_completion_date' => 'required|date_format:d-m-Y|after_or_equal:planned_end_date',
            'status' => 'required|in:Draft,Confirmed,Closed',
            'schedules.*.planned_qty' => 'nullable|numeric|min:1',
            'schedules.*.start_date' => 'required_with:schedules.*.planned_qty|nullable|date_format:d-m-Y',
            'schedules.*.end_date' => 'required_with:schedules.*.planned_qty|nullable|date_format:d-m-Y|after_or_equal:schedules.*.start_date',
            'schedules.*.due_date' => 'nullable|date_format:d-m-Y|after_or_equal:schedules.*.end_date',
            'schedules.*.scheduled_to' => 'required_with:schedules.*.planned_qty',
        ];

        $messages = [
            'required' => 'This field is required.',
            'required_with' => 'This field is required.',
            'date_format' => 'Invalid date format (must be DD-MM-YYYY).',
            'planned_end_date.after_or_equal' => 'Must be after or equal to Start Date',
            'expected_completion_date.after_or_equal' => 'Must be after or equal to End Date',
            'schedules.*.end_date.after_or_equal' => 'Must be after or equal to Start Date',
            'schedules.*.due_date.after_or_equal' => 'Must be after or equal to End Date',
        ];

        $request->validate($rules, $messages);

        $totalPlanned = $request->input('total_planned_qty');
        if ($request->has('schedules')) {
            foreach ($request->input('schedules') as $key => $schedule) {
                if (!empty($schedule['planned_qty']) && $schedule['planned_qty'] > $totalPlanned) {
                    return back()->withErrors(['schedules.'.$key.'.planned_qty' => 'Cannot exceed Total Planned Qty (' . $totalPlanned . ')'])->withInput();
                }
            }
        }

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
                ProductionStageConsumable::where('production_id', $productionId)->forceDelete();
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
                                ProcessScheduleService::create([
                                    'process_schedule_id' => $schedule->id,
                                    'service_id' => $service['service_id'],
                                    'applies_to' => $service['applies_to'],
                                    'calculated_qty' => $service['qty']
                                ]);
                            }
                        }
                    }

                    if ($data['status'] == 'Confirmed') {
                        $fsQty = $data['full_sleeve_qty'] ?? 0;
                        $hsQty = $data['half_sleeve_qty'] ?? 0;
                        
                        $jobCard = JobCardEntry::with(['fabricDetails', 'sleeveMeters', 'issueItems.fabricDetail'])->find($data['job_card_entry_id']);
                        
                        if ($jobCard) {
                            if ($jobCard->fabricDetails) {
                                foreach ($jobCard->fabricDetails as $fabricDetail) {
                                    $consumption = \App\Models\JobCardFabricConsumption::where('job_card_fabric_detail_id', $fabricDetail->id)->first();
                                    
                                    $fsRate = 0;
                                    $hsRate = 0;

                                    if ($consumption) {
                                        $fsRate = $consumption->fs_cons;
                                        $hsRate = $consumption->hs_cons;
                                    } elseif ($fabricDetail->fs_qty > 0 || $fabricDetail->hs_qty > 0) {
                                        $fsRate = $fabricDetail->fs_qty;
                                        $hsRate = $fabricDetail->hs_qty;
                                    } else {
                                        $fsSleeve = $jobCard->sleeveMeters->where('sleeve_type', 'Full Sleeve')->first();
                                        $hsSleeve = $jobCard->sleeveMeters->where('sleeve_type', 'Half Sleeve')->first();
                                        $fsRate = $fsSleeve ? $fsSleeve->meter : 0;
                                        $hsRate = $hsSleeve ? $hsSleeve->meter : 0;
                                    }
                                    
                                    $fsConsumption = $fsQty * $fsRate;
                                    $hsConsumption = $hsQty * $hsRate;
                                    $totalActual = $fsConsumption + $hsConsumption;

                                    if ($totalActual > 0) {
                                        $rawMaterialId = null;
                                        $uomId = null;
                                        $artNo = trim($fabricDetail->art_no);
                                        
                                        $stockItem = \DB::table('stock_entry_items')->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')->where('grn_entry_items.art_no', $artNo)->select('stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')->first();
                                        
                                        if ($stockItem) {
                                            $rawMaterialId = $stockItem->raw_material_id;
                                            $uomId = $stockItem->uom_id;
                                        }
                                        
                                        $sleeveType = 'All';
                                        if ($fsConsumption > 0 && $hsConsumption == 0) {
                                            $sleeveType = 'F/S';
                                        } elseif ($hsConsumption > 0 && $fsConsumption == 0) {
                                            $sleeveType = 'H/S';
                                        }

                                        ProductionStageConsumable::create([
                                            'job_card_id' => $data['job_card_entry_id'],
                                            'production_id' => $productionId,
                                            'production_stage_id' => $stageId, 
                                            'stage' => $stageName,
                                            'art_no' => $artNo,
                                            'item_type' => 'Consumable',
                                            'raw_material_id' => $rawMaterialId,
                                            'planned_qty' => ($scheduleData['planned_qty'] ?? 0),
                                            'fs_qty' => $fsConsumption,
                                            'hs_qty' => $hsConsumption,
                                            'total_qty' => $totalActual,
                                            'actual_qty' => $totalActual,
                                            'uom_id' => $uomId,
                                            'sleeve_type' => $sleeveType,
                                            'status' => 'Active',
                                            'remarks' => "Article: {$artNo}, FS: ".number_format($fsConsumption, 2)." ({$fsQty} x {$fsRate}), HS: ".number_format($hsConsumption, 2)." ({$hsQty} x {$hsRate})",
                                            'created_by' => Auth::id()
                                        ]);
                                    }
                                }
                            }

                            if ($jobCard->issueItems) {
                                foreach ($jobCard->issueItems as $issueItem) {
                                    $rate = $issueItem->average ?? 0;
                                    $totalConsumption = $data['total_planned_qty'] * $rate;

                                    if ($totalConsumption > 0) {
                                        $artNo = null;
                                        $rawMaterialId = null;
                                        $uomId = null;

                                        if ($issueItem->stock_entry_item_id) {
                                            $stockInfo = \DB::table('stock_entry_items')
                                                ->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')
                                                ->where('stock_entry_items.id', $issueItem->stock_entry_item_id)
                                                ->select('grn_entry_items.art_no', 'stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')
                                                ->first();
                                            
                                            if ($stockInfo) {
                                                $artNo = $stockInfo->art_no;
                                                $rawMaterialId = $stockInfo->raw_material_id;
                                                $uomId = $stockInfo->uom_id;
                                            }
                                        }

                                        $fs_comp = ($data['full_sleeve_qty'] ?? 0) * $rate;
                                        $hs_comp = ($data['half_sleeve_qty'] ?? 0) * $rate;

                                        $sleeveType = 'All';
                                        if ($fs_comp > 0 && $hs_comp == 0) {
                                            $sleeveType = 'F/S';
                                        } elseif ($hs_comp > 0 && $fs_comp == 0) {
                                            $sleeveType = 'H/S';
                                        }

                                        ProductionStageConsumable::create([
                                            'job_card_id' => $data['job_card_entry_id'],
                                            'production_id' => $productionId,
                                            'production_stage_id' => $stageId,
                                            'stage' => $stageName,
                                            'art_no' => $artNo,
                                            'item_type' => 'Consumable',
                                            'raw_material_id' => $rawMaterialId,
                                            'planned_qty' => ($scheduleData['planned_qty'] ?? 0),
                                            'fs_qty' => $fs_comp,
                                            'hs_qty' => $hs_comp,
                                            'total_qty' => $totalConsumption,
                                            'actual_qty' => $totalConsumption,
                                            'uom_id' => $uomId,
                                            'sleeve_type' => $sleeveType,
                                            'status' => 'Active',
                                            'remarks' => "Consumable: {$artNo}, Rate: {$rate}",
                                            'created_by' => Auth::id()
                                        ]);
                                    }
                                }
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
        $production = Production::with(['processSchedules.services.productionService', 'processSchedules.serviceProvider', 'jobCard.purchaseOrder', 'plant', 'processGroup', 'consumables.rawMaterial', 'consumables.uom'])->findOrFail($id);
        return view('productions/view_details', compact('production'));
    }
}
