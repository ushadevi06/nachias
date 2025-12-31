<?php

namespace App\Http\Controllers;

use App\Models\FabricType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FabricTypeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view fabric-type')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $fabricTypes = FabricType::orderBy('id','desc')->get();
            $data = [];
            $count = 1;

            foreach ($fabricTypes as $fabricType) {
                $checked = $fabricType->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input fabric-type-status-toggle"
                        data-id="' . $fabricType->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $fabricType->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit fabric-type')) {
                    $action .= '
                        <a href="' . url('fabric_type/add/' . $fabricType->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete fabric-type')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete"
                        onclick="delete_data(\'' . url('fabric_type/delete/' . $fabricType->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'fabric_type' => $fabricType->fabric_type,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('fabric_type.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit fabric-type')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create fabric-type')) {
                return unauthorizedRedirect();
            }
        }
        $fabricType = null;
        $oldData = null;

        if ($id) {
            $fabricType = FabricType::findOrFail($id);
            $oldData = $fabricType->toArray();
        }

        if (request()->isMethod('post')) {

            $request = request();

            $rules = [
                'fabric_type' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('fabric_types', 'fabric_type')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'status' => 'required|in:Active,Inactive'
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This Field already exists.',
            ];

            $request->validate($rules, $messages);

            $data = [
                'fabric_type' => $request->fabric_type,
                'status'      => $request->status
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                FabricType::where('id', $id)->update($data);
                $newData = FabricType::find($id)->toArray();

                addLog('update', 'Fabric Type', 'fabric_types', $id, $oldData, $newData);
                $message = 'Fabric Type updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $fabricType = FabricType::create($data);
                $newData = $fabricType->toArray();

                addLog('create', 'Fabric Type', 'fabric_types', $fabricType->id, null, $newData);
                $message = 'Fabric Type added successfully';
            }

            return redirect('fabric_type')->with('success', $message);
        }

        return view('fabric_type.add', compact('fabricType'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete fabric-type')) {
            return unauthorizedRedirect();
        }
        $fabricType = FabricType::findOrFail($id);
        $oldData = $fabricType->toArray();

        $fabricType->delete();

        addLog('delete', 'Fabric Type', 'fabric_types', $id, $oldData, null);

        return redirect('fabric_type')->with('success', 'Fabric Type deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $fabricType = FabricType::findOrFail($id);
        $oldData = $fabricType->toArray();

        $fabricType->status = $request->status;
        $fabricType->save();

        $newData = $fabricType->toArray();

        addLog('update_status', 'Fabric Type Status', 'fabric_types', $id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'status'  => $fabricType->status
        ]);
    }
}
