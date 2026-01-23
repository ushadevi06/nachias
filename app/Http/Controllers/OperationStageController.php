<?php

namespace App\Http\Controllers;

use App\Models\OperationStage;
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
            $operationStages = OperationStage::orderBy('id','desc')->get();
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
                    $action .= '
                        <a href="' . url('operation_stages/add/' . $stage->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                // if (auth()->id() == 1 || auth()->user()->can('delete operation-stages')) {
                //     $action .= '
                //         <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('operation_stages/delete/' . $stage->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                // }
                $action .= '</div>';
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'operation_stage_name' => $stage->operation_stage_name,
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
                    'max:255',
                    Rule::unique('operation_stages', 'operation_stage_name')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $validated = $request->validate($rules, $messages);
            $data = [
                'operation_stage_name' => $request->operation_stage_name,
                'status' => $request->status
            ];
            if ($id) {
                $data['updated_by'] = auth()->id();
                OperationStage::where('id', $id)->update($data);
                $newData = OperationStage::find($id)->toArray();
                addLog('update', 'Operation Stage', 'operation_stages', $id, $oldData, $newData);
                $message = 'Operation Stage updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $operationStage = OperationStage::create($data);
                $newData = $operationStage->toArray();
                addLog('create', 'Operation Stage', 'operation_stages', $operationStage->id, null, $newData);
                $message = 'Operation Stage added successfully';
            }
            return redirect('operation_stages')->with('success', $message);
        }
        return view('operation_stages.add', compact('operationStage'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete operation-stages')) {
            return unauthorizedRedirect();
        }
        $operationStage = OperationStage::findOrFail($id);
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
