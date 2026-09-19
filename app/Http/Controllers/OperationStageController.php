<?php

namespace App\Http\Controllers;

use App\Models\OperationStage;
use App\Models\JobCardOperation;
use App\Models\ProcessSchedule;
use App\Models\ProductionMovement;
use App\Models\ProductionService;
use App\Models\ServiceProvider;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class OperationStageController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view operation-stages')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $operationStages = OperationStage::orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;
            foreach ($operationStages as $stage) {
                $checked = $stage->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input operation-stage-status-toggle" data-id="' . $stage->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $stage->id . '"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('edit operation-stages')) {
                    $action .= '<a href="' . url('operation_stages/add/' . $stage->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete operation-stages')) {
                    $action .= '<button class="btn btn-delete" onclick="delete_data(`' . url('operation_stages/delete/' . $stage->id) . '`)"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'operation_stage_name' => $stage->operation_stage_name,
                    'working_days' => $stage->working_days ?? 0,
                    'target' => $stage->target,
                    'cost' => isset($stage->cost) ? number_format($stage->cost, 2) : '0.00',
                    'status' => $status,
                    'action' => $action,
                ];
            }
            return response()->json(['data' => $data]);
        }
        return view('operation_stages.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit operation-stages')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create operation-stages')) {
                return unauthorizedRedirect();
            }
        }
        $operationStage = null;
        $oldData = null;
        if ($id) {
            $operationStage = OperationStage::findOrFail($id);
            $oldData = $operationStage->toArray();
        }
        if (request()->isMethod('post')) {
            $request = request();
            $rules = [
                'operation_stage_name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'not_regex:/^0+$/',
                    Rule::unique('operation_stages', 'operation_stage_name')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'working_days' => 'nullable|integer|min:0|max:999',
                'cost' => 'nullable|numeric|min:0',
                'target' => 'nullable|integer|min:0',
                'status' => 'required|in:Active,Inactive'
            ];

            if ($request->has('unit_targets') && is_array($request->unit_targets)) {
                $rules['unit_targets.*.service_provider_id'] = 'required|distinct|exists:service_providers,id';
                $rules['unit_targets.*.target_qty'] = 'required|numeric|min:0';
            }

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique' => 'This field already exists.',
                'operation_stage_name.not_regex' => 'This field is an invalid format.',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
                'unit_targets.*.service_provider_id.required' => 'Unit / Service Provider is required.',
                'unit_targets.*.service_provider_id.distinct' => 'Duplicate unit selected. Each unit must be unique.',
                'unit_targets.*.service_provider_id.exists' => 'Selected unit is invalid.',
                'unit_targets.*.target_qty.required' => 'Target quantity is required.',
                'unit_targets.*.target_qty.numeric' => 'Target quantity must be a valid number.',
                'unit_targets.*.target_qty.min' => 'Target quantity cannot be negative.',
            ];
            $validated = $request->validate($rules, $messages);
            $data = [
                'operation_stage_name' => $request->operation_stage_name,
                'working_days' => $request->working_days ?? 0,
                'cost' => $request->cost ?? 0,
                'target' => $request->target !== null && $request->target !== '' ? (int) $request->target : null,
                'status' => $request->status
            ];
            $stageId = $id;
            if ($id) {
                $data['updated_by'] = auth()->id();
                OperationStage::where('id', $id)->update($data);
                $newData = OperationStage::find($id)->toArray();
                addLog('update', 'Operation Stage', 'operation_stages', $id, $oldData, $newData);
                $message = 'Operation Stage updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $operationStage = OperationStage::create($data);
                $stageId = $operationStage->id;
                $newData = $operationStage->toArray();
                addLog('create', 'Operation Stage', 'operation_stages', $operationStage->id, null, $newData);
                $message = 'Operation Stage added successfully';
            }

            if ($request->has('unit_targets') && is_array($request->unit_targets)) {
                \App\Models\OperationStageTarget::where('operation_stage_id', $stageId)->delete();
                foreach ($request->unit_targets as $item) {
                    $spId = isset($item['service_provider_id']) ? (int) $item['service_provider_id'] : null;
                    $tQty = isset($item['target_qty']) && $item['target_qty'] !== '' ? (int) $item['target_qty'] : null;
                    if ($spId && $tQty !== null && $tQty >= 0) {
                        \App\Models\OperationStageTarget::updateOrInsert(
                            ['operation_stage_id' => $stageId, 'service_provider_id' => $spId],
                            ['target_qty' => $tQty, 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            } else {
                \App\Models\OperationStageTarget::where('operation_stage_id', $stageId)->delete();
            }

            return redirect('operation_stages')->with('success', $message);
        }

        $serviceProviders = ServiceProvider::where('status', 'Active')->orderBy('id','desc')->get();
        $stageTargets = $operationStage ? $operationStage->targets : collect();

        return view('operation_stages.add', compact('operationStage', 'serviceProviders', 'stageTargets'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete operation-stages')) {
            return unauthorizedRedirect();
        }
        $operationStage = OperationStage::findOrFail($id);

        if (JobCardOperation::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Job Card Operations and cannot be deleted.');
        }
        if (ProcessSchedule::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Process Schedules and cannot be deleted.');
        }
        if (ProductionMovement::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Production Movements and cannot be deleted.');
        }
        if (ProductionService::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Production Services and cannot be deleted.');
        }
        if (ServiceProvider::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Service Providers and cannot be deleted.');
        }
        if (Ticket::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Tickets and cannot be deleted.');
        }
        if (User::where('operation_stage_id', $id)->exists()) {
            return redirect('operation_stages')->with('danger', 'This operation stage is currently referenced in Users and cannot be deleted.');
        }

        $oldData = $operationStage->toArray();
        $operationStage->delete();
        addLog('delete', 'Operation Stage', 'operation_stages', $id, $oldData, null);
        return redirect('operation_stages')->with('success', 'Operation Stage deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $operationStage = OperationStage::findOrFail($id);
        $oldData = $operationStage->toArray();
        $operationStage->status = $request->status;
        $operationStage->save();
        $newData = $operationStage->toArray();
        addLog('update_status', 'Operation Stage Status', 'operation_stages', $operationStage->id, $oldData, $newData);
        return response()->json(['success' => true, 'status' => $operationStage->status]);
    }
}
