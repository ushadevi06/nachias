<?php

namespace App\Http\Controllers;

use App\Models\ProductionService;
use App\Models\OperationStage;
use App\Models\TaskAdjustment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductionServiceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-services')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $services = ProductionService::with(['operationStage', 'processGroups'])->orderBy('operation_stage_id')->orderBy('sequence')->latest('id')->get();
            $data = [];
            $i = 1;

            foreach ($services as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input service-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit production-services')) {
                    $action .= '<a href="' . url('production_services/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete production-services')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('production_services/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'  => $i++,
                    'service_code' => $row->service_code,
                    'service_name' => $row->service_name,
                    'operation_stage' => $row->operationStage ? $row->operationStage->operation_stage_name : '-',
                    'process_group' => $row->processGroups->isNotEmpty() ? $row->processGroups->pluck('name')->implode(', ') : '-',
                    'applies_to'   => $row->applies_to,
                    'cost' => $row->cost !== null ? number_format($row->cost, 2) : '-',
                    'status'       => $status,
                    'action'       => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('production_services.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit production-services')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create production-services')) {
                return unauthorizedRedirect();
            }
        }

        $service = $id ? ProductionService::with('processGroups')->findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'service_name' => 'required|string|max:50|unique:production_services,service_name,' . $id . ',id,deleted_at,NULL',
                'service_code' => 'required|string|max:75|unique:production_services,service_code,' . $id . ',id,deleted_at,NULL',
                'operation_stage_id' => 'required|exists:operation_stages,id',
                'process_group_ids' => 'required|array',
                'process_group_ids.*' => 'exists:process_groups,id',
                'cost' => 'nullable|numeric|min:0',
                'status'       => 'required|in:Active,Inactive',
                'applies_to'   => 'required|in:ALL,Full Sleeve,Half Sleeve,Both',
                'base_quantity_source' => 'required|in:Total Qty,FS Qty,HS Qty',
                'sequence' => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::unique('production_services')
                        ->where(function ($query) use ($request) {
                            return $query->where('operation_stage_id', $request->operation_stage_id)
                                ->whereNull('deleted_at');
                        })
                        ->ignore($id),
                ],
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                'cost.numeric' => 'Cost must be a valid number.',
                'cost.min' => 'Cost must be 0 or greater.',
                'sequence.unique' => 'This sequence already exists for selected production stage.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only([
                'service_name', 'service_code', 'operation_stage_id',
                'sequence', 'status', 'cost',
                'applies_to', 'base_quantity_source'
            ]);
            $data['process_group_id'] = $request->process_group_ids[0] ?? null;
            if (empty($data['cost'])) {
                $data['cost'] = null;
            }

            if ($id) {
                $data['updated_by'] = auth()->id();
                $service->update($data);
                $service->processGroups()->sync($request->process_group_ids);
                addLog('update', 'Production Service', 'production_services', $id, null, $data);
                $msg = 'Production Service updated successfully';
            } else {
                $maxSequence = ProductionService::where('operation_stage_id',$request->operation_stage_id)->whereNull('deleted_at')->max('sequence');
                $data['sequence'] = ($maxSequence ?? 0) + 1;
                $data['created_by'] = auth()->id();
                $newService = ProductionService::create($data);
                $newService->processGroups()->sync($request->process_group_ids);
                addLog('create', 'Production Service', 'production_services', $newService->id, null, $data);
                $msg = 'Production Service added successfully';
            }

            return redirect('production_services')->with('success', $msg);
        }

        $operationStages = OperationStage::active()->orderBy('id','desc')->get();
        $processGroups = \App\Models\ProcessGroup::active()->orderBy('name')->get();
        $uoms = \App\Models\Uom::active()->orderBy('id','desc')->get();

        return view('production_services.add', compact('service', 'operationStages', 'uoms', 'processGroups'));
    }
    
    public function getNextSequence($stageId)
    {
        $nextSequence = ProductionService::where('operation_stage_id', $stageId)->whereNull('deleted_at')->max('sequence');
        return response()->json(['sequence' => ($nextSequence ?? 0) + 1]);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete production-services')) {
            return unauthorizedRedirect();
        }
        $service = ProductionService::findOrFail($id);
        if (TaskAdjustment::where('service_id', $id)->exists()) {
            return redirect('production_services')->with('danger', 'This service is currently referenced in Task Adjustments and cannot be deleted.');
        }

        $oldData = $service->toArray();
        $service->delete();
        addLog('delete', 'Production Service', 'production_services', $id, $oldData, null);
        return redirect('production_services')->with('success', 'Production Service deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $service = ProductionService::findOrFail($id);
        $oldData = $service->toArray();
        $service->status = $request->status;
        $service->save();
        $newData = $service->toArray();
        addLog('update_status', 'Production Service Status', 'production_services', $service->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $service->status
        ]);
    }
}
